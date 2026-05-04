<?php
/**
 * Logs blocked requests.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

class ScraperBlock_Logger {
	public function add( array $entry ): void {
		$logs   = (array) get_option( ScraperBlock_Rules::LOG_OPTION, array() );
		$max    = (int) apply_filters( 'scraperblock_log_max_entries', 50 );
		$logs[] = array(
			'time'   => gmdate( 'c' ),
			'ip'     => sanitize_text_field( (string) ( $entry['ip'] ?? '' ) ),
			'ua'     => sanitize_text_field( (string) ( $entry['ua'] ?? '' ) ),
			'uri'    => esc_url_raw( home_url( sanitize_text_field( (string) ( $entry['uri'] ?? '' ) ) ) ),
			'reason' => sanitize_key( (string) ( $entry['reason'] ?? 'blocked' ) ),
			'action' => sanitize_key( (string) ( $entry['action'] ?? 'deny' ) ),
		);

		if ( count( $logs ) > $max ) {
			$logs = array_slice( $logs, -1 * $max );
		}

		update_option( ScraperBlock_Rules::LOG_OPTION, $logs, false );
	}

	public function get_all(): array {
		$logs = (array) get_option( ScraperBlock_Rules::LOG_OPTION, array() );
		return array_reverse( $logs );
	}

	public function clear(): void {
		update_option( ScraperBlock_Rules::LOG_OPTION, array(), false );
	}
}
