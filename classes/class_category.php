<?php
/**
 * NPPP2U Category API Client
 */
class Woo_Product_Category {

  /** Version */
  const VERSION = "1.0.0";

  /**
   * Set up the client
   */
  public function __constructor() {}

  /**
   * Get category
   * 
   * @since 2.0.0
   */
  public function get_product_category( $category_id ) {
    $response = array();

    $args = array(
      'taxonomy'      => 'product_cat',
      'object_ids'    => $category_id,
      'orderby'       => 'name',
      'order'         => 'ASC',
      'hide_empty'    => false,
    );

    $categories = get_terms($args);
    foreach ($categories as $category) {
      $image_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
      $response[] = array(
        'ID'          => $category->term_id,
        'name'        => $category->name,
        'slug'        => $category->slug,
        'description' => $category->description,
        'parent'      => $category->parent,
        'taxonomy'    => $category->taxonomy,
        'count'       => $category->count,
        'thumbnail'   => wp_get_attachment_url(  $image_id )
      );
    }
    return $response;
  }

  /**
   * Get categories
   * 
   * @since 1.0.0
   */
  public function get_product_categories() {
    $response = array();
  
    $args = array(
      'taxonomy'      => 'product_cat',
      'orderby'       => 'name',
      'order'         => 'ASC',
      'hide_empty'    => false,
      'count'         => true
    );

    $categories = get_terms($args);
    foreach ($categories as $key => $category) {
      $response[] = get_product_category( $category->term_id);
    }
    return $response;
  }
  
}    
