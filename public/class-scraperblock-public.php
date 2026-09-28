<?php
/**
 * Public request filtering.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

class ScraperBlock_Public {
	private $rules;
	private $logger;
	private $limiter;

	public function __construct( ScraperBlock_Rules $rules, ScraperBlock_Logger $logger, ScraperBlock_Rate_Limiter $limiter ) {
		$this->rules   = $rules;
		$this->logger  = $logger;
		$this->limiter = $limiter;
	}

	public function maybe_block_request(): void {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		$settings = $this->rules->get_settings();
		if ( ( $settings['protection_enabled'] ?? 'yes' ) !== 'yes' ) {
			return;
		}

		if ( $this->is_page_protection_disabled( $settings ) ) {
			return;
		}

		$ua = sanitize_text_field( wp_unslash( (string) ( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ) );
		$ip = $this->rules->get_client_ip();
		if ( $this->should_bypass_internal_request( $ua, $ip ) ) {
			return;
		}

		if ( $this->rules->is_blocked_user_agent( $ua, $settings ) ) {
			$this->deny( 'blocked_user_agent', $ip, $ua );
		}

		// Rate limiting covers every anonymous visitor, so scrapers that fake a
		// browser user agent are slowed down too. Logged-in users are exempt.
		if ( ! is_user_logged_in() && ! $this->limiter->allow_request( $ip, $ua, $settings ) ) {
			$this->deny( 'rate_limit', $ip, $ua, 429 );
		}
	}

	public function output_meta_tags(): void {
		$settings = $this->rules->get_settings();
		if ( ( $settings['enable_meta_noai'] ?? 'yes' ) !== 'yes' ) {
			return;
		}

		echo "\n" . '<meta name="robots" content="noai, noimageai, noindexifembedded" />' . "\n";
		echo '<meta name="googlebot" content="noai" />' . "\n";
	}

	public function filter_robots_txt( string $output, bool $public ): string {
		$settings = $this->rules->get_settings();
		if ( ( $settings['enable_robots_blocking'] ?? 'yes' ) !== 'yes' ) {
			return $output;
		}

		$lines = array();
		foreach ( $this->rules->get_active_blocklist( $settings ) as $bot ) {
			$lines[] = 'User-agent: ' . $bot;
			$lines[] = 'Disallow: /';
		}

		return trim( $output ) . "\n\n# ScraperBlock\n" . implode( "\n", $lines ) . "\n";
	}

	private function deny( string $reason, string $ip, string $ua, int $status = 403 ): void {
		$this->logger->add(
			array(
				'ip'     => $ip,
				'ua'     => $ua,
				'uri'    => sanitize_text_field( wp_unslash( (string) ( $_SERVER['REQUEST_URI'] ?? '' ) ) ),
				'reason' => $reason,
				'action' => 'deny_' . $status,
			)
		);

		status_header( $status );
		nocache_headers();
		header( 'Content-Type: text/plain; charset=utf-8' );
		if ( 429 === $status ) {
			header( 'Retry-After: 60' );
			echo esc_html__( '429 Too Many Requests: Please slow down and try again in a minute.', 'windcodex-scraperblock' );
		} else {
			echo esc_html__( '403 Forbidden: Request blocked by ScraperBlock.', 'windcodex-scraperblock' );
		}
		exit;
	}

	private function is_page_protection_disabled( array $settings ): bool {
		if ( ( $settings['enable_per_page_control'] ?? 'yes' ) !== 'yes' ) {
			return false;
		}
		$post_id = 0;
		if ( is_singular() ) {
			$post_id = get_queried_object_id();
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$post_id = absint( get_option( 'woocommerce_shop_page_id' ) );
		}
		if ( $post_id < 1 ) {
			return false;
		}
		$disabled = get_post_meta( $post_id, '_scraperblock_disable_protection', true );
		return (string) $disabled === 'yes';
	}

	private function should_bypass_internal_request( string $ua, string $ip ): bool {
		$server_addr = sanitize_text_field( wp_unslash( (string) ( $_SERVER['SERVER_ADDR'] ?? '' ) ) );
		$remote_addr = sanitize_text_field( wp_unslash( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) ) );
		$local_ips   = array( '127.0.0.1', '::1' );

		if ( '' !== $server_addr ) {
			$local_ips[] = $server_addr;
		}

		$is_local_request      = in_array( $ip, $local_ips, true ) || in_array( $remote_addr, $local_ips, true );
		$is_wordpress_loopback = '' !== $ua && false !== stripos( $ua, 'WordPress/' );
		$is_plugin_check       = '' !== $ua && false !== stripos( $ua, 'plugin-check' );
		$default_bypass        = $is_local_request && ( $is_wordpress_loopback || $is_plugin_check );

		/**
		 * Allow safe bypass for trusted internal diagnostics.
		 *
		 * @param bool   $default_bypass Whether bypass is enabled by default.
		 * @param string $ua             Current request user agent.
		 * @param string $ip             Detected client IP.
		 * @param string $remote_addr    REMOTE_ADDR server value.
		 */
		return (bool) apply_filters( 'scraperblock_bypass_request', $default_bypass, $ua, $ip, $remote_addr );
	}
}
