<?php
/**
 * Plugin Name: Clevers Product Carousel
 * Plugin URI:  https://github.com/agenciaingenium/Clevers-Woocommerce-Product-Carousel/
 * Description: CPT "Product Carousel" + render server-side + sistema de plantillas estilo Woo para carruseles de productos.
 * Author:      Clevers Devs
 * Author URI:  https://clevers.dev
 * Version:     1.0.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Tested up to:      6.7
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: clevers-product-carousel
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
//  Constantes
// -----------------------------------------------------------------------------
const CLV_SLUG = 'clevers_carousel';
define( 'CLV_DIR', plugin_dir_path( __FILE__ ) );
define( 'CLV_URL', plugin_dir_url( __FILE__ ) );

// -----------------------------------------------------------------------------
//  1) CPT: clevers_carousel + assets
// -----------------------------------------------------------------------------
add_action(
	'init',
	function () {

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
			'labels'        => $labels,
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'menu_icon'     => 'dashicons-images-alt2',
			'supports'      => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'  => true,
		);

		register_post_type( CLV_SLUG, $args );

		require_once __DIR__ . '/includes/helpers-discount.php';
	}
);

// -----------------------------------------------------------------------------
//  2) Metabox básico (preset + query + opciones slick)
// -----------------------------------------------------------------------------
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'clv_carousel_settings',
			__( 'Carousel Settings', 'clevers-product-carousel' ),
			'clevers_product_carousel_carousel_settings_mb',
			CLV_SLUG,
			'normal',
			'high'
		);
	}
);

/**
 * Metabox de ajustes del carrusel.
 *
 * @param WP_Post $post Post actual.
 */
function clevers_product_carousel_carousel_settings_mb( $post ): void {
	$meta = clevers_product_carousel_get_carousel_meta( $post->ID );

	wp_nonce_field( 'clv_save_carousel', 'clv_carousel_nonce' );

	$preset        = (int) ( $meta['preset'] ?? 1 );
	$limit         = (int) ( $meta['limit'] ?? 8 );
	$orderby       = isset( $meta['orderby'] ) ? esc_attr( $meta['orderby'] ) : 'date';
	$order         = isset( $meta['order'] ) ? esc_attr( $meta['order'] ) : 'DESC';
	$categories    = (array) ( $meta['categories'] ?? array() );
	$on_sale       = ! empty( $meta['on_sale'] );
	$on_featured   = ! empty( $meta['on_featured'] );
	$instock_only  = ! empty( $meta['instock_only'] );
	$slidesToShow  = (int) ( $meta['slidesToShow'] ?? 4 );
	$autoplay      = ! empty( $meta['autoplay'] );
	$autoplayMs    = (int) ( $meta['autoplayMs'] ?? 3000 );
	$dots          = ! empty( $meta['dots'] );
	$arrows        = ! empty( $meta['arrows'] );

	$color_primary       = isset( $meta['color_primary'] ) ? esc_attr( $meta['color_primary'] ) : '';
	$color_primary2      = isset( $meta['color_primary2'] ) ? esc_attr( $meta['color_primary2'] ) : '';
	$color_secondary     = isset( $meta['color_secondary'] ) ? esc_attr( $meta['color_secondary'] ) : '';
	$color_text          = isset( $meta['color_text'] ) ? esc_attr( $meta['color_text'] ) : '';
	$color_card_bg       = isset( $meta['color_card_bg'] ) ? esc_attr( $meta['color_card_bg'] ) : '';
	$color_border        = isset( $meta['color_border'] ) ? esc_attr( $meta['color_border'] ) : '';
	$bubble_background   = isset( $meta['bubble_background'] ) ? esc_attr( $meta['bubble_background'] ) : '';
	$bubble_text         = isset( $meta['bubble_text'] ) ? esc_attr( $meta['bubble_text'] ) : '';
	$button_background   = isset( $meta['button_background'] ) ? esc_attr( $meta['button_background'] ) : '';
	$button_text         = isset( $meta['button_text'] ) ? esc_attr( $meta['button_text'] ) : '';

	?>
	<style>
		.clv-field {
			margin: 10px 0;
		}
		.clv-field label {
			display: block;
			font-weight: 600;
			margin-bottom: 4px;
		}
	</style>

	<div class="clv-field">
		<label for="clv[preset]">
			<?php esc_html_e( 'Preset / Design', 'clevers-product-carousel' ); ?>
		</label>
		<select name="clv[preset]">
			<option value="1" <?php selected( $preset, 1 ); ?>>Preset 1</option>
			<option value="2" <?php selected( $preset, 2 ); ?>>Preset 2</option>
			<option value="3" <?php selected( $preset, 3 ); ?>>Preset 3</option>
		</select>
	</div>

	<div class="clv-field">
		<label><?php esc_html_e( 'Limit', 'clevers-product-carousel' ); ?></label>
		<input type="number" min="1" name="clv[limit]" value="<?php echo esc_attr( $limit ); ?>"/>
	</div>

	<div class="clv-field">
		<label><?php esc_html_e( 'Order By / Order', 'clevers-product-carousel' ); ?></label>
		<!-- Aquí tus selects reales de orderby / order -->
	</div>

	<div class="clv-field">
		<label><?php esc_html_e( 'Product Categories (slugs, comma separated)', 'clevers-product-carousel' ); ?></label>
		<input type="text" name="clv[categories_csv]" value="<?php echo esc_attr( implode( ',', $categories ) ); ?>"/>
		<small><?php esc_html_e( 'Usa slugs de product_cat (no categorías de posts).', 'clevers-product-carousel' ); ?></small>
	</div>

	<div class="clv-field">
		<label><?php esc_html_e( 'Filters', 'clevers-product-carousel' ); ?></label>
		<label>
			<input type="checkbox" name="clv[on_sale]" <?php checked( $on_sale ); ?> />
			<?php esc_html_e( 'On Sale', 'clevers-product-carousel' ); ?>
		</label>
		<label>
			<input type="checkbox" name="clv[on_featured]" <?php checked( $on_featured ); ?> />
			<?php esc_html_e( 'Featured', 'clevers-product-carousel' ); ?>
		</label>
		<label>
			<input type="checkbox" name="clv[instock_only]" <?php checked( $instock_only ); ?> />
			<?php esc_html_e( 'In Stock Only', 'clevers-product-carousel' ); ?>
		</label>
	</div>

	<hr/>

	<h3><?php esc_html_e( 'Carousel Options', 'clevers-product-carousel' ); ?></h3>

	<div class="clv-field">
		<label><?php esc_html_e( 'Slides To Show', 'clevers-product-carousel' ); ?></label>
		<input type="number" min="1" name="clv[slidesToShow]" value="<?php echo esc_attr( $slidesToShow ); ?>"/>
	</div>

	<div class="clv-field">
		<label>
			<input type="checkbox" name="clv[autoplay]" <?php checked( $autoplay ); ?> />
			<?php esc_html_e( 'Autoplay', 'clevers-product-carousel' ); ?>
		</label>
	</div>

	<div class="clv-field">
		<label><?php esc_html_e( 'Autoplay Speed (ms)', 'clevers-product-carousel' ); ?></label>
		<input type="number" min="500" step="100" name="clv[autoplayMs]" value="<?php echo esc_attr( $autoplayMs ); ?>"/>
	</div>

	<div class="clv-field">
		<label>
			<input type="checkbox" name="clv[dots]" <?php checked( $dots ); ?> />
			<?php esc_html_e( 'Dots', 'clevers-product-carousel' ); ?>
		</label>
		<label>
			<input type="checkbox" name="clv[arrows]" <?php checked( $arrows ); ?> />
			<?php esc_html_e( 'Arrows', 'clevers-product-carousel' ); ?>
		</label>
	</div>

	<hr/>

	<h3><?php esc_html_e( 'Colors', 'clevers-product-carousel' ); ?></h3>

	<div class="clv-field"><label>Primary</label>
		<input type="color" name="clv[color_primary]" value="<?php echo esc_attr( $color_primary ); ?>">
	</div>
	<div class="clv-field"><label>Primary (Hover)</label>
		<input type="color" name="clv[color_primary2]" value="<?php echo esc_attr( $color_primary2 ); ?>">
	</div>
	<div class="clv-field"><label>Secondary</label>
		<input type="color" name="clv[color_secondary]" value="<?php echo esc_attr( $color_secondary ); ?>">
	</div>
	<div class="clv-field"><label>Bubble Background</label>
		<input type="color" name="clv[bubble_background]" value="<?php echo esc_attr( $bubble_background ); ?>">
	</div>
	<div class="clv-field"><label>Bubble Text</label>
		<input type="color" name="clv[bubble_text]" value="<?php echo esc_attr( $bubble_text ); ?>">
	</div>
	<div class="clv-field"><label>Button Background</label>
		<input type="color" name="clv[button_background]" value="<?php echo esc_attr( $button_background ); ?>">
	</div>
	<div class="clv-field"><label>Button Text</label>
		<input type="color" name="clv[button_text]" value="<?php echo esc_attr( $button_text ); ?>">
	</div>
	<div class="clv-field"><label>Texto</label>
		<input type="color" name="clv[color_text]" value="<?php echo esc_attr( $color_text ); ?>">
	</div>
	<div class="clv-field"><label>Fondo Card</label>
		<input type="color" name="clv[color_card_bg]" value="<?php echo esc_attr( $color_card_bg ); ?>">
	</div>
	<div class="clv-field"><label>Border Product Info</label>
		<input type="color" name="clv[color_border]" value="<?php echo esc_attr( $color_border ); ?>">
	</div>
	<?php
}

// -----------------------------------------------------------------------------
//  Guardado metabox
// -----------------------------------------------------------------------------
add_action(
	'save_post_' . CLV_SLUG,
	function ( $post_id, $post ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( $post->post_type !== CLV_SLUG ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'clv_carousel_nonce', FILTER_DEFAULT );
		if ( empty( $nonce ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $nonce ) );
		if ( ! wp_verify_nonce( $nonce, 'clv_save_carousel' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$in = filter_input( INPUT_POST, 'clv', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( ! is_array( $in ) ) {
			$in = array();
		}
		$in = wp_unslash( $in );

		$out              = array();
		$out['preset']    = max( 1, (int) ( $in['preset'] ?? 1 ) );
		$out['limit']     = max( 1, (int) ( $in['limit'] ?? 8 ) );
		$out['orderby']   = sanitize_text_field( $in['orderby'] ?? 'date' );
		$out['order']     = in_array( ( $in['order'] ?? 'DESC' ), array( 'ASC', 'DESC' ), true ) ? $in['order'] : 'DESC';
		$out['categories'] = array_filter(
			array_map(
				'sanitize_title',
				array_map(
					'trim',
					explode( ',', $in['categories_csv'] ?? '' )
				)
			)
		);
		$out['on_sale']      = ! empty( $in['on_sale'] );
		$out['on_featured']  = ! empty( $in['on_featured'] );
		$out['instock_only'] = ! empty( $in['instock_only'] );

		$out['slidesToShow'] = max( 1, (int) ( $in['slidesToShow'] ?? 4 ) );
		$out['autoplay']     = ! empty( $in['autoplay'] );
		$out['autoplayMs']   = max( 0, (int) ( $in['autoplayMs'] ?? 3000 ) );
		$out['dots']         = ! empty( $in['dots'] );
		$out['arrows']       = ! empty( $in['arrows'] );

		$text_fields = array(
			'color_primary',
			'color_primary2',
			'color_secondary',
			'bubble_background',
			'bubble_text',
			'color_accent',
			'color_text',
			'color_card_bg',
			'color_border',
			'button_background',
			'button_text',
		);

		foreach ( $text_fields as $field ) {
			$out[ $field ] = isset( $in[ $field ] ) ? sanitize_text_field( $in[ $field ] ) : '';
		}

		update_post_meta( $post_id, '_clv_settings', $out );

		$ver = (int) get_post_meta( $post_id, '_clv_cache_version', true );
		update_post_meta( $post_id, '_clv_cache_version', $ver + 1 );
	},
	10,
	2
);

// -----------------------------------------------------------------------------
//  3) Helpers: meta, query, settings
// -----------------------------------------------------------------------------
function clevers_product_carousel_get_carousel_meta( $id ): array {
	return (array) get_post_meta( $id, '_clv_settings', true );
}

function clevers_product_carousel_build_query_args( $carousel_id ) {
	$meta    = clevers_product_carousel_get_carousel_meta( $carousel_id );
	$orderby = $meta['orderby'] ?? 'date';

	$args = array(
		'limit'  => (int) ( $meta['limit'] ?? 8 ),
		'order'  => $meta['order'] ?? 'DESC',
		'return' => 'objects',
	);

	switch ( $orderby ) {
		case 'price':
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = '_price';
			break;
		case 'popularity':
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'total_sales';
			break;
		case 'rating':
			$args['orderby'] = 'rating';
			break;
		default:
			$args['orderby'] = $orderby;
	}

	if ( ! empty( $meta['categories'] ) ) {
		$args['category'] = (array) $meta['categories'];
	}
	if ( ! empty( $meta['on_sale'] ) ) {
		$ids          = wc_get_product_ids_on_sale();
		$args['include'] = $ids ? array_values( $ids ) : array( 0 );
	}
	if ( ! empty( $meta['on_featured'] ) ) {
		$ids          = wc_get_featured_product_ids();
		$args['include'] = $ids ? array_values( $ids ) : array( 0 );
	}
	if ( ! empty( $meta['instock_only'] ) ) {
		$args['stock_status'] = 'instock';
	}

	return apply_filters( 'clevers_carousel/query_args', $args, $carousel_id, $meta );
}

function clevers_product_carousel_get_settings( $carousel_id ) {
	$meta     = clevers_product_carousel_get_carousel_meta( $carousel_id );
	$defaults = array(
		'preset'       => 1,
		'slidesToShow' => 4,
		'autoplay'     => false,
		'autoplayMs'   => 3000,
		'dots'         => false,
		'arrows'       => true,
	);

	$settings = wp_parse_args( $meta, $defaults );

	return apply_filters( 'clevers_carousel/settings', $settings, $carousel_id );
}


// -----------------------------------------------------------------------------
//  4) Render + Template loader
// -----------------------------------------------------------------------------
function clevers_product_carousel_render_carousel( $carousel_id ) {
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

	ob_start();
	/**
	 * Hooks para personalizar render antes/después.
	 */
	do_action( 'clevers_carousel/before', $carousel_id, $settings );

	include clevers_product_carousel_locate_template( 'carousels/carousel-' . (int) ( $settings['preset'] ?? 1 ) . '.php' );

	do_action( 'clevers_carousel/after', $carousel_id, $settings );
	$html = ob_get_clean();

	set_transient( $cache_key, $html, 10 * MINUTE_IN_SECONDS );

	return $html;
}

function clevers_product_carousel_locate_template( $rel_path ): string {
	$theme_path = 'clevers-product-carousel/' . ltrim( $rel_path, '/' );
	$tpl        = locate_template( $theme_path );

	if ( $tpl ) {
		return $tpl;
	}

	return CLV_DIR . 'templates/' . $rel_path;
}

// -----------------------------------------------------------------------------
//  5) Shortcode
// -----------------------------------------------------------------------------
add_shortcode(
	'clevers_carousel',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts
		);

		return clevers_product_carousel_render_carousel( (int) $atts['id'] );
	}
);

// -----------------------------------------------------------------------------
//  7) Render de tarjeta
// -----------------------------------------------------------------------------
/**
 * Render de una tarjeta de producto.
 *
 * @param WC_Product $clevers_product_carousel_product Producto.
 * @param array      $settings                        Ajustes del carrusel.
 */
function clevers_product_carousel_render_card( $clevers_product_carousel_product, $settings ) {
	// Compatibilidad con plantillas WooCommerce que usan global $product.
	$GLOBALS['product'] = $clevers_product_carousel_product;

	$tpl = 'cards/card-' . (int) $settings['preset'] . '.php';

	include clevers_product_carousel_locate_template( $tpl );
}

// -----------------------------------------------------------------------------
//  8) Invalidación por cambios en productos (básico)
// -----------------------------------------------------------------------------
add_action(
	'save_post_product',
	function () {
		update_option(
			'clv_global_cache_bump',
			(int) get_option( 'clv_global_cache_bump', 0 ) + 1,
			false
		);
	}
);