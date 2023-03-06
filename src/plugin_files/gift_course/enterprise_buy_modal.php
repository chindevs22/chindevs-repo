//masterstudy-lms-learning-management-system-pro/stm-lms-templates/gift_courses/enterprise-buy-modal.php
// the file that generates the modal
<?php
/**
 * @var $course_id
 */
$groups = STM_LMS_Enterprise_Courses::stm_lms_get_enterprise_groups( true );
$price  = STM_LMS_Enterprise_Courses::get_enterprise_price( $course_id );
$limit = 10;
$user    = STM_LMS_User::get_current_user();
$user_id = $user['id'];
?>

<h2><?php esc_html_e( 'Gift This Course', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>
<div class="course_name">
	<?php
	printf(
		/* translators: %s Bundle price */
		esc_html__( '%s', 'masterstudy-lms-learning-management-system-pro' ), // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
		esc_html( get_the_title( $course_id ) )
	);
	?>
</div>

<div class="actions has-groups'">
	<a href="#"
		data-course-id="<?php echo intval( $course_id ); ?>"
		class="btn btn-default add-to-cart disabled"
		data-enterprise-price="<?php echo esc_attr( $price ); ?>">
		<?php
		printf(
			/* translators: %s Price */
			esc_html__( 'Add to cart %s', 'masterstudy-lms-learning-management-system-pro' ),
			'<span>' . STM_LMS_Helpers::display_price( '0' ) . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		?>
	</a>

	<a href="#" class="create_group"><?php esc_html_e( 'Add a Group', 'masterstudy-lms-learning-management-system-pro' ); ?></a>
</div>


<div class="stm_lms_popup_create_group">

	<div class="stm_lms_popup_create_group__inner">

		<div class="row">
			<div class="col-sm-6">
				<label>
					<span class="heading_font">
						<?php
						printf(
							/* translators: %s Group Limit */
							__( 'Add users: <span>(Max : %s)</span>', 'masterstudy-lms-learning-management-system-pro' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$limit // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						);
						?>
					</span>
					<input type="text" placeholder="<?php esc_attr_e( 'Enter member E-mail...', 'masterstudy-lms-learning-management-system-pro' ); ?>" class="form-control" name="gc_emails" id="gc_email"/>
					<span class="add_email_gc"><i class="lnricons-arrow-return"></i></span>
				</label>
			</div>

			<div class="col-sm-12">
				<div class="gc-emails"></div>

				<div class="stm_lms_group_new_error"></div>
			</div>
		</div>

	</div>

</div>
