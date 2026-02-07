/**
 * QuoteFlex API Search JavaScript
 *
 * Handles API search and import functionality.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

(function($) {
	'use strict';

	let searchResults = [];
	let selectedQuotes = [];

	/**
	 * Initialize on document ready.
	 */
	$(document).ready(function() {
		initSearchForm();
		initImportForm();
		initFilterToggle();
	});

	/**
	 * Initialize search form.
	 */
	function initSearchForm() {
		const $searchBtn = $('#quoteflex-search-btn');
		const $searchInput = $('#quoteflex-search-query');

		// Search button click.
		$searchBtn.on('click', function() {
			performSearch();
		});

		// Enter key in search input.
		$searchInput.on('keypress', function(e) {
			if (e.which === 13) {
				e.preventDefault();
				performSearch();
			}
		});
	}

	/**
	 * Perform API search.
	 */
	function performSearch() {
		const query = $('#quoteflex-search-query').val().trim();

		if (!query) {
			showMessage('Please enter a search term.', 'error');
			return;
		}

		// Show loading.
		$('#quoteflex-loading').show();
		$('#quoteflex-results-container').hide();
		$('#quoteflex-search-message').hide();

		// AJAX request.
		$.ajax({
			url: quoteflex_ajax.ajax_url,
			type: 'POST',
			data: {
				action: 'quoteflex_search_api',
				nonce: quoteflex_ajax.nonce,
				query: query
			},
			success: function(response) {
				$('#quoteflex-loading').hide();

				if (response.success) {
					searchResults = response.data.quotes;
					displayResults(searchResults);
				} else {
					showMessage(response.data.message, 'error');
				}
			},
			error: function() {
				$('#quoteflex-loading').hide();
				showMessage('An error occurred. Please try again.', 'error');
			}
		});
	}

	/**
	 * Display search results.
	 */
	function displayResults(quotes) {
		const $container = $('#quoteflex-results-list');
		const $resultsContainer = $('#quoteflex-results-container');
		const $resultsCount = $('#quoteflex-results-count');

		// Clear previous results.
		$container.empty();
		selectedQuotes = [];
		updateImportButton();

		if (!quotes || quotes.length === 0) {
			showMessage('No quotes found. Try a different search term.', 'info');
			return;
		}

		// Update count.
		const newCount = quotes.filter(q => !q.is_duplicate).length;
		$resultsCount.text(quotes.length + ' results found (' + newCount + ' new)');

		// Render each quote.
		quotes.forEach(function(quote, index) {
			const $item = createQuoteItem(quote, index);
			$container.append($item);
		});

		// Show results.
		$resultsContainer.show();
	}

	/**
	 * Create quote item element.
	 */
	function createQuoteItem(quote, index) {
		const isDuplicate = quote.is_duplicate;
		const checkboxId = 'quote-' + index;

		const $item = $('<div>', {
			'class': 'quoteflex-quote-item' + (isDuplicate ? ' is-duplicate' : ''),
			'data-index': index
		});

		// Checkbox.
		const $checkbox = $('<div>', {'class': 'quoteflex-quote-checkbox'});
		const $input = $('<input>', {
			type: 'checkbox',
			id: checkboxId,
			'class': 'quoteflex-quote-select',
			'data-index': index,
			disabled: isDuplicate
		});
		$checkbox.append($input);

		// Content.
		const $content = $('<div>', {'class': 'quoteflex-quote-content'});

		// Quote text.
		const $text = $('<div>', {
			'class': 'quoteflex-quote-text',
			html: '"' + escapeHtml(quote.quote_text) + '"'
		});

		// Author.
		const $author = $('<div>', {
			'class': 'quoteflex-quote-author',
			text: '— ' + quote.author
		});

		// Duplicate badge.
		if (isDuplicate) {
			const $badge = $('<span>', {
				'class': 'quoteflex-duplicate-badge',
				text: 'Already in Database'
			});
			$author.append($badge);
		}

		// Tags.
		if (quote.tags && quote.tags.length > 0) {
			const $tags = $('<div>', {'class': 'quoteflex-quote-tags'});
			quote.tags.forEach(function(tag) {
				$tags.append($('<span>', {
					'class': 'quoteflex-quote-tag',
					text: tag
				}));
			});
			$content.append($tags);
		}

		$content.append($text).append($author);
		$item.append($checkbox).append($content);

		return $item;
	}

	/**
	 * Initialize import form.
	 */
	function initImportForm() {
		// Checkbox change.
		$(document).on('change', '.quoteflex-quote-select', function() {
			const index = $(this).data('index');
			const quote = searchResults[index];

			if ($(this).is(':checked')) {
				selectedQuotes.push(quote);
			} else {
				selectedQuotes = selectedQuotes.filter(q => q.api_id !== quote.api_id);
			}

			updateImportButton();
		});

		// Import button click.
		$('#quoteflex-import-selected-btn').on('click', function() {
			importSelectedQuotes();
		});
	}

	/**
	 * Update import button state.
	 */
	function updateImportButton() {
		const $btn = $('#quoteflex-import-selected-btn');
		
		if (selectedQuotes.length > 0) {
			$btn.prop('disabled', false);
			$btn.text('Import Selected (' + selectedQuotes.length + ')');
		} else {
			$btn.prop('disabled', true);
			$btn.text('Import Selected');
		}
	}

	/**
	 * Import selected quotes.
	 */
	function importSelectedQuotes() {
		if (selectedQuotes.length === 0) {
			return;
		}

		const $btn = $('#quoteflex-import-selected-btn');
		$btn.prop('disabled', true).text('Importing...');

		// AJAX request.
		$.ajax({
			url: quoteflex_ajax.ajax_url,
			type: 'POST',
			data: {
				action: 'quoteflex_import_quotes',
				nonce: quoteflex_ajax.nonce,
				quotes: selectedQuotes
			},
			success: function(response) {
				if (response.success) {
					showMessage(response.data.message, 'success');
					
					// Remove imported quotes from results.
					selectedQuotes.forEach(function(quote) {
						const index = searchResults.findIndex(q => q.api_id === quote.api_id);
						if (index !== -1) {
							searchResults[index].is_duplicate = true;
						}
					});

					// Refresh display.
					displayResults(searchResults);
				} else {
					showMessage(response.data.message, 'error');
					$btn.prop('disabled', false);
					updateImportButton();
				}
			},
			error: function() {
				showMessage('Import failed. Please try again.', 'error');
				$btn.prop('disabled', false);
				updateImportButton();
			}
		});
	}

	/**
	 * Initialize filter toggle.
	 */
	function initFilterToggle() {
		$('#quoteflex-filter-new-only').on('change', function() {
			if ($(this).is(':checked')) {
				// Show only new quotes.
				$('.quoteflex-quote-item.is-duplicate').hide();
			} else {
				// Show all quotes.
				$('.quoteflex-quote-item').show();
			}
		});
	}

	/**
	 * Show message to user.
	 */
	function showMessage(message, type) {
		const $messageDiv = $('#quoteflex-search-message');
		const typeClass = type === 'error' ? 'notice-error' : (type === 'success' ? 'notice-success' : 'notice-info');

		$messageDiv
			.removeClass('notice-error notice-success notice-info')
			.addClass('notice quoteflex-notice ' + typeClass)
			.html('<p>' + escapeHtml(message) + '</p>')
			.show();
	}

	/**
	 * Escape HTML for safe display.
	 */
	function escapeHtml(text) {
		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}

})(jQuery);
