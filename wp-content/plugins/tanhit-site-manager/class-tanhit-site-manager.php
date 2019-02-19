<?php
/**
 * Class Tanhit_Site_Manager
 * @package Tanhit Site Manager
 * @since   1.0.0
 */
if ( ! class_exists( 'Tanhit_Site_Manager' ) ) :

	class Tanhit_Site_Manager {

		/**
		 * Options page
		 */
		const TANHIT_SITE_OPTIONS_PAGE = 'tanhit-options';	
	
		/**
		 * Plugin options key
		 */
		const TANHIT_SITE_OPTIONS_KEY = 'tanhitsite_options';	
	
		/**
		 * Parent menu page
		 * @var string
		 */
		public static $parent_menu = 'tools.php';	
	
		/**
		 * @var bool $_SCRIPT_DEBUG Internal representation of the define('SCRIPT_DEBUG')
		 */
		protected static $_SCRIPT_DEBUG = false;	
		
		/**
		 * @var string $_SCRIPT_SUFFIX Whether to use minimized or full versions of JS and CSS.
		 */
		protected static $_SCRIPT_SUFFIX = '.min';		
	
		public static function controller() {
			
			//if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				self::$_SCRIPT_DEBUG  = true;
				self::$_SCRIPT_SUFFIX = '';
			//}						

			add_action( 'init', array( __CLASS__, 'disable_wp_emojicons' ), 10 );		
		
			/**
			 * @since 1.0.0
			 */	
			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array( 
				__CLASS__, 
				'on_process_ajax'
			) );			
		
			if ( is_admin() ) {

				/* filter to remove TinyMCE emojis */
				add_filter( 'tiny_mce_plugins', array( __CLASS__, 'disable_emojicons_tinymce' ) );	
				
				add_action( 'admin_menu', array(
					__CLASS__,
					'add_admin_menu'
				) );
				
				add_action( 'admin_print_scripts', array(
					__CLASS__,
					'on_admin_scripts'
				) );				
				
			} else {
				
				add_action( 'wp_print_scripts', array(
					__CLASS__,
					'wp_print_scripts'
				) );	
			
			}
		}

		/**
		 * Enqueue scripts
		 *
		 * @scope front
		 * @since 1.0.0
		 * @return void
		 */				
		public static function wp_print_scripts() {
			
			if ( ! is_singular() ) {
				return;	
			}	
			
			if ( ! comments_open() ) {
				return;	
			}	
			
			wp_enqueue_script( 'comment-reply' );
			
		}
		
		/**
		 * @todo doc
		 */
		public static function get_options( $option = '' ) {
			$opts = get_option( self::TANHIT_SITE_OPTIONS_KEY );
			if ( ! empty( $option ) ) {
				if ( empty( $opts[ $option ] ) ) {
					return '';
				} else {	
					return $opts[ $option ];
				}
			}	
			return $opts ;
		}	
		
		/**
		 * Disable emojis for TinyMCE
		 */
		public static function disable_emojicons_tinymce( $plugins ) {
			
			if ( is_array( $plugins ) ) {
				$plugins = array_diff( $plugins, array( 'wpemoji' ) );
			}

			return $plugins;

		}
		
		/**
		 * Disable emojis
		 */
		public static function disable_wp_emojicons() {

			// all actions related to emojis
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		}
		
		/**
		 * Add submenu
		 *
		 * @scope admin
		 * @since 1.0.0
		 *
		 * @return void
		 */		
		public static function add_admin_menu() {
			
			add_submenu_page(
				self::$parent_menu,
				'Site Manager',
				'Site Manager',
				'administrator',
				self::TANHIT_SITE_OPTIONS_PAGE,
				array(
					__CLASS__,
					'options_page'
				)
			);			
			
		}
	
		/**
		 * Option page callback
		 *
		 * @scope admin
		 * @since 1.0.0
		 *
		 * @return void
		 */	
		public static function options_page() {		
		
			$options = get_option( self::TANHIT_SITE_OPTIONS_KEY );
			
			$private_category = empty( $options[ 'private_category' ] ) ? '' : $options[ 'private_category' ];
			
			$mail_poet_link = empty( $options[ 'mail_poet_link' ] ) ? '' : $options[ 'mail_poet_link' ];;
			
			?>
			<div class="wrap tanhit-wrap">
				<h1 class="">
					<?php _e( 'Tanhit Options', 'tanhit' ); ?>
				</h1>
				<h2>Установить приватные категории ( ID через запятую )</h2>
				<input class="tanhit-option" 
					data-action="set_private_category" 
					type="text" 
					value="<?php echo $private_category; ?>" 
					id="private_category" 
					name="private_category" 
					size="20" 
					placeholder="" /> 
				<br />
				<h2>Добавьте ссылку чтобы подписчики могли редактировать свои профили и списки ( MailPoet )</h2>
				<textarea 
					id="mail_poet_link" 
					name="mail_poet_link" 
					class="tanhit-option" 
					cols="120" rows="4"><?php echo $mail_poet_link; ?></textarea>	
				<br />
				<input type="button" class="tanhit-ajaxify" data-action="update_options" value="Сохранить" />	
			</div> <!-- .wrap --> <?php				
		}
		
		/**
		 * Enqueue scripts
		 *
		 * @scope admin
		 * @since 1.0.0
		 *
		 * @return void
		 */		
		public static function on_admin_scripts() {
			
			if ( empty( $_GET['page'] ) ||  self::TANHIT_SITE_OPTIONS_PAGE !== $_GET['page'] ) {
				return;
			}	
	
			wp_register_script(
				'tanhit-site-manager',
				plugin_dir_url( __FILE__ ) . 'tanhit-site-manager' . self::$_SCRIPT_SUFFIX . '.js',
				array( 'jquery' ),
				TANHIT_SITE_MANAGER_VERSION,
				true
			);
			wp_enqueue_script( 'tanhit-site-manager' );	
			wp_localize_script(
				'tanhit-site-manager',
				'TanhitSiteManager',
				array(
					'version' 			=> TANHIT_SITE_MANAGER_VERSION,
					'page'				=> $_GET['page'],
					'ajaxurl'      		=> admin_url( 'admin-ajax.php' ),
					'parentClass'  		=> __CLASS__,
					'process_ajax'		=> __CLASS__ . '_process_ajax'					
				)
			);
		
		}
		
		/**
		 * Handle ajax process
		 *
		 * @scope admin
		 * @since 1.0.0
		 * @return void
		 */		
		public function on_process_ajax() {

			$order = $_POST['order'];
			$ajax_return = array();
			$ajax_return['action'] = $order['action'];
			$ajax_return['status'] = '';
			
			switch ( $order['action'] ) :

				case 'update_options':
					
					$options = get_option( self::TANHIT_SITE_OPTIONS_KEY );
				
					foreach( $order[ 'options' ] as $option=>$value ) {
						$options[ $option ] = $value;
					}
					
					if ( update_option( self::TANHIT_SITE_OPTIONS_KEY, $options, false ) ) {
						$ajax_return['status'] = 'success';
					} else {
						$ajax_return['status'] = 'error';
					}	
					
				break;
				case 'reset_data':
					
				break;
			endswitch;

			echo json_encode( $ajax_return );
			die();
			
		}			
	
	}
	
endif;