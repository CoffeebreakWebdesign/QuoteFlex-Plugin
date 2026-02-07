<?php
/**
 * Edit Quote Template.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if quote exists.
if ( ! $quote ) {
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Edit Quote', 'quoteflex' ) . '</h1>';
	echo '<p>' . esc_html__( 'Quote not found.', 'quoteflex' ) . '</p>';
	echo '</div>';
	return;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Edit Quote', 'quoteflex' ); ?></h1>

	<?php settings_errors( 'quoteflex' ); ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'quoteflex_edit_quote', 'quoteflex_nonce' ); ?>
		<input type="hidden" name="quote_id" value="<?php echo esc_attr( $quote->id ); ?>">

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="quote_text"><?php esc_html_e( 'Quote Text', 'quoteflex' ); ?> <span class="required">*</span></label>
					</th>
					<td>
						<textarea 
							name="quote_text" 
							id="quote_text" 
							rows="5" 
							class="large-text" 
							required
						><?php echo esc_textarea( $quote->quote_text ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Enter the quote text.', 'quoteflex' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="author"><?php esc_html_e( 'Author', 'quoteflex' ); ?> <span class="required">*</span></label>
					</th>
					<td>
						<input 
							type="text" 
							name="author" 
							id="author" 
							class="regular-text" 
							value="<?php echo esc_attr( $quote->author ); ?>" 
							required
						/>
						<p class="description"><?php esc_html_e( 'Enter the author name.', 'quoteflex' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="author_description"><?php esc_html_e( 'Author Description', 'quoteflex' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							name="author_description" 
							id="author_description" 
							class="regular-text" 
							value="<?php echo esc_attr( $quote->author_description ); ?>" 
						/>
						<p class="description"><?php esc_html_e( 'Optional description (e.g., "British Prime Minister").', 'quoteflex' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="source"><?php esc_html_e( 'Source', 'quoteflex' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							name="source" 
							id="source" 
							class="regular-text" 
							value="<?php echo esc_attr( $quote->source ); ?>" 
						/>
						<p class="description"><?php esc_html_e( 'Optional source (e.g., book name, speech).', 'quoteflex' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="category"><?php esc_html_e( 'Category', 'quoteflex' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							name="category" 
							id="category" 
							class="regular-text" 
							value="<?php echo esc_attr( $quote->category ); ?>" 
						/>
						<p class="description"><?php esc_html_e( 'Optional category (e.g., Inspirational, Leadership).', 'quoteflex' ); ?></p>
					</td>
				</tr>

				<?php if ( ! empty( $sets ) ) : ?>
				<tr>
					<th scope="row">
						<?php esc_html_e( 'Assign to Sets', 'quoteflex' ); ?>
					</th>
					<td>
						<fieldset>
							<?php foreach ( $sets as $set ) : ?>
								<label>
									<input 
										type="checkbox" 
										name="quote_sets[]" 
										value="<?php echo esc_attr( $set->id ); ?>"
										<?php checked( in_array( $set->id, $assigned_sets ) ); ?>
									/>
									<?php echo esc_html( $set->set_name ); ?>
								</label><br>
							<?php endforeach; ?>
						</fieldset>
						<p class="description"><?php esc_html_e( 'Select which quote sets this quote belongs to.', 'quoteflex' ); ?></p>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<th scope="row">
						<?php esc_html_e( 'Status', 'quoteflex' ); ?>
					</th>
					<td>
						<fieldset>
							<label>
								<input 
									type="radio" 
									name="status" 
									value="active" 
									<?php checked( $quote->status, 'active' ); ?>
								/>
								<?php esc_html_e( 'Active', 'quoteflex' ); ?>
							</label><br>
							<label>
								<input 
									type="radio" 
									name="status" 
									value="inactive"
									<?php checked( $quote->status, 'inactive' ); ?>
								/>
								<?php esc_html_e( 'Inactive', 'quoteflex' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<?php esc_html_e( 'Quote Info', 'quoteflex' ); ?>
					</th>
					<td>
						<p>
							<strong><?php esc_html_e( 'Source Type:', 'quoteflex' ); ?></strong> 
							<?php echo esc_html( ucfirst( $quote->source_type ) ); ?>
							<?php if ( $quote->api_source ) : ?>
								(<?php echo esc_html( $quote->api_source ); ?>)
							<?php endif; ?>
						</p>
						<p>
							<strong><?php esc_html_e( 'Date Added:', 'quoteflex' ); ?></strong> 
							<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $quote->date_added ) ) ); ?>
						</p>
						<p>
							<strong><?php esc_html_e( 'Last Modified:', 'quoteflex' ); ?></strong> 
							<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $quote->date_modified ) ) ); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input 
				type="submit" 
				name="quoteflex_update_quote" 
				class="button button-primary" 
				value="<?php esc_attr_e( 'Update Quote', 'quoteflex' ); ?>"
			/>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-all-quotes' ) ); ?>" class="button">
				<?php esc_html_e( 'Cancel', 'quoteflex' ); ?>
			</a>
			<a 
				href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=quoteflex-all-quotes&action=delete&id=' . $quote->id ), 'quoteflex_delete_quote_' . $quote->id ) ); ?>" 
				class="button button-link-delete" 
				onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this quote?', 'quoteflex' ); ?>');"
			>
				<?php esc_html_e( 'Delete Quote', 'quoteflex' ); ?>
			</a>
		</p>
	</form>
</div>
