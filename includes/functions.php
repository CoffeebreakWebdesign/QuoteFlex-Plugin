<?php
/**
 * Helper Functions.
 *
 * Utility functions used throughout the plugin.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugin settings.
 *
 * @since 1.0.0
 * @param string $key     Setting key (optional).
 * @param mixed  $default Default value.
 * @return mixed Setting value or all settings.
 */
function quoteflex_get_setting( $key = '', $default = false ) {
	$settings = get_option( 'quoteflex_settings', array() );

	if ( empty( $key ) ) {
		return $settings;
	}

	return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Get quote manager instance.
 *
 * @since 1.0.0
 * @return QuoteFlex_Quote_Manager
 */
function quoteflex_get_quote_manager() {
	require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-quote-manager.php';
	return new QuoteFlex_Quote_Manager();
}

/**
 * Get set manager instance.
 *
 * @since 1.0.0
 * @return QuoteFlex_Set_Manager
 */
function quoteflex_get_set_manager() {
	require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-set-manager.php';
	return new QuoteFlex_Set_Manager();
}

/**
 * Get display handler instance.
 *
 * @since 1.0.0
 * @return QuoteFlex_Display_Handler
 */
function quoteflex_get_display_handler() {
	require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-display-handler.php';
	return new QuoteFlex_Display_Handler();
}

/**
 * Display a random quote.
 *
 * Helper function to display a quote from template files.
 *
 * @since 1.0.0
 * @param array $args Display arguments.
 */
function quoteflex_display_quote( $args = array() ) {
	$display_handler = quoteflex_get_display_handler();
	
	$quote = $display_handler->get_quote( $args );
	
	$output = $display_handler->render_quote( $quote, $args );
	
	echo $output;
}

/**
 * Get total quote count.
 *
 * @since 1.0.0
 * @return int
 */
function quoteflex_get_total_quotes() {
	$quote_manager = quoteflex_get_quote_manager();
	return $quote_manager->count( array() );
}

/**
 * Get total set count.
 *
 * @since 1.0.0
 * @return int
 */
function quoteflex_get_total_sets() {
	global $wpdb;
	$table = $wpdb->prefix . 'quoteflex_sets';
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
}

/**
 * Debug log function.
 *
 * @since 1.0.0
 * @param mixed $message Message to log.
 */
function quoteflex_log( $message ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
		if ( is_array( $message ) || is_object( $message ) ) {
			error_log( 'QuoteFlex: ' . print_r( $message, true ) );
		} else {
			error_log( 'QuoteFlex: ' . $message );
		}
	}
}
