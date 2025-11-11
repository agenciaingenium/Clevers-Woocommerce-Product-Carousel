<?php
/** @var WC_Product $product */
/** @var array $settings */

$discount = clv_get_discount_percentage($product, 'max'); // o 'min' / 'avg'
?>
<div class="clevers-product card-2">
    <div class="product-media">
        <?php if ($discount !== null): ?>
            <span class="clv-badge-round"><?php echo esc_html($discount); ?>%</span>
        <?php endif; ?>
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-thumb">
            <?php echo $product->get_image('woocommerce_thumbnail'); ?>
        </a>
    </div>

    <a class="product-title" href="<?php echo esc_url($product->get_permalink()); ?>">
        <?php echo esc_html(mb_strtoupper($product->get_name())); ?>
    </a>

    <div class="price-area">
        <?php echo wp_kses_post($product->get_price_html()); ?>
    </div>

    <div class="actions">
        <?php if ($product->is_type('simple')): ?>
            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
               class="clevers-button ajax_add_to_cart add_to_cart_button"
               data-product_id="<?php echo esc_attr($product->get_id()); ?>"
               data-product_sku="<?php echo esc_attr($product->get_sku()); ?>">
                <?php esc_html_e('Añadir al carrito', 'clevers-product-carousel'); ?>
            </a>
        <?php else: ?>
            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="clevers-button">
                <?php esc_html_e('Seleccionar opciones', 'clevers-product-carousel'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>