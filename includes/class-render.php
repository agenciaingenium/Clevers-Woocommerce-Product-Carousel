<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Clevers_Product_Carousel_Render {

	public function init() {
		add_shortcode( 'clevers_carousel', array( $this, 'shortcode' ) );
		add_action( 'save_post_product', array( $this, 'invalidate_cache' ) );
	}

	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts
		);

		return $this->render_carousel( (int) $atts['id'] );
	}

	public function render_carousel( $carousel_id ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}

		$carousel = get_post( $carousel_id );
		if ( ! $carousel || CLV_SLUG !== $carousel->post_type ) {
			return '';
		}

		$args     = clevers_product_carousel_build_query_args( $carousel_id );
		$settings = clevers_product_carousel_get_settings( $carousel_id );

		wp_enqueue_style( 'slick' );
		wp_enqueue_style( 'slick-theme' );
		wp_enqueue_style( 'clv-carousel' );
		wp_enqueue_script( 'slick' );
		wp_enqueue_script( 'clv-carousel' );

		$vars = array();
		if ( ! empty( $settings['color_primary'] ) ) {
			$vars[] = '--clevers-primary:' . $settings['color_primary'] . ';';
		}
		if ( ! empty( $settings['color_primary2'] ) ) {
			$vars[] = '--clevers-primary-hover:' . $settings['color_primary2'] . ';';
		}
		if ( ! empty( $settings['color_secondary'] ) ) {
			$vars[] = '--clevers-secondary:' . $settings['color_secondary'] . ';';
		}
		if ( ! empty( $settings['color_accent'] ) ) {
			$vars[] = '--clevers-accent:' . $settings['color_accent'] . ';';
		}
		if ( ! empty( $settings['color_text'] ) ) {
			$vars[] = '--clevers-text:' . $settings['color_text'] . ';';
		}
		if ( ! empty( $settings['color_card_bg'] ) ) {
			$vars[] = '--clevers-card-bg:' . $settings['color_card_bg'] . ';';
		}
		if ( ! empty( $settings['color_border'] ) ) {
			$vars[] = '--clevers-border:' . $settings['color_border'] . ';';
		}
		if ( ! empty( $settings['bubble_background'] ) ) {
			$vars[] = '--clevers-bubble-background:' . $settings['bubble_background'] . ';';
		}
		if ( ! empty( $settings['bubble_text'] ) ) {
			$vars[] = '--clevers-bubble-text:' . $settings['bubble_text'] . ';';
		}
		if ( ! empty( $settings['button_background'] ) ) {
			$vars[] = '--clevers-button-background:' . $settings['button_background'] . ';';
		}
		if ( ! empty( $settings['button_text'] ) ) {
			$vars[] = '--clevers-button-text:' . $settings['button_text'] . ';';
		}

		if ( $vars ) {
			$inline = '#clevers-product-carousel-' . (int) $carousel_id . '{' . implode( '', $vars ) . '}';
			wp_add_inline_style( 'clv-carousel', $inline );
		}

		$ver       = (int) get_post_meta( $carousel_id, '_clv_cache_version', true );
		$bump      = (int) get_option( 'clv_global_cache_bump', 0 );
		$cache_key = 'clv_carousel_' . $carousel_id . '_v' . $ver . '_g' . $bump . '_' .
					 md5( wp_json_encode( $args ) . '|' . wp_json_encode( $settings ) );

		$html = get_transient( $cache_key );
		if ( false !== $html ) {
			return $html;
		}

		$products = ( new WC_Product_Query( $args ) )->get_products();

		// Save global product to restore later.
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		global $product;
		$original_product = $product;

		ob_start();
		/**
		 * Hooks para personalizar render antes/después.
		 */
		do_action( 'clevers_carousel/before', $carousel_id, $settings );

		include $this->locate_template( 'carousels/carousel-' . (int) ( $settings['preset'] ?? 1 ) . '.php' );

		do_action( 'clevers_carousel/after', $carousel_id, $settings );
		$html = ob_get_clean();

		// Restore global product
		$product = $original_product;
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

		set_transient( $cache_key, $html, 10 * MINUTE_IN_SECONDS );

		return $html;
	}

	public function locate_template( $rel_path ) {
		$theme_path = 'clevers-product-carousel/' . ltrim( $rel_path, '/' );
		$tpl        = locate_template( $theme_path );

		if ( $tpl ) {
			return $tpl;
		}

		return CLV_DIR . 'templates/' . $rel_path;
	}

	public function invalidate_cache() {
		update_option(
			'clv_global_cache_bump',
			(int) get_option( 'clv_global_cache_bump', 0 ) + 1,
			false
		);
	}
}

// Helper functions for templates
function clevers_product_carousel_render_card( $clevers_product_carousel_product, $settings ) {
	// Compatibilidad con plantillas WooCommerce que usan global $product.
	$GLOBALS['product'] = $clevers_product_carousel_product;

	$tpl = 'cards/card-' . (int) $settings['preset'] . '.php';

	$render = new Clevers_Product_Carousel_Render();
	include $render->locate_template( $tpl );
}
