<?php
/**
 * Quote Sets Template.
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
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Quote Sets', 'quoteflex' ); ?></h1>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-sets&action=create' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Create New Set', 'quoteflex' ); ?>
	</a>
	<hr class="wp-header-end">

	<?php
	// Success messages.
	if ( isset( $_GET['created'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Quote set created successfully!', 'quoteflex' ) . '</p></div>';
	}
	if ( isset( $_GET['updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Quote set updated successfully!', 'quoteflex' ) . '</p></div>';
	}
	if ( isset( $_GET['deleted'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Quote set deleted successfully!', 'quoteflex' ) . '</p></div>';
	}
	if ( isset( $_GET['error'] ) ) {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'An error occurred. Please try again.', 'quoteflex' ) . '</p></div>';
	}
	?>

	<?php if ( empty( $sets ) ) : ?>
		<div class="quoteflex-empty-state" style="background: #fff; padding: 40px 20px; border: 1px solid #ccd0d4; border-radius: 4px; text-align: center; margin-top: 20px;">
			<p style="font-size: 16px; color: #646970; margin-bottom: 20px;">
				<?php esc_html_e( 'No quote sets yet. Create your first set to organize your quotes!', 'quoteflex' ); ?>
			</p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-sets&action=create' ) ); ?>" class="button button-primary button-large">
				<?php esc_html_e( 'Create Your First Set', 'quoteflex' ); ?>
			</a>
		</div>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
			<thead>
				<tr>
					<th scope="col" style="width: 40%;"><?php esc_html_e( 'Set Name', 'quoteflex' ); ?></th>
					<th scope="col" style="width: 15%;"><?php esc_html_e( 'Slug', 'quoteflex' ); ?></th>
					<th scope="col" style="width: 15%;"><?php esc_html_e( 'Quotes', 'quoteflex' ); ?></th>
					<th scope="col" style="width: 15%;"><?php esc_html_e( 'Date Created', 'quoteflex' ); ?></th>
					<th scope="col" style="width: 15%;"><?php esc_html_e( 'Actions', 'quoteflex' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $sets as $set ) : ?>
					<tr>
						<td>
							<strong>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-sets&action=edit&id=' . $set->id ) ); ?>">
									<?php echo esc_html( $set->set_name ); ?>
								</a>
							</strong>
							<?php if ( $set->description ) : ?>
								<br>
								<span style="color: #646970; font-size: 13px;"><?php echo esc_html( wp_trim_words( $set->description, 15 ) ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<code><?php echo esc_html( $set->set_slug ); ?></code>
						</td>
						<td>
							<?php echo esc_html( number_format_i18n( $set->quote_count ) ); ?>
						</td>
						<td>
							<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $set->date_created ) ) ); ?>
						</td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-sets&action=edit&id=' . $set->id ) ); ?>" class="button button-small">
								<?php esc_html_e( 'Edit', 'quoteflex' ); ?>
							</a>
							<a 
								href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=quoteflex-sets&action=delete&id=' . $set->id ), 'quoteflex_delete_set_' . $set->id ) ); ?>" 
								class="button button-small button-link-delete" 
								onclick="return confirm('<?php esc_attr_e( 'Are you sure? This will not delete the quotes, only the set.', 'quoteflex' ); ?>');"
							>
								<?php esc_html_e( 'Delete', 'quoteflex' ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div style="margin-top: 20px; padding: 15px; background: #f0f0f1; border-left: 4px solid #2271b1; border-radius: 2px;">
			<p style="margin: 0;">
				<strong><?php esc_html_e( 'How to use quote sets:', 'quoteflex' ); ?></strong><br>
				<?php esc_html_e( 'Use quote sets to organize quotes for different pages or contexts. Display them using shortcode:', 'quoteflex' ); ?>
				<code>[quoteflex set="your-set-slug"]</code>
			</p>
		</div>
	<?php endif; ?>
</div>
