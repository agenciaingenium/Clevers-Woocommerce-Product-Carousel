<?php

define( 'ABSPATH', __DIR__ );
define( 'CLV_DIR', dirname( __DIR__ ) . '/' );
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}
if ( ! defined( 'CLV_SLUG' ) ) {
	define( 'CLV_SLUG', 'clevers_carousel' );
}

$GLOBALS['mock_state'] = array(
	'post_meta'             => array(),
	'posts'                 => array(),
	'options'               => array(),
	'transients'            => array(),
	'set_transients'        => array(),
	'filters'               => array(),
	'locate_template_value' => '',
	'referer'               => '',
	'product_ids_on_sale'   => array(),
	'featured_product_ids'  => array(),
);

function reset_mock_state() {
	$GLOBALS['mock_state']['post_meta'] = array();
	$GLOBALS['mock_state']['posts'] = array();
	$GLOBALS['mock_state']['options'] = array();
	$GLOBALS['mock_state']['transients'] = array();
	$GLOBALS['mock_state']['set_transients'] = array();
	$GLOBALS['mock_state']['filters'] = array();
	$GLOBALS['mock_state']['locate_template_value'] = '';
	$GLOBALS['mock_state']['referer'] = '';
	$GLOBALS['mock_state']['product_ids_on_sale'] = array();
	$GLOBALS['mock_state']['featured_product_ids'] = array();

	$_REQUEST = array();
	WC_Product_Query::$construct_count = 0;
	WC_Product_Query::$products = array();
}

class WP_Post {
	public $post_content = '';
	public $post_type = '';
}

class WooCommerce {}

class WC_Product_Query {
	public static $construct_count = 0;
	public static $products = array();

	public function __construct( $args ) {
		unset( $args );
		self::$construct_count++;
	}

	public function get_products() {
		return self::$products;
	}
}

function get_post_meta( $id, $key = '', $single = false ) {
	unset( $key, $single );
	return $GLOBALS['mock_state']['post_meta'][ $id ] ?? array();
}
function get_post( $id ) { return $GLOBALS['mock_state']['posts'][ $id ] ?? null; }
function get_option( $name, $default = false ) { return $GLOBALS['mock_state']['options'][ $name ] ?? $default; }
function update_option( $name, $value, $autoload = false ) { unset( $autoload ); $GLOBALS['mock_state']['options'][ $name ] = $value; return true; }
function get_transient( $key ) { return $GLOBALS['mock_state']['transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['mock_state']['set_transients'][ $key ] = array( 'value' => $value, 'ttl' => $ttl ); return true; }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function sanitize_title( $value ) { return strtolower( trim( preg_replace( '/[^a-zA-Z0-9\-_]+/', '-', (string) $value ), '-' ) ); }
function sanitize_hex_color( $value ) { return preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', (string) $value ) ? strtolower( $value ) : ''; }
function wp_parse_args( $args, $defaults ) { return array_merge( $defaults, (array) $args ); }
function wp_json_encode( $value ) { return json_encode( $value ); }
function wp_unslash( $value ) { return stripslashes( (string) $value ); }
function wp_get_referer() { return $GLOBALS['mock_state']['referer']; }

function __( $text, $domain = null ) { unset( $domain ); return $text; }
function current_user_can( $cap ) { unset( $cap ); return true; }
function delete_transient( $key ) { unset( $GLOBALS['mock_state']['transients'][ $key ] ); return true; }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function wp_upload_dir() { return array( 'basedir' => sys_get_temp_dir() ); }
function apply_filters( $hook, $value ) {
	$args = func_get_args();
	array_shift( $args );
	$value = array_shift( $args );
	foreach ( $GLOBALS['mock_state']['filters'][ $hook ] ?? array() as $cb ) {
		$value = $cb( $value, ...$args );
	}
	return $value;
}
function add_filter( $hook, $callback ) { $GLOBALS['mock_state']['filters'][ $hook ][] = $callback; return true; }
function do_action( $hook ) { unset( $hook ); }
function locate_template( $template ) { unset( $template ); return $GLOBALS['mock_state']['locate_template_value']; }
function shortcode_atts( $pairs, $atts ) { return array_merge( $pairs, (array) $atts ); }
function is_admin() { return false; }
function is_singular() { return true; }
function get_queried_object() { return new WP_Post(); }
function has_shortcode( $content, $tag ) { unset( $content, $tag ); return false; }
function has_block( $name, $post = null ) { unset( $name, $post ); return false; }
function wp_enqueue_style( $handle ) { unset( $handle ); }
function wp_enqueue_script( $handle ) { unset( $handle ); }
function wp_add_inline_style( $handle, $css ) { unset( $handle, $css ); }
function wc_get_product_ids_on_sale() { return $GLOBALS['mock_state']['product_ids_on_sale']; }
function wc_get_featured_product_ids() { return $GLOBALS['mock_state']['featured_product_ids']; }
function get_post_type( $id ) { return $GLOBALS['mock_state']['posts'][ $id ]->post_type ?? ''; }

require_once dirname( __DIR__ ) . '/includes/functions.php';
require_once dirname( __DIR__ ) . '/includes/class-render.php';

require_once dirname( __DIR__ ) . '/includes/class-health-check.php';
