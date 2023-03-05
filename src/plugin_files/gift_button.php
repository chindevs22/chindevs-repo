// code that calls the gift_course.js script code (not actual file names)
// masterstudy-lms-learning-management-system-pro/stm-lms-templates/global/gift-button/mixed.php
<?php
/**
 * @var $course_id
 * @var $item_id
 */

stm_lms_register_script( 'buy-button', array( 'jquery.cookie' ) );
stm_lms_register_style( 'buy-button-mixed' );

$item_id      = ( ! empty( $item_id ) ) ? $item_id : '';
$has_course   = STM_LMS_User::has_course_access( $course_id, $item_id, false );
$course_price = STM_LMS_Course::get_course_price( $course_id );

if ( isset( $has_access ) ) {
	$has_course = $has_access;
}

$is_prerequisite_passed = true;

if ( class_exists( 'STM_LMS_Prerequisites' ) ) {
	$is_prerequisite_passed = STM_LMS_Prerequisites::is_prerequisite( true, $course_id );
}

do_action( 'stm_lms_before_button_mixed', $course_id );

if ( apply_filters( 'stm_lms_before_button_stop', false, $course_id ) && false === $has_course ) {
	return false;
}

$is_affiliate = STM_LMS_Courses_Pro::is_external_course( $course_id );
$not_salebale = get_post_meta( $course_id, 'not_single_sale', true );


if ( ! $is_affiliate ) :
	?>
	<div class="stm-lms-buy-buttons stm-lms-buy-buttons-mixed stm-lms-buy-buttons-mixed-pro">
		<?php
			$price             = get_post_meta( $course_id, 'price', true );
			$sale_price        = STM_LMS_Course::get_sale_price( $course_id );
			$not_in_membership = get_post_meta( $course_id, 'not_membership', true );
			$btn_class         = array( 'btn btn-default' );

			if ( empty( $price ) && ! empty( $sale_price ) ) {
				$price      = $sale_price;
				$sale_price = '';
			}

			if ( ! empty( $price ) && ! empty( $sale_price ) ) {
				$tmp_price  = $sale_price;
				$sale_price = $price;
				$price      = $tmp_price;
			}

			if ( $not_salebale ) {
				$price      = '';
				$sale_price = '';
			}

			$btn_class[] = 'btn_big heading_font';

			if ( is_user_logged_in() ) {
				$attributes = array();
				if ( ! $not_salebale ) {
					$attributes[] = 'data-buy-course="' . intval( $course_id ) . '"';
				}
			} else {
				stm_lms_register_style( 'login' );
				stm_lms_register_style( 'register' );
				enqueue_login_script();
				enqueue_register_script();
				$attributes = array(
					'data-target=".stm-lms-modal-login"',
					'data-lms-modal="login"',
				);
			}

			$subscription_enabled = ( empty( $not_in_membership ) && STM_LMS_Subscriptions::subscription_enabled() );
			if ( $subscription_enabled ) {
				$plans_courses = STM_LMS_Course::course_in_plan( $course_id );
			}

			$dropdown_enabled = ! empty( $plans_courses );

			if ( empty( $plans_courses ) ) {
				$dropdown_enabled = is_user_logged_in() && class_exists( 'STM_LMS_Point_System' );
			}

			$gift_course = true;
			$mixed_classes   = array( 'stm_lms_mixed_button' );
			$mixed_classes[] = ( $dropdown_enabled ) ? 'subscription_enabled' : 'gifting_course';

			$show_buttons = apply_filters( 'stm_lms_pro_show_button', true, $course_id );
			if ( $show_buttons ) :
			?>
			<div id="form-wrapper" style="display:none;">
						<?php echo do_shortcode('[contact-form-7 id="86012" title="Gift A Course"]'); ?>
					</div>
			<div class="<?php echo esc_attr( implode( ' ', $mixed_classes ) ); ?>">
				<div class="buy-button <?php echo esc_attr( implode( ' ', $btn_class ) ); ?>"
						<?php
						if ( ! $dropdown_enabled ) {
							echo wp_kses_post( implode( ' ', apply_filters( 'stm_lms_buy_button_auth', $attributes, $course_id ) ) );
						}
						?>
				>

					<span>
						<?php esc_html_e( 'Gift Course', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>

					<?php if ( ! empty( $price ) || ! empty( $sale_price ) ) : ?>
						<div class="btn-prices btn-prices-price">

							<?php if ( ! empty( $sale_price ) ) : ?>
								<label class="sale_price" title="<?php echo esc_attr( STM_LMS_Helpers::display_price( $sale_price ) ); ?>"><?php echo wp_kses_post( STM_LMS_Helpers::display_price( $sale_price ) ); ?></label>
							<?php endif; ?>

							<?php if ( ! empty( $price ) ) : ?>
								<label class="price" title="<?php echo esc_attr( STM_LMS_Helpers::display_price( $price ) ); ?>"><?php echo wp_kses_post( STM_LMS_Helpers::display_price( $price ) ); ?></label>
							<?php endif; ?>

						</div>
					<?php endif; ?>

				</div>
			</div>
				<?php
		endif;
		do_action( 'stm_lms_buy_button_end', $course_id );
		?>
	</div>
	<?php
endif;
