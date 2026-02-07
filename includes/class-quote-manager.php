<?php
/**
 * Quote Manager Class.
 *
 * Handles all CRUD operations for quotes.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Quote Manager Class.
 */
class QuoteFlex_Quote_Manager {

	/**
	 * Table name.
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'quoteflex_quotes';
	}

	/**
	 * Create a new quote.
	 *
	 * @since 1.0.0
	 * @param array $data Quote data.
	 * @return int|false Quote ID on success, false on failure.
	 */
	public function create( $data ) {
		global $wpdb;

		$defaults = array(
			'quote_text'          => '',
			'author'              => '',
			'author_description'  => '',
			'source'              => '',
			'source_type'         => 'manual',
			'api_source'          => '',
			'category'            => '',
			'status'              => 'active',
		);

		$data = wp_parse_args( $data, $defaults );

		// Validate required fields.
		if ( empty( $data['quote_text'] ) || empty( $data['author'] ) ) {
			return false;
		}

		// Sanitize data.
		$insert_data = array(
			'quote_text'         => sanitize_textarea_field( $data['quote_text'] ),
			'author'             => sanitize_text_field( $data['author'] ),
			'author_description' => sanitize_text_field( $data['author_description'] ),
			'source'             => sanitize_text_field( $data['source'] ),
			'source_type'        => sanitize_text_field( $data['source_type'] ),
			'api_source'         => sanitize_text_field( $data['api_source'] ),
			'category'           => sanitize_text_field( $data['category'] ),
			'status'             => sanitize_text_field( $data['status'] ),
		);

		$result = $wpdb->insert(
			$this->table_name,
			$insert_data,
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		if ( $result ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Get a quote by ID.
	 *
	 * @since 1.0.0
	 * @param int $id Quote ID.
	 * @return object|null Quote object or null.
	 */
	public function get( $id ) {
		global $wpdb;

		$quote = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE id = %d",
				$id
			)
		);

		return $quote;
	}

	/**
	 * Update a quote.
	 *
	 * @since 1.0.0
	 * @param int   $id   Quote ID.
	 * @param array $data Quote data to update.
	 * @return bool True on success, false on failure.
	 */
	public function update( $id, $data ) {
		global $wpdb;

		// Sanitize data.
		$update_data = array();

		if ( isset( $data['quote_text'] ) ) {
			$update_data['quote_text'] = sanitize_textarea_field( $data['quote_text'] );
		}
		if ( isset( $data['author'] ) ) {
			$update_data['author'] = sanitize_text_field( $data['author'] );
		}
		if ( isset( $data['author_description'] ) ) {
			$update_data['author_description'] = sanitize_text_field( $data['author_description'] );
		}
		if ( isset( $data['source'] ) ) {
			$update_data['source'] = sanitize_text_field( $data['source'] );
		}
		if ( isset( $data['category'] ) ) {
			$update_data['category'] = sanitize_text_field( $data['category'] );
		}
		if ( isset( $data['status'] ) ) {
			$update_data['status'] = sanitize_text_field( $data['status'] );
		}

		if ( empty( $update_data ) ) {
			return false;
		}

		$result = $wpdb->update(
			$this->table_name,
			$update_data,
			array( 'id' => $id ),
			array_fill( 0, count( $update_data ), '%s' ),
			array( '%d' )
		);

		return $result !== false;
	}

	/**
	 * Delete a quote.
	 *
	 * @since 1.0.0
	 * @param int $id Quote ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $id ) {
		global $wpdb;

		$result = $wpdb->delete(
			$this->table_name,
			array( 'id' => $id ),
			array( '%d' )
		);

		return $result !== false;
	}

	/**
	 * Get all quotes with optional filters.
	 *
	 * @since 1.0.0
	 * @param array $args Query arguments.
	 * @return array Array of quote objects.
	 */
	public function get_all( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'status'      => 'active',
			'category'    => '',
			'source_type' => '',
			'search'      => '',
			'orderby'     => 'date_added',
			'order'       => 'DESC',
			'limit'       => 20,
			'offset'      => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		$where = array( '1=1' );

		// Status filter.
		if ( ! empty( $args['status'] ) ) {
			$where[] = $wpdb->prepare( 'status = %s', $args['status'] );
		}

		// Category filter.
		if ( ! empty( $args['category'] ) ) {
			$where[] = $wpdb->prepare( 'category = %s', $args['category'] );
		}

		// Source type filter.
		if ( ! empty( $args['source_type'] ) ) {
			$where[] = $wpdb->prepare( 'source_type = %s', $args['source_type'] );
		}

		// Search.
		if ( ! empty( $args['search'] ) ) {
			$search = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$where[] = $wpdb->prepare( '(quote_text LIKE %s OR author LIKE %s)', $search, $search );
		}

		$where_sql = implode( ' AND ', $where );

		// Build query.
		$sql = "SELECT * FROM {$this->table_name} WHERE {$where_sql}";

		// Order by - whitelist validation for security.
		$allowed_orderby = array( 'id', 'quote_text', 'author', 'date_added', 'date_modified', 'status' );
		$orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'date_added';
		$order   = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
		$sql    .= " ORDER BY {$orderby} {$order}";

		// Limit and offset.
		if ( $args['limit'] > 0 ) {
			$sql .= $wpdb->prepare( ' LIMIT %d OFFSET %d', $args['limit'], $args['offset'] );
		}

		$quotes = $wpdb->get_results( $sql );

		return $quotes;
	}

	/**
	 * Count total quotes.
	 *
	 * @since 1.0.0
	 * @param array $args Optional filters.
	 * @return int Total count.
	 */
	public function count( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'status'      => '',
			'category'    => '',
			'source_type' => '',
			'search'      => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$where = array( '1=1' );

		if ( ! empty( $args['status'] ) ) {
			$where[] = $wpdb->prepare( 'status = %s', $args['status'] );
		}

		if ( ! empty( $args['category'] ) ) {
			$where[] = $wpdb->prepare( 'category = %s', $args['category'] );
		}

		if ( ! empty( $args['source_type'] ) ) {
			$where[] = $wpdb->prepare( 'source_type = %s', $args['source_type'] );
		}

		if ( ! empty( $args['search'] ) ) {
			$search = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$where[] = $wpdb->prepare( '(quote_text LIKE %s OR author LIKE %s)', $search, $search );
		}

		$where_sql = implode( ' AND ', $where );

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where_sql}" );

		return (int) $count;
	}

	/**
	 * Check if quote exists (for duplicate detection).
	 *
	 * @since 1.0.0
	 * @param string $quote_text Quote text.
	 * @param string $author     Author name.
	 * @return bool True if exists, false otherwise.
	 */
	public function exists( $quote_text, $author ) {
		global $wpdb;

		// Normalize for comparison.
		$normalized_quote  = $this->normalize_text( $quote_text );
		$normalized_author = $this->normalize_text( $author );

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} 
				WHERE LOWER(REPLACE(REPLACE(REPLACE(quote_text, '.', ''), ',', ''), '\"', '')) = %s 
				AND LOWER(author) = %s",
				$normalized_quote,
				$normalized_author
			)
		);

		return $count > 0;
	}

	/**
	 * Normalize text for comparison.
	 *
	 * @since 1.0.0
	 * @param string $text Text to normalize.
	 * @return string Normalized text.
	 */
	private function normalize_text( $text ) {
		$text = strtolower( $text );
		$text = str_replace( array( '.', ',', '"', "'", '!', '?' ), '', $text );
		$text = trim( $text );
		return $text;
	}
}
