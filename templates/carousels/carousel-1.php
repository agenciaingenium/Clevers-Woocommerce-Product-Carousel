<?php
// templates/carousels/carousel-1.php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var array $settings */
/** @var int $carousel_id */
/** @var WC_Product[] $products */
?>
<div
	class="clevers-product-carousel preset-1"
	id="clevers-product-carousel-<?php echo (int) $carousel_id; ?>"
	role="region"
	aria-roledescription="carousel"
	aria-label="<?php echo esc_attr( sprintf( __( 'Product carousel %d', 'clevers-product-carousel' ), (int) $carousel_id ) ); ?>"
>
	<?php if ( ! empty( $products ) ) : ?>
		<div class="slick-carousel" <?php echo clevers_product_carousel_get_slider_data_attributes( $carousel_id, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped in helper. ?>>
			<?php foreach ( $products as $clevers_product_carousel_product ) : ?>
				<div class="carousel-item">
					<?php clevers_product_carousel_render_card( $clevers_product_carousel_product, $settings ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'No hay productos disponibles.', 'clevers-product-carousel' ); ?></p>
	<?php endif; ?>
</div>
