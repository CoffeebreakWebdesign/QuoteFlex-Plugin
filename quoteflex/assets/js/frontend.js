/**
 * QuoteFlex Frontend JavaScript
 *
 * Handles AJAX refresh functionality.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize on document ready.
	 */
	$(document).ready(function() {
		initRefreshButtons();
	});

	/**
	 * Initialize refresh button handlers.
	 */
	function initRefreshButtons() {
		$(document).on('click', '.quoteflex-refresh', function(e) {
			e.preventDefault();
			
			const $button = $(this);
			const $container = $button.closest('.quoteflex-quote');
			
			// Get data attributes.
			const set = $button.data('set') || '';
			const category = $button.data('category') || '';
			const template = $button.data('template') || 'default';
			const showAuthor = $button.data('show-author') || true;
			const showSource = $button.data('show-source') || false;
			const enableRefresh = $button.data('enable-refresh') !== false;
			const animation = $button.data('animation') || 'fade';
			
			// Disable button during request.
			$button.prop('disabled', true).text('Loading...');
			
			// AJAX request.
			$.ajax({
				url: quoteflex_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'quoteflex_get_random_quote',
					nonce: quoteflex_ajax.nonce,
					set: set,
					category: category,
					template: template,
					show_author: showAuthor,
					show_source: showSource,
					enable_refresh: enableRefresh,
					animation: animation
				},
				success: function(response) {
					if (response.success) {
						// Fade out old quote.
						$container.fadeOut(300, function() {
							// Replace with new quote.
							const $newQuote = $(response.data.html);
							$container.replaceWith($newQuote);
							
							// Fade in new quote.
							$newQuote.hide().fadeIn(300);
						});
					} else {
						// Re-enable button on error.
						$button.prop('disabled', false).html('↻ New Quote');
						showError(response.data.message || 'Failed to load quote.');
					}
				},
				error: function() {
					// Re-enable button on error.
					$button.prop('disabled', false).html('↻ New Quote');
					showError('Failed to load new quote. Please try again.');
				}
			});
		});
	}

	/**
	 * Show error message.
	 */
	function showError(message) {
		// Simple console log for now.
		// Could be enhanced with visible error messages.
		console.error('QuoteFlex Error:', message);
	}

})(jQuery);
