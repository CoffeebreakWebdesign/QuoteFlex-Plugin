<?php
/**
 * Admin Menu Class.
 *
 * Registers all admin menu pages.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Admin Menu Class.
 */
class QuoteFlex_Admin_Menu {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	/**
	 * Register admin menu.
	 *
	 * @since 1.0.0
	 */
	public function register_menu() {
		// Main menu.
		add_menu_page(
			__( 'QuoteFlex', 'quoteflex' ),
			__( 'QuoteFlex', 'quoteflex' ),
			'manage_options',
			'quoteflex',
			array( $this, 'dashboard_page' ),
			'dashicons-format-quote',
			30
		);

		// Dashboard submenu.
		add_submenu_page(
			'quoteflex',
			__( 'Dashboard', 'quoteflex' ),
			__( 'Dashboard', 'quoteflex' ),
			'manage_options',
			'quoteflex',
			array( $this, 'dashboard_page' )
		);

		// Search & Import.
		add_submenu_page(
			'quoteflex',
			__( 'Search & Import', 'quoteflex' ),
			__( 'Search & Import', 'quoteflex' ),
			'manage_options',
			'quoteflex-search-import',
			array( $this, 'search_import_page' )
		);

		// Add New Quote.
		add_submenu_page(
			'quoteflex',
			__( 'Add New Quote', 'quoteflex' ),
			__( 'Add New Quote', 'quoteflex' ),
			'manage_options',
			'quoteflex-add-quote',
			array( $this, 'add_quote_page' )
		);

		// All Quotes.
		add_submenu_page(
			'quoteflex',
			__( 'All Quotes', 'quoteflex' ),
			__( 'All Quotes', 'quoteflex' ),
			'manage_options',
			'quoteflex-all-quotes',
			array( $this, 'all_quotes_page' )
		);

		// Quote Sets.
		add_submenu_page(
			'quoteflex',
			__( 'Quote Sets', 'quoteflex' ),
			__( 'Quote Sets', 'quoteflex' ),
			'manage_options',
			'quoteflex-sets',
			array( $this, 'quote_sets_page' )
		);

		// Settings.
		add_submenu_page(
			'quoteflex',
			__( 'Settings', 'quoteflex' ),
			__( 'Settings', 'quoteflex' ),
			'manage_options',
			'quoteflex-settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Dashboard page callback.
	 *
	 * @since 1.0.0
	 */
	public function dashboard_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'quoteflex' ) );
		}

		// Load dashboard class.
		require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-dashboard.php';
		$dashboard = new QuoteFlex_Dashboard();
		$dashboard->render();
	}

	/**
	 * Search & Import page callback.
	 *
	 * @since 1.0.0
	 */
	public function search_import_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'quoteflex' ) );
		}

		// Load search import class.
		require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-search-import.php';
		$search_import = new QuoteFlex_Search_Import();
		$search_import->render();
	}

	/**
	 * Add New Quote page callback.
	 *
	 * @since 1.0.0
	 */
	public function add_quote_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'quoteflex' ) );
		}

		// Load add quote class.
		require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-add-quote.php';
		$add_quote = new QuoteFlex_Add_Quote();
		$add_quote->render();
	}

	/**
	 * All Quotes page callback.
	 *
	 * @since 1.0.0
	 */
	public function all_quotes_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'quoteflex' ) );
		}

		// Check if editing a quote.
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['id'] ) ) {
			$this->edit_quote_page();
			return;
		}

		// Load all quotes class.
		require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-all-quotes.php';
		$all_quotes = new QuoteFlex_All_Quotes();
		$all_quotes->render();
	}

	/**
	 * Edit Quote page.
	 *
	 * @since 1.0.0
	 */
	private function edit_quote_page() {
		$quote_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

		if ( ! $quote_id ) {
			wp_die( esc_html__( 'Invalid quote ID.', 'quoteflex' ) );
		}

		// Load quote manager.
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-quote-manager.php';
		$quote_manager = new QuoteFlex_Quote_Manager();

		// Handle update.
		if ( isset( $_POST['quoteflex_update_quote'] ) ) {
			$this->process_edit_form( $quote_id, $quote_manager );
		}

		// Get quote.
		$quote = $quote_manager->get( $quote_id );

		// Get available sets.
		global $wpdb;
		$sets_table = $wpdb->prefix . 'quoteflex_sets';
		$sets = $wpdb->get_results( "SELECT id, set_name FROM $sets_table ORDER BY set_name ASC" );
		$sets = $sets ? $sets : array();

		// Get assigned sets.
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-set-manager.php';
		$set_manager = new QuoteFlex_Set_Manager();
		$assigned_sets = $set_manager->get_quote_sets( $quote_id );

		// Load edit view.
		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/edit-quote.php';
	}

	/**
	 * Process edit form submission.
	 *
	 * @since 1.0.0
	 * @param int                      $quote_id Quote ID.
	 * @param QuoteFlex_Quote_Manager $quote_manager Quote manager instance.
	 */
	private function process_edit_form( $quote_id, $quote_manager ) {
		// Verify nonce.
		if ( ! isset( $_POST['quoteflex_nonce'] ) || ! wp_verify_nonce( $_POST['quoteflex_nonce'], 'quoteflex_edit_quote' ) ) {
			wp_die( esc_html__( 'Security check failed', 'quoteflex' ) );
		}

		// Validate required fields.
		if ( empty( $_POST['quote_text'] ) || empty( $_POST['author'] ) ) {
			add_settings_error( 'quoteflex', 'missing_fields', __( 'Quote text and author are required.', 'quoteflex' ), 'error' );
			return;
		}

		// Prepare update data.
		$data = array(
			'quote_text'         => sanitize_textarea_field( $_POST['quote_text'] ),
			'author'             => sanitize_text_field( $_POST['author'] ),
			'author_description' => sanitize_text_field( $_POST['author_description'] ),
			'source'             => sanitize_text_field( $_POST['source'] ),
			'category'           => sanitize_text_field( $_POST['category'] ),
			'status'             => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'active',
		);

		// Update quote.
		$updated = $quote_manager->update( $quote_id, $data );

		if ( $updated ) {
			// Handle set assignments - sync relationships.
			require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-set-manager.php';
			$set_manager = new QuoteFlex_Set_Manager();
			
			// Get current assignments.
			$current_sets = $set_manager->get_quote_sets( $quote_id );
			
			// Get new assignments from form.
			$new_sets = isset( $_POST['quote_sets'] ) && is_array( $_POST['quote_sets'] ) 
				? array_map( 'intval', $_POST['quote_sets'] ) 
				: array();
			
			// Remove old assignments not in new list.
			foreach ( $current_sets as $set_id ) {
				if ( ! in_array( $set_id, $new_sets ) ) {
					$set_manager->remove_quote( $quote_id, $set_id );
				}
			}
			
			// Add new assignments.
			foreach ( $new_sets as $set_id ) {
				if ( ! in_array( $set_id, $current_sets ) ) {
					$set_manager->assign_quote( $quote_id, $set_id );
				}
			}

			add_settings_error( 'quoteflex', 'quote_updated', __( 'Quote updated successfully!', 'quoteflex' ), 'success' );
		} else {
			add_settings_error( 'quoteflex', 'quote_failed', __( 'Failed to update quote. Please try again.', 'quoteflex' ), 'error' );
		}
	}

	/**
	 * Quote Sets page callback.
	 *
	 * @since 1.0.0
	 */
	public function quote_sets_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'quoteflex' ) );
		}

		// Check if creating or editing a set.
		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'create', 'edit' ) ) ) {
			$this->edit_set_page();
			return;
		}

		// Load quote sets class.
		require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-quote-sets.php';
		$quote_sets = new QuoteFlex_Quote_Sets();
		$quote_sets->render();
	}

	/**
	 * Edit/Create Set page.
	 *
	 * @since 1.0.0
	 */
	private function edit_set_page() {
		require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-edit-set.php';
		$edit_set = new QuoteFlex_Edit_Set();
		$edit_set->render();
	}

	/**
	 * Settings page callback.
	 *
	 * @since 1.0.0
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'quoteflex' ) );
		}

		// Load settings class.
		require_once QUOTEFLEX_PLUGIN_DIR . 'admin/class-settings.php';
		$settings = new QuoteFlex_Settings();
		$settings->render();
	}
}
