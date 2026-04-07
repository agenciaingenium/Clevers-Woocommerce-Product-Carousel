<?php
/**
 * Plugin Name: Clevers Product Carousel
 * Plugin URI:  https://github.com/agenciaingenium/Clevers-Woocommerce-Product-Carousel/
 * Description: Create customizable WooCommerce product carousels with server-side rendering and theme-overridable templates.
 * Author:      Clevers Devs
 * Author URI:  https://clevers.dev
 * Version: 1.2.3
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Tested up to:      6.9
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: clevers-product-carousel
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
//  Constantes
// -----------------------------------------------------------------------------
if ( ! defined( 'CLV_SLUG' ) ) {
	define( 'CLV_SLUG', 'clevers_carousel' );
}

if ( ! defined( 'CLV_DIR' ) ) {
	define( 'CLV_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'CLV_URL' ) ) {
	define( 'CLV_URL', plugin_dir_url( __FILE__ ) );
}

// -----------------------------------------------------------------------------
//  Includes & Init
// -----------------------------------------------------------------------------
require_once CLV_DIR . 'includes/functions.php';
require_once CLV_DIR . 'includes/helpers-discount.php';
require_once CLV_DIR . 'includes/class-cpt.php';
require_once CLV_DIR . 'includes/class-admin.php';
require_once CLV_DIR . 'includes/class-render.php';
require_once CLV_DIR . 'includes/class-health-check.php';

function clevers_product_carousel_load_textdomain() {
	load_plugin_textdomain( 'clevers-product-carousel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'clevers_product_carousel_load_textdomain' );

function clevers_product_carousel_init() {
	$cpt = new Clevers_Product_Carousel_CPT();
	$cpt->init();

	if ( is_admin() ) {
		$admin = new Clevers_Product_Carousel_Admin();
		$admin->init();
	}

	$render = new Clevers_Product_Carousel_Render();
	$render->init();
}

clevers_product_carousel_init();

register_activation_hook( __FILE__, array( 'Clevers_Product_Carousel_Health_Check', 'run_on_activation' ) );
add_action( 'admin_notices', array( 'Clevers_Product_Carousel_Health_Check', 'render_activation_notice' ) );
