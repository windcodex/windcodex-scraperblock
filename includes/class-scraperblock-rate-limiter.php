<?php
/**
 * Basic rate limiting by IP + user-agent.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

class ScraperBlock_Rate_Limiter {
	public function allow_request( string $ip, string $ua, array $settings ): bool {
		$enabled = ( $settings['enable_rate_limit'] ?? 'yes' ) === 'yes';
		if ( ! $enabled ) {
			return true;
		}

		$limit = absint( $settings['requests_per_minute'] ?? 60 );
		if ( $limit < 1 ) {
			return true;
		}

		$key = 'scraperblock_rl_' . md5( strtolower( $ip . '|' . $ua ) );
		$hit = (int) get_transient( $key );

		if ( $hit >= $limit ) {
			return false;
		}

		set_transient( $key, $hit + 1, MINUTE_IN_SECONDS );
		return true;
	}
}
