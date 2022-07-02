<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>
<style>
	#frm_err {
		padding: 10px;
		color: red;
	}
</style>



<section class="elementor-section elementor-top-section elementor-element elementor-element-bafdf52 inner-banner inner-banner__overlay-two elementor-section-boxed elementor-section-height-default elementor-section-height-default parallax_section_no qode_elementor_container_no" data-id="bafdf52" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
	<div class="elementor-container elementor-column-gap-default">
		<div class="elementor-row">
			<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-36d2c16" data-id="36d2c16" data-element_type="column">
				<div class="elementor-column-wrap elementor-element-populated">
					<div class="elementor-widget-wrap">
						<div class="elementor-element elementor-element-8e84600 inner-banner__title--mobile elementor-widget elementor-widget-text-editor" data-id="8e84600" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
								<div class="elementor-text-editor elementor-clearfix">
									<h2 class="inner-banner__title inner-banner__title--mobile">
									</h2>
								</div>
							</div>
						</div>


					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="elementor-section elementor-top-section elementor-element elementor-element-f434517 elementor-section-boxed elementor-section-height-default elementor-section-height-default parallax_section_no qode_elementor_container_no" data-id="f434517" data-element_type="section">
	<div class="elementor-container elementor-column-gap-extended">
		<div class="elementor-row">
			<div class="center elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-0c2c716" data-id="0c2c716" data-element_type="column">
				<div class="elementor-column-wrap elementor-element-populated">
					<div class="elementor-widget-wrap">
						<div class="elementor-element elementor-element-7ae3d87 block-title text-left elementor-widget elementor-widget-text-editor" data-id="7ae3d87" data-element_type="widget" data-widget_type="text-editor.default">
							<div class="elementor-widget-container">
								<div class="elementor-text-editor elementor-clearfix">
									<h3 class="block-title__title">Log In</h3>
								</div>
							</div>
						</div>
						<div class="elementor-element elementor-element-56f45a3 elementor-widget elementor-widget-bridge_cf_7" data-id="56f45a3" data-element_type="widget" data-widget_type="bridge_cf_7.default">
							<div class="elementor-widget-container">
								<div class="qode-contact-form-7 qode-contact-form-7-0">
									<div role="form" class="wpcf7" id="wpcf7-f679-p673-o1" lang="en-US" dir="ltr">
										<?php echo $this->Form->create(null, ['url' => ['controller' => 'users', 'action' => 'login'], 'autocomplete' => 'off', 'id' => 'e_frm', 'class' => 'auth-login-form mt-2', 'data-toggle' => 'validator']);  ?>

										<div class="row">
											<div class="col-sm-12">
												<div class="case-form-one__field">
													<span class="wpcf7-form-control-wrap first-name">
														<?php echo $this->Form->control('user_name', ['id' => 'user_name', 'label' => false, 'type' => 'text', 'class' => 'wpcf7-form-control wpcf7-text placeholder', 'autocomplete' => 'new-email', 'placeholder' => 'User Name', 'templates' => ['inputContainer' => '{{content}}'], 'required' => true]); ?>
													</span>
												</div>
												</p>
											</div>
											<div class="col-sm-12">
												<div class="case-form-one__field">
													<span class="wpcf7-form-control-wrap first-name">
														<?php echo $this->Form->control('password', ['id' => 'password', 'label' => false, 'type' => 'password', 'class' => 'wpcf7-form-control wpcf7-text placeholder', 'autocomplete' => 'new-password', 'placeholder' => 'Password', 'templates' => ['inputContainer' => '{{content}}'], 'required' => true]); ?>
													</span>
												</div>
												</p>
											</div>

											<div class="col-lg-12 col-md-12 col-sm-12">

												<div id="frm_err"></div>

											</div>
											<div class="col-lg-12 col-md-12 col-sm-12">
												<div class="case-form-one__field text-left mb-0">
													<input type="button" value="Login" class="wpcf7-form-control wpcf7-submit qbutton contact_form_button thm-btn case-form-one__btn" id="login_sbtn" />
												</div>
											</div>
											<p>
												<!-- /.col-md-6 col-sm-12 -->
										</div>

										<?php echo $this->Form->end(); ?>
										<br><br><br>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	$(document).ready(function() {


		$("#login_sbtn").click(function() {
			$("#e_frm").ajaxForm({
				target: '#frm_err',
				headers: {
					'X-CSRF-Token': $('[name="_csrfToken"]').val()
				},
				beforeSubmit: function() {
					$("#login_sbtn").prop("disabled", true);
					$("#login_sbtn").val('Please wait..');
				},
				success: function(response) {
					$("#login_sbtn").prop("disabled", false);
					$("#login_sbtn").val('Login');
				},
				error: function(response) {
					$('#f_err').html('<div class="alert alert-danger">Sorry, this is not working at the moment. Please try again later.</div>');
					$("#login_sbtn").prop("disabled", false);
					$("#login_sbtn").val('Login');
				},
			}).submit();
		});
	});
</script>