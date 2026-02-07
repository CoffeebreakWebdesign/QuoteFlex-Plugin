<?php
/**
 * Gutenberg Block Class.
 *
 * Registers the QuoteFlex Gutenberg block.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Block Class.
 */
class QuoteFlex_Block {

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

		// Register block.
		add_action( 'init', array( $this, 'register_block' ) );

		// Enqueue block editor assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the Gutenberg block.
	 *
	 * @since 1.0.0
	 */
	public function register_block() {
		register_block_type( 'quoteflex/quote-display', array(
			'attributes'      => array(
				'set'           => array(
					'type'    => 'string',
					'default' => '',
				),
				'category'      => array(
					'type'    => 'string',
					'default' => '',
				),
				'template'      => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'showAuthor'    => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showSource'    => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'enableRefresh' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'animation'     => array(
					'type'    => 'string',
					'default' => 'fade',
				),
			),
			'render_callback' => array( $this, 'render_block' ),
		) );
	}

	/**
	 * Render block callback.
	 *
	 * @since 1.0.0
	 * @param array $attributes Block attributes.
	 * @return string Block output.
	 */
	public function render_block( $attributes ) {
		// Get quote.
		$quote = $this->display_handler->get_quote(
			array(
				'set'      => $attributes['set'],
				'category' => $attributes['category'],
			)
		);

		// Prepare display arguments.
		$display_args = array(
			'template'       => $attributes['template'],
			'show_author'    => $attributes['showAuthor'],
			'show_source'    => $attributes['showSource'],
			'enable_refresh' => $attributes['enableRefresh'],
			'set'            => $attributes['set'],
			'animation'      => $attributes['animation'],
		);

		// Render quote.
		return $this->display_handler->render_quote( $quote, $display_args );
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_script(
			'quoteflex-block-editor',
			QUOTEFLEX_PLUGIN_URL . 'assets/js/block-editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
			QUOTEFLEX_VERSION,
			true
		);

		wp_enqueue_style(
			'quoteflex-block-editor',
			QUOTEFLEX_PLUGIN_URL . 'assets/css/block-editor.css',
			array( 'wp-edit-blocks' ),
			QUOTEFLEX_VERSION
		);

		// Pass available sets to JavaScript.
		$sets = $this->get_available_sets();

		wp_localize_script(
			'quoteflex-block-editor',
			'quoteflexBlock',
			array(
				'sets' => $sets,
			)
		);
	}

	/**
	 * Get available quote sets for block editor.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function get_available_sets() {
		global $wpdb;
		$table = $wpdb->prefix . 'quoteflex_sets';
		
		$sets = $wpdb->get_results( "SELECT id, set_name, set_slug FROM $table ORDER BY set_name ASC" );
		
		if ( ! $sets ) {
			return array();
		}

		// Format for JavaScript.
		$formatted_sets = array();
		foreach ( $sets as $set ) {
			$formatted_sets[] = array(
				'value' => $set->set_slug,
				'label' => $set->set_name,
			);
		}

		return $formatted_sets;
	}
}
