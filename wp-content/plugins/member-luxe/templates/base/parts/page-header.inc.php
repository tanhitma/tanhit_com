<?php
if ($main_options['header']['visible'] == 'on') {
    $levels_ids = wpm_get_all_user_accesible_levels(get_current_user_id());
    $headers = explode(',', $main_options['headers']['priority']);
    $page_header = '';
    foreach($headers as $item){
        if(in_array($item, $levels_ids) && $main_options['headers']['headers'][$item]['disabled'] != 'disabled' ){
            $page_header = $main_options['headers']['headers'][$item]['content'];
            break;
        }
    }
    if(empty($page_header)){
        $page_header = $main_options['headers']['headers']['default']['content'];
    }
    ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="content-col wpm-single header-wrap">
                <div class="header-content wpm-content-text">
                    <?php
                    add_filter('the_content', 'wpm_add_infoprotector_key_to_url');
                    echo apply_filters('the_content', stripslashes($page_header)); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>