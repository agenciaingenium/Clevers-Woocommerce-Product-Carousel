<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Clevers_Product_Carousel_Render {

	public function init() {
		add_shortcode( 'clevers_carousel', array( $this, 'shortcode' ) );
		add_action( 'init', array( $this, 'register_block_type' ) );

		// Invalidación por cambios de productos.
		add_action( 'save_post_product', array( $this, 'invalidate_cache' ) );
		add_action( 'save_post_product_variation', array( $this, 'invalidate_cache' ) );
		add_action( 'woocommerce_update_product', array( $this, 'invalidate_cache' ) );
		add_action( 'woocommerce_delete_product_transients', array( $this, 'invalidate_cache' ) );
		add_action( 'woocommerce_scheduled_sales', array( $this, 'invalidate_cache' ) );
		add_action( 'set_object_terms', array( $this, 'invalidate_cache_on_terms_change' ), 10, 6 );
		add_action( 'updated_post_meta', array( $this, 'invalidate_cache_on_product_meta_change' ), 10, 4 );
		add_action( 'added_post_meta', array( $this, 'invalidate_cache_on_product_meta_change' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'invalidate_cache_on_product_meta_change' ), 10, 4 );
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

	public function register_block_type() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$handle = 'clv-carousel-block-editor';
		$src    = CLV_URL . 'assets/block.js';
		$path   = CLV_DIR . 'assets/block.js';
		$ver    = file_exists( $path ) ? (string) filemtime( $path ) : '1.0.0';

		wp_register_script(
			$handle,
			$src,
			array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-components', 'wp-block-editor', 'wp-data' ),
			$ver,
			true
		);

		register_block_type(
			'clevers-product-carousel/carousel',
			array(
				'api_version'     => 2,
				'editor_script'   => $handle,
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'carouselId' => array(
						'type'    => 'number',
						'default' => 0,
					),
				),
			)
		);
	}

	public function render_block( $attributes ) {
		$carousel_id = isset( $attributes['carouselId'] ) ? (int) $attributes['carouselId'] : 0;
		return $this->render_carousel( $carousel_id );
	}

	public function render_carousel( $carousel_id ) {
		if ( ! class_exists( 'WooCommerce' ) || $carousel_id <= 0 ) {
			return '';
		}

		$carousel = get_post( $carousel_id );
		if ( ! $carousel || CLV_SLUG !== $carousel->post_type ) {
			return '';
		}

		$args     = clevers_product_carousel_build_query_args( $carousel_id );
		$settings = clevers_product_carousel_get_settings( $carousel_id );

		wp_enqueue_style( 'clv-slick' );
		wp_enqueue_style( 'clv-slick-theme' );
		wp_enqueue_style( 'clv-carousel' );
		wp_enqueue_script( 'clv-slick' );
		wp_enqueue_script( 'clv-carousel' );

		$this->enqueue_inline_vars( $carousel_id, $settings );

		$ver       = (int) get_post_meta( $carousel_id, '_clv_cache_version', true );
		$bump      = (int) get_option( 'clv_global_cache_bump', 0 );
		$cache_key = 'clv_carousel_' . $carousel_id . '_v' . $ver . '_g' . $bump . '_' .
			md5( wp_json_encode( $args ) . '|' . wp_json_encode( $settings ) );

		$html = get_transient( $cache_key );
		if ( false !== $html ) {
			return (string) apply_filters( 'clevers_carousel/rendered_html', $html, $carousel_id, $settings, true );
		}

		$products = ( new WC_Product_Query( $args ) )->get_products();
		$products = apply_filters( 'clevers_carousel/products', $products, $carousel_id, $args, $settings );

		// Save global product to restore later.
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		global $product;
		$original_product = $product;

		ob_start();
		do_action( 'clevers_carousel/before', $carousel_id, $settings, $products );

		$template_rel = 'carousels/carousel-' . (int) ( $settings['preset'] ?? 1 ) . '.php';
		$template_rel = apply_filters( 'clevers_carousel/carousel_template_relpath', $template_rel, $carousel_id, $settings, $products );
		include clevers_product_carousel_locate_template( $template_rel );

		do_action( 'clevers_carousel/after', $carousel_id, $settings, $products );
		$html = (string) ob_get_clean();

		$product = $original_product;
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

		$cache_ttl = (int) apply_filters( 'clevers_carousel/cache_ttl', 10 * MINUTE_IN_SECONDS, $carousel_id, $settings, $args );
		set_transient( $cache_key, $html, max( MINUTE_IN_SECONDS, $cache_ttl ) );

		return (string) apply_filters( 'clevers_carousel/rendered_html', $html, $carousel_id, $settings, false );
	}

	private function enqueue_inline_vars( $carousel_id, array $settings ) {
		$vars_map = array(
			'color_primary'     => '--clevers-primary',
			'color_primary2'    => '--clevers-primary-hover',
			'color_secondary'   => '--clevers-secondary',
			'color_accent'      => '--clevers-accent',
			'color_text'        => '--clevers-text',
			'color_card_bg'     => '--clevers-card-bg',
			'color_border'      => '--clevers-border',
			'bubble_background' => '--clevers-bubble-background',
			'bubble_text'       => '--clevers-bubble-text',
			'button_background' => '--clevers-button-background',
			'button_text'       => '--clevers-button-text',
		);

		$vars = array();
		foreach ( $vars_map as $setting_key => $css_var ) {
			if ( empty( $settings[ $setting_key ] ) ) {
				continue;
			}

			$sanitized = clevers_product_carousel_sanitize_css_value( $settings[ $setting_key ] );
			if ( '' === $sanitized ) {
				continue;
			}

			$vars[] = $css_var . ':' . $sanitized . ';';
		}

		if ( ! $vars ) {
			return;
		}

		$inline = '#clevers-product-carousel-' . (int) $carousel_id . '{' . implode( '', $vars ) . '}';
		wp_add_inline_style( 'clv-carousel', $inline );
	}

	public function invalidate_cache() {
		update_option(
			'clv_global_cache_bump',
			(int) get_option( 'clv_global_cache_bump', 0 ) + 1,
			false
		);
	}

	public function invalidate_cache_on_terms_change( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		unset( $terms, $tt_ids, $append, $old_tt_ids );

		if ( 'product_cat' !== $taxonomy && 'product_tag' !== $taxonomy ) {
			return;
		}

		$post_type = get_post_type( (int) $object_id );
		if ( 'product' !== $post_type && 'product_variation' !== $post_type ) {
			return;
		}

		$this->invalidate_cache();
	}

	public function invalidate_cache_on_product_meta_change( $meta_id, $object_id, $meta_key, $meta_value ) {
		unset( $meta_id, $meta_value );

		$post_type = get_post_type( (int) $object_id );
		if ( 'product' !== $post_type && 'product_variation' !== $post_type ) {
			return;
		}

		$watched_keys = array(
			'_price',
			'_sale_price',
			'_regular_price',
			'_stock',
			'_stock_status',
			'_featured',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
		);

		if ( in_array( (string) $meta_key, $watched_keys, true ) ) {
			$this->invalidate_cache();
		}
	}
}

/**
 * Helper functions for templates.
 *
 * @param WC_Product $clevers_product_carousel_product Producto.
 * @param array      $settings Ajustes.
 * @return void
 */
function clevers_product_carousel_render_card( $clevers_product_carousel_product, $settings ) {
	// Compatibilidad con plantillas WooCommerce que usan global $product.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- WooCommerce template compatibility.
	$GLOBALS['product'] = $clevers_product_carousel_product;

	$tpl = 'cards/card-' . (int) ( $settings['preset'] ?? 1 ) . '.php';
	$tpl = apply_filters( 'clevers_carousel/card_template_relpath', $tpl, $clevers_product_carousel_product, $settings );

	include clevers_product_carousel_locate_template( $tpl );
}

/**
 * Construye atributos para el contenedor Slick.
 *
 * @param int   $carousel_id ID del carrusel.
 * @param array $settings Ajustes.
 * @return string
 */
function clevers_product_carousel_get_slider_data_attributes( $carousel_id, array $settings ): string {
	$attrs = array(
		'data-carousel-id'      => (string) (int) $carousel_id,
			'data-slides'           => (string) max( 1, min( 8, (int) ( $settings['slidesToShow'] ?? 4 ) ) ),
			'data-slides-tablet'    => (string) max( 1, min( 8, (int) ( $settings['slidesToShowTablet'] ?? 2 ) ) ),
			'data-slides-mobile'    => (string) max( 1, min( 8, (int) ( $settings['slidesToShowMobile'] ?? 1 ) ) ),
		'data-autoplay'         => ! empty( $settings['autoplay'] ) ? 'true' : 'false',
		'data-speed'            => (string) max( 500, min( 60000, (int) ( $settings['autoplayMs'] ?? 3000 ) ) ),
		'data-dots'             => ! empty( $settings['dots'] ) ? 'true' : 'false',
		'data-arrows'           => ! empty( $settings['arrows'] ) ? 'true' : 'false',
		'data-pause-on-hover'   => ! empty( $settings['pauseOnHover'] ) ? 'true' : 'false',
		'data-pause-on-focus'   => ! empty( $settings['pauseOnFocus'] ) ? 'true' : 'false',
			'data-reduced-motion'   => ! empty( $settings['reducedMotionAutoplayOff'] ) ? 'true' : 'false',
			'data-builder-compat'   => ! empty( $settings['builder_compat_mode'] ) ? 'true' : 'false',
			'data-builder-delay'    => (string) max( 0, min( 5000, (int) ( $settings['builder_init_delay_ms'] ?? 0 ) ) ),
			'data-disable-center-on-builder' => ! empty( $settings['builder_disable_center_mode'] ) ? 'true' : 'false',
	);

	$attrs['style'] = sprintf(
		'--clv-fallback-desktop:%1$d;--clv-fallback-tablet:%2$d;--clv-fallback-mobile:%3$d;',
		(int) $attrs['data-slides'],
		(int) $attrs['data-slides-tablet'],
		(int) $attrs['data-slides-mobile']
	);

	$attrs = apply_filters( 'clevers_carousel/slider_data_attributes', $attrs, $carousel_id, $settings );

	$parts = array();
	foreach ( $attrs as $name => $value ) {
		$parts[] = sprintf( '%s="%s"', esc_attr( (string) $name ), esc_attr( (string) $value ) );
	}

	return implode( ' ', $parts );
}
