<?php
/**
 * NPPP2U Product API Client
 */
class Woo_Product {

  /** Version */
  const VERSION = "1.0.0";

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
  public function get_product( $product_id ) {
    $response = array();

    if ( $product = wc_get_product( $product_id ) ) {
      // Get product thumbnail
      $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'thumbnail', false );
      $medium = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'medium', false );
      $large = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large', false );

      $stock_quantity = $product->get_stock_quantity();
      if ($stock_quantity === null)
        $stock_quantity = 0;

      $response[] = array(
        'id'                    => $product->get_id(),
        'name'                  => $product->get_name(),
        'slug'                  => $product->get_slug(),
        'price'                 => number_format((float)$product->get_price(), 2, '.', ''),
        'regular_price'         => number_format((float)$product->get_regular_price(), 2, '.', ''),
        'sale_price'            => number_format((float)$product->get_sale_price(), 2, '.', ''),
        'stock_status'          => $product->get_stock_status(),
        'stock_quantity'        => $stock_quantity,
        'backorders'            => $product->get_backorders(),
        'backorders_allowed'    => $product->get_backorders_allowed(),
        'backordered'           => $product->get_backordered(),
        'thumbnail_url'         => $thumbnail[0],
        'medium_url'            => $medium[0],
        'large_url'             => $large[0],
      );
    }

    return array(
      "product" => $response
    );
  }

  /**
   * Get products
   * 
   * @since 2.0.0
   */
  public function get_products() {

    $products = wc_get_products(array(
      'limit'     => -1,
      'orderby'   => 'rand',
      'order'     => 'ASC',
      'status'    => 'publish'
    ));

    $response = array();

    foreach ( $products as $product ) {
      // Get product thumbnail
      $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'thumbnail', false );
      $medium = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'medium', false );
      $large = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'large', false );

      $stock_quantity = $product->get_stock_quantity();
      if ($stock_quantity === null)
        $stock_quantity = 0;

      $response[] = array(
        'id'                    => $product->get_id(),
        'name'                  => $product->get_name(),
        'slug'                  => $product->get_slug(),
        'price'                 => number_format((float)$product->get_price(), 2, '.', ''),
        'regular_price'         => number_format((float)$product->get_regular_price(), 2, '.', ''),
        'sale_price'            => number_format((float)$product->get_sale_price(), 2, '.', ''),
        'stock_status'          => $product->get_stock_status(),
        'stock_quantity'        => $stock_quantity,
        'backorders'            => $product->get_backorders(),
        'backorders_allowed'    => $product->get_backorders_allowed(),
        'backordered'           => $product->get_backordered(),
        'thumbnail_url'         => $thumbnail[0],
        'medium_url'            => $medium[0],
        'large_url'             => $large[0],
      );
    }

    return array(
      "count"     => count($response),
      "products"  => $response
    );
  }

}
