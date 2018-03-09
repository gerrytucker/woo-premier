<?php
/**
 * NPPP2U Product API Client
 */
class Woo_Product {

  /** Version */
  const VERSION = "2.0.0";

  /**
   * Set up the client
   */
  public function __constructor() {}

  /**
   * Get product
   * 
   * @since 2.0.0
   * @param int $id
   */
  public function get_product( $id ) {
    $response = array();

    if ( $product = wc_get_product( $id ) ) {
      // Get product thumbnail
      $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id(), 'thumbnail', false ) );

      $response[] = array(
        'id'              => $product->get_id(),
        'name'            => $product->get_name(),
        'price'           => $product->get_price(),
        'regular_price'   => $product->get_regular_price(),
        'sale_price'      => $product->get_sale_price(),
        'thumbnail_url'   => $thumbnail[0],
        'meta_data'       => $product->get_meta_data()
      );
    }

    return $response;
  }

  /**
   * Get products
   * 
   * @since 2.0.0
   */
  public function get_products() {

    $products = wc_get_products(array(
      'limit'     => -1,
      'orderby'   => 'name',
      'order'     => 'ASC',
      'status'    => 'publish'
    ));

    $response = array();

    foreach ( $products as $product ) {
      // Get product thumbnail
      $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id(), 'thumbnail', false ) );

      $response[] = array(
        'id'              => $product->get_id(),
        'name'            => $product->get_name(),
        'price'           => $product->get_price(),
        'regular_price'   => $product->get_regular_price(),
        'sale_price'      => $product->get_sale_price(),
        'thumbnail_url'   => $thumbnail[0],
        'meta_data'       => $product->get_meta_data()
      );
    }

    return $response;
  }
}