<?php
/**
 * Block class.
 *
 * @package SiteCounts
 */

namespace XWP\SiteCounts;

use WP_Block;

/**
 * The Site Counts dynamic block.
 *
 * Registers and renders the dynamic block.
 */
class Block {

	/**
	 * The Plugin instance.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Instantiates the class.
	 *
	 * @param Plugin $plugin The plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Adds the action to register the block.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Registers the block.
	 */
	public function register_block() {
		register_block_type_from_metadata(
			$this->plugin->dir(),
			array(
				'render_callback' => array( $this, 'render_callback' ),
			)
		);
	}

	/**
	 * Renders the block.
	 *
	 * @param array    $attributes The attributes for the block.
	 * @param string   $content    The block content, if any.
	 * @param WP_Block $block      The instance of this block.
	 * @return string The markup of the block.
	 */
	public function render_callback( $attributes, $content, $block ) {
		$post_types = get_post_types( array( 'public' => true ) );
		$class_name = isset( $attributes['className'] ) ? $attributes['className'] : '';
		$post_id    = isset( $_GET['post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : ''; // phpcs:ignore
		ob_start();

		?>
		<div class="<?php echo esc_attr( $class_name ); ?>">
			<h2>Post Counts</h2>
			<?php
			if ( $post_types ) {
				foreach ( $post_types as $post_type_slug ) :
					$post_type_object = get_post_type_object( $post_type_slug );
					$post_type_name   = $post_type_object ? esc_attr( $post_type_object->labels->name ) : '';
					$count_posts      = wp_count_posts( $post_type_slug );
					$post_count       = $count_posts ? $count_posts->publish : 0;
					?>
					<p><?php echo 'There are ' . esc_attr( $post_count ) . ' ' . esc_attr( $post_type_name ) . '.'; ?></p>
				<?php endforeach; ?>
			<?php } ?>
			<p><?php echo 'The current post ID is ' . esc_attr( $post_id ) . '.'; ?></p>
		</div>
		<?php

		return ob_get_clean();
	}
}
