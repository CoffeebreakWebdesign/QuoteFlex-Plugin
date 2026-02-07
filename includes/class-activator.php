<?php
/**
 * Fired during plugin activation.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Activator Class.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class QuoteFlex_Activator {

	/**
	 * Run on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Check minimum requirements.
		self::check_requirements();

		// Create database tables.
		self::create_database();

		// Set default options.
		self::set_default_options();

		// Create default quote set.
		self::create_default_set();

		// Set activation flag.
		update_option( 'quoteflex_activated', true );
		update_option( 'quoteflex_activation_date', current_time( 'mysql' ) );
	}

	/**
	 * Check minimum requirements.
	 *
	 * @since 1.0.0
	 */
	private static function check_requirements() {
		global $wp_version;

		// Check WordPress version.
		if ( version_compare( $wp_version, '6.0', '<' ) ) {
			deactivate_plugins( QUOTEFLEX_BASENAME );
			wp_die(
				esc_html__( 'QuoteFlex requires WordPress 6.0 or higher.', 'quoteflex' ),
				esc_html__( 'Plugin Activation Error', 'quoteflex' ),
				array( 'back_link' => true )
			);
		}

		// Check PHP version.
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			deactivate_plugins( QUOTEFLEX_BASENAME );
			wp_die(
				esc_html__( 'QuoteFlex requires PHP 7.4 or higher.', 'quoteflex' ),
				esc_html__( 'Plugin Activation Error', 'quoteflex' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Create database tables.
	 *
	 * @since 1.0.0
	 */
	private static function create_database() {
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-installer.php';
		QuoteFlex_Installer::install();
	}

	/**
	 * Set default plugin options.
	 *
	 * @since 1.0.0
	 */
	private static function set_default_options() {
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

		add_option( 'quoteflex_settings', $defaults );
	}

	/**
	 * Create a default quote set.
	 *
	 * @since 1.0.0
	 */
	private static function create_default_set() {
		global $wpdb;

		$table = $wpdb->prefix . 'quoteflex_sets';

		// Check if default set already exists.
		$exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $table WHERE set_slug = %s",
				'general'
			)
		);

		if ( ! $exists ) {
			$wpdb->insert(
				$table,
				array(
					'set_name'    => __( 'General Quotes', 'quoteflex' ),
					'set_slug'    => 'general',
					'description' => __( 'Default quote collection', 'quoteflex' ),
				),
				array( '%s', '%s', '%s' )
			);
		}
	}
}
