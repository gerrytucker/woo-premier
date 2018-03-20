<?php

/**
 * Plugin Name:       NPPP2u for WooCommerce
 * Plugin URI:        https://nppp2u.co.uk/wp/plugins/woo-nppp2u
 * Description:       WooCommerce API Client for Norfolk Produce Prepacked 2U
 * Version:           2.0.4
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
require_once('classes/class_cart.php');


class Woo_NPPP2U {

	// API Version
	const API_VERSION = 'np/v2';

	// DB Version
	const DB_VERSION = '1.1';

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
	public function woo_activate() {
		self::woo_db_install();
	}

	/**
	 * Check db version
	 * 
	 * @since 2.0.0
	 */
	public function woo_update_db_check() {
		global $np_db_version;
		if ( get_site_option( 'np_db_version' ) != $np_db_version ) {
			self::woo_db_install();
		}
	}

	/**
	 * Install db
	 * 
	 * @since 2.0.0
	 */
	public function woo_db_install() {
		global $wpdb;

		$table = $wpdb->prefix . 'cart';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table (
			customer_id mediumint(9) NOT NULL,
			product_id mediumint(9) NOT NULL,
			line_qty mediumint(9) NOT NULL,
			UNIQUE KEY customer_product (customer_id, product_id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );
		add_option( 'np_db_version', self::DB_VERSION );

	}

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
   * Register customer function routes
   * 
   * @since 2.0.0
   */
  public function register_customer_routes() {

		// Get customer
		register_rest_route( self::API_VERSION, 'customer/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer' )
		));
	
		// Get customer by email
		register_rest_route( self::API_VERSION, 'customer/email/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer_by_email' )
		));
    
		// Get customer orders (processing/completed)
		register_rest_route( self::API_VERSION, 'customer/orders/completed', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer_completed_orders' )
		));
    
		// Get customer orders (pending="open")
		register_rest_route( self::API_VERSION, 'customer/orders/open', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_customer_open_orders' )
		));
    
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
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_product' )
		));

		// Get Products
		register_rest_route( self::API_VERSION, 'products/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_products' )
		));

		// Get product in order
		register_rest_route( self::API_VERSION, 'order/(?P<order_id>\d+)/product/(?P<product_id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_product_in_order' )
		));
		
		// Remove product from order
		register_rest_route( self::API_VERSION, 'order/(?P<order_id>\d+)/remove/(?P<product_id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_remove_product_from_order' )
		));
		
	}	
	
	/**
	 * Register order routes
	 * 
	 * @since 2.0.0
	 */
	public function register_order_routes() {
		
		// Get order
		register_rest_route( self::API_VERSION, 'order/(?P<id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_order' )
		));
		
		// Complete order
		register_rest_route( self::API_VERSION, 'order/(?P<order_id>\d+)/complete/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_complete_order' )
		));
		
	}

	/**
	 * Register cart routes
	 * 
	 * @since 2.0.0
	 */
	public function register_cart_routes() {
		
		// Get customer cart
		register_rest_route( self::API_VERSION, 'customer/(?P<customer_id>\d+)/cart/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_cart' )
		));
		
		// Clear customer cart
		register_rest_route( self::API_VERSION, 'customer/(?P<customer_id>\d+)/cart/clear/', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_clear_cart' )
		));
		
		// Get customer cart item
		register_rest_route( self::API_VERSION, 'customer/(?P<customer_id>\d+)/cart/(?P<product_id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_get_cart_item' )
		));
		
		// Update/add customer cart item
		register_rest_route( self::API_VERSION, 'customer/(?P<customer_id>\d+)/cart/(?P<product_id>\d+)/(?P<line_qty>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_update_cart_item' )
		));
		
		// Delete customer cart item
		register_rest_route( self::API_VERSION, 'customer/(?P<customer_id>\d+)/cart/delete/(?P<product_id>\d+)', array(
			'methods'	=> 'POST',
			'callback'	=> array( 'Woo_NPPP2U', 'woo_delete_cart_item' )
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
	 * Remove product from order
	 * 
	 * @since 2.0.0
	 * @param int order_id
	 * @param int product_id
	 */
	static function woo_remove_product_from_order( WP_REST_Request $request ) {
		$order_id = $request['order_id'];
		$product_id = $request['product_id'];
		
		$woo = new Woo_Order();
		$order = $woo->remove_product_from_order( $order_id, $product_id );
		
		return new WP_REST_Response( $order, 200 );
	}

	/**
	 * Get an order
	 * 
	 * @since 2.0.0
	 * @param int order_id
	 */
	static function woo_get_order( WP_REST_Request $request ) {
		$id = $request['id'];

		$woo = new Woo_Order();
		if ( $order = $woo->get_order( $id ) ) {
			return new WP_REST_Response( $order, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );			
		}

	}

	/**
	 * Complete an order
	 * 
	 * @since 2.0.0
	 * @param int order_id
	 */
	static function woo_complete_order( WP_REST_Request $request ) {
		$id = $request['order_id'];

		$woo = new Woo_Order();
		if ( $order = $woo->complete_order( $id ) ) {
			return new WP_REST_Response( $order, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );			
		}

	}

	/**
	 * Get customer cart
	 * 
	 * @since 2.0.0
	 * @param int customer_id
	 */
	public function woo_get_cart( WP_REST_Request $request ) {
		$id = $request['customer_id'];

		$woo = new Woo_Cart();
		if( $cart = $woo->get_cart( $id ) ) {
			return new WP_REST_Response( $cart, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Clear customer cart
	 * 
	 * @since 2.0.0
	 * @param int customer_id
	 */
	public function woo_clear_cart( WP_REST_Request $request ) {
		$id = $request['customer_id'];

		$woo = new Woo_Cart();
		if( $result = $woo->clear_cart( $id ) ) {
			return new WP_REST_Response( $result, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Get customer cart item
	 * 
	 * @since 2.0.0
	 * @param int customer_id
	 */
	public function woo_get_cart_item( WP_REST_Request $request ) {
		$customer_id = $request['customer_id'];
		$product_id = $request['product_id'];

		$woo = new Woo_Cart();
		if( $cart = $woo->get_cart_item( $customer_id, $product_id ) ) {
			return new WP_REST_Response( $cart, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Update/add cart item
	 * 
	 * @since 2.0.0
	 * @param int customer_id
	 * @param int product_id
	 * @param int line_qty
	 */
	public function woo_update_cart_item( WP_REST_Request $request ) {
		$customer_id = $request['customer_id'];
		$product_id = $request['product_id'];
		$line_qty = $request['line_qty'];

		$woo = new Woo_Cart();
		if( $cart = $woo->update_cart_item( $customer_id, $product_id, $line_qty ) ) {
			return new WP_REST_Response( $cart, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Delete customer cart item
	 * 
	 * @since 2.0.0
	 * @param int customer_id
	 * @param int product_id
	 */
	public function woo_delete_cart_item( WP_REST_Request $request ) {
		$customer_id = $request['customer_id'];
		$product_id = $request['product_id'];

		$woo = new Woo_Cart();
		if( $cart = $woo->delete_cart_item( $customer_id, $product_id ) ) {
			return new WP_REST_Response( $cart, 200 );
		} else {
			return new WP_REST_Response( array(), 404 );
		}
	}

	/**
	 * Initialize plugin
	 * 
	 * @since 2.0.0
	 */
	static function init() {
		register_activation_hook( __FILE__, array( 'Woo_NPPP2U', 'woo_activate' ) );
		add_action( 'rest_api_init', array( 'Woo_NPPP2U', 'woo_register_api_hooks' ) );
		add_action( 'plugins_loaded', array( 'Woo_NPPP2U', 'woo_update_db_check') );
	}

}

$woo_nppp2u = new Woo_NPPP2U();
$woo_nppp2u->init();
