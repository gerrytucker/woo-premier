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
      $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'thumbnail', false );
      $retina_thumbnail = wr2x_get_retina_from_url($thumbnail[0]);
      $medium = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'medium', false );
      $retina_medium = wr2x_get_retina_from_url($medium[0]);
      $large = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large', false );
  
      $response[] = array(
        'id'              => $product->get_id(),
        'name'            => $product->get_name(),
        'slug'            => $product->get_slug(),
        'price'           => number_format((float)$product->get_price(), 2, '.', ''),
        'regular_price'   => number_format((float)$product->get_regular_price(), 2, '.', ''),
        'sale_price'      => number_format((float)$product->get_sale_price(), 2, '.', ''),
        'thumbnail_url'   => $thumbnail[0],
        'retina_thumbnail_url'
                          => $retina_thumbnail,
        'medium_url'      => $medium[0],
        'retina_medium_url'
                          => $retina_medium,
        'large_url'       => $large[0],
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
      $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'thumbnail', false );
      $retina_thumbnail = wr2x_get_retina_from_url($thumbnail[0]);
      $medium = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'medium', false );
      $retina_medium = wr2x_get_retina_from_url($medium[0]);
      $large = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large', false );
  
      $response[] = array(
        'id'              => $product->get_id(),
        'name'            => $product->get_name(),
        'slug'            => $product->get_slug(),
        'price'           => number_format((float)$product->get_price(), 2, '.', ''),
        'regular_price'   => number_format((float)$product->get_regular_price(), 2, '.', ''),
        'sale_price'      => number_format((float)$product->get_sale_price(), 2, '.', ''),
        'thumbnail_url'   => $thumbnail[0],
        'retina_thumbnail_url'
                          => $retina_thumbnail,
        'medium_url'      => $medium[0],
        'retina_medium_url'
                          => $retina_medium,
        'large_url'       => $large[0],
      );
    }

    return $response;
  }

}