<?php
/**
 * This file is used for writing all the re-usable custom functions.
 *
 * @since   1.0.0
 * @package Blog_List
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if the function exists.
 */
if ( ! function_exists( 'wp_get_posts' ) ) {
	/**
	 * Get the posts.
	 *
	 * @param string $post_type Post type.
	 * @param int    $paged Paged value.
	 * @param int    $posts_per_page Posts per page.
	 * @return object
	 * @since 1.0.0
	 */
	function wp_get_posts( $post_type = 'post', $paged = 1, $posts_per_page = -1 ) {
		// Prepare the arguments array.
		$args = array(
			'post_type'      => $post_type,
			'paged'          => $paged,
			'posts_per_page' => $posts_per_page,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		/**
		 * Posts/custom posts listing arguments filter.
		 *
		 * This filter helps to modify the arguments for retreiving posts of default/custom post types.
		 *
		 * @param array $args Holds the post arguments.
		 * @return array
		 */
		$args = apply_filters( 'cf_posts_args', $args );

		return new WP_Query( $args );
	}
}

/**
 * Check if function exists.
 */
if ( ! function_exists( 'wp_validate_card_number' ) ) {
	/**
	 * Function for validate credit card number.
	 *
	 * @param int $number Holds card number.
	 */
	function wp_validate_card_number( $number ) {

		$cardtype = array(
			'visa'       => '/^4[0-9]{12}(?:[0-9]{3})?$/',
			'mastercard' => '/^5[1-5][0-9]{14}$/',
			'amex'       => ' /^3[47][0-9]{13}$/',
			'discover'   => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
		);

		// Return card type if pattern is match with card number.
		if ( preg_match( $cardtype['visa'], $number ) ) {
			$type = 'visa';
		} elseif ( preg_match( $cardtype['mastercard'], $number ) ) {
			$type = 'mastercard';
		} elseif ( preg_match( $cardtype['amex'], $number ) ) {
			$type = 'amex';
		} elseif ( preg_match( $cardtype['discover'], $number ) ) {
			$type = 'discover';
		} else {
			$type = false;
		}

		return $type;
	}
}
