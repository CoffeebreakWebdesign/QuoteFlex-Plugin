<?php
/**
 * Widget Class.
 *
 * Displays random quotes in sidebars/widget areas.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Widget Class.
 */
class QuoteFlex_Widget extends WP_Widget {

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
		parent::__construct(
			'quoteflex_widget',
			__( 'QuoteFlex Random Quote', 'quoteflex' ),
			array(
				'description' => __( 'Display a random quote from your collection', 'quoteflex' ),
			)
		);

		require_once QUOTEFLEX_PLUGIN_DIR . 'includes/class-display-handler.php';
		$this->display_handler = new QuoteFlex_Display_Handler();
	}

	/**
	 * Widget output.
	 *
	 * @since 1.0.0
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance settings.
	 */
	public function widget( $args, $instance ) {
		// Get settings.
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$set = ! empty( $instance['set'] ) ? $instance['set'] : '';
		$template = ! empty( $instance['template'] ) ? $instance['template'] : 'minimal';
		$show_author = isset( $instance['show_author'] ) ? (bool) $instance['show_author'] : true;
		$show_source = isset( $instance['show_source'] ) ? (bool) $instance['show_source'] : false;
		$enable_refresh = isset( $instance['enable_refresh'] ) ? (bool) $instance['enable_refresh'] : true;

		// Get quote.
		$quote = $this->display_handler->get_quote(
			array(
				'set' => $set,
			)
		);

		// Start widget output.
		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		// Display quote.
		if ( $quote ) {
			$display_args = array(
				'template'       => $template,
				'show_author'    => $show_author,
				'show_source'    => $show_source,
				'enable_refresh' => $enable_refresh,
				'set'            => $set,
				'animation'      => 'fade',
			);

			echo $this->display_handler->render_quote( $quote, $display_args );
		} else {
			echo '<p>' . esc_html__( 'No quotes available.', 'quoteflex' ) . '</p>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Widget settings form.
	 *
	 * @since 1.0.0
	 * @param array $instance Widget instance settings.
	 * @return string
	 */
	public function form( $instance ) {
		// Get current settings.
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$set = ! empty( $instance['set'] ) ? $instance['set'] : '';
		$template = ! empty( $instance['template'] ) ? $instance['template'] : 'minimal';
		$show_author = isset( $instance['show_author'] ) ? (bool) $instance['show_author'] : true;
		$show_source = isset( $instance['show_source'] ) ? (bool) $instance['show_source'] : false;
		$enable_refresh = isset( $instance['enable_refresh'] ) ? (bool) $instance['enable_refresh'] : true;

		// Get available sets.
		$sets = $this->get_available_sets();
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'quoteflex' ); ?>
			</label>
			<input 
				class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
				type="text" 
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'set' ) ); ?>">
				<?php esc_html_e( 'Quote Set:', 'quoteflex' ); ?>
			</label>
			<select 
				class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'set' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'set' ) ); ?>"
			>
				<option value=""><?php esc_html_e( 'All Quotes (Random)', 'quoteflex' ); ?></option>
				<?php foreach ( $sets as $set_data ) : ?>
					<option value="<?php echo esc_attr( $set_data->set_slug ); ?>" <?php selected( $set, $set_data->set_slug ); ?>>
						<?php echo esc_html( $set_data->set_name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>">
				<?php esc_html_e( 'Template:', 'quoteflex' ); ?>
			</label>
			<select 
				class="widefat" 
				id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>"
			>
				<option value="default" <?php selected( $template, 'default' ); ?>><?php esc_html_e( 'Default', 'quoteflex' ); ?></option>
				<option value="boxed" <?php selected( $template, 'boxed' ); ?>><?php esc_html_e( 'Boxed', 'quoteflex' ); ?></option>
				<option value="card" <?php selected( $template, 'card' ); ?>><?php esc_html_e( 'Card', 'quoteflex' ); ?></option>
				<option value="minimal" <?php selected( $template, 'minimal' ); ?>><?php esc_html_e( 'Minimal', 'quoteflex' ); ?></option>
			</select>
		</p>

		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?php checked( $show_author ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_author' ) ); ?>"
			/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>">
				<?php esc_html_e( 'Show Author', 'quoteflex' ); ?>
			</label>
		</p>

		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?php checked( $show_source ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'show_source' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'show_source' ) ); ?>"
			/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_source' ) ); ?>">
				<?php esc_html_e( 'Show Source', 'quoteflex' ); ?>
			</label>
		</p>

		<p>
			<input 
				class="checkbox" 
				type="checkbox" 
				<?php checked( $enable_refresh ); ?> 
				id="<?php echo esc_attr( $this->get_field_id( 'enable_refresh' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'enable_refresh' ) ); ?>"
			/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'enable_refresh' ) ); ?>">
				<?php esc_html_e( 'Enable Refresh Button', 'quoteflex' ); ?>
			</label>
		</p>

		<?php
	}

	/**
	 * Update widget settings.
	 *
	 * @since 1.0.0
	 * @param array $new_instance New settings.
	 * @param array $old_instance Previous settings.
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['set'] = ! empty( $new_instance['set'] ) ? sanitize_text_field( $new_instance['set'] ) : '';
		$instance['template'] = ! empty( $new_instance['template'] ) ? sanitize_text_field( $new_instance['template'] ) : 'minimal';
		$instance['show_author'] = ! empty( $new_instance['show_author'] ) ? 1 : 0;
		$instance['show_source'] = ! empty( $new_instance['show_source'] ) ? 1 : 0;
		$instance['enable_refresh'] = ! empty( $new_instance['enable_refresh'] ) ? 1 : 0;

		return $instance;
	}

	/**
	 * Get available quote sets.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function get_available_sets() {
		global $wpdb;
		$table = $wpdb->prefix . 'quoteflex_sets';
		
		$sets = $wpdb->get_results( "SELECT set_name, set_slug FROM $table ORDER BY set_name ASC" );
		
		return $sets ? $sets : array();
	}
}

/**
 * Register widget.
 */
function quoteflex_register_widget() {
	register_widget( 'QuoteFlex_Widget' );
}
add_action( 'widgets_init', 'quoteflex_register_widget' );
