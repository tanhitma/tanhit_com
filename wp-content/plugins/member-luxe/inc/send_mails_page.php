<?php
	global $user_identity, $user_email, $current_user, $user_ID;

	$err_msg = '';
	get_currentuserinfo();

	$from_sender = 0;
    $from_address = empty($user_email) ? get_bloginfo('email') : $user_email;
    $from_name = get_user_meta($user_ID, 'nickname', true);
    $from_name = empty($from_name) ? get_bloginfo('name') : $from_name;


	// Send the email if it has been requested
	if (array_key_exists('send', $_POST) && $_POST['send']=='true') {
	    // Use current user info only if from name and address has not been set by the form
	    if (!isset($_POST['fromName']) || !isset($_POST['fromAddress']) || empty($_POST['fromName']) || empty($_POST['fromAddress'])) {
	        $from_name = empty($user_identity) ? get_bloginfo('name') : $user_identity;
		    $from_address = $user_email;
	    } else {
		    $from_name = $_POST['fromName'];
		    $from_address = $_POST['fromAddress'];
	    }

		if ( !isset($_POST['send_targets']) || !is_array($_POST['send_targets']) || empty($_POST['send_targets']) ) {
			$err_msg = $err_msg . __('Вы должны выбрать хотя бы один уровень доступа.') . '<br/>';
		} else {
			$send_targets = $_POST['send_targets'];
		}
	
		if ( !isset( $_POST['subject'] ) || trim($_POST['subject'])=='' ) {
			$err_msg = $err_msg . __('Вы должны написать тему.') . '<br/>';
		} else {
			$subject = $_POST['subject'];
		}
	
		if ( !isset( $_POST['mailcontent'] ) || trim($_POST['mailcontent'])=='' ) {
			$err_msg = $err_msg . __('Вы долны ввести информацию.') . '<br/>';
		} else {
			$mail_content = $_POST['mailcontent'];
		}
		
		if ( !isset( $_POST['from_sender'] ) || trim($_POST['from_sender'])=='' ) {
			$from_sender = 0;
		} else {
			$from_sender = $_POST['from_sender'];
		}
	}
	if (!isset($send_targets)) {
		$send_targets = array();
	}

	if (!isset($mail_format)) {
		$mail_format = 'html';
	}

	if (!isset($subject)) {
		$subject = '';
	}

	if (!isset($mail_content)) {
		$mail_content = '';
	}

    // Replace the template variables concerning the blog and sender details
    // --

    $subject = wpm_replace_sender_templates($subject, $from_name);
    $mail_content = wpm_replace_sender_templates($mail_content, $from_name);
    $subject = wpm_replace_blog_templates($subject);
    $mail_content = wpm_replace_blog_templates($mail_content);

	// If error, we simply show the form again
	if (array_key_exists('send', $_POST) && ($_POST['send']=='true') && ($err_msg == '')) {
		// No error, send the mail
        // $mail_content = wpautop($mail_content);
	?>
	<div class="wrap">
	<?php
		// Fetch users
		// --
        $error = false;
        $term_keys = array();

        $recipients = wpm_get_users_by_levels($send_targets);
        if (empty($recipients)) {
            $error = 'Получатели не найдены';
        } elseif (preg_match('/\[pin_code\]/', $mail_content)) {
            list($term_keys, $error) = wpm_get_term_keys_to_send($_POST['send_term_key']);

            if($error === false && count($term_keys) < count($recipients)) {
                $error = 'Количество кодов доступа меньше чем получателей письма';
            } elseif($error === false) {
                list($term_id) = explode('_', $_POST['send_term_key']);
            }
        }

    ?>

        <?php if ($error !== false) : ?>
            <p><strong><?php _e($error); ?></strong></p>
        <?php else : ?>
            <?php wpm_send_mail($recipients, $subject, $mail_content, $from_name, $from_address, $term_keys, $term_id); ?>
            <div class="updated fade">
				<p><?php echo __('Сообщение отправлено'); ?></p>
			</div>
            <?php include('send_mails_form.php'); ?>
        <?php endif; ?>
	</div>

<?php
	} else {
		// Redirect to the form page
		include('send_mails_form.php');
	}
?>
