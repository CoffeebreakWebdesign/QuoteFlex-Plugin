<?php
/**
 * Edit Set Class.
 *
 * Handles the Create/Edit Set page.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Edit Set Class.
 */
class QuoteFlex_Edit_Set {

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

		$this->process_form();
	}

	/**
	 * Process form submission.
	 *
	 * @since 1.0.0
	 */
	private function process_form() {
		if ( ! isset( $_POST['quoteflex_save_set'] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! isset( $_POST['quoteflex_nonce'] ) || ! wp_verify_nonce( $_POST['quoteflex_nonce'], 'quoteflex_edit_set' ) ) {
			wp_die( esc_html__( 'Security check failed', 'quoteflex' ) );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions', 'quoteflex' ) );
		}

		// Validate required fields.
		if ( empty( $_POST['set_name'] ) ) {
			add_settings_error( 'quoteflex', 'missing_fields', __( 'Set name is required.', 'quoteflex' ), 'error' );
			return;
		}

		// Prepare data.
		$data = array(
			'set_name'    => sanitize_text_field( $_POST['set_name'] ),
			'set_slug'    => sanitize_text_field( $_POST['set_slug'] ),
			'description' => sanitize_textarea_field( $_POST['description'] ),
		);

		// Check if creating or updating.
		$set_id = isset( $_POST['set_id'] ) ? absint( $_POST['set_id'] ) : 0;

		if ( $set_id ) {
			// Update existing set.
			$updated = $this->set_manager->update( $set_id, $data );

			if ( $updated ) {
				wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-sets&updated=1' ) );
				exit;
			} else {
				add_settings_error( 'quoteflex', 'update_failed', __( 'Failed to update set. Please try again.', 'quoteflex' ), 'error' );
			}
		} else {
			// Create new set.
			$set_id = $this->set_manager->create( $data );

			if ( $set_id ) {
				wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-sets&created=1' ) );
				exit;
			} else {
				add_settings_error( 'quoteflex', 'create_failed', __( 'Failed to create set. Please try again.', 'quoteflex' ), 'error' );
			}
		}
	}

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$set_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		$is_edit = $set_id > 0;

		// Get set if editing.
		$set = null;
		if ( $is_edit ) {
			$set = $this->set_manager->get( $set_id );
			if ( ! $set ) {
				wp_die( esc_html__( 'Set not found.', 'quoteflex' ) );
			}
		}

		// Get assigned quotes if editing.
		$assigned_quotes = array();
		if ( $is_edit ) {
			$assigned_quotes = $this->get_assigned_quotes( $set_id );
		}

		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/edit-set.php';
	}

	/**
	 * Get quotes assigned to a set.
	 *
	 * @since 1.0.0
	 * @param int $set_id Set ID.
	 * @return array Array of quote objects.
	 */
	private function get_assigned_quotes( $set_id ) {
		global $wpdb;

		$quotes_table = $wpdb->prefix . 'quoteflex_quotes';
		$relationships_table = $wpdb->prefix . 'quoteflex_set_relationships';

		$quotes = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT q.* 
				FROM $quotes_table q
				INNER JOIN $relationships_table r ON q.id = r.quote_id
				WHERE r.set_id = %d
				ORDER BY q.date_added DESC",
				$set_id
			)
		);

		return $quotes ? $quotes : array();
	}
}
