<?php

function wpm_is_pin_code_page()
{
    global $wp_query;

    return $wp_query->query_vars
           && (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'wpm-page')
           && (isset($wp_query->query_vars['wpm-page']) && $wp_query->query_vars['wpm-page'] == 'pin-code')
           && wpm_is_pin_code_page_enabled();
}

function wpm_is_pin_code_page_enabled()
{
    $main_options = get_option('wpm_main_options');

    return isset($main_options['pincode_page'])
           && (isset($main_options['pincode_page']['lvl']) && $main_options['pincode_page']['lvl'] !== '')
           && (isset($main_options['pincode_page']['term_key']) && $main_options['pincode_page']['term_key'] !== '');
}

function wpm_is_pin_code_page_lvl($term_id)
{
    $main_options = get_option('wpm_main_options');

    return isset($main_options['pincode_page'])
           && isset($main_options['pincode_page']['lvl'])
           && $main_options['pincode_page']['lvl'] == $term_id;
}

function wpm_is_pin_code_page_term_key($key)
{
    $main_options = get_option('wpm_main_options');

    return isset($main_options['pincode_page'])
           && isset($main_options['pincode_page']['term_key'])
           && $main_options['pincode_page']['term_key'] == $key;
}

function wpm_tk_get_term_units($data)
{
    $units = array_key_exists('units', $data) ? $data['units'] : 'months';

    return $data['duration'] . '_' . $units;
}

function wpm_get_term_keys_options_for_pin_code_page($levels = null)
{
    if ($levels === null) {
        $levels = get_terms('wpm-levels', array());
    }

    $html = '';

    foreach ($levels AS $level) {
        $term_keys = wpm_get_term_keys($level->term_id);

        if (is_array($term_keys) && !empty($term_keys)) {
            $keys_by_period = array_count_values(array_map('wpm_tk_get_term_units', $term_keys));

            foreach ($keys_by_period as $key => $value) {
                list($duration, $units) = explode('_', $key);
                $result[$key]['new'] = 0;
                $result[$key]['used'] = 0;

                foreach ($term_keys as $item) {
                    $status = $item['status'];

                    if (isset($item['sent']) && $item['sent']) {
                        $status = 'used';
                    }

                    if ($item['duration'] == $duration && wpm_mail_is_units_equal($item, $units)) {
                        $result[$key][$status]++;
                    }
                }
                $units_msg = $units == 'days' ? 'дн.' : 'мес.';

                $html .= sprintf(
                    '<option value="%d_%s" data-main="%d" %s>%d %s - %d</option>',
                    $level->term_id,
                    $key,
                    $level->term_id,
                    (wpm_is_pin_code_page_term_key($level->term_id . '_' . $key) ? 'selected="selected"' : ''),
                    $duration,
                    $units_msg,
                    $result[$key]["new"]
                );
            }
        }
    }

    return $html;
}

function wpm_get_available_pin_code()
{
    $main_options = get_option('wpm_main_options');
    $key = $main_options['pincode_page']['term_key'];

    list($term_id, $duration, $units) = explode('_', $key);

    $term_keys_to_update = array();
    $code = false;

    foreach (wpm_get_term_keys($term_id) AS $v) {
        $_units = array_key_exists('units', $v) ? $v['units'] : 'months';
        $is_sent = isset($v['sent']) && $v['sent'] == true;
        $is_new = $v['status'] == 'new' && !$is_sent;

        if($is_new && $v['duration'] == $duration && $_units == $units && $code === false) {
            $v['sent'] = true;
            $code = $v['key'];
        }

        $term_keys_to_update[] = $v;
    }

    wpm_set_term_keys($term_id, $term_keys_to_update);

    return $code;
}

function wpm_get_pin_code_page_url()
{
    $virtualPost = new stdClass();
    $virtualPost->post_type = 'wpm-page';
    $virtualPost->post_status = 'publish';
    $virtualPost->post_title = 'Получение пин-кода';
    $virtualPost->ID = 1;

    $link = get_permalink($virtualPost);

    return preg_match('/\?/', $link)
        ? $link . '=pin-code'
        : $link . 'pin-code';
}

function wpm_get_pin_code()
{
    $result = array();

    if (wpm_is_pin_code_page_enabled()) {
        if (isset($_SESSION['wpm_pc'])) {
            $result['success'] = true;
            $result['code'] = $_SESSION['wpm_pc'];
        } elseif (is_user_logged_in() && get_user_option('wpm_pc')) {
            $result['success'] = true;
            $result['code'] = get_user_option('wpm_pc');
        } else {
            $code = wpm_get_available_pin_code();
            if($code !== false) {
                $result['success'] = true;
                $result['code'] = $code;
                $_SESSION['wpm_pc'] = $code;
                if(is_user_logged_in()) {
                    update_user_option(get_current_user_id(), 'wpm_pc', $code);
                }
            } else {
                $result['error'] = 'Нет свободных бесплатных пин-кодов';
            }
        }
    } else {
        $result['error'] = 'Страница раздачи пин-кодов отключена';
    }

    echo json_encode($result);
    die();
}

add_action('wp_ajax_wpm_get_pin_code_action', 'wpm_get_pin_code');
add_action('wp_ajax_nopriv_wpm_get_pin_code_action', 'wpm_get_pin_code');

function wpm_get_term_keys($term_id, $key_name = 'wpm_term_keys')
{
    global $wpdb;
    $options_table = $wpdb->prefix . "options";

    $term_keys = get_option("{$key_name}_{$term_id}");

    if($term_keys === false) {
        $term_keys = array();
    }

    $query = "SELECT option_value FROM {$options_table} WHERE option_name LIKE '{$key_name}\_add\_".intval($term_id)."\_%'";

    foreach ($wpdb->get_col($query) AS $value) {
        $val = maybe_unserialize($value);
        if (is_array($val)) {
            foreach($val AS $v) {
                $term_keys[] = $v;
            }
        }
    }

    return $term_keys;
}

function wpm_set_term_keys($term_id, $keys, $key_name = 'wpm_term_keys')
{
    wpm_delete_term_keys($term_id, $key_name);

    $i = 0;

    foreach (array_chunk((array)$keys, 1000) AS $chunk) {
        if (!$i) {
            update_option("{$key_name}_" . intval($term_id), $chunk);
        } else {
            update_option("{$key_name}_add_" . intval($term_id) . "_" . $i, $chunk);
        }

        $i++;
    }

    return true;
}

function wpm_delete_term_keys($term_id, $key_name = 'wpm_term_keys')
{
    global $wpdb;
    $options_table = $wpdb->prefix . "options";

    delete_option("{$key_name}_{$term_id}");

    $query = "SELECT option_name FROM {$options_table} WHERE option_name LIKE '{$key_name}\_add\_".intval($term_id)."\_%'";

    foreach ($wpdb->get_col($query) AS $option) {
        delete_option($option);
    }
}
