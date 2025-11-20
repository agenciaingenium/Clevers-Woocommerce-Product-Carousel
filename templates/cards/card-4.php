<?php
// templates/cards/card-1.php (preset 3)

/**
 * Card 4 / Preset 4
 *
 * @var WC_Product $clevers_product_carousel_product
 * @var array $settings
 */

if (!$clevers_product_carousel_product instanceof WC_Product) {
    return;
}

$price_html = $clevers_product_carousel_product->get_price_html();
$permalink = $clevers_product_carousel_product->get_permalink();
$title = $clevers_product_carousel_product->get_name();
$img = $clevers_product_carousel_product->get_image();

// Helper con prefijo del plugin.
$discount = clevers_product_carousel_get_discount_percentage(
        $clevers_product_carousel_product,
        'max'
);
// Texto accesible para el botón "Añadir al carrito".
$aria_label = sprintf(
/* translators: %s: product title. */
        esc_html__('Ver Producto', 'clevers-product-carousel'),
        $title
);
?>
<div class="clevers-card preset-4-card"
     data-product-id="<?php echo esc_attr($clevers_product_carousel_product->get_id()); ?>">
    <div class="clevers-card-image">
        <a href="<?php echo esc_url($permalink); ?>" class="product-thumb">
            <?php if ($discount) : ?>
                <span class="badge-discount"><?php echo esc_html($discount); ?>% Dcto</span>
            <?php endif; ?>

            <?php echo wp_kses_post($img); ?>

        </a>
            <a href="<?php echo esc_url($permalink); ?>" class="button select-options">
                <?php esc_html_e('Ver Producto', 'clevers-product-carousel'); ?>
            </a>

    </div>

    <div class="product-info">
        <a href="<?php echo esc_url($permalink); ?>" class="product-title">
            <?php echo esc_html($title); ?>
        </a>

        <div class="price-area">
            <?php echo wp_kses_post($price_html); ?>
        </div>
    </div>
</div>