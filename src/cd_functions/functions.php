<?php
// --------------------------------------------------------------------------------------------
// CHIN DEVS GLOBAL VARIABLES
// --------------------------------------------------------------------------------------------

$sectionToLessonMap = array(); //mgml section ID -> array string "Section Name WP_LESSONID1 WP_LESSONID2"
$lessonToQuestionsMap = array(); // mgml quiz ID -> array of WP question IDS
$lessonMGMLtoWP = array();
$courseMGMLtoWP = array();
$questionMGMLtoWP = array();
$attemptNumberMap = array(); // userID + courseId + quizId -> attempt number for all questions
$wpQuestionsToAnswers = array(); // wp question ID to wp array of answers
$userMGMLtoWP = array();
$randomEmailCounter = 50;
$selfAssessmentToUser = array(); // self assessment id in mgml to user in mgml
$existingMetaMapping = array (
        'billing_address_1' => 'address',
        'billing_city' =>'city',
        'billing_state' => 'state',
        'billing_country' => 'country',
        'billing_postcode' => 'pin_code',
        'billing_phone' => 'phone_no'
    );

$newMetaMapping = array (
     'date_of_birth' => 'dob',
     'gender' => 'gender'
);

$productCategoryMap = array(
    "Publications" => 95,
    "Pendrives" => 96,
    "Combo Offers" => 303
);

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

/////''' Custom code for any outputs modifying
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

//
//
// --------------------------------------------------------------------------------------------
// CHIN DEV CODE ADDED BELOW
// --------------------------------------------------------------------------------------------
//
//

require_once 'cd_functions/import_questions.php';
require_once 'cd_functions/import_lessons.php';
require_once 'cd_functions/import_courses.php';
require_once 'cd_functions/import_users.php';
require_once 'cd_functions/import_user_progress.php';
require_once 'cd_functions/import_publications.php';
require_once 'cd_functions/registration_form_api.php';
require_once 'cd_functions/product_shortcodes.php';


function create_course_data() {
    read_csv("questions.csv", "question");
    read_csv("lesson_test.csv", "lesson");
    read_csv("course_materials.csv", "course");
    read_csv("users.csv", "user");
    read_csv("user_self_assessment.csv", "userquiz");
    read_csv("user_self_assessment_details.csv", "useranswers");
    read_csv("enrol.csv", "enrol");
	//read_csv("publications.csv", "publications");
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
		} else if ($type == "course") {
			create_course_from_csv($tempArray);
		} else if ($type == "question") {
			create_question_from_csv($tempArray);
		} else if ($type == "user") {
		    create_user_from_csv($tempArray);
		} else if ($type == "userquiz") {
		    progress_users_quiz_from_csv($tempArray);
		} else if ($type == "useranswers") {
		    progress_users_answers_from_csv($tempArray);
		} else if ($type == "enrol") {
		    enrol_users_from_csv($tempArray);
		} else if ($type == "publications") {
			create_publications_from_csv($tempArray);
		}
	}
	fclose($file);
}


//FEEDBACK FORM
// based on form submission
function submit_form_js() {
    ?>
        <script>
			document.addEventListener( 'wpcf7submit', function( event ) {
			  button = document.getElementsByClassName("stm-lms-lesson_navigation_complete")[0];
			  button.style.display = "inline";
			}, false );

        </script>
    <?php
}

function hide_complete_button() {
	?>
		<style>.stm-lms-lesson_navigation_complete {display: none;}</style>
	<?php
}
add_action('wp_head', 'submit_form_js');
add_shortcode('shortcodefeedback', 'hide_complete_button'); // required on lesson page

// add_shortcode( 'test-functions', 'create_country_options' );
// add_action('wp_head', 'update_registration_form');
//add_shortcode( 'test-functions', 'create_course_data' );
add_shortcode( 'test-functions', 'create_course_data' );
