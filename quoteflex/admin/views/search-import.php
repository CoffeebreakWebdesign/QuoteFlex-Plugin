<?php
/**
 * Search & Import Template.
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
	<h1><?php esc_html_e( 'Search & Import Quotes', 'quoteflex' ); ?></h1>
	<p><?php esc_html_e( 'Search the Quotable.io API for quotes and import them to your collection.', 'quoteflex' ); ?></p>

	<div class="quoteflex-search-import" style="margin-top: 20px;">
		
		<!-- Search Form -->
		<div class="quoteflex-search-form" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin-bottom: 20px;">
			<div style="display: flex; gap: 10px; align-items: flex-start;">
				<div style="flex: 1;">
					<label for="quoteflex-search-query" style="display: block; margin-bottom: 5px; font-weight: 600;">
						<?php esc_html_e( 'Search Term', 'quoteflex' ); ?>
					</label>
					<input 
						type="text" 
						id="quoteflex-search-query" 
						class="regular-text" 
						placeholder="<?php esc_attr_e( 'e.g., success, motivation, leadership...', 'quoteflex' ); ?>"
						style="width: 100%;"
					/>
					<p class="description">
						<?php esc_html_e( 'Search by keyword, topic, or author name.', 'quoteflex' ); ?>
					</p>
				</div>
				<div style="padding-top: 28px;">
					<button type="button" id="quoteflex-search-btn" class="button button-primary button-large">
						<?php esc_html_e( 'Search API', 'quoteflex' ); ?>
					</button>
				</div>
			</div>

			<div id="quoteflex-search-message" style="margin-top: 15px; display: none;"></div>
		</div>

		<!-- Results Area -->
		<div id="quoteflex-results-container" style="display: none;">
			
			<!-- Results Header -->
			<div class="quoteflex-results-header" style="background: #fff; padding: 15px 20px; border: 1px solid #ccd0d4; border-radius: 4px 4px 0 0; display: flex; justify-content: space-between; align-items: center;">
				<div>
					<strong id="quoteflex-results-count"></strong>
					<span style="margin-left: 10px;">
						<label>
							<input type="checkbox" id="quoteflex-filter-new-only" />
							<?php esc_html_e( 'Show new quotes only', 'quoteflex' ); ?>
						</label>
					</span>
				</div>
				<div>
					<button type="button" id="quoteflex-import-selected-btn" class="button button-primary" disabled>
						<?php esc_html_e( 'Import Selected', 'quoteflex' ); ?>
					</button>
				</div>
			</div>

			<!-- Results List -->
			<div id="quoteflex-results-list" style="background: #fff; border: 1px solid #ccd0d4; border-top: none; border-radius: 0 0 4px 4px;">
				<!-- Results will be populated via JavaScript -->
			</div>

		</div>

		<!-- Loading Spinner -->
		<div id="quoteflex-loading" style="display: none; text-align: center; padding: 40px;">
			<span class="spinner is-active" style="float: none; margin: 0 auto;"></span>
			<p style="margin-top: 10px; color: #646970;">
				<?php esc_html_e( 'Searching quotes...', 'quoteflex' ); ?>
			</p>
		</div>

	</div>
</div>

<style>
.quoteflex-quote-item {
	padding: 20px;
	border-bottom: 1px solid #dcdcde;
	display: flex;
	gap: 15px;
	transition: background 0.2s;
}

.quoteflex-quote-item:last-child {
	border-bottom: none;
}

.quoteflex-quote-item:hover {
	background: #f6f7f7;
}

.quoteflex-quote-item.is-duplicate {
	background: #f0f0f1;
	opacity: 0.6;
}

.quoteflex-quote-checkbox {
	flex-shrink: 0;
	padding-top: 3px;
}

.quoteflex-quote-content {
	flex: 1;
}

.quoteflex-quote-text {
	font-size: 15px;
	line-height: 1.6;
	margin-bottom: 10px;
	color: #1d2327;
}

.quoteflex-quote-author {
	font-size: 14px;
	color: #646970;
	font-style: italic;
}

.quoteflex-quote-tags {
	display: inline-flex;
	gap: 5px;
	margin-top: 8px;
	flex-wrap: wrap;
}

.quoteflex-quote-tag {
	background: #f0f0f1;
	padding: 2px 8px;
	border-radius: 3px;
	font-size: 12px;
	color: #646970;
}

.quoteflex-duplicate-badge {
	display: inline-block;
	background: #d63638;
	color: white;
	padding: 3px 8px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: 600;
	margin-left: 10px;
}

.notice.quoteflex-notice {
	margin: 0;
	margin-bottom: 15px;
}
</style>
