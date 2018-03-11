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

}