<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads Editor assets.
 */
if ( ! function_exists( 'ht_toc_cgb_block_editor_assets' ) ) {
	function ht_toc_cgb_block_editor_assets() {
		// Load this block in both post editor and site editor
		$current_screen = function_exists('get_current_screen') ? get_current_screen() : false;
		
		// Allow in post editor and site editor, but exclude from widgets screen
		if( $current_screen && is_a($current_screen, 'WP_Screen') && 'widgets' === $current_screen->base ){
			return;
		}
		
		// Javascript.
		wp_enqueue_script(
			'ht_toc-block-js',
			plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ),
			true // Load in the footer.
		);

		// Styles.
		wp_enqueue_style(
			'ht_toc-block-editor-css',
			plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' )
		);

		// Add editor styles to iframe for proper block rendering
		add_editor_style( plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ) );

		// Plugin script localization (passes info from PHP to JS).
		wp_localize_script(
			'ht_toc-block-js',
			'ht_toc_global',
			array(
				'pluginDirPath' => plugin_dir_path( __DIR__ ),
				'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			)
		);
	}
}
// Hook: Editor assets. (needs frontend and backend)
add_action( 'enqueue_block_editor_assets', 'ht_toc_cgb_block_editor_assets' );
add_action( 'enqueue_block_editor_assets', 'ht_toc_cgb_block_assets' );

/**
 * Loads Frontend assets.
 */
if ( ! function_exists( 'ht_toc_cgb_block_assets' ) ) {
	function ht_toc_cgb_block_assets() {
		// Javascript.
		wp_enqueue_script(
			'ht_toc-script-js',
			plugins_url( '/dist/script.min.js', dirname( __FILE__ ) ),
			array(),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/script.min.js' ),
			true // Load in the footer.
		);

		// Styles.
		wp_enqueue_style(
			'ht_toc-style-css',
			plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ),
			array(),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' )
		);
	}
}
// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'ht_toc_cgb_block_assets' );