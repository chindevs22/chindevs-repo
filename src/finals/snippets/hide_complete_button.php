

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