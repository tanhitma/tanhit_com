<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_Autoloader
 *
 * autoloads all classes used in this plugin
 */
class WC_LD_Autoloader {

	private $path;

	/**
	 * The Constructor, sets the path of the class directory.
	 *
	 * @param $path
	 */
	public function __construct( $path ) {
		$this->path = $path;
	}


	/**
	 * Autoloader load method. Load the class.
	 *
	 * @param $class_name
	 */
	public function load( $class_name ) {

		// Only load DLM
		if ( 0 === strpos( $class_name, 'WC_LD_' ) ) {

			// String to lower
			$class_name = strtolower( $class_name );

			// Format file name
			$file_name = 'class-' . str_ireplace( '_', '-', str_ireplace( 'WC_LD_', 'wc-ld-', $class_name ) ) . '.php';

			// Setup the file path
			$file_path = $this->path;

			// Append file name to clas path
			$file_path .= $file_name;

			// Check & load file
			if ( file_exists( $file_path ) ) {
				require_once( $file_path );
			}

		}

	}

}