<?php
/**
 * Plugin Name: Hayona Cookie Consent
 * Plugin URI: 
 * Description: A straightforward plugin to comply with the EU cookie law, including implied consent. 
 * Author: Hayona
 * Version: 2.0.0
 * Author URI: http://www.hayona.nl
 * License: GPLv2
 * Domain Path: /languages
 * Text Domain: hayona-cookies
 * GitHub Plugin URI: https://github.com/hayona/hayona-cookies
 * 
 * Hayona Cookie Consent is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * Hayona Cookie Consent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *  
 * You should have received a copy of the GNU General Public License
 * along with Hayona Cookie Consent. If not, see <http://www.gnu.org/licenses/>.
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Hayona_Cookies {

	
	/**
	 * Static property to hold our singleton instance
	 */
	static $instance = false;


	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$is_enabled = esc_attr( get_option( 'hc_is_enabled' ) );

		// Front end
		if( $is_enabled == "on" ) {
			add_action( 'wp_footer', 				array( $this, 'cookie_banner' ), 100 );
			add_action( 'wp_enqueue_scripts', 		array( $this, 'front_end_assets' ) );
			add_filter( 'the_content', 				array( $this, 'privacy_settings_form' ) );
			add_filter( 'body_class', 				array( $this, 'get_color_scheme' ) );
		}

		// Back end
		add_action( 'upgrader_process_complete', 	array( $this, 'plugin_updated' ) );
		add_action( 'plugins_loaded', 				array( $this, 'load_translations' ) );
		add_action( 'admin_init', 					array( $this, 'admin_register_settings' ) );
		add_action( 'admin_head', 					array( $this, 'admin_assets' ) );
		add_action( 'admin_menu', 					array( $this, 'admin_options_page' ) );

		if( $is_enabled != "on" ) {
			add_action( 'admin_notices', 			array( $this, 'admin_notice_disabled' ) ); 
		}

		// Activation
		register_activation_hook( __FILE__, 		array( $this, 'activate_plugin' ) );
	}


	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * returns it.
	 *
	 * @return Hayona_Cookies
	 */
	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}


	/**
	 * Load translations
	 *
	 * @return void
	 */
	public function load_translations() {

		load_plugin_textdomain(
			'hayona-cookies', 
			false, 
			basename( dirname( __FILE__ ) ) . '/languages' 
		);
	}

	private function plugin_updated( $upgrader_object, $data ) {
		$hc_plugin = plugin_basename( __FILE__ );

		if ( $options['action'] == 'update' &&
			$options['type'] == 'plugin' &&
			isset( $options['plugins'] ) ) {
			
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin == $hc_plugin ) {
					set_transient( 'hc_updated', 1 );
				}
			}
		}
	}


	/**
	 * Activate plugin
	 */
	public function activate_plugin() {

		// Check whether the plugin got updated. If not, set a installed transient
		// variable to tag it as a new installation.
		if ( !get_transient( 'hc_updated' ) ) {
			set_transient( 'hc_installed', 1 );
			// update_option( 'hc_legacy_mode_enabled', 'on' ); // for debugging
		} else {
			update_option( 'hc_legacy_mode_enabled', 'on' );
		}

		// Check if plugin has been activated before
		$timestamp = esc_attr( get_option( 'hc_consent_timestamp' ) );

		if( ! isset( $timestamp ) ) {
			// Set some defaults
			$this->invalidate_consent();
			update_option( 'hc_implied_consent_enabled', 'on' );
			update_option( 'hc_banner_text', __('This site uses cookies. By continuing to browse the site or clicking OK, you are agreeing to our use of cookies. Select ‘Change settings’ for more information.', 'hayona-cookies'));
		}
	}


	/**
	 * Invalidate consent
	 *
	 * @description: Invalidate consent of all users by resetting timestamp
	 */
	public function invalidate_consent() {
		$timestamp = intval( microtime( true ) * 1000 );
		update_option( 'hc_consent_timestamp', $timestamp );
	}


	/**
	 * Get cookies
	 * 
	 * @description: Returns array with cookies that the user has specified
	 */
	private function get_cookies() {

		$options = array( 'not_required', 'required' );

		foreach( $options as $option ) {
			$get_cookies[ $option ] = explode("\n", esc_attr( get_option( 'hc_cookielist_consent_' . $option ) ) );
		}

		return $get_cookies;
	}

	/**
	 * Get color scheme  
	 *
	 * @description: Return color scheme classname
	 */
	public function get_color_scheme( $classes ) {
		// Get color scheme
		$color_scheme = esc_attr( get_option( 'hc_banner_color_scheme' ) );

		switch ($color_scheme) {
			case 'light':
				$classes[] = 'hc-styling';
				$classes[] = 'hc-styling--light';
				break;

			case 'none':
				$color_scheme_class = '';
				break;
			
			default:
				$classes[] = 'hc-styling';
				$classes[] = 'hc-styling--dark';
				break;
		}
		return $classes;
	}


	/**
	 * Front end assets
	 *
	 * @description: Enqueue front-end assets
	 */
	public function front_end_assets() {

		// Check if it isn't a new installation
		if ( !get_transient( 'hc_installed' ) ) {
			$hc_legacy_mode_enabled = esc_attr( get_option( 'hc_legacy_mode_enabled' ) );

			// Load in the proper css and javascript based on the
			// current hc_legacy_mode_enabled option
			if ( $hc_legacy_mode_enabled == 'on' ) {
				wp_enqueue_style(
					'hayona-cookies',
					plugins_url( 'assets/css/1.1.4/min/style.css', __FILE__ ),
					array(),
					'1.1',
					'screen'
				);
	
				wp_enqueue_script(
					'hayona-cookies',
					plugins_url( 'assets/js/1.1.4/min/cookie-banner.min.js', __FILE__ ),
					array(),
					'1.1',
					true
				);
			} else {
				wp_enqueue_style(
					'hayona-cookies-style',
					plugins_url( 'assets/css/2.0/min/style.css', __FILE__ ),
					array(),
					'2.0',
					'screen'
				);

				wp_enqueue_style(
					'hayona-cookies-style-settings',
					plugins_url( 'assets/css/2.0/settings.css', __FILE__ ),
					array(),
					'2.0',
					'screen'
				);

				wp_enqueue_script(
					'hayona-cookies-banner',
					plugins_url( 'assets/js/2.0/min/cookie-banner.min.js', __FILE__ ),
					array(),
					'2.0',
					true
				);

				wp_enqueue_style(
					'hayona-cookies-settings',
					plugins_url( 'assets/js/2.0/settings.js', __FILE__ ),
					array(),
					'2.0'
				);
			}
		} else {
			wp_enqueue_style(
				'hayona-cookies-style',
				plugins_url( 'assets/css/2.0/min/style.css', __FILE__ ),
				array(),
				'2.0',
				'screen'
			);

			wp_enqueue_style(
				'hayona-cookies-style-settings',
				plugins_url( 'assets/css/2.0/settings.css', __FILE__ ),
				array(),
				'2.0',
				'screen'
			);

			wp_enqueue_script(
				'hayona-cookies-banner',
				plugins_url( 'assets/js/2.0/min/cookie-banner.min.js', __FILE__ ),
				array(),
				'2.0',
				true
			);

			wp_enqueue_script(
				'hayona-cookies-settings',
				plugins_url( 'assets/js/2.0/settings.js', __FILE__ ),
				array(),
				'2.0'
			);
		}

		// for debugging purpose:
		// $hc_legacy_mode_enabled = esc_attr( get_option( 'hc_legacy_mode_enabled' ) );

		// if ( $hc_legacy_mode_enabled == 'on' ) {
		// 	wp_enqueue_style(
		// 		'hayona-cookies',
		// 		plugins_url( 'assets/css/1.1.4/min/style.css', __FILE__ ),
		// 		array(),
		// 		'1.1.4',
		// 		'screen'
		// 	);

		// 	wp_enqueue_script(
		// 		'hayona-cookies',
		// 		plugins_url( 'assets/js/1.1.4/min/cookie-banner.min.js', __FILE__ ),
		// 		array(),
		// 		'1.1.4',
		// 		true
		// 	);
		// } else {
		// 	wp_enqueue_style(
		// 		'hayona-cookies-style',
		// 		plugins_url( 'assets/css/2.0/min/style.css', __FILE__ ),
		// 		array(),
		// 		'2.0',
		// 		'screen'
		// 	);

		// 	wp_enqueue_style(
		// 		'hayona-cookies-style-settings',
		// 		plugins_url( 'assets/css/2.0/settings.css', __FILE__ ),
		// 		array(),
		// 		'2.0',
		// 		'screen'
		// 	);

		// 	wp_enqueue_script(
		// 		'hayona-cookies-banner',
		// 		plugins_url( 'assets/js/2.0/min/cookie-banner.min.js', __FILE__ ),
		// 		array(),
		// 		'2.0',
		// 		true
		// 	);

		// 	wp_enqueue_script(
		// 		'hayona-cookies-settings',
		// 		plugins_url( 'assets/js/2.0/settings.js', __FILE__ ),
		// 		array(),
		// 		'2.0'
		// 	);
		// }
	}

	/**
	 * Cookie legacy banner
	 * 
	 * Description: Shows the legacy banner on the page
	 */
	private function cookie_legacy_banner() {
		// Get permission timestamp
		$permission_timestamp = esc_attr( get_option('hc_consent_timestamp') );

		// Is implied consent enabled?
		$implied_consent_enabled = esc_attr( get_option( 'hc_implied_consent_enabled' ) ); 

		if( $implied_consent_enabled == "on" ) {
			$implied_consent_enabled = "true";
		} else {
			$implied_consent_enabled = "false";
		}

		// Are we on a settings page?
		$settings_page_id = esc_attr( get_option( 'hc_privacy_statement_url' ) );
		$current_page_id = get_the_id();

		if( $settings_page_id == $current_page_id ) {
			$is_settings_page = "true";
		} else {
			$is_settings_page = "false";
		}

		// Load banner html
		$cookie_expiration = esc_attr( get_option('hc_cookie_expiration') );
		$banner_type = esc_attr( get_option( 'hc_banner_type' ) );
		$banner_markup = '<div class="hc-banner">
						<div class="hc-banner__body">' . esc_attr( get_option('hc_banner_text') ) . '</div>
						<ul class="hc-toolbar">
							<li><a class="hc-button accept-cookies" href="#">' . __( "All right, close", 'hayona-cookies' ) . '</a></li>
							<li><a class="hc-dimmed" href="' . get_permalink( esc_attr( get_option( 'hc_privacy_statement_url' ) ) ) . '"> ' . __( "Change your settings", 'hayona-cookies' ) . ' </a></li>
						</ul>
						<div class="hc-banner__close">
							<span class="hc-banner__close__icon"></span> ' . __( 'Cancel', 'hayona-cookies' ) . '
						</div>
					</div>';
		$banner_script = '<script type="text/javascript">
						jQuery(document).ready( hayonaCookies.init( {
							timestamp: ' . $permission_timestamp . ',
							isSettingsPage: ' . $is_settings_page . ', 
							implicitConsentEnabled: ' . $implied_consent_enabled . ',
							cookieExpiration: ' . $cookie_expiration . ',
							bannerType: "' . $banner_type . '"
						} ) );
					</script>';

		$banner_markup = apply_filters( 'hc_banner_markup', $banner_markup );
		$banner_script = apply_filters( 'hc_banner_script', $banner_script );

		// Print banner to page
		echo $banner_markup . $banner_script;
	}

	/**
	 * Cookie new banner
	 * 
	 * Description: Shows the new banner on the page
	 */
	private function cookie_new_banner() {

		$privacy_statement_url = get_permalink( esc_attr( get_option( 'hc_privacy_statement_url' ) ) );

		$cookie_expiration = esc_attr( get_option( 'hc_cookie_expiration' ) );

		$form_placement_before = esc_attr( get_option( 'hc_form_placement_before' ) );

		$use_body_offset = esc_attr( get_option( 'hc_banner_use_body_offset' ) ) ? 'true' : 'false';

		$offset_header_selector = esc_attr( get_option( 'hc_banner_offset_header_selector' ) );

		if ( empty( $offset_header_selector ) )
			$offset_header_selector = 'undefined';

		$hc_banner_button_yes_text = esc_attr( get_option( 'hc_banner_text_button_yes' ) );
		$hc_banner_button_no_text = esc_attr( get_option( 'hc_banner_text_button_no' ) );

		$implied_consent_enabled = esc_attr( get_option( 'hc_implied_consent_enabled' ) ) ? 'true' : 'false';

		$hc_banner_text = esc_attr( get_option( 'hc_banner_text' ) );
		$form_head_text = esc_attr( get_option( 'hc_form_head_text' ) );
		$form_subtitle_text = esc_attr( get_option( 'hc_form_subtitle_text' ) );
		$form_option_one_button_text = esc_attr( get_option( 'hc_form_option_one_button_text' ) );
		$form_option_two_button_text = esc_attr( get_option( 'hc_form_option_two_button_text' ) );
		$form_allowed_text = esc_attr( get_option( 'hc_form_allowed_text' ) );
		$form_disallowed_text = esc_attr( get_option( 'hc_form_disallowed_text' ) );

		$cookies = $this->get_cookies();

		// If the use body offset setting is on true, display
		// the banner on the top of the page. If false, display
		// it on the bottom.
		if ( $use_body_offset == 'true' ) {
			if ( is_admin_bar_showing() ) {
				$banner_markup = "
				.gtmcc-banner {
					position: fixed !important;
					top: 31px !important;
					left: 0 !important;
					right: 0 !important;
					z-index: 21 !important;
				}";
			} else {
				$banner_markup = "
				.gtmcc-banner {
					position: fixed !important;
					top: 0 !important;
					left: 0 !important;
					right: 0 !important;
					z-index: 21 !important;
				}";
			}
		} else {
			$banner_markup = "
			.gtmcc-banner {
				position: fixed !important;
				bottom: 0 !important;
				left: 0 !important;
				right: 0 !important;
				z-index: 21 !important;
			}";
		}

		// The new banner settings
		$banner_script = "var cookieConsentSettings = {
				
					/* Edit general settings */
					privacyStatementUrl: '$privacy_statement_url',
					cookies: [
						['test'],
						['test2']
					],
			resetAllBeforeTimestamp: 1530021013000,
			consentExpiration: $cookie_expiration,
			gtmEventName: 'consent',
			gtmTrackEventName: 'track_consent',
			formPlacementBefore: '$form_placement_before',
			useBodyOffset: $use_body_offset,\n\t\t\t";

			if ( $offset_header_selector != 'undefined' ) {
				$banner_script .= "offsetHeaderSelector: '$offset_header_selector',\n\t\t\t";
			} else {
				$banner_script .= "offsetHeaderSelector: $offset_header_selector,\n\t\t\t";
			}

			$banner_script .= " implicitConsentEnabled: $implied_consent_enabled,

			/* Edit banner text */
			explanation: '$hc_banner_text',
			buttonYes: '$hc_banner_button_yes_text',
			buttonNo: '$hc_banner_button_no_text',

			/* Edit form text */
			formHead: '$form_head_text',
			formSubtitle: '$form_subtitle_text',
			optionOneButton: '$form_option_one_button_text',
			optionTwoButton: '$form_option_two_button_text',
			allowed: '$form_allowed_text',
			disallowed: '$form_disallowed_text'
		};";

		$banner_markup = apply_filters( 'hc_banner_markup', $banner_markup );
		$banner_script = apply_filters( 'hc_banner_script', $banner_script );

		// Write the settings to the setting files
		file_put_contents( plugin_dir_path( __FILE__ ) . 'assets/css/2.0/settings.css', $banner_markup );
		file_put_contents( plugin_dir_path( __FILE__ ) . 'assets/js/2.0/settings.js', $banner_script );
	}


	/**
	 * Cookie banner
	 *
	 * @description: Insert cookie banner to page
	 */
	public function cookie_banner() {

		// Check if the plugin got updated instead of installed
		if ( !get_transient( 'hc_installed' ) ) {
		// if ( get_transient( 'hc_installed' ) ) { // for debugging purpose
			$hc_legacy_mode_enabled = esc_attr( get_option( 'hc_legacy_mode_enabled' ) );

			if ( $hc_legacy_mode_enabled == 'on' ) {
				$this->cookie_legacy_banner();
			} else {
				$this->cookie_new_banner();
			}
		} else {
			$this->cookie_new_banner();
		}
	}


	/**
	 * Privacy settings form
	 *
	 * @description: Output privacy settings to the right page
	 */
	public function privacy_settings_form( $content ) {

		$settings_page_id = esc_attr( get_option('hc_privacy_statement_url') );
		$current_page_id = get_the_id();
		$legacy_mode_enabled = esc_attr( get_option( 'hc_legacy_mode_enabled' ) );

		if( $settings_page_id == $current_page_id && $legacy_mode_enabled != false ) {

			// Get all cookies
			$cookies = $this->get_cookies();

			$cookielist = array("", ""); 
			foreach( $cookies["not_required"] as $cookie ) {
				$cookielist[0] .= '
					<tr>
						<td>' . $cookie . '</td>
						<td>' . __( 'Allowed', 'hayona-cookies' ) . '</td>
					</tr>';
				$cookielist[1] .= '
					<tr>
						<td>' . $cookie . '</td>
						<td>' . __( 'Allowed', 'hayona-cookies' ) . '</td>
					</tr>';
			}
			foreach( $cookies["required"] as $cookie ) {
				$cookielist[0] .= '
					<tr>
						<td>' . $cookie . '</td>
						<td>' . __( 'Allowed', 'hayona-cookies' ) . '</td>
					</tr>';
				$cookielist[1] .= '
					<tr>
						<td>' . $cookie . '</td>
						<td>' . __( 'Not Allowed', 'hayona-cookies' ) . '</td>
					</tr>';
			}

			// Privacy settings HTML code
			$privacy_settings = '

				<div class="hc-settings">
					<div class="hc-settings__header">
						<span class="hc-h2">' . __( 'Cookie preferences', 'hayona-cookies' ) . '</span>
						<p>
							' . __( 'Please select which cookies you want to accept from this website.', 'hayona-cookies' ) . '
						</p>
					</div>
					<ul class="hc-settings__options">
						<li><div class="hc-settings__option">
							<span class="hc-h3"> ' .
								__( 'Option 1', 'hayona-cookies' ) . '
							</span>
							<p> ' .
								__( 'Allow all cookies', 'hayona-cookies' ) . '
							</p>
							<table class="hc-cookielist">
								'. $cookielist[0] . '
							</table>
							<a class="hc-button hc-button--grey accept-cookies" href="#">' . 
								__( 'Allow all cookies', 'hayona-cookies' ) . '								
							</a>
							<span class="hc-status hc-status--accept" style="display: none;">' . 
								__( "You've picked this option.", 'hayona-cookies' ) . '
							</span>
						</div></li>
						<li><div class="hc-settings__option">
							<span class="hc-h3"> ' .
								__( 'Option 2', 'hayona-cookies' ) . '
							</span>
							<p>' .
								__( 'Accept only functional and non privacy sensitive cookies (no PII)', 'hayona-cookies' ) . '
							</p>
							<table class="hc-cookielist">
								'. $cookielist[1] . '
							</table>
							<a class="hc-button hc-button--grey reject-cookies" href="#">' . 
								__( 'Allow some cookies', 'hayona-cookies' ) . '
							</a>
							<span class="hc-status hc-status--reject" style="display: none;">' . 
								__( "You've picked this option.", 'hayona-cookies' ) . '
							</span>
						</div></li>
					</ul>
				</div>';
			$privacy_settings = apply_filters( 'hc_privacy_settings_form', $privacy_settings );
			$content = $privacy_settings . $content;
		} 

		return $content;
	}


	/**
	 * Admin assets
	 *
	 * @description: Enqueue admin assets
	 */
	public function admin_assets() {

		wp_enqueue_style( 
			'hayona-cookies', 
			plugins_url( 'assets/css/min/admin.css', __FILE__ ) 
		);
	}


	/**
	 * Admin notice disabled
	 *
	 * @description: Notify user if the banner is disabled
	 */
	public function admin_notice_disabled() {
		$class = "notice is-dismissible updated";

		$message = sprintf( 
			wp_kses( 
				__('The cookie banner is not visible yet. Go to the <a href="%s">plugin settings page</a> to configure and enable the plugin.', 
				'hayona-cookies'), 
			array(  'a' => array( 'href' => array() ) ) ), 
			esc_url( admin_url( 'options-general.php?page=hayona-cookies' ) ) 
		);

		echo 	"<div class=\"$class\"> 
					<p>$message</p>
					<button type=\"button\" class=\"notice-dismiss\">
						<span class=\"screen-reader-text\">" . __( "Dismiss this notice.", 'hayona-cookies' ) . "</span>
					</button>
				</div>"; 
	}

	/**
	 * Admin legacy settings
	 * 
	 * registers all legacy settings
	 */
	public function admin_register_legacy_settings() {
		// Banner settings 
		add_settings_section( 'hc_section_banner', __('Banner settings', 'hayona-cookies'), array( $this, 'section_banner_callback'), 'hc_banner' );
		register_setting( 'hc_banner', 'hc_implied_consent_enabled' );
		register_setting( 'hc_banner', 'hc_banner_text' );
		register_setting( 'hc_banner', 'hc_banner_color_scheme' );
		register_setting( 'hc_banner', 'hc_banner_type' );
		add_settings_field( 'hc_banner_text', __( 'Banner text', 'hayona-cookies' ), array( $this, 'field_banner_text_callback'), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_implied_consent_enabled', __( 'Implied consent', 'hayona-cookies' ), array( $this, 'field_implied_consent_enabled_callback'), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_banner_color_scheme', __( 'Color scheme', 'hayona-cookies' ), array( $this, 'field_banner_color_scheme_callback'), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_banner_type', __( 'Banner type', 'hayona-cookies' ), array( $this, 'field_banner_type_callback'), 'hc_banner', 'hc_section_banner' );
	}

	/**
	 * Admin new settings
	 * 
	 * registers all new settings
	 */
	public function admin_register_new_settings() {
		// Banner settings
		add_settings_section( 'hc_section_banner', __( 'Banner settings', 'hayona-cookies'), array( $this, 'section_new_banner_callback'), 'hc_banner' );

		register_setting( 'hc_banner', 'hc_implied_consent_enabled' );
		register_setting( 'hc_banner', 'hc_banner_text' );
		register_setting( 'hc_banner', 'hc_banner_color' );
		register_setting( 'hc_banner', 'hc_banner_text_button_yes' );
		register_setting( 'hc_banner', 'hc_banner_text_button_no' );
		register_setting( 'hc_banner', 'hc_banner_use_body_offset' );
		register_setting( 'hc_banner', 'hc_banner_offset_header_selector' );

		add_settings_field( 'hc_banner_text', __( 'Banner text', 'hayona-cookies' ), array( $this, 'field_banner_text_callback' ), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_implied_consent_enabled', __( 'Implied consent', 'hayona-cookies' ), array( $this, 'field_implied_consent_enabled_callback' ), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_banner_color', __( 'Banner color', 'hayona-cookies' ), array( $this, 'field_banner_color_callback' ), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_banner_text_button_yes', __( 'Banner Yes button text', 'hayona-cookies' ), array( $this, 'field_banner_text_button_yes_callback' ), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_banner_text_button_no', __( 'Banner No button text', 'hayona-cookies' ), array( $this, 'field_banner_text_button_no_callback' ), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_banner_use_body_offset', __( 'Use body offset', 'hayona-cookies' ), array( $this, 'field_banner_use_body_offset_callback' ), 'hc_banner', 'hc_section_banner' );
		add_settings_field( 'hc_banner_offset_header_selector', __( 'Offset header selector', 'hayona-cookies' ), array( $this, 'field_banner_offset_header_selector_callback' ), 'hc_banner', 'hc_section_banner' );

		// Form settings
		add_settings_section( 'hc_section_form', __( 'Form settings', 'hayona-cookies' ), array( $this, 'section_form_callback'), 'hc_form' );

		register_setting( 'hc_form', 'hc_form_placement_before' );
		register_setting( 'hc_form', 'hc_form_head_text' );
		register_setting( 'hc_form', 'hc_form_subtitle_text' );
		register_setting( 'hc_form', 'hc_form_option_one_button_text' );
		register_setting( 'hc_form', 'hc_form_option_two_button_text' );
		register_setting( 'hc_form', 'hc_form_allowed_text' );
		register_setting( 'hc_form', 'hc_form_disallowed_text' );

		add_settings_field( 'hc_form_placement_before', __( 'Form placement before', 'hayona-cookies' ), array( $this, 'field_form_placement_before_callback' ), 'hc_form', 'hc_section_form' );
		add_settings_field( 'hc_form_head_text', __( 'Form head text', 'hayona-cookies' ), array( $this, 'field_form_head_text_callback' ), 'hc_form', 'hc_section_form' );
		add_settings_field( 'hc_form_subtitle_text', __( 'Form subtitle text', 'hayona-cookies' ), array( $this, 'field_form_subtitle_text_callback' ), 'hc_form', 'hc_section_form' );
		add_settings_field( 'hc_form_option_one_button_text', __( 'Form option one button text', 'hayona-cookies' ), array( $this, 'field_form_option_one_button_text_callback' ), 'hc_form', 'hc_section_form' );
		add_settings_field( 'hc_form_option_two_button_text', __( 'Form option two button text', 'hayona-cookies' ), array( $this, 'field_form_option_two_button_text_callback' ), 'hc_form', 'hc_section_form' );
		add_settings_field( 'hc_form_allowed_text', __( 'Form allowed text', 'hayona-cookies' ), array( $this, 'field_form_allowed_text_callback' ), 'hc_form', 'hc_section_form' );
		add_settings_field( 'hc_form_disallowed_text', __( 'Form disallowed text', 'hayona-cookies' ), array( $this, 'field_form_disallowed_text_callback' ), 'hc_form', 'hc_section_form' );
	}

	/**
	 * Admin register settings
	 *
	 * @description: Register all plugin settings
	 */
	public function admin_register_settings() { 

		// General settings
		add_settings_section( 'hc_section_general', __('General settings', 'hayona-cookies'), array( $this, 'section_general_callback' ), 'hc_general' );
		register_setting( 'hc_general', 'hc_privacy_statement_url' );
		register_setting( 'hc_general', 'hc_is_enabled' );
		add_settings_field( 'hc_privacy_statement_url', __( 'Privacy statement', 'hayona-cookies' ), array( $this, 'field_privacy_statement_url_callback' ), 'hc_general', 'hc_section_general' );
		add_settings_field( 'hc_is_enabled', __( 'Enable plugin', 'hayona-cookies' ), array( $this, 'field_is_enabled_callback'), 'hc_general', 'hc_section_general' );

		$legacy_mode_enabled = esc_attr( get_option( 'hc_legacy_mode_enabled' ) );

		if ( $legacy_mode_enabled != false ) {
			$this->admin_register_legacy_settings();
		} else {
			$this->admin_register_new_settings();
		}

		// Cookie settings 
		add_settings_section( 'hc_section_cookie', __('Cookie settings', 'hayona-cookies'), array( $this, 'section_cookie_callback'), 'hc_cookies' );
		register_setting( 'hc_cookies', 'hc_cookielist_consent_required' );
		register_setting( 'hc_cookies', 'hc_cookielist_consent_not_required' );
		register_setting( 'hc_cookies', 'hc_cookie_expiration' );
		register_setting( 'hc_cookies', 'hc_consent_timestamp' );
		register_setting( 'hc_cookies', 'hc_reset_consent_timestamp' );
		add_settings_field('hc_cookielist_consent_not_required', __( 'No permission required', 'hayona-cookies' ), array( $this, 'field_cookielist_consent_not_required_callback'), 'hc_cookies', 'hc_section_cookie');
		add_settings_field('hc_cookielist_consent_required', __( 'Permission required', 'hayona-cookies' ), array( $this, 'field_cookielist_consent_required_callback'), 'hc_cookies', 'hc_section_cookie');
		add_settings_field('hc_cookie_expiration', __( 'Cookie expiration time', 'hayona-cookies' ), array( $this, 'field_cookie_expiration_callback'), 'hc_cookies', 'hc_section_cookie');
		add_settings_field('hc_reset_consent_timestamp', __( 'Reset permissions', 'hayona-cookies' ), array( $this, 'field_reset_consent_timestamp_callback'), 'hc_cookies', 'hc_section_cookie');

		// Only add the legacy mode option if the user updated to the latest version
		if ( get_transient( 'hc_updated' ) ) {
		// if ( get_transient( 'hc_installed' ) ) { // debug
			register_setting( 'hc_general', 'hc_legacy_mode_enabled' );
			add_settings_field( 'hc_legacy_mode_enabled', __( 'Enable legacy mode', 'hayona-cookies' ), array( $this, 'field_legacy_mode_enabled_callback' ), 'hc_general', 'hc_section_general' );
		}
	}


	/**
	 * Admin options page
	 *
	 */
	public function admin_options_page() {

		add_options_page( 
			'Hayona Cookie Consent', 
			'Cookie Consent', 
			'manage_options', 
			'hayona-cookies', 
			array( $this, 'options_page_callback' ) 
		); 
	}


	/**
	 * Callback functions
	 */
	public function section_general_callback() {
		echo 
			'<p class="larger">' .
					__( "You're almost there!", 'hayona-cookies' ) .
					" " .
					sprintf( wp_kses(
						__( 'Please go through our step by step <a href="%1$s">installation guide</a>', 'hayona-cookies' ), 
						array(  'a' => array( 'href' => array() ) ) ),
						esc_url('https://wordpress.org/plugins/hayona-cookies/installation/') ) .
					" " .
					 __( "and enable the plugin if you're done.", 'hayona-cookies' ) .
			'</p>';
	}

	public function field_privacy_statement_url_callback() {
		$hc_pages = get_pages();
		$hc_current_page = esc_attr( get_option('hc_privacy_statement_url') );

		echo '<select name="hc_privacy_statement_url">';
		foreach( $hc_pages as $page ) {
	
			if( $page->ID == $hc_current_page ) {
				echo "<option value=\"$page->ID\" selected=\"selected\">$page->post_title</option>";
			} else {
				echo "<option value=\"$page->ID\">$page->post_title</option>";
			}
		}
		echo '</select>';
		echo '<p class="description">';
			_e( "Please select the page that contains your privacy statement. A small cookie preferences form will be placed at the top of this page. This way your visitors can change their privacy settings on any given moment.", 'hayona-cookies' );
		echo '</p>';
	}

	public function field_is_enabled_callback() {
		$hc_is_enabled = esc_attr( get_option('hc_is_enabled') );

		echo '<label for="hc-form__enable">';
		if( $hc_is_enabled == "on" ) {
			echo "<input type=\"checkbox\" name=\"hc_is_enabled\" id=\"hc-form__enable\" checked=\"checked\" />";
			_e( "Enable plugin", 'hayona-cookies' );
		} else {
			echo "<input type=\"checkbox\" name=\"hc_is_enabled\" id=\"hc-form__enable\" />";
			_e( "Enable plugin", 'hayona-cookies' );
		}
		echo '</label>';
		echo '<p class="description">';
		echo __( "You can enable the plugin if you have done the steps listed above.", 'hayona-cookies' );
		echo '</p>';
	}

	public function field_legacy_mode_enabled_callback() {
		$hc_legacy_mode_enabled = esc_attr( get_option( 'hc_legacy_mode_enabled' ) );

		echo '<label for="hc_legacy_mode_enabled">';
		if ( $hc_legacy_mode_enabled == 'on' ) {
			echo '<input type="checkbox" name="hc_legacy_mode_enabled" checked="checked"/>';
		} else {
			echo '<input type="checkbox" name="hc_legacy_mode_enabled"/>';
		}
		_e( 'Enable legacy mode', 'hayona-cookies' );
		echo '</label>';

		echo '<p class="description">';
		echo __( 'Legacy mode enables you to switch back to the old banner, switching this off gives you the new banner and settings', 'hayona-cookies' );
		echo '</p>';
	}

	public function section_banner_callback() {
		echo '<p class="larger">';
			_e( "Informing your visitors about cookies on your website is an important part of conforming to the EU cookie law. Use the banner text to provide this information.", 'hayona-cookies' );
			echo ' ';
			printf( wp_kses(
				__( 'See the <a href="%1$s">documentation</a> for various examples in different situations.', 'hayona-cookies' ), array(  'a' => array( 'href' => array() ) ) ),
				esc_url('https://wordpress.org/plugins/hayona-cookies/installation/') ); 
		echo '</p>';
	}

	public function section_new_banner_callback() {
		echo '<p class="larger">';
			_e( "Informing your visitors about cookies on your website is an important part of conforming to the EU cookie law. Use the banner text to provide this information.", 'hayona-cookies' );
			echo ' ';
			printf( wp_kses(
				__( 'See the <a href="%1$s">documentation</a> for various examples in different situations.', 'hayona-cookies' ), array( 'a' => array( 'href' => array() ) ) ),
				esc_url( 'https://wordpress.org/plugins/hayona-cookies/installation/') );
		echo '</p>';
	}

	public function field_banner_text_callback() {
		echo '<textarea name="hc_banner_text" rows="5" cols="50"
				class="large-text" 
				placeholder="' . esc_attr__( "Place your banner text here...", 'hayona-cookies' ) . '">';
			echo esc_attr( get_option('hc_banner_text') );
		echo '</textarea>';
		echo '<p class="description">';
			printf( wp_kses(
				__( 'Need some examples? See <a href="%1$s">documentation</a>.', 'hayona-cookies' ), array(  'a' => array( 'href' => array() ) ) ),
				esc_url('https://wordpress.org/plugins/hayona-cookies/installation/') ); 
		echo '</p>';
	}

	public function field_implied_consent_enabled_callback() {
		$hc_implied_consent_enabled = esc_attr( get_option('hc_implied_consent_enabled') );
		echo '<label>';
		if( $hc_implied_consent_enabled == "on" ) {
			echo '<input type="checkbox" name="hc_implied_consent_enabled" checked="checked" />';
			_e( "Enable implied consent", 'hayona-cookies' ); 
		} else {
			echo '<input type="checkbox" name="hc_implied_consent_enabled" /> ';
			_e( "Enable implied consent", 'hayona-cookies' ); 
		} 
		echo '</label>';
		echo '<p class="description">';
			_e( "Implied consent means if a user clicks through to the next page, it will count as permission.", 'hayona-cookies' );  
			_e( "If this option is enabled you'll have to notify your site visitors about it throught the banner text.", 'hayona-cookies' ); 
		echo '</p>';
	}

	public function field_banner_color_scheme_callback() {
		$hc_color_scheme = esc_attr( get_option('hc_banner_color_scheme') );
		echo '<select name="hc_banner_color_scheme">';
		echo '<option value="dark" ';
			if( $hc_color_scheme == "dark" ) {
				echo 'selected="selected"';
			}
			echo '>';
			_e( "Dark color scheme", 'hayona-cookies' );
		echo '</option>';
		echo '<option value="light" ';
				if( $hc_color_scheme == "light" ) {
					echo 'selected="selected"';
				}
				echo '>';
			_e( "Light color scheme", 'hayona-cookies' ); 
		echo '</option>';
		echo '<option value="none" ';
				if( $hc_color_scheme == "none" ) {
					echo 'selected="selected"';
				}
				echo '>';
			_e( "No color scheme", 'hayona-cookies' ); 
		echo '</option>';
		echo '</select>';
		echo '<p class="description">';
			_e( "We'll add more in time. Select 'No color scheme' if you prefer to write your own CSS.", 'hayona-cookies' ); 
		echo '</p>';
	}

	public function field_banner_color_callback() {
		// TODO
	}

	public function field_banner_type_callback() {
		$hc_color_scheme = esc_attr( get_option('hc_banner_type') );

		echo '<select name="hc_banner_type">';
		echo '<option value="default" ';
			if( $hc_color_scheme == "default" ) {
				echo 'selected="selected"';
			}
			echo '>';
			_e( "Default banner", 'hayona-cookies' );
		echo '</option>';
		echo '<option value="cookiewall" ';
				if( $hc_color_scheme == "cookiewall" ) {
					echo 'selected="selected"';
				}
				echo '>';
			_e( "Cookie wall", 'hayona-cookies' ); 
		echo '</option>';
		echo '</select>';
		echo '<p class="description">';
			_e( "Default banner is recommended, because the website remains accessible from the first pageview.", 'hayona-cookies' ); 
			echo ' ';
			_e( "However, the default banner sometimes conflicts with styles from within a theme.", 'hayona-cookies' ); 
			echo ' ';
			_e( "In those cases, use the cookie wall.", 'hayona-cookies' ); 
		echo '</p>';
	}

	public function field_banner_text_button_yes_callback() {
		$hc_banner_text_button_yes = esc_attr( get_option( 'hc_banner_text_button_yes' ) );

		echo "<input type='text' name='hc_banner_text_button_yes' value='$hc_banner_text_button_yes'>";
	}

	public function field_banner_text_button_no_callback() {
		$hc_banner_text_button_no = esc_attr( get_option( 'hc_banner_text_button_no' ) );

		echo "<input type='text' name='hc_banner_text_button_no' value='$hc_banner_text_button_no'>";
	}

	public function field_banner_use_body_offset_callback() {
		$useBodyOffset = esc_attr( get_option( 'hc_banner_use_body_offset' ) );

		echo '<label>';

		if ( $useBodyOffset == 'on' ) {
			echo '<input type="checkbox" name="hc_banner_use_body_offset" checked="checked">';
			_e( 'Use a body offset for the banner.', 'hayona_cookies' );
		} else {
			echo '<input type="checkbox" name="hc_banner_use_body_offset">';
			_e( 'Use a body offset for the banner.', 'hayona-cookies' );
		}

		echo '</label>';
	}

	public function field_banner_offset_header_selector_callback() {
		$hc_offset_header_selector = esc_attr( get_option( 'hc_banner_offset_header_selector' ) );

		if ( !empty( $hc_offset_header_selector ) ) {
			echo "<input type='text' name='hc_banner_offset_header_selector' value='$hc_offset_header_selector'>";
		} else {
			echo '<input type="text" name="hc_banner_offset_header_selector" value="">';
		}
	}

	public function section_form_callback() {
		echo '<p class="larger">';
			_e( "Form settings section placeholder text.", 'hayona-cookies' );
		echo '</p>';
	}

	public function field_form_placement_before_callback() {
		$hc_form_placement_before = esc_attr( get_option( 'hc_form_placement_before' ) );

		if ( !empty( $hc_form_placement_before ) ) {
			echo "<input type='text' name='hc_form_placement_before' value='$hc_form_placement_before'>";
		} else {
			echo '<input type="text" name="hc_form_placement_before" value="">';
		}

		echo '<p>';
		_e( 'The element to place the consent form above', 'hayona-cookies' );
		echo '</p>';
	}

	public function field_form_head_text_callback() {
		$hc_form_head_text = esc_attr( get_option( 'hc_form_head_text' ) );

		if ( !empty( $hc_form_head_text ) ) {
			echo "<input type='text' name='hc_form_head_text' value='$hc_form_head_text'>";
		} else {
			echo '<input type="text" name="hc_form_head_text" value="">';
		}

		echo '<p>';
		_e( 'The text for the consent form header.', 'hayona-cookies' );
		echo '</p>';
	}

	public function field_form_subtitle_text_callback() {
		$hc_form_subtitle_text = esc_attr( get_option( 'hc_form_subtitle_text' ) );

		if ( !empty( $hc_form_subtitle_text ) ) {
			echo "<input type='text' name='hc_form_subtitle_text' value='$hc_form_subtitle_text'>";
		} else {
			echo '<input type="text" name="hc_form_subtitle_text" value="">';
		}

		echo '<p>';
		_e( 'The text for the consent form subtitle', 'hayona-cookies' );
		echo '</p>';
	}

	public function field_form_option_one_button_text_callback() {
		$hc_form_option_one_button_text = esc_attr( get_option( 'hc_form_option_one_button_text' ) );

		if ( !empty( $hc_form_option_one_button_text ) ) {
			echo "<input type='text' name='hc_form_option_one_button_text' value='$hc_form_option_one_button_text'>";
		} else {
			echo '<input type="text" name="hc_form_option_one_button_text" value="">';
		}
	}

	public function field_form_option_two_button_text_callback() {
		$hc_form_option_two_button_text = esc_attr( get_option( 'hc_form_option_two_button_text' ) );

		if ( !empty( $hc_form_option_two_button_text ) ) {
			echo "<input type='text' name='hc_form_option_two_button_text' value='$hc_form_option_two_button_text'>";
		} else {
			echo '<input type="text" name="hc_form_option_two_button_text" value="">';
		}
	}

	public function field_form_allowed_text_callback() {
		$hc_form_allowed_text = esc_attr( get_option( 'hc_form_allowed_text' ) );

		if ( !empty( $hc_form_allowed_text ) ) {
			echo "<input type='text' name='hc_form_allowed_text' value='$hc_form_allowed_text'>";
		} else {
			echo "<input type='text' name='hc_form_allowed_text' value=''>";
		}

		echo '<p>';
		_e( 'Text to show next to the allowed cookies.', 'hayona-cookies' );
		echo '</p>';
	}

	public function field_form_disallowed_text_callback() {
		$hc_form_disallowed_text = esc_attr( get_option( 'hc_form_disallowed_text' ) );

		if ( !empty( $hc_form_disallowed_text ) ) {
			echo "<input type='text' name='hc_form_disallowed_text' value='$hc_form_disallowed_text'>";
		} else {
			echo "<input type='text' name='hc_form_disallowed_text' value=''>";
		}

		echo '<p>';
		_e( 'Text to show next to the disallowed cookies.', 'hayona-cookies' );
		echo '</p>';
	}

	public function section_cookie_callback() {
		echo '<p class="larger">';
			_e( "List all the cookies you use on your website. Place every cookie on a new line.", 'hayona-cookies' );
			echo ' ';
			_e( "We distinguish between two different kinds of cookies.", 'hayona-cookies' );
			echo ' ';
			_e( "The first category contains cookies that don't require permission (usually functional and non-PII cookies).", 'hayona-cookies' );
			echo ' ';
			_e( "The second category contains cookies that do require permission. These are cookies used for tracking, profiling, advertising and cookies that store PII.", 'hayona-cookies' );
		echo '</p>';
	}

	public function field_cookielist_consent_not_required_callback() {
		echo '<textarea name="hc_cookielist_consent_not_required" 
					rows="5" cols="50" class="large-text" 
					placeholder="' . esc_attr__( 'List your cookies one by one...', 'hayona-cookies' ) . '">';
			echo esc_attr( get_option('hc_cookielist_consent_not_required') );
		echo '</textarea>';
		echo '<p class="description">';
			_e( "List your cookies one by one. Place each cookie on a new line.", 'hayona-cookies' );
			printf( wp_kses(
				__( 'Need some examples? See <a href="%1$s">documentation</a>.', 'hayona-cookies' ), array(  'a' => array( 'href' => array() ) ) ), 
				esc_url('https://wordpress.org/plugins/hayona-cookies/') );  
		echo '</p>';
	}

	public function field_cookielist_consent_required_callback() {
		echo '<textarea name="hc_cookielist_consent_required"
					rows="5" cols="50" class="large-text" 
					placeholder="' . esc_attr__( 'List your cookies one by one...', 'hayona-cookies' ) . '">'; 
			echo esc_attr( get_option('hc_cookielist_consent_required') );
		echo '</textarea>';
		echo '<p class="description">';
			_e( "List your cookies one by one. Place each cookie on a new line.", 'hayona-cookies' ); 
			printf( wp_kses( 
				__( 'Need some examples? See <a href="%1$s">documentation</a>.', 'hayona-cookies' ), array(  'a' => array( 'href' => array() ) ) ),
				esc_url('https://wordpress.org/plugins/hayona-cookies/') );  
		echo '</p>';
	}

	public function field_cookie_expiration_callback() {
		echo '<input name="hc_cookie_expiration" 
					type="number"
					class="small-text" 
					value="' . esc_attr( get_option( 'hc_cookie_expiration', 365 ) ) . '">';
		echo '<p class="description">';
			_e( "How long would you like to remember the cookie consent of your users?  Default is 365 days.", 'hayona-cookies' ); 
		echo '</p>';
	}

	public function field_reset_consent_timestamp_callback() {
		echo '<label>';
			echo '<input type="checkbox" name="hc_reset_consent_timestamp" /> ';
			_e( "Reset permissions", 'hayona-cookies' );
		echo '</label>';
		echo '<input type="hidden" name="hc_consent_timestamp" value="' . esc_attr( get_option('hc_consent_timestamp') ) . '">';
		echo '<p class="description">';
			_e( "Did you just change anything to the cookies on your site? This means you'll need to ask every visitor for their consent again, even if they gave consent before.", 'hayona-cookies' );
			echo ' ';
			_e( "Check this box and press save to do this.", 'hayona-cookies' ); 
			echo '<strong style="color: red; display: block; padding-top: 10px;">';
				_e( "Warning: this option will delete all cookie consents. ", 'hayona-cookies' );
			echo '</strong>';
		echo '</p>';
	}

	public function options_page_callback() {

		$invalidate_consent = esc_attr( get_option('hc_reset_consent_timestamp') );
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

		// Invalidate consent if 'hc_reset_consent_timestamp' has been selected
		if( $invalidate_consent == "on" ) {
			$this->invalidate_consent();
			update_option( 'hc_reset_consent_timestamp', "" );
		}

		$legacy_mode_enabled = esc_attr( get_option( 'hc_legacy_mode_enabled' ) );

		// The HTML of our options page
		echo '<div class="wrap"><div class="hc-admin__header"><h1>' . __( 'Hayona Cookie Consent', 'hayona-cookies') . '</h1></div>';
		echo '<h2 class="nav-tab-wrapper">';
			echo '<a href="?page=hayona-cookies&tab=general" class="nav-tab';
					echo $active_tab == 'general' ? ' nav-tab-active' : '';
					echo '">' . __( 'General settings', 'hayona-cookies' ) . '</a>';
			echo '<a href="?page=hayona-cookies&tab=banner" class="nav-tab';
				echo $active_tab == 'banner' ? ' nav-tab-active' : '';
				echo '">' . __( 'Banner settings', 'hayona-cookies' ) . '</a>';
			if ( $legacy_mode_enabled == false ) {
				echo '<a href="?page=hayona-cookies&tab=form" class="nav-tab';
					echo $active_tab == 'form' ? ' nav-tab-active' : '';
					echo '">' . __( 'Form settings', 'hayona-cookies' ) . '</a>';
			}
			echo '<a href="?page=hayona-cookies&tab=cookies" class="nav-tab';
					echo $active_tab == 'cookies' ? ' nav-tab-active' : '';
					echo '">' . __( 'Cookie settings', 'hayona-cookies' ) . '</a>';
		echo '</h2>';
		echo '<form method="post" action="options.php">';

		if( $active_tab == 'general' ) {
			settings_fields( 'hc_general' ); 
			do_settings_sections( 'hc_general' );
		} elseif( $active_tab == 'banner' ) {
			settings_fields( 'hc_banner' ); 
			do_settings_sections( 'hc_banner' );
		} elseif( $legacy_mode_enabled == false && $active_tab == 'form' ) {
			settings_fields( 'hc_form' );
			do_settings_sections( 'hc_form' );
		} elseif( $active_tab == 'cookies' ) {
			settings_fields( 'hc_cookies' ); 
			do_settings_sections( 'hc_cookies' );
		}

		submit_button(); 

		echo '<div class="hc-admin__footer">
				<a href="https://wordpress.org/plugins/hayona-cookies/">' .
					__( "Documentation", 'hayona-cookies' ) .
				'</a> —
				<a href="https://wordpress.org/plugins/hayona-cookies/installation">' .
					__( "Installation guide", 'hayona-cookies' ) .
				'</a> —
				<a href="http://www.hayona.com">' . 
				__( "Plugin by Hayona", 'hayona-cookies' ) . 
				'</a>
			</div>';

		echo '</form>';
		echo '</div>';
	}
}

// Instantiate our class
$Hayona_Cookies = Hayona_Cookies::getInstance();
