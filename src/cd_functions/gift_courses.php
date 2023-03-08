<?php

// if ( class_exists( 'STM_LMS_Woocommerce_Courses_Admin' ) && STM_LMS_Cart::woocommerce_checkout_enabled() ) {
// 	new STM_LMS_Woocommerce_Courses_Admin(
// 		'gift_course',
// 		esc_html__( 'Gift Course LMS Products', 'masterstudy-lms-learning-management-system-pro' ),
// 		$gift_meta_key
// 	);
// }


add_action( 'wp_ajax_stm_lms_add_to_cart_gc', 'add_to_cart_gc' );
add_filter( 'stm_lms_before_create_order', 'stm_lms_before_create_order' );
add_action( 'stm_lms_woocommerce_order_approved', 'stm_lms_woocommerce_order_approved' );

function get_price( $course_id ) {
	return get_post_meta( $course_id, 'price', true );
}

//todo add back mail function
// from enterprise main.php
function create_group_users( $emails ) {
	$userList = array();
	foreach ( $emails as $email ) {
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			array_push($userList, $user);
			continue;
		}

		/*Create User*/
		$username = sanitize_title( $email );
		$password = wp_generate_password();

		//Create WP User entry
		$user_id = wp_create_user($username, $password, $email);
		//WP User object
		$wp_user = new WP_User($user_id);
		//Set the role of this user to subscriber.
		$wp_user->set_role('subscriber');
		array_push($userList, $wp_user);
	}
	return $userList;
}

//  on the existing action where woocomerce approves order
//  add this code
//  this is from includes/classes/class-woocommerce.php
//  this action isn't even being called!!
function stm_lms_woocommerce_order_approved( $course_data ) {

	if ( ! empty( $course_data['gift_course_id'] ) ) {
		echo "here inside woocommerce order approved";
		/* Get Group Members */
		$group_id = intval( $course_data['gift_course_id'] );
		$emails = array("usercreatedbygifting@gmail.com");
		$users    = create_group_users( $emails );

		if ( ! empty( $users ) ) {
			foreach ( $users as $id ) {
				STM_LMS_Course::add_user_course( $course_data['item_id'], $id, 0, 0, false, 55555 );
				STM_LMS_Course::add_student( $course_data['item_id'] );
			}
		}
	}
}

// called before it creates the order
// adds the stm_lms_courses with course id to the order metadata
// before that add the enterprise_id equal to the $cart_item enterprise_id
function stm_lms_before_create_order( $order_meta, $cart_item ) {
	if ( ! empty( $cart_item['gift_course_id'] ) ) {
		$order_meta['gift_course_id'] = $cart_item['gift_course_id'];
	}
	return $order_meta;
}


function check_enterprise_in_cart( $user_id, $item_id, $group_id, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_cart_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	return $wpdb->get_results( $wpdb->prepare( 'SELECT %s FROM %s WHERE user_id = %d AND item_id = %d AND enterprise = %d', $fields, $table, $user_id, $item_id, $group_id ), ARRAY_N );
}

/*Product*/
function create_product( $id ) {
	$product_id = has_product( $id );

	/* translators: %s Title */
	$title        = sprintf( esc_html__( 'LATEST GIFT for %s', 'masterstudy-lms-learning-management-system-pro' ), get_the_title( $id ) );
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
	$enterprise = '55555';
	$quantity = 1;
	$price = get_price($item_id);
// 	$price    = apply_filters( 'stm_lms_enterprice_price', self::get_enterprise_price( $item_id ), $item_id, $user_id );

	$is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();

	// check if in cart
	// $item_added = count( self::check_enterprise_in_cart( $user_id, $item_id, $enterprise, array( 'user_cart_id', 'enterprise' ) ) );
	$item_added = false;
	// add to cart
	if ( ! $item_added ) {
		stm_lms_add_user_cart( compact( 'user_id', 'item_id', 'quantity', 'price', 'enterprise' ) );
	}

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
		//ADD THIS ONE BACK IN
      	//WC()->cart->add_to_cart( $product_id, 1, 0, array(), array( 'enterprise_id' => '112233' ) );
		WC()->cart->add_to_cart( $product_id, 1, 0, array(), array( 'gift_course_id' => '55555' ) );

		$r['text']     = esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system-pro' );
		$r['cart_url'] = esc_url( wc_get_cart_url() );
	}

	$r['redirect'] = STM_LMS_Options::get_option( 'redirect_after_purchase', false );
	wp_send_json( $r );
}

// update the name of the item in the cart with a "gift" tag
add_filter( 'woocommerce_cart_item_name', 'woo_cart_group_name', 100, 3 );
function woo_cart_group_name( $title, $cart_item, $cart_item_key ) {
	if ( ! empty( $cart_item['gift_course_id'] ) ) {
		$course_id = $cart_item['gift_course_id'];
		$sub_title = "<span class='product-enterprise-group'>" . sprintf( esc_html__( 'Gift for %s', 'masterstudy-lms-learning-management-system-pro' ), "friends" ) . '</span>';

		$title .= $sub_title;
	}
	return $title;
}

?>