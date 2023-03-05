// in enqueue.php
// there is this peice of code related to price symbol

// in plugins -> lms non pro version -> _core/lms/enqueue.php
wp_localize_script(
		'stm-lms-lms',
		'stm_lms_vars',
		array(
			'symbol'             => STM_LMS_Options::get_option( 'currency_symbol', '$' ),
			'position'           => STM_LMS_Options::get_option( 'currency_position', 'left' ),
			'currency_thousands' => STM_LMS_Options::get_option( 'currency_thousands', ',' ),
			'wp_rest_nonce'      => wp_create_nonce( 'wp_rest' ),
		)
	);