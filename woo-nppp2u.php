<?php

/**
 * Plugin Name:       Woocommerce for NPPP2u
 * Plugin URI:        https://nppp2u.co.uk/wp/plugins/woo-nppp2u
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
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

// If this file is called directly, abort.
class Woo_NPPP2U {

	function __constructor() {

	}

	static function np_activate() {
		flush_rewrite_rules();
	}

	static function np_register_api_hooks() {


		/**		==================
		 * 		ORDER ENDPOINTS
		 * 		==================
		 */
		// Get customer
		register_rest_route( 'np/v1', 'order/create/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'np_create_order' )
		));
	
		/**		==================
		 * 		CUSTOMER ENDPOINTS
		 * 		==================
		 */
		// Get customer
		register_rest_route( 'np/v1', 'customer/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'np_get_customer' )
		));
	
		// Get customer by email
		register_rest_route( 'np/v1', 'customer/email/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'np_get_customer_by_email' )
		));
	
		/**		=================
		 * 		PRODUCT ENDPOINTS
		 * 		=================
		 */
		// Get Products
		register_rest_route( 'np/v1', 'products/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'np_get_products' )
		));
	
		// Get A Single Product
		register_rest_route( 'np/v1', 'products/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'np_get_product' )
		));
	
	}		

	/**
	 * Create an order
	 *
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function np_create_order( WP_REST_Request $request ) {

		$order_details = $request['order_details'];

		$woo = new Woo_Order();

		if ( $order = $woo->create_order( $order_details ) ) {
			return new WP_REST_Response( $order, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Get a Customer
	 *
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function np_get_customer( WP_REST_Request $request ) {

		$customer_id = $request['id'];

		$woo = new Woo_Customer();

		if ( $customer = $woo->get_customer( $customer_id ) ) {
			return new WP_REST_Response( $customer, 200 );
		}
		else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Get a Customer By Email
	 *
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function np_get_customer_by_email( WP_REST_Request $request ) {

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
	 * LIST PRODUCTS
	 *
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function np_get_products( WP_REST_Request $request ) {

		$woo = new Woo_Product();

		if ( $products = $woo->get_products() ) {
			return new WP_REST_Response( $products, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}
			
	}

	/**
	 * GET A SINGLE PRODUCT
	 *
	 * @param WP_REST_Request $request
	 * @return void
	 */
	static function np_get_product( WP_REST_Request $request ) {

		$product_id = $request['id'];

		$woo = new Woo_Product();

		if ( $product = $woo->get_product( $product_id ) ) {
			return new WP_REST_Response( $product, 200 );
		} else {
			// return an 404 empty result set
			return new WP_REST_Response( array(), 404 );
		}
	}

	static function init() {
		register_activation_hook( __FILE__, array( 'Woo_NPPP2U', 'np_activate' ) );
		add_action( 'rest_api_init', array( 'Woo_NPPP2U', 'np_register_api_hooks' ) );
	}

}

$woo_nppp2u = new Woo_NPPP2U();
$woo_nppp2u->init();
