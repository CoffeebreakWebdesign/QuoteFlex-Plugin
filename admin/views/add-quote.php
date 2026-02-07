<?php
/**
 * Add New Quote Template.
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
	<h1><?php esc_html_e( 'Add New Quote', 'quoteflex' ); ?></h1>

	<?php settings_errors( 'quoteflex' ); ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'quoteflex_add_quote', 'quoteflex_nonce' ); ?>

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
						><?php echo isset( $_POST['quote_text'] ) ? esc_textarea( $_POST['quote_text'] ) : ''; ?></textarea>
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
							value="<?php echo isset( $_POST['author'] ) ? esc_attr( $_POST['author'] ) : ''; ?>" 
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
							value="<?php echo isset( $_POST['author_description'] ) ? esc_attr( $_POST['author_description'] ) : ''; ?>" 
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
							value="<?php echo isset( $_POST['source'] ) ? esc_attr( $_POST['source'] ) : ''; ?>" 
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
							value="<?php echo isset( $_POST['category'] ) ? esc_attr( $_POST['category'] ) : ''; ?>" 
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
									<?php checked( ! isset( $_POST['status'] ) || $_POST['status'] === 'active' ); ?>
								/>
								<?php esc_html_e( 'Active', 'quoteflex' ); ?>
							</label><br>
							<label>
								<input 
									type="radio" 
									name="status" 
									value="inactive"
									<?php checked( isset( $_POST['status'] ) && $_POST['status'] === 'inactive' ); ?>
								/>
								<?php esc_html_e( 'Inactive', 'quoteflex' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input 
				type="submit" 
				name="quoteflex_add_quote" 
				class="button button-primary" 
				value="<?php esc_attr_e( 'Add Quote', 'quoteflex' ); ?>"
			/>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflex-all-quotes' ) ); ?>" class="button">
				<?php esc_html_e( 'Cancel', 'quoteflex' ); ?>
			</a>
		</p>
	</form>
</div>
