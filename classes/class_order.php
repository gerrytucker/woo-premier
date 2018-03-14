<?php
/**
 * NPPP2U Order API Client
 */
class Woo_Order {

  /** Version */
  const VERSION = "2.0.0";

  /**
   * Set up the client
   */
  public function __constructor() {}

  /**
   * Create order
   * @since 2.0.0
   * @param array order details
   */
  public function create_order( $order_details ) {
    global $woocommerce;

    $customer = $order_details['customer'];

    // Create an order
    $order = wc_create_order(array(
      'status'        => 'pending',
      'customer_id'   => (is_numeric($customer['id']) ? absint($customer['id']) : 0)
    ));

    // Set addresses (billing|shipping)
    $order->set_address($customer['billing'], 'billing');
    $order->set_address($customer['shipping'], 'shipping');

    // Set payment method
    $order->set_payment_method('cod');

    // Set created via
    $order->set_created_via('app');

    // Get the order number
    $order_number = $order->get_order_number();

    // Update order customer user
    update_post_meta( $order_number, '_customer_user', $customer['id'] );

    // Return order
    return wc_get_order( is_numeric($order_number) ? absint($order_number) : 0 );
  }

  /**
   * Add item to order
   * @since 2.0.0
   */
  public function add_item ( $customer_id, $order_number=0, $item=array() )  {

    $woo = new Woo_Customer();
    $customer = $woo->get_customer( $customer_id );

    if ( $order_number == 0 ) {
      $order_number = self::create_order( $customer );
    }

  }

  /**
   * Get an order
   * 
   * @since 2.0.0
   * @param int order_id
   */
  public function get_order( $order_id ) {
    $response = array();

    if ( $order = new WC_Order( $order_id ) ) {
      $date_created = $order->get_date_created();
      $response_items = array();
      foreach ( $order->get_items() as $item => $item_data ) {
        $product = wc_get_product( $item_data['product_id'] );
        $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id(), 'thumbnail', false ) );
        $response_items[] = array(
          'id'            => $item_data['product_id'],
          'name'          => $item_data['name'],
          'price'         => $product->get_price(),
          'regular_price' => $product->get_regular_price(),
          'sale_price'    => $product->get_sale_price(),
          'thumbnail_url' => $thumbnail[0],
          'qty'           => $item_data['qty'],
          'total'         => $item_data['total']
        );
        $order_qty = $order_qty + absint($item_data['qty']);
        $order_total = $order_total + $item_data['total'];
      }
      $response = array(
        'id'                => $order->get_id(),
        'customer_id'       => $order->get_customer_id(),
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
        'items'             => $response_items,
        'qty'               => $order_qty,
        'total'             => $order_total
      );
      return $response;
    } else {
      return false;
    }

  }

  /**
   * Get product in order
   * 
   * @since 2.0.0
   * @param int order_id
   * @param int product_id
   */
  public function product_in_order( $order_id, $product_id ) {
    $product_in_order = false;
    $response_product = array();

    $product = wc_get_product( $product_id );
    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id(), 'thumbnail', false ) );

    $order = wc_get_order( $order_id );
    foreach ( $order->get_items() as $item => $item_data ) {
      if ( $item_data['product_id'] == $product_id ) {
        $response_product[] = array(
          'id'            => $item_data['product_id'],
          'name'          => $item_data['name'],
          'price'         => $product->get_price(),
          'regular_price' => $product->get_regular_price(),
          'sale_price'    => $product->get_sale_price(),
          'thumbnail_url' => $thumbnail[0],
          'qty'           => $item_data['qty'],
          'total'         => $item_data['total'],
        );
        $product_in_order = true;
      }
    }
    if ( $product_in_order == false ) {
      $response_product[] = array(
        'id'            => $product->get_id(),
        'name'          => $product->get_name(),
        'price'         => $product->get_price(),
        'regular_price' => $product->get_regular_price(),
        'sale_price'    => $product->get_sale_price(),
        'thumbnail_url' => $thumbnail[0],
        'qty'           => 0,
        'total'         => 0,
      );
    }
    
    return $response_product;
    
  }

  /**
   * Remove product from order
   * 
   * @since 2.0.0
   * @param int order_id
   * @param int product_id
   */
  public function remove_product_from_order( $order_id, $product_id ) {

    $order = new WC_Order( $order_id );
    foreach ( $order->get_items() as $item_id => $item ) {
      if ( $item['product_id'] == $product_id ) {
        wc_delete_order_item( $item_id );
        $item->delete_meta_data('_qty');
        $item->delete_meta_data('_line_total');
      }
    }
    $order->calculate_totals();
    $woo = new Woo_Order();
    return $woo->get_order( $order_id );
  }
}