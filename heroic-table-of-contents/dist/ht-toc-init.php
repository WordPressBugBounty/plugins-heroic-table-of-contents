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
 * Check if we're in the WordPress Customizer (any context).
 * Used to prevent conflicts with theme customizers (e.g., OceanWP).
 */
if ( ! function_exists( 'ht_toc_is_customizer' ) ) {
	function ht_toc_is_customizer() {
		// Check for Customizer preview (frontend)
		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
			return true;
		}
		// Check for Customizer global (both panel and preview)
		if ( isset( $GLOBALS['wp_customize'] ) ) {
			return true;
		}
		return false;
	}
}

/**
 * Loads Editor-only assets (Block JS + Editor-only CSS).
 * Hooked to: enqueue_block_editor_assets (editor only)
 */
if ( ! function_exists( 'ht_toc_cgb_block_editor_assets' ) ) {
	function ht_toc_cgb_block_editor_assets() {
		// Skip loading in Customizer to avoid conflicts with theme customizers (e.g., OceanWP)
		if ( ht_toc_is_customizer() ) {
			return;
		}

		// Load this block in both post editor and site editor
		$current_screen = function_exists('get_current_screen') ? get_current_screen() : false;
		
		// Allow in post editor and site editor, but exclude from widgets screen
		if( $current_screen && is_a($current_screen, 'WP_Screen') && 'widgets' === $current_screen->base ){
			return;
		}
		
		// Block Javascript - Editor only
		wp_enqueue_script(
			'ht_toc-block-js',
			plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ),
			true // Load in the footer.
		);

		// Editor-only styles (contains editor UI components, NOT loaded on frontend)
		wp_enqueue_style(
			'ht_toc-block-editor-css',
			plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' )
		);

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
 * Loads Frontend + Editor shared assets (JS functionality + Shared CSS).
 * Hooked to: enqueue_block_assets (both frontend and editor)
 */
if ( ! function_exists( 'ht_toc_cgb_block_assets' ) ) {
	function ht_toc_cgb_block_assets() {
		// Skip JavaScript in Customizer to avoid conflicts with theme customizers (e.g., OceanWP)
		// CSS is still loaded so TOC appears styled in Customizer preview
		if ( ! ht_toc_is_customizer() ) {
			// Frontend Javascript - TOC functionality (skip in Customizer)
			wp_enqueue_script(
				'ht_toc-script-js',
				plugins_url( '/dist/script.min.js', dirname( __FILE__ ) ),
				array(),
				filemtime( plugin_dir_path( __DIR__ ) . 'dist/script.min.js' ),
				true // Load in the footer.
			);
		}

		// Shared styles - Frontend and editor (always load, including Customizer)
		wp_enqueue_style(
			'ht_toc-style-css',
			plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ),
			array(),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' )
		);
	}
}
// Hook: enqueue_block_assets - Loads on both frontend and editor
add_action( 'enqueue_block_assets', 'ht_toc_cgb_block_assets' );