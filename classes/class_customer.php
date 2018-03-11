<?php
/**
 * NPPP2U Customer API Client
 */
require_once 'class_product.php';

class Woo_Customer {

  /** Version */
  const VERSION = "2.0.0";

  /**
   * Set up the client
   */
  public function __constructor() {}

  /**
   * Get customer
   * 
   * @since 2.0.0
   * @param int $id
   */
  public function get_customer( $id ) {
    $response = array();
    
    if ( $customer = new WC_Customer( $id ) ) {
      $response = array(
        'customer'        => array(
          'id'            => $customer->get_id(),
          'first_name'    => $customer->get_first_name(),
          'last_name'     => $customer->get_last_name(),
          'email'         => $customer->get_email(),
          'username'      => $customer->get_username(),
          'display_name'  => $customer->get_display_name(),
          'billing'       => array(
            'first_name'  => $customer->get_billing_first_name(),
            'last_name'   => $customer->get_billing_last_name(),
            'company'     => $customer->get_billing_company(),
            'address_1'   => $customer->get_billing_address_1(),
            'address_2'   => $customer->get_billing_address_2(),
            'city'        => $customer->get_billing_city(),
            'state'       => $customer->get_billing_state(),
            'postcode'    => $customer->get_billing_postcode(),
            'country'     => $customer->get_billing_country(),
            'email'       => $customer->get_billing_email(),
            'phone'       => $customer->get_billing_phone(),
          ),
          'shipping'       => array(
            'first_name'  => $customer->get_billing_first_name(),
            'last_name'   => $customer->get_billing_last_name(),
            'company'     => $customer->get_billing_company(),
            'address_1'   => $customer->get_billing_address_1(),
            'address_2'   => $customer->get_billing_address_2(),
            'city'        => $customer->get_billing_city(),
            'state'       => $customer->get_billing_state(),
            'postcode'    => $customer->get_billing_postcode(),
            'country'     => $customer->get_billing_country(),
          ),
          'order_count'   => $customer->get_order_count(),
          'orders'        => self::get_customer_orders( $id ),
          'open_orders'   => self::get_customer_open_orders( $id ),
          'total_spent'   => $customer->get_total_spent(),
        )
      );

    }

    return $response;
    
  }

  /**
   * Get customer by email
   * @since 2.0.0
   * @param str $email
   */
  public function get_customer_by_email ( $email ) {
    global $wpdb;

    $id = $wpdb->get_var( $wpdb->prepare("
      SELECT user_id
      FROM $wpdb->usermeta
      WHERE meta_key = 'billing_email'
      AND meta_value = '%s'
    ", $email));

    return self::get_customer( $id );
  }

  /**
   * Get customer orders
   * 
   * @since 2.0.0
   * @param int $id customer id
   */
  public function get_customer_orders( $id ) {
    $response = array(); $response_items = array();

    $orders = wc_get_orders( array( 'customer_id' => $id, 'status' => array('processing', 'completed'), 'orderby' => 'ID' ) );
    foreach ($orders as $order) {
      $date_created = $order->get_date_created();
      $response_items = array();
      $items = $order->get_items();
      foreach ( $items as $item ) {
        $product = $item->get_product();
        $response_items[] = array(
          'id'              => $item->get_product_id(),
          'name'            => $product->get_name(),
          'price'           => $product->get_price(),
          'regular_price'   => $product->get_regular_price(),
          'sale_price'      => $product->get_sale_price(),
          'qty'             => $item->get_quantity(),
          'total'           => $item->get_total(),
          'thumbnail_url'   => $thumbnail[0],
        );
      }
      $response[] = array(
        'id'                => $order->get_id(),
        'date_created' 	    => $date_created->date('d M Y @ H:i:s'),
        'status' 		        => $order->get_status(),
        'billing_address'   => $order->get_formatted_billing_address(),
        'billing' 		    => array(
          'first_name' 	    => $order->get_billing_first_name(),
          'last_name' 	    => $order->get_billing_last_name(),
          'company' 		    => $order->get_billing_company(),
          'address_1' 	    => $order->get_billing_address_1(),
          'address_2' 	    => $order->get_billing_address_2(),
          'city' 			      => $order->get_billing_city(),
          'state' 		      => $order->get_billing_state(),
          'postcode' 		    => $order->get_billing_postcode(),
          'country' 		    => $order->get_billing_country(),
          'email' 		      => $order->get_billing_email(),
          'phone' 		      => $order->get_billing_phone(),
        ),
        'shipping_address'  => $order->get_formatted_shipping_address(),
        'shipping' 	      => array(
          'first_name' 	    => $order->get_shipping_first_name(),
          'last_name' 	    => $order->get_shipping_last_name(),
          'company' 		    => $order->get_shipping_company(),
          'address_1' 	    => $order->get_shipping_address_1(),
          'address_2' 	    => $order->get_shipping_address_2(),
          'city' 			      => $order->get_shipping_city(),
          'state' 		      => $order->get_shipping_state(),
          'postcode' 		    => $order->get_shipping_postcode(),
          'country' 		    => $order->get_shipping_country()
        ),
        'items'             => $response_items  
      );
    }

    return $response;
  }
  
  /**
   * Get customer open orders
   * 
   * @since 2.0.0
   * @param int $id customer id
   */
  public function get_customer_open_orders( $id ) {
    $response = array(); $response_items = array();

    $orders = wc_get_orders( array( 'customer_id' => $id, 'status' => array('pending'), 'orderby' => 'ID' ) );
    foreach ( $orders as $order ) {
      $date_created = $order->get_date_created();
      $response_items = array();
      $ord = wc_get_order( $order->get_id() );
      foreach ( $ord->get_items() as $item => $item_data ) {
        $product = wc_get_product( $item_data['product_id'] );
        $response_items[] = array(
          'id'            => $item_data['product_id'],
          'name'          => $item_data['name'],
          'price'         => $product->get_price(),
          'regular_price' => $product->get_regular_price(),
          'sale_price'    => $product->get_sale_price(),
          'thumbnail_url' => $thumbnail[0],
          'qty'           => $order->get_item_meta( $item_id, '_qty', true ),
          'total'         => $order->get_item_meta( $item_id, '_line_total', true )
        );
      }
      $response[] = array(
        'id'                => $order->get_id(),
        'date_created' 	    => $date_created->date('d M Y @ H:i:s'),
        'status' 		        => $order->get_status(),
        'billing_address'   => $order->get_formatted_billing_address(),
        'billing' 		    => array(
          'first_name' 	    => $order->get_billing_first_name(),
          'last_name' 	    => $order->get_billing_last_name(),
          'company' 		    => $order->get_billing_company(),
          'address_1' 	    => $order->get_billing_address_1(),
          'address_2' 	    => $order->get_billing_address_2(),
          'city' 			      => $order->get_billing_city(),
          'state' 		      => $order->get_billing_state(),
          'postcode' 		    => $order->get_billing_postcode(),
          'country' 		    => $order->get_billing_country(),
          'email' 		      => $order->get_billing_email(),
          'phone' 		      => $order->get_billing_phone(),
        ),
        'shipping_address'  => $order->get_formatted_shipping_address(),
        'shipping' 	      => array(
          'first_name' 	    => $order->get_shipping_first_name(),
          'last_name' 	    => $order->get_shipping_last_name(),
          'company' 		    => $order->get_shipping_company(),
          'address_1' 	    => $order->get_shipping_address_1(),
          'address_2' 	    => $order->get_shipping_address_2(),
          'city' 			      => $order->get_shipping_city(),
          'state' 		      => $order->get_shipping_state(),
          'postcode' 		    => $order->get_shipping_postcode(),
          'country' 		    => $order->get_shipping_country()
        ),
        'items'             => $response_items  
      );
    }

    return $response;
  }
  
}
