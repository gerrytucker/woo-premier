<?php

class Woo_Order {

    /**
     * Constructoer
     */
    function __constructor() {}

    /**
     * Set address (billing/shipping)
     */
    function set_address( $order, $address, $address_type="billing" ) {

      $order_address = array();

      if ( $address_type == "billing" ) {
        $order_address = array(
          'first_name'    => $address['first_name'],
          'last_name'     => $address['last_name'],
          'company'       => $address['company'],
          'email'         => $address['email'],
          'phone'         => $address['phone'],
          'address_1'     => $address['address_1'],
          'address_2'     => $address['address_2'],
          'city'          => $address['city'],
          'state'         => $address['state'],
          'postcode'      => $address['postcode'],
          'country'       => $address['country'],
        );
      }
      else {
        $order_address = array(
          'first_name'    => $address['first_name'],
          'last_name'     => $address['last_name'],
          'company'       => $address['company'],
          'address_1'     => $address['address_1'],
          'address_2'     => $address['address_2'],
          'city'          => $address['city'],
          'state'         => $address['state'],
          'postcode'      => $address['postcode'],
          'country'       => $address['country'],
        );
      }

      return $order->set_address( $order_address, $address_type );
    }

    /**
     * Set paymemt method
     */
    function set_payment_method( $order, $payment_method="cod" ) {

      return $order->set_payment_method( $payment_method );

    }

    /**
     * Set created via
     */
    function set_created_via( $order, $created_via="app" ) {

      return $order->set_created_via( $app );

    }

    /**
     * Create order
     */
    static function create_order( $order_details ) {
        global $woocommerce;

        $customer = $order_details['customer'];

        // Create the order
        $order = wc_create_order();

        // Set customer
        $order->set_customer_id(is_numeric($customer['id']) ? absint($customer['id']) : 0);

        // Set billing address
        // Billing address
        $order_billing_address = array(
          'first_name'    => $customer['billing']['first_name'],
          'last_name'     => $customer['billing']['last_name'],
          'company'       => $customer['billing']['company'],
          'email'         => $customer['billing']['email'],
          'phone'         => $customer['billing']['phone'],
          'address_1'     => $customer['billing']['address_1'],
          'address_2'     => $customer['billing']['address_2'],
          'city'          => $customer['billing']['city'],
          'state'         => $customer['billing']['state'],
          'postcode'      => $customer['billing']['postcode'],
          'country'       => $customer['billing']['country'],
        );
        set_address( $order, $customer_billing_address, 'billing');

        // Shipping address
        $order_shipping_address = array(
          'first_name'    => $customer['shipping']['first_name'],
          'last_name'     => $customer['shipping']['last_name'],
          'company'       => $customer['shipping']['company'],
          'address_1'     => $customer['shipping']['address_1'],
          'address_2'     => $customer['shipping']['address_2'],
          'city'          => $customer['shipping']['city'],
          'state'         => $customer['shipping']['state'],
          'postcode'      => $customer['shipping']['postcode'],
          'country'       => $customer['shipping']['country'],
        );
        set_address( $order, $customer_shipping_address, 'shipping');

        // Set payment method
        set_payment_method($order, 'cod');

        // Set created by to 'app'
        set_created_via( $order, 'app' );
        
        // Get the order number
        $order_id = $order->get_order_number();

        // Add the products
        $items = $order_details['items'];
        foreach( $items as $item ) {
          $product = wc_get_product( $item['id'] );
          $item_qty = ( is_numeric($item['qty']) ? absint($item['qty']) : 0);
          add_order_line( $order_id, $product->get_id(), $item_qty );
        }

        // Update post meta
        update_post_meta( $order_number, '_customer_user', $customer['id'] );

        // Calculate totals
        $order->calculate_totals();
        $order->update_status('wp-processing', 'Order created by app - ', true);
        
        // Return the order
        return array(
            'order_id'  => wc_get_order( is_numeric($order_number) ? absint($order_number) : 0)
        );

    }

    /**
     * Add Order Line
     */
    static function add_order_line( $order_id, $product_id, $qty ) {

      if ( $product = wc_get_product( $product_id ) ) {
        $order_item_id = wc_add_order_item(
          $order_id,
          array(
            'order_item_name'       => $product->get_name(),
            'order_item_line_type'  => 'line_item'
          )
        );
        wc_add_order_item_meta( $order_item_id, '_product_id', $product_id, true );
        wc_add_order_item_meta( $order_item_id, '_qty', $qty, true );
        return array( 'status' => 'ok' );
      }
      else {
        return array( 'status' => 'error' );
      }
    }

    /**
     * Update Order Line
     */
    static function update_order_line( $order_id, $order_item_id, $qty) {

      if ( wc_update_order_item_meta( $order_item_id, '_qty', $qty ) ) {
        $order = new WC_Order( $order_id );
        $order->calculate_totals();
        return array( 'status' => 'ok' );
      }
      else {
        return array( 'status' => 'error' );
      }

    }

    /**
     * Remove Order Line
     */
    static function remove_order_line( $order_item_id ) {

      if ( wc_delete_order_item( item_id ) ) {
        return array( 'status' => 'ok' );
      } else {
        return array( 'status' => 'error' );
      }

    }
}