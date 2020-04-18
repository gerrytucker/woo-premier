<?php
/**
 * NPPP2U Category API Client
 */
class Woo_Category {

  /** Version */
  const VERSION = "1.0.0";

  /**
   * Set up the client
   */
  public function __constructor() {}

  /**
   * Get category
   * 
   * @since 1.0.0
   */
  public function get_categories() {
    $response = array();

    $orderby = 'name';
    $order = 'asc';
    $hide_empty = false;
    $args = array(
      'orderby'     => $orderby,
      'order'       => $order,
      'hide_empty'  => $hide_empty
    );
    $categories = get_terms('product_cat', $args);
    foreach ($categories as $key => $category) {
      $response[] = array(
        'ID'    => $category->term_id,
        'name'  => $category->name
      );
    }
    return $response;
  }

}
