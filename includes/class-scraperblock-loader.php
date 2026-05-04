<?php
/**
 * Hook loader.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

class ScraperBlock_Loader {
	private static $instance = null;
	private $actions = array();
	private $filters = array();

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->define_hooks();
			self::$instance->run();
		}
		return self::$instance;
	}

	private function __construct() {}

	private function define_hooks(): void {
		$rules   = new ScraperBlock_Rules();
		$logger  = new ScraperBlock_Logger();
		$limiter = new ScraperBlock_Rate_Limiter();
		$admin   = new ScraperBlock_Admin( $rules, $logger );
		$public  = new ScraperBlock_Public( $rules, $logger, $limiter );

		$this->add_action( 'admin_menu', $admin, 'register_menu' );
		$this->add_action( 'admin_init', $admin, 'register_settings' );
		$this->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_assets' );
		$this->add_action( 'wp_ajax_scraperblock_save_settings', $admin, 'ajax_save_settings' );
		$this->add_action( 'wp_ajax_scraperblock_reset_settings', $admin, 'ajax_reset_settings' );
		$this->add_action( 'admin_post_scraperblock_clear_logs', $admin, 'clear_logs' );
		$this->add_action( 'admin_post_scraperblock_reset_settings', $admin, 'reset_settings' );
		$this->add_action( 'wp_dashboard_setup', $admin, 'register_dashboard_widget' );
		$this->add_action( 'add_meta_boxes', $admin, 'register_meta_box' );
		$this->add_action( 'save_post', $admin, 'save_meta_box' );
		$this->add_filter( 'plugin_action_links_' . SCRAPERBLOCK_PLUGIN_BASE, $admin, 'plugin_action_links' );

		$this->add_action( 'template_redirect', $public, 'maybe_block_request', 0 );
		$this->add_action( 'wp_head', $public, 'output_meta_tags', 1 );
		$this->add_filter( 'robots_txt', $public, 'filter_robots_txt', 10, 2 );
	}

	private function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ): void {
		$this->actions[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}

	private function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ): void {
		$this->filters[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}

	private function run(): void {
		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}

