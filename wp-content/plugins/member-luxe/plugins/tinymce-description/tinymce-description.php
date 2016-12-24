<?php

add_action('init', 'wpm_rich_text_tags', 9999);
function wpm_rich_text_tags() {
	
	global $wpdb, $user, $current_user, $pagenow, $wp_version;
	
	//load_plugin_textdomain( 'rich-text-tags', false, '/rich-text-tags/languages' ); // I18n
	
	// ADD EVENTS
	if(	$pagenow == 'edit-tags.php' ) {  //
		if(!user_can_richedit()) { return; }

		if( isset($_GET['action']) && $_GET['action'] == 'edit' && $_GET['taxonomy'] == 'wpm-category'){
            wp_enqueue_script('wpm_rte', plugins_url('/wpm_rt_taxonomy.js', __FILE__), array('jquery'));
            wp_enqueue_style('editor-buttons');
        }
		$taxonomies = get_taxonomies();
		
		foreach($taxonomies as $tax) {
			if($tax != 'wpm-category') continue;
            add_action($tax.'_edit_form_fields', 'wpm_add_form');
			//add_action($tax.'_add_form_fields', 'wpm_add_form');
		}
		
		add_filter('attachment_fields_to_edit', 'wpm_add_form_media', 1, 2);
		add_filter('media_post_single_attachment_fields_to_edit', 'wpm_add_form_media', 1, 2);
		
		if($pagenow == 'edit-tags.php' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && empty($_REQUEST['taxonomy'])) {
			add_action('edit_term','wpm_rt_taxonomy_save');
		}
		
		foreach ( array( 'pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description' ) as $filter ) {
			remove_filter( $filter, 'wp_filter_kses' );
      	}


        //add_action('show_user_profile', 'wpm_add_form', 1);
		//add_action('edit_user_profile', 'wpm_add_form', 1);
		//add_action('edit_user_profile_update', 'wpm_rt_taxonomy_save');
		
		if(empty($_REQUEST['action'])) {
			add_filter('get_terms', 'wpm_shorten_term_description');
		}
	}
	
	// Enable shortcodes in category, taxonomy, tag descriptions
	if(function_exists('term_description')) {
		add_filter('term_description', 'do_shortcode');
	} else {
		add_filter('category_description', 'do_shortcode');
	}
}

// PROCESS FIELDS
function wpm_rt_taxonomy_save() {
	global $tag_ID;
	$a = array('description');
	foreach($a as $v) {
		wp_update_term($tag_ID,$v,$_POST[$v]);
	}
}

function wpm_add_form_media($form_fields, $post) {
	
	$form_fields['post_content']['input'] = 'html';
	
	// We remove the ' and " from the $name so it works for tinyMCE.
	$name = "attachments[$post->ID][post_content]";

	// Let's grab the editor.
	ob_start();
	wp_editor($post->post_content, $name, 
			array(
				'textarea_name' => $name, 
				'editor_css' => wpm_rtt_get_css(),
			)
	);
	$editor = ob_get_clean();

	$form_fields['post_content']['html'] = $editor;

	return $form_fields;
}

function wpm_rtt_get_css() {
	return '
	<style type="text/css">
	    .wp-editor-container .quicktags-toolbar input.ed_button {
			width:auto;
		}
		.html-active .wp-editor-area { border:0;}
	</style>';
}

function wpm_add_form($object = ''){

	global $pagenow;?>
	
	<style type="text/css">
		.quicktags-toolbar input { width:auto!important; }
		.wp-editor-area {border: none!important;}
	</style>
	
	<?php 
		$content = is_object($object) && isset($object->description) ? html_entity_decode($object->description, ENT_COMPAT, 'UTF-8') : '';

		if( in_array($pagenow, array('edit-tags.php')) ) {
			$editor_id = 'tag_description';
			$editor_selector = 'description';
		} else {
			$editor_id = $editor_selector = 'category_description';
		}
		
		?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="description"><?php _ex('Описание', 'Taxonomy Description'); ?></label></th>
        <td><?php wp_editor($content, $editor_id, array(
                    'textarea_name' => $editor_selector,
                    'editor_css' => wpm_rtt_get_css(),
        )); ?><br />
        <span class="description"><?php _e('Описание категории'); ?></span></td>
    </tr>
    <?php
    include_once(WP_PLUGIN_DIR.'/member-luxe/inc/js/wpm-admin-js.php');
}


function wpm_wp_trim_excerpt($text) {

    $text = strip_shortcodes( $text );

    /** This filter is documented in wp-includes/post-template.php */
    $text = apply_filters( 'the_content', $text );
    $text = str_replace(']]>', ']]&gt;', $text);

    /**
     * Filter the number of words in an excerpt.
     *
     * @since 2.7.0
     *
     * @param int $number The number of words. Default 55.
     */
    $excerpt_length = apply_filters( 'excerpt_length', 55 );
    /**
     * Filter the string in the "more" link displayed after a trimmed excerpt.
     *
     * @since 2.9.0
     *
     * @param string $more_string The string shown within the more link.
     */
    $excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
    $text = wp_trim_words( $text, 55, $excerpt_more );

    /**
     * Filter the trimmed excerpt string.
     *
     * @since 2.8.0
     *
     * @param string $text        The trimmed text.
     * @param string $raw_excerpt The text prior to trimming.
     */
    return apply_filters( 'wp_trim_excerpt', $text );
}

function wpm_shorten_term_description($terms = array(), $taxonomies = null, $args = array()) {
	if(is_array($terms)) {
	foreach($terms as $key=>$term) {
		if(is_object($term) && isset($term->description)) {
			$term->description = wpm_wp_trim_excerpt($term->description);

		}
	}
	}
	return $terms;
}
