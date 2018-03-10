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
}