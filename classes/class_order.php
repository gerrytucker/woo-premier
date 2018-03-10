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
   * Set address
   * @since 2.0.0
   * @param obj order object
   * @param arr billing address
   * @param str address type (billing|shipping)
   */
  private function set_address( $order, $address=array(), $type='billing' ) {

    return $order->set_address( $address, $type );

  }

}