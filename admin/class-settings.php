<?php
/**
 * Settings Class.
 *
 * Handles the Settings page.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Settings Class.
 */
class QuoteFlex_Settings {

	/**
	 * Settings option name.
	 *
	 * @var string
	 */
	private $option_name = 'quoteflex_settings';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->process_form();
	}

	/**
	 * Process settings form.
	 *
	 * @since 1.0.0
	 */
	private function process_form() {
		if ( ! isset( $_POST['quoteflex_save_settings'] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! isset( $_POST['quoteflex_nonce'] ) || ! wp_verify_nonce( $_POST['quoteflex_nonce'], 'quoteflex_settings' ) ) {
			wp_die( esc_html__( 'Security check failed', 'quoteflex' ) );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions', 'quoteflex' ) );
		}

		// Prepare settings data.
		$settings = array(
			'api_source'          => isset( $_POST['api_source'] ) ? sanitize_text_field( $_POST['api_source'] ) : 'quotable',
			'cache_duration'      => isset( $_POST['cache_duration'] ) ? absint( $_POST['cache_duration'] ) : 3600,
			'default_template'    => isset( $_POST['default_template'] ) ? sanitize_text_field( $_POST['default_template'] ) : 'default',
			'show_author'         => isset( $_POST['show_author'] ) ? 1 : 0,
			'show_source'         => isset( $_POST['show_source'] ) ? 1 : 0,
			'enable_ajax_refresh' => isset( $_POST['enable_ajax_refresh'] ) ? 1 : 0,
			'animation_effect'    => isset( $_POST['animation_effect'] ) ? sanitize_text_field( $_POST['animation_effect'] ) : 'fade',
			'delete_on_uninstall' => isset( $_POST['delete_on_uninstall'] ) ? 1 : 0,
		);

		// Update settings.
		update_option( $this->option_name, $settings );

		// Clear cache if requested.
		if ( isset( $_POST['clear_cache'] ) ) {
			$this->clear_cache();
		}

		add_settings_error( 'quoteflex', 'settings_updated', __( 'Settings saved successfully!', 'quoteflex' ), 'success' );
	}

	/**
	 * Clear all plugin cache.
	 *
	 * @since 1.0.0
	 */
	private function clear_cache() {
		global $wpdb;

		$wpdb->query(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE '_transient_quoteflex_%' 
			OR option_name LIKE '_transient_timeout_quoteflex_%'"
		);
	}

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$settings = get_option( $this->option_name, array() );

		// Set defaults if not set.
		$defaults = array(
			'api_source'          => 'quotable',
			'cache_duration'      => 3600,
			'default_template'    => 'default',
			'show_author'         => true,
			'show_source'         => false,
			'enable_ajax_refresh' => true,
			'animation_effect'    => 'fade',
			'delete_on_uninstall' => false,
		);

		$settings = wp_parse_args( $settings, $defaults );

		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/settings.php';
	}
}
