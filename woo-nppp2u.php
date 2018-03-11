<?php

/**
 * Plugin Name:       Woocommerce for NPPP2u
 * Plugin URI:        https://nppp2u.co.uk/wp/plugins/woo-nppp2u
 * Description:       Woocommerce API Client for Norfolk Produce Prepacked 2U
 * Version:           2.0.0
 * Author:            Gerry Tucker
 * Author URI:        https://gerrytucker@gerrytucker.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-nppp2u
 * Domain Path:       /languages
 */

require_once('woocommerce-api.php');
require_once('classes/class_customer.php');
require_once('classes/class_product.php');
require_once('classes/class_order.php');


class Woo_NPPP2U {

	/**
	 * Set up the client
	 * @since 2.0.0
	 */
	function __constructor() {}

  /**
   * Activate the plugin
   * @since 2.0.0
   */
	public function woo_activate() {
		flush_rewrite_rules();
	}

  /**
   * Register API routes
   * @since 2.0.0
   */
	public function woo_register_api_hooks() {

    self::register_customer_routes();
    self::register_product_routes();

	}		

  /**
   * Register customer function routes
   * @since 2.0.0
   */
  public function register_customer_routes() {

		// Get customer
		register_rest_route( 'np/v2', 'customer/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer' )
		));
	
		// Get customer by email
		register_rest_route( 'np/v2', 'customer/email/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer_by_email' )
		));
    
		// Get customer orders (processing/completed)
		register_rest_route( 'np/v2', 'customer/orders/completed', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer_completed_orders' )
		));
    
		// Get customer orders (pending="open")
		register_rest_route( 'np/v2', 'customer/orders/open', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer_open_orders' )
		));
    
  }

  /**
   * Register product routes
   * @since 2.0.0
   */
  public function register_product_routes() {

		// Get Product
		register_rest_route( 'np/v2', 'product/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_product' )
		));

		// Get Products
		register_rest_route( 'np/v2', 'products/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_products' )
		));

		// Get product in order
		register_rest_route( 'np/v2', 'order/product/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_product_in_order' )
		));
		
	}	

	/**
	 * Get a Customer
	 *
   * @since 2.0.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_customer( WP_REST_Request $request ) {

		$id = $request['id'];

		$woo = new Woo_Customer();

		if ( $customer = $woo->get_customer( $id ) ) {
			return new WP_REST_Response( $customer, 200 );
		}
		else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Get a Customer By Email
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function woo_get_customer_by_email( WP_REST_Request $request ) {

		$email = $request['email'];

		$woo = new Woo_Customer();

		if ( $customer = $woo->get_customer_by_email( $email ) ) {
			return new WP_REST_Response( $customer, 200 );
		}
		else {
			return new WP_REST_Response( array(), 404 );
		}

	}

  /**
   * Get customer open orders
   * 
   * @since 2.0.0
   * @param int customer id
   */
  static function woo_get_customer_open_orders( WP_REST_Request $request ) {
    $id = $request['id'];

    $woo = new Woo_Customer();
    if ( $orders = $woo->get_customer_open_orders( $id ) ) {
      return new WP_REST_Response( $orders, 200 );
    }
    else {
      return new WP_REST_Response( array(), 404 );
    }

  }

  /**
   * Get customer completed orders
   * 
   * @since 2.0.0
   * @param int customer id
   */
  static function woo_get_customer_completed_orders( WP_REST_Request $request ) {
    $id = $request['id'];

    $woo = new Woo_Customer();
    if ( $orders = $woo->get_customer_orders( $id ) ) {
      return new WP_REST_Response( $orders, 200 );
    }
    else {
      return new WP_REST_Response( array(), 404 );
    }

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
	 * Get product in order
	 * 
	 * @since 2.0.0
	 * @param int order_id
	 * @param int product_id
	 */
	static function woo_get_product_in_order( WP_REST_Request $request ) {
		$order_id = $request['order_id'];
		$product_id = $request['product_id'];
		
		$woo = new Woo_Order();
		$product = $woo->product_in_order( $order_id, $product_id );
		
		return new WP_REST_Response( $product, 200 );
	}

	/**
	 * Initialize plugin
	 * 
	 * @since 2.0.0
	 */
	static function init() {
		register_activation_hook( __FILE__, array( 'Woo_NPPP2U', 'woo_activate' ) );
		add_action( 'rest_api_init', array( 'Woo_NPPP2U', 'woo_register_api_hooks' ) );
	}

}

$woo_nppp2u = new Woo_NPPP2U();
$woo_nppp2u->init();
