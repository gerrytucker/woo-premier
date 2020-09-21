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
      $image_id = $product->get_image_id();
      $thumbnail = wp_get_attachment_image_url( $image_id, 'thumbnail' );
      if( $thumbnail == false) $thumbnail = wc_placeholder_img_src('thumbnail');
      $medium = wp_get_attachment_image_url( $image_id, 'medium' );
      if( $medium == false) $medium = wc_placeholder_img_src('medium');
      $large = wp_get_attachment_image_url( $image_id, 'large' );
      if( $large == false) $large = wc_placeholder_img_src('large');

      $stock_quantity = $product->get_stock_quantity();
      if ($stock_quantity === null)
        $stock_quantity = 0;

      $categories = array();
      $terms = get_the_terms($product->get_id(), 'product_cat');
      foreach ($terms as $term) {
        $categories[] = array(
          'category_id'         => $term->term_id,
          'name'                => $term->name
        );
      }

      $response[] = array(
        'id'                    => $product->get_id(),
        'name'                  => $product->get_name(),
        'slug'                  => $product->get_slug(),
        'categories'            => $categories,
        'price'                 => number_format((float)$product->get_price(), 2, '.', ''),
        'regular_price'         => number_format((float)$product->get_regular_price(), 2, '.', ''),
        'sale_price'            => number_format((float)$product->get_sale_price(), 2, '.', ''),
        'stock_status'          => $product->get_stock_status(),
        'stock_quantity'        => $stock_quantity,
        'thumbnail_url'         => $thumbnail,
        'medium_url'            => $medium,
        'large_url'             => $large,
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
      $response[] = get_product($product->id);
    }

    return $response;
  }

  /**
   * Get products by category slug
   * 
   * @since 2.0.0
   * @param int $id
   */
  public function get_products_by_cat_slug( $cat_slug ) {

    $products = wc_get_products(array(
      'category'  => array($cat_slug),
      'limit'     => -1,
      'orderby'   => 'name',
      'order'     => 'ASC',
      'status'    => 'publish'
    ));

    $response = array();

    foreach ( $products as $product ) {
      $response[] = get_product($product->id);
    }

    return $response;
  }

}
