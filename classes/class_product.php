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

      // Barcode meta
      if ( $barcode_meta = get_post_meta($product->get_id(), '_barcode') ) {
        $barcode = $barcode_meta[0];
      } else {
        $barcode = '';
      }

      // SKU
      if ( $sku_meta = get_post_meta($product->get_id(), '_sku') ) {
        $sku = $sku_meta[0];
      } else {
        $sku = '';
      }

      $woo = new Woo_Image();
      $image = $woo->get_image($image_id);

      $response = array(
        'id'                    => $product->get_id(),
        'name'                  => $product->get_name(),
        'slug'                  => $product->get_slug(),
        'categories'            => $categories,
        'sku'                   => $sku,
        'barcode'               => $barcode,
        'price'                 => number_format((float)$product->get_price(), 2, '.', ''),
        'regular_price'         => number_format((float)$product->get_regular_price(), 2, '.', ''),
        'sale_price'            => number_format((float)$product->get_sale_price(), 2, '.', ''),
        'stock_status'          => $product->get_stock_status(),
        'stock_quantity'        => $stock_quantity,
        'image_id'              => $image_id,
        'thumbnail_url'         => $thumbnail,
        'medium_url'            => $medium,
        'large_url'             => $large,
        'image'                 => $image,
      );
    }

    return $response;
  }

  /**
   * Get products
   *
   * @since 2.0.0
   */
  public function get_products($args) {

    $atts = shortcode_atts( array(
      'orderby'   => 'name',
      'order'     => 'ASC',
      'status'    => 'publish',
      'page'      => 1,
      'limit'     => 10
    ), $args);

    $products = wc_get_products($atts);

    $response = array();
    foreach ( $products as $product ) {
      $response[] = $this->get_product($product->get_id());
    }

    return $response;
  }

  /**
   * Get products by barcode
   *
   * @since 2.0.0
   */
  function handle_barcode_query_var( $query, $query_vars) {
    if ( !empty( $query_vars['barcode'] ) ) {
      $query['meta_query'][] = array(
        'key'       => '_barcode',
        'value'     => esc_attr( $query_vars['barcode'] )
      );
    }
    return $query;
  }

  public function get_products_by_barcode($barcode) {

    add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array($this, 'handle_barcode_query_var'), 10, 2 );

    $products = wc_get_products(array(
      'limit'     => -1,
      'orderby'   => 'name',
      'order'     => 'ASC',
      'status'    => 'publish',
      'barcode'   => $barcode,
    ));

    $response = array();
    foreach ( $products as $product ) {
      $response[] = $this->get_product($product->get_id());
    }

    remove_filter( 'woocommerce_product_data_store_cpt_get_products_query', array($this, 'handle_barcode_query_var'), 10 );

    return $response;
  }

  /**
   * Get products by SKU
   *
   * @since 2.3.0
   */
  function handle_sku_query_var( $query, $query_vars) {
    if ( !empty( $query_vars['sku'] ) ) {
      $query['meta_query'][] = array(
        'key'       => '_sku',
        'value'     => esc_attr( $query_vars['sku'] )
      );
    }
    return $query;
  }

  public function get_products_by_sku($sku) {

    add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array($this, 'handle_sku_query_var'), 10, 2 );

    $products = wc_get_products(array(
      'limit'     => -1,
      'orderby'   => 'name',
      'order'     => 'ASC',
      'status'    => 'publish',
      'sku'       => $sku,
    ));

    $response = array();
    foreach ( $products as $product ) {
      $response[] = $this->get_product($product->get_id());
    }

    remove_filter( 'woocommerce_product_data_store_cpt_get_products_query', array($this, 'handle_sku_query_var'), 10 );

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
      $response[] = $this->get_product($product->get_id());
    }

    return $response;
  }

  public function update_product_qty( $product_id, $new_stock_quantity) {

    if ( $product = new WC_Product($product_id) ) {
      $product->set_stock_quantity($new_stock_quantity);
      $product->save();
    }

    return $this->get_product($product_id);
  }
}
