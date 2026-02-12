<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Clevers_Product_Carousel_CPT {

	public function init() {
		add_action( 'init', array( $this, 'register_cpt_and_assets' ) );
		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
	}

	public function add_image_sizes() {
		add_image_size(
			'clevers_carousel_thumb',
			330,
			400,
			true // hard crop
		);
	}

	public function register_cpt_and_assets() {
		// Slick desde el propio plugin (no CDN).
		wp_register_style(
			'slick',
			CLV_URL . 'assets/vendor/slick/slick.css',
			array(),
			'1.8.1'
		);

		wp_register_style(
			'slick-theme',
			CLV_URL . 'assets/vendor/slick/slick-theme.css',
			array( 'slick' ),
			'1.8.1'
		);

		wp_register_script(
			'slick',
			CLV_URL . 'assets/vendor/slick/slick.min.js',
			array( 'jquery' ),
			'1.8.1',
			true
		);

		// Tus assets locales con busting por filemtime.
		$css     = CLV_DIR . 'assets/carousel.css';
		$js      = CLV_DIR . 'assets/carousel.js';
		$css_ver = file_exists( $css ) ? filemtime( $css ) : '0.1.0';
		$js_ver  = file_exists( $js ) ? filemtime( $js ) : '0.1.0';

		wp_register_style(
			'clv-carousel',
			CLV_URL . 'assets/carousel.css',
			array( 'slick', 'slick-theme' ),
			$css_ver
		);

		wp_register_script(
			'clv-carousel',
			CLV_URL . 'assets/carousel.js',
			array( 'slick' ),
			$js_ver,
			true
		);

		$labels = array(
			'name'          => __( 'Product Carousels', 'clevers-product-carousel' ),
			'singular_name' => __( 'Product Carousel', 'clevers-product-carousel' ),
			'add_new'       => __( 'Add New', 'clevers-product-carousel' ),
			'add_new_item'  => __( 'Add New Carousel', 'clevers-product-carousel' ),
			'edit_item'     => __( 'Edit Carousel', 'clevers-product-carousel' ),
			'new_item'      => __( 'New Carousel', 'clevers-product-carousel' ),
			'all_items'     => __( 'All Carousels', 'clevers-product-carousel' ),
			'menu_name'     => __( 'Product Carousels', 'clevers-product-carousel' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-images-alt2',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		);

		register_post_type( CLV_SLUG, $args );
	}
}
