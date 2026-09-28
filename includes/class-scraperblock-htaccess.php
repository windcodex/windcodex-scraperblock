<?php
/**
 * .htaccess rule management.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

class ScraperBlock_Htaccess {
	private const START = '# BEGIN ScraperBlock';
	private const END   = '# END ScraperBlock';

	public static function sync_rules( array $settings ): void {
		if ( ( $settings['enable_htaccess_blocking'] ?? 'no' ) !== 'yes' ) {
			self::remove_rules();
			return;
		}

		$home_path = trailingslashit( get_home_path() );
		$path = $home_path . '.htaccess';
		
		// Check if .htaccess exists and is writable
		if ( file_exists( $path ) ) {
			if ( ! wp_is_writable( $path ) ) {
				return;
			}
		} else {
			// Check if home directory is writable for creating .htaccess
			if ( ! wp_is_writable( $home_path ) ) {
				return;
			}
		}

		$rules = new ScraperBlock_Rules();
		$uas   = array_merge(
			$rules->get_active_blocklist( $settings ),
			$rules->get_custom_user_agents( $settings )
		);
		$uas   = array_values( array_unique( array_filter( array_map( 'trim', $uas ) ) ) );

		// Without any conditions the RewriteRule would block every visitor.
		if ( empty( $uas ) ) {
			self::remove_rules();
			return;
		}

		$block = implode( PHP_EOL, self::build_block_lines( $uas ) );

		$current = file_exists( $path ) ? (string) file_get_contents( $path ) : '';
		$updated = self::replace_block( $current, $block );
		file_put_contents( $path, trim( $updated ) . PHP_EOL );
	}

	/**
	 * Build the .htaccess block for the given user agents.
	 *
	 * Conditions are joined with [OR] - without it Apache ANDs consecutive
	 * RewriteCond lines, so a request would have to match every bot at once.
	 *
	 * @param string[] $uas User-agent substrings to block.
	 * @return string[]
	 */
	public static function build_block_lines( array $uas ): array {
		$lines = array( self::START, '<IfModule mod_rewrite.c>', 'RewriteEngine On' );
		$last  = count( $uas ) - 1;
		foreach ( array_values( $uas ) as $i => $ua ) {
			$lines[] = 'RewriteCond %{HTTP_USER_AGENT} ' . self::to_pattern( $ua ) . ( $i < $last ? ' [NC,OR]' : ' [NC]' );
		}
		$lines[] = 'RewriteRule .* - [F,L]';
		$lines[] = '</IfModule>';
		$lines[] = self::END;
		return $lines;
	}

	/**
	 * Turn a user-agent substring into a safe RewriteCond pattern. Whitespace
	 * becomes \s because a literal space would end the pattern and break the
	 * whole .htaccess file.
	 */
	private static function to_pattern( string $ua ): string {
		return (string) preg_replace( '/\s+/', '\\s', preg_quote( $ua, '/' ) );
	}

	public static function remove_rules(): void {
		if ( ! function_exists( 'get_home_path' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		$path = trailingslashit( get_home_path() ) . '.htaccess';
		if ( ! file_exists( $path ) || ! wp_is_writable( $path ) ) {
			return;
		}

		$current = (string) file_get_contents( $path );
		$updated = self::replace_block( $current, '' );
		if ( $updated !== $current ) {
			file_put_contents( $path, trim( $updated ) . PHP_EOL );
		}
	}

	private static function replace_block( string $content, string $replacement ): string {
		$pattern = '/' . preg_quote( self::START, '/' ) . '.*?' . preg_quote( self::END, '/' ) . '\R?/s';
		$content = (string) preg_replace( $pattern, '', $content );
		if ( '' === $replacement ) {
			return trim( $content ) . PHP_EOL;
		}
		return trim( $content ) . PHP_EOL . PHP_EOL . $replacement . PHP_EOL;
	}
}
