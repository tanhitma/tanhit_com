<!-- wppage admin js -->
<script type="text/javascript">
jQuery(function ($) {

    // ajax url
    var wp_ajax = {"ajaxurl": "<?php echo admin_url('admin-ajax.php'); ?>"};


    $('.wpm-inner-tabs').wpmSmartTabs({'direction': 'horizontal'});
    $('.wpm-tabs').wpmSmartTabs({'direction': 'vertical'});
    $('.headers-design-tabs').wpmSmartTabs({'direction': 'horizontal', 'type': 'sortable'});


//------------------------- radio, checkbox, select

    $('.wppage_radio input:checked').each(function () {
        $(this).parent().addClass('wppage_checked');
    });

    $('.wppage_radio input').live('change', function () {
        var name = $(this).attr('name');
        $('input[name = ' + name + ']').each(function () {
            $(this).parent().removeClass('wppage_checked');
        });
        $(this).parent().addClass('wppage_checked');
    });

    $('.wpp_subsc_thumb input').live('change', function () {
        if ($(this).hasClass('trial')) return false;
        var name = $(this).attr('name');
        $('input[name = ' + name + ']').each(function () {
            $(this).parent().removeClass('wppage_checked');
        });
        $(this).parent().addClass('wppage_checked');
    });
    $('.ps_bullets_form input, .p_cbutton input, .ps_satisfaction input, .ps_arrows input, .ps_bonus input, .ps_cbutton input, .ps_timer_image input, .wpp_header input').live('change', function () {
        var name = $(this).attr('name');
        $('input[name = ' + name + ']').each(function () {
            $(this).parent().removeClass('wppage_checked');
        });
        $(this).parent().addClass('wppage_checked');
    });


    $('.wppage_checkbox input:checked, .p_cbutton input:checked').each(function () {
        $(this).parent().addClass('wppage_checked');
    });

    $('.wppage_checkbox input:checkbox').live('change', function () {
        $(this).parent().toggleClass('wppage_checked');
    });

    $('.wppage_checkbox input:radio').live('change', function () {
        $('input[name=' + $(this).attr("name") + ']').each(function () {
            $(this).parent().removeClass('wppage_checked');
        });
        $(this).parent().toggleClass('wppage_checked');
    });
    $('.wppage_checkbox input:disabled, .wppage_radio input:disabled').live('click', function () {
        return false;
    });


//----------------------------- video border style  -------

    $('.wppage_radio_V input:checked').each(function () {
        $(this).parent().addClass('wppage_checked');
    });

    $('.wppage_radio_v input').live('change', function () {
        if ($('input[name=video_border]:checked').val() == 'yes') {
            var name = $(this).attr('name');
            $('input[name = ' + name + ']').each(function () {
                $(this).parent().removeClass('wppage_checked');
            });
            $(this).parent().addClass('wppage_checked');
        }
    });

    $('input[name=video_border]').live('change', function () {
        if ($(this).val() == 'yes') {
            $('#video-width, #video-height').attr('disabled', 'disabled');
            $('.video_border_640 input').click();
            $('.video_styles label:first-child input').click();
        } else {
            $('#video-width, #video-height').removeAttr('disabled');
            $('.video_border_sizes label, .video_styles > label').removeClass('wppage_checked');
            $('.video_border_sizes input:checked, .video_styles input:checked').removeAttr('checked');
        }
    });
    $('input[name=video_border_size]').live('click', function () {

        if ($('input[name=video_border]:checked').val() == 'yes') {
            if ($(this).val() == '480x270') {
                $('#video-width').val('480');
                $('#video-height').val('270');
            }
            if ($(this).val() == '560x315') {
                $('#video-width').val('560');
                $('#video-height').val('315');
            }
            if ($(this).val() == '640x360') {
                $('#video-width').val('640');
                $('#video-height').val('360');
            }
            if ($(this).val() == '720x405') {
                $('#video-width').val('720');
                $('#video-height').val('405');
            }
        }
    });


// --------------------------- wppage editor

    $('.wppage_edit_ico, .wppage_text_preview').click(function () {
        var name = $(this).attr('text_id');
        // tinyMCE.get(name).dom.setStyles(tinyMCE.get(name).dom.select('body'), {'min-height': '75px', 'line-height': 'normal'});
        $('.wppage_editor_box').css({'display': 'none', 'opacity': 0});
        //$('.' + $(this).attr('text_id')).css({'display':'block'});
        $('.' + $(this).attr('text_id') + '_box').css({'display': 'block'}).animate({'opacity': 1}, 150, function () {
            // alert(tinyMCE.get(name).getContent(content));
        });
    });

    $('a.wppage_save_text').click(function () {
        var name = $(this).attr('text_id');
        var content = tinyMCE.get(name).getContent();
        $('.' + name + '_text').html(content);
        $(this).parent().parent().animate({'opacity': 0}, 150).css({'display': 'none'});

    });
    $('a.wppage_cancel_text').click(function () {
        var name = $(this).attr('text_id');
        var content = $('.' + name + '_text').html();
        tinyMCE.get(name).setContent(content);
        $(this).parent().parent().css({'display': 'none'});
    });
//---------------------------   wppage select

    $('#wpp_media_smartresponder_button_style_selected, #wpp_smartresponder_button_style_selected').live('click', function () {
        var name = $(this).attr('box_id');
        $('.' + name).css({'display': 'block'});
    });
    $('a.wpp_close_box').click(function () {
        $(this).parent().css({'display': 'none'});
    });
    $('input[name=wpp_media_smartresponder_button_style]').live('change', function () {
        $('#wpp_media_smartresponder_button_style_selected').attr('class', '').addClass('ps_button_' + $(this).val());
    });
    $('input[name=wpp_smartresponder_button_style]').live('change', function () {
        $('#wpp_smartresponder_button_style_selected').attr('class', '').addClass('ps_button_' + $(this).val());
    });


//------------------------------ wpp_smartresponder_form_version

    $('input[name=wpp_smartresponder_form_version]:checked').each(function () {
        $('.wpp_smartresponder_settings').css({'display': 'none'});
        $('#wppage_inner_tab_' + $(this).val()).css({'display': 'block'});
    });

    $('input[name=wpp_smartresponder_form_version]').live('change', function () {
        $('.wpp_smartresponder_settings').css({'display': 'none'});
        $('#wppage_inner_tab_' + $(this).val()).css({'display': 'block'});
    });


    $('input[name=wpp_media_smartresponder_form_version]:checked').each(function () {
        $('.wpp_media_smartresponder_settings_box').css({'display': 'none'});
        $('#wppage_media_inner_tab_' + $(this).val()).css({'display': 'block'});
    });

    $('input[name=wpp_media_smartresponder_form_version]').live('change', function () {
        $('.wpp_media_smartresponder_settings_box').css({'display': 'none'});
        $('#wppage_media_inner_tab_' + $(this).val()).css({'display': 'block'});
    });


//------------------------- find uid gid

    $('#coach_responder_code').bind('blur', function () {

        var uid = did = tid = '';
        $('#crc_temp_1').html($('#coach_responder_code').val());
        uid = $('#crc_temp_1').find('input[name="uid"]').val();
        did = $('#crc_temp_1').find('input[name="did[]"]').val();
        tid = $('#crc_temp_1').find('input[name="tid"]').val();


        $('#coach_responder_uid').attr('value', uid);
        $('#coach_responder_did').attr('value', did);
        $('#coach_responder_tid').attr('value', tid);

    });

    $('#wpp_getresponse_code').bind('blur', function () {

        var wid = '';
        $('#getresponse_code_temp').html($('#wpp_getresponse_code').val());
        var url = $('#getresponse_code_temp').find('script').attr('src');

        $('#wpp_getresponse_wid').attr('value', wid);

    });

    $('#smartresponder_code').live('change', (function () {
        var uid = did = tid = "";
        $('body').append('<div id="temp_code" style="display:none"></div>');
        $("#temp_code").html($('#smartresponder_code').val());

        uid = $("#temp_code").find("input[name='uid']").val();
        did = $("#temp_code").find("input[name='did[]']").val();
        tid = $("#temp_code").find("input[name='tid']").val();
        $("#r_uid").attr("value", uid);
        $("#r_did").attr("value", did);
        $("#r_tid").attr("value", tid);
        $('#temp_code').remove();
    }));

    $('#wpp_smartresponder_code').live('change', (function () {
        var uid = did = tid = "";
        $('body').append('<div id="crc_temp_2" style="display:none"></div>');
        $("#crc_temp_2").html($('#wpp_smartresponder_code').val());

        uid = $("#crc_temp_2").find("input[name='uid']").val();
        did = $("#crc_temp_2").find("input[name='did[]']").val();
        tid = $("#crc_temp_2").find("input[name='tid']").val();
        $("#wpp_smartresponder_uid").attr("value", uid);
        $("#wpp_smartresponder_did").attr("value", did);
        $("#wpp_smartresponder_tid").attr("value", tid);
        $('#crc_temp_2').remove();
    }));
    $('#wpp_media_smartresponder_code').live('change', (function () {
        var uid = did = tid = "";
        $('body').append('<div id="crc_temp_3" style="display:none"></div>');
        $("#crc_temp_3").html($('#wpp_media_smartresponder_code').val());

        uid = $("#crc_temp_3").find("input[name='uid']").val();
        did = $("#crc_temp_3").find("input[name='did[]']").val();
        tid = $("#crc_temp_3").find("input[name='tid']").val();
        $("#wpp_media_smartresponder_uid").attr("value", uid);
        $("#wpp_media_smartresponder_did").attr("value", did);
        $("#wpp_media_smartresponder_tid").attr("value", tid);
        $('#crc_temp_3').remove();
    }));
    $('#sortable_comments').sortable({
        update: function (event, ui) {
            var order = [];
            $('#sortable_comments > li input').each(function () {
                order.push($(this).val());
            });
            $('#use_comments_order').val(order);
        }
    });

//---------------

    $('#ps_background_image_selected').live('click', function () {
        var name = $(this).attr('box_id');
        $('.' + name).css({'display': 'block'});
    });

    $('#ps_background_image').bind('change', function () {
        $('#bg_preview').css({'background-image': 'url(' + $("#ps_background_image").val() + ')'});

    });

    $('.wpp_bg_images_content .wpp_bg').live('click', function () {
        var bg = $(this).children('input').val();
        if (bg != 'no_image') {
            $('#bg_preview').css({'background-image': 'url(' + bg + ')'});
            $('#ps_background_image').val(bg);

        } else {
            $('#bg_preview').css({'background-image': ''}).addClass('no_image');
            $('#ps_background_image').val('');
        }

    });

    //-------------------------

    $('.page-header-add').on('click', function(e){
        e.preventDefault();
        var items_input = $('#wpm-design-headers-priority');
        var items = items_input.val().split(",");
        var items_select = $('#users-level-for-header');
        if(items.indexOf(items_select.val()) == -1){
            items.push(items_select.val());
            items_input.val(items);
            $('form[name=wpm-settings-form]').submit();
        }
    });

    $('.page-header-remove').on('click', function(e){
        e.preventDefault();
        var items_input = $('#wpm-design-headers-priority');
        var items = items_input.val().split(",");
        var element_index = items.indexOf($(this).attr('header-id'));
        if(element_index != -1){
            items.splice(element_index, 1);
            items_input.val(items);
            $('form[name=wpm-settings-form]').submit();
        }
    });


});


function getParameterByName(name, string) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(string);
    if (results == null)
        return "";
    else
        return decodeURIComponent(results[1].replace(/\+/g, " "));
}


(function ($) {

    /* Smart tabs */

    $.fn.wpmSmartTabs = function (params) {

        var tab_id = this.attr('tab-id');
        var index = tab_id + '_active_tab_index';

        var old_index = 0;

        var orders = [];

        try {
            // getter: Fetch previous value
            old_index = window.sessionStorage.getItem(index);
        } catch (e) {
        }

        var tabs = this.tabs({
            active: old_index,
            items: "li:not(.ui-state-disabled)",
            activate: function (event, ui) {
                //  Get future value
                var new_index = ui.newTab.parent().children().index(ui.newTab);
                //  Set future value
                try {
                    window.sessionStorage.setItem(index, new_index);
                } catch (e) {
                }
            }
        });

        if (params['direction'] == 'vertical') {
            tabs.addClass('ui-tabs-vertical ui-helper-clearfix');
        }

        if (params['type'] == 'sortable') {
            tabs.find( ".ui-tabs-nav" ).sortable({
                items: "li:not(.ui-state-disabled)",
                axis: "x",
                stop: function() {
                    tabs.tabs( "refresh" );
                },
                update: function( event, ui ) {
                    orders = $(this).sortable('toArray', {'attribute': 'header-id'});
                   $('#wpm-design-headers-priority').val(orders.join());
                }
            });
        }
       // tabs.find('li').disableSelection();

    };

})(jQuery);

</script>
<!-- // wppage js -->

