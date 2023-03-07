//cd functions file that defines all our our add actions
// we need to have the code thats in the STM LMS enterprise courses class definition main.php file here insteead
// just returns a success message "hey"
<?php

// if ( class_exists( 'STM_LMS_Woocommerce_Courses_Admin' ) && STM_LMS_Cart::woocommerce_checkout_enabled() ) {
// 	new STM_LMS_Woocommerce_Courses_Admin(
// 		'gift_course',
// 		esc_html__( 'Gift Course LMS Products', 'masterstudy-lms-learning-management-system-pro' ),
// 		$gift_meta_key
// 	);
// }


add_action( 'wp_ajax_stm_lms_add_to_cart_gc', 'add_to_cart_gc' );

function get_price( $course_id ) {
	return get_post_meta( $course_id, 'price', true );
}


// function check_enterprise_in_cart( $user_id, $item_id, $group_id, $fields = array() ) {
// 	global $wpdb;
// 	$table = stm_lms_user_cart_name( $wpdb );

// 	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

// 	return $wpdb->get_results( $wpdb->prepare( 'SELECT %s FROM %s WHERE user_id = %d AND item_id = %d AND enterprise = %d', $fields, $table, $user_id, $item_id, $group_id ), ARRAY_N );
// }

/*Product*/
function create_product( $id ) {
	$product_id = has_product( $id );

	/* translators: %s Title */
	$title        = sprintf( esc_html__( 'OUR GIFT Course for %s', 'masterstudy-lms-learning-management-system-pro' ), get_the_title( $id ) );
	$price        = get_price( $id );
	$thumbnail_id = get_post_thumbnail_id( $id );

	if ( isset( $price ) && '' === $price ) {
		return false;
	}

	$product = array(
		'post_title'  => $title,
		'post_type'   => 'product',
		'post_status' => 'publish',
	);

	if ( $product_id ) {
		$product['ID'] = $product_id;
	}

	$product_id = wp_insert_post( $product );

	wp_set_object_terms(
		$product_id,
		array( 'exclude-from-catalog', 'exclude-from-search' ),
		'product_visibility'
	);

	if ( ! empty( $price ) ) {
		update_post_meta( $product_id, '_price', $price );
		update_post_meta( $product_id, '_regular_price', $price );
	}

	if ( ! empty( $thumbnail_id ) ) {
		set_post_thumbnail( $product_id, $thumbnail_id );
	}

	wp_set_object_terms( $product_id, 'stm_lms_product', 'product_type' );

	add_post_meta( $id, 'stm_lms_gift_course_id', $product_id ); //unique true?
	add_post_meta( $product_id, 'stm_lms_gift_course_id', $id );

	update_post_meta( $product_id, '_virtual', 1 );
	update_post_meta( $product_id, '_downloadable', 1 );

	return $product_id;
}

function has_product( $id ) {
	 $product_id = get_post_meta( $id , 'stm_lms_gift_course_id', false );
	 if ( !$product_id || empty( $product_id ) ) {
		 return false;
	 }
	 return $product_id;
 }


function add_to_cart_gc() {

	if ( ! is_user_logged_in() || empty( $_GET['course_id'] ) ) {
		die;
	}
	$r = array();

	$user     = STM_LMS_User::get_current_user();
	$user_id  = $user['id'];
	$item_id  = intval( $_GET['course_id'] );
	$emails   = array_map( 'intval', wp_unslash( $_GET['emails'] ) );
	$quantity = 1;
	$price = get_price($item_id);

// 	$price    = apply_filters( 'stm_lms_enterprice_price', self::get_enterprise_price( $item_id ), $item_id, $user_id );
	$is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();

	// 		$item_added = count( self::check_enterprise_in_cart( $user_id, $item_id, $enterprise, array( 'user_cart_id', 'enterprise' ) ) );
	if ( ! $is_woocommerce ) {
		$r['text']     = esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system-pro' );
		$r['cart_url'] = esc_url( STM_LMS_Cart::checkout_url() );
	} else {
		$product_id = create_product( $item_id );

		// Load cart functions which are loaded only on the front-end.
		include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
		include_once WC_ABSPATH . 'includes/class-wc-cart.php';

		if ( is_null( WC()->cart ) ) {
			wc_load_cart();
		}

		WC()->cart->add_to_cart( $product_id, 1, 0, array(), array( 'enterprise_id' => '112233' ) );

		$r['text']     = esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system-pro' );
		$r['cart_url'] = esc_url( wc_get_cart_url() );
	}

	$r['redirect'] = STM_LMS_Options::get_option( 'redirect_after_purchase', false );

	wp_send_json( $r );

// 	$r['text']     = esc_html__( 'HEYY PD:' . $product_id  , 'masterstudy-lms-learning-management-system-pro' );
// 	wp_send_json( $r );
}

?>