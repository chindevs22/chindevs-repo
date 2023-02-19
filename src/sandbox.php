
// add course image
function add_course_image2() {
	$course_post_id = 4995;
	$course_id = 9;
    $wpdata['post_status'] ='inherit';
    $wpdata['post_parent'] = $course_post_id;
    $wpdata['post_title'] = "Course {$course_id} Thumbnail";
    $wpdata['guid'] = "https://dev108.freewaydns.net/wp-content/uploads/course_materials/{$course_id}/thumbnail.jpg";
    $wpdata['post_mime_type'] = 'image/jpeg';
    $wpdata['post_type'] = 'attachment';

    $course_image_id = wp_insert_post( $wpdata );
    update_post_meta($course_post_id, '_thumbnail_id', $course_image_id);
}


function add_course_image_3($course_post_id, $course_id) {
   $upload_dir = wp_upload_dir();
   $upload_path = $uploaddir['path'] . "course_materials/{$course_id}/thumbnail.jpg";
   $filename = "thumbnail.jpg";
   $course_post_id = 4993;
   $course_id = 9;
   $wp_filetype = wp_check_filetype(basename($filename), null );
   echo $wp_filetype;
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attachment_id = wp_insert_attachment( $attachment, $upload_path, $course_post_id );

	print_r($attachment_id);
     if ( ! is_wp_error( $attachment_id ) ) {
		echo ABSPATH;
		// Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');
         $imagenew = get_post( $attachment_id );
         $fullsizepath = get_attached_file( $imagenew->ID );
         echo $fullsizepath;
        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_path );
        wp_update_attachment_metadata( $attachment_id, $attachment_data );
        set_post_thumbnail( $course_post_id, $attachment_id );
    }
}


function add_course_image_3() {
   $upload_dir = wp_upload_dir();
   $upload_path = $upload_dir . "course_materials/{$course_id}/thumbnail.jpg";
   $filename = "thumbnail.jpg";
   $course_post_id = 4993;
   $course_id = 9;
   $wp_filetype = wp_check_filetype(basename($filename), null );
   echo $wp_filetype;
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attachment_id = wp_insert_attachment( $attachment, $upload_path, $course_post_id );

	echo $attachment_id;
     if ( ! is_wp_error( $attachment_id ) ) {
		echo ABSPATH;
		// Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');
         $imagenew = get_post( $attachment_id );
         $fullsizepath = get_attached_file( $imagenew->ID );
         echo $fullsizepath;
        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_path );
        wp_update_attachment_metadata( $attachment_id, $attachment_data );
        set_post_thumbnail( $course_post_id, $attachment_id );
    }
}


add_shortcode( 'test-function', 'add_course_image_3' );



            if ( ! is_wp_error( $attachment_id ) ) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
                wp_update_attachment_metadata( $attachment_id, $attachment_data );
                set_post_thumbnail( $post_id, $attachment_id );
            }

https://dev108.freewaydns.net/wp-content/uploads/course_materials/9/thumbnail.jpg