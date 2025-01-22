<?php



$current_url  = home_url( $_SERVER['REQUEST_URI'] );

$layout_param = filter_input( INPUT_GET, 'stm-layout', FILTER_SANITIZE_STRING );

$post_id      = get_the_ID();

$add_leasing  = get_post_meta( $post_id, 'stm_add_leasing', true );

$add_van      = get_post_meta( $post_id, 'stm_add_van', true );

?>



<div class="elementor-row stm-buttons">

	<?php if ( 'leasing' == $layout_param ) : ?>

		<div class="elementor-column">

			<a href="?" class="elementor-button elementor-size-md black-button">

				<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM BIL', 'motors-child' ); ?></span>

			</a>

		</div>

		<?php if ( $add_van == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=van" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM VAREBIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>



		<?php if ( $add_leasing == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=leasing_van" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM LEASING VAREBIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>

	<?php elseif ( 'van' == $layout_param ) : ?>

		<div class="elementor-column">

			<a href="?" class="elementor-button elementor-size-md black-button">

				<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM BIL', 'motors-child' ); ?></span>

			</a>

		</div>



		<?php if ( $add_leasing == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=leasing" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM LEASING BIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>

		<?php if ( $add_leasing == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=leasing_van" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM LEASING VAREBIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>

	<?php elseif ( 'leasing_van' == $layout_param ) : ?>



		<div class="elementor-column">

			<a href="?" class="elementor-button elementor-size-md black-button">

				<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM BIL', 'motors-child' ); ?></span>

			</a>

		</div>

	<?php if ( $add_van == 'yes' ) : ?>

		<div class="elementor-column">

			<a href="?stm-layout=van" class="elementor-button elementor-size-md black-button">

				<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM VAREBIL', 'motors-child' ); ?></span>

			</a>

		</div>

	<?php endif; ?>

		<?php if ( $add_leasing == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=leasing" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM LEASING BIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>

	<?php else : ?>

		<?php if ( $add_van == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=van" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM VAREBIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>

		<?php if ( $add_leasing == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=leasing" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM LEASING BIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>

		<?php if ( $add_leasing == 'yes' ) : ?>

			<div class="elementor-column">

				<a href="?stm-layout=leasing_van" class="elementor-button elementor-size-md black-button">

					<span class="elementor-button-text"><?php esc_html_e( 'VIS SOM LEASING VAREBIL', 'motors-child' ); ?></span>

				</a>

			</div>

		<?php endif; ?>

	<?php endif; ?>

</div>







