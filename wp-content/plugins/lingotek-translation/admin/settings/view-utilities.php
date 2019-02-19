<?php
$page_key = $this->plugin_slug . '_settings&sm=utilities';

if (!empty($_POST)) {
	check_admin_referer($page_key, '_wpnonce_' . $page_key);

	// progress dialog placeholder
	if (!empty($_POST['utility_disassociate'])) {
		$ids = Lingotek_Utilities::get_all_document_ids();
		if (!empty($ids))
			printf('<div id="lingotek-progressdialog" title="%s"><div id="lingotek-progressbar"></div></div>', __('Disassociating content...', 'lingotek-translation'));
	}

	$utilities = array();
	if(array_key_exists('utility_set_default_language', $_POST) && $_POST['utility_set_default_language'] == 'on'){
		$utilities[] = 'utility_set_default_language';
	}

	$GLOBALS['wp_lingotek']->utilities->run_utilities($utilities);

	settings_errors();
}

?>
<h3><?php _e('Utilities', 'lingotek-translation'); ?></h3>
<p class="description"><?php _e('These utilities are designed to help you prepare and maintain your multilingual content.', 'lingotek-translation'); ?></p>

<h4><?php _e('Language', 'lingotek-translation'); ?></h4>
<form id="lingotek-utilities" method="post" action="admin.php?page=<?php echo $page_key; ?>" class="validate"><?php
	wp_nonce_field($page_key, '_wpnonce_' . $page_key);

	printf(
		'<p><input type="checkbox" name="%1$s" id="%1$s"/><label for="%1$s">%2$s</label></p>',
		'utility_set_default_language',
		__('Set <i>default language</i> as the language for all content that has not been assigned a language.', 'lingotek-translation')
	);

	?>
	<h4><?php _e('Disassociation', 'lingotek-translation'); ?></h4>
	<?php

	printf(
		'<p><input type="checkbox" name="%1$s" id="%1$s"/><label for="%1$s">%2$s</label></p>',
		'utility_disassociate',
		__('Disassociate all the content from Lingotek TMS.', 'lingotek-translation')
	);

	$confirm_disassociate = __('You are about to disassociate all your content from Lingotek TMS. Are you sure ?', 'lingotek-translation');

	$confirm_js = "
		d = document.getElementById('utility_disassociate');
		if (d.checked == true) {
			return confirm('$confirm_disassociate');
		}";

	submit_button(__('Run Utilities', 'lingotek-translation'), 'primary', 'submit', true, sprintf('onclick="%s"', $confirm_js)); ?>
</form><?php


