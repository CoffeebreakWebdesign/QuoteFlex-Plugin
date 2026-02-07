<?php
/**
 * Set Manager Class.
 *
 * Handles all CRUD operations for quote sets.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Set Manager Class.
 */
class QuoteFlex_Set_Manager {

	/**
	 * Sets table name.
	 *
	 * @var string
	 */
	private $sets_table;

	/**
	 * Relationships table name.
	 *
	 * @var string
	 */
	private $relationships_table;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->sets_table = $wpdb->prefix . 'quoteflex_sets';
		$this->relationships_table = $wpdb->prefix . 'quoteflex_set_relationships';
	}

	/**
	 * Create a new set.
	 *
	 * @since 1.0.0
	 * @param array $data Set data.
	 * @return int|false Set ID on success, false on failure.
	 */
	public function create( $data ) {
		global $wpdb;

		$defaults = array(
			'set_name'    => '',
			'set_slug'    => '',
			'description' => '',
		);

		$data = wp_parse_args( $data, $defaults );

		// Validate required fields.
		if ( empty( $data['set_name'] ) ) {
			return false;
		}

		// Generate slug if not provided.
		if ( empty( $data['set_slug'] ) ) {
			$data['set_slug'] = sanitize_title( $data['set_name'] );
		}

		// Ensure unique slug.
		$data['set_slug'] = $this->get_unique_slug( $data['set_slug'] );

		// Sanitize data.
		$insert_data = array(
			'set_name'    => sanitize_text_field( $data['set_name'] ),
			'set_slug'    => sanitize_title( $data['set_slug'] ),
			'description' => sanitize_textarea_field( $data['description'] ),
		);

		$result = $wpdb->insert(
			$this->sets_table,
			$insert_data,
			array( '%s', '%s', '%s' )
		);

		if ( $result ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Get a set by ID.
	 *
	 * @since 1.0.0
	 * @param int $id Set ID.
	 * @return object|null Set object or null.
	 */
	public function get( $id ) {
		global $wpdb;

		$set = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->sets_table} WHERE id = %d",
				$id
			)
		);

		return $set;
	}

	/**
	 * Get a set by slug.
	 *
	 * @since 1.0.0
	 * @param string $slug Set slug.
	 * @return object|null Set object or null.
	 */
	public function get_by_slug( $slug ) {
		global $wpdb;

		$set = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->sets_table} WHERE set_slug = %s",
				$slug
			)
		);

		return $set;
	}

	/**
	 * Update a set.
	 *
	 * @since 1.0.0
	 * @param int   $id   Set ID.
	 * @param array $data Set data to update.
	 * @return bool True on success, false on failure.
	 */
	public function update( $id, $data ) {
		global $wpdb;

		// Sanitize data.
		$update_data = array();

		if ( isset( $data['set_name'] ) ) {
			$update_data['set_name'] = sanitize_text_field( $data['set_name'] );
		}
		if ( isset( $data['set_slug'] ) ) {
			$slug = sanitize_title( $data['set_slug'] );
			// Ensure unique slug (excluding current set).
			$update_data['set_slug'] = $this->get_unique_slug( $slug, $id );
		}
		if ( isset( $data['description'] ) ) {
			$update_data['description'] = sanitize_textarea_field( $data['description'] );
		}

		if ( empty( $update_data ) ) {
			return false;
		}

		$result = $wpdb->update(
			$this->sets_table,
			$update_data,
			array( 'id' => $id ),
			array_fill( 0, count( $update_data ), '%s' ),
			array( '%d' )
		);

		return $result !== false;
	}

	/**
	 * Delete a set.
	 *
	 * @since 1.0.0
	 * @param int $id Set ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $id ) {
		global $wpdb;

		// Relationships will be deleted automatically via CASCADE.
		$result = $wpdb->delete(
			$this->sets_table,
			array( 'id' => $id ),
			array( '%d' )
		);

		return $result !== false;
	}

	/**
	 * Get all sets.
	 *
	 * @since 1.0.0
	 * @param array $args Query arguments.
	 * @return array Array of set objects.
	 */
	public function get_all( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'orderby' => 'set_name',
			'order'   => 'ASC',
		);

		$args = wp_parse_args( $args, $defaults );

		// Order by - whitelist validation for security.
		$allowed_orderby = array( 'id', 'set_name', 'set_slug', 'date_created', 'date_modified' );
		$orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'set_name';
		$order   = strtoupper( $args['order'] ) === 'DESC' ? 'DESC' : 'ASC';

		$sql = "SELECT * FROM {$this->sets_table} ORDER BY {$orderby} {$order}";

		$sets = $wpdb->get_results( $sql );

		return $sets ? $sets : array();
	}

	/**
	 * Get quote count for a set.
	 *
	 * @since 1.0.0
	 * @param int $set_id Set ID.
	 * @return int Quote count.
	 */
	public function get_quote_count( $set_id ) {
		global $wpdb;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->relationships_table} WHERE set_id = %d",
				$set_id
			)
		);

		return (int) $count;
	}

	/**
	 * Assign quote to set.
	 *
	 * @since 1.0.0
	 * @param int $quote_id Quote ID.
	 * @param int $set_id   Set ID.
	 * @return bool True on success, false on failure.
	 */
	public function assign_quote( $quote_id, $set_id ) {
		global $wpdb;

		// Check if already assigned.
		$exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$this->relationships_table} WHERE quote_id = %d AND set_id = %d",
				$quote_id,
				$set_id
			)
		);

		if ( $exists ) {
			return true; // Already assigned.
		}

		$result = $wpdb->insert(
			$this->relationships_table,
			array(
				'quote_id' => $quote_id,
				'set_id'   => $set_id,
			),
			array( '%d', '%d' )
		);

		return $result !== false;
	}

	/**
	 * Remove quote from set.
	 *
	 * @since 1.0.0
	 * @param int $quote_id Quote ID.
	 * @param int $set_id   Set ID.
	 * @return bool True on success, false on failure.
	 */
	public function remove_quote( $quote_id, $set_id ) {
		global $wpdb;

		$result = $wpdb->delete(
			$this->relationships_table,
			array(
				'quote_id' => $quote_id,
				'set_id'   => $set_id,
			),
			array( '%d', '%d' )
		);

		return $result !== false;
	}

	/**
	 * Get sets for a quote.
	 *
	 * @since 1.0.0
	 * @param int $quote_id Quote ID.
	 * @return array Array of set IDs.
	 */
	public function get_quote_sets( $quote_id ) {
		global $wpdb;

		$set_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT set_id FROM {$this->relationships_table} WHERE quote_id = %d",
				$quote_id
			)
		);

		return $set_ids ? array_map( 'intval', $set_ids ) : array();
	}

	/**
	 * Get unique slug.
	 *
	 * @since 1.0.0
	 * @param string $slug Desired slug.
	 * @param int    $exclude_id Set ID to exclude from check.
	 * @return string Unique slug.
	 */
	private function get_unique_slug( $slug, $exclude_id = 0 ) {
		global $wpdb;

		$original_slug = $slug;
		$counter = 1;

		while ( true ) {
			$query = $wpdb->prepare(
				"SELECT id FROM {$this->sets_table} WHERE set_slug = %s",
				$slug
			);

			if ( $exclude_id ) {
				$query .= $wpdb->prepare( " AND id != %d", $exclude_id );
			}

			$exists = $wpdb->get_var( $query );

			if ( ! $exists ) {
				return $slug;
			}

			$slug = $original_slug . '-' . $counter;
			$counter++;
		}
	}
}
