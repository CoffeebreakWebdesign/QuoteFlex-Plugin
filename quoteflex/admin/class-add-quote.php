<?php
/**
 * Add Quote Class.
 *
 * Handles the Add New Quote page.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Add Quote Class.
 */
class QuoteFlex_Add_Quote {

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

		$this->process_form();
	}

	/**
	 * Process form submission.
	 *
	 * @since 1.0.0
	 */
	private function process_form() {
		if ( ! isset( $_POST['quoteflex_add_quote'] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! isset( $_POST['quoteflex_nonce'] ) || ! wp_verify_nonce( $_POST['quoteflex_nonce'], 'quoteflex_add_quote' ) ) {
			wp_die( esc_html__( 'Security check failed', 'quoteflex' ) );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions', 'quoteflex' ) );
		}

		// Validate required fields.
		if ( empty( $_POST['quote_text'] ) || empty( $_POST['author'] ) ) {
			add_settings_error( 'quoteflex', 'missing_fields', __( 'Quote text and author are required.', 'quoteflex' ), 'error' );
			return;
		}

		// Prepare data.
		$data = array(
			'quote_text'         => sanitize_textarea_field( $_POST['quote_text'] ),
			'author'             => sanitize_text_field( $_POST['author'] ),
			'author_description' => sanitize_text_field( $_POST['author_description'] ),
			'source'             => sanitize_text_field( $_POST['source'] ),
			'category'           => sanitize_text_field( $_POST['category'] ),
			'status'             => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'active',
			'source_type'        => 'manual',
		);

		// Create quote.
		$quote_id = $this->quote_manager->create( $data );

		if ( $quote_id ) {
			// Handle set assignments.
			if ( ! empty( $_POST['quote_sets'] ) && is_array( $_POST['quote_sets'] ) ) {
				require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-set-manager.php';
				$set_manager = new QuoteFlex_Set_Manager();
				
				foreach ( $_POST['quote_sets'] as $set_id ) {
					$set_manager->assign_quote( $quote_id, absint( $set_id ) );
				}
			}

			add_settings_error( 'quoteflex', 'quote_added', __( 'Quote added successfully!', 'quoteflex' ), 'success' );
			
			// Redirect to All Quotes page.
			wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-all-quotes&added=1' ) );
			exit;
		} else {
			add_settings_error( 'quoteflex', 'quote_failed', __( 'Failed to add quote. Please try again.', 'quoteflex' ), 'error' );
		}
	}

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		// Get available sets (will be empty until Stage 4).
		$sets = $this->get_available_sets();

		// Load view template.
		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/add-quote.php';
	}

	/**
	 * Get available quote sets.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function get_available_sets() {
		global $wpdb;
		$table = $wpdb->prefix . 'quoteflex_sets';
		
		$sets = $wpdb->get_results( "SELECT id, set_name FROM $table ORDER BY set_name ASC" );
		
		return $sets ? $sets : array();
	}
}
