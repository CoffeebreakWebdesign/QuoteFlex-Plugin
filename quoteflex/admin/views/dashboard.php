<?php
/**
 * Dashboard Template.
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
	<h1><?php esc_html_e( 'QuoteFlex Dashboard', 'quoteflex' ); ?></h1>

	<div class="quoteflex-dashboard">
		<!-- Statistics Cards -->
		<div class="quoteflex-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
			
			<!-- Total Quotes -->
			<div class="quoteflex-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
				<div style="font-size: 14px; color: #646970; margin-bottom: 10px;">
					<?php esc_html_e( 'Total Quotes', 'quoteflex' ); ?>
				</div>
				<div style="font-size: 32px; font-weight: 600; color: #1d2327;">
					<?php echo esc_html( number_format_i18n( $stats['total_quotes'] ) ); ?>
				</div>
			</div>

			<!-- Active Quotes -->
			<div class="quoteflex-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
				<div style="font-size: 14px; color: #646970; margin-bottom: 10px;">
					<?php esc_html_e( 'Active Quotes', 'quoteflex' ); ?>
				</div>
				<div style="font-size: 32px; font-weight: 600; color: #00a32a;">
					<?php echo esc_html( number_format_i18n( $stats['active_quotes'] ) ); ?>
				</div>
			</div>

			<!-- Quote Sets -->
			<div class="quoteflex-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
				<div style="font-size: 14px; color: #646970; margin-bottom: 10px;">
					<?php esc_html_e( 'Quote Sets', 'quoteflex' ); ?>
				</div>
				<div style="font-size: 32px; font-weight: 600; color: #2271b1;">
					<?php echo esc_html( number_format_i18n( $stats['total_sets'] ) ); ?>
				</div>
			</div>

			<!-- Added Today -->
			<div class="quoteflex-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
				<div style="font-size: 14px; color: #646970; margin-bottom: 10px;">
					<?php esc_html_e( 'Added Today', 'quoteflex' ); ?>
				</div>
				<div style="font-size: 32px; font-weight: 600; color: #d63638;">
					<?php echo esc_html( number_format_i18n( $stats['quotes_today'] ) ); ?>
				</div>
			</div>

		</div>

		<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">
			
			<!-- Recent Activity -->
			<div class="quoteflex-recent-activity" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
				<h2 style="margin-top: 0; font-size: 18px;"><?php esc_html_e( 'Recent Quotes', 'quoteflex' ); ?></h2>
				
				<?php if ( ! empty( $recent_quotes ) ) : ?>
					<table class="widefat" style="margin-top: 15px;">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Quote', 'quoteflex' ); ?></th>
								<th><?php esc_html_e( 'Author', 'quoteflex' ); ?></th>
								<th><?php esc_html_e( 'Source', 'quoteflex' ); ?></th>
								<th><?php esc_html_e( 'Date', 'quoteflex' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $recent_quotes as $quote ) : ?>
								<tr>
									<td>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-all-quotes&action=edit&id=' . $quote->id ) ); ?>">
											<?php echo esc_html( wp_trim_words( $quote->quote_text, 10 ) ); ?>
										</a>
									</td>
									<td><?php echo esc_html( $quote->author ); ?></td>
									<td><?php echo esc_html( ucfirst( $quote->source_type ) ); ?></td>
									<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $quote->date_added ) ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p style="color: #646970; margin-top: 15px;">
						<?php esc_html_e( 'No quotes yet. Get started by adding your first quote!', 'quoteflex' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<!-- Quick Actions -->
			<div class="quoteflex-quick-actions" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
				<h2 style="margin-top: 0; font-size: 18px;"><?php esc_html_e( 'Quick Actions', 'quoteflex' ); ?></h2>
				
				<div style="margin-top: 15px;">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-add-quote' ) ); ?>" class="button button-primary button-large" style="width: 100%; text-align: center; margin-bottom: 10px;">
						<?php esc_html_e( 'Add New Quote', 'quoteflex' ); ?>
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-search-import' ) ); ?>" class="button button-large" style="width: 100%; text-align: center; margin-bottom: 10px;">
						<?php esc_html_e( 'Search & Import', 'quoteflex' ); ?>
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-all-quotes' ) ); ?>" class="button button-large" style="width: 100%; text-align: center; margin-bottom: 10px;">
						<?php esc_html_e( 'View All Quotes', 'quoteflex' ); ?>
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-sets' ) ); ?>" class="button button-large" style="width: 100%; text-align: center;">
						<?php esc_html_e( 'Manage Quote Sets', 'quoteflex' ); ?>
					</a>
				</div>

				<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dcdcde;">
					<h3 style="font-size: 14px; margin-top: 0;"><?php esc_html_e( 'Quote Sources', 'quoteflex' ); ?></h3>
					<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
						<span style="color: #646970;"><?php esc_html_e( 'From API:', 'quoteflex' ); ?></span>
						<strong><?php echo esc_html( number_format_i18n( $stats['api_quotes'] ) ); ?></strong>
					</div>
					<div style="display: flex; justify-content: space-between;">
						<span style="color: #646970;"><?php esc_html_e( 'Manual:', 'quoteflex' ); ?></span>
						<strong><?php echo esc_html( number_format_i18n( $stats['manual_quotes'] ) ); ?></strong>
					</div>
				</div>

				<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dcdcde;">
					<p style="margin: 0; color: #646970; font-size: 13px;">
						📚 <?php esc_html_e( 'Need help?', 'quoteflex' ); ?>
						<a href="https://quoteflex.io/docs" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'View Documentation', 'quoteflex' ); ?>
						</a>
					</p>
				</div>
			</div>

		</div>
	</div>
</div>
