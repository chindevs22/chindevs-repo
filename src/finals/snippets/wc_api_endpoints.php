// woo commerce api endpoints

// create country_options on the form
function create_country_options() {
    $form_options = get_option('stm_lms_form_builder_forms');

	$prof_form = $form_options[2];
	$fields = $prof_form['fields'];
	for ($x = 0; $x < count($fields); $x++) {
		$field = $fields[$x];
		if ($field['label'] == 'Country') {
			$field['choices'] = get_countries();
		}
		$fields[$x] = $field;
	}
	$form_options[2]['fields'] = $fields;
	update_option('stm_lms_form_builder_forms', $form_options);
}

function get_all_countries() {
	$url = 'https://dev108.freewaydns.net/wp-json/wc/v3/data/';
    $consumer_key = 'ck_3e131759ef59ae8f530869faa0b7aa4df9b6ba9a';
    $consumer_secret = 'cs_a370a874dc02d0d28fcac5101922b23b9b8bcf30';

    // Get countries
    $response = wp_remote_get( $url . 'countries', array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
        )
    ) );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $countries = json_decode( wp_remote_retrieve_body( $response ) );
	return $countries;

}

function get_countries() {
	$countries = get_all_countries();
    // Get countries
    $country_names = array();
    foreach ( $countries as $country ) {
		array_push($country_names, $country->name);
	}
	return $country_names;
}

function get_country_by_name($name) {
	$countries = get_all_countries();

	 foreach ( $countries as $country ) {
		if ($country->name == $name) {
			return $country;
		}
	}
}

function get_states_by_country($country_name) {
	$country = get_country_by_name($country_name);
	$states = $country->states;
	if (count($states) == 0 ) {
		return array ("No States Available");
	}
	$state_names = array();
	foreach ( $states as $state ) {
		array_push($state_names, $state->name);
	}
	return $state_names;
}

//populate states dropdown
function get_states() {
  $country = $_POST['country'];
  $states = get_states_by_country($country);
  $options = '';
  foreach ($states as $state) {
	  $options .= '<option value="' . $state . '">' . $state . '</option>';
  }
  echo $options;
  wp_die();
}
add_action('wp_ajax_get_states', 'get_states');
add_action('wp_ajax_nopriv_get_states', 'get_states');

// populate cities dropdown - yet to be implemented
function get_cities($state) {
  $state = $_POST['state'];
  $cities = get_cities_by_state($state);
  $options = '';
  foreach ($cities as $city) {
	  $options .= '<option value="' . $city . '">' . $city . '</option>';
  }
  echo $options;
  wp_die();
}

add_action('wp_ajax_get_cities', 'get_cities');
add_action('wp_ajax_nopriv_get_cities', 'get_cities');