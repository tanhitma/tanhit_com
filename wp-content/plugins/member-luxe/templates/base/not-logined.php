<?php $design_options = get_option('wpm_design_options'); ?>
<script type="text/javascript">
    jQuery(function ($) {
        var hash = window.location.hash;
        if(hash == '#registration'){
            $('a[href="#wpm-register"]').click();
        }else{
            $('a[href="#wpm-login"]').click();
        }
    });
</script>
<div class="wpm-welcome-page">
    <?php
    $logo_url = $main_options['logo']['url'];
    if (!empty($logo_url)) {
        echo "<img class='logo text-center' title='' alt='' src='$logo_url'>";
    }
    ?>
    <?php
    if($main_options['login_content']['visible'] && $main_options['login_content']['position'] == 'top'){ ?>
    <div class="login-content top">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="wpm-content-text">
                        <?php echo apply_filters('the_content', $main_options['login_content']['content']); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php } ?>
    <div id="wpm-login-tabs" class="wpm-tabs">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#wpm-login" data-toggle="tab" class="wpm-welcome-tab-login wpm-welcome-tab"><span>1</span><?php echo $design_options['buttons']['welcome_tabs']['text_login']; ?></a></li>
            <li><a href="#wpm-register" data-toggle="tab" class="wpm-welcome-tab-register wpm-welcome-tab"><span>2</span><?php echo $design_options['buttons']['welcome_tabs']['text_register']; ?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="wpm-login">
                <div class="tab-content-inner">
                    <?php wpm_ajax_login_form(); ?>
                </div>
            </div>
            <div class="tab-pane" id="wpm-register">
                <div class="tab-content-inner">
                    <?php wpm_register_form(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    if($main_options['login_content']['visible'] && $main_options['login_content']['position'] == 'bottom'){ ?>
        <div class="login-content botton">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="wpm-content-text">
                            <?php echo apply_filters('the_content', $main_options['login_content']['content']); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php } ?>
</div>