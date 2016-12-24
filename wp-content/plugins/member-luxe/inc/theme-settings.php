<?php

wpm_load_theme_css();

function wpm_load_theme_css(){

    $design_options = get_option('wpm_design_options');
    $border_radius = array_key_exists('border-radius', $design_options['main']) ? $design_options['main']['border-radius'] : 5;
    ?>
    <!-- theme custom style -->
    <style type="text/css">
        body{
            background-color: #<?php echo $design_options['main']['background_color']; ?>;
            background-repeat: <?php echo $design_options['main']['background_image']['repeat']; ?>;
            background-position: <?php echo $design_options['main']['background_image']['position']; ?>;
            background-image: url('<?php echo $design_options['main']['background_image']['url']; ?>');
            background-attachment:  <?php echo $design_options['main']['background-attachment-fixed']=='on' ? 'fixed' : 'inherit';?>;
        }
        .wpm-button {
            border-radius: <?php echo $border_radius;?>px !important;
        }
        .wpm-comments-wrap {
            border-radius: <?php echo $border_radius;?>px;
            -webkit-border-radius: <?php echo $border_radius;?>px;
            -moz-border-radius: <?php echo $border_radius;?>px;
            -khtml-border-radius: <?php echo $border_radius;?>px;
            background-color: #<?php echo $design_options['page']['background_color']; ?>;
            border: 1px solid #<?php echo $design_options['page']['border']['color']; ?>;
        }

        .wpm-content-inner-wrap {
            background-color: #<?php echo $design_options['page']['background_color']; ?>;
            border: 1px solid #<?php echo $design_options['page']['border']['color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }
        .wpm-content-wrap .loader, .loader {
            border: 1px solid #<?php echo $design_options['page']['border']['color']; ?>!important;
            background-color: #<?php echo $design_options['page']['background_color']; ?>!important;
        }
        .table-head, tr.table-head td {
            background-color: #<?php echo $design_options['page']['header']['background_color']; ?>;
            color: #<?php echo $design_options['page']['header']['text_color']; ?>;
        }

        .wpm-page-header{
            background-color: #<?php echo $design_options['single']['header']['background_color']; ?>;
            border-color: #<?php echo $design_options['single']['header']['border_color']; ?>;
            border-radius: <?php echo $border_radius;?>px;

        }
        .wpm-page-header .title{
            color: #<?php echo $design_options['single']['header']['title_text_color']; ?>!important;
        }
        .wpm-page-header .description{
            color: #<?php echo $design_options['single']['header']['desc_text_color']; ?>!important;
        }
        .wpm-page-header .col-label, .wpm-page-header .info-row [class^="icon-"], .wpm-page-header .info-row [class*=" icon-"]{
            color: #<?php echo $design_options['single']['header']['label_color']; ?>!important;
        }
        .col-info .description, .col-info .title{
            border-color: #<?php echo $design_options['single']['header']['border_color']; ?>;
            color: #<?php echo $design_options['single']['header']['text_color']; ?>;
        }
        .homework-title, #comments-title{
            border-color: #<?php echo $design_options['page']['border']['color']; ?>;
        }

        .table-head [class^="icon-"], .table-head [class*=" icon-"] {
            color: #<?php echo $design_options['page']['header']['text_color']; ?>;
        }

        .post-row{
            border-bottom: 1px solid #<?php echo $design_options['page']['border']['color']; ?>;
        }
        .post-row:nth-child(even) {
            background-color: #<?php echo $design_options['page']['row']['even']['background_color']; ?>;
            color: #<?php echo $design_options['page']['row']['even']['text_color']; ?>;
        }
        .post-row:nth-child(even):hover{
            background-color: #<?php echo $design_options['page']['row']['even']['background_color_hover']; ?>;
            color: #<?php echo $design_options['page']['row']['even']['text_color_hover']; ?>;
        }
        .post-row:nth-child(odd) {
            background-color: #<?php echo $design_options['page']['row']['odd']['background_color']; ?>;
            color: #<?php echo $design_options['page']['row']['odd']['text_color']; ?>;
        }
        .post-row:nth-child(odd):hover{
            background-color: #<?php echo $design_options['page']['row']['odd']['background_color_hover']; ?>;
            color: #<?php echo $design_options['page']['row']['odd']['text_color_hover']; ?>;
        }
        /* content */
        .wpm-content{
            border: 1px solid #<?php echo $design_options['page']['border']['color']; ?>;
            background-color: #<?php echo $design_options['page']['background_color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }
        .wpm-content-text{
            color: #<?php echo $design_options['page']['text_color']; ?>!important;
        }
        .wpm-content-text a{
            color: #<?php echo $design_options['page']['link_color']; ?>!important;
        }
        .wpm-content-text a:hover, .wpm-content-text a:active{
            color: #<?php echo $design_options['page']['link_color_hover']; ?>!important;
        }

        .wpm-content-wrap .loader {
            border-radius: <?php echo $border_radius;?>px;
        }

        /* menu */
        .sidebar-col{
            font-size: <?php echo $design_options['menu']['font_size']; ?>px;
        }

        .main-menu, .header-content, .footer-content{
            background-color: #<?php echo $design_options['menu']['background_color']; ?>;
            border-color: #<?php echo $design_options['menu']['border']['color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }

        .main-menu li > a, .main-menu .children li:first-child > a{
            border-color: #<?php echo $design_options['menu']['border']['color']; ?>;
        }
        .main-menu li:last-child .children li:first-child > a, .main-menu > li > ul li:last-child{
            border-color: #<?php echo $design_options['menu']['border']['color']; ?>;
        }


        .main-menu .plus{
            border-color: #<?php echo $design_options['menu']['border']['color']; ?>;
            color: #<?php echo $design_options['menu']['border']['color']; ?>;
        }
        .main-menu .plus:hover, .main-menu .current-cat > .plus{
            border-color: #<?php echo $design_options['menu']['a']['active_color']; ?>;
            color: #<?php echo $design_options['menu']['a']['active_color']; ?>;
        }
        .main-menu > li > a{
            color: #<?php echo $design_options['menu']['a']['normal_color']; ?> !important;
            font-weight: <?php echo $design_options['menu']['bold']=='on' ? 'bold' : 'normal'?>;
        }
        .main-menu > li > a:hover{
            color: #<?php echo $design_options['menu']['a']['active_color']; ?> !important;
        }

        .main-menu .current-cat > a, .main-menu .current_page_item > a {
            color: #<?php echo (array_key_exists('selected_link_color', $design_options['menu']['a']) ? $design_options['menu']['a']['selected_link_color'] : $design_options['menu']['a']['active_color']); ?> !important;
            <?php if(array_key_exists('current_bold', $design_options['menu']) && $design_options['menu']['current_bold'] == 'on') echo 'font-weight: bold;' ?>
        }

        .main-menu .children a{
            color: #<?php echo $design_options['menu']['a_submenu']['normal_color']; ?> !important;
            font-weight: <?php echo $design_options['menu']['submenu_bold']=='on' ? 'bold' : 'normal'?>;
        }
        .main-menu .children a:hover{
            color: #<?php echo $design_options['menu']['a_submenu']['active_color']; ?> !important;
        }

        .main-menu .children .current-cat > a {
            color: #<?php echo (array_key_exists('selected_link_color', $design_options['menu']['a_submenu']) ? $design_options['menu']['a_submenu']['selected_link_color'] : $design_options['menu']['a_submenu']['normal_color']); ?> !important;
        }
        /* buttons */
        .show-content{
            border-color: #<?php echo $design_options['buttons']['show']['border_color']; ?>;
            color: #<?php echo $design_options['buttons']['show']['text_color']; ?>;
            background-color: #<?php echo $design_options['buttons']['show']['background_color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }
        .show-content:hover, .post-row:hover .show-content{
            border-color: #<?php echo $design_options['buttons']['show']['border_color_hover']; ?>;
            color: #<?php echo $design_options['buttons']['show']['text_color_hover']; ?>;
            background-color: #<?php echo $design_options['buttons']['show']['background_color_hover']; ?>;
        }
        .no-access{
            border-color: #<?php echo $design_options['buttons']['no_access']['border_color']; ?>;
            color: #<?php echo $design_options['buttons']['no_access']['text_color']; ?>;
            background-color: #<?php echo $design_options['buttons']['no_access']['background_color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }
        .no-access:hover, .post-row:hover .no-access{
            border-color: #<?php echo $design_options['buttons']['no_access']['border_color_hover']; ?>;
            color: #<?php echo $design_options['buttons']['no_access']['text_color_hover']; ?>;
            background-color: #<?php echo $design_options['buttons']['no_access']['background_color_hover']; ?>;
        }
        .back-button{
            border-color: #<?php echo $design_options['buttons']['back']['border_color']; ?>;
            color: #<?php echo $design_options['buttons']['back']['text_color']; ?>;
            background-color: #<?php echo $design_options['buttons']['back']['background_color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }
        .back-button:hover{
            border-color: #<?php echo $design_options['buttons']['back']['border_color_hover']; ?>;
            color: #<?php echo $design_options['buttons']['back']['text_color_hover']; ?>;
            background-color: #<?php echo $design_options['buttons']['back']['background_color_hover']; ?>;
        }
        /* */
        .wpm-homework-respond-button{
            border-color: #<?php echo $design_options['buttons']['home_work_respond_on_page']['border_color']; ?>!important;
            color: #<?php echo $design_options['buttons']['home_work_respond_on_page']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_respond_on_page']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-homework-respond-button:hover{
            border-color: #<?php echo $design_options['buttons']['home_work_respond_on_page']['border_color_hover']; ?>!important;
            color: #<?php echo $design_options['buttons']['home_work_respond_on_page']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_respond_on_page']['background_color_hover']; ?>!important;
        }
        /* */
        .wpm-respond-popup-button{
            color: #<?php echo $design_options['buttons']['home_work_respond_on_popup']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_respond_on_popup']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-respond-popup-button:hover{
            color: #<?php echo $design_options['buttons']['home_work_respond_on_popup']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_respond_on_popup']['background_color_hover']; ?>!important;
        }

        /* */
        .wpm-homework-edit-button{
            border-color: #<?php echo $design_options['buttons']['home_work_edit']['border_color']; ?>!important;
            color: #<?php echo $design_options['buttons']['home_work_edit']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-homework-edit-button:hover{
            border-color: #<?php echo $design_options['buttons']['home_work_edit']['border_color_hover']; ?>!important;
            color: #<?php echo $design_options['buttons']['home_work_edit']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit']['background_color_hover']; ?>!important;
        }
        /* */
        .wpm-homework-edit-popup-button{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-homework-edit-popup-button:hover{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup']['background_color_hover']; ?>!important;
        }
        /* add file button*/
        .wpm-homework-edit-popup-addfile-button{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['background_color']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['border_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-homework-edit-popup-addfile-button:hover{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['background_color_hover']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['border_color_hover']; ?>!important;
        }
        /* upload file button */
        .wpm-homework-edit-popup-upload-button{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['background_color']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['border_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-homework-edit-popup-upload-button:hover{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['background_color_hover']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['border_color_hover']; ?>!important;
        }
        /* cancel upload button */
        .wpm-homework-edit-popup-cancel-button{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['background_color']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['border_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-homework-edit-popup-cancel-button:hover{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['background_color_hover']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['border_color_hover']; ?>!important;
        }
        /* delete file button */
        .wpm-homework-edit-popup-delete-button{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['background_color']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['border_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-homework-edit-popup-delete-button:hover{
            color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['background_color_hover']; ?>!important;
            border-color: #<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['border_color_hover']; ?>!important;
        }
        /* refresh comments */
        .refresh-comments{
            color: #<?php echo $design_options['buttons']['refresh_comments']['text_color']; ?>!important;
        }
        .refresh-comments:hover{
            color: #<?php echo $design_options['buttons']['refresh_comments']['text_color_hover']; ?>!important;
        }
        /* refresh comments */
        .wpm-welcome-tab-login{
            color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_login']; ?>!important;
        }
        .wpm-welcome-tab-login span{
            background-color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_login']; ?>!important;
        }
        .wpm-welcome-tab-login:hover, .active .wpm-welcome-tab-login{
            color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_login_hover']; ?>!important;
        }
        .wpm-welcome-tab-login:hover span, .active .wpm-welcome-tab-login span{
            background-color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_login_hover']; ?>!important;
        }

        .wpm-welcome-tab-register{
            color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_register']; ?>!important;
        }
        .wpm-welcome-tab-register span{
            background-color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_register']; ?>!important;
        }
        .wpm-welcome-tab-register:hover, .active .wpm-welcome-tab-register{
            color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_register_hover']; ?>!important;
        }
        .wpm-welcome-tab-register:hover span, .active .wpm-welcome-tab-register span{
            background-color: #<?php echo $design_options['buttons']['welcome_tabs']['text_color_register_hover']; ?>!important;
        }


        /* */
        .wpm-comment-button{
            border-color: #<?php echo $design_options['buttons']['send_comment']['border_color']; ?>!important;
            color: #<?php echo $design_options['buttons']['send_comment']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['send_comment']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-comment-button:hover{
            border-color: #<?php echo $design_options['buttons']['send_comment']['border_color_hover']; ?>!important;
            color: #<?php echo $design_options['buttons']['send_comment']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['send_comment']['background_color_hover']; ?>!important;
        }
        /* */
        .wpm-sign-in-button{
            color: #<?php echo $design_options['buttons']['sign_in']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['sign_in']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-sign-in-button:hover{
            color: #<?php echo $design_options['buttons']['sign_in']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['sign_in']['background_color_hover']; ?>!important;
        }


        /*  */
        .wpm-button-ask{
            color: #<?php echo $design_options['buttons']['ask']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['ask']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-button-ask:hover{
            color: #<?php echo $design_options['buttons']['ask']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['ask']['background_color_hover']; ?>!important;
        }
        /*  */
        .wpm-register-button{
            color: #<?php echo $design_options['buttons']['register']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['register']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-register-button:hover{
            color: #<?php echo $design_options['buttons']['register']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['register']['background_color_hover']; ?>!important;
        }
        /**/
        .wpm-activate-pin-button{
            color: #<?php echo $design_options['buttons']['activate_pin']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['activate_pin']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-activate-pin-button:hover{
            color: #<?php echo $design_options['buttons']['activate_pin']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['activate_pin']['background_color_hover']; ?>!important;
        }
        /**/
        .wpm-get-pin-code-button{
            color: #<?php echo $design_options['buttons']['get_pin']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['get_pin']['background_color']; ?>!important;
            border-radius: <?php echo $border_radius;?>px!important;
        }
        .wpm-get-pin-code-button:hover{
            color: #<?php echo $design_options['buttons']['get_pin']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['get_pin']['background_color_hover']; ?>!important;
        }
        /**/
        .wpm-copy-pin-code-button{
            border-color: #<?php echo $design_options['buttons']['copy_pin']['border_color']; ?>;
            color: #<?php echo $design_options['buttons']['copy_pin']['text_color']; ?>;
            background-color: #<?php echo $design_options['buttons']['copy_pin']['background_color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }
        .wpm-copy-pin-code-button:hover, .wpm-pin-code-page .wpm-button.zeroclipboard-is-hover{
            border-color: #<?php echo $design_options['buttons']['copy_pin']['border_color_hover']; ?>!important;
            color: #<?php echo $design_options['buttons']['copy_pin']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['copy_pin']['background_color_hover']; ?>!important;
        }
        /**/
        .wpm-register-on-pin-button{
            border-color: #<?php echo $design_options['buttons']['register_on_pin']['border_color']; ?>;
            color: #<?php echo $design_options['buttons']['register_on_pin']['text_color']; ?>;
            background-color: #<?php echo $design_options['buttons']['register_on_pin']['background_color']; ?>;
            border-radius: <?php echo $border_radius;?>px;
        }
        .wpm-register-on-pin-button:hover{
            border-color: #<?php echo $design_options['buttons']['register_on_pin']['border_color_hover']; ?>!important;
            color: #<?php echo $design_options['buttons']['register_on_pin']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['register_on_pin']['background_color_hover']; ?>!important;
        }
        /**/
        .wpm-top-admin-bar, .wpm-top-admin-bar .wpm-nav-bar, .wpm-top-admin-bar .nav-menu-mobile, .wpm-top-admin-bar .toggle-mobile-menu-wrap{
            background-color: #<?php echo $design_options['buttons']['top_admin_bar']['background_panel_color']; ?>!important;
        }
        .wpm-top-admin-bar a{
            color: #<?php echo $design_options['buttons']['top_admin_bar']['text_color']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['top_admin_bar']['background_color']; ?>!important;
        }
        .wpm-top-admin-bar a:hover{
            color: #<?php echo $design_options['buttons']['top_admin_bar']['text_color_hover']; ?>!important;
            background-color: #<?php echo $design_options['buttons']['top_admin_bar']['background_color_hover']; ?>!important;
        }
        .wpm-top-admin-bar .menu-toggle span{
            background-color: #<?php echo $design_options['buttons']['top_admin_bar']['text_color']; ?>;
        }

        /* comments */
        .comment-image > a{
            border-color: #<?php echo $design_options['page']['border']['color']; ?>;
        }
        /* preloader */
        #preloader_1 span{
            background: #<?php echo $design_options['preloader']['color_1']; ?>!important;
        }
        @keyframes preloader_1 {
            0% {height:5px;transform:translateY(0px);background:#<?php echo $design_options['preloader']['color_1']; ?>!important;}
            25% {height:30px;transform:translateY(15px);background:#<?php echo $design_options['preloader']['color_1']; ?>!important;}
            50% {height:5px;transform:translateY(0px);background:#<?php echo $design_options['preloader']['color_1']; ?>!important;}
            100% {height:5px;transform:translateY(0px);background:#<?php echo $design_options['preloader']['color_1']; ?>!important;}
        }

    </style>
    <!-- // theme custom style -->
<?php }