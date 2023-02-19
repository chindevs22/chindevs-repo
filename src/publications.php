
$productCategoryMap = array(
    "Publications" => 95,
    "Pendrives" => 96,
    "Combo Offers" => 303
)


// function create_product_tester() {
//     $wpdata['post_title'] = 'Duplicate Nyayas';
//     $wpdata['post_status'] ='publish';
//     $wpdata['post_type'] = 'product';
//     $product_post_id = wp_insert_post( $wpdata );
//
//     global $productCategoryMap;
//     $taxonomy = 'product_cat';
//     $parent_cat = "Pendrives";
//     $sub_cat = "Yoga";
//     $wp_category_int = 0;
//     $parent_cat_id = $productCategoryMap[$parent_cat];
//     echo $parent_cat_id . " <br>";
//     $defaults = array('parent'=> $parent_cat_id);
//
//     $term_response = term_exists($sub_cat, $taxonomy); // a record
//
//     if ($term_response == null) {
//         echo "Inserting new Term <br>";
//         $new_term = wp_insert_term($sub_cat, $taxonomy , $defaults);
// 		$wp_category_int = intval($new_term['term_id']);
//     } else {
//         $current_term = term_exists($sub_cat, $taxonomy, $parent_cat_id);
//         echo "Inside else <br>";
//         print_r($current_term);
//         if ($current_term == null) {
//             echo "Curr turm null <br>";
//             $new_term = wp_insert_term($sub_cat, $taxonomy, $defaults);
// 			print_r($new_term);
// 			$wp_category_int = intval($new_term['term_id']);
//         } else {
//             echo "Curr term already exists <br>";
// 			$wp_category_int = intval($current_term['term_id']);
//         }
//     }
//     wp_set_object_terms($product_post_id, $wp_category_int, $taxonomy, $append = true );
// }
// Helper Function for Building Attribute Array for Publications
function build_attr_array($attr, $count) {
	$key = trim($attr['key'], " \x3A");
    $data_arr = array();
    $data_arr['name'] = $key;
    $data_arr['value'] = $attr['value'];
    $data_arr['position'] = $count;
    $data_arr['is_visible'] = 1;
    $data_arr['is_variation'] = 0;
    $data_arr['is_taxonomy'] = 0;
    return $data_arr;
}

// product is a post
function create_publications_from_csv($productData) {
    $wpdata['post_title'] = $productData['title'];
    $wpdata['post_excerpt'] = html_entity_decode($productData['description']);
    $wpdata['post_content']  = html_entity_decode($productData['description']);
    $wpdata['post_status'] ='publish';
    $wpdata['post_type'] = 'product';
    $product_post_id = wp_insert_post( $wpdata );

    // add product metadata
    $prod_price =  $productData['usd_price']; //need to handle rupees

    update_post_meta($product_post_id, '_visibility', 'visible');
    update_post_meta($product_post_id, '_stock_status', 'instock');
    update_post_meta($product_post_id, '_stock',  $productData['quantity']);
    update_post_meta($product_post_id, '_regular_price', $prod_price);
    update_post_meta($product_post_id, '_featured', $productData['featured_product'] == 1 ? 'yes' : 'no');
    if ($productData['discount_flag'] == 1) {
        $percent_off = $prod_price * $productData['discounted_price']/100;
        $sale_price = round($prod_price - $percent_off);
        update_post_meta($product_post_id, '_sale_price', $sale_price);
    }
    update_post_meta($product_post_id, '_weight', $productData['weight']/1000);

    // set the product image - TBD once we have an image

    // set the product category -----------------------------------------------------------------

    global $productCategoryMap; // make sure global var exists
        $taxonomy = 'product_cat';
    $parent_cat = $productData['parent_category_name']; // ensure in CSV
    $sub_cat = $productData['sub_category_name']; // ensure in CSV
    $wp_category_int = 0;
    $parent_cat_id = $productCategoryMap[$parent_cat];
    $defaults = array('parent'=> $parent_cat_id);

    $term_response = term_exists($sub_cat, $taxonomy); // a record
    if ($term_response == null) {
//         echo "Inserting new Term <br>";
        $new_term = wp_insert_term($sub_cat, $taxonomy , $defaults);
        $wp_category_int = intval($new_term['term_id']);
    } else {
        $current_term = term_exists($sub_cat, $taxonomy, $parent_cat_id);
//         echo "Inside else <br>";
        print_r($current_term);
        if ($current_term == null) {
//             echo "Curr turm null <br>";
            $new_term = wp_insert_term($sub_cat, $taxonomy, $defaults);
            print_r($new_term);
            $wp_category_int = intval($new_term['term_id']);
        } else {
//             echo "Curr term already exists <br>";
            $wp_category_int = intval($current_term['term_id']);
        }
    }
//     echo "setting term for " . $sub_cat  . " and " . $parent_cat . " and " . $productData['title'] . "<br>";
    wp_set_object_terms($product_post_id, $wp_category_int, $taxonomy, $append = true );

    // Set the the product attributes for language and author -----------------------------------------

    $extra_attributes = json_decode($productData['variable_filed'], true);
    $count = 0;
    $outer_arr = array();
    $terms_array = array(); // add each term (Author: Swami Ji) to the array

	foreach($extra_attributes as $attr) {
		$key = trim($attr['key'], " \x3A"); // strip whitespace and colon from key
        array_push($terms_array, "" . $key . ": " . $attr['value']); // build tags
        print_r($attr);
        $data_arr = build_attr_array($attr, $count);
        $count += 1;
        $outer_arr[strtolower($key)] = $data_arr;
    }
    // add language attribute from language col
    // ensure new_language exists in CSV
    if(!array_key_exists('language', $outer_arr)) {
        $attr = array("key" => "Language", "value" => $productData['new_language']);
        array_push($terms_array, "" . $attr['key'] . ": " . $attr['value']);
        $data_arr = build_attr_array($attr, $count);
        $outer_arr[strtolower($attr['key'])] = $data_arr;
    }
    update_post_meta($product_post_id, '_product_attributes', $outer_arr);

    // Set the Tags for the Product
    echo "setting tag for " . $productData['title'] . " and ID: " . $product_post_id . "<br>";
	print_r("THE ARRAY OF TERMS");
	echo "<br>";
	print_r($terms_array);
	echo "<br>";
    wp_set_object_terms($product_post_id, $terms_array, 'product_tag', $append = true );
}

