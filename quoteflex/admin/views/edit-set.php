<?php
/**
 * Edit Set Template.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_title = $is_edit ? __( 'Edit Quote Set', 'quoteflex' ) : __( 'Create New Quote Set', 'quoteflex' );
$button_text = $is_edit ? __( 'Update Set', 'quoteflex' ) : __( 'Create Set', 'quoteflex' );
?>

<div class="wrap">
	<h1><?php echo esc_html( $page_title ); ?></h1>

	<?php settings_errors( 'quoteflex' ); ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'quoteflex_edit_set', 'quoteflex_nonce' ); ?>
		<?php if ( $is_edit ) : ?>
			<input type="hidden" name="set_id" value="<?php echo esc_attr( $set->id ); ?>">
		<?php endif; ?>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="set_name"><?php esc_html_e( 'Set Name', 'quoteflex' ); ?> <span class="required">*</span></label>
					</th>
					<td>
						<input 
							type="text" 
							name="set_name" 
							id="set_name" 
							class="regular-text" 
							value="<?php echo $is_edit ? esc_attr( $set->set_name ) : ''; ?>" 
							required
						/>
						<p class="description"><?php esc_html_e( 'Enter the name of this quote set.', 'quoteflex' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="set_slug"><?php esc_html_e( 'Slug', 'quoteflex' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							name="set_slug" 
							id="set_slug" 
							class="regular-text" 
							value="<?php echo $is_edit ? esc_attr( $set->set_slug ) : ''; ?>" 
						/>
						<p class="description">
							<?php esc_html_e( 'URL-friendly version of the name. Leave blank to auto-generate.', 'quoteflex' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="description"><?php esc_html_e( 'Description', 'quoteflex' ); ?></label>
					</th>
					<td>
						<textarea 
							name="description" 
							id="description" 
							rows="3" 
							class="large-text"
						><?php echo $is_edit ? esc_textarea( $set->description ) : ''; ?></textarea>
						<p class="description"><?php esc_html_e( 'Optional description of this quote set.', 'quoteflex' ); ?></p>
					</td>
				</tr>

				<?php if ( $is_edit ) : ?>
				<tr>
					<th scope="row">
						<?php esc_html_e( 'Shortcode', 'quoteflex' ); ?>
					</th>
					<td>
						<code>[quoteflex set="<?php echo esc_attr( $set->set_slug ); ?>"]</code>
						<p class="description"><?php esc_html_e( 'Use this shortcode to display quotes from this set.', 'quoteflex' ); ?></p>
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<p class="submit">
			<input 
				type="submit" 
				name="quoteflex_save_set" 
				class="button button-primary" 
				value="<?php echo esc_attr( $button_text ); ?>"
			/>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-sets' ) ); ?>" class="button">
				<?php esc_html_e( 'Cancel', 'quoteflex' ); ?>
			</a>
			<?php if ( $is_edit ) : ?>
				<a 
					href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=quoteflex-sets&action=delete&id=' . $set->id ), 'quoteflex_delete_set_' . $set->id ) ); ?>" 
					class="button button-link-delete" 
					style="margin-left: 20px;"
					onclick="return confirm('<?php esc_attr_e( 'Are you sure? This will not delete the quotes, only the set.', 'quoteflex' ); ?>');"
				>
					<?php esc_html_e( 'Delete Set', 'quoteflex' ); ?>
				</a>
			<?php endif; ?>
		</p>
	</form>

	<?php if ( $is_edit ) : ?>
		<hr style="margin: 40px 0;">

		<!-- Assigned Quotes Section -->
		<div class="quoteflex-assigned-quotes">
			<h2><?php esc_html_e( 'Quotes in This Set', 'quoteflex' ); ?> (<?php echo count( $assigned_quotes ); ?>)</h2>

			<?php if ( empty( $assigned_quotes ) ) : ?>
				<div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
					<p style="margin: 0; color: #646970;">
						<?php esc_html_e( 'No quotes assigned to this set yet. You can assign quotes when adding or editing them.', 'quoteflex' ); ?>
					</p>
				</div>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th style="width: 60%;"><?php esc_html_e( 'Quote', 'quoteflex' ); ?></th>
							<th style="width: 25%;"><?php esc_html_e( 'Author', 'quoteflex' ); ?></th>
							<th style="width: 15%;"><?php esc_html_e( 'Actions', 'quoteflex' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $assigned_quotes as $quote ) : ?>
							<tr>
								<td>
									<?php echo esc_html( wp_trim_words( $quote->quote_text, 15 ) ); ?>
								</td>
								<td>
									<?php echo esc_html( $quote->author ); ?>
								</td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-all-quotes&action=edit&id=' . $quote->id ) ); ?>" class="button button-small">
										<?php esc_html_e( 'Edit', 'quoteflex' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
