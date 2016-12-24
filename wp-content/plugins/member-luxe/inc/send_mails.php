<?php

function wpm_send_mails_page()
{
    require_once('send_mails_page.php');
}

function wpm_replace_sender_templates($text, $sender_name)
{
    $text = preg_replace('/%FROM_NAME%/', wpm_preg_quote($sender_name), $text);
    return $text;
}

function wpm_preg_quote($str)
{
    return preg_replace('/(\$|\\\\)(?=\d)/', '\\\\\1', $str);
}

function wpm_replace_blog_templates($text)
{
    $blog_url = get_option('home');
    $blog_name = get_option('blogname');

    $text = preg_replace('/%BLOG_URL%/', wpm_preg_quote($blog_url), $text);
    $text = preg_replace('/%BLOG_NAME%/', wpm_preg_quote($blog_name), $text);
    return $text;
}

function wpm_get_all_levels($parent_id = 0, $nested_level = 0)
{
    $taxonomies = array(
        'wpm-levels'
    );

    $args = array(
        'orderby'           => 'name',
        'order'             => 'ASC',
        'hide_empty'        => false,
        'exclude'           => array(),
        'exclude_tree'      => array(),
        'include'           => array(),
        'number'            => '',
        'fields'            => 'all',
        'slug'              => '',
        'parent'            => $parent_id,
        'hierarchical'      => true,
        'child_of'          => 0,
        'get'               => '',
        'name__like'        => '',
        'description__like' => '',
        'pad_counts'        => false,
        'offset'            => '',
        'search'            => '',
        'cache_domain'      => 'core'
    );

    $levels = get_terms($taxonomies, $args);

    foreach ($levels AS $level) {
        $level->children = wpm_get_all_levels($level->term_id, $nested_level + 1);
        $level->nested_level = $nested_level;
    }
    return $levels;
}

function wpm_get_levels_options_for_emails($send_targets, $levels = null)
{
    if ($levels === null) {
        $levels = wpm_get_all_levels();
    }

    $html = '';

    foreach ($levels AS $level) {
        $html .= sprintf(
            '<option value="%d" %s>%s%s</option>',
            $level->term_id,
            (in_array($level->term_id, $send_targets) ? ' selected="yes"' : ''),
            ($level->nested_level ? implode('', array_fill(0, $level->nested_level, '&nbsp;&nbsp;')) : ''),
            $level->name
        );
        $html .= wpm_get_levels_options_for_emails($send_targets, $level->children);
    }

    return $html;
}

function wpm_get_term_units($data) {
    $units = array_key_exists('units', $data) ? $data['units'] : 'months';

    return $data['duration'] . '_' . $units;
}

function wpm_get_term_keys_options_for_emails($levels = null)
{
    if ($levels === null) {
        $levels = get_terms('wpm-levels', array());
    }

    $html = '';

    foreach ($levels AS $level) {
        $term_keys = wpm_get_term_keys($level->term_id);

        if (is_array($term_keys) && !empty($term_keys)) {
            $keys_by_period = array_count_values(array_map('wpm_get_term_units', $term_keys));

            foreach ($keys_by_period as $key => $value) {
                list($duration, $units) = explode('_', $key);
                $result[$key]['new'] = 0;
                $result[$key]['used'] = 0;

                foreach ($term_keys as $item) {
                    $status = $item['status'];

                    if(isset($item['sent']) && $item['sent']) {
                        $status = 'used';
                    }

                    if ($item['duration'] == $duration && wpm_mail_is_units_equal($item, $units)) {
                        $result[$key][$status]++;
                    }
                }
                $units_msg = $units == 'days' ? 'дн.' : 'мес.';

                $html .= sprintf(
                    '<option value="%d_%s" data-main="%d">%d %s - %d</option>',
                    $level->term_id,
                    $key,
                    $level->term_id,
                    $duration,
                    $units_msg,
                    $result[$key]["new"]
                );
            }
        }
    }

    return $html;
}

function wpm_mail_is_units_equal($key, $cur_units)
{
    $units = array_key_exists('units', $key) ? $key['units'] : 'months';

    return $cur_units == $units;
}

function wpm_get_users_by_levels(array $levels = array())
{
    $levelIds = array();
    $termKeys = array();

    foreach ($levels AS $level) {
        $levelIds[] = $level;
        $child = wpm_all_categories($level);
        if (!empty($child)) {
            $levelIds = array_merge($levelIds, $child);
        }
    }

    $levelIds = array_unique($levelIds);

    foreach ($levelIds AS $levelId) {
        $termKeys = array_merge($termKeys, wpm_get_term_keys($levelId));
    }

    $users = array();

    foreach ($termKeys AS $termKey) {
        if ($termKey['status'] == 'used' && time() < strtotime($termKey['date_end'])) {
            $uq = new WP_User_Query(array(
                'meta_key'     => 'user_key',
                'meta_value'   => $termKey['key'],
                'meta_compare' => 'LIKE',
                'fields'       => array('user_email')
            ));
            $user = $uq->get_results();
            if (count($user)) {
                $users[] = $user[0]->user_email;
            }
        }
    }

    return $users;
}

function wpm_get_term_keys_to_send($key)
{
    $result = array();
    $error = false;

    if ($key != '') {
        list($term_id, $duration, $units) = explode('_', $key);

        foreach (wpm_get_term_keys($term_id) AS $v) {
            $_units = array_key_exists('units', $v) ? $v['units'] : 'months';
            $isValid = $v['status'] == 'new'
                && (!isset($v['sent']) || !$v['sent'])
                && $v['duration'] == $duration
                && $_units == $units;

            if($isValid) {
                $result[] = $v;
            }
        }

    } else {
        $error = 'Не выбраны коды доступа';
    }

    return array($result, $error);
}

function wpm_send_mail($recipients = array(), $subject = '', $message = '', $sender_name = '', $sender_email = '', $term_keys = array(), $term_id)
{
    $main_options = get_option('wpm_main_options');
    $used_term_keys = array();

    $mandrill_is_on = wpm_mandrill_is_on();

    if ($mandrill_is_on) {
        $mandrill = new Mandrill($main_options['letters']['mandrill_api_key']);
    }

    $ses_is_on = wpm_ses_is_on();

    if($sender_name == '') {
        $sender_name = get_bloginfo("name");
    }

    if($sender_email == '') {
        $sender_email = 'no-reply@' . $_SERVER['SERVER_NAME'];
    }

    if(!is_array($recipients)) {
        $recipients = array($recipients);
    }

    $recipients = array_unique($recipients);

    foreach ($recipients as $recipient) {

        $html = stripslashes($message);

        if (count($term_keys)) {
            $code = array_shift($term_keys);
            $html = preg_replace('/\[pin_code\]/', $code['key'], $html);
            $used_term_keys[$code['key']] = $code;
        }

        $user_message_args = array(
            'subject' => $subject,
            'from_email' => $sender_email,
            'from_name' => $sender_name,
            'html' => wpautop($html),
            'inline_css' => true,
            'to' => array(
                array('email' => $recipient)
            )
        );

        if ($mandrill_is_on) {
            $result = $mandrill->messages->send($user_message_args, true);
        } elseif ($ses_is_on) {
            $user_message_args['from_email'] = $main_options['letters']['ses_email'];
            $result = wpm_ses_mail($user_message_args);
        } else {
            $headers = 'From: ' . $sender_name . ' <' . $sender_email . '>' . "\r\n";
            $result = wp_mail($recipient, $subject, $html, $headers);
        }
    }

    if (count($used_term_keys)) {
        $term_keys_to_update = array();
        foreach (wpm_get_term_keys($term_id) AS $_term_key) {
            if (isset($used_term_keys[$_term_key['key']])) {
                $_term_key['sent'] = true;
            }
            $term_keys_to_update[] = $_term_key;
        }
        wpm_set_term_keys($term_id, $term_keys_to_update);
    }

    return $result;
}