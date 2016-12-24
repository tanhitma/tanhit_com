<?php
/**
 *
 */
function wpm_design()
{
    wp_enqueue_media();
    $options = get_option('wpm_options');
    include_once('js/admin-js.php');

    ?>
    <script>
        jQuery(function ($) {

            var message = $('.notification');
            message.attr('class', 'notification').css({'display': 'none'}).html('');
            $("form[name=wpm-ajax-form]").on("submit", function (event) {
                event.preventDefault();
                $('.button-preloader').css({'visibility': 'visible'});
                //console.log($( this ).serializeArray());
                //console.log($(this).serializeArray());
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: "wpm_ajax_save_options_action",
                        form_data: $(this).serializeArray(),
                        group: $(this).attr('id')
                    },
                    success: function (data) {
                        //console.log(data);
                        message.html(data.message).fadeIn('slow');
                        setTimeout(function () {
                            message.fadeOut('slow')
                        }, 2000);
                        $('.button-preloader').css({'visibility': 'hidden'});
                    },
                    error: function (errorThrown) {
                        message.html('<span>Произошла ошибка!</span>').addClass().fadeIn('slow');
                        setTimeout(function () {
                            message.fadeOut('slow')
                        }, 2000);
                        $('.button-preloader').css({'visibility': 'hidden'});
                    }
                });
                //console.log( $( this ).serializeArray() );
            });
            // Upload media file ====================================
            var wpm_file_frame;
            var image_id = '';
            $('.wpm-media-upload-button').live('click', function (event) {
                image_id = $(this).attr('data-id');

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (wpm_file_frame) {
                    wpm_file_frame.open();
                    return;
                }

                // Create the media frame.
                wpm_file_frame = wp.media.frames.downloadable_file = wp.media({
                    title: '<?php _e('Выберите файл', 'wpm'); ?>',
                    button: {
                        text: '<?php _e('Использовать изображение', 'wpm'); ?>'
                    },
                    multiple: false
                });

                // When an image is selected, run a callback.
                wpm_file_frame.on('select', function () {
                    var attachment = wpm_file_frame.state().get('selection').first().toJSON();
                    // console.log(attachment);
                    $('input[name=wpm_' + image_id + ']').val(attachment.url);
                    $('#wpm-' + image_id + '-preview').attr('src', attachment.url).show();
                    $('#delete-wpm-' + image_id).show();


                });
                // Finally, open the modal.
                wpm_file_frame.open();
            });
            $('.wpm-delete-media-button').on('click', function () {
                image_id = $(this).attr('data-id');
                $('input[name=wpm_' + image_id + ']').val('');
                $('#delete-wpm-' + image_id).hide();
                $('#wpm-' + image_id + '-preview').hide();
            });
        });
    </script>
    <div class="wrap wpm-options-page">
        <div id="icon-options-general" class="icon32"></div>
        <h2>Дизайн
            <div class="notification"></div>
        </h2>
        <div class="options-wrap wpm-ui-wrap">
            <div id="wpm-options-tabs" class="wpm-tabs wpm-tabs-vertical">
                <ul class="tabs-nav">
                    <li><a href="#tab-1">Логотип, favicon</a></li>
                    <li><a href="#tab-2">Дизайн</a></li>
                    <li><a href="#tab-3">Шапка</a></li>
                    <li><a href="#tab-4">Подвал</a></li>
                </ul>
                <div id="tab-1" class="tab">
                    <div class="wpm-tab-content">
                        <form name="wpm-ajax-form" id="logo">
                            <label>Логотип<br>
                                <input type="text" name="wpm_logo" value="<?php echo $options['logo']['wpm_logo']; ?>"
                                       class="wide"></label>

                            <div class="wpm-control-row">
                                <p>
                                    <button type="button" class="wpm-media-upload-button button"
                                            data-id="logo"><?php _e('Загрузить', 'wpm'); ?></button>
                                    &nbsp;&nbsp; <a id="delete-wpm-logo"
                                                    class="wpm-delete-media-button button submit-delete"
                                                    data-id="logo"><?php _e('Удалить', 'wpm'); ?></a>
                                </p>
                            </div>
                            <div class="wpm-logo-preview-wrap">
                                <div class="wpm-logo-preview-box">
                                    <img src="<?php echo $options['logo']['wpm_logo']; ?>" title="" alt=""
                                         id="wpm-logo-preview">
                                </div>
                            </div>

                            <label>Favicon<br>
                                <input type="text" name="wpm_favicon"
                                       value="<?php echo $options['logo']['wpm_favicon']; ?>" class="wide"></label>

                            <div class="wpm-control-row">
                                <p>
                                    <button type="button" class="wpm-media-upload-button button"
                                            data-id="favicon"><?php _e('Загрузить', 'wpm'); ?></button>
                                    &nbsp;&nbsp; <a id="delete-wpm-favicon"
                                                    class="wpm-delete-media-button button submit-delete"
                                                    data-id="favicon"><?php _e('Удалить', 'wpm'); ?></a>
                                </p>
                            </div>
                            <div class="wpm-favicon-preview-wrap">
                                <div class="wpm-favicon-preview-box">
                                    <img src="<?php echo $options['logo']['wpm_favicon']; ?>" title="" alt=""
                                         id="wpm-favicon-preview">
                                </div>
                            </div>

                            <div class="wpm-tab-footer">
                                <button type="submit"
                                        class="button button-primary wpm-save-options"><?php _e('Сохранить', 'wpm'); ?></button>
                                <span class="buttom-preloader"></span></div>
                        </form>
                    </div>
                </div>
                <div id="tab-2" class="tab">
                    <div class="wpm-tab-content">
                        <form name="wpm-ajax-form" id="design">

                            <div class="wpm-tab-footer">
                                <button type="submit"
                                        class="button button-primary wpm-save-options"><?php _e('Сохранить', 'wpm'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="tab-3" class="tab">
                    <div class="wpm-tab-content">
                        <form name="wpm-ajax-form" id="header">

                            <div class="wpm-tab-footer">
                                <button type="submit"
                                        class="button button-primary wpm-save-options"><?php _e('Сохранить', 'wpm'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="tab-4" class="tab">
                    <div class="wpm-tab-content">
                        <form name="wpm-ajax-form" id="footer">

                            <div class="wpm-tab-footer">
                                <button type="submit"
                                        class="button button-primary wpm-save-options"><?php _e('Сохранить', 'wpm'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php
}



function wpm_default_design_settings()
{

}