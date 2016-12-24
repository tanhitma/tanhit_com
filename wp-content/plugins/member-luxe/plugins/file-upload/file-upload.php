<?php
/*
Plugin Name: JQuery Html5 File Upload
Plugin URI: http://wordpress.org/extend/plugins/jquery-html5-file-upload/
Description: This plugin adds a file upload functionality to the front-end screen. It allows multiple file upload asynchronously along with upload status bar.
Version: 3.0
Author: sinashshajahan
Author URI: 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
require_once( plugin_dir_path( __FILE__ ) . 'UploadHandler.php' );

function jquery_html5_file_upload_install() {
	add_option("wpm_jqhfu_accepted_file_types", 'gif|jpeg|jpg|png|pdf|zip|rar', '', 'yes');
	add_option("wpm_jqhfu_inline_file_types", 'gif|jpeg|jpg|png|pdf|zip|rar', '', 'yes');
	add_option("wpm_jqhfu_maximum_file_size", '5', '', 'yes');
	add_option("wpm_jqhfu_thumbnail_width", '80', '', 'yes');
	add_option("wpm_jqhfu_thumbnail_height", '80', '', 'yes');
	
	$upload_array = wp_upload_dir();
	$upload_dir=$upload_array['basedir'].'/files/';
	/* Create the directory where you upoad the file */
	if (!is_dir($upload_dir)) {
		$is_success=mkdir($upload_dir, '0755', true);
		if(!$is_success)
			die('Unable to create a directory within the upload folder');
	}
}

function jquery_html5_file_upload_remove() {
	/* Deletes the database field */
	delete_option('wpm_jqhfu_accepted_file_types');
	delete_option('wpm_jqhfu_inline_file_types');
	delete_option('wpm_jqhfu_maximum_file_size');
	delete_option('wpm_jqhfu_thumbnail_width');
	delete_option('wpm_jqhfu_thumbnail_height');
}

// Add settings link on plugin page
function jquery_html5_file_upload_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=jquery-html5-file-upload-setting.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 

function jquery_html5_file_upload_html_page() {
$args = array(
    'orderby'                 => 'display_name',
    'order'                   => 'ASC',
    'selected'                => $_POST['user']
);
?>
<h2>JQuery HTML5 File Upload Setting</h2>

<form method="post" >
<?php wp_nonce_field('update-options'); ?>

<table >
<tr >
<td>Accepted File Types</td>
<td >
<input type="text" name="accepted_file_types" value="<?php print(get_option('wpm_jqhfu_accepted_file_types')); ?>" />&nbsp;filetype seperated by | (e.g. gif|jpeg|jpg|png)
</td>
</tr>
<tr >
<td>Inline File Types</td>
<td >
<input type="text" name="inline_file_types" value="<?php print(get_option('wpm_jqhfu_inline_file_types')); ?>" />&nbsp;filetype seperated by | (e.g. gif|jpeg|jpg|png)
</td>
</tr>
<tr >
<td>Maximum File Size</td>
<td >
<input type="text" name="maximum_file_size" value="<?php print(get_option('wpm_jqhfu_maximum_file_size')); ?>" />&nbsp;MB
</td>
</tr>
<tr >
<td>Thumbnail Width </td>
<td >
<input type="text" name="thumbnail_width" value="<?php print(get_option('wpm_jqhfu_thumbnail_width')); ?>" />&nbsp;px
</td>
</tr
<tr >
<td>Thumbnail Height </td>
<td >
<input type="text" name="thumbnail_height" value="<?php print(get_option('wpm_jqhfu_thumbnail_height')); ?>" />&nbsp;px
</td>
</tr>
<tr>
<td colspan="2">
<input type="submit" name="savesetting" value="Save Setting" />
</td>
</tr>
</table>
<br/>
<hr/>
<h2>View Uploaded Files</h2>
<table >
<tr >
<td>Select User</td>
<td >
<?php wp_dropdown_users($args); ?> 
</td>
<td>
<input type="submit" name="viewfiles" value="View Files" /> &nbsp; <input type="submit" name="viewguestfiles" value="View Guest Files" />
</td>
</tr>
<tr>
</table>
<table>
<tr>
<td>
<?php
if(isset($_POST['viewfiles']) && $_POST['viewfiles']=='View Files')
{
if ($_POST['user']) {
	$upload_array = wp_upload_dir();
	$imgpath=$upload_array['basedir'].'/files/'.$_POST ['user'].'/';
	$filearray=glob($imgpath.'*');
	if($filearray && is_array($filearray))
	{
		foreach($filearray as $filename){
			if(basename($filename)!='thumbnail'){
			print('<a href="'.$upload_array['baseurl'].'/files/'.$_POST ['user'].'/'.basename($filename).'" target="_blank"/>'.basename($filename).'</a>');
			print('<br/>');
			}
		}
	}
} 
}
else
if(isset($_POST['viewguestfiles']) && $_POST['viewguestfiles']=='View Guest Files')
{
	$upload_array = wp_upload_dir();
	$imgpath=$upload_array['basedir'].'/files/guest/';
	$filearray=glob($imgpath.'*');
	if($filearray && is_array($filearray))
	{
		foreach($filearray as $filename){
			if(basename($filename)!='thumbnail'){
			print('<a href="'.$upload_array['baseurl'].'/files/guest/'.basename($filename).'" target="_blank"/>'.basename($filename).'</a>');
			print('<br/>');
			}
		}
	}
}
?>
</td>
</tr>
</table>
</form>
<?php
}


function wpm_jqhfu_enqueue_scripts() {
	$stylepath=plugin_dir_url(__FILE__).'css/';
	$scriptpath=plugin_dir_url(__FILE__).'js/';
	
	wpm_enqueue_style ( 'blueimp-gallery-style', $stylepath.'blueimp-gallery.min.css' );
	wpm_enqueue_style ( 'jquery.fileupload-style', $stylepath.'jquery.fileupload.css' );
	wpm_enqueue_style ( 'fontawesome', $stylepath.'fontawesome/css/font-awesome.min.css' );

	wpm_enqueue_script ( 'jtmpl-script', $scriptpath . 'tmpl.min.js');
	wpm_enqueue_script ( 'load-image-all-script', $scriptpath . 'load-image.all.min.js');
	wpm_enqueue_script ( 'canvas-to-blob-script', $scriptpath . 'canvas-to-blob.min.js');
	wpm_enqueue_script ( 'jquery-blueimp-gallery-script', $scriptpath . 'jquery.blueimp-gallery.min.js');
	wpm_enqueue_script ( 'jquery-iframe-transport-script', $scriptpath . 'jquery.iframe-transport.js');
	wpm_enqueue_script ( 'jquery-fileupload-script', $scriptpath . 'jquery.fileupload.js');
	wpm_enqueue_script ( 'jquery-fileupload-process-script', $scriptpath . 'jquery.fileupload-process.js');
	wpm_enqueue_script ( 'jquery-fileupload-image-script', $scriptpath . 'jquery.fileupload-image.js');
	wpm_enqueue_script ( 'jquery-fileupload-audio-script', $scriptpath . 'jquery.fileupload-audio.js');
	wpm_enqueue_script ( 'jquery-fileupload-video-script', $scriptpath . 'jquery.fileupload-video.js');
	wpm_enqueue_script ( 'jquery-fileupload-validate-script', $scriptpath . 'jquery.fileupload-validate.js');
	wpm_enqueue_script ( 'jquery-fileupload-ui-script', $scriptpath . 'jquery.fileupload-ui.js');
	wpm_enqueue_script ( 'jquery-fileupload-jquery-ui-script', $scriptpath . 'jquery.fileupload-jquery-ui.js');
}

function wpm_jqhfu_load_ajax_function()
{
	global $current_user;
	get_currentuserinfo();
	$current_user_id = $current_user->ID;

	if (!isset($current_user_id) || $current_user_id == '') {
		$current_user_id = 'guest';
	}

	if (isset($_POST['wpm_task'])) {
		$wpmTaskId = intval($_POST['wpm_task']);
	} elseif (isset($_GET['wpm_task'])) {
		$wpmTaskId = intval($_GET['wpm_task']);
	} elseif (isset($_DELETE['wpm_task'])) {
		$wpmTaskId = intval($_DELETE['wpm_task']);
	}

	if (isset($wpmTaskId)) {
		UploadHandler::$wpmTaskId = $wpmTaskId;
		$current_user_id = 'wpm_task_' . $wpmTaskId . '_' . $current_user_id;
	}

	new UploadHandler(null, $current_user_id, true, null);

	die();
}

function wpm_jqhfu_add_inline_script() {
?>
<script type="text/javascript">
	function initFileUpload() {
		var form = jQuery('#fileupload'),
			url = '<?php echo admin_url('admin-ajax.php');?>',
			pageId;
		if (form.length) {
			pageId = form.closest('.homework-respnose-form').attr('page-id');
			form.fileupload({
				url        : url,
				autoUpload : true,
				formData   : [
					{name : 'wpm_task', value : pageId},
					{name : 'action', value : 'load_ajax_function'}
				]
			});
			form.addClass('fileupload-processing');
			jQuery.ajax({
				//xhrFields: {withCredentials: true},
				url             : url,
				data            : {
					action   : "load_ajax_function",
					wpm_task : pageId
				},
				acceptFileTypes : /(\.|\/)(gif|jpeg|jpg|png|pdf|zip|rar)$/i,
				dataType        : 'json',
				context         : form[0]
			}).always(function () {
				jQuery(this).removeClass('fileupload-processing');
			}).done(function (result) {
				jQuery(this).fileupload('option', 'done')
					.call(this, jQuery.Event('done'), {result : result});
			});
		}
	}

jQuery(function () {
	'use strict';
	initFileUpload();
});
</script>
<?php
}

/* Block of code that need to be printed to the form*/
function jquery_html5_file_upload_hook() {
	$design_options = get_option('wpm_design_options');
?>
    <div id="fileupload">
       <div class="fileupload-buttonbar">
       <div class="fileupload-buttons">
            <label class="wpm_jqhfu-file-container">
                <input type="file" name="files[]" multiple class="wpm_jqhfu-inputfile">
				<span class="wpm-button wpm-homework-edit-popup-addfile-button"><?php echo $design_options['buttons']['home_work_edit_on_popup_add_file']['text']; ?></span>
            </label>
            <span class="fileupload-process"></span>
        </div>
        <div class="fileupload-progress wpm_jqhfu-fade" style="display:none;max-width:500px;margin-top:2px;">
            <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
            <div class="progress-extended">&nbsp;</div>
        </div>
    </div>
	<div class="wpm_jqhfu-upload-download-table">
    <table role="presentation"><tbody class="files"></tbody></table>
	</div>	
    </div>
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload wpm_jqhfu-fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <span class="name">{%=file.name%}</span>
            <strong class="error"></strong>
        </td>
        <td>
            <p class="size">Загрузка...</p>
            <div class="progress"></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="start wpm-button wpm-homework-edit-popup-upload-button" disabled><?php echo $design_options['buttons']['home_work_edit_on_popup_upload']['text']; ?></button>
            {% } %}
            {% if (!i) { %}
                <button class="cancel wpm-button wpm-homework-edit-popup-cancel-button"><?php echo $design_options['buttons']['home_work_edit_on_popup_cancel']['text']; ?></button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download wpm_jqhfu-fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" rel="wpm_homework_file" class="fancybox"><img src="{%=file.thumbnailUrl%}"></a>
                {% } else { %}
                    <i class="fa fa-file-{%=file.extension=='rar'?'zip':file.extension%}-o" aria-hidden="true"></i>
                {% } %}
            </span>
        </td>
        <td>
            <span class="name">{%=file.name%}</span>
            {% if (file.error) { %}
                <div><span class="error">Ошибка</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <button class="delete wpm-button wpm-homework-edit-popup-delete-button" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}&action=load_ajax_function"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}><?php echo $design_options['buttons']['home_work_edit_on_popup_delete']['text']; ?></button>
        </td>
    </tr>
{% } %}
</script>
<?php
}

function jquery_file_upload_shortcode() {
      jquery_html5_file_upload_hook();
}

/* Add the resources */
add_action( 'wpm_head', 'wpm_jqhfu_enqueue_scripts', 1000 );

/* Load the inline script */
add_action( 'wpm_footer', 'wpm_jqhfu_add_inline_script' );

/* Hook on ajax call */
add_action('wp_ajax_load_ajax_function', 'wpm_jqhfu_load_ajax_function');
add_action('wp_ajax_nopriv_load_ajax_function', 'wpm_jqhfu_load_ajax_function');

add_shortcode ('jquery_file_upload', 'jquery_file_upload_shortcode');