<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function sinh_get_order_meta( $oder_id ) {
	return get_post_meta( $oder_id );
}

function sinh_get_shipping_address( $order_meta ) {
	return $order_meta['_shipping_address_1'][0] . ' ' . $order_meta['_shipping_city'][0];
}

function sinh_get_google_map_shipping_address( $order_meta ) {
	$google_link = 'https://www.google.com/maps/place/';
	$address = sinh_get_shipping_address( $order_meta );
	$exploded = explode( " ", $address );
	foreach ( $exploded as $value ) {
		$google_link .= $value . '+';
	}
	$google_link .= 'Việt+Nam';
	return $google_link;
}

function sinh_get_shipping_phone( $order_meta ) {
	return $order_meta['_billing_phone'][0];
}

function sinh_get_shipping_person_name( $order_meta ) {
	return $order_meta['_shipping_first_name'][0] . ' ' . $order_meta['_shipping_last_name'][0];
}

function sinh_get_user_rating_link( $order_meta ) {
	$user_id = $order_meta['_customer_user'][0];
	$user = get_user_by( 'ID', $user_id );
	if ( class_exists('Woo_Sinh_Marketplace') ) {
		return Woo_Sinh_Marketplace::sinh_get_user_rating_guid( $user );
	}
	return null;
} 

?>