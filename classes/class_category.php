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
      'count'         => true,
      'name__like'    => 'NP'
    );

    $categories = get_terms($args);
    foreach ($categories as $key => $category) {
      $response[] = array(
        'ID'          => $category->term_id,
        'name'        => $category->name,
        'description' => $category->description,
        'parent'      => $category->parent,
        'count'       => $category->count
      );
    }
    return $response;
  }
  
}    
