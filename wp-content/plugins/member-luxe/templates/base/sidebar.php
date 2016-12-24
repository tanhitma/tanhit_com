<?php

$home_page = get_post(get_option('wpm_start_page'));
$main_options = get_option('wpm_main_options');
$start_page = $main_options['home_id'];
$schedule_page = $main_options['schedule_id'];
$schedule_is_hidden = (array_key_exists('hide_schedule', $main_options) && $main_options['hide_schedule']=='on') ? true : false;

$term_list = wp_get_post_terms(get_the_ID(), 'wpm-category', array("fields" => "ids"));

$args = array(
    'include'   => $start_page,
    'post_type' => 'wpm-page',
); ?>

<ul class="main-menu">
    <?php
    if(is_single()){
        $terms = get_the_terms(get_the_ID(), 'wpm-category');
        if(!empty($terms)){
            $current_category = $terms[0]->term_id;
        }else{
            $current_category = '';
        }
    }else{
        $current_category = '';
    }

    $user_status  = wpm_get_user_status($current_user->ID);

    if ($user_status == 'active' || in_array('administrator', $current_user->roles)) {

        if ($start_page) {
            wp_list_pages('post_type=wpm-page&title_li=&include=' . $start_page);
        }

//        $exclude_terms = implode(',', wpm_get_excluded_categories($current_category));
        $exclude_terms = wpm_get_excluded_categories($current_category);
        $args_list = array(
            'taxonomy'         => 'wpm-category', // Registered tax name
            'show_count'       => false,
            'title_li'         => '',
            'hide_empty'       => 0,
            'hierarchical'     => true,
            'current_category' => $current_category,
            'echo'             => 0,
            'depth'            => 0,
            'exclude'          => $exclude_terms
        );

        $tax = get_terms('wpm-category', array(
            'hide_empty' => 0,
            'exclude' => $exclude_terms
        ));
        if (!empty($tax)) {
            wpm_category_list_with_ancestor_class($args_list);
        }
    }
    ?>
</ul>
<?php
if (!$schedule_is_hidden && $schedule_page && $schedule_page != 'no') { ?>
    <ul class="main-menu schedule-menu">
        <?php wp_list_pages('post_type=wpm-page&title_li=&include=' . $schedule_page); ?>
    </ul>
<?php } ?>
<button
    class="wpm-button back-button sidebar-back-button"><?php _e("Вернуться к списку материалов", "wpm"); ?></button>

