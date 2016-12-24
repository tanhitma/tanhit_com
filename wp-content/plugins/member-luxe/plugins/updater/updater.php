<?php


function wpm_update_plugin( $url = 'http://static.wppage.ru/member-luxe/member-luxe.zip' ) { // 
	$return = '';
	$target_url = ABSPATH . 'wp-content/plugins/member-luxe/';
	$download_file = download_url( $url ); // download target file
	if ( is_wp_error($download_file) ) {
		$return .= ' <p>Ошибка: скачивание файла не удалось.</p> ';
		return $return;
	}


	$dir = $target_url; // delete all files and folders in target folder
	if( is_dir( $dir ) ) { // Open a known directory, and proceed to read its contents
		if( $dh = opendir( $dir ) ) {
			while( ( $file = readdir( $dh ) ) !== false ) {
				if ($file != "." && $file != "..") {
					if( is_dir( $target_url.$file ) ){
						wpm_delete_folder( $target_url.$file );
					}
					if( is_file( $target_url.$file ) ){
						unlink( $target_url.$file ); // delete file
					}
				}
			}
		}
	}
	WP_Filesystem();
	$result = unzip_file( $download_file, $target_url );
	$return .= ' <br>target_url= ' . $target_url.' <br> ';

	if ( is_wp_error( $result ) ) {
		if ( 'incompatible_archive' == $result->get_error_code() ) {
			$return .= ' <p>Ошибка: разархивация не удалась.</p> ';
			return $return;
		}
	}
	$cozy_updater_plugin = 'member-luxe/member-luxe.php';
	deactivate_plugins( $cozy_updater_plugin );
	activate_plugins( $cozy_updater_plugin );

	unlink( $download_file ); // delete downloaded file
	$return = '<h2>Обновление прошло успешно.</h2>';

	wpm_install();

	return $return;
}

//----------------------

function wpm_delete_folder_new( $targ ){ // recursively delete folders and files
    if(is_dir($targ)){
        $files = glob( $targ . '*', GLOB_MARK ); // GLOB_MARK adds a slash to directories returned
        foreach( $files as $file ){
            //if ( $file != '.' && $file != '..' ) {
            wpm_delete_folder( $file ); // recursive
            //}
        }
        rmdir( $targ );
        echo '<p><strong>remove dir</strong>: '.$targ.'</p>';
    }else{
        unlink( $targ );
        echo '<p>remove file: '.$targ.'</p>';
    }
}

//-------------------------------

function wpm_delete_folder($dirname) {
    if (is_dir($dirname)){
        $dir_handle = opendir($dirname);
    }
    if (!$dir_handle){
        return false;
    }
    while($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname."/".$file)){
                unlink($dirname."/".$file);
            }else{
                wpm_delete_folder($dirname.'/'.$file);
            }
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}


function wpm_get_latest_version() {
	$fw_url = 'http://static.wppage.ru/member-luxe/readme.txt'; //http://static.wppage.ru/readme.txt 
	$temp_file_addr = download_url( $fw_url );
	$latest_version = '0';
	if( ! is_wp_error( $temp_file_addr ) && $file_contents = file( $temp_file_addr ) ) {
		foreach ( $file_contents as $line_num => $line ) {
			$current_line =  $line;
			if ( strpos($line, 'version') !== false ) { // preg_match( '/^[0-9]/', $line )
				$current_line = stristr( $current_line, 'version' ); // search for the first word 'version' in the file line y line
				$current_line = preg_replace( '~[^0-9,.]~','',$current_line );
				$output['version'] = $current_line;
				$latest_version = $current_line;
				break;
			}
		}
		unlink( $temp_file_addr );

	}
	return $latest_version;
}


add_action('admin_head', 'wpm_update_check'); // check WPM update in 'admin_head' hook
function wpm_update_check() {
	if( !get_option( 'wpm_latest_version' ) ) { // option does not exist
		$wpm_latest_version = wpm_get_latest_version(); // check latest version
		update_option( 'wpm_latest_version', $wpm_latest_version );
		update_option( 'wpm_update_check', date( 'Y-m-d H:i:s' ) );
	} else {
		if( strtotime( get_option( 'wpm_update_check' ) ) < strtotime( '-1 hours' ) ) { // check if it is passed 1 day since last update check
			$wpm_latest_version = wpm_get_latest_version(); // check latest version
			update_option( 'wpm_latest_version', $wpm_latest_version );
            update_option( 'wpm_update_check', date( 'Y-m-d H:i:s' ) );
		}
	}

	$wpm_latest_version = get_option( 'wpm_latest_version' );
	$wpm_version = get_option( 'wpm_version' );
	if( version_compare( $wpm_version, $wpm_latest_version ) < 0) { // we need to update

    }
	return version_compare( $wpm_version, $wpm_latest_version );
}



function wpm_update_info() {
	$return_html = '';

	$wpm_latest_version = get_option( 'wpm_latest_version' );
	$wpm_version = get_option( 'wpm_version' );
	$wpm_update_check = get_option( 'wpm_update_check' );

    if( version_compare( $wpm_version, $wpm_latest_version ) < 0 ) { // we need to update
        $return_html .= '<p><strong>Появилась новая версия MemberLux!.</strong></p>';
    } else {
        $return_html .= '<p><strong>У вас установлена последняя версия.</strong></p>';
    }

	$return_html .= '<p>Ваша версия: '.$wpm_version.'</p>';
	$return_html .= '<p>Последняя версия: '.$wpm_latest_version.'</p>';
	$return_html .= '<p>Время проверки обновления: '.$wpm_update_check.'</p>';

	return $return_html;
}



//---------------------------

function wpm_updater() {

?>
    <div class="wrap wpm-options-page">
        <div id="icon-options-general" class="icon32"></div>
        <h2>Обновление</h2>
        <div class="options-wrap wpm-ui-wrap">
            <div class="content-wrap">
                <?php
                if( isset($_GET['do']) && $_GET['do'] == 'update' ){
                    $wpm_latest_version = get_option('wpm_latest_version');
                    echo wpm_update_plugin();
                    update_option( 'wpm_version', $wpm_latest_version );
                }

                wpm_update_check();

                echo wpm_update_info();

                $wpm_latest_version = get_option( 'wpm_latest_version' );
                $wpm_version = get_option( 'wpm_version' );

                //echo 'vers='.version_compare( $wpm_version, $wpm_latest_version );

                if( version_compare( $wpm_version, $wpm_latest_version ) < 0) : // we need to update

                    ?>

                    <form action="edit.php" method="get">
                        <input type="hidden" name="page" value="wpm-updater">
                        <input type="hidden" name="post_type" value="wpm-page">
                        <input type="hidden" name="do" value="update">
                        <p class="submit ">
                            <input type="submit" class="button-primary button-hero" value="Обновить" />
                        </p>

                    </form>

                <?php
                endif;
                ?>
            </div>
	</div> <!-- /#tab_container -->

</div> <!-- /.wrap -->
<?php

}
