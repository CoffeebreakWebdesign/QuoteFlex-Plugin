<?php
/**
 * Minimal Quote Template.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="quoteflex-quote quoteflex-template-minimal" data-quote-id="<?php echo esc_attr( $quote->id ); ?>">
	<blockquote class="quoteflex-text">
		<?php echo esc_html( $quote->quote_text ); ?>
	</blockquote>
	
	<?php if ( $show_author ) : ?>
		<cite class="quoteflex-author">
			— <?php echo esc_html( $quote->author ); ?>
		</cite>
	<?php endif; ?>
	
	<?php if ( $enable_refresh ) : ?>
		<button 
			class="quoteflex-refresh" 
			data-set="<?php echo esc_attr( $set_slug ); ?>"
			data-template="minimal"
			data-show-author="<?php echo esc_attr( $show_author ? 'true' : 'false' ); ?>"
			data-show-source="<?php echo esc_attr( $show_source ? 'true' : 'false' ); ?>"
			data-animation="<?php echo esc_attr( $animation ); ?>"
		>
			↻
		</button>
	<?php endif; ?>
</div>
