<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var WC_Product $clevers_product_carousel_product */
/** @var array       $settings */

if ( ! $clevers_product_carousel_product ) {
    return;
}

$clevers_product_carousel_discount = clevers_product_carousel_get_discount_percentage( $clevers_product_carousel_product, 'max' ); // 'min' / 'avg' también válidos.
?>
<div class="clevers-product card-2">
    <div class="product-media">
        <?php if ( null !== $clevers_product_carousel_discount ) : ?>
            <?php echo wp_kses_post( clevers_product_carousel_render_discount_badge( (int) $clevers_product_carousel_discount, $settings, 'clv-badge-round' ) ); ?>
        <?php endif; ?>

        <a href="<?php echo esc_url( $clevers_product_carousel_product->get_permalink() ); ?>" class="product-thumb" aria-label="<?php echo esc_attr( $clevers_product_carousel_product->get_name() ); ?>">
            <?php echo wp_kses_post( $clevers_product_carousel_product->get_image( 'woocommerce_thumbnail' ) ); ?>
        </a>
    </div>

    <a class="product-title" href="<?php echo esc_url( $clevers_product_carousel_product->get_permalink() ); ?>">
        <?php echo esc_html( mb_strtoupper( $clevers_product_carousel_product->get_name() ) ); ?>
    </a>

    <div class="price-area">
        <?php echo wp_kses_post( $clevers_product_carousel_product->get_price_html() ); ?>
    </div>

    <div class="actions">
        <?php if ( $clevers_product_carousel_product->is_type( 'simple' ) ) : ?>
            <a href="<?php echo esc_url( $clevers_product_carousel_product->add_to_cart_url() ); ?>"
               class="clevers-button ajax_add_to_cart add_to_cart_button"
               data-product_id="<?php echo esc_attr( $clevers_product_carousel_product->get_id() ); ?>"
               data-product_sku="<?php echo esc_attr( $clevers_product_carousel_product->get_sku() ); ?>"
               aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to your cart', 'clevers-product-carousel' ), $clevers_product_carousel_product->get_name() ) ); ?>">
                <?php esc_html_e( 'Añadir al carrito', 'clevers-product-carousel' ); ?>
            </a>
        <?php else : ?>
            <a href="<?php echo esc_url( $clevers_product_carousel_product->get_permalink() ); ?>" class="clevers-button" aria-label="<?php echo esc_attr( $clevers_product_carousel_product->get_name() ); ?>">
                <?php esc_html_e( 'Seleccionar opciones', 'clevers-product-carousel' ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
