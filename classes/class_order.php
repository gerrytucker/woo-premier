<?php

class Woo_Order {

    /**
     * Constructoer
     */
    function __constructor() {}
    
    /**
     * Create order
     */
    static function create_order( $order_details ) {
        global $woocommerce;

        $customer = $order_details['customer'];

        // Billing address
        $customer_billing_address = $customer['billing'];
        $order_billing_address = array(
            'first_name'    => $customer_billing_address['first_name'],
            'last_name'     => $customer_billing_address['last_name'],
            'company'       => $customer_billing_address['company'],
            'email'         => $customer_billing_address['email'],
            'phone'         => $customer_billing_address['phone'],
            'address_1'     => $customer_billing_address['address_1'],
            'address_2'     => $customer_billing_address['address_2'],
            'city'          => $customer_billing_address['city'],
            'state'         => $customer_billing_address['state'],
            'postcode'      => $customer_billing_address['postcode'],
            'country'       => $customer_billing_address['country'],
        );
        // Shipping address
        $customer_shipping_address = $customer['shipping'];
        $order_shipping_address = array(
            'first_name'    => $customer_shipping_address['first_name'],
            'last_name'     => $customer_shipping_address['last_name'],
            'company'       => $customer_shipping_address['company'],
            'address_1'     => $customer_shipping_address['address_1'],
            'address_2'     => $customer_shipping_address['address_2'],
            'city'          => $customer_shipping_address['city'],
            'state'         => $customer_shipping_address['state'],
            'postcode'      => $customer_shipping_address['postcode'],
            'country'       => $customer_shipping_address['country'],
        );
        
        // Create the order
        $order = wc_create_order();

        // Set customer
        $order->set_customer_id(is_numeric($customer['id']) ? absint($customer['id']) : 0);

        // Set addresses
        $order->set_address( $customer_billing_address, 'billing');
        $order->set_address( $customer_shipping_address, 'shipping');

        // Set payment method
        $order->set_payment_method('cod');

        // Set created by to 'app'
        $order-set_created_via( 'app' );
        
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