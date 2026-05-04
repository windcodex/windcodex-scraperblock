<?php
/**
 * Settings page view.
 *
 * @package ScraperBlock
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="gg-header-card">
	<div class="gg-breadcrumb">
		<a href="<?php echo esc_url( admin_url( 'options-general.php?page=scraperblock-settings' ) ); ?>"><?php esc_html_e( 'ScraperBlock', 'windcodex-scraperblock' ); ?></a>
		<span class="gg-breadcrumb-sep">/</span>
		<span id="gg-breadcrumb-current"><?php esc_html_e( 'General', 'windcodex-scraperblock' ); ?></span>
	</div>
</div>

<div class="wrap gg-wrap">
	<div class="gg-tabs-nav" role="tablist">
		<button class="gg-tab-btn gg-tab-active" data-tab="general" data-breadcrumb="General" aria-selected="true"><?php esc_html_e( 'General', 'windcodex-scraperblock' ); ?></button>
		<button class="gg-tab-btn" data-tab="logs" data-breadcrumb="Logs" aria-selected="false"><?php esc_html_e( 'Logs', 'windcodex-scraperblock' ); ?></button>
	</div>

	<form method="post" action="options.php" id="gg-settings-form">
		<?php settings_fields( 'scraperblock_settings_group' ); ?>

		<div class="gg-tab-panel gg-tab-panel-active" data-panel="general">
			<div class="gg-card">
				<div class="gg-card-header">
					<div>
						<div class="gg-card-header-title"><?php esc_html_e( 'Core Features', 'windcodex-scraperblock' ); ?></div>
						<div class="gg-card-header-sub"><?php esc_html_e( 'Configure core bot protection controls for your site.', 'windcodex-scraperblock' ); ?></div>
					</div>
				</div>

				<div class="gg-form-row">
					<div class="gg-row-label"><div class="gg-row-title">Enable Protection</div><div class="gg-row-hint">Enable or disable all bot protection logic.</div></div>
					<div class="gg-row-body"><label class="gg-toggle-wrap"><input type="checkbox" name="scraperblock_settings[protection_enabled]" value="yes" class="gg-toggle-checkbox" <?php checked( $settings['protection_enabled'] ?? 'yes', 'yes' ); ?>><span class="gg-toggle-track"><span class="gg-toggle-thumb"></span></span></label></div>
				</div>

				<div class="gg-form-row">
					<div class="gg-row-label"><div class="gg-row-title">robots.txt Blocking</div><div class="gg-row-hint">Lightweight crawler guidance layer.</div></div>
					<div class="gg-row-body"><label class="gg-toggle-wrap"><input type="checkbox" name="scraperblock_settings[enable_robots_blocking]" value="yes" class="gg-toggle-checkbox" <?php checked( $settings['enable_robots_blocking'] ?? 'yes', 'yes' ); ?>><span class="gg-toggle-track"><span class="gg-toggle-thumb"></span></span></label></div>
				</div>

				<div class="gg-form-row">
					<div class="gg-row-label"><div class="gg-row-title">.htaccess Blocking</div><div class="gg-row-hint">Strong server-level blocking (Apache only). Faster than WordPress-level blocking.</div></div>
					<div class="gg-row-body"><label class="gg-toggle-wrap"><input type="checkbox" name="scraperblock_settings[enable_htaccess_blocking]" value="yes" class="gg-toggle-checkbox" <?php checked( $settings['enable_htaccess_blocking'] ?? 'no', 'yes' ); ?>><span class="gg-toggle-track"><span class="gg-toggle-thumb"></span></span></label></div>
				</div>

				<div class="gg-form-row">
					<div class="gg-row-label"><div class="gg-row-title">noai / Meta Tags</div><div class="gg-row-hint">Tells AI bots not to use your content for training.</div></div>
					<div class="gg-row-body"><label class="gg-toggle-wrap"><input type="checkbox" name="scraperblock_settings[enable_meta_noai]" value="yes" class="gg-toggle-checkbox" <?php checked( $settings['enable_meta_noai'] ?? 'yes', 'yes' ); ?>><span class="gg-toggle-track"><span class="gg-toggle-thumb"></span></span></label></div>
				</div>

				<div class="gg-form-row">
					<div class="gg-row-label"><div class="gg-row-title">Per-page Control</div><div class="gg-row-hint">Useful for public pages you want to allow.</div></div>
					<div class="gg-row-body"><label class="gg-toggle-wrap"><input type="checkbox" name="scraperblock_settings[enable_per_page_control]" value="yes" class="gg-toggle-checkbox" <?php checked( $settings['enable_per_page_control'] ?? 'yes', 'yes' ); ?>><span class="gg-toggle-track"><span class="gg-toggle-thumb"></span></span></label></div>
				</div>

				<div class="gg-form-row gg-rate-limit-row">
					<div class="gg-row-label"><div class="gg-row-title">Basic Rate Limiting</div><div class="gg-row-hint">Helps reduce aggressive repeated hits.</div></div>
					<div class="gg-row-body gg-inline-fields">
						<label class="gg-toggle-wrap"><input type="checkbox" name="scraperblock_settings[enable_rate_limit]" value="yes" class="gg-toggle-checkbox" <?php checked( $settings['enable_rate_limit'] ?? 'yes', 'yes' ); ?>><span class="gg-toggle-track"><span class="gg-toggle-thumb"></span></span></label>
						<input type="number" min="1" class="gg-input gg-input-sm gg-rate-limit-input" name="scraperblock_settings[requests_per_minute]" value="<?php echo esc_attr( (string) ( $settings['requests_per_minute'] ?? 60 ) ); ?>">
						<span class="gg-inline-text"><?php esc_html_e( 'requests per minute', 'windcodex-scraperblock' ); ?></span>
						<div class="gg-field-hint"><?php esc_html_e( 'Recommended: 30-120. Lower = stricter.', 'windcodex-scraperblock' ); ?></div>
					</div>
				</div>

				<div class="gg-form-row gg-form-row-last gg-custom-ua-row">
					<div class="gg-row-label"><div class="gg-row-title">Custom User-agents</div><div class="gg-row-hint">Extend built-in blocklist with your own patterns.</div></div>
					<div class="gg-row-body">
						<textarea class="gg-input" name="scraperblock_settings[custom_user_agents]" rows="5" placeholder="One bot signature per line&#10;Examples:&#10;SomeBot&#10;python-requests&#10;curl/"><?php echo esc_textarea( (string) ( $settings['custom_user_agents'] ?? '' ) ); ?></textarea>
						<div class="gg-botlist-showcase">
							<div class="gg-botlist-card">
								<div class="gg-botlist-head">
									<div class="gg-botlist-title"><?php esc_html_e( 'Default Bot List', 'windcodex-scraperblock' ); ?></div>
									<div class="gg-botlist-sub"><?php echo esc_html( sprintf( 'Default list: %d signatures', count( $default_bot_list ) ) ); ?></div>
								</div>
								<input type="text" class="gg-input gg-input-sm gg-botlist-search" data-botlist-filter="chips" placeholder="<?php esc_attr_e( 'Search bot signature...', 'windcodex-scraperblock' ); ?>">
								<div class="gg-botlist-chips">
									<?php foreach ( $default_bot_list as $scraperblock_bot_signature ) : ?>
										<span class="gg-bot-chip" data-bot-signature="<?php echo esc_attr( strtolower( (string) $scraperblock_bot_signature ) ); ?>"><?php echo esc_html( (string) $scraperblock_bot_signature ); ?></span>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="gg-footer-bar" id="gg-footer-bar" data-tab-visible="general">
			<div class="gg-footer-left">
				<button type="button" id="gg-save-btn" class="gg-btn-primary">
					<span class="gg-btn-label"><?php esc_html_e( 'Save Settings', 'windcodex-scraperblock' ); ?></span>
				</button>
				<button type="button" id="gg-reset-btn" class="gg-btn-reset">
					<span class="gg-btn-label"><?php esc_html_e( 'Reset to Defaults', 'windcodex-scraperblock' ); ?></span>
				</button>
			</div>
			<div class="gg-toast" id="gg-toast" role="status" aria-live="polite"></div>
		</div>
	</form>

	<div class="gg-tab-panel" data-panel="logs">
		<div class="gg-card">
			<div class="gg-card-header"><div><div class="gg-card-header-title"><?php esc_html_e( 'Basic Logs (last 50)', 'windcodex-scraperblock' ); ?></div><div class="gg-card-header-sub"><?php esc_html_e( 'Review recent blocked requests for quick verification and tuning.', 'windcodex-scraperblock' ); ?></div></div><div class="gg-card-header-actions"><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="scraperblock_clear_logs"><?php wp_nonce_field( 'scraperblock_clear_logs' ); ?><?php submit_button( __( 'Clear Logs', 'windcodex-scraperblock' ), 'secondary gg-btn-outline', 'submit', false ); ?></form></div></div>
			<div class="gg-row-body">
				<?php $scraperblock_cleared = filter_input( INPUT_GET, 'cleared', FILTER_SANITIZE_NUMBER_INT ); ?>
				<?php if ( ! empty( $scraperblock_cleared ) ) : ?>
					<div class="notice notice-success inline"><p><?php esc_html_e( 'Logs cleared.', 'windcodex-scraperblock' ); ?></p></div>
				<?php endif; ?>
				<?php if ( empty( $logs ) ) : ?>
					<p class="gg-empty-logs-msg">No blocked requests yet - your site is clean or protection just started.</p>
				<?php else : ?>
					<table class="widefat striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Time', 'windcodex-scraperblock' ); ?></th>
								<th><?php esc_html_e( 'Bot / User Agent', 'windcodex-scraperblock' ); ?></th>
								<th><?php esc_html_e( 'IP Address', 'windcodex-scraperblock' ); ?></th>
								<th><?php esc_html_e( 'URL', 'windcodex-scraperblock' ); ?></th>
								<th><?php esc_html_e( 'Action', 'windcodex-scraperblock' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $logs as $scraperblock_row ) : ?>
								<?php
								$scraperblock_time_raw   = (string) ( $scraperblock_row['time'] ?? '' );
								$scraperblock_time_unix  = strtotime( $scraperblock_time_raw );
								$scraperblock_time_label = false !== $scraperblock_time_unix
									? sprintf(
										/* translators: %s: human-readable elapsed time (for example, "5 minutes"). */
										__( '%s ago', 'windcodex-scraperblock' ),
										human_time_diff( $scraperblock_time_unix, current_time( 'timestamp' ) )
									)
									: $scraperblock_time_raw;
								$scraperblock_ua_label   = (string) ( $scraperblock_row['ua'] ?? '' );
								if ( '' === $scraperblock_ua_label ) {
									$scraperblock_ua_label = (string) ( $scraperblock_row['reason'] ?? '-' );
								}
								$scraperblock_action_label = (string) ( $scraperblock_row['action'] ?? 'blocked' );
								if ( 'deny' === $scraperblock_action_label ) {
									$scraperblock_action_label = 'blocked';
								}
								$scraperblock_action_label = ucwords( str_replace( '_', ' ', $scraperblock_action_label ) );
								?>
								<tr>
									<td><?php echo esc_html( $scraperblock_time_label ); ?></td>
									<td><?php echo esc_html( $scraperblock_ua_label ); ?></td>
									<td><?php echo esc_html( (string) ( $scraperblock_row['ip'] ?? '' ) ); ?></td>
									<td><?php echo esc_html( (string) ( $scraperblock_row['uri'] ?? '' ) ); ?></td>
									<td><?php echo esc_html( $scraperblock_action_label ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
				
			</div>
		</div>
	</div>

</div>


