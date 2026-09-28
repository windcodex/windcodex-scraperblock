<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WindCodex ScraperBlock
 * Description:       AI bot blocker for WordPress to protect content from scrapers with user-agent blocking, robots.txt and meta noai controls, per-page rules, and rate limiting.
 * Version:           1.0.3
 * Author:            WindCodex
 * Author URI:        https://www.windcodex.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       windcodex-scraperblock
 * Domain Path:       /languages
 * Requires at least: 6.9
 * Tested up to:      7.0
 * Requires PHP:      7.4
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

define( 'SCRAPERBLOCK_VERSION', '1.0.3' );
define( 'SCRAPERBLOCK_PLUGIN_FILE', __FILE__ );
define( 'SCRAPERBLOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCRAPERBLOCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'SCRAPERBLOCK_PLUGIN_BASE' ) ) {
	define( 'SCRAPERBLOCK_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}

// --- Translations ---------------------------------------------------------------

function scraperblock_load_textdomain(): void {
	load_plugin_textdomain( 'windcodex-scraperblock', false, dirname( SCRAPERBLOCK_PLUGIN_BASE ) . '/languages' );
}
add_action( 'init', 'scraperblock_load_textdomain' );

// --- Pro Active check ---------------------------------------------

function scraperblock_check_pro_active(): void {
	if ( defined( 'SCRAPERBLOCK_PRO_VERSION' ) ) {
		add_action( 'admin_notices', 'scraperblock_pro_active_notice' );
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( SCRAPERBLOCK_PLUGIN_BASE );
		}
	}
}
add_action( 'plugins_loaded', 'scraperblock_check_pro_active' );

function scraperblock_pro_active_notice(): void {
	echo '<div class="notice notice-error"><p>';
	echo wp_kses_post(
		sprintf(
			/* translators: %s: ScraperBlock Pro plugin URL */
			__( '<strong>WindCodex ScraperBlock</strong> cannot be activated while <strong>ScraperBlock Pro</strong> is active.', 'windcodex-scraperblock' ),
			'https://www.windcodex.com/scraperblock/'
		)
	);
	echo '</p></div>';
}

function scraperblock_init(): void {
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-loader.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-rules.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-rate-limiter.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-logger.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-htaccess.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'admin/class-scraperblock-admin.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'admin/class-scraperblock-notices.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'public/class-scraperblock-public.php';

	ScraperBlock_Loader::instance();
}
add_action( 'plugins_loaded', 'scraperblock_init', 20 );

function scraperblock_activate(): void {
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-rules.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-htaccess.php';

	$defaults = ScraperBlock_Rules::get_default_settings();
	$current  = (array) get_option( ScraperBlock_Rules::SETTINGS_OPTION, array() );
	update_option( ScraperBlock_Rules::SETTINGS_OPTION, array_merge( $defaults, $current ) );
	update_option( 'scraperblock_version', SCRAPERBLOCK_VERSION );

	// Track first activation time for the review request notice.
	if ( ! get_option( 'scraperblock_activated_time' ) ) {
		update_option( 'scraperblock_activated_time', time() );
	}

	ScraperBlock_Htaccess::sync_rules( array_merge( $defaults, $current ) );
}
register_activation_hook( __FILE__, 'scraperblock_activate' );

function scraperblock_deactivate(): void {
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-htaccess.php';
	ScraperBlock_Htaccess::remove_rules();
}
register_deactivation_hook( __FILE__, 'scraperblock_deactivate' );
