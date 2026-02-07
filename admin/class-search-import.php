<?php
/**
 * Search & Import Class.
 *
 * Handles the Search & Import page and AJAX requests.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Search & Import Class.
 */
class QuoteFlex_Search_Import {

	/**
	 * API handler instance.
	 *
	 * @var QuoteFlex_API_Handler
	 */
	private $api_handler;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-api-handler.php';
		$this->api_handler = new QuoteFlex_API_Handler();

		// Register AJAX handlers.
		add_action( 'wp_ajax_quoteflex_search_api', array( $this, 'ajax_search_api' ) );
		add_action( 'wp_ajax_quoteflex_import_quotes', array( $this, 'ajax_import_quotes' ) );
	}

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/search-import.php';
	}

	/**
	 * AJAX handler for API search.
	 *
	 * @since 1.0.0
	 */
	public function ajax_search_api() {
		// Verify nonce.
		check_ajax_referer( 'quoteflex_ajax_nonce', 'nonce' );

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have sufficient permissions.', 'quoteflex' ),
			) );
		}

		// Get search query.
		$query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

		if ( empty( $query ) ) {
			wp_send_json_error( array(
				'message' => __( 'Please enter a search term.', 'quoteflex' ),
			) );
		}

		// Search API.
		$quotes = $this->api_handler->search_quotes( $query, 20 );

		// Handle errors.
		if ( is_wp_error( $quotes ) ) {
			wp_send_json_error( array(
				'message' => $quotes->get_error_message(),
			) );
		}

		// Return results.
		wp_send_json_success( array(
			'quotes' => $quotes,
			'count'  => count( $quotes ),
		) );
	}

	/**
	 * AJAX handler for importing quotes.
	 *
	 * @since 1.0.0
	 */
	public function ajax_import_quotes() {
		// Verify nonce.
		check_ajax_referer( 'quoteflex_ajax_nonce', 'nonce' );

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have sufficient permissions.', 'quoteflex' ),
			) );
		}

		// Get quotes data.
		$quotes_data = isset( $_POST['quotes'] ) ? $_POST['quotes'] : array();

		if ( empty( $quotes_data ) ) {
			wp_send_json_error( array(
				'message' => __( 'No quotes selected for import.', 'quoteflex' ),
			) );
		}

		// Sanitize quotes data.
		$sanitized_quotes = array();
		foreach ( $quotes_data as $quote ) {
			$sanitized_quotes[] = array(
				'quote_text' => sanitize_textarea_field( $quote['quote_text'] ),
				'author'     => sanitize_text_field( $quote['author'] ),
				'tags'       => isset( $quote['tags'] ) ? array_map( 'sanitize_text_field', $quote['tags'] ) : array(),
			);
		}

		// Import quotes.
		$results = $this->api_handler->bulk_import( $sanitized_quotes );

		// Return results.
		wp_send_json_success( array(
			'success' => $results['success'],
			'failed'  => $results['failed'],
			'message' => sprintf(
				/* translators: 1: Success count, 2: Failed count */
				__( '%1$d quotes imported successfully. %2$d failed.', 'quoteflex' ),
				$results['success'],
				$results['failed']
			),
		) );
	}
}
