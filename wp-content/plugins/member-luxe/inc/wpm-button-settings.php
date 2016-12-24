<div id="wpm-buttons-tabs" class="wpm-tas inner-vertical-tabs" tab-id="buttons-tabs-1">
    <ul class="tabs-nav">
        <li><a href="#button-tab-1">Показать</a></li>
        <li><a href="#button-tab-2">Вернуться к списку</a></li>
        <li><a href="#button-tab-3">Нет доступа</a></li>
        <!-- Домашнее задание -->
        <li><a href="#button-tab-4">Домашнее задание [Ответить на странице]</a></li>
        <li><a href="#button-tab-5">Домашнее задание [Отправить ответ в pop-up]</a></li>
        <li><a href="#button-tab-6">Домашнее задание [Редактировать]</a></li>
        <li><a href="#button-tab-7">Домашнее задание [Редактировать ответ, в pop-up]</a></li>
        <li><a href="#button-tab-17">Домашнее задание [Добавить файлы, в pop-up]</a></li>
        <li><a href="#button-tab-19">Домашнее задание [Загрузить, в pop-up]</a></li>
        <li><a href="#button-tab-20">Домашнее задание [Отмена, в pop-up]</a></li>
        <li><a href="#button-tab-18">Домашнее задание [Удалить, в pop-up]</a></li>
        <!-- Комментарии -->
        <li><a href="#button-tab-21">Комментарии [Обновить]</a></li>
        <li><a href="#button-tab-8">Отправить комментарий</a></li>
        <!-- Вход в систему -->
        <li><a href="#button-tab-9">Войти в систему</a></li>
        <li><a href="#button-tab-10">Зарегистрироваться в системе</a></li>
        <li><a href="#button-tab-22">Войти в систему [Закладки]</a></li>
        <!-- Панель навигации -->
        <li><a href="#button-tab-11">Активация пин-кода</a></li>
        <li><a href="#button-tab-12">Получение пин-кода [Получить]</a></li>
        <li><a href="#button-tab-13">Получение пин-кода [Скопировать]</a></li>
        <li><a href="#button-tab-14">Получение пин-кода [Пройти регистрацию]</a></li>
        <li><a href="#button-tab-15">Задать вопрос</a></li>
        <li><a href="#button-tab-16">Кнопки в верхней панели</a></li>
    </ul>
    <div id="button-tab-1" class="tab">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $show_bth_text = array_key_exists('text', $design_options['buttons']['show']) ? $design_options['buttons']['show']['text'] : 'Показать'; ?>
                    <input type="text" name="design_options[buttons][show][text]"
                           value="<?php echo $show_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][show][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['show']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][show][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['show']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][show][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['show']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][show][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['show']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text" name="design_options[buttons][show][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['show']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][show][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['show']['text_color_hover']; ?>">
                </label>
            </div>
        </div>

    </div>
    <div id="button-tab-2">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $back_bth_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов'; ?>
                    <input type="text" name="design_options[buttons][back][text]"
                           value="<?php echo $back_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][back][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['back']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][back][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['back']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][back][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['back']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][back][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['back']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text" name="design_options[buttons][back][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['back']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][back][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['back']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-3">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $no_access_bth_text = array_key_exists('text', $design_options['buttons']['no_access']) ? $design_options['buttons']['no_access']['text'] : 'Нет доступа'; ?>
                    <input type="text" name="design_options[buttons][no_access][text]"
                           value="<?php echo $no_access_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][no_access][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['no_access']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][no_access][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['no_access']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][no_access][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['no_access']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][no_access][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['no_access']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][no_access][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['no_access']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][no_access][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['no_access']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-4">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_respond_on_page_bth_text = array_key_exists('text', $design_options['buttons']['home_work_respond_on_page']) ? $design_options['buttons']['home_work_respond_on_page']['text'] : 'Ответить'; ?>
                    <input type="text" name="design_options[buttons][home_work_respond_on_page][text]"
                           value="<?php echo $home_work_respond_on_page_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_page][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_page']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_page][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_page']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_page][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_page']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_page][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_page']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_page][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_page']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_page][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_page']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-5">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_respond_on_popup_bth_text = array_key_exists('text', $design_options['buttons']['home_work_respond_on_popup']) ? $design_options['buttons']['home_work_respond_on_popup']['text'] : 'Отправить'; ?>
                    <input type="text" name="design_options[buttons][home_work_respond_on_popup][text]"
                           value="<?php echo $home_work_respond_on_popup_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_popup][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_popup']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_popup][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_popup']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_popup][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_popup']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_respond_on_popup][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_respond_on_popup']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-6">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_edit_bth_text = array_key_exists('text', $design_options['buttons']['home_work_edit']) ? $design_options['buttons']['home_work_edit']['text'] : 'Редактировать'; ?>
                    <input type="text" name="design_options[buttons][home_work_edit][text]"
                           value="<?php echo $home_work_edit_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-7">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_edit_on_popup_bth_text = array_key_exists('text', $design_options['buttons']['home_work_edit_on_popup']) ? $design_options['buttons']['home_work_edit_on_popup']['text'] : 'Отправить'; ?>
                    <input type="text" name="design_options[buttons][home_work_edit_on_popup][text]"
                           value="<?php echo $home_work_edit_on_popup_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup']['background_color_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-17">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_edit_on_popup_addfile_bth_text = array_key_exists('text', $design_options['buttons']['home_work_edit_on_popup_add_file']) ? $design_options['buttons']['home_work_edit_on_popup_add_file']['text'] : 'Добавить файлы'; ?>
                    <input type="text" name="design_options[buttons][home_work_edit_on_popup_add_file][text]"
                           value="<?php echo $home_work_edit_on_popup_addfile_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_add_file][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_add_file][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['background_color_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_add_file][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_add_file][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_add_file][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_add_file][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <!-- upload file button -->
    <div id="button-tab-19">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_edit_on_popup_upload_bth_text = array_key_exists('text', $design_options['buttons']['home_work_edit_on_popup_upload']) ? $design_options['buttons']['home_work_edit_on_popup_upload']['text'] : 'Загрузить'; ?>
                    <input type="text" name="design_options[buttons][home_work_edit_on_popup_upload][text]"
                           value="<?php echo $home_work_edit_on_popup_upload_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_upload][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_upload][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['background_color_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_upload][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_upload][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_upload][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_upload][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <!-- cancel upload button -->
    <div id="button-tab-20">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_edit_on_popup_cancel_bth_text = array_key_exists('text', $design_options['buttons']['home_work_edit_on_popup_cancel']) ? $design_options['buttons']['home_work_edit_on_popup_cancel']['text'] : 'Отмена'; ?>
                    <input type="text" name="design_options[buttons][home_work_edit_on_popup_cancel][text]"
                           value="<?php echo $home_work_edit_on_popup_cancel_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_cancel][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_cancel][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['background_color_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_cancel][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_cancel][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_cancel][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_cancel][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <!-- delete file button -->
    <div id="button-tab-18">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $home_work_edit_on_popup_delete_bth_text = array_key_exists('text', $design_options['buttons']['home_work_edit_on_popup_delete']) ? $design_options['buttons']['home_work_edit_on_popup_delete']['text'] : 'Удалить'; ?>
                    <input type="text" name="design_options[buttons][home_work_edit_on_popup_delete][text]"
                           value="<?php echo $home_work_edit_on_popup_delete_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_delete][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_delete][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['background_color_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_delete][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_delete][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_delete][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][home_work_edit_on_popup_delete][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <!-- refresh comments -->
    <div id="button-tab-21">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $refresh_comments_bth_text = array_key_exists('text', $design_options['buttons']['refresh_comments']) ? $design_options['buttons']['refresh_comments']['text'] : 'Обновить'; ?>
                    <input type="text" name="design_options[buttons][refresh_comments][text]"
                           value="<?php echo $refresh_comments_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][refresh_comments][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['refresh_comments']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][refresh_comments][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['refresh_comments']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <!-- send comment -->
    <div id="button-tab-8">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $send_comment_bth_text = array_key_exists('text', $design_options['buttons']['send_comment']) ? $design_options['buttons']['send_comment']['text'] : 'Отправить'; ?>
                    <input type="text" name="design_options[buttons][send_comment][text]"
                           value="<?php echo $send_comment_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][send_comment][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['send_comment']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][send_comment][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['send_comment']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][send_comment][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['send_comment']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][send_comment][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['send_comment']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <!-- log in -->
    <div id="button-tab-9">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $sign_in_bth_text = array_key_exists('text', $design_options['buttons']['sign_in']) ? $design_options['buttons']['sign_in']['text'] : 'Войти'; ?>
                    <input type="text" name="design_options[buttons][sign_in][text]"
                           value="<?php echo $sign_in_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][sign_in][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['sign_in']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][sign_in][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['sign_in']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][sign_in][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['sign_in']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][sign_in][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['sign_in']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-10">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $log_in_bth_text = array_key_exists('text', $design_options['buttons']['register']) ? $design_options['buttons']['register']['text'] : 'Зарегистрироваться'; ?>
                    <input type="text" name="design_options[buttons][register][text]"
                           value="<?php echo $log_in_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <!-- Вход в систему (Отключен доступ) -->
    <div id="button-tab-22">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки [Вход]', 'wpm'); ?><br>
                    <?php $login_panel_login_bth_text = array_key_exists('text_login', $design_options['buttons']['welcome_tabs']) ? $design_options['buttons']['welcome_tabs']['text_login'] : 'Вход'; ?>
                    <input type="text" name="design_options[buttons][welcome_tabs][text_login]"
                           value="<?php echo $login_panel_login_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки [Регистрация]', 'wpm'); ?><br>
                    <?php $login_panel_register_bth_text = array_key_exists('text_register', $design_options['buttons']['welcome_tabs']) ? $design_options['buttons']['welcome_tabs']['text_register'] : 'Регистрация'; ?>
                    <input type="text" name="design_options[buttons][welcome_tabs][text_register]"
                           value="<?php echo $login_panel_register_bth_text; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста [Вход]', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][welcome_tabs][text_color_login]"
                           class="color"
                           value="<?php echo $design_options['buttons']['welcome_tabs']['text_color_login']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста [Вход] (активная, при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][welcome_tabs][text_color_login_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['welcome_tabs']['text_color_login_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста [Регистрация]', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][welcome_tabs][text_color_register]"
                           class="color"
                           value="<?php echo $design_options['buttons']['welcome_tabs']['text_color_register']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста [Регистрация] (активная, при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][welcome_tabs][text_color_register_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['welcome_tabs']['text_color_register_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-11">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $wpm_activate_pin_bth_text = array_key_exists('text', $design_options['buttons']['activate_pin']) ? $design_options['buttons']['activate_pin']['text'] : 'Отправить'; ?>
                    <input type="text" name="design_options[buttons][activate_pin][text]"
                           value="<?php echo $wpm_activate_pin_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][activate_pin][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['activate_pin']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][activate_pin][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['activate_pin']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][activate_pin][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['activate_pin']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][activate_pin][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['activate_pin']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-12">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $wpm_get_pin_bth_text = array_key_exists('text', $design_options['buttons']['get_pin']) ? $design_options['buttons']['get_pin']['text'] : 'Получить пин-код'; ?>
                    <input type="text" name="design_options[buttons][get_pin][text]"
                           value="<?php echo $wpm_get_pin_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][get_pin][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['get_pin']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][get_pin][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['get_pin']['background_color_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][get_pin][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['get_pin']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][get_pin][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['get_pin']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-13">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $wpm_copy_pin_bth_text = array_key_exists('text', $design_options['buttons']['copy_pin']) ? $design_options['buttons']['copy_pin']['text'] : 'Скопировать пин-код'; ?>
                    <input type="text" name="design_options[buttons][copy_pin][text]"
                           value="<?php echo $wpm_copy_pin_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][copy_pin][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['copy_pin']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][copy_pin][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['copy_pin']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][copy_pin][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['copy_pin']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][copy_pin][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['copy_pin']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][copy_pin][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['copy_pin']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][copy_pin][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['copy_pin']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-14">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $wpm_register_on_pin_bth_text = array_key_exists('text', $design_options['buttons']['register_on_pin']) ? $design_options['buttons']['register_on_pin']['text'] : 'Пройти регистрацию'; ?>
                    <input type="text" name="design_options[buttons][register_on_pin][text]"
                           value="<?php echo $wpm_register_on_pin_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register_on_pin][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register_on_pin']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register_on_pin][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register_on_pin']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register_on_pin][border_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register_on_pin']['border_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет рамки (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register_on_pin][border_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register_on_pin']['border_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register_on_pin][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register_on_pin']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][register_on_pin][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['register_on_pin']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-15">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Текст кнопки', 'wpm'); ?><br>
                    <?php $wpm_ask_bth_text = array_key_exists('text', $design_options['buttons']['ask']) ? $design_options['buttons']['ask']['text'] : 'Отправить'; ?>
                    <input type="text" name="design_options[buttons][ask][text]"
                           value="<?php echo $wpm_ask_bth_text; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][ask][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['ask']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][ask][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['ask']['background_color_hover']; ?>">
                </label>
            </div>

            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][ask][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['ask']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][ask][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['ask']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
    <div id="button-tab-16">
        <div class="wpm-tab-content">
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона панели', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][top_admin_bar][background_panel_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['top_admin_bar']['background_panel_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][top_admin_bar][background_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['top_admin_bar']['background_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет фона (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][top_admin_bar][background_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['top_admin_bar']['background_color_hover']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][top_admin_bar][text_color]"
                           class="color"
                           value="<?php echo $design_options['buttons']['top_admin_bar']['text_color']; ?>">
                </label>
            </div>
            <div class="wpm-control-row">
                <label><?php _e('Цвет текста (при наведении)', 'wpm'); ?><br>
                    <input type="text"
                           name="design_options[buttons][top_admin_bar][text_color_hover]"
                           class="color"
                           value="<?php echo $design_options['buttons']['top_admin_bar']['text_color_hover']; ?>">
                </label>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $("#wpm-buttons-tabs").tabs({
            autoHeight: false,
            collapsible: false,
            fx: {
                opacity: 'toggle',
                duration: 'fast'
            }
        }).addClass('ui-tabs-vertical ui-helper-clearfix');
    });
</script>