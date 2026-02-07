<?php
/**
 * Shortcode Handler Class.
 *
 * Handles the [quoteflex] shortcode.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Shortcode Class.
 */
class QuoteFlex_Shortcode {

	/**
	 * Display handler instance.
	 *
	 * @var QuoteFlex_Display_Handler
	 */
	private $display_handler;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-display-handler.php';
		$this->display_handler = new QuoteFlex_Display_Handler();

		// Register shortcode.
		add_shortcode( 'quoteflex', array( $this, 'render' ) );

		// Register AJAX handler for refresh.
		add_action( 'wp_ajax_quoteflex_get_random_quote', array( $this, 'ajax_get_random_quote' ) );
		add_action( 'wp_ajax_nopriv_quoteflex_get_random_quote', array( $this, 'ajax_get_random_quote' ) );
	}

	/**
	 * Render shortcode.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode output.
	 */
	public function render( $atts ) {
		// Parse attributes.
		$atts = shortcode_atts(
			array(
				'set'           => '',
				'category'      => '',
				'template'      => 'default',
				'show_author'   => 'yes',
				'show_source'   => 'no',
				'refresh'       => 'yes',
				'animation'     => 'fade',
			),
			$atts,
			'quoteflex'
		);

		// Get quote.
		$quote = $this->display_handler->get_quote(
			array(
				'set'      => $atts['set'],
				'category' => $atts['category'],
			)
		);

		// Prepare display arguments.
		$display_args = array(
			'template'       => $atts['template'],
			'show_author'    => $atts['show_author'] === 'yes',
			'show_source'    => $atts['show_source'] === 'yes',
			'enable_refresh' => $atts['refresh'] === 'yes',
			'set'            => $atts['set'],
			'animation'      => $atts['animation'],
		);

		// Render quote.
		$output = $this->display_handler->render_quote( $quote, $display_args );

		return $output;
	}

	/**
	 * AJAX handler for getting random quote.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_random_quote() {
		// Verify nonce.
		check_ajax_referer( 'quoteflex_ajax_nonce', 'nonce' );

		// Get parameters.
		$set = isset( $_POST['set'] ) ? sanitize_text_field( $_POST['set'] ) : '';
		$category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
		$template = isset( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : 'default';
		$show_author = isset( $_POST['show_author'] ) ? $_POST['show_author'] === 'true' : true;
		$show_source = isset( $_POST['show_source'] ) ? $_POST['show_source'] === 'true' : false;
		$enable_refresh = isset( $_POST['enable_refresh'] ) ? $_POST['enable_refresh'] === 'true' : true;
		$animation = isset( $_POST['animation'] ) ? sanitize_text_field( $_POST['animation'] ) : 'fade';

		// Get quote.
		$quote = $this->display_handler->get_quote(
			array(
				'set'      => $set,
				'category' => $category,
			)
		);

		if ( ! $quote ) {
			wp_send_json_error( array(
				'message' => __( 'No quotes found.', 'quoteflex' ),
			) );
		}

		// Prepare display arguments.
		$display_args = array(
			'template'       => $template,
			'show_author'    => $show_author,
			'show_source'    => $show_source,
			'enable_refresh' => $enable_refresh,
			'set'            => $set,
			'animation'      => $animation,
		);

		// Render quote.
		$html = $this->display_handler->render_quote( $quote, $display_args );

		wp_send_json_success( array(
			'html' => $html,
		) );
	}
}
