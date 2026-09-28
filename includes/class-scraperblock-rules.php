<?php
/**
 * Core settings and user-agent rules.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

class ScraperBlock_Rules {
	public const SETTINGS_OPTION = 'scraperblock_settings';
	public const LOG_OPTION      = 'scraperblock_logs';

	public static function get_default_settings(): array {
		return array(
			'protection_enabled'        => 'yes',
			'enable_robots_blocking'    => 'yes',
			'enable_htaccess_blocking'  => 'no',
			'enable_meta_noai'          => 'yes',
			'enable_per_page_control'   => 'yes',
			'enable_rate_limit'         => 'yes',
			'requests_per_minute'       => 60,
			'custom_user_agents'        => '',
			'allow_ai_search_bots'      => 'no',
		);
	}

	/**
	 * AI search and assistant bots. They fetch pages to answer users (and cite
	 * the store) rather than to train models, so they can be allowed separately.
	 */
	public const AI_SEARCH_BOTS = array( 'OAI-SearchBot', 'ChatGPT-User', 'PerplexityBot', 'Perplexity-User', 'MistralAI-User' );

	public function get_settings(): array {
		return array_merge(
			self::get_default_settings(),
			(array) get_option( self::SETTINGS_OPTION, array() )
		);
	}


	public function get_bot_blocklist(): array {
		return array(
			'GPTBot', 'ChatGPT-User', 'Google-Extended', 'CCBot', 'ClaudeBot', 'Claude-Web',
			'anthropic-ai', 'PerplexityBot', 'Perplexity-User', 'Bytespider', 'TikTokSpider',
			'Amazonbot', 'Applebot-Extended', 'Meta-ExternalAgent', 'Meta-ExternalFetcher',
			'ImagesiftBot', 'Diffbot', 'AI2Bot', 'OAI-SearchBot', 'YouBot',
			'SemrushBot', 'AhrefsBot', 'MJ12bot', 'DotBot', 'BLEXBot', 'YandexBot',
			'Baiduspider', 'Sogou', 'PetalBot', 'MegaIndex', 'Seekport',
			'TurnitinBot', 'PetalSearchBot', 'DataForSeoBot', 'omgili', 'ZoominfoBot',
			'Exabot', 'Scrapy', 'python-requests', 'Go-http-client', 'curl/', 'Wget/',
			'HeadlessChrome', 'PhantomJS', 'Puppeteer', 'facebookexternalhit', 'ia_archiver',
			'archive.org_bot', 'Slackbot-LinkExpanding', 'Discordbot', 'LinkedInBot', 'Qwantify',
			'cohere-ai', 'cohere-training-data-crawler', 'MeltwaterNews', 'Meltwater',
			'DomainStatsBot', 'WellKnownBot', 'Neevabot', 'MistralAI-User',
		);
	}

	/**
	 * Built-in bots that are blocked with the current settings.
	 */
	public function get_active_blocklist( array $settings ): array {
		$bots = $this->get_bot_blocklist();
		if ( ( $settings['allow_ai_search_bots'] ?? 'no' ) === 'yes' ) {
			$bots = array_values( array_diff( $bots, self::AI_SEARCH_BOTS ) );
		}
		return $bots;
	}

	public function get_custom_user_agents( array $settings ): array {
		$raw = isset( $settings['custom_user_agents'] ) ? (string) $settings['custom_user_agents'] : '';
		if ( '' === trim( $raw ) ) {
			return array();
		}

		$rows = preg_split( '/\R+/', $raw );
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$agents = array();
		foreach ( $rows as $row ) {
			$row = trim( sanitize_text_field( $row ) );
			if ( '' !== $row ) {
				$agents[] = $row;
			}
		}

		return array_values( array_unique( $agents ) );
	}

	public function is_blocked_user_agent( string $ua, array $settings ): bool {
		$ua = trim( $ua );
		if ( '' === $ua ) {
			return false;
		}

		$blocklist = array_merge( $this->get_active_blocklist( $settings ), $this->get_custom_user_agents( $settings ) );
		foreach ( $blocklist as $needle ) {
			if ( '' !== $needle && false !== stripos( $ua, $needle ) ) {
				return true;
			}
		}

		return false;
	}

	public function get_client_ip(): string {
		$keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
		foreach ( $keys as $key ) {
			if ( empty( $_SERVER[ $key ] ) ) {
				continue;
			}
			$raw = sanitize_text_field( wp_unslash( (string) $_SERVER[ $key ] ) );
			$ip  = trim( explode( ',', $raw )[0] ?? '' );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
		return '';
	}
}






