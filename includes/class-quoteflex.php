<?php
/**
 * The core plugin class.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main QuoteFlex Class.
 */
class QuoteFlex {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->version     = QUOTEFLEX_VERSION;
		$this->plugin_name = 'quoteflex';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load required dependencies.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		// Core classes (will create these in next stages).
		// require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-quote-manager.php';
		// require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-set-manager.php';
		// require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-api-handler.php';
		// require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-display-handler.php';
		// require_once QUOTEFLEX_PLUGIN_DIR . 'includes/functions.php';
	}

	/**
	 * Set the plugin locale for internationalization.
	 *
	 * @since 1.0.0
	 */
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'quoteflex',
			false,
			dirname( QUOTEFLEX_BASENAME ) . '/languages/'
		);
	}

	/**
	 * Register admin hooks.
	 *
	 * @since 1.0.0
	 */
	private function define_admin_hooks() {
		if ( is_admin() ) {
			// Enqueue admin styles and scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

			// Load admin menu.
			require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-admin-menu.php';
			new QuoteFlex_Admin_Menu();
		}
	}

	/**
	 * Register public-facing hooks.
	 *
	 * @since 1.0.0
	 */
	private function define_public_hooks() {
		// Enqueue public styles and scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );

		// Load shortcode.
		require_once QUOTEFLEX_PLUGIN_DIR . 'public/class-shortcode.php';
		new QuoteFlex_Shortcode();

		// Load Gutenberg block.
		require_once QUOTEFLEX_PLUGIN_DIR . 'public/class-block.php';
		new QuoteFlex_Block();

		// Load widget.
		require_once QUOTEFLEX_PLUGIN_DIR . 'public/class-widget.php';
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @since 1.0.0
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on QuoteFlex pages.
		if ( strpos( $hook, 'quoteflex' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'quoteflex-admin',
			QUOTEFLEX_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			$this->version
		);

		wp_enqueue_script(
			'quoteflex-admin',
			QUOTEFLEX_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		// Enqueue API search JS on search-import page.
		if ( strpos( $hook, 'quoteflex-search-import' ) !== false ) {
			wp_enqueue_script(
				'quoteflex-api-search',
				QUOTEFLEX_PLUGIN_URL . 'assets/js/api-search.js',
				array( 'jquery' ),
				$this->version,
				true
			);
		}

		// Localize script for AJAX.
		wp_localize_script(
			'quoteflex-admin',
			'quoteflex_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'quoteflex_ajax_nonce' ),
			)
		);
	}

	/**
	 * Enqueue public assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_public_assets() {
		wp_enqueue_style(
			'quoteflex-frontend',
			QUOTEFLEX_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			$this->version
		);

		wp_enqueue_script(
			'quoteflex-frontend',
			QUOTEFLEX_PLUGIN_URL . 'assets/js/frontend.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		// Localize script for AJAX.
		wp_localize_script(
			'quoteflex-frontend',
			'quoteflex_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'quoteflex_ajax_nonce' ),
			)
		);
	}

	/**
	 * Run the plugin.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		// Plugin is initialized via hooks in constructor.
	}

	/**
	 * Get plugin name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get plugin version.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
}
