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

function read_csv() {

    print "READING CSV\n";
    //file mapping from our File Manager
	$fileName = '/home/freewaydns-dev108/cd-test-docs/chin_users_2.csv';
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
        $csvUserID;
        $tempArray = array();

        // create mapping based on ehader
        foreach($line as $value) {
			$tempArray[$mappingLine[$count++]] = $value;
        }
		create_user_from_csv($tempArray);
	}
	fclose($file);
	print "COMPLETED CSV READING\n";
}

// OLD CHIN USER DATA FROM OTHER MAP
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


function create_user_from_csv($userData) {
    // Create array of User info from CSV data
	$wpdata['user_pass'] = $userData['password'];
	$wpdata['user_login'] = $userData['name'];
	$wpdata['first_name'] = explode(' ', $userData['name'])[0];
	$wpdata['last_name'] = explode(' ', $userData['name'])[1];
	$wpdata['display_name'] = $userData['name'];
	$wpdata['user_email'] = $userData['email'];

	//Check to see if an account exists with the given username and email address value.
	if ( !username_exists($wpdata['user_login']) && !email_exists($wp_data['user_email']) ) {
		//Create WP User entry
		$user_id = wp_insert_user($wpdata);
		//WP User object
		$wp_user = new WP_User($user_id);
		echo "CREATED USER " . $user_id . "\n";

		//Set the role of this user to administrator.
		$wp_user->set_role('subscriber');
		// Create the Metadata for the user
		create_meta($userData, $user_id);
	}
	else {
		print "Already Created User\n";
		wp_update_user($wpdata);
		create_meta($userData, $user_id);
	}
}

function create_meta($userData, $user_id) {
    // create on a global scope
    global $existingMetaMapping, $newMetaMapping;

    foreach ($existingMetaMapping as $key => $value) {
       update_user_meta( $user_id, $key, $userData[$value] );
    }
    foreach ($newMetaMapping as $key => $value) {
        echo "results from adding " . $key . "" . add_user_meta( $user_id, $key, $userData[$value], true );
    }
    echo "CREATED Metadata for user " . $user_id . "\n";

    // print_r (get_user_meta(12));
}

add_shortcode( 'test', 'read_csv' );
