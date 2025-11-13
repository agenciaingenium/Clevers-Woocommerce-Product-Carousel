<?php
// templates/cards/card-1.php (preset 3)

/**
 * Card 3 / Preset 3
 *
 * @var WC_Product $clevers_product_carousel_product
 * @var array      $settings
 */

if ( ! $clevers_product_carousel_product instanceof WC_Product ) {
    return;
}

$price_html = $clevers_product_carousel_product->get_price_html();
$permalink  = $clevers_product_carousel_product->get_permalink();
$title      = $clevers_product_carousel_product->get_name();
$img        = $clevers_product_carousel_product->get_image();

// Helper con prefijo del plugin.
$discount = clevers_product_carousel_get_discount_percentage(
    $clevers_product_carousel_product,
    'max' // 'min' / 'avg' también válidos
);

// Texto accesible para el botón "Añadir al carrito".
$aria_label = sprintf(
    /* translators: %s: product title. */
    esc_html__( 'Add %s to your cart', 'clevers-product-carousel' ),
    $title
);
?>
<div class="clevers-card preset-3-card" data-product-id="<?php echo esc_attr( $clevers_product_carousel_product->get_id() ); ?>">
    <a href="<?php echo esc_url( $permalink ); ?>" class="product-thumb">
        <?php if ( $discount ) : ?>
            <span class="badge-discount"><?php echo esc_html( $discount ); ?>%</span>
        <?php endif; ?>

        <?php echo wp_kses_post( $img ); ?>
    </a>

    <div class="product-info">
        <a href="<?php echo esc_url( $permalink ); ?>" class="product-title">
            <?php echo esc_html( $title ); ?>
        </a>

        <div class="price-area">
            <?php echo wp_kses_post( $price_html ); ?>
        </div>

        <?php if ( $clevers_product_carousel_product->is_type( 'simple' ) ) : ?>

            <a href="<?php echo esc_url( $clevers_product_carousel_product->add_to_cart_url() ); ?>"
               class="button add_to_cart_button ajax_add_to_cart"
               data-product_id="<?php echo esc_attr( $clevers_product_carousel_product->get_id() ); ?>"
               data-product_sku="<?php echo esc_attr( $clevers_product_carousel_product->get_sku() ); ?>"
               aria-label="<?php echo esc_attr( $aria_label ); ?>">
                <?php esc_html_e( 'Añadir al carrito', 'clevers-product-carousel' ); ?>
            </a>

        <?php else : ?>

            <a href="<?php echo esc_url( $permalink ); ?>" class="button select-options">
                <?php esc_html_e( 'Seleccionar opciones', 'clevers-product-carousel' ); ?>
            </a>

        <?php endif; ?>
    </div>
</div>