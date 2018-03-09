<?php
/**
 * NPPP2U Customer API Client
 */
class WOO_Customer {

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
          'orders'        => get_customer_orders( $id ),
          'total_spent'   => $customer->get_total_spent(),
        )
      );

      return $response;
      
    }
  }
}