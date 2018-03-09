<?php

class Woo_Product {

    function __constructor() {}

    /**
     * GET ALL PRODUCTS
     */
    static function get_products() {

		$products = wc_get_products(array(
			'limit' 		=> -1,
			'orderby' 		=> 'name',
			'order' 		=> 'ASC',
			'status' 		=> 'publish',
		));

        $product_array= array();

		foreach ( $products as $product ) {
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id(), 'post-thumbnail', false) );
			$product_array[] = array(
				'id' 			=> $product->get_id(),
				'name' 			=> $product->get_name(),
				'slug' 			=> $product->get_slug(),
				'featured' 		=> $product->get_featured(),
				'price' 		=> $product->get_price(),
				'regular_price' => $product->get_regular_price(),
				'sale_price' 	=> $product->get_sale_price(),
				'images'		=> self::get_product_images( $product->get_id() ),
				'thumbnail_url' => $thumbnail[0]
			);
		}

        return $product_array;
    }

    /**
     * GET SINGLE PRODUCT
     */
    static function get_product( $product_id ) {
        $product_array = array();

		if ( $product = wc_get_product( $product_id ) ) {
            
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id(), 'large', false) );
			$product_array[] = array(
				'id' 			=> $product->get_id(),
				'name' 			=> $product->get_name(),
				'slug' 			=> $product->get_slug(),
				'featured' 		=> $product->get_featured(),
				'price' 		=> $product->get_price(),
				'regular_price' => $product->get_regular_price(),
				'sale_price' 	=> $product->get_sale_price(),
				'images'		=> self::get_product_images( $product->get_id() ),
				'thumbnail_url' => $thumbnail[0],
				'meta_data' 	=> $product->get_meta_data()
			);

		}
        return $product_array;

    }

    /**
     * GET PRODUCT THUMBNAIL
     */
    static function get_product_thumbnail_src( $product_id ) {
		$product_thumbnail = null;

		if ( $product = wc_get_product( $product_id ) ) {
            
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id(), 'post-thumbnail', false) );
			$product_thumbnail = $thumbnail[0];

		}
        return $product_thumbnail;

    }

	static function get_product_images( $product_id ) {
		$product_images = array();

		if ( $product = wc_get_product( $product_id ) ) {
			$args = array(
				'post_parent' 		=> $product->get_id(),
				'post_type' 		=> 'attachment',
				'numberposts' 		=> -1,
				'post_status' 		=> 'any',
				'post_mime_type' 	=> 'image',
				'order_by' 			=> 'menu_order',
				'order' 			=> 'ASC'
			);
			$images = get_posts( $args );
			if ( $images ) {
				foreach ( $images as $image ) {
					$product_images[] = array(
						'url'		=> wp_get_attachment_url( $image->ID )
					);
				}
			}
		}

		return $product_images;
	}
}