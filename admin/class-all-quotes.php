<?php
/**
 * All Quotes Class.
 *
 * Displays all quotes in a list table.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * QuoteFlex All Quotes Class.
 */
class QuoteFlex_All_Quotes {

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

		$this->process_actions();
	}

	/**
	 * Process bulk and single actions.
	 *
	 * @since 1.0.0
	 */
	private function process_actions() {
		// Handle single delete.
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
			$this->delete_quote( absint( $_GET['id'] ) );
		}

		// Handle bulk actions.
		if ( isset( $_POST['action'] ) && $_POST['action'] !== '-1' && isset( $_POST['quotes'] ) ) {
			$this->process_bulk_action();
		}
	}

	/**
	 * Delete a single quote.
	 *
	 * @since 1.0.0
	 * @param int $quote_id Quote ID.
	 */
	private function delete_quote( $quote_id ) {
		// Verify nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'quoteflex_delete_quote_' . $quote_id ) ) {
			wp_die( esc_html__( 'Security check failed', 'quoteflex' ) );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions', 'quoteflex' ) );
		}

		// Delete quote.
		$deleted = $this->quote_manager->delete( $quote_id );

		if ( $deleted ) {
			wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-all-quotes&deleted=1' ) );
		} else {
			wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-all-quotes&error=1' ) );
		}
		exit;
	}

	/**
	 * Process bulk actions.
	 *
	 * @since 1.0.0
	 */
	private function process_bulk_action() {
		// Verify nonce.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'bulk-quotes' ) ) {
			wp_die( esc_html__( 'Security check failed', 'quoteflex' ) );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions', 'quoteflex' ) );
		}

		$action   = sanitize_text_field( $_POST['action'] );
		$quote_ids = array_map( 'absint', $_POST['quotes'] );

		$count = 0;

		foreach ( $quote_ids as $quote_id ) {
			switch ( $action ) {
				case 'delete':
					if ( $this->quote_manager->delete( $quote_id ) ) {
						$count++;
					}
					break;

				case 'activate':
					if ( $this->quote_manager->update( $quote_id, array( 'status' => 'active' ) ) ) {
						$count++;
					}
					break;

				case 'deactivate':
					if ( $this->quote_manager->update( $quote_id, array( 'status' => 'inactive' ) ) ) {
						$count++;
					}
					break;
			}
		}

		wp_safe_redirect( admin_url( 'admin.php?page=quoteflex-all-quotes&bulk_action=' . $action . '&count=' . $count ) );
		exit;
	}

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$list_table = new QuoteFlex_Quotes_List_Table( $this->quote_manager );
		$list_table->prepare_items();

		include QUOTEFLEX_PLUGIN_DIR . 'admin/views/all-quotes.php';
	}
}

/**
 * QuoteFlex Quotes List Table Class.
 */
class QuoteFlex_Quotes_List_Table extends WP_List_Table {

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
	 * @param QuoteFlex_Quote_Manager $quote_manager Quote manager instance.
	 */
	public function __construct( $quote_manager ) {
		$this->quote_manager = $quote_manager;

		parent::__construct( array(
			'singular' => 'quote',
			'plural'   => 'quotes',
			'ajax'     => false,
		) );
	}

	/**
	 * Get columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'quote_text'  => __( 'Quote', 'quoteflex' ),
			'author'      => __( 'Author', 'quoteflex' ),
			'category'    => __( 'Category', 'quoteflex' ),
			'source_type' => __( 'Source', 'quoteflex' ),
			'status'      => __( 'Status', 'quoteflex' ),
			'date_added'  => __( 'Date Added', 'quoteflex' ),
		);
	}

	/**
	 * Get sortable columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'author'     => array( 'author', false ),
			'date_added' => array( 'date_added', true ),
		);
	}

	/**
	 * Get bulk actions.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'delete'     => __( 'Delete', 'quoteflex' ),
			'activate'   => __( 'Activate', 'quoteflex' ),
			'deactivate' => __( 'Deactivate', 'quoteflex' ),
		);
	}

	/**
	 * Prepare items for display.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$per_page = 20;
		$current_page = $this->get_pagenum();

		// Get filters.
		$search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
		$status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date_added';
		$order = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';

		$args = array(
			'search'  => $search,
			'status'  => $status,
			'orderby' => $orderby,
			'order'   => $order,
			'limit'   => $per_page,
			'offset'  => ( $current_page - 1 ) * $per_page,
		);

		// Get quotes.
		$this->items = $this->quote_manager->get_all( $args );

		// Get total count.
		$total_items = $this->quote_manager->count( array(
			'search' => $search,
			'status' => $status,
		) );

		// Set pagination.
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
	}

	/**
	 * Checkbox column.
	 *
	 * @since 1.0.0
	 * @param object $item Item.
	 * @return string
	 */
	protected function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="quotes[]" value="%d" />', $item->id );
	}

	/**
	 * Quote text column.
	 *
	 * @since 1.0.0
	 * @param object $item Item.
	 * @return string
	 */
	protected function column_quote_text( $item ) {
		$edit_url = admin_url( 'admin.php?page=quoteflex-all-quotes&action=edit&id=' . $item->id );
		$delete_url = wp_nonce_url(
			admin_url( 'admin.php?page=quoteflex-all-quotes&action=delete&id=' . $item->id ),
			'quoteflex_delete_quote_' . $item->id
		);

		$excerpt = wp_trim_words( $item->quote_text, 15 );

		$actions = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'quoteflex' ) ),
			'delete' => sprintf( '<a href="%s" onclick="return confirm(\'%s\');">%s</a>', 
				esc_url( $delete_url ), 
				esc_attr__( 'Are you sure?', 'quoteflex' ),
				__( 'Delete', 'quoteflex' )
			),
		);

		return sprintf( '<strong>%s</strong> %s', esc_html( $excerpt ), $this->row_actions( $actions ) );
	}

	/**
	 * Default column.
	 *
	 * @since 1.0.0
	 * @param object $item Item.
	 * @param string $column_name Column name.
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'author':
				return esc_html( $item->author );
			case 'category':
				return esc_html( $item->category ? $item->category : '—' );
			case 'source_type':
				return esc_html( ucfirst( $item->source_type ) );
			case 'status':
				return $item->status === 'active' 
					? '<span style="color: green;">●</span> ' . esc_html__( 'Active', 'quoteflex' )
					: '<span style="color: red;">●</span> ' . esc_html__( 'Inactive', 'quoteflex' );
			case 'date_added':
				return esc_html( date_i18n( get_option( 'date_format' ), strtotime( $item->date_added ) ) );
			default:
				return '';
		}
	}

	/**
	 * No items message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No quotes found.', 'quoteflex' );
	}
}
