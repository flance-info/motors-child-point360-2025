<?php
$user = stm_get_user_custom_fields( $user_id );

if ( is_wp_error( $user ) ) {
	return;
}
$dealer = stm_get_user_role( $user['user_id'] );
	$post_ids = [1755, 93719, 93740,93728];
if ( $dealer ) :
	$ratings = stm_get_dealer_marks( $user_id ); ?>

	<div class="stm-add-a-car-user">
		<div class="stm-add-a-car-user-wrapper">
			<div class="left-info left-dealer-info">
				<div class="stm-dealer-image-custom-view">
					<?php if ( ! empty( $user['logo'] ) ) : ?>
						<img src="<?php echo esc_url( $user['logo'] ); ?>"/>
					<?php else : ?>
						<img src="<?php stm_get_dealer_logo_placeholder(); ?>"/>
					<?php endif; ?>
				</div>
				<h4><?php stm_display_user_name( $user['user_id'], $user_login, $f_name, $l_name ); ?></h4>

				<?php if ( ! empty( $ratings['average'] ) ) : ?>
					<div class="stm-star-rating">
						<div class="inner">
							<div class="stm-star-rating-upper" style="width:<?php echo esc_attr( $ratings['average_width'] ); ?>"></div>
							<div class="stm-star-rating-lower"></div>
						</div>
						<div class="heading-font"><?php echo wp_kses_post( $ratings['average'] ); ?></div>
					</div>
				<?php endif; ?>

			</div>

			<ul class="add-car-btns-wrap">
				<?php

				if ( false === $restricted ) :
					if ( in_array($post_id, $post_ids )) {
						$btn_type = ( ! empty( $_id ) ) ? 'edit' : 'pay';
					} else {
						$btn_type = ( ! empty( $_id ) ) ? 'edit' : 'pay';
					}
					$btn_type = ( ! empty( get_post_meta( $_id, 'pay_per_listing', true ) ) ) ? 'edit-ppl' : $btn_type;
					?>
				<br/><br />
					<li class="btn-add-edit">
						<button type="submit" class="heading-font enabled" data-load="<?php echo esc_attr( $btn_type ); ?>"
							<?php
							if ( empty( $_id ) ) {
								echo 'data-toggle="tooltip" data-placement="top" title="' . esc_html__( 'Add a Listing using Free or Paid Plan limits', 'motors-elementor-widgets' ) . '"';
							}
							?>
						>
							<?php if ( ! empty( $_id ) ) : ?>
								<i class="stm-service-icon-add_check"></i><?php esc_html_e( 'Edit Listing', 'motors-elementor-widgets' ); ?>
							<?php else : ?>
								<i class="stm-service-icon-add_check"></i><?php esc_html_e( 'OPRET KØRETØJ', 'motors-elementor-widgets' ); ?>
							<?php endif; ?>
						</button>
						<span class="stm-add-a-car-loader pay"><i class="stm-icon-load1"></i></span>
					</li>
				<?php endif; ?>
				<?php if ( stm_me_get_wpcfto_mod( 'dealer_pay_per_listing', false ) && empty( $_id ) ) : ?>
				<!--
					<li class="btn-ppl">
						<button type="submit" class="heading-font enabled" data-load="pay"
							<?php
							if ( empty( $_id ) ) {
								echo 'data-toggle="tooltip" data-placement="top" title="' . esc_html__( 'Pay for this Listing', 'motors-elementor-widgets' ) . '"';
							}
							?>
						>
							<i class="stm-service-icon-payment_listing"></i><?php esc_html_e( 'Pay for Listing', 'motors-elementor-widgets' ); ?>
						</button>
						<span class="stm-add-a-car-loader pay"><i class="stm-icon-load1"></i></span>
					</li>
					!-->
				<?php endif; ?>
			</ul>

			<div class="right-info">

				<a target="_blank" href="<?php echo esc_url( add_query_arg( array( 'view-myself' => 1 ), get_author_posts_url( $user_id ) ) ); ?>">
					<i class="fas fa-external-link-alt"></i><?php esc_html_e( 'Vis min profil', 'motors-elementor-widgets' ); ?>
				</a>

				<div class="stm_logout">
					<a href="#"><?php esc_html_e( 'Log ud', 'motors-elementor-widgets' ); ?></a>
					<?php esc_html_e( 'og log p&aring; med en anden konto', 'motors-elementor-widgets' ); ?>
				</div>

			</div>

		</div>
	</div>

<?php else : ?>

	<div class="stm-add-a-car-user">
		<div class="stm-add-a-car-user-wrapper">
			<div class="left-info">
				<div class="avatar">
					<?php if ( ! empty( $user['image'] ) ) : ?>
						<img src="<?php echo esc_url( $user['image'] ); ?>"/>
					<?php else : ?>
						<i class="stm-service-icon-user"></i>
					<?php endif; ?>
				</div>
				<div class="user-info">
					<h4><?php stm_display_user_name( $user['user_id'], $user_login, $f_name, $l_name ); ?></h4>
					<div class="stm-label"><?php esc_html_e( 'Privat s&aelig;lger', 'motors-elementor-widgets' ); ?></div>
				</div>
			</div>

			<ul class="add-car-btns-wrap">
				<?php
				if ( false === $restricted ) :
					$post_id = get_the_ID();  // 1755 is add car page id
					if ( in_array($post_id, $post_ids )) {
						$btn_type = ( ! empty( $_id ) ) ? 'edit' : 'pay';
					} else {
						$btn_type = ( ! empty( $_id ) ) ? 'edit' : 'add';
					}

					$btn_type = ( ! empty( get_post_meta( $_id, 'pay_per_listing', true ) ) ) ? 'edit-ppl' : $btn_type;
					?>
					<!-- <li class="btn-add-edit"> !-->
					<?php if ( $post_id !== 1755 ) : ?>
					<li class="btn-add-edit">
						<button type="submit" class="heading-font enabled" data-load="<?php echo esc_attr( $btn_type ); ?>"
							<?php
							if ( empty( $_id ) ) {
								echo 'data-toggle="tooltip" data-placement="top" title="' . esc_html__( 'Add a Listing using Free or Paid Plan limits', 'motors-elementor-widgets' ) . '"';
							}
							?>
						>
							<?php if ( ! empty( $_id ) ) : ?>
								<i class="stm-service-icon-add_check"></i><?php esc_html_e( 'Edit Listing', 'motors-elementor-widgets' ); ?>
							<?php else : ?>

								<i class="stm-service-icon-add_check"></i><?php esc_html_e( 'OPRET KØRETØJ', 'motors-elementor-widgets' ); ?>

							<?php endif; ?>
						</button>
						<span class="stm-add-a-car-loader add"><i class="stm-icon-load1"></i></span>
					</li>
				<?php else: ?>
				<?php if ( ! empty( $_id ) ) : ?>
				<li class="btn-add-edit">
						<button type="submit" class="heading-font enabled" data-load="<?php echo esc_attr( $btn_type ); ?>">
								<i class="stm-service-icon-add_check"></i><?php esc_html_e( 'Edit Listing', 'motors-elementor-widgets' ); ?>
						</button>
						<span class="stm-add-a-car-loader add"><i class="stm-icon-load1"></i></span>
				</li>
				<?php endif; ?>
				<?php endif; ?>
				<?php endif; ?>
				<?php if ( $dealer_ppl && empty( $_id ) ) : ?><br/><br/>
					<li class="btn-ppl">
						<button type="submit" class="heading-font enabled"
								data-load="pay"
							<?php
							if ( empty( $_id ) ) {
								echo 'data-toggle="tooltip" data-placement="top" title="' . esc_html__( 'OPRET KØRETØJ', 'motors-child' ) . '"';
							}
							?>
						>
							<i class="stm-service-icon-payment_listing"></i><?php esc_html_e( 'OPRET KØRETØJ', 'motors-child' ); ?>
						</button>
						<span class="stm-add-a-car-loader pay"><i class="stm-icon-load1"></i></span>
					</li>
				<?php endif; ?>
			</ul>

			<div class="right-info">
				<a target="_blank" href="<?php echo esc_url( add_query_arg( array( 'view-myself' => 1 ), get_author_posts_url( $user_id ) ) ); ?>">
					<i class="fas fa-external-link-alt"></i><?php esc_html_e( 'Vis min profil', 'motors-elementor-widgets' ); ?>
				</a>
				<div class="stm_logout">
					<a href="#"><?php esc_html_e( 'Log ud', 'motors-elementor-widgets' ); ?></a>
					<?php esc_html_e( 'for, at logge ind med anden konto', 'motors-elementor-widgets' ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
endif;
