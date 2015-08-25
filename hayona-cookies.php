<?php
/*
Plugin Name: Hayona Cookies
Plugin URI: 
Description: Comply with EU cookie law: tell your visitors how you use cookies, obtain their consent and give them some control.
Author: Hayona
Version: 1.0
Author URI: http://www.hayona.nl
License: GPLv2
Domain Path: /languages
Text Domain: hayona-cookies

Hayona Cookies is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Hayona Cookies is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Hayona Cookies. If not, see <http://www.gnu.org/licenses/>.
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Hayona_Cookies {

	public function __construct() {

		// On activation, set a cookie token
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// Load translations
		add_action( 'plugins_loaded', array( $this, 'load_translations' ) );

		// Is the plugin enabled via the settings page??
		$is_enabled = esc_attr( get_option('is_enabled') );

		/*
		 * Admin functions
		 */
		if ( is_admin() ){ 

			add_action( 'admin_init', array( $this, 'admin_register_settings' ) );
			add_action( 'admin_head', array( $this, 'admin_assets' ) );
			add_action( 'admin_menu', array( $this, 'admin_options_page' ) );

			if( $is_enabled != "on" ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_disabled' ) ); 
			}

		} 

		/*
		 * Frontend functions
		 */
		else {

			if( $is_enabled == "on" ) {
				add_action( 'wp_footer', array( $this, 'hc_banner' ), 100 );
				add_action( 'wp_head', array( $this, 'hc_gtm_script' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'hc_assets' ) );
				add_filter( 'the_content', array( $this, 'hc_privacy_settings' ) );
			}
		}
	}


	// Generate and store a cookie token on activation
	function activate() {
	    $this->reset_cookie_token();
	}

	// Load translations
	public function load_translations() {
		load_plugin_textdomain('hayona-cookies', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	// Add options page (actual options page is at the bottom of this file)
	public function admin_options_page() {
		add_options_page( 'Hayona Cookies', 'Hayona Cookies', 'manage_options', 'hayona-cookies', array( $this, 'load_options_page' ) ); 
	}

	// Insert front-end assets
	public function hc_assets() {
		wp_enqueue_style( 'hayona-cookies', plugins_url( 'css/style.css', __FILE__ ) );
		wp_enqueue_script( 'hayona-cookies', plugins_url( 'js/main.js', __FILE__ ), array( 'jquery' ), null, true );
	}

	// Insert backend assets
	public function admin_assets() {
		wp_enqueue_style( 'hayona-cookies', plugins_url( 'css/admin.css', __FILE__ ) );
	}


	/*
	 * Reset cookie token
	 *
	 * The cookie token is stored in a cookie, together with the permission 
	 * settings of the users. If the cookies on a site change, the cookie token
	 * changes also. This will invalidate the permission settings. 
	 */
	private function reset_cookie_token() {
		$random_number = rand( 1000000, 9999999 );
		update_option( 'cookie_token', $random_number );
	}


	/*
	 * If the plugin is disabled, display a little notification
	 */
	function admin_notice_disabled() {
		$class = "notice is-dismissible updated";
		$message = sprintf( 
				__('The cookie banner is not visible yet. Go to the %1$splugin page%2$s to review the settings and enable the plugin.', 'hayona-cookies'),
				'<a href="' . admin_url( 'options-general.php?page=hayona-cookies' ) . '">',
				'</a>' );
	        echo"<div class=\"$class\"> <p>$message</p><button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button></div>"; 
	} 


	// Insert google tag manager data layer script
	public function hc_gtm_script() {
		$html = "\r\n<!-- Hayona Cookies plugin -->\r\n<script type=\"text/javascript\">dataLayer = [];</script>\r\n<!-- End Hayona Cookies plugin -->\r\n";
		echo $html;
	}


	/*
	 * Get color scheme class names. These are used in 
	 *  - Cookie banner
	 *  - Settings form
	 */
	public function get_color_scheme() {
		// Get color scheme
		$color_scheme = esc_attr( get_option( 'banner_color_scheme' ) );
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


	// Generate banner HTML and insert it to the footer of every page
	public function hc_banner() {

		// Get permission token
		$permission_token = esc_attr( get_option('cookie_token') );

		// Get color scheme
		$color_scheme = $this->get_color_scheme();

		// Is implied consent enabled?
		$implied_consent_enabled = esc_attr( get_option( 'implied_consent_enabled' ) ); 

		if( $implied_consent_enabled == "on" ) {
			$implied_consent_enabled = "true";
		} else {
			$implied_consent_enabled = "false";
		}

		// Are we on a settings page?
		$settings_page_id = esc_attr( get_option('privacy_statement_url') );
		$current_page_id = get_the_id();
		if( $settings_page_id == $current_page_id ) {
			$is_settings_page = "true";
		} else {
			$is_settings_page = "false";
		}

		// Load banner html
		$banner = '<div class="hc-banner ' . $color_scheme . '">
						<div class="hc-banner__body">' . esc_attr( get_option('banner_text') ) . '</div>
						<ul class="hc-toolbar">
							<li><a class="hc-button accept-cookies" href="#">Oké, sluiten</a></li>
							<li><a class="hc-dimmed" href="' . get_permalink( esc_attr( get_option('privacy_statement_url') ) ) . '">Instellingen wijzigen</a></li>
						</ul>
					</div>';
		$banner .= '<script type="text/javascript">
						jQuery(document).ready( hayonaCookies.init( ' . $permission_token . ', ' . $is_settings_page . ', ' . $implied_consent_enabled . ' ) );
					</script>';

		// Print banner to page
		echo $banner;
	}


	/*
	 * Get cookie lists
	 * 
	 * @return nested array with all cookies that the user has specified
	 */
	public function get_cookies() {

		$permission_needed = esc_attr( get_option('cookielist_permission_not_required') );
		$permission_not_needed = esc_attr( get_option('cookielist_permission_required') );

		$get_cookies[0] 		= explode("\n", $permission_needed );
		$get_cookies[1] 		= explode("\n", $permission_not_needed );

		return $get_cookies;
	}


	/*
	 * Insert cookie settings on privacy statement page
	 */
	public function hc_privacy_settings( $content ) {

		$settings_page_id = esc_attr( get_option('privacy_statement_url') );
		$current_page_id = get_the_id();

		if( $settings_page_id == $current_page_id ) {

			// Get color scheme
			$color_scheme = $this->get_color_scheme();

			// Get all cookies
			$cookies = $this->get_cookies();

			// Define table content
			$table_content = "";

			// Prepare table rows
			// These cookies do not require permission
			foreach( $cookies[0] as $cookie ) {
				$table_content .= '<tr>
										<th>' . $cookie . '</th>
										<td>✓ ' . __( 'Allowed', 'hayona-cookies' ) . '</td>
										<td>✓ ' . __( 'Allowed', 'hayona-cookies' ) . '</td>
									</tr>';
			}

			// These cookies DO require permission
			foreach( $cookies[1] as $cookie ) {
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
			$content = $privacy_settings . $content;
		} 

		return $content;
	}


	// Register options page settings
	public function admin_register_settings() { 
		register_setting( 'hayona-cookies', 'privacy_statement_url' );
		register_setting( 'hayona-cookies', 'is_enabled' );
		register_setting( 'hayona-cookies', 'implied_consent_enabled' );
		register_setting( 'hayona-cookies', 'banner_text' );
		register_setting( 'hayona-cookies', 'banner_color_scheme' );
		register_setting( 'hayona-cookies', 'cookielist_permission_required' );
		register_setting( 'hayona-cookies', 'cookielist_permission_not_required' );
		register_setting( 'hayona-cookies', 'reset_cookie_token' );
		register_setting( 'hayona-cookies', 'cookie_token' );
	}

	// The actual options page
	public function load_options_page() {

		/*
		 * The options page is reloaded after the options have been saved.
		 * That's why, every time the options page loads, we check if 'reset
		 * permissions' has been selected. In that case we reset the cookie 
		 * token to a new random number. This way everyone will be asked 
		 * again for permission to store cookies.
		 */
		$reset_cookie_token = esc_attr( get_option('reset_cookie_token') );
		if( $reset_cookie_token == "on" ) {

			// Generate a random cookie token
			$this->reset_cookie_token();

			update_option( 'reset_cookie_token', "" );
		}

		// The HTML of our options page
		echo '<div class="wrap"><div class="hc-admin__header"><h2>' . __( 'Hayona Cookies', 'hayona-cookies') . '</h2></div>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'hayona-cookies' ); ?>
				
<div class="hc-admin__settings">
	<div class="hc-box">
		<h3><?php _e( 'General settings', 'hayona-cookies' ); ?></h3>
		<p>
			<?php _e( 'You\'re almost there! Just a couple more things to do', 'hayona-cookies' ); ?>:
		</p>
		<ol>
			<li>
				<?php printf( 
					__( 'Install Google Tag Manager on your website (see %1$sdocumentation%2$s)', 'hayona-cookies' ), 
					'<a href="#">', 
					'</a>' ); 
				?>
			</li>
			<li><?php _e( 'Review the settings below', 'hayona-cookies' ); ?></li>
			<li><?php _e( 'Enable the plugin', 'hayona-cookies' ); ?></li>
		</ol>
		<table class="form-table hc-form-table">
			<tr>
				<th>
					<?php _e( 'Privacy statement', 'hayona-cookies' ); ?>
				</th>
				<td>
					<select name="privacy_statement_url">
					<?php 
						$hc_pages = get_pages();
						$hc_current_page = esc_attr( get_option('privacy_statement_url') );
						foreach( $hc_pages as $page ) :
							echo $page->ID;
							echo $page->post_title;
					 ?>
						<?php if( $page->ID == $hc_current_page ) : ?>
							<option value="<?php echo $page->ID; ?>" selected="selected"><?php echo $page->post_title; ?></option>
						<?php else: ?>
							<option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
						<?php endif; ?>

					<?php endforeach; ?>
					</select>
					<p class="description">
						<?php _e( 'Please select the page that contains your privacy statement. A small cookie preferences form will be placed at the top of this page. This way your visitors can change their privacy settings on any given moment.', 'hayona-cookies' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<?php _e( 'Enable plugin', 'hayona-cookies' ); ?>
				</th>
				<td>
					<?php 
						$hc_is_enabled = esc_attr( get_option('is_enabled') );
					 ?>
					<label for="hc-form__enable">
						<?php if( $hc_is_enabled == "on" ): ?>
						<input type="checkbox" name="is_enabled" id="hc-form__enable" checked="checked" /> <?php _e( 'Enable plugin', 'hayona-cookies' ); ?>
						<?php else: ?>
						<input type="checkbox" name="is_enabled" id="hc-form__enable" /> <?php _e( 'Enable plugin', 'hayona-cookies' ); ?>
						<?php endif; ?>
					</label>
					<p class="description">
						<?php _e( 'You can enable the plugin if you have done the steps listed above.', 'hayona-cookies' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</div>		

	<div class="hc-box">
		<h3><?php _e( 'Banner settings', 'hayona-cookies' ); ?></h3>
		<p>
			<?php printf( 
				__( 'Informing your visitors about cookies on your website is an important part of conforming to the EU cookie law. Use the banner text to provide this information. See the %1$sdocumentation%2$s for various examples in different situations.', 'hayona-cookies' ), 
				'<a href="#">', 
				'</a>' ); 
			?>
		</p>
		<table class="form-table hc-form-table">
			<tr>
				<th>
					<?php _e( 'Banner text', 'hayona-cookies' ); ?>
				</th>
				<td>
					<textarea name="banner_text" rows="5" cols="50"
							class="large-text" 
							placeholder="<?php esc_attr__( 'Place your banner text here...', 'hayona-cookies' ); ?>"
							><?php echo esc_attr( get_option('banner_text') ); ?></textarea>
					<p class="description">
						<?php printf( 
							__( 'Need some examples? See %1$sdocumentation%2$s.', 'hayona-cookies' ), 
							'<a href="#">', 
							'</a>' ); 
						?>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<?php _e( 'Implied consent', 'hayona-cookies' ); ?>
				</th>
				<td>
					<?php 
						$hc_implied_consent_enabled = esc_attr( get_option('implied_consent_enabled') );
					 ?>
					<label>
						<?php if( $hc_implied_consent_enabled == "on" ): ?>
						<input type="checkbox" name="implied_consent_enabled" checked="checked" /> <?php _e( 'Enable implied consent', 'hayona-cookies' ); ?>
						<?php else: ?>
						<input type="checkbox" name="implied_consent_enabled" /> <?php _e( 'Enable implied consent', 'hayona-cookies' ); ?>
						<?php endif; ?>
					</label>
					<p class="description">
						<?php _e( 'Implied consent means if a user clicks through to the next page, it will count as permission.', 'hayona-cookies' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<?php _e( 'Color scheme', 'hayona-cookies' ); ?>
				</th>
				<?php 
					$hc_color_scheme = esc_attr( get_option('banner_color_scheme') );
				 ?>
				<td>
					<select name="banner_color_scheme">
						<option value="dark" 
								<?php if( $hc_color_scheme == "dark" ): ?>selected="selected"<?php endif; ?>>
							<?php _e( 'Dark color scheme', 'hayona-cookies' ); ?>
						</option>
						<option value="light" 
								<?php if( $hc_color_scheme == "light" ): ?>selected="selected"<?php endif; ?>>
							<?php _e( 'Light color scheme', 'hayona-cookies' ); ?>
						</option>
						<option value="none" 
								<?php if( $hc_color_scheme == "none" ): ?>selected="selected"<?php endif; ?>>
							<?php _e( 'No color scheme', 'hayona-cookies' ); ?>
						</option>
					</select>
					<p class="description">
						<?php _e( 'We\'ll add more in time. Select \'No color scheme\' if you prefer to write your own CSS.', 'hayona-cookies' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</div>
	
	<div class="hc-box">
		<h3><?php _e( 'Cookie settings', 'hayona-cookies' ); ?></h3>
		<p>
			<?php _e( 'List all the cookies you use on your website. Place every cookie on a new line.', 'hayona-cookies' ); ?>
		</p>
		<p>
			<?php _e( 'We distinguish between two different kinds of cookies.', 'hayona-cookies' ); ?>
			<?php _e( 'The first category contains cookies that don\'t require permission (usually functional and non-PII cookies).', 'hayona-cookies' ); ?>
			<?php _e( 'The second category contains cookies that do require permission. These are cookies used for tracking, profiling, advertising and cookies that store PII.', 'hayona-cookies' ); ?>
		</p>
		<table class="form-table hc-form-table">
			<tr>
				<th>
					<?php _e( 'No permission required', 'hayona-cookies' ); ?>
				</th>
				<td>
					<textarea name="cookielist_permission_not_required" 
						rows="5" cols="50" class="large-text" 
						placeholder="<?php esc_attr__( 'List your cookies one by one...', 'hayona-cookies' ); ?>"
						><?php echo esc_attr( get_option('cookielist_permission_not_required') ); ?></textarea>
					<p class="description">
						<?php _e( 'List your cookies one by one. Place each cookie on a new line. ', 'hayona-cookies' ); ?>
						<?php printf( 
							__( 'Need some examples? See %1$sdocumentation%2$s.', 'hayona-cookies' ), 
							'<a href="#">', 
							'</a>' ); 
						?>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<?php _e( 'Permission required', 'hayona-cookies' ); ?>
				</th>
				<td>
					<textarea name="cookielist_permission_required" 
						rows="5" cols="50" class="large-text" 
						placeholder="<?php esc_attr__( 'List your cookies one by one...', 'hayona-cookies' ); ?>" 
						><?php echo esc_attr( get_option('cookielist_permission_required') ); ?></textarea>
					<p class="description">
						<?php _e( 'List your cookies one by one. Place each cookie on a new line. ', 'hayona-cookies' ); ?>
						<?php printf( 
							__( 'Need some examples? See %1$sdocumentation%2$s.', 'hayona-cookies' ), 
							'<a href="#">', 
							'</a>' ); 
						?>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<?php _e( 'Reset permissions', 'hayona-cookies' ); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" name="reset_cookie_token" /> <?php _e( 'Reset permissions', 'hayona-cookies' ); ?>
					</label>
					<input type="hidden" name="cookie_token" value="<?php echo esc_attr( get_option('cookie_token') ); ?>">
					<p class="description">
						<?php _e( 'Did you just change anything to the cookies on your site? This means you\'ll need to ask every visitor for their permission again, even if they gave permission before.', 'hayona-cookies' ); ?>
						<?php _e( 'Check this box and press save to do this.', 'hayona-cookies' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</div>
</div>

<div class="hc-admin__sidebar">
	
	<div class="hc-box">
		<h2><?php _e( 'About this plugin', 'hayona-cookies' ); ?></h2>
		<ul>
			<li><a href="#"><?php _e( 'Plugin info', 'hayona-cookies' ); ?></a></li>
		</ul>
		<h3><?php _e( 'Documentation', 'hayona-cookies' ); ?></h3>
		<ul>
			<li><a href="#"><?php _e( 'Read documentation', 'hayona-cookies' ); ?></a></li>
		</ul>
	</div>

</div>

		<?php
			echo '</form>';
			echo '</div>';
		}

}

new Hayona_Cookies();