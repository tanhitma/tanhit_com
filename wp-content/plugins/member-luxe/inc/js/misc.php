<?php



//add_action('admin_head', 'wpm_check_key');
function wpm_check_key(){

    $wpm_key = get_option('wpm_key');
    global $current_screen;
    global $typenow;

    //$current_screen->post_type == 'wpm-page' || $typenow == 'wpm-page' || $_GET['taxonomy'] == 'wpm-levels' ||
    if($current_screen->post_type == 'wpm-page' || $typenow == 'wpm-page' || $_GET['taxonomy'] == 'wpm-levels' || $_GET['post_type'] == 'wpm-page'){

        if(!$wpm_key || empty($wpm_key)){

            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $redirect_url = site_url('wp-admin/edit.php?post_type=wpm-page&page=wpm-options');

            if($redirect_url != $actual_link){
                ?>
                <script type="text/javascript">
                    window.location = '<?php echo $redirect_url; ?>';
                </script>
            <?php
            }

           // wp_redirect($redirect_url);
           // exit;
        }
    }

}

