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
 * Construye los argumentos de la query para el carrusel.
 *
 * @param int $carousel_id ID del carrusel.
 * @return array
 */
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
			$args['orderby'] = 'price';
			break;
		case 'popularity':
			$args['orderby'] = 'popularity';
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
		$ids             = wc_get_product_ids_on_sale();
		$args['include'] = $ids ? array_values( $ids ) : array( 0 );
	}
	if ( ! empty( $meta['on_featured'] ) ) {
		$ids             = wc_get_featured_product_ids();
		$args['include'] = $ids ? array_values( $ids ) : array( 0 );
	}
	if ( ! empty( $meta['instock_only'] ) ) {
		$args['stock_status'] = 'instock';
	}

	return apply_filters( 'clevers_carousel/query_args', $args, $carousel_id, $meta );
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
