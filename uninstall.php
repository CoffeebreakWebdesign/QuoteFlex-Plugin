<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugin data only if user opted to delete on uninstall.
 */
$settings = get_option( 'quoteflex_settings', array() );

if ( isset( $settings['delete_on_uninstall'] ) && $settings['delete_on_uninstall'] ) {
	quoteflex_uninstall_cleanup();
}

/**
 * Perform cleanup on uninstall.
 *
 * @since 1.0.0
 */
function quoteflex_uninstall_cleanup() {
	global $wpdb;

	// Delete database tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quoteflex_quotes" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quoteflex_sets" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quoteflex_set_relationships" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quoteflex_categories" );

	// Delete all plugin options.
	delete_option( 'quoteflex_settings' );
	delete_option( 'quoteflex_db_version' );
	delete_option( 'quoteflex_activated' );
	delete_option( 'quoteflex_activation_date' );
	delete_option( 'quoteflex_deactivated' );
	delete_option( 'quoteflex_deactivation_date' );

	// Delete all transients.
	$wpdb->query(
		"DELETE FROM {$wpdb->options} 
		WHERE option_name LIKE '_transient_quoteflex_%' 
		OR option_name LIKE '_transient_timeout_quoteflex_%'"
	);

	// Clear any scheduled hooks.
	wp_clear_scheduled_hook( 'quoteflex_daily_cleanup' );
}
