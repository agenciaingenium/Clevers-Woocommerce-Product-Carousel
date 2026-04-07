<?php

use PHPUnit\Framework\TestCase;

final class RenderTest extends TestCase {
	protected function setUp(): void {
		reset_mock_state();
	}

	public function test_render_carousel_uses_cache_hit_without_querying_products(): void {
		$post = new WP_Post();
		$post->post_type = CLV_SLUG;
		$GLOBALS['mock_state']['posts'][21] = $post;
		$GLOBALS['mock_state']['post_meta'][21] = array();

		$args = clevers_product_carousel_build_query_args( 21 );
		$settings = clevers_product_carousel_get_settings( 21 );
		$cache_key = 'clv_carousel_21_v0_g0_' . md5( wp_json_encode( $args ) . '|' . wp_json_encode( $settings ) );
		$GLOBALS['mock_state']['transients'][ $cache_key ] = '<div>cached</div>';

		$renderer = new Clevers_Product_Carousel_Render();
		$html = $renderer->render_carousel( 21 );

		$this->assertStringContainsString( 'cached', $html );
		$this->assertSame( 0, WC_Product_Query::$construct_count );
	}

	public function test_render_carousel_cache_miss_queries_and_stores_transient(): void {
		$post = new WP_Post();
		$post->post_type = CLV_SLUG;
		$GLOBALS['mock_state']['posts'][22] = $post;
		$GLOBALS['mock_state']['post_meta'][22] = array();
		$GLOBALS['mock_state']['locate_template_value'] = dirname( __DIR__ ) . '/fixtures/simple-template.php';

		$renderer = new Clevers_Product_Carousel_Render();
		$html = $renderer->render_carousel( 22 );

		$this->assertStringContainsString( 'Rendered from fixture', $html );
		$this->assertSame( 1, WC_Product_Query::$construct_count );
		$this->assertNotEmpty( $GLOBALS['mock_state']['set_transients'] );
	}
}
