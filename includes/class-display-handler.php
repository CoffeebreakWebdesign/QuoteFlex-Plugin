<?php
/**
 * Display Handler Class.
 *
 * Handles frontend display logic for quotes.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Display Handler Class.
 */
class QuoteFlex_Display_Handler {

	/**
	 * Quote manager instance.
	 *
	 * @var QuoteFlex_Quote_Manager
	 */
	private $quote_manager;

	/**
	 * Set manager instance.
	 *
	 * @var QuoteFlex_Set_Manager
	 */
	private $set_manager;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-quote-manager.php';
		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-set-manager.php';
		
		$this->quote_manager = new QuoteFlex_Quote_Manager();
		$this->set_manager = new QuoteFlex_Set_Manager();
	}

	/**
	 * Get random quote from a set.
	 *
	 * @since 1.0.0
	 * @param string $set_slug Set slug.
	 * @return object|null Quote object or null.
	 */
	public function get_random_quote_from_set( $set_slug ) {
		global $wpdb;

		// Get set.
		$set = $this->set_manager->get_by_slug( $set_slug );
		if ( ! $set ) {
			return null;
		}

		// Get random quote from set.
		$quotes_table = $wpdb->prefix . 'quoteflex_quotes';
		$relationships_table = $wpdb->prefix . 'quoteflex_set_relationships';

		$quote = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT q.* 
				FROM $quotes_table q
				INNER JOIN $relationships_table r ON q.id = r.quote_id
				WHERE r.set_id = %d AND q.status = 'active'
				ORDER BY RAND()
				LIMIT 1",
				$set->id
			)
		);

		return $quote;
	}

	/**
	 * Get random quote from category.
	 *
	 * @since 1.0.0
	 * @param string $category Category name.
	 * @return object|null Quote object or null.
	 */
	public function get_random_quote_from_category( $category ) {
		global $wpdb;

		$quotes_table = $wpdb->prefix . 'quoteflex_quotes';

		$quote = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $quotes_table 
				WHERE category = %s AND status = 'active'
				ORDER BY RAND()
				LIMIT 1",
				$category
			)
		);

		return $quote;
	}

	/**
	 * Get random quote (any).
	 *
	 * @since 1.0.0
	 * @return object|null Quote object or null.
	 */
	public function get_random_quote() {
		global $wpdb;

		$quotes_table = $wpdb->prefix . 'quoteflex_quotes';

		$quote = $wpdb->get_row(
			"SELECT * FROM $quotes_table 
			WHERE status = 'active'
			ORDER BY RAND()
			LIMIT 1"
		);

		return $quote;
	}

	/**
	 * Render quote template.
	 *
	 * @since 1.0.0
	 * @param object $quote Quote object.
	 * @param array  $args Display arguments.
	 * @return string HTML output.
	 */
	public function render_quote( $quote, $args = array() ) {
		if ( ! $quote ) {
			return '<p>' . esc_html__( 'No quotes found.', 'quoteflex' ) . '</p>';
		}

		$defaults = array(
			'template'      => 'default',
			'show_author'   => true,
			'show_source'   => false,
			'enable_refresh' => true,
			'set'           => '',
			'animation'     => 'fade',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get template file.
		$template_file = $this->get_template_file( $args['template'] );

		if ( ! file_exists( $template_file ) ) {
			return '<p>' . esc_html__( 'Template not found.', 'quoteflex' ) . '</p>';
		}

		// Extract variables for template.
		$show_author = $args['show_author'];
		$show_source = $args['show_source'];
		$enable_refresh = $args['enable_refresh'];
		$set_slug = $args['set'];
		$animation = $args['animation'];

		// Start output buffering.
		ob_start();
		include $template_file;
		$output = ob_get_clean();

		// Add animation wrapper if needed.
		if ( $animation !== 'none' ) {
			$output = '<div class="quoteflex-animation quoteflex-animation-' . esc_attr( $animation ) . '">' . $output . '</div>';
		}

		return $output;
	}

	/**
	 * Get template file path.
	 *
	 * @since 1.0.0
	 * @param string $template Template name.
	 * @return string Template file path.
	 */
	private function get_template_file( $template ) {
		$template = sanitize_file_name( $template );
		$template_file = QUOTEFLEX_PLUGIN_DIR . 'public/templates/quote-' . $template . '.php';

		// Allow themes to override templates.
		$theme_template = get_stylesheet_directory() . '/quoteflex/quote-' . $template . '.php';
		if ( file_exists( $theme_template ) ) {
			return $theme_template;
		}

		return $template_file;
	}

	/**
	 * Get quote by parameters.
	 *
	 * @since 1.0.0
	 * @param array $args Query arguments.
	 * @return object|null Quote object or null.
	 */
	public function get_quote( $args = array() ) {
		$defaults = array(
			'set'      => '',
			'category' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get by set.
		if ( ! empty( $args['set'] ) ) {
			return $this->get_random_quote_from_set( $args['set'] );
		}

		// Get by category.
		if ( ! empty( $args['category'] ) ) {
			return $this->get_random_quote_from_category( $args['category'] );
		}

		// Get random.
		return $this->get_random_quote();
	}
}
