<?php
/**
 * Settings Template.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'QuoteFlex Settings', 'quoteflex' ); ?></h1>

	<?php settings_errors( 'quoteflex' ); ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'quoteflex_settings', 'quoteflex_nonce' ); ?>

		<h2 class="nav-tab-wrapper">
			<a href="#api-settings" class="nav-tab nav-tab-active"><?php esc_html_e( 'API Settings', 'quoteflex' ); ?></a>
			<a href="#display-settings" class="nav-tab"><?php esc_html_e( 'Display Settings', 'quoteflex' ); ?></a>
			<a href="#advanced-settings" class="nav-tab"><?php esc_html_e( 'Advanced', 'quoteflex' ); ?></a>
		</h2>

		<!-- API Settings Tab -->
		<div id="api-settings" class="quoteflex-tab-content">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="api_source"><?php esc_html_e( 'API Source', 'quoteflex' ); ?></label>
						</th>
						<td>
							<select name="api_source" id="api_source">
								<option value="quotable" <?php selected( $settings['api_source'], 'quotable' ); ?>>
									<?php esc_html_e( 'Quotable.io', 'quoteflex' ); ?>
								</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Primary API source for quote search and import.', 'quoteflex' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cache_duration"><?php esc_html_e( 'Cache Duration', 'quoteflex' ); ?></label>
						</th>
						<td>
							<input 
								type="number" 
								name="cache_duration" 
								id="cache_duration" 
								class="small-text" 
								value="<?php echo esc_attr( $settings['cache_duration'] ); ?>" 
								min="0"
							/> <?php esc_html_e( 'seconds', 'quoteflex' ); ?>
							<p class="description">
								<?php esc_html_e( 'How long to cache API responses. Use 0 to disable caching. Default: 3600 (1 hour).', 'quoteflex' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php esc_html_e( 'Clear Cache', 'quoteflex' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" name="clear_cache" value="1" />
								<?php esc_html_e( 'Clear all cached API responses', 'quoteflex' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Check this box to clear the cache when saving settings.', 'quoteflex' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Display Settings Tab -->
		<div id="display-settings" class="quoteflex-tab-content" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="default_template"><?php esc_html_e( 'Default Template', 'quoteflex' ); ?></label>
						</th>
						<td>
							<select name="default_template" id="default_template">
								<option value="default" <?php selected( $settings['default_template'], 'default' ); ?>>
									<?php esc_html_e( 'Default', 'quoteflex' ); ?>
								</option>
								<option value="boxed" <?php selected( $settings['default_template'], 'boxed' ); ?>>
									<?php esc_html_e( 'Boxed', 'quoteflex' ); ?>
								</option>
								<option value="card" <?php selected( $settings['default_template'], 'card' ); ?>>
									<?php esc_html_e( 'Card', 'quoteflex' ); ?>
								</option>
								<option value="minimal" <?php selected( $settings['default_template'], 'minimal' ); ?>>
									<?php esc_html_e( 'Minimal', 'quoteflex' ); ?>
								</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Default display template for quotes.', 'quoteflex' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php esc_html_e( 'Default Display Options', 'quoteflex' ); ?>
						</th>
						<td>
							<fieldset>
								<label>
									<input 
										type="checkbox" 
										name="show_author" 
										value="1" 
										<?php checked( $settings['show_author'] ); ?>
									/>
									<?php esc_html_e( 'Show Author', 'quoteflex' ); ?>
								</label><br>

								<label>
									<input 
										type="checkbox" 
										name="show_source" 
										value="1" 
										<?php checked( $settings['show_source'] ); ?>
									/>
									<?php esc_html_e( 'Show Source', 'quoteflex' ); ?>
								</label><br>

								<label>
									<input 
										type="checkbox" 
										name="enable_ajax_refresh" 
										value="1" 
										<?php checked( $settings['enable_ajax_refresh'] ); ?>
									/>
									<?php esc_html_e( 'Enable AJAX Refresh Button', 'quoteflex' ); ?>
								</label>
							</fieldset>
							<p class="description">
								<?php esc_html_e( 'Default display options (can be overridden in shortcode/block).', 'quoteflex' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="animation_effect"><?php esc_html_e( 'Animation Effect', 'quoteflex' ); ?></label>
						</th>
						<td>
							<select name="animation_effect" id="animation_effect">
								<option value="none" <?php selected( $settings['animation_effect'], 'none' ); ?>>
									<?php esc_html_e( 'None', 'quoteflex' ); ?>
								</option>
								<option value="fade" <?php selected( $settings['animation_effect'], 'fade' ); ?>>
									<?php esc_html_e( 'Fade In', 'quoteflex' ); ?>
								</option>
								<option value="slide" <?php selected( $settings['animation_effect'], 'slide' ); ?>>
									<?php esc_html_e( 'Slide Down', 'quoteflex' ); ?>
								</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Animation effect when displaying quotes.', 'quoteflex' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Advanced Settings Tab -->
		<div id="advanced-settings" class="quoteflex-tab-content" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Uninstall Options', 'quoteflex' ); ?>
						</th>
						<td>
							<label>
								<input 
									type="checkbox" 
									name="delete_on_uninstall" 
									value="1" 
									<?php checked( $settings['delete_on_uninstall'] ); ?>
								/>
								<?php esc_html_e( 'Delete all data when plugin is uninstalled', 'quoteflex' ); ?>
							</label>
							<p class="description" style="color: #d63638;">
								<?php esc_html_e( 'Warning: This will permanently delete all quotes, sets, and settings when you uninstall the plugin.', 'quoteflex' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php esc_html_e( 'Plugin Information', 'quoteflex' ); ?>
						</th>
						<td>
							<p><strong><?php esc_html_e( 'Version:', 'quoteflex' ); ?></strong> <?php echo esc_html( QUOTEFLEX_VERSION ); ?></p>
							<p><strong><?php esc_html_e( 'Database Version:', 'quoteflex' ); ?></strong> <?php echo esc_html( get_option( 'quoteflex_db_version', 'N/A' ) ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<p class="submit">
			<input 
				type="submit" 
				name="quoteflex_save_settings" 
				class="button button-primary" 
				value="<?php esc_attr_e( 'Save Settings', 'quoteflex' ); ?>"
			/>
		</p>
	</form>
</div>

<script>
jQuery(document).ready(function($) {
	// Tab switching.
	$('.nav-tab').on('click', function(e) {
		e.preventDefault();
		
		// Remove active class from all tabs.
		$('.nav-tab').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		
		// Hide all tab content.
		$('.quoteflex-tab-content').hide();
		
		// Show selected tab content.
		const target = $(this).attr('href');
		$(target).show();
	});
});
</script>
