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
$discount = clv_get_discount_percentage($product, 'max'); // o 'min' / 'avg'
?>
<div class="clevers-card preset-1-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <a href="<?php echo esc_url($permalink); ?>" class="product-thumb">
        <?php if ($discount) : ?>
            <span class="badge-discount">-<?php echo esc_html($discount); ?>%</span>
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
               aria-label="<?php echo esc_attr(sprintf(__('Add %s to your cart', 'clevers-slider'), $title)); ?>">
                <?php _e('Añadir al carrito', 'clevers-slider'); ?>
            </a>
        <?php else : ?>
            <a href="<?php echo esc_url($permalink); ?>" class="button select-options">
                <?php _e('Seleccionar opciones', 'clevers-slider'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
    .clevers-card {
        text-align: center;
        position: relative;
        padding: 10px;
    }

    .clevers-card .product-thumb img {
        width: 100%;
        height: auto;
        border-radius: 6px;
    }

    .clevers-card .badge-discount {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #e11d48;
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .clevers-card .product-info {
        margin-top: 10px;
    }

    .clevers-card .product-title {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #222;
        text-decoration: none;
        margin-bottom: 4px;
    }

    .clevers-card .price-area {
        margin-bottom: 8px;
        font-size: 14px;
    }

    .clevers-card .button {
        background: #2563eb;
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 13px;
        text-decoration: none;
        display: inline-block;
    }

    .clevers-card .button:hover {
        background: #1e40af;
        color: #fff;
    }
</style>