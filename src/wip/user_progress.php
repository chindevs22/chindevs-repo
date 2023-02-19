// --------------------------------------------------------------------------------------------
// CHIN DEVS GLOBAL VARIABLES
// --------------------------------------------------------------------------------------------

$sectionToLessonMap = array(); //mgml section ID -> array string "Section Name WP_LESSONID1 WP_LESSONID2"
$lessonToQuestionsMap = array(); // mgml quiz ID -> array of WP question IDS
$lessonMGMLtoWP = array();
$courseMGMLtoWP = array();
$questionMGMLtoWP = array();
$wpQuestionsToAnswers = array(); // wp question ID to wp array of answers
$userMGMLtoWP = array();
$selfAssessmentToUser = array(); // self assessment id in mgml to user in mgml
$existingMetaMapping = array (
        'billing_address_1' => 'address1',
        'billing_address_2' => 'address2',
        'billing_city' =>'city',
        'billing_state' => 'state',
        'billing_country' => 'country',
        'billing_postcode' => 'pin_code'
        'billing_phone' => 'mobile'
    );

$newMetaMapping = array (
     'date_of_birth' => 'dob',
     'gender' => 'gender',
     'nakshthra' =>'nakshthra',
     'education' => 'educational_qualification',
     'spoken_language' => 'spoken_language',
     'reading_writing_languages' => 'reading_writing_languages',
     'mgml_old_id' => 'id'
 );

//
//
// --------------------------------------------------------------------------------------------
// CHIN DEV CODE ADDED BELOW
// --------------------------------------------------------------------------------------------
//
//

function create_course_data() {
	// ensure no duplicate headers in CSV files
	read_csv("sample_questions.csv", "question");
 	read_csv("lesson_materials_section.csv", "lesson");
	read_csv("same_course_material.csv", "course");
	read_csv("users.csv", "user");
	read_csv("user_self_assessment.csv", "userquiz");
	read_csv("user_self_assessment_details.csv", "useranswers");
	read_csv("enrol.csv", "enrol");
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
		} else if ($type == "userquiz" {
		    progress_users_quiz_from_csv($tempArray);
		} else if ($type == "useranswers" {
		    progress_users_answers_from_csv($tempArray);
		} else if ($type == "enrol") {
		    enrol_users_from_csv($tempArray);
		}
	}
	fclose($file);
}

// Build the Media Path
function generate_lesson_file_string($attachment, $course_id) {
    $path2 = "{\"error\":\"\",\"path\":\"/home/freewaydns-dev108/htdocs/dev108.freewaydns.net/wp-content/uploads/course_materials/{$course_id}/{$attachment}\",\"url\":\"https://dev108.freewaydns.net/wp-content/uploads/course_materials/{$course_id}/{$attachment}\"}";
    $file_data2 = json_encode(array(
            "lesson_files" => $path2,
            "lesson_files_label" => $attachment
        ));
   return "[{$file_data2}]";
}

// Create Lesson Data
function create_lesson_from_csv($lessonData) {
	 global $lessonToQuestionsMap, $sectionToLessonMap, $lessonMGMLtoWP;

    $wpdata['post_title'] = $lessonData['lesson_name'];
    $wpdata['post_status'] ='publish';
    if ($lessonData['lesson_type'] == 'quiz') {
        $wpdata['post_type'] = 'stm-quizzes';
        $wpdata['post_content'] = $lessonData['summary'];
    } else {
        $wpdata['post_type'] = 'stm-lessons';
    }
    $lesson_post_id = wp_insert_post( $wpdata );

    $lessonMGMLtoWP[$lessonData['id']] = $lesson_post_id; //save MGML ID
    $sectionID = $lessonData['section_id']; //map section ID for course
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

// Build Course
function create_course_from_csv($courseData) {
    global $courseMGMLtoWP, $sectionToLessonMap;

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

	foreach ($sectionArray as $sectionID) {
		if ($sectionToLessonMap[$sectionID]) {
			$combinedArray = array_merge($combinedArray, $sectionToLessonMap[$sectionID]);
		}
    }

	$curriculum_string = implode(",", $combinedArray);
	$course_post_id = wp_insert_post( $wpdata );
	$courseMGMLtoWP[$courseData['id']] = $course_post_id;
	update_post_meta($course_post_id, 'price', $courseData['price_usd']);
	update_post_meta($course_post_id, 'curriculum', $curriculum_string);
}


function create_array_from_string($sectionString, $delimiter) {
	$sectionString = trim($sectionString, '[');
    $sectionString = trim($sectionString, ']');
    $sectionString = trim($sectionString, ' ');
	$sectionString = trim($sectionString, '"');
    $sectionArray = explode($delimiter, $sectionString);
    return $sectionArray;
}

function create_question_from_csv($questionData) {
    global $lessonToQuestionsMap, $questionMGMLtoWP, $wpQuestionsToAnswers;

    $wpdata['post_title'] = $questionData['title'];
    $wpdata['post_status'] ='publish';
    $wpdata['post_type'] = 'stm-questions';
    $question_post_id = wp_insert_post( $wpdata );
    $quiz_id = $questionData['quiz_id'];

    $questionMGMLtoWP[$questionData['id']] = $question_post_id; //map MGML question ID

    // MAP question ID to quiz
    if (!array_key_exists($quiz_id, $lessonToQuestionsMap)) {
 		$lessonToQuestionsMap[$quiz_id] = array($question_post_id);
    } else {
        array_push($lessonToQuestionsMap[$quiz_id], $question_post_id);
    }

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
    // map WP qusetion to WP Answers List
    // TODO - maybe not an array .. what is answers??
    $wpQuestionsToAnswers[$question_post_id] = $answers;
    update_post_meta($question_post_id, 'answers', $answers);
}

function create_user_from_csv($userData) {
    $global $userMGMLtoWP;

    // Create array of User info from CSV data
	$wpdata['user_pass'] = $userData['password'];
	$wpdata['user_login'] = $userData['name'];
	$wpdata['first_name'] = explode(' ', $userData['name'])[0];
	$wpdata['last_name'] = explode(' ', $userData['name'])[1];
	$wpdata['display_name'] = $userData['name'];
	$wpdata['user_email'] = $userData['email'];

	if ( !username_exists($wpdata['user_login']) && !email_exists($wp_data['user_email']) ) {
		$user_id = wp_insert_user($wpdata);
		$wp_user = new WP_User($user_id);
		$userMGMLtoWP[$userData['id']] = $user_id;
		$wp_user->set_role('subscriber');
		create_meta($userData, $user_id);
	}
	else {
		wp_update_user($wpdata);
		create_meta($userData, $user_id);
	}
}

function create_meta($userData, $user_id) {
    global $existingMetaMapping, $newMetaMapping;
    foreach ($existingMetaMapping as $key => $value) {
       update_user_meta( $user_id, $key, $userData[$value] );
    }
    foreach ($newMetaMapping as $key => $value) {
       add_user_meta( $user_id, $key, $userData[$value], true );
    }
     print_r (get_user_meta(12));
}

function progress_users_quiz_from_csv($progressData) {
  echo "inside user quiz";
  global $wpdb, $userMGMLtoWP, $courseMGMLtoWP, $selfAssessmentToUser;
  $table_name = 'test_stm_lms_user_quizzes';
  $grade = $progressData['marks']/$progressData['quiz_marks'] * 100;
  // map self assessment id to user id
  $selfAssessmentToUser[$progressData['id']] = $progressData['user_id'];

  $wpdb->insert($table_name, array(
      'user_quiz_id' => NULL,
      'user_id' => $userMGMLtoWP[$progressData['user_id']],
      'course_id' => $courseMGMLtoWP[$progressData['course_id']], //its getting JOINED to TABLE
      'quiz_id' => $lessonMGMLtoWP[$progressData['quiz_id']],
      'progress' => $grade,
      'status' => 'passed',
      'sequency' => '[]',
  ));
}

function progress_users_answers_from_csv($answerData) {
   echo "inside user answers users";
    global $wpdb, $userMGMLtoWP, $courseMGMLtoWP, $lessonMGMLtoWP, $selfAssessmentToUser, $questionMGMLtoWP;
    $table_name = 'test_stm_lms_user_answers';

    // get user id
    $sa_id = $answerData['self_assessment_id'];
    $mgml_user_id = $selfAssessmentToUser[$sa_id];

    // TODO CHECK
    // the WP question ID should be part of the $lessonToQuestionsMap of $answerData['quiz_id']

    $wp_question_id = $questionMGMLtoWP[$answerData['question_id']];
    $options = $wpQuestionsToAnswers[$wp_question_id];
    $userAnswers = create_array_from_string($answerData['answers'], '","'); // ex [ 2, 4 ]
    $arrLength = count($userAnswers);
    $chosenAnswers = array();
    for($x = 0; $x < $arrLength; $x++) {
        $correctAnswer = $userAnswers[$x]; //2
        array_push($chosenAnswers, $options[$correctAnswer - 1]);
    }
    $answerString = implode(",", $chosenAnswers); // comma seperated string of answers

    /// EVENTUALY DON'T HARDCODE THE ATTEMPT NUMBER
    $correctAnswer = ($answerData['question_marks'] == $answerData['marks_obtained']) ? "1" : "0";
    $wpdb->insert($table_name, array(
        'user_answer_id' => NULL,
        'user_id' => $userMGMLtoWP[$mgml_user_id],
        'course_id' => $courseMGMLtoWP[$answerData['course_id']], //being joined to details
        'quiz_id' => $lessonMGMLtoWP[$answerData['quiz_id']], // being joined to details
        'question_id' => $wp_question_id,
        'user_answer' => $answerString,
        'correct_answer' => $correctAnswer, //// a 1 or zero
        'attempt_number' => 1,
    ));
}

function enrol_users_from_csv($enrolData) {
   echo "inside enrol users";
    global $wpdb, $userMGMLtoWP, $courseMGMLtoWP, $lessonMGMLtoWP, $selfAssessmentToUser, $questionMGMLtoWP;
    $table_name = 'test_stm_lms_user_courses';
    $wpdb->insert($table_name, array(
        'user_course_id' => NULL,
        'user_id' => $userMGMLtoWP[$enrolData['id']],
        'course_id' => $courseMGMLtoWP[$enrolData['course_id']],
        'current_lesson_id' => '0',
        'progress_percent' => '43',
        'status' => 'enrolled',
    ));
}

add_shortcode( 'test_text_lesson' , 'create_course_data');
