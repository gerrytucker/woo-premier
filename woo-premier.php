<?php

/**
 * Plugin Name:       Premier for WooCommerce
 * Plugin URI:        https://scratbygardencentre.com/wp/plugins/woo-nppp2u
 * Description:       WooCommerce API Client for Scratby Premier
 * GitHub Plugin URI: https://github.com/gerrytucker/woo-premier
 * Version:           1.0.15
 * Author:            Gerry Tucker
 * Author URI:        https://gerrytucker@gerrytucker.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-premier
 * Domain Path:       /languages
 */

require_once('woocommerce-api.php');
require_once('classes/class_product.php');
require_once('classes/class_category.php');


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

		// Get Product
		register_rest_route( self::API_VERSION, 'product/(?P<id>\d+)', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_product' )
		));

		// Get Products
		register_rest_route( self::API_VERSION, 'products/', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_products' )
		));

		// Get Categories
		register_rest_route( self::API_VERSION, 'categories/', array(
			'methods'	=> 'GET',
			'callback'	=> array( 'Woo_Premier', 'woo_get_categories' )
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

		$woo = new Woo_Product();

		if ( $products = $woo->get_products() ) {
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

		$id = $request['id'];

		$woo = new Woo_Product();

		if ( $product = $woo->get_product( $id ) ) {
			return new WP_REST_Response( $product, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Get categories
	 *
   * @since 1.0.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_categories( WP_REST_Request $request ) {

		$woo = new Woo_Category();

		if ( $categories = $woo->get_categories() ) {
			return new WP_REST_Response( $categories, 200 );
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
