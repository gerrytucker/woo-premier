<?php

/**
 * Plugin Name:       NPPP2u for WooCommerce
 * Plugin URI:        https://nppp2u.co.uk/wp/plugins/woo-nppp2u
 * Description:       WooCommerce API Client for Norfolk Produce Prepacked 2U
 * GitHub Plugin URI: https://github.com/gerrytucker/woo-premier
 * Version:           1.0.0
 * Author:            Gerry Tucker
 * Author URI:        https://gerrytucker@gerrytucker.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-nppp2u
 * Domain Path:       /languages
 */

require_once('woocommerce-api.php');
require_once('classes/class_product.php');


class Woo_Premier {

	// API Version
	const API_VERSION = 'premier/v2';

	/**
	 * Set up the client
	 * 
	 * @since 2.0.0
	 */
	function __constructor() {}

	/**
	 * Activate the plugin
	 * 
	 * @since 2.0.0
	 */
	public function woo_activate() {}

  /**
   * Register API routes
   * 
   * @since 2.0.0
   */
	public function woo_register_api_hooks() {

    self::register_customer_routes();
    self::register_product_routes();
    self::register_order_routes();
    self::register_cart_routes();

	}		

  /**
   * Register product routes
   * 
   * @since 2.0.0
   */
  public function register_product_routes() {

		// Get Product
		register_rest_route( self::API_VERSION, 'product/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_Premier', 'woo_get_product' )
		));

		// Get Products
		register_rest_route( self::API_VERSION, 'products/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_Premier', 'woo_get_products' )
		));

	}	
	
	/**
	 * Get products
	 *
     * @since 2.0.0
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
   * @since 2.0.0
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
	 * Initialize plugin
	 * 
	 * @since 2.0.0
	 */
	static function init() {
		register_activation_hook( __FILE__, array( 'Woo_Premier', 'woo_activate' ) );
		add_action( 'rest_api_init', array( 'Woo_Premier', 'woo_register_api_hooks' ) );
	}

}

$Woo_Premier = new Woo_Premier();
$Woo_Premier->init();
