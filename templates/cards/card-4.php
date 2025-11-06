<?php
// templates/cards/card-1.php

/** @var WC_Product $product */
/** @var array $settings */

if (!$product) return;

$price_html = $product->get_price_html();
$permalink = $product->get_permalink();
$title = $product->get_name();
$img = $product->get_image();
$discount = null;

$regular_price = (float)$product->get_regular_price();
$sale_price = (float)$product->get_sale_price();
$regular_price = (float)($product->get_regular_price() ?: 0);
$sale_price = (float)($product->get_sale_price() ?: 0);
$discount = null;

if ($regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price) {
    $discount = abs(round((($regular_price - $sale_price) / $regular_price) * 100));
}
?>
<div class="clevers-card preset-3-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <a href="<?php echo esc_url($permalink); ?>" class="product-thumb">
        <?php if ($discount) : ?>
            <span class="badge-discount"><?php echo esc_html($discount); ?>%</span>
        <?php endif; ?>
        <?php echo $img; ?>
    </a>

    <div class="product-info">
        <a href="<?php echo esc_url($permalink); ?>" class="product-title"><?php echo esc_html($title); ?></a>
        <div class="price-area"><?php echo $price_html; ?></div>

        <?php if ($product->is_type('simple')) : ?>
            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
               class="button add_to_cart_button ajax_add_to_cart"
               data-product_id="<?php echo esc_attr($product->get_id()); ?>"
               data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
               aria-label="<?php echo esc_attr(sprintf(__('Add %s to your cart', 'clevers-product-carousel'), $title)); ?>">
                <?php _e('Añadir al carrito', 'clevers-product-carousel'); ?>
            </a>
        <?php else : ?>
            <a href="<?php echo esc_url($permalink); ?>" class="button select-options">
                <?php _e('Seleccionar opciones', 'clevers-product-carousel'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>