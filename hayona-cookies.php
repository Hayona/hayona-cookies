<?php
/*
Plugin Name: Hayona Cookie Consent
Plugin URI: 
Description: Comply with EU cookie law: tell your visitors how you use cookies, obtain their consent and give them some control.
Author: Hayona
Version: 1.0.5
Author URI: http://www.hayona.nl
License: GPLv2
Domain Path: /languages
Text Domain: hayona-cookies

Hayona Cookie Consent is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Hayona Cookie Consent is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Hayona Cookie Consent. If not, see <http://www.gnu.org/licenses/>.
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Hayona_Cookies {

	public function __construct() {

		$is_enabled = esc_attr( get_option( 'hc_is_enabled' ) );

		// On activation, set a cookie timestamp
		register_activation_hook( __FILE__, array( $this, 'reset_cookie_timestamp' ) );

		// Load translations
		add_action( 'plugins_loaded', array( $this, 'load_translations' ) );

		if ( is_admin() ){ 

			add_action( 'admin_init', array( $this, 'admin_register_settings' ) );
			add_action( 'admin_head', array( $this, 'admin_assets' ) );
			add_action( 'admin_menu', array( $this, 'admin_options_page' ) );

			if( $is_enabled != "on" ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_disabled' ) ); 
			}

		} 

		else {

			if( $is_enabled == "on" ) {
				add_action( 'wp_footer', array( $this, 'hc_banner' ), 100 );
				add_action( 'wp_enqueue_scripts', array( $this, 'hc_assets' ) );
				add_filter( 'the_content', array( $this, 'hc_privacy_settings' ) );
			}
		}
	}


	/**
	 * Load translations
	 */
	public function load_translations() {
		load_plugin_textdomain('hayona-cookies', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Reset cookie timestamp
	 *
	 * @description: Invalidates all permissions
	 */
	public function reset_cookie_timestamp() {
		$timestamp = intval( microtime( true ) * 1000 );
		update_option( 'hc_consent_timestamp', $timestamp );
	}


	/**
	 * Get cookies
	 * 
	 * @description: Returns nested array with all cookies that the user has specified
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
	private function get_color_scheme() {
		// Get color scheme
		$color_scheme = esc_attr( get_option( 'hc_banner_color_scheme' ) );
		switch ($color_scheme) {
			case 'light':
				$color_scheme_class = 'hc-styling hc-styling--light';
				break;

			case 'none':
				$color_scheme_class = '';
				break;
			
			default:
				$color_scheme_class = 'hc-styling hc-styling--dark';
				break;
		}
		return $color_scheme_class;
	}



	/**
	 * HC Assets
	 *
	 * @description: Enqueue front-end assets
	 */
	public function hc_assets() {
		wp_enqueue_style( 'hayona-cookies', plugins_url( 'assets/css/min/style.css', __FILE__ ), array(), '1.0.5', 'screen' );
		wp_enqueue_script( 'hayona-cookies', plugins_url( 'assets/js/min/main-min.js', __FILE__ ), array( 'jquery' ), null, true );
	}


	/**
	 * HC Banner
	 *
	 * @description: Include banner in page
	 */
	public function hc_banner() {

		// Get permission timestamp
		$permission_timestamp = esc_attr( get_option('hc_consent_timestamp') );

		// Get color scheme
		$color_scheme = $this->get_color_scheme();

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
		$banner_markup = '<div class="hc-banner ' . $color_scheme . '">
						<div class="hc-banner__body">' . esc_attr( get_option('hc_banner_text') ) . '</div>
						<ul class="hc-toolbar">
							<li><a class="hc-button accept-cookies" href="#">' . __( "All right, close", 'hayona-cookies' ) . '</a></li>
							<li><a class="hc-dimmed" href="' . get_permalink( esc_attr( get_option( 'hc_privacy_statement_url' ) ) ) . '"> ' . __( "Change your settings", 'hayona-cookies' ) . ' </a></li>
						</ul>
					</div>';
		$banner_script = '<script type="text/javascript">
						jQuery(document).ready( hayonaCookies.init( {
							timestamp: ' . $permission_timestamp . ',
							isSettingsPage: ' . $is_settings_page . ', 
							implicitConsentEnabled: ' . $implied_consent_enabled . ',
							cookieExpiration: ' . $cookie_expiration . '
						} ) );
					</script>';

		$banner_markup = apply_filters( 'hc_banner_markup', $banner_markup );
		$banner_script = apply_filters( 'hc_banner_script', $banner_script );

		// Print banner to page
		echo $banner_markup . $banner_script;
	}


	/**
	 * HC Privacy Settings
	 *
	 * @description: Output privacy settings to the right page
	 */
	public function hc_privacy_settings( $content ) {

		$settings_page_id = esc_attr( get_option('hc_privacy_statement_url') );
		$current_page_id = get_the_id();

		if( $settings_page_id == $current_page_id ) {

			// Get color scheme
			$color_scheme = $this->get_color_scheme();

			// Get all cookies
			$cookies = $this->get_cookies();

			// Define table content
			$table_content = "";

			// These cookies do not require permission
			foreach( $cookies["not_required"] as $cookie ) {
				$table_content .= '<tr>
										<th>' . $cookie . '</th>
										<td>✓ ' . __( 'Allowed', 'hayona-cookies' ) . '</td>
										<td>✓ ' . __( 'Allowed', 'hayona-cookies' ) . '</td>
									</tr>';
			}

			// These cookies DO require permission
			foreach( $cookies["required"] as $cookie ) {
				$table_content .= '<tr>
										<th>' . $cookie . '</th>
										<td>✓ ' . __( 'Allowed', 'hayona-cookies' ) . '</td>
										<td>✘ ' . __( 'Not allowed', 'hayona-cookies' ) . '</td>
									</tr>';
			}

			// Privacy settings HTML code
			$privacy_settings = '<div class="hc-settings ' . $color_scheme . '">
	<div class="hc-settings__header">
		<span class="hc-h2">' . __( 'Cookie preferences', 'hayona-cookies' ) . '</span>
		<p>
			' . __( 'Please select which cookies you want to accept from this website.', 'hayona-cookies' ) . '
		</p>
	</div>
	<table class="hc-table">
		<colgroup>
			<col>
			<col>
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="hc-empty-cell"></th>
				<th>
					' . __( 'Option 1: Allow all cookies', 'hayona-cookies' ) . '
				</th>
				<th>
					' . __( 'Option 2: Accept only functional and non privacy sensitive cookies (no PII).', 'hayona-cookies' ) . '
				</th>
			</tr>
		</thead>
		<tbody>
			' . $table_content . '
		</tbody>
		<tfoot>
			<tr>
				<th>' . __( 'Your choice', 'hayona-cookies' ) . ':</th>
				<td>
					<a class="hc-button hc-button--grey accept-cookies" href="#"><span></span>' . __( 'Allow all cookies', 'hayona-cookies' ) . '</a>
				</td>
				<td>
					<a class="hc-button hc-button--grey reject-cookies" href="#"><span></span>' . __( 'Accept only functional and non privacy sensitive cookies (no PII)', 'hayona-cookies' ) . '</a>
				</td>
			</tr>
		</tfoot>
	</table>
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
		wp_enqueue_style( 'hayona-cookies', plugins_url( 'assets/css/min/admin.css', __FILE__ ) );
	}


	/**
	 * Admin notice disabled
	 *
	 * @description: Notify user if the banner is disabled
	 */
	public function admin_notice_disabled() {
		$class = "notice is-dismissible updated";
		$message = sprintf( wp_kses( __('The cookie banner is not visible yet. Go to the <a href="%s">plugin settings page</a> to review the settings and enable the plugin.', 'hayona-cookies'), 
				array(  'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'options-general.php?page=hayona-cookies' ) ) );

		echo 	"<div class=\"$class\"> 
					<p>$message</p>
					<button type=\"button\" class=\"notice-dismiss\">
						<span class=\"screen-reader-text\">" . __( "Dismiss this notice.", 'hayona-cookies' ) . "</span>
					</button>
				</div>"; 
	}


	/**
	 * Admin register settings
	 *
	 * @description: Register all plugin settings
	 */
	public function admin_register_settings() { 
		register_setting( 'hayona-cookies', 'hc_privacy_statement_url' );
		register_setting( 'hayona-cookies', 'hc_is_enabled' );
		register_setting( 'hayona-cookies', 'hc_implied_consent_enabled' );
		register_setting( 'hayona-cookies', 'hc_cookie_expiration' );
		register_setting( 'hayona-cookies', 'hc_banner_text' );
		register_setting( 'hayona-cookies', 'hc_banner_color_scheme' );
		register_setting( 'hayona-cookies', 'hc_cookielist_consent_required' );
		register_setting( 'hayona-cookies', 'hc_cookielist_consent_not_required' );
		register_setting( 'hayona-cookies', 'hc_reset_consent_timestamp' );
		register_setting( 'hayona-cookies', 'hc_consent_timestamp' );


		/** 
		 * General settings
		 */
		add_settings_section( 'hc_section_general', __('General settings', 'hayona-cookies'),	'hc_section_general_intro', 'hayona-cookies-general' );
		add_settings_field('hc_privacy_statement_url', __( 'Privacy statement', 'hayona-cookies' ), 'hc_privacy_statement_url', 'hayona-cookies-general', 'hc_section_general');
		add_settings_field('hc_is_enabled', __( 'Enable plugin', 'hayona-cookies' ), 'hc_is_enabled', 'hayona-cookies-general', 'hc_section_general');

		function hc_section_general_intro() {
			_e( "You're almost there! Just a couple more things to do", 'hayona-cookies' );
			echo '<ol>';
			echo '<li>' . 
					sprintf( wp_kses(
					__( 'Install Google Tag Manager on your website (see <a href="%1$s">documentation</a>)', 'hayona-cookies' ), array(  'a' 
			=> array( 'href' => array() ) ) ),
					esc_url('https://wordpress.org/plugins/hayona-cookies/installation/') ) .
					'</li>';
			echo '<li>' . __( "Review the settings below", 'hayona-cookies' ) . '</li>';
			echo '<li>' . __( "Enable the plugin", 'hayona-cookies' ) . '</li>';
			echo '</ol>';
		}

		function hc_privacy_statement_url() {
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

		function hc_is_enabled() {
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


		/** 
		 * Banner settings
		 */
		add_settings_section( 'hc_section_banner', __('Banner settings', 'hayona-cookies'),	'hc_section_banner_intro', 'hayona-cookies-banner' );
		add_settings_field('hc_banner_text', __( 'Banner text', 'hayona-cookies' ), 'hc_banner_text', 'hayona-cookies-banner', 'hc_section_banner');
		add_settings_field('hc_implied_consent_enabled', __( 'Implied consent', 'hayona-cookies' ), 'hc_implied_consent_enabled', 'hayona-cookies-banner', 'hc_section_banner');
		add_settings_field('hc_banner_color_scheme', __( 'Color scheme', 'hayona-cookies' ), 'hc_banner_color_scheme', 'hayona-cookies-banner', 'hc_section_banner');

		function hc_section_banner_intro() {
			echo '<p>';
				_e( "Informing your visitors about cookies on your website is an important part of conforming to the EU cookie law. Use the banner text to provide this information.", 'hayona-cookies' );
				echo ' ';
				printf( wp_kses(
					__( 'See the <a href="%1$s">documentation</a> for various examples in different situations.', 'hayona-cookies' ), array(  'a' => array( 'href' => array() ) ) ),
					esc_url('https://wordpress.org/plugins/hayona-cookies/installation/') ); 
			echo '</p>';
		}

		function hc_banner_text() {
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

		function hc_implied_consent_enabled() {
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

		function hc_banner_color_scheme() {
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


		/** 
		 * Cookie settings
		 */
		add_settings_section( 'hc_section_cookie', __('Cookie settings', 'hayona-cookies'),	'hc_section_cookie_intro', 'hayona-cookies-cookies' );
		add_settings_field('hc_cookielist_consent_not_required', __( 'No permission required', 'hayona-cookies' ), 'hc_cookielist_consent_not_required', 'hayona-cookies-cookies', 'hc_section_cookie');
		add_settings_field('hc_cookielist_consent_required', __( 'No permission required', 'hayona-cookies' ), 'hc_cookielist_consent_required', 'hayona-cookies-cookies', 'hc_section_cookie');
		add_settings_field('hc_cookie_expiration', __( 'Cookie expiration time', 'hayona-cookies' ), 'hc_cookie_expiration', 'hayona-cookies-cookies', 'hc_section_cookie');
		add_settings_field('hc_reset_consent_timestamp', __( 'Reset permissions', 'hayona-cookies' ), 'hc_reset_consent_timestamp', 'hayona-cookies-cookies', 'hc_section_cookie');

		function hc_section_cookie_intro() {
			echo '<p>';
				_e( "List all the cookies you use on your website. Place every cookie on a new line.", 'hayona-cookies' );
			echo '</p>';
			echo '<p>';
				_e( "We distinguish between two different kinds of cookies.", 'hayona-cookies' );
				_e( "The first category contains cookies that don't require permission (usually functional and non-PII cookies).", 'hayona-cookies' );
				_e( "The second category contains cookies that do require permission. These are cookies used for tracking, profiling, advertising and cookies that store PII.", 'hayona-cookies' );
			echo '</p>';
		}

		function hc_cookielist_consent_not_required() {
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

		function hc_cookielist_consent_required() {
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

		function hc_cookie_expiration() {
			echo '<input name="hc_cookie_expiration" 
						type="number"
						class="small-text" 
						value="' . esc_attr( get_option( 'hc_cookie_expiration', 365 ) ) . '">';
			echo '<p class="description">';
				_e( "How long would you like to remember the cookie consent of your users?  Default is 365 days.", 'hayona-cookies' ); 
			echo '</p>';
		}

		function hc_reset_consent_timestamp() {
			echo '<label>';
				echo '<input type="checkbox" name="hc_reset_consent_timestamp" /> ';
				_e( "Reset permissions", 'hayona-cookies' );
			echo '</label>';
			echo '<input type="hidden" name="hc_consent_timestamp" value="' . esc_attr( get_option('hc_consent_timestamp') ) . '">';
			echo '<p class="description">';
				_e( "Did you just change anything to the cookies on your site? This means you'll need to ask every visitor for their permission again, even if they gave permission before.", 'hayona-cookies' );
				_e( "Check this box and press save to do this.", 'hayona-cookies' ); 
				echo '<strong style="color: red; display: block; padding-top: 10px;">';
					_e( "Warning: this option will delete all cookie consents. ", 'hayona-cookies' );
				echo '</strong>';
			echo '</p>';
		}
	}


	/**
	 * Admin options page
	 *
	 * @description: Load options page
	 */
	public function admin_options_page() {
		add_options_page( 'Hayona Cookie Consent', 'Hayona Cookie Consent', 'manage_options', 'hayona-cookies', array( $this, 'load_options_page' ) ); 
	}


	/**
	 * Load options page
	 *
	 * @description: Options page markup
	 */
	public function load_options_page() {

		/**
		 * The options page is reloaded after the options have been saved.
		 * That's why, every time the options page loads, we check if 'reset
		 * permissions' has been selected. In that case we reset the cookie 
		 * timestamp to a new random number. This way everyone will be asked 
		 * again for permission to store cookies.
		 */
		$reset_cookie_timestamp = esc_attr( get_option('hc_reset_consent_timestamp') );
		if( $reset_cookie_timestamp == "on" ) {

			// Generate a random cookie timestamp
			$this->reset_cookie_timestamp();

			update_option( 'hc_reset_consent_timestamp', "" );
		}

		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'hayona-cookies-general';

		// The HTML of our options page
		echo '<div class="wrap"><div class="hc-admin__header"><h1>' . __( 'Hayona Cookie Consent', 'hayona-cookies') . '</h1></div>';
		echo '<h2 class="nav-tab-wrapper">';
			echo '<a href="?page=hayona-cookies&tab=hayona-cookies-general" class="nav-tab';
					echo $active_tab == 'hayona-cookies-general' ? ' nav-tab-active' : '';
					echo '">' . __( 'General settings', 'hayona-cookies' ) . '</a>';
			echo '<a href="?page=hayona-cookies&tab=hayona-cookies-banner" class="nav-tab';
					echo $active_tab == 'hayona-cookies-banner' ? ' nav-tab-active' : '';
					echo '">' . __( 'Banner settings', 'hayona-cookies' ) . '</a>';
			echo '<a href="?page=hayona-cookies&tab=hayona-cookies-cookies" class="nav-tab';
					echo $active_tab == 'hayona-cookies-cookies' ? ' nav-tab-active' : '';
					echo '">' . __( 'Cookie settings', 'hayona-cookies' ) . '</a>';
		echo '</h2>';
		echo '<form method="post" action="options.php">';

		if( $active_tab == 'hayona-cookies-general' ) {
			settings_fields( 'hayona-cookies-general' ); 
			do_settings_sections( 'hayona-cookies-general' );
		} elseif( $active_tab == 'hayona-cookies-banner' ) {
			settings_fields( 'hayona-cookies-banner' ); 
			do_settings_sections( 'hayona-cookies-banner' );
		} elseif( $active_tab == 'hayona-cookies-cookies' ) {
			settings_fields( 'hayona-cookies-cookies' ); 
			do_settings_sections( 'hayona-cookies-cookies' );
		}


		submit_button(); 

		echo '<div class="hc-admin__sidebar">';
			echo '<div class="hc-box">';
				echo '<h2>';
					_e( "About this plugin", 'hayona-cookies' ); 
				echo '</h2>';
				echo '<ul>';
					echo '<li><a href="https://wordpress.org/plugins/hayona-cookies/">';
						_e( "Plugin info", 'hayona-cookies' );
					echo '</a></li>';
				echo '</ul>';
				echo '<h3>';
					_e( "Documentation", 'hayona-cookies' );
				echo '</h3>';
				echo '<ul>';
					echo '<li><a href="https://wordpress.org/plugins/hayona-cookies/">';
						_e( "Read documentation", 'hayona-cookies' ); 
					echo '</a></li>';
				echo '</ul>';
			echo '</div>';

		echo '</div>';

		echo '</form>';
		echo '</div>';
	}

}

new Hayona_Cookies();
