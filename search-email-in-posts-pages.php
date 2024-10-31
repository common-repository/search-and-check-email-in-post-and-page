<?php
/**
 * Plugin Name: Search Clickable Email Address for Mobile
 * Plugin URI: http://www.brainvire.com
 * Description: Enables easy search of email addresses (wrapped with and without mailto in anchor tag) from all posts, custom posts, and pages in the website.
 * Version: 1.0.1
 * Author: brainvireinfo
 * Author URI: http://www.brainvire.com
 * License: GPL2
 *
 * @package SearchClickableEmail
 */

// Exit if try to access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin directory path.
define( 'SEARCH_DIR', plugin_dir_path( __FILE__ ) );
define( 'SEARCH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Includes all plugin files.
include_once SEARCH_DIR . '/search-email.php';

// Create custom menu.
add_action( 'admin_menu', 'sei_search_email_init_install' );

if ( ! function_exists( 'sei_search_email_init_install' ) ) {

	/**
	 * Function to create a sub menu.
	 *
	 * @return void
	 */
	function sei_search_email_init_install() {

		// Create new top level menu.
		add_menu_page(
			'Search clickable email', // Page Title.
			'Search Email', // Menu Title.
			'manage_options', // Capability.
			'search-clickable-email', // Menu Slug.
			'sei_search_clickable_email', // Callable function.
			SEARCH_PLUGIN_URL . 'assets/images/SearchIcon.png' // Icon.
		);
	}
}

/**
 *Add link for settings
*/
add_filter( 'plugin_action_links', 'seip_admin_settings', 10, 4 );

/**
 * Add the Setting Links
 *
 * @since 1.0.1
 * @name seip_admin_settings
 * @param array  $actions actions.
 * @param string $plugin_file plugin file name.
 * @return $actions
 * @author Brainvire <https://www.brainvire.com/>
 * @link https://www.brainvire.com/
 */
function seip_admin_settings( $actions, $plugin_file ) {
	static $plugin;
	if ( ! isset( $plugin ) ) {
		$plugin = plugin_basename( __FILE__ );
	}
	if ( $plugin === $plugin_file ) {
		$settings = array();
		$settings['settings']         = '<a href="' . esc_url( admin_url( 'admin.php?page=search-clickable-email' ) ) . '">' . esc_html__( 'Settings', 'disable-wp-user-login' ) . '</a>';
		$actions                      = array_merge( $settings, $actions );
	}
	return $actions;
}