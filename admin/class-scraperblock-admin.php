<?php
/**
 * Admin handlers.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;

class ScraperBlock_Admin {
	private $rules;
	private $logger;

	public function __construct( ScraperBlock_Rules $rules, ScraperBlock_Logger $logger ) {
		$this->rules  = $rules;
		$this->logger = $logger;
	}

	public function register_menu(): void {
		add_options_page(
			__( 'ScraperBlock', 'windcodex-scraperblock' ),
			__( 'ScraperBlock', 'windcodex-scraperblock' ),
			'manage_options',
			'scraperblock-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings(): void {
		register_setting(
			'scraperblock_settings_group',
			ScraperBlock_Rules::SETTINGS_OPTION,
			array( 'sanitize_callback' => array( $this, 'sanitize_settings' ) )
		);
	}

	public function sanitize_settings( array $input ): array {
		$clean = array();
		$clean['protection_enabled']       = ( $input['protection_enabled'] ?? '' ) === 'yes' ? 'yes' : 'no';
		$clean['enable_robots_blocking']   = ( $input['enable_robots_blocking'] ?? '' ) === 'yes' ? 'yes' : 'no';
		$clean['enable_htaccess_blocking'] = ( $input['enable_htaccess_blocking'] ?? '' ) === 'yes' ? 'yes' : 'no';
		$clean['enable_meta_noai']         = ( $input['enable_meta_noai'] ?? '' ) === 'yes' ? 'yes' : 'no';
		$clean['enable_per_page_control']  = ( $input['enable_per_page_control'] ?? '' ) === 'yes' ? 'yes' : 'no';
		$clean['enable_rate_limit']        = ( $input['enable_rate_limit'] ?? '' ) === 'yes' ? 'yes' : 'no';
		$clean['requests_per_minute']      = max( 1, absint( $input['requests_per_minute'] ?? 60 ) );
		$clean['custom_user_agents']       = sanitize_textarea_field( (string) ( $input['custom_user_agents'] ?? '' ) );
		$clean['allow_ai_search_bots']     = ( $input['allow_ai_search_bots'] ?? '' ) === 'yes' ? 'yes' : 'no';

		ScraperBlock_Htaccess::sync_rules( $clean );
		return $clean;
	}

	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'windcodex-scraperblock' ) );
		}

		$settings              = $this->rules->get_settings();
		$logs                  = $this->logger->get_all();
		$default_bot_list      = $this->rules->get_bot_blocklist();
		require SCRAPERBLOCK_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	public function enqueue_assets( string $hook ): void {
		if ( 'settings_page_scraperblock-settings' !== $hook ) {
			return;
		}
		wp_enqueue_style(
			'scraperblock-admin',
			SCRAPERBLOCK_PLUGIN_URL . 'admin/assets/admin.css',
			array(),
			SCRAPERBLOCK_VERSION
		);
		wp_enqueue_script(
			'scraperblock-admin',
			SCRAPERBLOCK_PLUGIN_URL . 'admin/assets/admin.js',
			array( 'jquery' ),
			SCRAPERBLOCK_VERSION,
			true
		);
		wp_localize_script(
			'scraperblock-admin',
			'scraperblockAdmin',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'saveAction'   => 'scraperblock_save_settings',
				'saveNonce'    => wp_create_nonce( 'scraperblock_save_settings' ),
				'resetAction'  => 'scraperblock_reset_settings',
				'resetNonce'   => wp_create_nonce( 'scraperblock_reset_settings' ),				'i18n'         => array(
					'saving'        => __( 'Saving...', 'windcodex-scraperblock' ),
					'saved'         => __( 'Settings saved!', 'windcodex-scraperblock' ),
					'save_error'    => __( 'Unable to save settings. Please try again.', 'windcodex-scraperblock' ),
					'reset_confirm' => __( 'Reset all settings to default values?', 'windcodex-scraperblock' ),
					'reset_done'    => __( 'Settings reset to defaults.', 'windcodex-scraperblock' ),
					'reset_error'   => __( 'Unable to reset settings. Please try again.', 'windcodex-scraperblock' ),
				),
			)
		);
	}

	public function ajax_save_settings(): void {
		check_ajax_referer( 'scraperblock_save_settings', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'windcodex-scraperblock' ) ) );
		}

		$raw_input = filter_input( INPUT_POST, 'scraperblock_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$input     = is_array( $raw_input ) ? wp_unslash( $raw_input ) : array();

		$clean = $this->sanitize_settings( $input );
		update_option( ScraperBlock_Rules::SETTINGS_OPTION, $clean );

		wp_send_json_success(
			array(
				'message' => __( 'Settings saved successfully.', 'windcodex-scraperblock' ),
			)
		);
	}

	public function ajax_reset_settings(): void {
		check_ajax_referer( 'scraperblock_reset_settings', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'windcodex-scraperblock' ) ) );
		}

		$defaults = ScraperBlock_Rules::get_default_settings();
		update_option( ScraperBlock_Rules::SETTINGS_OPTION, $defaults );
		ScraperBlock_Htaccess::sync_rules( $defaults );

		wp_send_json_success(
			array(
				'message'  => __( 'Settings reset to defaults.', 'windcodex-scraperblock' ),
				'settings' => $defaults,
			)
		);
	}

	public function clear_logs(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'windcodex-scraperblock' ) );
		}

		check_admin_referer( 'scraperblock_clear_logs' );
		$this->logger->clear();
		wp_safe_redirect( admin_url( 'options-general.php?page=scraperblock-settings&cleared=1' ) );
		exit;
	}

	public function reset_settings(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'windcodex-scraperblock' ) );
		}

		check_admin_referer( 'scraperblock_reset_settings' );
		$defaults = ScraperBlock_Rules::get_default_settings();
		update_option( ScraperBlock_Rules::SETTINGS_OPTION, $defaults );
		ScraperBlock_Htaccess::sync_rules( $defaults );
		wp_safe_redirect( admin_url( 'options-general.php?page=scraperblock-settings&reset=1' ) );
		exit;
	}

	public function register_dashboard_widget(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'scraperblock_dashboard_widget',
			__( 'ScraperBlock - Live Dashboard', 'windcodex-scraperblock' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	public function render_dashboard_widget(): void {
		$logs     = $this->logger->get_all();
		$last_24h = $this->count_blocks_last_24h( $logs );
		$total    = count( $logs );
		?>
		<div style="margin:0;">
			<p style="margin:0 0 6px;font-size:12px;color:#64748b;"><?php esc_html_e( 'Blocked bots in last 24 hours', 'windcodex-scraperblock' ); ?></p>
			<p style="margin:0 0 10px;font-size:28px;line-height:1.2;font-weight:700;color:#0f172a;"><?php echo esc_html( (string) $last_24h ); ?></p>
			<?php
			$total_logged_text = sprintf(
				/* translators: %d: total number of blocked requests logged. */
				__( 'Total logged blocks: %d', 'windcodex-scraperblock' ),
				$total
			);
			?>
			<p style="margin:0 0 12px;font-size:12px;color:#64748b;"><?php echo esc_html( $total_logged_text ); ?></p>
			<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'options-general.php?page=scraperblock-settings' ) ); ?>"><?php esc_html_e( 'Open ScraperBlock Settings', 'windcodex-scraperblock' ); ?></a>
		</div>
		<?php
	}

	private function count_blocks_last_24h( array $logs ): int {
		$window_start = time() - DAY_IN_SECONDS;
		$count        = 0;

		foreach ( $logs as $row ) {
			$ts = strtotime( (string) ( $row['time'] ?? '' ) );
			if ( false !== $ts && $ts >= $window_start ) {
				++$count;
			}
		}

		return $count;
	}

	public function register_meta_box(): void {
		$settings              = $this->rules->get_settings();
		if ( ( $settings['enable_per_page_control'] ?? 'yes' ) !== 'yes' ) {
			return;
		}

		foreach ( array( 'post', 'page' ) as $screen ) {
			add_meta_box(
				'scraperblock_page_control',
				__( 'ScraperBlock', 'windcodex-scraperblock' ),
				array( $this, 'render_meta_box' ),
				$screen,
				'side'
			);
		}
	}

	public function render_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'scraperblock_save_post_' . $post->ID, 'scraperblock_post_nonce' );
		$value = (string) get_post_meta( $post->ID, '_scraperblock_disable_protection', true );
		?>
		<p>
			<label>
				<input type="checkbox" name="scraperblock_disable_protection" value="yes" <?php checked( $value, 'yes' ); ?>>
				<?php esc_html_e( 'Disable protection for this page/post', 'windcodex-scraperblock' ); ?>
			</label>
		</p>
		<?php
	}

	public function save_meta_box( int $post_id ): void {
		if ( ! isset( $_POST['scraperblock_post_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['scraperblock_post_nonce'] ) ), 'scraperblock_save_post_' . $post_id ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$value = ( isset( $_POST['scraperblock_disable_protection'] ) && sanitize_text_field( wp_unslash( $_POST['scraperblock_disable_protection'] ) ) === 'yes' ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_scraperblock_disable_protection', $value );
	}

	public function plugin_action_links( array $links ): array {
		array_unshift(
			$links,
			'<a href="' . esc_url( admin_url( 'options-general.php?page=scraperblock-settings' ) ) . '">' . esc_html__( 'Settings', 'windcodex-scraperblock' ) . '</a>',
			'<a href="' . esc_url( 'https://docs.windcodex.com/docs/scraperblock' ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Docs', 'windcodex-scraperblock' ) . '</a>'
		);
		return $links;
	}
}

