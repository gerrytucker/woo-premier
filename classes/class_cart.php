<?php
/**
 * NPPP2U Cart Functions
 * 
 * @since 2.0.0
 */

class Woo_Cart {

  // Version
  const VERSION = "2.0.0";

  // Set up the client
  public function __constructor() {}

  /**
   * Get customer cart
   * 
   * @since 2.0.0
   * @param int customer_id
   */
  public function get_cart( $customer_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'cart';

    $cart = $wpdb->get_results("
      SELECT product_id, line_qty FROM $table
      WHERE customer_id = $customer_id
      ORDER BY product_id;
    ");
    foreach( $cart as $item ) {
      $product = wc_get_product(absint($item->product_id));
      $items[] = array(
        'product_id'  => $item->product_id,
        'name'        => $product->get_name(),
        'line_qty'    => $item->line_qty
      );
    }
    usort( $items, function( $a, $b) {
      return $a['name'] <=> $b['name'];
    });
    return $items;
  }
  
  /**
   * Get product from customer cart
   * 
   * @since 2.0.0
   * @param int customer_id
   * @param int product_id
   */
  public function get_cart_item( $customer_id, $product_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'cart';

    $cart = $wpdb->get_results("
      SELECT product_id, line_qty FROM $table
      WHERE customer_id = $customer_id
      AND product_id = $product_id
      ORDER BY product_id;
    ");
    foreach( $cart as $item ) {
      $product = wc_get_product(absint($item->product_id));
      $items[] = array(
        'product_id'  => $item->product_id,
        'name'        => $product->get_name(),
        'line_qty'    => $item->line_qty
      );
    }
    return $items;
  }

  /**
   * Delete product from customer cart
   * 
   * @since 2.0.0
   * @param int customer_id
   * @param int product_id
   */
  public function delete_cart_item( $customer_id, $product_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'cart';

    $result = $wpdb->delete(
      $table,
      array(
        'customer_id' => '%d',
        'product_id' => '%d'
      ),
      array($customer_id, $product_id)
    );
    if( $result == false ) {
      return false;
    } else {
      return array(
        'status'  => 'ok',
        'count'   => absint($result)
      );
    }
  }

  /**
   * Update/add to customer cart
   * 
   * @since 2.0.0
   * @param int customer_id
   * @param int product_id
   * @param int line_qty
   */
  public function update_cart_item( $customer_id, $product_id, $line_qty ) {
    global $wpdb;
    $table = $wpdb->prefix . 'cart';

    if($line_qty == 0) {
      return self::delete_cart_item($customer_id, $product_id, $line_qty);
    }

    $result = $wpdb->replace(
      $table,
      array(
        'customer_id' => '%d',
        'product_id' => '%d',
        'line_qty' => '%d'
      ),
      array($customer_id, $product_id, $line_qty)
    );
    if( $result == false ) {
      return false;
    } else {
      return array(
        'status'  => 'ok',
        'count'   => absint($result)
      );
    }
  }

  /**
   * Clear customer cart
   * 
   * @since 2.0.0
   * @param int customer_id
   */
  public function clear_cart( $customer_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'cart';

    $result = $wpdb->delete($table, array('customer_id' => $customer_id), array('%d') );
    if( $result == false ) {
      return false;
    } else {
      return array(
        'status'  => 'ok',
        'count'   => absint($result)
      );
    }
  }
  
}