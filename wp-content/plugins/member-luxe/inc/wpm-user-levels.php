<?php
/**
 *
 */
function wpm_user_levels()
{
    $user_levels = get_option('wpm_user_levels');
    ?>
    <script>

        jQuery(function($){

            // ajax url
            var wp_ajax = {"ajaxurl": "<?php echo admin_url('admin-ajax.php'); ?>"};

            // show popup window with shortcode settings
            $('.wpm-ajax-button').click(function () {
                var role = $(this).attr('role');
                var title = $(this).attr('title');
                tb_show(title, "#TB_inline?width=640&&height=300&inlineId=wpm-modal-box");
                $('#TB_ajaxContent').html('');
                $('#TB_ajaxContent').css({'width': '640', 'height': ($('#TB_window').height() - 50) + 'px'}).addClass('wpm-loader');

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: "wpm_get_options_part_action",
                        role: role
                    },
                    success: function (data) {
                        //console.log(data);
                        $('#TB_ajaxContent').html(data).removeClass('wpm-loader');
                    },
                    error: function(errorThrown){
//                        alert(errorThrown);
                    }
                });
            });
            $('.add-new-user-level').on('click', function(){

            });

            $('.row .generate').click( function(){
                $(this).parent().children('.hidden.keys').slideToggle('fast').siblings('.hidden').slideUp('fast');
            });

            // add new keys
            $('.add-new-keys').click(function(){
                //console.log('add-new-keys');
                var level = $(this).attr('level');
                $.ajax({
                    type: 'GET',
                    url: ajaxurl,
                    data: {
                        action: 'wpm_ajax_generate_user_level_codes_action'
                    },
                    success: function (data) {
                        $('#new-keys-'+level).html(data);
                        $('.hidden.new-keys.level-' + level).slideDown('fast').siblings('.hidden').slideUp('fast');

                    },
                    error: function(errorThrown){
                        //alert(errorThrown);
                    }
                });
                return false;
            });
            $('.new-keys-cancel').click(function(){
                var level = $(this).attr('level');
                $('#new-keys-'+level).html('');
                $('.hidden.new-keys.level-' + level).slideUp('fast');
            });
            $('.new-keys-save').click(function(){
                var level = $(this).attr('level');
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'wpm_ajax_save_codes_action',
                        wpm_level: level,
                        wpm_keys: $('#new-keys-'+level).html()
                    },
                    success: function (data) {
                        //console.log(data);
                    },
                    error: function(errorThrown){
                        //alert(errorThrown);
                    }
                });
                return false;
            });


        });
    </script>
    <div class="wrap wpm-options-page">
        <div id="icon-options-general" class="icon32"></div>
        <h2><?php _e('Уровни доступа', 'wpm'); ?> <a href="#" class="md-trigger add-new-h2 wpm-ajax-button" data-modal="modal-2" title="<?php _e('Добавить новый уровень', 'wpm'); ?>"  role="new-user-level"><?php _e('Добавить новый уровень', 'wpm'); ?></a></h2>
        <div class="clear"></div>
        <div class="options-wrap stuffbox">
            <div class="wpm-row-table codes-table">
                <?php
                $html = '';
                if(!empty($user_levels)){
                    foreach($user_levels as $key => $value){

                        $html .= '<div class="row column-title" key="'.$key.'"><span class="title">'.$value["title"].'</span><a class="button wpm-options-button" role="codes">Коды доступа</a><a class="button edit wpm-options-button" role="edit-level">Редактировать</a><a class="button wpm-options-button" role="remove-level">Удалить</a>
                        </div>';
                    }
                    echo $html;
                }

                ?>
            </div>
            <div class="hidden">
                <div id="wpm-modal-box">
                    <div id="wpm-modal-content">

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php }


/**
 *  Add user level
 */

function add_user_level(){
    $title = $_POST['title'];
    if(empty($title)){
        $title = __('Название уровня доступа', 'wpm');
    }
    /*$levels[$level]['new'] = array_merge($levels[$level]['new'], $keys);
    $result = update_option('wpm_user_levels', $levels);*/
}

/**
 *
 */


function wpm_get_options_part(){
    $role = $_POST['role'];
    if(!empty($role)){
        switch($role){
            case 'new-user-level': wpm_new_level(); break;
            case 'get-codes': wpm_get_codes(); break;
            case 'edit-level': wpm_edit_level_form(); break;
            case 'save-level': wpm_save_level(); break;
            case 'remove-level': wpm_remove_level(); break;
            case 'generate-codes': wpm_generate_codes(); break;
        }

    }else{
        die();
    }

}
add_action('wp_ajax_wpm_get_options_part_action', 'wpm_get_options_part'); //

/**
 *  Edit user level
 */
function wpm_new_level(){
    $level_id = '';
    $html = '
    <div class="wpm-modal-content-wrap">
        <input type="text" name="user-level-title" value="" placeholder="'.__('Название уровня доступа', 'wpm').'" class="wpm-input-text wpm-title wpm-wide">
    </div>
    <div class="wpm-modal-footer">
            <button class="button button-primary button-large wpm-pull-left wpm-ajax-button" role="add-new-user-level">Добавить</button>
        </div>
    ';
    echo $html;
    die();
}


/**
 *  Edit user level
 */
function wpm_edit_level_form(){
    $level_id = '';
    $html = '
    <div>
        <input type="text" name="user-level-title" value="'.$title.'">
    </div>
    ';
}


/**
 *
 */

function wpm_get_codes(){
    if(!empty($value['new'])){
        $new = '';
        $new_count = count($value['new']);
        foreach($value['new'] as $new_key){
            $new .=  $new_key .'</br>';
        }
    }else{
        $new = 'Нет ключей';
        $new_count = '0';
    }
    if(!empty($value['used'])){
        $used = '';
        $used_count = count($value['used']);
        foreach($value['used'] as $used_key){
            $used .=  $used_key .'</br>';
        }
    }else{
        $used = 'Нет ключей';
        $used_count = '0';
    }

    $html = '';
    $html .= '<div class="hidden keys level-'.$key.'">
                 <p>
                                <a class="button new-keys-save" level="'.$key.'">Сохранить</a>
                                <a class="button new-keys-copy" level="'.$key.'">Копировать</a>
                                <a class="button new-keys-cancel" level="'.$key.'">Отменить</a>
                                <a class="button new-keys-delete" level="'.$key.'">Удалить все коды</a>
                            </p>
                            <div>
                                <div class="keys new-keys"><h4>Не использованные ключи ('.$new_count.')</h4>'.$new.'</div>
                                <div class="keys used-keys"><h4>Использованные ('.$used_count.')</h4>'.$used.'</div>
                            </div>
                        </div>
                        <div class="hidden new-keys level-'.$key.'">
                            <div class="">
                                <div id="new-keys-'.$key.'"></div>
                            </div>
                        </div>';
    echo $html;
    die();
}

/**
 *
 */

function wpm_save_level()
{
    $keys = explode('<br>', $_POST['wpm_keys']);
    $level = $_POST['wpm_level'];
    $levels = get_option('wpm_user_levels');
    $levels[$level]['new'] = array_merge($levels[$level]['new'], $keys);
    $result = update_option('wpm_user_levels', $levels);
    die(); // stop executing script
}
add_action('wp_ajax_wpm_ajax_save_level_action', 'wpm_ajax_save_level'); // ajax for logged in users