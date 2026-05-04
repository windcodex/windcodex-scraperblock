<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WindCodex ScraperBlock
 * Description:       AI bot blocker for WordPress to protect content from scrapers with user-agent blocking, robots.txt and meta noai controls, per-page rules, and rate limiting.
 * Version:           1.0.0
 * Author:            WindCodex
 * Author URI:        https://www.windcodex.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       windcodex-scraperblock
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

define( 'SCRAPERBLOCK_VERSION', '1.0.0' );
define( 'SCRAPERBLOCK_PLUGIN_FILE', __FILE__ );
define( 'SCRAPERBLOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCRAPERBLOCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SCRAPERBLOCK_PLUGIN_BASE', plugin_basename( __FILE__ ) );

function scraperblock_init(): void {
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-loader.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-rules.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-rate-limiter.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-logger.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'includes/class-scraperblock-htaccess.php';
	require_once SCRAPERBLOCK_PLUGIN_DIR . 'admin/class-scraperblock-admin.php';
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

	ScraperBlock_Htaccess::sync_rules( array_merge( $defaults, $current ) );
}
register_activation_hook( __FILE__, 'scraperblock_activate' );

function scraperblock_deactivate(): void {
	ScraperBlock_Htaccess::remove_rules();
}
register_deactivation_hook( __FILE__, 'scraperblock_deactivate' );
