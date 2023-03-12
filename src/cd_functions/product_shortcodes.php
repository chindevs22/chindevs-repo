<?php
function get_product_description( $product_id ) {
    $product = wc_get_product( $product_id );

    if ( $product ) {
        return do_shortcode($product->get_description());
    }
}

function get_product_reviews( $product_id ) {
	$args = array ('post_id' => $product_id);
	$comments = get_comments( $args );

    return wp_list_comments( array( 'callback' => 'woocommerce_comments' ), $comments);
}

function get_product_free_video_shortcode( $product_id ) {
    $product = wc_get_product( $product_id );

	$video_link = $product->get_meta('video');

	$video_data = do_shortcode("[embedyt]".$video_link."[/embedyt]");
    if ( $product ) {
        return "<div style='width: 50%;'>" . $video_data . "</div>";
    }
}
add_shortcode( 'get_product_free_video', 'get_product_free_video_shortcode' );

function get_product_name_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'get_product_name' );

    $product_id = absint( $atts['id'] );
    $product = wc_get_product( $product_id );


    if ( $product ) {
			return '<div class="name">' . $product->get_title() . '</div>';
//         return $product->get_title();
    }
}
add_shortcode( 'get_product_name', 'get_product_name_shortcode' );

function get_product_shortdesc_shortcode( $product_id ) {
    $product = wc_get_product( $product_id );

    if ( $product ) {
        return $product->get_short_description();
    }
}
add_shortcode( 'get_product_shortdesc', 'get_product_shortdesc_shortcode' );

function create_elementor_template($atts) {
	$atts = shortcode_atts(array(
		'template_id' => '',
		'product_id' => '',
	), $atts);

	$template_id = $atts['template_id'];
	$product_id = $atts['product_id'];

	if (!$template_id) {
		return '';
	}
	$args = array(
		'post_type' => 'elementor_library',
		'p' => $template_id,
	);
	$query = new WP_Query($args);
	if (!$query->have_posts()) {
		return '';
	}
	$query->the_post();
	$content = get_the_content();
	wp_reset_postdata();
	$content = str_replace('[get_product_shortdesc]', get_product_shortdesc_shortcode($product_id), $content);
	$content = str_replace('[get_product_free_video]', get_product_free_video_shortcode($product_id), $content);
// 	$content = str_replace('[get_product_name]', get_product_name_shortcode($product_id), $content);
	$content = str_replace('[get_product_description]', get_product_description($product_id), $content);
	$content = str_replace('[reviews]', get_product_reviews($product_id), $content);
	$content = str_replace('[product]', do_shortcode('[products ids="' . $product_id . '"]'), $content);

	return do_shortcode($content);
}

add_shortcode('elementor_template', 'create_elementor_template');
?>