<?php

include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/ses/Ses/Ses.class.php');

$ses_errors = array();

function wpm_ses_is_on()
{
    $main_options = get_option('wpm_main_options');

    return array_key_exists('ses_is_on', $main_options['letters'])
           && $main_options['letters']['ses_is_on'] == 'on'
           && !empty($main_options['letters']['ses_access_key'])
           && !empty($main_options['letters']['ses_secret_key']);
}

function wpm_ses_get_args($arguments)
{
    $to = array();

    if (isset($arguments['to']) && is_array($arguments['to'])) {
        foreach ($arguments['to'] AS $item) {
            $to[] = is_array($item) && isset($item['email'])
                ? $item['email']
                : $item;
        }

        unset($arguments['to']);
    }

    if (isset($arguments['inline_css'])) {
        unset($arguments['inline_css']);
    }

    $defaults = array(
        'subject' => '',
        'from_email' => '',
        'from_name' => '',
        'html' => '',
        'to' => $to
    );

    return array_merge($defaults, $arguments);
}

function wpm_ses_mail($arguments, $attachments = '', $accessKey = null, $secretKey = null, $host = null, $isTest = false)
{
    $main_options = get_option('wpm_main_options');

    if (!wpm_ses_is_on() && !$isTest) {
        return false;
    }

    $values = wpm_ses_get_args($arguments);

    if (is_null($accessKey)) {
        $accessKey = $main_options['letters']['ses_access_key'];
    }

    if (is_null($secretKey)) {
        $secretKey = $main_options['letters']['ses_secret_key'];
    }

    if (is_null($host)) {
        $host = $main_options['letters']['ses_host'];
    }

    $SES = new SimpleEmailService($accessKey, $secretKey, $host);

    $message = preg_replace('/<(http:.*)>/', '$1', $values['html']);
    $message = preg_replace('/<(https:.*)>/', '$1', $message);
    $html = $message;
    $txt = strip_tags($html);
    if (strlen($html) == strlen($txt)) {
        $html = '';
    }
    $txt = html_entity_decode($txt, ENT_NOQUOTES, 'UTF-8');

    $m = new SimpleEmailServiceMessage();

    if (!is_array($values['to']) && preg_match('/,/im', $values['to'])) {
        $to = explode(',', $values['to']);
        foreach ($to as $toline) {
            $m->addTo($toline);
        }
    } else {
        $m->addTo($values['to']);
    }


    if (isset($values['from_name']) && $values['from_name'] && $values['from_name'] !== '') {
        $from = $values['from_name'] . ' <' . $values['from_email'] . '>';
    } else {
        $from = $values['from_email'];
    }
    $m->setFrom($from);
    $m->setSubject($values['subject']);
    if ($html == '') {
        $m->setMessageFromString($txt);
    } else {
        $m->setMessageFromString($txt, $html);
    }

    if ('' != $attachments) {
        if (!is_array($attachments)) {
            $attachments = explode("\n", $attachments);
        }

        foreach ($attachments as $afile) {
            $m->addAttachmentFromFile(basename($afile), $afile);
        }
    }

    $res = $SES->sendEmail($m);

    if (is_array($res)) {
        return $res['MessageId'];
    } else {
        return null;
    }
}

function wpm_test_ses_mail()
{
    global $ses_errors;

    $accessKey = $_POST['fields']['access_key'];
    $secretKey = $_POST['fields']['secret_key'];
    $email = $_POST['fields']['email'];
    $host = $_POST['fields']['host'];

    $messageArgs = array(
        'subject' => 'Тестовое сообщение Amazon SES',
        'from_email' => $email,
        'html' => 'Тестовое сообщение Amazon SES',
        'to' => $email
    );

    if (!wpm_ses_mail($messageArgs, '', $accessKey, $secretKey, $host, true)) {
        $message = 'Ошибка при отправке через Amazon SES';

        if (count($ses_errors)) {
            $message .= ': ' . array_pop($ses_errors);
        }
    } else {
        $message = 'Сообщение успешно отправлено';
    }

    echo json_encode(array(
        'message' => $message,
    ));
    die();
}

add_action('wp_ajax_wpm_test_ses_mail', 'wpm_test_ses_mail');