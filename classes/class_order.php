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
   * Get customer orders
   * 
   * @since 2.0.0
   * @param int $id customer id
   */
  public function get_customer_orders( $id ) {
    $response = array();

    if ( $orders = wc_get_orders( array( 'customer_id' => $customer_id, 'status' => array('completed'), 'orderby' => 'ID' ) ) ) {
      foreach ( $orders as $order ) {
        $date_created = $order->get_date_created();
        $response_items = array();
        $items = $order->get_items();
        foreach ( $items as $item ) {
          $product = $item->get_product();
          $response_items[] = array(
            'id'      => $item->get_product_id(),
            'name'    => $product->get_name(),
            'qty'     => $item->get_quantity(),
            'total'   => $item->get_total(),
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
          'items'             => $return_items  
        );
      }
    }

    return $response;
  }

}