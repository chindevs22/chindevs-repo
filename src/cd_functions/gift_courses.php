//cd functions file that defines all our our add actions
// we need to have the code thats in the STM LMS enterprise courses class definition main.php file here insteead
// just returns a success message "hey"
<?php

add_action( 'wp_ajax_stm_lms_add_to_cart_gc', 'add_to_cart_gc' );

function add_to_cart_gc() {

// 	if ( ! is_user_logged_in() || empty( $_GET['course_id'] ) ) {
// 		die;
// 	}
// 	$r = array();

// 	$user     = STM_LMS_User::get_current_user();
// 	$user_id  = $user['id'];
// 	$item_id  = intval( $_GET['course_id'] );
// 	$groups   = array_map( 'intval', wp_unslash( $_GET['groups'] ) );
// 	$quantity = 1;
// 	$price    = apply_filters( 'stm_lms_enterprice_price', self::get_enterprise_price( $item_id ), $item_id, $user_id );

// 	foreach ( $groups as $enterprise ) {
// 		$is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();

// 		$item_added = count( self::check_enterprise_in_cart( $user_id, $item_id, $enterprise, array( 'user_cart_id', 'enterprise' ) ) );

// 		if ( ! $item_added ) {
// 			stm_lms_add_user_cart( compact( 'user_id', 'item_id', 'quantity', 'price', 'enterprise' ) );
// 		}

// 		if ( ! $is_woocommerce ) {
// 			$r['text']     = esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system-pro' );
// 			$r['cart_url'] = esc_url( STM_LMS_Cart::checkout_url() );
// 		} else {
// 			$product_id = self::create_product( $item_id );

// 			// Load cart functions which are loaded only on the front-end.
// 			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
// 			include_once WC_ABSPATH . 'includes/class-wc-cart.php';

// 			if ( is_null( WC()->cart ) ) {
// 				wc_load_cart();
// 			}

// 			WC()->cart->add_to_cart( $product_id, 1, 0, array(), array( 'enterprise_id' => $enterprise ) );

// 			$r['text']     = esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system-pro' );
// 			$r['cart_url'] = esc_url( wc_get_cart_url() );
// 		}
// 	}

// 	$r['redirect'] = STM_LMS_Options::get_option( 'redirect_after_purchase', false );

// 	wp_send_json( $r );

	$r = array();
	$r['text']     = esc_html__( 'HEYY', 'masterstudy-lms-learning-management-system-pro' );
	wp_send_json( $r );
}

?>