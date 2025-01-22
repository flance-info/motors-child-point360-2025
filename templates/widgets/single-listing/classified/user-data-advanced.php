<?php
global $listing_id;

$listing_id = ( is_null( $listing_id ) ) ? get_the_ID() : $listing_id;
?>
<div>
	<?php

	$user_added_by = get_post_meta( $listing_id, 'stm_car_user', true );

	$lowercase_sku  = get_points_plan($listing_id);
	if (in_array($lowercase_sku, ['p2','p3'])){
		$user_added_by = 2;
	}

	if ( ! empty( $user_added_by ) ) :

		$user_data = get_userdata( $user_added_by );

		if ( $user_data ) :
			$user_fields = stm_get_user_custom_fields( $user_added_by );
			$is_dealer   = stm_get_user_role( $user_added_by );

			if ( $is_dealer ) :
				$ratings = stm_get_dealer_marks( $user_added_by );
				?>
				<div class="stm-listing-car-dealer-info">
					<a class="stm-no-text-decoration" href="<?php echo ( is_listing() || stm_is_aircrafts() ) ? esc_url( stm_get_author_link( $user_added_by ) ) : '#!'; ?>">
						<h3 class="title">
							<?php stm_display_user_name( $user_added_by ); ?>
						</h3>
					</a>
					<div class="clearfix">
						<div class="dealer-image">
							<div class="stm-dealer-image-custom-view">
								<a href="<?php echo ( is_listing() || stm_is_aircrafts() ) ? esc_url( stm_get_author_link( $user_added_by ) ) : '#!'; ?>">
									<?php if ( ! empty( $user_fields['logo'] ) ) : ?>
										<img src="<?php echo esc_url( $user_fields['logo'] ); ?>"/>
									<?php else : ?>
										<img src="<?php stm_get_dealer_logo_placeholder(); ?>"/>
									<?php endif; ?>
								</a>
							</div>
						</div>
						<?php if ( ! empty( $ratings['average'] ) ) : ?>
							<div class="dealer-rating">
								<div class="stm-rate-unit">
									<div class="stm-rate-inner">
										<div class="stm-rate-not-filled"></div>
										<div class="stm-rate-filled" style="width:<?php echo esc_attr( $ratings['average_width'] ); ?>"></div>
									</div>
								</div>
								<div class="stm-rate-sum">
									(<?php esc_html_e( 'Reviews', 'motors' ); ?> <?php echo esc_attr( $ratings['count'] ); ?>)
								</div>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( $ud_show_phone || $ud_show_whatsapp || $ud_show_email || $ud_show_location ) : ?>
						<div class="dealer-contacts">
							<?php if ( ! empty( $user_fields['phone'] ) && $ud_show_phone ) : ?>
								<div class="dealer-contact-unit phone">
									<?php echo wp_kses( $ud_dpn_icon, apply_filters( 'stm_ew_kses_svg', array() ) ); ?>
									<?php if ( $ud_show_full_phone ) : ?>
										<div class="phone heading-font">
											<?php echo esc_html( $user_fields['phone'] ); ?>
										</div>
									<?php else : ?>
										<div class="phone heading-font">
											<?php echo esc_html( substr_replace( $user_fields['phone'], '*******', 3, strlen( $user_fields['phone'] ) ) ); ?>
										</div>
										<span class="stm-show-number" data-listing-id="<?php echo intval( $listing_id ); ?>" data-id="<?php echo esc_attr( $user_fields['user_id'] ); ?>">
											<?php echo esc_html( $ud_dpn_show_number ); ?>
										</span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $ud_show_whatsapp ) && ! empty( $user_fields['phone'] ) && ! empty( $user_fields['stm_whatsapp_number'] ) ) : ?>
								<div class="dealer-contact-unit whatsapp">
									<a href="https://wa.me/<?php echo esc_attr( trim( preg_replace( '/[^0-9]/', '', $user_fields['phone'] ) ) ); ?>" target="_blank">
										<div class="whatsapp-btn">
											<?php echo wp_kses( $ud_wa_icon, apply_filters( 'stm_ew_kses_svg', array() ) ); ?>
											<span>
											<?php echo esc_html( $ud_wa_label ); ?>
										</span>
										</div>
									</a>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $ud_show_email ) && ! empty( $user_fields['email'] ) && ! empty( $user_fields['show_mail'] ) ) : ?>
								<div class="dealer-contact-unit mail">
									<a href="mailto:<?php echo esc_attr( $user_fields['email'] ); ?>">
										<div class="email-btn">
											<?php echo wp_kses( $ud_de_icon, apply_filters( 'stm_ew_kses_svg', array() ) ); ?>
											<span>
											<?php echo esc_html( $ud_de_label ); ?>
										</span>
										</div>
									</a>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $ud_show_location ) && ! empty( $user_fields['location'] ) ) : ?>
								<div class="dealer-contact-unit address">
									<?php echo wp_kses( $ud_location_icon, apply_filters( 'stm_ew_kses_svg', array() ) ); ?>
									<div class="address"><?php echo esc_attr( $user_fields['location'] ); ?></div>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="stm-listing-car-dealer-info stm-common-user">
					<div class="clearfix stm-user-main-info-c">
						<div class="image">
							<a href="<?php echo ( is_listing() || stm_is_aircrafts() ) ? esc_url( stm_get_author_link( $user_added_by ) ) : '#!'; ?>">
								<?php if ( ! empty( $user_fields['image'] ) ) : ?>
									<img src="<?php echo esc_url( $user_fields['image'] ); ?>"/>
								<?php else : ?>
									<div class="no-avatar">
										<i class="stm-service-icon-user"></i>
									</div>
								<?php endif; ?>
							</a>
						</div>
						<a class="stm-no-text-decoration" href="<?php echo ( is_listing() || stm_is_aircrafts() ) ? esc_url( stm_get_author_link( $user_added_by ) ) : '#!'; ?>">

							<?php if (!in_array($lowercase_sku, ['p2','p3'])){ ?>
								<h3 class="title">
								<?php stm_display_user_name( $user_added_by ); ?>
							</h3>
							<div class="stm-label"><?php esc_html_e( 'Private Seller', 'motors' ); ?></div>
							<?php }else{ ?>
								<h3 class="title"> Point360 </h3>
							<?php } ?>
						</a>
					</div>

					<?php if ( $ud_show_phone || $ud_show_whatsapp || $ud_show_email ) : ?>
						<div class="dealer-contacts">
							<?php if ( ! empty( $user_fields['phone'] ) && $ud_show_phone ) : ?>
								<div class="dealer-contact-unit phone">
									<?php echo wp_kses( $ud_dpn_icon, apply_filters( 'stm_ew_kses_svg', array() ) ); ?>
									<?php if ( $ud_show_full_phone ) : ?>
										<div class="phone heading-font"><?php echo esc_html( $user_fields['phone'] ); ?></div>
									<?php else : ?>
										<div class="phone heading-font">
											<?php echo esc_html( substr_replace( $user_fields['phone'], '*******', 3, strlen( $user_fields['phone'] ) ) ); ?>
										</div>
										<span class="stm-show-number" data-listing-id="<?php echo intval( $listing_id ); ?>" data-id="<?php echo esc_attr( $user_fields['user_id'] ); ?>">
											<?php echo esc_html( $ud_dpn_show_number ); ?>
										</span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $ud_show_whatsapp ) && ! empty( $user_fields['phone'] ) && ! empty( $user_fields['stm_whatsapp_number'] ) ) : ?>
								<div class="dealer-contact-unit whatsapp">
									<a href="https://wa.me/<?php echo esc_attr( trim( preg_replace( '/[^0-9]/', '', $user_fields['phone'] ) ) ); ?>" target="_blank">
										<div class="whatsapp-btn heading-font">
											<?php echo wp_kses( $ud_wa_icon, apply_filters( 'stm_ew_kses_svg', array() ) ); ?>
											<span>
											<?php echo esc_html( $ud_wa_label ); ?>
										</span>
										</div>
									</a>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $ud_show_email ) && ! empty( $user_fields['email'] ) && ! empty( $user_fields['show_mail'] ) ) : ?>
								<div class="dealer-contact-unit mail">
									<a href="mailto:<?php echo esc_attr( $user_fields['email'] ); ?>">
										<div class="email-btn heading-font">
											<?php echo wp_kses( $ud_de_icon, apply_filters( 'stm_ew_kses_svg', array() ) ); ?>
											<span>
											<?php echo esc_html( $ud_de_label ); ?>
										</span>
											</span>
										</div>
									</a>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php
			endif;
		endif;
	endif;
	?>
</div>
