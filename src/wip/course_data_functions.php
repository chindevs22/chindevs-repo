<?php
$sectionToLessonMap = array();
$lessonToQuestionsMap = array();

$theme_info = wp_get_theme();
define( 'STM_THEME_VERSION', ( WP_DEBUG ) ? time() : $theme_info->get( 'Version' ) );
define( 'STM_MS_SHORTCODES', '1' );

define( 'STM_THEME_NAME', 'Masterstudy' );
define( 'STM_THEME_CATEGORY', 'Education WordPress Theme' );
define( 'STM_ENVATO_ID', '12170274' );
define( 'STM_TOKEN_OPTION', 'stm_masterstudy_token' );
define( 'STM_TOKEN_CHECKED_OPTION', 'stm_masterstudy_token_checked' );
define( 'STM_THEME_SETTINGS_URL', 'stm_option_options' );
define( 'STM_GENERATE_TOKEN', 'https://docs.stylemixthemes.com/masterstudy-theme-documentation/installation-and-activation/theme-activation' );
define( 'STM_SUBMIT_A_TICKET', 'https://support.stylemixthemes.com/tickets/new/support?item_id=12' );
define( 'STM_DEMO_SITE_URL', 'https://stylemixthemes.com/masterstudy/' );
define( 'STM_DOCUMENTATION_URL', 'https://docs.stylemixthemes.com/masterstudy-theme-documentation/' );
define( 'STM_CHANGELOG_URL', 'https://docs.stylemixthemes.com/masterstudy-theme-documentation/extra-materials/changelog' );
define( 'STM_INSTRUCTIONS_URL', 'https://docs.stylemixthemes.com/masterstudy-theme-documentation/installation-and-activation/theme-activation' );
define( 'STM_INSTALL_VIDEO_URL', 'https://www.youtube.com/watch?v=a8zb5KTAw48&list=PL3Pyh_1kFGGDikfKuVbGb_dqKmXZY86Ve&index=6&ab_channel=StylemixThemes' );
define( 'STM_VOTE_URL', 'https://stylemixthemes.cnflx.io/boards/masterstudy-lms' );
define( 'STM_BUY_ANOTHER_LICENSE', 'https://themeforest.net/item/masterstudy-education-center-wordpress-theme/12170274' );
define( 'STM_VIDEO_TUTORIALS', 'https://www.youtube.com/playlist?list=PL3Pyh_1kFGGDikfKuVbGb_dqKmXZY86Ve' );
define( 'STM_FACEBOOK_COMMUNITY', 'https://www.facebook.com/groups/masterstudylms' );
define( 'STM_TEMPLATE_URI', get_template_directory_uri() );
define( 'STM_TEMPLATE_DIR', get_template_directory() );
define( 'STM_THEME_SLUG', 'stm' );
define( 'STM_INC_PATH', get_template_directory() . '/inc' );

$inc_path     = get_template_directory() . '/inc';
$widgets_path = get_template_directory() . '/inc/widgets';
// Theme setups


add_filter( 'stm_theme_default_layout', 'get_stm_theme_default_layout' );
function get_stm_theme_default_layout() {
	return 'default';
}

add_filter( 'stm_theme_default_layout_name', 'get_stm_theme_default_layout_name' );
function get_stm_theme_default_layout_name() {
	return 'classic_lms';
}

add_filter( 'stm_theme_demos', 'masterstudy_get_demos' );
add_filter( 'stm_theme_demo_layout', 'stm_get_layout' );
add_filter( 'stm_theme_plugins', 'get_stm_theme_plugins' );
add_filter( 'stm_theme_layout_plugins', 'stm_layout_plugins', 10, 1 );

function get_stm_theme_plugins() {
	return stm_require_plugins( true );
}

add_filter( 'stm_theme_enable_elementor', 'get_stm_theme_enable_elementor' );

function get_stm_theme_enable_elementor() {
	return true;
}

add_filter( 'stm_theme_secondary_required_plugins', 'get_stm_theme_secondary_required_plugins' );
add_filter( 'stm_theme_elementor_addon', 'get_stm_theme_elementor_addon' );
add_action( 'stm_reset_theme_options', 'do_stm_reset_theme_options' );


if ( is_admin() && file_exists( get_template_directory() . '/admin/admin.php' ) ) {
	require_once get_template_directory() . '/admin/admin.php';
}

// Custom code and theme main setups
require_once $inc_path . '/setup.php';

// Header an Footer actions
require_once $inc_path . '/header.php';
require_once $inc_path . '/footer.php';

// Enqueue scripts and styles for theme
require_once $inc_path . '/scripts_styles.php';

/*Theme configs*/
require_once $inc_path . '/theme-config.php';

// Visual composer custom modules
if ( defined( 'WPB_VC_VERSION' ) ) {
	require_once $inc_path . '/visual_composer.php';
}

require_once $inc_path . '/elementor.php';

// Custom code for any outputs modifying
//require_once($inc_path . '/payment.php');
require_once $inc_path . '/custom.php';

// Custom code for woocommerce modifying
if ( class_exists( 'WooCommerce' ) ) {
	require_once $inc_path . '/woocommerce_setups.php';
}

if ( defined( 'STM_LMS_URL' ) ) {
	require_once $inc_path . '/lms/main.php';
}
function stm_glob_pagenow() {
	global $pagenow;

	return $pagenow;
}

function stm_glob_wpdb() {
	global $wpdb;

	return $wpdb;
}

if ( class_exists( 'BuddyPress' ) ) {
	require_once $inc_path . '/buddypress.php';
}

//Announcement banner
if ( is_admin() ) {
	require_once $inc_path . '/admin/generate_styles.php';
	require_once $inc_path . '/admin/admin_helpers.php';
	require_once $inc_path . '/tgm/tgm-plugin-registration.php';
}

// --------------------------------------------------------------------------------------------
// CHIN DEV CODE ADDED BELOW
// --------------------------------------------------------------------------------------------
//
//
//
//

function create_course_data() {
	// ensure no duplicate headers in CSV files
	read_csv("sample_questions.csv", "question");
 	read_csv("lesson_materials_section.csv", "lesson");
	read_csv("same_course_material.csv", "course");
}

function read_csv($file_name, $type) {
    //file mapping from our File Manager
	$fileName = "/home/freewaydns-dev108/cd-test-docs/{$file_name}";
	$file = fopen($fileName, 'r');
	$dataArray = array();
 	$headerLine = true;
	while (($line = fgetcsv($file)) !== FALSE) {
        // check header line and if so store for the column names
	    if($headerLine) {
	        $headerLine = false;
	        $mappingLine = $line;
	        continue;
	    }
        // loop through the column values in one row
        $count = 0;
        $tempArray = array();
        // create mapping based on header
        foreach($line as $value) {
            $sanitized_value = preg_replace("/\\\\u([0-9abcdef]{4})/", "&#x$1;", $value);
			$tempArray[$mappingLine[$count++]] = $sanitized_value;
        }
		if ($type == "lesson") {
			create_lesson_from_csv($tempArray);
		}
		else if ($type == "course") {
			create_course_from_csv($tempArray);
		} else if ($type == "question") {
			create_question_from_csv($tempArray);
		}
	}
	fclose($file);
}

function generate_lesson_file_string($attachment, $course_id) {

    $path2 = "{\"error\":\"\",\"path\":\"/home/freewaydns-dev108/htdocs/dev108.freewaydns.net/wp-content/uploads/course_materials/{$course_id}/{$attachment}\",\"url\":\"https://dev108.freewaydns.net/wp-content/uploads/course_materials/{$course_id}/{$attachment}\"}";

    $file_data2 = json_encode(array(
            "lesson_files" => $path2,
            "lesson_files_label" => $attachment
        ));
   return "[{$file_data2}]";
}

function create_lesson_from_csv($lessonData) {

	 global $lessonToQuestionsMap, $sectionToLessonMap;

    $wpdata['post_title'] = $lessonData['lesson_name'];
    $wpdata['post_status'] ='publish';
    if ($lessonData['lesson_type'] == 'quiz') {
        $wpdata['post_type'] = 'stm-quizzes';
        $wpdata['post_content'] = $lessonData['summary'];
    } else {
        $wpdata['post_type'] = 'stm-lessons';
    }
    $lesson_post_id = wp_insert_post( $wpdata );

    // add section ID to the section map
    $sectionID = $lessonData['section_id'];

    if (!array_key_exists($sectionID, $sectionToLessonMap)) {
        $sectionToLessonMap[$sectionID] = array("{$lessonData['section_name']}", "{$lesson_post_id}");
    } else {
        array_push($sectionToLessonMap[$sectionID], "{$lesson_post_id}");
    }

    if ($lessonData['lesson_type'] == 'video' ) {
        update_post_meta($lesson_post_id, 'duration', $lessonData['duration']);
        update_post_meta($lesson_post_id, 'type', $lessonData['lesson_type']);
        $video_type = strtolower($lessonData['video_type']);
        update_post_meta($lesson_post_id, 'video_type', $video_type);
        update_post_meta($lesson_post_id, "lesson_{$video_type}_url", $lessonData['video_url']);
    }
    if ($lessonData['lesson_type'] == 'other') {
         update_post_meta($lesson_post_id, 'type', 'text');
         $file_path =  generate_lesson_file_string($lessonData['attachment'], $lessonData['course_id']);
         update_post_meta($lesson_post_id, 'lesson_files_pack', wp_slash($file_path));
    }
    if ($lessonData['lesson_type'] == 'audio') {
          update_post_meta($lesson_post_id, 'duration', $lessonData['duration']);
          update_post_meta($lesson_post_id, 'type', 'video');
          update_post_meta($lesson_post_id, 'video_type', 'ext_link');
          update_post_meta($lesson_post_id, 'lesson_ext_link_url', $lessonData['audio_url']);
    }
    if ($lessonData['lesson_type'] == 'quiz') {
          update_post_meta($lesson_post_id, 'correct_answer', 'on');
          update_post_meta($lesson_post_id, 'passing_grade', '100');
          update_post_meta($lesson_post_id, 're_take_cut', '0');
          update_post_meta($lesson_post_id, 'quiz_style', 'global');
          $questionArray = $lessonToQuestionsMap[$lessonData['id']];
		  if (!empty($questionArray)) {
			  $questionString = implode(",", $questionArray);
			  update_post_meta($lesson_post_id, 'questions', $questionString);
		  } else {
			  echo "No questions available for Quiz";
		  }
    }
}

function create_course_from_csv($courseData) {
    // Create array of Course info from CSV data
	$wpdata['post_title'] = $courseData['title'];
// 	$wpdata['post_content'] = $courseData['description'];
	$wpdata['post_excerpt'] = $courseData['short_description'];
	$wpdata['post_status'] ='publish';
	$wpdata['post_type'] = 'stm-courses';

    $curriculum_string = "";
	$combinedArray = array();
	$sectionString = $courseData['section'];
	$sectionArray = create_array_from_string($sectionString, ",");

    global $sectionToLessonMap;

	foreach ($sectionArray as $sectionID) {
		if ($sectionToLessonMap[$sectionID]) {
			$combinedArray = array_merge($combinedArray, $sectionToLessonMap[$sectionID]);
		}
    }

// 	print_r($combinedArray);
	$curriculum_string = implode(",", $combinedArray);
//     echo $curriculum_string;
	$course_post_id = wp_insert_post( $wpdata );

	update_post_meta($course_post_id, 'curriculum', $curriculum_string);
}

update_post_meta($course_post_id, 'price', $courseData['price_usd']);
function create_array_from_string($sectionString, $delimiter) {
	$sectionString = trim($sectionString, '[');
    $sectionString = trim($sectionString, ']');
    $sectionString = trim($sectionString, ' ');
	$sectionString = trim($sectionString, '"');
    $sectionArray = explode($delimiter, $sectionString);
    return $sectionArray;
}

function create_question_from_csv($questionData) {
    global $lessonToQuestionsMap;

    $wpdata['post_title'] = $questionData['title'];
    $wpdata['post_status'] ='publish';
    $wpdata['post_type'] = 'stm-questions';
    $question_post_id = wp_insert_post( $wpdata );
    $quiz_id = $questionData['quiz_id'];

    // save question ID to quiz
    if (!array_key_exists($quiz_id, $lessonToQuestionsMap)) {
 		$lessonToQuestionsMap[$quiz_id] = array($question_post_id);
    } else {
        array_push($lessonToQuestionsMap[$quiz_id], $question_post_id);
    }

// 	echo "the lesson to question map from inside the QUESTION <br> <br>";
// 	print_r($lessonToQuestionsMap);

    // add metadata for question
    if ($questionData['type'] == 'multiple_choice') {
        update_post_meta($question_post_id, 'type', 'multi_choice');
    } elseif ($questionData['type'] == 'matching')  {
        update_post_meta($question_post_id, 'type', 'item_match');
    } else {
       update_post_meta($question_post_id, 'type', $questionData['type']);
    }

    $answers = array();
    if ($questionData['type'] != 'matching')  {
        // if not matching
        $count = 1;
        $options = create_array_from_string($questionData['options'], '","');
        //print_r($options);
        $isCorrect = $questionData['correct_answers'];
        foreach ($options as $option) {
            $option = trim($option, "\"");
            $optionArray["text"] = $option;
            $optionArray["isTrue"] = str_contains($isCorrect, $count++) ? "1" : "0";
            array_push($answers, $optionArray);
        }
    }
	else {
        $matching_data = $questionData['options'];
        $questionKey  = '"questions":';
        $optionKey = '"options":';
        $qPos = stripos($matching_data, $questionKey);
        $oPos = stripos($matching_data, $optionKey);
        $questionString = substr($matching_data, $qPos+12, $oPos-14);
        $optionString = substr($matching_data, $oPos+10, -1);

        $questions = create_array_from_string($questionString, '","');
        $options = create_array_from_string($optionString, '","');

		$arrLength = count($questions);
        $correctAnswers = create_array_from_string($questionData['correct_answers'], '","');

        for($x = 0; $x < $arrLength; $x++) {
 			$correctAnswer = $correctAnswers[$x];
            $optionArray["question"] = $questions[$x];
            $optionArray["text"] = $options[$correctAnswer - 1];
            $optionArray["isTrue"] = 0;
            array_push($answers, $optionArray);
        }
    }
    update_post_meta($question_post_id, 'answers', $answers);
}

function test_trial() {
// Create array of Course info from CSV data
	$wpdata['post_title'] = "SAMPLE TRIAL COURSE 1";
	$wpdata['post_status'] ='publish';
	$wpdata['post_type'] = 'stm-courses';
	$course_post_id = wp_insert_post( $wpdata );

	update_post_meta($course_post_id, 'shareware', 'on');

}
add_shortcode( 'test_text_lesson' , 'create_course_data');
