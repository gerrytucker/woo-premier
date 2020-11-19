<?php
/**
 * Image API Client
 */
class Woo_Image {

  /** Version */
  const VERSION = "1.0.0";

  /**
   * Set up the client
   */
  public function __constructor() {}

  public function get_image( $image_id ) {
    $response = array();

    if (!function_exists('fly_get_attachment_image')) return $response;

    $image = fly_get_attachment_image($image_id, array(400, 400));
    var_dump($image);

    return array(
      'image'     => $image
    );
  }
}