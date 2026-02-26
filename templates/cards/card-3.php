<?php
// templates/cards/card-1.php (preset 3)

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Card 3 / Preset 3
 *
 * @var WC_Product $clevers_product_carousel_product
 * @var array      $settings
 */

if ( ! $clevers_product_carousel_product instanceof WC_Product ) {
    return;
}

$clevers_product_carousel_price_html = $clevers_product_carousel_product->get_price_html();
$clevers_product_carousel_permalink  = $clevers_product_carousel_product->get_permalink();
$clevers_product_carousel_title      = $clevers_product_carousel_product->get_name();
$clevers_product_carousel_img        = $clevers_product_carousel_product->get_image( 'woocommerce_thumbnail' );

// Helper con prefijo del plugin.
$clevers_product_carousel_discount = clevers_product_carousel_get_discount_percentage(
    $clevers_product_carousel_product,
    'max' // 'min' / 'avg' también válidos
);
?>
<div class="clevers-card preset-3-card" data-product-id="<?php echo esc_attr( $clevers_product_carousel_product->get_id() ); ?>">
    <a href="<?php echo esc_url( $clevers_product_carousel_permalink ); ?>" class="product-thumb" aria-label="<?php echo esc_attr( $clevers_product_carousel_title ); ?>">
        <?php if ( $clevers_product_carousel_discount ) : ?>
            <?php echo wp_kses_post( clevers_product_carousel_render_discount_badge( (int) $clevers_product_carousel_discount, $settings, 'badge-discount' ) ); ?>
        <?php endif; ?>

        <?php echo wp_kses_post( $clevers_product_carousel_img ); ?>
    </a>

    <div class="product-info">
        <a href="<?php echo esc_url( $clevers_product_carousel_permalink ); ?>" class="product-title">
            <?php echo esc_html( $clevers_product_carousel_title ); ?>
        </a>

        <div class="price-area">
            <?php echo wp_kses_post( $clevers_product_carousel_price_html ); ?>
        </div>

            <a href="<?php echo esc_url( $clevers_product_carousel_permalink ); ?>" class="button select-options" aria-label="<?php echo esc_attr( $clevers_product_carousel_title ); ?>">
                <?php esc_html_e( 'Ver Producto', 'clevers-product-carousel' ); ?>
            </a>

    </div>
</div>
