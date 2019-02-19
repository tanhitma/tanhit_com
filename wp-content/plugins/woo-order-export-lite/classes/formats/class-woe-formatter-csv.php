<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WOE_Formatter_Csv extends WOE_Formatter {
	public static $linebreak = '';
	public static $delimiter = '';
	var $rows;
	
	public function __construct( $mode, $filename, $settings, $format, $labels ) {
		parent::__construct( $mode, $filename, $settings, $format, $labels );
		
		self::$linebreak = $this->convert_literals( $this->settings['linebreak'] );
		self::$delimiter = $this->convert_literals( $this->settings['delimiter'] );
		
		// register the filter 
		stream_filter_register( 'WOE_Formatter_Csv_crlf', 'WOE_Formatter_Csv_crlf_filter' );
		// attach to stream 
		stream_filter_append($this->handle, 'WOE_Formatter_Csv_crlf');
	}

	public function start( $data = '' ) {
		$data = apply_filters( "woe_csv_header_filter", $data );
		
		if ( $this->settings['add_utf8_bom'] ) {
			fwrite( $this->handle, chr( 239 ) . chr( 187 ) . chr( 191 ) );
		}
		
		if ( $this->settings['display_column_names'] AND $data ) {
			if ( $this->mode == 'preview' ) {
				$this->rows[] = $data;
			} else {
				fputcsv( $this->handle, $data, self::$delimiter );
			}
		}
	}
	
	private function convert_literals( $s ) {
		$s = str_replace( '\r', "\r", $s );
		$s = str_replace( '\t', "\t", $s );
		$s = str_replace( '\n', "\n", $s );
		return $s;
	}

	public function output( $rec ) {

		if ( $this->has_output_filter ) {
			$rec = apply_filters( "woe_csv_output_filter", $rec, $rec );
		}

		if ( $this->mode == 'preview' ) {
			$this->rows[] = $rec;
		} else {
			fputcsv( $this->handle, $rec, self::$delimiter);
		}
	}

	public function finish() {
		if ( $this->mode == 'preview' ) {
			fwrite( $this->handle, '<table>' );
			if ( count( $this->rows ) < 2 ) {
				$this->rows[] = array( __( '<td colspan=10><b>No results</b></td>', 'woocommerce-order-export' ) );
			}
			foreach ( $this->rows as $rec ) {
				$rec = array_map( function ( $a ) {
					return '<td>' . $a . '';
				}, $rec );
				fwrite( $this->handle, '<tr><td>' . join( '</td><td>', $rec ) . "</td><tr>\n" );
			}
			fwrite( $this->handle, '</table>' );
		}
		parent::finish();
	}


	private function adjust_cols_width() {
		$this->rows[1] = array_values( $this->rows[1] );
		foreach ( $this->rows[0] as $pos => $val1 ) {
			$val21                 = (string) $val1;
			$val2                  = (string) $this->rows[1][ $pos ];
			$max                   = max( strlen( $val1 ), strlen( $val2 ) );
			$this->rows[0][ $pos ] = str_pad( $val1, $max, " " );
			$this->rows[1][ $pos ] = str_pad( $val2, $max, " " );
		}
	}
}

// filter class that applies CRLF line endings
class WOE_Formatter_Csv_crlf_filter extends php_user_filter {
	function filter( $in, $out, &$consumed, $closing ) {
		while ( $bucket = stream_bucket_make_writeable( $in ) ) {
			// make sure the line endings aren't already CRLF
			$bucket->data = preg_replace( "/(?<!\r)\n/", WOE_Formatter_Csv::$linebreak, $bucket->data );
			$consumed += $bucket->datalen;
			stream_bucket_append( $out, $bucket );
		}

		return PSFS_PASS_ON;
	}
}

