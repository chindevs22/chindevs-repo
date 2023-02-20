// inside masterstudy pro plugin
//stm-lms-templates/course/classic/parts/tabs

<div class="stm_lms_course__content">
	<div class="classic-files">
		<?php
		STM_LMS_Templates::show_lms_template(
			'course/parts/course_file',
			array( 'id' => get_the_ID() )
		);
		?>
	</div>
</div>
