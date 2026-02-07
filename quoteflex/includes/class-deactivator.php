<?php
/**
 * Fired during plugin deactivation.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Deactivator Class.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class QuoteFlex_Deactivator {

	/**
	 * Run on plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Clear scheduled hooks.
		self::clear_scheduled_hooks();

		// Clear transients/cache.
		self::clear_cache();

		// Set deactivation flag.
		update_option( 'quoteflex_deactivated', true );
		update_option( 'quoteflex_deactivation_date', current_time( 'mysql' ) );
	}

	/**
	 * Clear any scheduled cron hooks.
	 *
	 * @since 1.0.0
	 */
	private static function clear_scheduled_hooks() {
		// Clear any scheduled events (if we add cron jobs in future).
		$timestamp = wp_next_scheduled( 'quoteflex_daily_cleanup' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'quoteflex_daily_cleanup' );
		}
	}

	/**
	 * Clear all plugin transients and cache.
	 *
	 * @since 1.0.0
	 */
	private static function clear_cache() {
		global $wpdb;

		// Delete all transients with our prefix.
		$wpdb->query(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE '_transient_quoteflex_%' 
			OR option_name LIKE '_transient_timeout_quoteflex_%'"
		);

		// Clear object cache if available.
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}
	}
}
