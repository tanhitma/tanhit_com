<?php

if (!is_admin())
    return;

//require_once(dirname(__FILE__) . '/wpm_duplicate-post-options.php');

/**
 * Wrapper for the option 'wpm_duplicate_post_version'
 */
function wpm_duplicate_post_get_installed_version()
{
    return get_option('wpm_duplicate_post_version');
}

/**
 * Wrapper for the defined constant DUPLICATE_POST_CURRENT_VERSION
 */
function wpm_duplicate_post_get_current_version()
{
    return WPM_DUPLICATE_POST_CURRENT_VERSION;
}

/**
 * Plugin upgrade
 */
add_action('admin_init', 'wpm_duplicate_post_plugin_upgrade');

function wpm_duplicate_post_plugin_upgrade()
{
    $installed_version = wpm_duplicate_post_get_installed_version();

    if (empty($installed_version)) { // first install

        // Add capability to admin and editors

        // Get default roles
        $default_roles = array(
            3 => 'editor',
            8 => 'administrator',
        );

        // Cycle all roles and assign capability if its level >= wpm_duplicate_post_copy_user_level
        foreach ($default_roles as $level => $name) {
            $role = get_role($name);
            if (!empty($role)) $role->add_cap('copy_posts');
        }

        update_option('wpm_duplicate_post_copyexcerpt', '1');
        update_option('wpm_duplicate_post_copyattachments', '1');
        update_option('wpm_duplicate_post_copychildren', '0');
        update_option('wpm_duplicate_post_copystatus', '0');
        update_option('wpm_duplicate_post_taxonomies_blacklist', array());
        update_option('wpm_duplicate_post_show_row', '1');
        update_option('wpm_duplicate_post_show_adminbar', '0');
        update_option('wpm_duplicate_post_show_submitbox', '0');
    }
    // Update version number
    update_option('wpm_duplicate_post_version', wpm_duplicate_post_get_current_version());

}

if (get_option('wpm_duplicate_post_show_row') == 1) {

    add_filter('page_row_actions', 'wpm_duplicate_post_make_wpm_duplicate_link_row', 10, 2);
}

/**
 * Add the link to action list for post_row_actions
 */
function wpm_duplicate_post_make_wpm_duplicate_link_row($actions, $post)
{
    global $current_screen;
    if ($current_screen->post_type == 'wpm-page') {
        if (wpm_duplicate_post_is_current_user_allowed_to_copy()) {
            $actions['clone'] = '<a href="' . wpm_duplicate_post_get_clone_post_link($post->ID, 'display', false) . '" title="'
                . esc_attr(__("Создать копию страницы", @wpm_DUPLICATE_POST_I18N_DOMAIN))
                . '">' . __('Клонировать', WPM_DUPLICATE_POST_I18N_DOMAIN) . '</a>';
            /*$actions['edit_as_new_draft'] = '<a href="'. wpm_duplicate_post_get_clone_post_link( $post->ID ) .'" title="'
            . esc_attr(__('Copy to a new draft', WPM_DUPLICATE_POST_I18N_DOMAIN))
            . '">' .  __('New Draft', WPM_DUPLICATE_POST_I18N_DOMAIN) . '</a>';*/
        }
    }
    return $actions;
}

/**
 * Add a button in the post/page edit screen to create a clone
 */
/*if (get_option('wpm_duplicate_post_show_submitbox') == 1){
	add_action( 'post_submitbox_start', 'wpm_duplicate_post_add_wpm_duplicate_post_button' );
}

function wpm_duplicate_post_add_wpm_duplicate_post_button()
{
    if (isset($_GET['post']) && wpm_duplicate_post_is_current_user_allowed_to_copy()) {
        ?>
        <div id="wpm_duplicate-action">
            <a class="submitwpm_duplicate duplication"
               href="<?php echo wpm_duplicate_post_get_clone_post_link($_GET['post']) ?>"><?php _e('Copy to a new draft', WPM_DUPLICATE_POST_I18N_DOMAIN); ?>
            </a>
        </div>
    <?php
    }
}
*/
/**
 * Connect actions to functions
 */
add_action('admin_action_wpm_duplicate_post_save_as_new_post', 'wpm_duplicate_post_save_as_new_post');
add_action('admin_action_wpm_duplicate_post_save_as_new_post_draft', 'wpm_duplicate_post_save_as_new_post_draft');

/*
 * This function calls the creation of a new copy of the selected post (as a draft)
 * then redirects to the edit post screen
 */
function wpm_duplicate_post_save_as_new_post_draft()
{
    wpm_duplicate_post_save_as_new_post('draft');
}

/*
 * This function calls the creation of a new copy of the selected post (by default preserving the original publish status)
 * then redirects to the post list
 */
function wpm_duplicate_post_save_as_new_post($status = '')
{
  //  echo 'wpm_duplicate_post_save_as_new_post';
    if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'wpm_duplicate_post_save_as_new_post' == $_REQUEST['action']))) {
        wp_die(__('No post to wpm_duplicate has been supplied!', WPM_DUPLICATE_POST_I18N_DOMAIN));
    }

    // Get the original post
    $id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
    $post = get_post($id);

    // Copy the post and insert it
    if (isset($post) && $post != null) {
        $new_id = wpm_duplicate_post_create_wpm_duplicate($post, $status);

        if ($status == '') {
            // Redirect to the post list screen
            wp_redirect(admin_url('edit.php?post_type=' . $post->post_type));
        } else {
            // Redirect to the edit screen for the new draft post
            wp_redirect(admin_url('post.php?action=edit&post=' . $new_id));
        }
        exit;

    } else {
        $post_type_obj = get_post_type_object($post->post_type);
        wp_die(esc_attr(__('Copy creation failed, could not find original:', WPM_DUPLICATE_POST_I18N_DOMAIN)) . ' ' . $id);
    }
}

/**
 * Get the currently registered user
 */
function wpm_duplicate_post_get_current_user()
{
    if (function_exists('wp_get_current_user')) {
        return wp_get_current_user();
    } else if (function_exists('get_currentuserinfo')) {
        global $userdata;
        get_currentuserinfo();
        return $userdata;
    } else {
        $user_login = $_COOKIE[USER_COOKIE];
        $current_user = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_login='$user_login'");
        return $current_user;
    }
}

/**
 * Copy the taxonomies of a post to another post
 */
function wpm_duplicate_post_copy_post_taxonomies($new_id, $post)
{
    global $wpdb;
    if (isset($wpdb->terms)) {
        // Clear default category (added by wp_insert_post)
        wp_set_object_terms($new_id, NULL, 'category');

        $post_taxonomies = get_object_taxonomies($post->post_type);
        $taxonomies_blacklist = get_option('wpm_duplicate_post_taxonomies_blacklist');
        if ($taxonomies_blacklist == "") $taxonomies_blacklist = array();
        $taxonomies = array_diff($post_taxonomies, $taxonomies_blacklist);
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post->ID, $taxonomy, array('orderby' => 'term_order'));
            $terms = array();
            for ($i = 0; $i < count($post_terms); $i++) {
                $terms[] = $post_terms[$i]->slug;
            }
            wp_set_object_terms($new_id, $terms, $taxonomy);
        }
    }
}

// Using our action hooks to copy taxonomies
add_action('dp_wpm_duplicate_post', 'wpm_duplicate_post_copy_post_taxonomies', 10, 2);
add_action('dp_wpm_duplicate_page', 'wpm_duplicate_post_copy_post_taxonomies', 10, 2);

/**
 * Copy the meta information of a post to another post
 */
function wpm_duplicate_post_copy_post_meta_info($new_id, $post)
{
    $post_meta_keys = get_post_custom_keys($post->ID);
    if (empty($post_meta_keys)) return;
    $meta_blacklist = explode(",", get_option('wpm_duplicate_post_blacklist'));
    if ($meta_blacklist == "") $meta_blacklist = array();
    $meta_keys = array_diff($post_meta_keys, $meta_blacklist);

    foreach ($meta_keys as $meta_key) {
        $meta_values = get_post_custom_values($meta_key, $post->ID);
        foreach ($meta_values as $meta_value) {
            $meta_value = maybe_unserialize($meta_value);
            add_post_meta($new_id, $meta_key, $meta_value);
        }
    }
}

// Using our action hooks to copy meta fields
add_action('dp_wpm_duplicate_post', 'wpm_duplicate_post_copy_post_meta_info', 10, 2);
add_action('dp_wpm_duplicate_page', 'wpm_duplicate_post_copy_post_meta_info', 10, 2);

/**
 * Copy the attachments
 * It simply copies the table entries, actual file won't be wpm_duplicated
 */
function wpm_duplicate_post_copy_children($new_id, $post)
{
    $copy_attachments = get_option('wpm_duplicate_post_copyattachments');
    $copy_children = get_option('wpm_duplicate_post_copychildren');

    // get children
    $children = get_posts(array('post_type' => 'any', 'numberposts' => -1, 'post_status' => 'any', 'post_parent' => $post->ID));
    // clone old attachments
    foreach ($children as $child) {
        if ($copy_attachments == 0 && $child->post_type == 'attachment') continue;
        if ($copy_children == 0 && $child->post_type != 'attachment') continue;
        wpm_duplicate_post_create_wpm_duplicate($child, '', $new_id);
    }
}

// Using our action hooks to copy attachments
add_action('dp_wpm_duplicate_post', 'wpm_duplicate_post_copy_children', 10, 2);
add_action('dp_wpm_duplicate_page', 'wpm_duplicate_post_copy_children', 10, 2);


/**
 * Create a wpm_duplicate from a post
 */
function wpm_duplicate_post_create_wpm_duplicate($post, $status = '', $parent_id = '')
{

    // We don't want to clone revisions
    if ($post->post_type == 'revision') return;

    if ($post->post_type != 'attachment') {
        $prefix = get_option('wpm_duplicate_post_title_prefix');
        $suffix = get_option('wpm_duplicate_post_title_suffix');
        if (!empty($prefix)) $prefix .= " ";
        if (!empty($suffix)) $suffix = " " . $suffix;
        if (get_option('wpm_duplicate_post_copystatus') == 0) $status = 'draft';
    }
    $new_post_author = wpm_duplicate_post_get_current_user();

    $new_post = array(
        'menu_order'     => $post->menu_order,
        'comment_status' => $post->comment_status,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author->ID,
        'post_content'   => $post->post_content,
        'post_excerpt'   => (get_option('wpm_duplicate_post_copyexcerpt') == '1') ? $post->post_excerpt : "",
        'post_mime_type' => $post->post_mime_type,
        'post_parent'    => $new_post_parent = empty($parent_id) ? $post->post_parent : $parent_id,
        'post_password'  => $post->post_password,
        'post_status'    => $new_post_status = (empty($status)) ? $post->post_status : $status,
        'post_title'     => $prefix . $post->post_title . $suffix,
        'post_type'      => $post->post_type,
    );

    if (get_option('wpm_duplicate_post_copydate') == 1) {
        $new_post['post_date'] = $new_post_date = $post->post_date;
        $new_post['post_date_gmt'] = get_gmt_from_date($new_post_date);
    }

    $new_post_id = wp_insert_post($new_post);


    // If you have written a plugin which uses non-WP database tables to save
    // information about a post you can hook this action to dupe that data.
    if ($post->post_type == 'page' || (function_exists('is_post_type_hierarchical') && is_post_type_hierarchical($post->post_type)))
        do_action('dp_wpm_duplicate_page', $new_post_id, $post);
    else
        do_action('dp_wpm_duplicate_post', $new_post_id, $post);

    delete_post_meta($new_post_id, '_dp_original');
    add_post_meta($new_post_id, '_dp_original', $post->ID);

    // If the copy is published or scheduled, we have to set a proper slug.
    if ($new_post_status == 'publish' || $new_post_status == 'future') {
        $post_name = wp_unique_post_slug($post->post_name, $new_post_id, $new_post_status, $post->post_type, $new_post_parent);

        $new_post = array();
        $new_post['ID'] = $new_post_id;
        $new_post['post_name'] = $post_name;

        // Update the post into the database
        wp_update_post($new_post);
    }

    return $new_post_id;
}
