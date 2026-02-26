<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Obtiene los metadatos del carrusel.
 *
 * @param int $id ID del post.
 * @return array
 */
function clevers_product_carousel_get_carousel_meta( $id ): array {
	return (array) get_post_meta( $id, '_clv_settings', true );
}

/**
 * Devuelve los valores permitidos para orderby.
 *
 * @return string[]
 */
function clevers_product_carousel_get_allowed_orderby_values(): array {
	return array(
		'date',
		'modified',
		'title',
		'menu_order',
		'rand',
		'price',
		'popularity',
		'rating',
		'date_modified',
	);
}

/**
 * Combina listas de IDs con estrategia configurable.
 *
 * @param int[]|null $current  Lista actual.
 * @param int[]      $incoming Lista entrante.
 * @param string     $strategy intersection|union
 * @return int[]
 */
function clevers_product_carousel_merge_product_ids( ?array $current, array $incoming, string $strategy ): array {
	$incoming = array_values( array_unique( array_map( 'intval', $incoming ) ) );

	if ( null === $current ) {
		return $incoming;
	}

	if ( 'union' === $strategy ) {
		return array_values( array_unique( array_merge( $current, $incoming ) ) );
	}

	return array_values( array_intersect( $current, $incoming ) );
}

/**
 * Sanitiza valores simples para CSS variables.
 *
 * @param mixed $value Valor.
 * @return string
 */
function clevers_product_carousel_sanitize_css_value( $value ): string {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	if ( 'transparent' === strtolower( $value ) ) {
		return 'transparent';
	}

	$hex = sanitize_hex_color( $value );
	return $hex ? $hex : '';
}

/**
 * Localiza una plantilla con soporte de override en tema.
 *
 * @param string $rel_path Ruta relativa dentro de templates/.
 * @return string
 */
function clevers_product_carousel_locate_template( $rel_path ): string {
	$rel_path  = ltrim( (string) $rel_path, '/' );
	$theme_path = 'clevers-product-carousel/' . $rel_path;
	$tpl        = locate_template( $theme_path );

	if ( $tpl ) {
		return $tpl;
	}

	return CLV_DIR . 'templates/' . $rel_path;
}

/**
 * Construye los argumentos de la query para el carrusel.
 *
 * @param int $carousel_id ID del carrusel.
 * @return array
 */
function clevers_product_carousel_build_query_args( $carousel_id ) {
	$meta    = clevers_product_carousel_get_carousel_meta( $carousel_id );
	$orderby = sanitize_text_field( (string) ( $meta['orderby'] ?? 'date' ) );

	if ( ! in_array( $orderby, clevers_product_carousel_get_allowed_orderby_values(), true ) ) {
		$orderby = 'date';
	}

	$order = strtoupper( sanitize_text_field( (string) ( $meta['order'] ?? 'DESC' ) ) );
	$order = in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'DESC';

	$args = array(
		'limit'  => max( 1, min( 48, (int) ( $meta['limit'] ?? 8 ) ) ),
		'order'  => $order,
		'return' => 'objects',
	);

	$manual_product_ids = array_values(
		array_unique(
			array_filter(
				array_map( 'intval', (array) ( $meta['manual_product_ids'] ?? array() ) )
			)
		)
	);

	if ( ! empty( $meta['manual_products_enabled'] ) && ! empty( $manual_product_ids ) ) {
		$args['include'] = $manual_product_ids;
		$args['orderby'] = 'include';

		return apply_filters( 'clevers_carousel/query_args', $args, $carousel_id, $meta );
	}

	switch ( $orderby ) {
		case 'price':
		case 'popularity':
		case 'rating':
		case 'modified':
		case 'menu_order':
		case 'rand':
		case 'title':
		case 'date_modified':
			$args['orderby'] = $orderby;
			break;
		default:
			$args['orderby'] = 'date';
			break;
	}

	if ( ! empty( $meta['categories'] ) ) {
		$args['category'] = array_values(
			array_filter(
				array_map( 'sanitize_title', (array) $meta['categories'] )
			)
		);
	}

	$include_ids = null;
	$strategy    = apply_filters( 'clevers_carousel/include_strategy', 'intersection', $carousel_id, $meta );
	$strategy    = ( 'union' === $strategy ) ? 'union' : 'intersection';

	if ( ! empty( $meta['on_sale'] ) ) {
		$include_ids = clevers_product_carousel_merge_product_ids(
			$include_ids,
			(array) wc_get_product_ids_on_sale(),
			$strategy
		);
	}

	if ( ! empty( $meta['on_featured'] ) ) {
		$include_ids = clevers_product_carousel_merge_product_ids(
			$include_ids,
			(array) wc_get_featured_product_ids(),
			$strategy
		);
	}

	if ( null !== $include_ids ) {
		$args['include'] = ! empty( $include_ids ) ? array_values( $include_ids ) : array( 0 );
	}

	if ( ! empty( $meta['instock_only'] ) ) {
		$args['stock_status'] = 'instock';
	}

	$args = apply_filters( 'clevers_carousel/query_args', $args, $carousel_id, $meta );

	return is_array( $args ) ? $args : array();
}

/**
 * Obtiene los ajustes del carrusel con valores por defecto.
 *
 * @param int $carousel_id ID del carrusel.
 * @return array
 */
function clevers_product_carousel_get_settings( $carousel_id ) {
	$meta     = clevers_product_carousel_get_carousel_meta( $carousel_id );
	$defaults = array(
		'preset'                    => 1,
		'slidesToShow'              => 4,
		'slidesToShowTablet'        => 2,
		'slidesToShowMobile'        => 1,
		'autoplay'                  => false,
		'autoplayMs'                => 3000,
		'dots'                      => false,
		'arrows'                    => true,
		'pauseOnHover'              => true,
		'pauseOnFocus'              => true,
		'reducedMotionAutoplayOff'  => true,
		'builder_compat_mode'       => false,
		'builder_init_delay_ms'     => 0,
		'builder_disable_center_mode' => false,
	);

	$settings = wp_parse_args( $meta, $defaults );
	$settings['preset']       = max( 1, min( 4, (int) $settings['preset'] ) );
	$settings['slidesToShow'] = max( 1, min( 8, (int) $settings['slidesToShow'] ) );
	$settings['slidesToShowTablet'] = max( 1, min( 8, (int) ( $settings['slidesToShowTablet'] ?? min( 2, $settings['slidesToShow'] ) ) ) );
	$settings['slidesToShowMobile'] = max( 1, min( 8, (int) ( $settings['slidesToShowMobile'] ?? 1 ) ) );
	$settings['autoplayMs']   = max( 500, min( 60000, (int) $settings['autoplayMs'] ) );
	$settings['autoplay']     = ! empty( $settings['autoplay'] );
	$settings['dots']         = ! empty( $settings['dots'] );
	$settings['arrows']       = ! empty( $settings['arrows'] );
	$settings['builder_compat_mode'] = ! empty( $settings['builder_compat_mode'] );
	$settings['builder_init_delay_ms'] = max( 0, min( 5000, (int) ( $settings['builder_init_delay_ms'] ?? 0 ) ) );
	$settings['builder_disable_center_mode'] = ! empty( $settings['builder_disable_center_mode'] );

	return apply_filters( 'clevers_carousel/settings', $settings, $carousel_id );
}
