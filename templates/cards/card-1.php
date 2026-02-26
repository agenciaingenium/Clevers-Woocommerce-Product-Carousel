<?php
// templates/cards/card-1.php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var WC_Product $clevers_product_carousel_product */
/** @var array $settings */

if (!$clevers_product_carousel_product) {
    return;
}

$clevers_product_carousel_price_html = $clevers_product_carousel_product->get_price_html();
$clevers_product_carousel_permalink = $clevers_product_carousel_product->get_permalink();
$clevers_product_carousel_title = $clevers_product_carousel_product->get_name();
$clevers_product_carousel_img = $clevers_product_carousel_product->get_image( 'woocommerce_thumbnail' );

$clevers_product_carousel_discount = clevers_product_carousel_get_discount_percentage($clevers_product_carousel_product, 'max');


$clevers_product_carousel_aria_label = sprintf(
/* translators: %s: product title. */
        esc_html__('Add %s to your cart', 'clevers-product-carousel'),
        $clevers_product_carousel_title
);
?>

<div class="clevers-card preset-1-card" data-product-id="<?php echo esc_attr($clevers_product_carousel_product->get_id()); ?>">

    <a href="<?php echo esc_url($clevers_product_carousel_permalink); ?>" class="product-thumb" aria-label="<?php echo esc_attr( $clevers_product_carousel_title ); ?>">
        <?php if ($clevers_product_carousel_discount) : ?>
            <?php echo wp_kses_post( clevers_product_carousel_render_discount_badge( (int) $clevers_product_carousel_discount, $settings, 'badge-discount' ) ); ?>
        <?php endif; ?>

        <?php echo wp_kses_post($clevers_product_carousel_img); ?>
    </a>
    <div class="product-info">

        <a href="<?php echo esc_url($clevers_product_carousel_permalink); ?>" class="product-title">
            <?php echo esc_html($clevers_product_carousel_title); ?>
        </a>

        <div class="price-area"><?php echo wp_kses_post($clevers_product_carousel_price_html); ?></div>

        <?php if ($clevers_product_carousel_product->is_type('simple')) : ?>

            <a href="<?php echo esc_url($clevers_product_carousel_product->add_to_cart_url()); ?>"
               class="button add_to_cart_button ajax_add_to_cart"
               data-product_id="<?php echo esc_attr($clevers_product_carousel_product->get_id()); ?>"
               data-product_sku="<?php echo esc_attr($clevers_product_carousel_product->get_sku()); ?>"
               data-success_message="<?php echo esc_attr__( 'Product added to cart', 'clevers-product-carousel' ); ?>"
               aria-label="<?php echo esc_attr($clevers_product_carousel_aria_label); ?>">
                <?php esc_html_e('Añadir al carrito', 'clevers-product-carousel'); ?>
            </a>

        <?php else : ?>

            <a href="<?php echo esc_url($clevers_product_carousel_permalink); ?>" class="button select-options" aria-label="<?php echo esc_attr( $clevers_product_carousel_title ); ?>">
                <?php esc_html_e('Seleccionar opciones', 'clevers-product-carousel'); ?>
            </a>

        <?php endif; ?>

    </div>
</div>
