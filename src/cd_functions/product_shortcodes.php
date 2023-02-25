
function get_product_description_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'get_product_description' );

    $product_id = absint( $atts['id'] );
    $product = wc_get_product( $product_id );

    if ( $product ) {
        return $product->get_description();
    }
}
add_shortcode( 'get_product_description', 'get_product_description_shortcode' );

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

function get_product_shortdesc_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'get_product_name' );

    $product_id = absint( $atts['id'] );
    $product = wc_get_product( $product_id );

    if ( $product ) {
        return $product->get_short_description();
    }
}
add_shortcode( 'get_product_shortdesc', 'get_product_shortdesc_shortcode' );