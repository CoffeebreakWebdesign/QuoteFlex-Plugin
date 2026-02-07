<?php
/**
 * API Handler Class.
 *
 * Handles external API integration for quote search and import.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex API Handler Class.
 */
class QuoteFlex_API_Handler {

	/**
	 * API base URL.
	 *
	 * @var string
	 */
	private $api_url = 'https://api.quotable.io';

	/**
	 * Quote manager instance.
	 *
	 * @var QuoteFlex_Quote_Manager
	 */
	private $quote_manager;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-quote-manager.php';
		$this->quote_manager = new QuoteFlex_Quote_Manager();
	}

	/**
	 * Search quotes from API.
	 *
	 * @since 1.0.0
	 * @param string $query Search term.
	 * @param int    $limit Number of results.
	 * @return array|WP_Error Array of quotes or error.
	 */
	public function search_quotes( $query, $limit = 20 ) {
		// Check cache first.
		$cache_key = 'quoteflex_search_' . md5( $query . $limit );
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Build API URL.
		$url = add_query_arg(
			array(
				'query' => urlencode( $query ),
				'limit' => $limit,
			),
			$this->api_url . '/search/quotes'
		);

		// Make request.
		$response = wp_remote_get( $url, array(
			'timeout' => 30,
			'headers' => array(
				'Accept' => 'application/json',
			),
		) );

		// Check for errors.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code !== 200 ) {
			return new WP_Error( 'api_error', __( 'API request failed.', 'quoteflex' ) );
		}

		// Parse response.
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['results'] ) ) {
			return array();
		}

		// Process quotes and check for duplicates.
		$quotes = array();
		foreach ( $data['results'] as $quote_data ) {
			$quotes[] = $this->process_quote( $quote_data );
		}

		// Cache results for 1 hour.
		set_transient( $cache_key, $quotes, HOUR_IN_SECONDS );

		return $quotes;
	}

	/**
	 * Get random quote from API.
	 *
	 * @since 1.0.0
	 * @return array|WP_Error Quote data or error.
	 */
	public function get_random_quote() {
		$url = $this->api_url . '/quotes/random';

		$response = wp_remote_get( $url, array(
			'timeout' => 30,
			'headers' => array(
				'Accept' => 'application/json',
			),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code !== 200 ) {
			return new WP_Error( 'api_error', __( 'API request failed.', 'quoteflex' ) );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data ) ) {
			return new WP_Error( 'no_data', __( 'No data returned from API.', 'quoteflex' ) );
		}

		// Handle array response (random returns array with single quote).
		if ( isset( $data[0] ) ) {
			$data = $data[0];
		}

		return $this->process_quote( $data );
	}

	/**
	 * Process quote data from API.
	 *
	 * @since 1.0.0
	 * @param array $quote_data Raw quote data from API.
	 * @return array Processed quote data.
	 */
	private function process_quote( $quote_data ) {
		$quote_text = isset( $quote_data['content'] ) ? $quote_data['content'] : '';
		$author = isset( $quote_data['author'] ) ? $quote_data['author'] : '';

		// Check if duplicate.
		$is_duplicate = $this->quote_manager->exists( $quote_text, $author );

		return array(
			'quote_text'  => $quote_text,
			'author'      => $author,
			'tags'        => isset( $quote_data['tags'] ) ? $quote_data['tags'] : array(),
			'length'      => isset( $quote_data['length'] ) ? $quote_data['length'] : 0,
			'api_id'      => isset( $quote_data['_id'] ) ? $quote_data['_id'] : '',
			'is_duplicate' => $is_duplicate,
		);
	}

	/**
	 * Import quote to database.
	 *
	 * @since 1.0.0
	 * @param array $quote_data Quote data from API.
	 * @return int|false Quote ID on success, false on failure.
	 */
	public function import_quote( $quote_data ) {
		// Check if already exists.
		if ( $this->quote_manager->exists( $quote_data['quote_text'], $quote_data['author'] ) ) {
			return false;
		}

		// Prepare data for insert.
		$data = array(
			'quote_text'   => $quote_data['quote_text'],
			'author'       => $quote_data['author'],
			'source_type'  => 'api',
			'api_source'   => 'quotable',
			'category'     => ! empty( $quote_data['tags'] ) ? implode( ', ', $quote_data['tags'] ) : '',
			'status'       => 'active',
		);

		return $this->quote_manager->create( $data );
	}

	/**
	 * Bulk import quotes.
	 *
	 * @since 1.0.0
	 * @param array $quotes_data Array of quote data.
	 * @return array Results with success count and errors.
	 */
	public function bulk_import( $quotes_data ) {
		$results = array(
			'success' => 0,
			'failed'  => 0,
			'errors'  => array(),
		);

		foreach ( $quotes_data as $quote_data ) {
			$quote_id = $this->import_quote( $quote_data );

			if ( $quote_id ) {
				$results['success']++;
			} else {
				$results['failed']++;
				$results['errors'][] = sprintf(
					/* translators: %s: Quote excerpt */
					__( 'Failed to import: %s', 'quoteflex' ),
					wp_trim_words( $quote_data['quote_text'], 10 )
				);
			}
		}

		return $results;
	}
}
