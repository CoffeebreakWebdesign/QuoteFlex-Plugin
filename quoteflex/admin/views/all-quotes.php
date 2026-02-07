<?php
/**
 * All Quotes Template.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'All Quotes', 'quoteflex' ); ?></h1>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-add-quote' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Add New', 'quoteflex' ); ?>
	</a>
	<hr class="wp-header-end">

	<?php
	// Success messages.
	if ( isset( $_GET['added'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Quote added successfully!', 'quoteflex' ) . '</p></div>';
	}
	if ( isset( $_GET['deleted'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Quote deleted successfully!', 'quoteflex' ) . '</p></div>';
	}
	if ( isset( $_GET['bulk_action'] ) && isset( $_GET['count'] ) ) {
		$action = sanitize_text_field( $_GET['bulk_action'] );
		$count = absint( $_GET['count'] );
		/* translators: %d: Number of quotes affected */
		$message = sprintf( _n( '%d quote updated.', '%d quotes updated.', $count, 'quoteflex' ), $count );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
	}
	if ( isset( $_GET['error'] ) ) {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'An error occurred. Please try again.', 'quoteflex' ) . '</p></div>';
	}
	?>

	<form method="get">
		<input type="hidden" name="page" value="quoteflex-all-quotes">
		<?php
		$list_table->search_box( __( 'Search Quotes', 'quoteflex' ), 'quote' );
		?>
	</form>

	<form method="post">
		<?php
		$list_table->display();
		?>
	</form>
</div>
