<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Clevers_Product_Carousel_Admin {

	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_' . CLV_SLUG, array( $this, 'save_meta_box' ), 10, 2 );
	}

	public function add_meta_boxes() {
		add_meta_box(
			'clv_carousel_settings',
			__( 'Carousel Settings', 'clevers-product-carousel' ),
			array( $this, 'render_meta_box' ),
			CLV_SLUG,
			'normal',
			'high'
		);
	}

	public function render_meta_box( $post ) {
		$meta = clevers_product_carousel_get_carousel_meta( $post->ID );

		wp_nonce_field( 'clv_save_carousel', 'clv_carousel_nonce' );

		$preset         = (int) ( $meta['preset'] ?? 1 );
		$limit          = (int) ( $meta['limit'] ?? 8 );
		$orderby        = isset( $meta['orderby'] ) ? esc_attr( $meta['orderby'] ) : 'date';
		$order          = isset( $meta['order'] ) ? esc_attr( $meta['order'] ) : 'DESC';
		$categories     = (array) ( $meta['categories'] ?? array() );
		$on_sale        = ! empty( $meta['on_sale'] );
		$on_featured    = ! empty( $meta['on_featured'] );
		$instock_only   = ! empty( $meta['instock_only'] );
		$slidesToShow   = (int) ( $meta['slidesToShow'] ?? 4 );
		$autoplay       = ! empty( $meta['autoplay'] );
		$autoplayMs     = (int) ( $meta['autoplayMs'] ?? 3000 );
		$dots           = ! empty( $meta['dots'] );
		$arrows         = ! empty( $meta['arrows'] );

		$color_primary      = isset( $meta['color_primary'] ) ? esc_attr( $meta['color_primary'] ) : '';
		$color_primary2     = isset( $meta['color_primary2'] ) ? esc_attr( $meta['color_primary2'] ) : '';
		$color_secondary    = isset( $meta['color_secondary'] ) ? esc_attr( $meta['color_secondary'] ) : '';
		$color_text         = isset( $meta['color_text'] ) ? esc_attr( $meta['color_text'] ) : '';
		$color_card_bg      = isset( $meta['color_card_bg'] ) ? esc_attr( $meta['color_card_bg'] ) : '';
		$color_border       = isset( $meta['color_border'] ) ? esc_attr( $meta['color_border'] ) : '';
		$bubble_background  = isset( $meta['bubble_background'] ) ? esc_attr( $meta['bubble_background'] ) : '';
		$bubble_text        = isset( $meta['bubble_text'] ) ? esc_attr( $meta['bubble_text'] ) : '';
		$button_background  = isset( $meta['button_background'] ) ? esc_attr( $meta['button_background'] ) : '';
		$button_text        = isset( $meta['button_text'] ) ? esc_attr( $meta['button_text'] ) : '';
		$button_background_color_value = ( 'transparent' === $button_background ) ? '' : $button_background;
		$button_text_color_value       = ( 'transparent' === $button_text ) ? '' : $button_text;

		?>
		<style>
			.clv-field {
				margin: 10px 0;
			}

			.clv-field label {
				display: block;
				font-weight: 600;
				margin-bottom: 4px;
			}
		</style>

		<div class="clv-field">
			<label for="clv[preset]">
				<?php esc_html_e( 'Preset / Design', 'clevers-product-carousel' ); ?>
			</label>
			<select name="clv[preset]">
				<option value="1" <?php selected( $preset, 1 ); ?>>Preset 1</option>
				<option value="2" <?php selected( $preset, 2 ); ?>>Preset 2</option>
				<option value="3" <?php selected( $preset, 3 ); ?>>Preset 3</option>
				<option value="4" <?php selected( $preset, 4 ); ?>>Preset 4</option>
			</select>
		</div>

		<div class="clv-field">
			<label><?php esc_html_e( 'Limit', 'clevers-product-carousel' ); ?></label>
			<input type="number" min="1" name="clv[limit]" value="<?php echo esc_attr( $limit ); ?>"/>
		</div>

		<div class="clv-field">
			<label><?php esc_html_e( 'Order By / Order', 'clevers-product-carousel' ); ?></label>
			<!-- Aquí tus selects reales de orderby / order -->
		</div>

		<div class="clv-field">
			<label><?php esc_html_e( 'Product Categories (slugs, comma separated)', 'clevers-product-carousel' ); ?></label>
			<input type="text" name="clv[categories_csv]" value="<?php echo esc_attr( implode( ',', $categories ) ); ?>"/>
			<small><?php esc_html_e( 'Usa slugs de product_cat (no categorías de posts).', 'clevers-product-carousel' ); ?></small>
		</div>

		<div class="clv-field">
			<label><?php esc_html_e( 'Filters', 'clevers-product-carousel' ); ?></label>
			<label>
				<input type="checkbox" name="clv[on_sale]" <?php checked( $on_sale ); ?> />
				<?php esc_html_e( 'On Sale', 'clevers-product-carousel' ); ?>
			</label>
			<label>
				<input type="checkbox" name="clv[on_featured]" <?php checked( $on_featured ); ?> />
				<?php esc_html_e( 'Featured', 'clevers-product-carousel' ); ?>
			</label>
			<label>
				<input type="checkbox" name="clv[instock_only]" <?php checked( $instock_only ); ?> />
				<?php esc_html_e( 'In Stock Only', 'clevers-product-carousel' ); ?>
			</label>
		</div>

		<hr/>

		<h3><?php esc_html_e( 'Carousel Options', 'clevers-product-carousel' ); ?></h3>

		<div class="clv-field">
			<label><?php esc_html_e( 'Slides To Show', 'clevers-product-carousel' ); ?></label>
			<input type="number" min="1" name="clv[slidesToShow]" value="<?php echo esc_attr( $slidesToShow ); ?>"/>
		</div>

		<div class="clv-field">
			<label>
				<input type="checkbox" name="clv[autoplay]" <?php checked( $autoplay ); ?> />
				<?php esc_html_e( 'Autoplay', 'clevers-product-carousel' ); ?>
			</label>
		</div>

		<div class="clv-field">
			<label><?php esc_html_e( 'Autoplay Speed (ms)', 'clevers-product-carousel' ); ?></label>
			<input type="number" min="500" step="100" name="clv[autoplayMs]" value="<?php echo esc_attr( $autoplayMs ); ?>"/>
		</div>

		<div class="clv-field">
			<label>
				<input type="checkbox" name="clv[dots]" <?php checked( $dots ); ?> />
				<?php esc_html_e( 'Dots', 'clevers-product-carousel' ); ?>
			</label>
			<label>
				<input type="checkbox" name="clv[arrows]" <?php checked( $arrows ); ?> />
				<?php esc_html_e( 'Arrows', 'clevers-product-carousel' ); ?>
			</label>
		</div>

		<hr/>

		<h3><?php esc_html_e( 'Colors', 'clevers-product-carousel' ); ?></h3>

		<div class="clv-field"><label>Primary</label>
			<input type="color" name="clv[color_primary]" value="<?php echo esc_attr( $color_primary ); ?>">
		</div>
		<div class="clv-field"><label>Primary (Hover)</label>
			<input type="color" name="clv[color_primary2]" value="<?php echo esc_attr( $color_primary2 ); ?>">
		</div>
		<div class="clv-field"><label>Secondary</label>
			<input type="color" name="clv[color_secondary]" value="<?php echo esc_attr( $color_secondary ); ?>">
		</div>
		<div class="clv-field"><label>Bubble Background</label>
			<input type="color" name="clv[bubble_background]" value="<?php echo esc_attr( $bubble_background ); ?>">
		</div>
		<div class="clv-field"><label>Bubble Text</label>
			<input type="color" name="clv[bubble_text]" value="<?php echo esc_attr( $bubble_text ); ?>">
		</div>
		<div class="clv-field"><label>Button Background</label>
			<input type="color" name="clv[button_background]" value="<?php echo esc_attr( $button_background ); ?>">
		</div>
		<div class="clv-field">
			<label>Button Text</label>
			<input type="color" name="clv[button_text]" value="<?php echo esc_attr( $button_text_color_value ); ?>">
			<label style="margin-left:8px;">
				<input type="checkbox"
					   name="clv[button_text_transparent]" <?php checked( 'transparent', $button_text ); ?> />
				<?php esc_html_e( 'Transparente', 'clevers-product-carousel' ); ?>
			</label>
		</div>
		<div class="clv-field"><label>Texto</label>
			<input type="color" name="clv[color_text]" value="<?php echo esc_attr( $color_text ); ?>">
		</div>
		<div class="clv-field"><label>Fondo Card</label>
			<input type="color" name="clv[color_card_bg]" value="<?php echo esc_attr( $color_card_bg ); ?>">
		</div>
		<div class="clv-field"><label>Border Product Info</label>
			<input type="color" name="clv[color_border]" value="<?php echo esc_attr( $color_border ); ?>">
		</div>
		<?php
	}

	public function save_meta_box( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( $post->post_type !== CLV_SLUG ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'clv_carousel_nonce', FILTER_DEFAULT );
		if ( empty( $nonce ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $nonce ) );
		if ( ! wp_verify_nonce( $nonce, 'clv_save_carousel' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$in = filter_input( INPUT_POST, 'clv', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( ! is_array( $in ) ) {
			$in = array();
		}
		$in = wp_unslash( $in );

		$out = array();
		$out['preset'] = max( 1, (int) ( $in['preset'] ?? 1 ) );
		$out['limit'] = max( 1, (int) ( $in['limit'] ?? 8 ) );
		$out['orderby'] = sanitize_text_field( $in['orderby'] ?? 'date' );
		$out['order'] = in_array( ( $in['order'] ?? 'DESC' ), array( 'ASC', 'DESC' ), true ) ? $in['order'] : 'DESC';
		$out['categories'] = array_filter(
			array_map(
				'sanitize_title',
				array_map(
					'trim',
					explode( ',', $in['categories_csv'] ?? '' )
				)
			)
		);
		$out['on_sale'] = ! empty( $in['on_sale'] );
		$out['on_featured'] = ! empty( $in['on_featured'] );
		$out['instock_only'] = ! empty( $in['instock_only'] );

		$out['slidesToShow'] = max( 1, (int) ( $in['slidesToShow'] ?? 4 ) );
		$out['autoplay'] = ! empty( $in['autoplay'] );
		$out['autoplayMs'] = max( 0, (int) ( $in['autoplayMs'] ?? 3000 ) );
		$out['dots'] = ! empty( $in['dots'] );
		$out['arrows'] = ! empty( $in['arrows'] );

		$text_fields = array(
			'color_primary',
			'color_primary2',
			'color_secondary',
			'bubble_background',
			'bubble_text',
			'color_accent',
			'color_text',
			'color_card_bg',
			'color_border',
			'button_background',
			'button_text',
		);

		foreach ( $text_fields as $field ) {
			$out[ $field ] = isset( $in[ $field ] ) ? sanitize_text_field( $in[ $field ] ) : '';
		}

		update_post_meta( $post_id, '_clv_settings', $out );

		$ver = (int) get_post_meta( $post_id, '_clv_cache_version', true );
		update_post_meta( $post_id, '_clv_cache_version', $ver + 1 );
	}
}
