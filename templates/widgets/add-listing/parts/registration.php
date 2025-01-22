<?php
$stm_title_user = stm_me_get_wpcfto_mod( 'addl_reg_log_title', '' );
$stm_text_user  = stm_me_get_wpcfto_mod( 'addl_reg_log_desc', '' );
$_link          = stm_me_get_wpcfto_mod( 'addl_reg_log_link', '' );
?>
<div class="stm-user-registration-unit">
	<div class="clearfix stm_register_title">
		<h3><?php esc_html_e( 'OPRET KONTO', 'motors-elementor-widgets' ); ?></h3>
		<div class="stm_login_me"><?php esc_html_e( 'Har du allerede en konto?', 'motors-elementor-widgets' ); ?>
			<a href="#"><?php esc_html_e( 'Log ind med eksisterende konto', 'motors-elementor-widgets' ); ?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-sm-3 col-md-push-9 col-sm-push-9 col-xs-push-0">
			<?php if ( stm_is_listing() && defined( 'WORDPRESS_SOCIAL_LOGIN_ABS_PATH' ) ) : ?>
				<div class="stm-social-login-wrap">
					<?php do_action( 'wordpress_social_login' ); ?>
				</div>
			<?php endif; ?>
			<div class="heading-font stm-title"><?php echo esc_html( $stm_title_user ); ?></div>
			<div class="stm-text"><?php echo esc_html( $stm_text_user ); ?></div>
		</div>

		<div class="col-md-9 col-sm-9 col-md-pull-3 col-sm-pull-3 col-xs-pull-0">
			<div class="stm-login-register-form">
				<div class="stm-register-form">
					<form id="add-car-register-form" method="post">
						<input type="hidden" name="redirect" value="disable">

						<div class="row form-group">
							<div class="col-md-6">
								<h4><?php esc_html_e( 'Fornavn', 'motors-elementor-widgets' ); ?></h4>
								<input class="user_validated_field" type="text" name="stm_user_first_name" placeholder="<?php esc_attr_e( 'Indtast fornavn', 'motors-elementor-widgets' ); ?>"/>
							</div>
							<div class="col-md-6">
								<h4><?php esc_html_e( 'Efternavn', 'motors-elementor-widgets' ); ?></h4>
								<input class="user_validated_field" type="text" name="stm_user_last_name" placeholder="<?php esc_attr_e( 'Indtast efternavn', 'motors-elementor-widgets' ); ?>"/>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-md-6">
								<h4><?php esc_html_e( 'Telefonnummer', 'motors-elementor-widgets' ); ?></h4>
								<input class="user_validated_field" type="tel" name="stm_user_phone" placeholder="<?php esc_attr_e( 'Indtast telefonnummer', 'motors-elementor-widgets' ); ?>"/>
								<label for="whatsapp-checker">
									<input type="checkbox" name="stm_whatsapp_number" id="whatsapp-checker"/>
									<span><small
												class="text-muted"><?php esc_html_e( 'Jeg har Whatsapp konto med dette telefonnummer', 'motors-elementor-widgets' ); ?></small></span>
								</label>
							</div>
							<div class="col-md-6">
								<h4><?php esc_html_e( 'Email *', 'motors-elementor-widgets' ); ?></h4>
								<input class="user_validated_field" type="email" name="stm_user_mail" placeholder="<?php esc_attr_e( 'Indtast e-mail', 'motors-elementor-widgets' ); ?>"/>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-md-6">
								<h4><?php esc_html_e( 'Brugernavn *', 'motors-elementor-widgets' ); ?></h4>
								<input class="user_validated_field" type="text" name="stm_nickname" placeholder="<?php esc_attr_e( 'Indtast brugernavn', 'motors-elementor-widgets' ); ?>"/>
							</div>
							<div class="col-md-6">
								<h4><?php esc_html_e( 'Adgangskode *', 'motors-elementor-widgets' ); ?></h4>
								<div class="stm-show-password">
									<i class="fas fa-eye-slash"></i>
									<input class="user_validated_field" type="password" name="stm_user_password" placeholder="<?php esc_attr_e( 'Indtast adgangskode', 'motors-elementor-widgets' ); ?>"/>
								</div>
							</div>
						</div>

						<div class="form-group form-checker">
							<label>
								<input type="checkbox" name="stm_accept_terms"/>
								<span>
								<?php esc_html_e( 'JEG ACCEPTERE', 'motors-elementor-widgets' ); ?>
									<?php
									if ( ! empty( $_link ) ) :
										?>
										<a href="<?php echo get_the_permalink($_link); ?>" target="_blank"><?php echo get_the_title($_link); ?></a><?php //phpcs:ignore?>
									<?php endif; ?>
							</span>
							</label>
						</div>

						<div class="form-group form-group-submit">
							<?php
							$recaptcha_enabled    = stm_me_get_wpcfto_mod( 'enable_recaptcha', 0 );
							$recaptcha_public_key = stm_me_get_wpcfto_mod( 'recaptcha_public_key' );
							$recaptcha_secret_key = stm_me_get_wpcfto_mod( 'recaptcha_secret_key' );

							if ( ! empty( $recaptcha_enabled ) && $recaptcha_enabled && ! empty( $recaptcha_public_key ) && ! empty( $recaptcha_secret_key ) ) :
								//// phpcs:disable
								?>
								<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>
								<script type="text/javascript">
                                    function onSubmitACRegister() {
                                        var form = $('#add-car-register-form');

                                        $.ajax({
                                            type: "POST",
                                            url: ajaxurl,
                                            dataType: 'json',
                                            context: this,
                                            data: form.serialize() + '&action=stm_custom_register',
                                            beforeSend: function () {
                                                form.find('input').removeClass('form-error');
                                                form.find('.stm-listing-loader').addClass('visible');
                                                $('.stm-validation-message').empty();
                                            },
                                            success: function (data) {
                                                if (data.user_html) {
                                                    var $user_html = $(data.user_html).appendTo('#stm_user_info');
                                                    $('.stm-not-disabled, .stm-not-enabled').slideUp('fast', function () {
                                                        $('#stm_user_info').slideDown('fast');
                                                    });
                                                    $("html, body").animate({scrollTop: $('.stm-form-checking-user').offset().top}, "slow");

                                                    $('.stm-form-checking-user button[type="submit"]').removeClass('disabled').addClass('enabled');

                                                    if (data.plans_select && $('#user_plans_select_wrap').length > 0) {
                                                        $('#user_plans_select_wrap').html(data.plans_select);
                                                        $('#user_plans_select_wrap select').select2();
                                                    }
													$('.stm-login-display').show();
													$('.stm-login-hide').hide();

                                                }

                                                if (data.restricted && data.restricted) {
                                                    $('.btn-add-edit').remove();
                                                }

                                                form.find('.stm-listing-loader').removeClass('visible');
                                                for (var err in data.errors) {
                                                    form.find('input[name=' + err + ']').addClass('form-error');
                                                }

                                                if (data.redirect_url) {
                                                    window.location = data.redirect_url;
                                                }

                                                if (data.message) {
                                                    var message = $('<div class="stm-message-ajax-validation heading-font">' + data.message + '</div>').hide();

                                                    form.find('.stm-validation-message').append(message);
                                                    message.slideDown('fast');
                                                }
                                            }
                                        });
                                    }
								</script>
								<?php // phpcs:enable?>
							<input class="g-recaptcha heading-font" data-sitekey="<?php echo esc_attr( $recaptcha_public_key ); ?>" data-callback='onSubmitACRegister' type="submit" value="<?php esc_html_e( 'Sign up now!', 'motors-elementor-widgets' ); ?>" disabled/>
							<?php else : ?>
							<input class="heading-font" type="submit" value="<?php esc_html_e( 'OPRET KONTO', 'motors-elementor-widgets' ); ?>" disabled/>
							<?php endif; ?>
							<span class="stm-listing-loader"><i class="stm-icon-load1"></i></span>
						</div>

						<div class="stm-validation-message"></div>

					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php // phpcs:disable ?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var $ = jQuery;
        $('.stm-show-password .fas').mousedown(function () {
            $(this).closest('.stm-show-password').find('input').attr('type', 'text');
            $(this).addClass('fa-eye');
            $(this).removeClass('fa-eye-slash');
        });

        $(document).mouseup(function () {
            $('.stm-show-password').find('input').attr('type', 'password');
            $('.stm-show-password .fas').addClass('fa-eye-slash');
            $('.stm-show-password .fas').removeClass('fa-eye');
        });

        $("body").on('touchstart', '.stm-show-password .fas', function () {
            $(this).closest('.stm-show-password').find('input').attr('type', 'text');
            $(this).addClass('fa-eye');
            $(this).removeClass('fa-eye-slash');
        });
    });
</script>
<?php // phpcs:enable ?>
