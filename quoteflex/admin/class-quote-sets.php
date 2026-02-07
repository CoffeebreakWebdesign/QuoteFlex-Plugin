<?php
/**
 * Quote Sets Class.
 *
 * Handles the Quote Sets management page.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Quote Sets Class.
 */
class QuoteFlex_Quote_Sets {

	/**
	 * Set manager instance.
	 *
	 * @var QuoteFlex_Set_Manager
	 */
	private $set_manager;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-set-manager.php';
		$this->set_manager = new QuoteFlex_Set_Manager();

		$this->process_actions();
	}

	/**
	 * Process actions (delete, etc).
	 *
	 * @since 1.0.0
	 */
	private function process_actions() {
		// Handle delete.
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
			$this->delete_set( absint( $_GET['id'] ) );
		}
	}

	/**
	 * Delete a set.
	 *
	 * @since 1.0.0
	 * @param int $set_id Set ID.
	 */
	private function delete_set( $set_id ) {
		// Verify nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'quoteflex_delete_set_' . $set_id ) ) {
			wp_die( esc_html__( 'Security check failed', 'quoteflex' ) );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions', 'quoteflex' ) );
		}

		// Delete set.
		$deleted = $this->set_manager->delete( $set_id );

		if ( $deleted ) {
			wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-sets&deleted=1' ) );
		} else {
			wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-sets&error=1' ) );
		}
		exit;
	}

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$sets = $this->set_manager->get_all();

		// Add quote count to each set.
		foreach ( $sets as $set ) {
			$set->quote_count = $this->set_manager->get_quote_count( $set->id );
		}

		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/quote-sets.php';
	}
}
