<?php
/**
 * Plugin Name:       WooCommerce API Client for Scratby Premier
 * Plugin URI:        https://scratbygardencentre.com/wp/plugins/woo-nppp2u
 * Description:       WooCommerce API Client for Scratby Premier
 * GitHub Plugin URI: https://github.com/gerrytucker/woo-premier
 * Version:           2.8.0
 * Author:            Gerry Tucker
 * Author URI:        https://gerrytucker@gerrytucker.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-premier
 * Domain Path:       /languages
 * WC requires at least: 4.0.0
 * WC tested up to: 4.9.0
 */
require_once('woocommerce-api.php');
require_once('classes/class_product.php');
require_once('classes/class_category.php');
require_once('classes/class_image.php');


class Woo_Premier {

	// API Version
	const API_VERSION = 'premier/v2';

	/**
	 * Set up the client
	 *
	 * @since 1.0.0
	 */
	function __constructor() {}

	/**
	 * Activate the plugin
	 *
	 * @since 1.0.0
	 */
	public function woo_activate() {}

  /**
   * Register API routes
   *
   * @since 1.0.0
   */
	public function woo_register_api_hooks() {

    self::register_product_routes();

	}

  /**
   * Register product routes
   *
   * @since 1.0.0
   */
  public function register_product_routes() {

		// Get Categories
		register_rest_route( self::API_VERSION, 'categories/', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_product_categories' )
		));

		// Get Category
		register_rest_route( self::API_VERSION, 'category/(?P<id>\d+)', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_product_category' )
		));

		// Get Products
		register_rest_route( self::API_VERSION, 'products/', array(
			'methods'	=> 'GET',
      'callback'	=> array( 'Woo_Premier', 'woo_get_products' ),
      'args'	=>  array('page', 'limit')
		));

		// Get Product
		register_rest_route( self::API_VERSION, 'product/(?P<id>\d+)', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_product' )
		));

		// Get Products
		register_rest_route( self::API_VERSION, 'products/cat_slug/', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_products_by_cat_slug/<?P<slug>[a-zA-Z0-9._-]+' )
		));

		// Get Products by Barcode
		register_rest_route( self::API_VERSION, 'products/barcode/(?P<barcode>\d+)', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_products_by_barcode' )
		));

		// Get Products by SKU
		register_rest_route( self::API_VERSION, 'products/sku/(?P<sku>\d+)', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_products_by_sku' )
		));

		// Update product stock quantity
		register_rest_route( self::API_VERSION, 'products/stock/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array('Woo_Premier', 'woo_update_stock_qty'),
			'args' => array('qty')
		));

	}

	/**
	 * Get products
	 *
   * @since 1.0.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_products( WP_REST_Request $request ) {

    $page = $request['page'];
    $limit = $request['limit'];

		$woo = new Woo_Product();

		if ( $products = $woo->get_products($page, $limit) ) {
			return new WP_REST_Response( $products, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}

	}

	/**
	 * Get products by Barcode
	 *
   * @since 2.2.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_products_by_barcode( WP_REST_Request $request ) {

		$woo = new Woo_Product();

		$barcode = $request['barcode'];

		if ( $products = $woo->get_products_by_barcode($barcode) ) {
			return new WP_REST_Response( $products, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}

	}

	/**
	 * Get products by SKU
	 *
   * @since 2.3.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_products_by_sku( WP_REST_Request $request ) {

		$woo = new Woo_Product();

		$sku = $request['sku'];

		if ( $products = $woo->get_products_by_sku($sku) ) {
			return new WP_REST_Response( $products, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}

	}

	/**
	 * Update product stock quantity
	 *
   * @since 2.7.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_update_stock_qty( WP_REST_Request $request ) {

		$woo = new Woo_Product();

		$product_id = $request['id'];
		$new_stock_quantity = $request['qty'];

		if ($products = $woo->update_product_qty($product_id, $new_stock_quantity) ) {
			return new WP_REST_Response( $products, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}

	}

	/**
	 * Get product
	 *
   * @since 1.0.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_product( WP_REST_Request $request ) {

		$woo = new Woo_Product();

		$id = $request['id'];

		if ( $products = $woo->get_product($id) ) {
			return new WP_REST_Response( $products, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}

	}

	/**
	 * Get products by category slug
	 *
   * @since 2.0.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_products_by_cat_slug( WP_REST_Request $request ) {

		$woo = new Woo_Product();

		$cat_slug = $request['slug'];

		if ( $products = $woo->get_products_by_cat_slug($cat_slug) ) {
			return new WP_REST_Response( $products, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}

	}

	/**
	 * Get product categories
	 *
   * @since 1.0.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_product_categories( WP_REST_Request $request ) {

		$woo = new Woo_Product_Category();

		if ( $categories = $woo->get_product_categories() ) {
			return new WP_REST_Response( $categories, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Get product category
	 *
   * @since 2.1.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_product_category( WP_REST_Request $request ) {

		$woo = new Woo_Product_Category();

		$id = $request['id'];

		if ( $category = $woo->get_product_category($id) ) {
			return new WP_REST_Response( $category, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}

	}

	/**
	 * Initialize plugin
	 *
	 * @since 1.0.0
	 */
	static function init() {
		register_activation_hook( __FILE__, array( 'Woo_Premier', 'woo_activate' ) );
		add_action( 'rest_api_init', array( 'Woo_Premier', 'woo_register_api_hooks' ) );
	}

}

$Woo_Premier = new Woo_Premier();
$Woo_Premier->init();
