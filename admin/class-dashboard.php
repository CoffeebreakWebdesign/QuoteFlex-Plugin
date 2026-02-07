<?php
/**
 * Dashboard Class.
 *
 * Displays plugin dashboard with statistics.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Dashboard Class.
 */
class QuoteFlex_Dashboard {

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
	 * Render the dashboard.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$stats = $this->get_statistics();
		$recent_quotes = $this->get_recent_quotes();

		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Get plugin statistics.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function get_statistics() {
		global $wpdb;

		// Total quotes.
		$total_quotes = $this->quote_manager->count( array() );

		// Active quotes.
		$active_quotes = $this->quote_manager->count( array( 'status' => 'active' ) );

		// Quotes by source type.
		$api_quotes = $this->quote_manager->count( array( 'source_type' => 'api' ) );
		$manual_quotes = $this->quote_manager->count( array( 'source_type' => 'manual' ) );

		// Total sets.
		$sets_table = $wpdb->prefix . 'quoteflex_sets';
		$total_sets = $wpdb->get_var( "SELECT COUNT(*) FROM $sets_table" );

		// Quotes added today.
		$quotes_table = $wpdb->prefix . 'quoteflex_quotes';
		$today = current_time( 'Y-m-d' );
		$quotes_today = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $quotes_table WHERE DATE(date_added) = %s",
				$today
			)
		);

		return array(
			'total_quotes'   => (int) $total_quotes,
			'active_quotes'  => (int) $active_quotes,
			'api_quotes'     => (int) $api_quotes,
			'manual_quotes'  => (int) $manual_quotes,
			'total_sets'     => (int) $total_sets,
			'quotes_today'   => (int) $quotes_today,
		);
	}

	/**
	 * Get recent quotes.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function get_recent_quotes() {
		$quotes = $this->quote_manager->get_all( array(
			'orderby' => 'date_added',
			'order'   => 'DESC',
			'limit'   => 5,
			'offset'  => 0,
			'status'  => '', // Get all statuses.
		) );

		return $quotes;
	}
}
