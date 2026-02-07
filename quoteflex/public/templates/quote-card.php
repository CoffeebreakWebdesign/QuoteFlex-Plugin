<?php
/**
 * Card Quote Template.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="quoteflex-quote quoteflex-template-card" data-quote-id="<?php echo esc_attr( $quote->id ); ?>">
	<div class="quoteflex-card">
		<div class="quoteflex-card-body">
			<blockquote class="quoteflex-text">
				<?php echo esc_html( $quote->quote_text ); ?>
			</blockquote>
		</div>
		
		<div class="quoteflex-card-footer">
			<?php if ( $show_author ) : ?>
				<cite class="quoteflex-author">
					— <?php echo esc_html( $quote->author ); ?>
					<?php if ( ! empty( $quote->author_description ) ) : ?>
						<span class="quoteflex-author-desc">, <?php echo esc_html( $quote->author_description ); ?></span>
					<?php endif; ?>
				</cite>
			<?php endif; ?>
			
			<?php if ( $show_source && ! empty( $quote->source ) ) : ?>
				<span class="quoteflex-source"><?php echo esc_html( $quote->source ); ?></span>
			<?php endif; ?>
			
			<?php if ( $enable_refresh ) : ?>
				<button 
					class="quoteflex-refresh" 
					data-set="<?php echo esc_attr( $set_slug ); ?>"
					data-template="card"
					data-show-author="<?php echo esc_attr( $show_author ? 'true' : 'false' ); ?>"
					data-show-source="<?php echo esc_attr( $show_source ? 'true' : 'false' ); ?>"
					data-animation="<?php echo esc_attr( $animation ); ?>"
				>
					↻ <?php esc_html_e( 'New Quote', 'quoteflex' ); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
</div>
