<?php
/** @var int $carousel_id */
/** @var array $settings */
/** @var WC_Product[] $products */
?>
<div class="clevers-product-carousel preset-2" id="clevers-product-carousel-<?php echo (int)$carousel_id; ?>">
    <?php if (!empty($products)) : ?>
        <div class="slick-carousel"
             data-slides="<?php echo (int)($settings['slidesToShow'] ?? 4); ?>"
             data-autoplay="<?php echo !empty($settings['autoplay']) ? 'true' : 'false'; ?>"
             data-speed="<?php echo (int)($settings['autoplayMs'] ?? 3000); ?>"
             data-dots="<?php echo !empty($settings['dots']) ? 'true' : 'false'; ?>"
             data-arrows="<?php echo !empty($settings['arrows']) ? 'true' : 'false'; ?>"
             data-center="true"> <!-- centro activo para borde/zoom -->
            <?php foreach ($products as $product): ?>
                <div class="carousel-item">
                    <?php clv_render_card($product, $settings); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p><?php esc_html_e('No hay productos disponibles.', 'clevers-product-carousel'); ?></p>
    <?php endif; ?>
</div>