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

$clv_price_html = $clevers_product_carousel_product->get_price_html();
$clv_permalink  = $clevers_product_carousel_product->get_permalink();
$clv_title      = $clevers_product_carousel_product->get_name();
$clv_img        = $clevers_product_carousel_product->get_image();

// Helper con prefijo del plugin.
$clv_discount = clevers_product_carousel_get_discount_percentage(
    $clevers_product_carousel_product,
    'max' // 'min' / 'avg' también válidos
);
?>
<div class="clevers-card preset-3-card" data-product-id="<?php echo esc_attr( $clevers_product_carousel_product->get_id() ); ?>">
    <a href="<?php echo esc_url( $clv_permalink ); ?>" class="product-thumb">
        <?php if ( $clv_discount ) : ?>
            <span class="badge-discount"><?php echo esc_html( $clv_discount ); ?>%</span>
        <?php endif; ?>

        <?php echo wp_kses_post( $clv_img ); ?>
    </a>

    <div class="product-info">
        <a href="<?php echo esc_url( $clv_permalink ); ?>" class="product-title">
            <?php echo esc_html( $clv_title ); ?>
        </a>

        <div class="price-area">
            <?php echo wp_kses_post( $clv_price_html ); ?>
        </div>

            <a href="<?php echo esc_url( $clv_permalink ); ?>" class="button select-options">
                <?php esc_html_e( 'Ver Producto', 'clevers-product-carousel' ); ?>
            </a>

    </div>
</div>
