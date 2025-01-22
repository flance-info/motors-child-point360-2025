<div class="col-md-4 col-sm-6">
	<div class="stm_price_input">
		<div class="stm_label heading-font"><?php esc_html_e( 'Indtast Prisen', 'motors-elementor-widgets' ); ?>*
			(<?php echo wp_kses_post( stm_get_price_currency() ); ?>)
		</div>
		<input type="number" class="heading-font" name="stm_van_price" value="<?php echo esc_attr( $van_price ); ?>" required/>
	</div>
</div>

<div class="col-md-8 col-sm-6">
	<h4><?php esc_html_e('Pris&aelig;t Varebilen', 'motors-child') ?></h4>
	<?php if ( ! empty( $stm_title_price ) ) : ?>

	<?php endif; ?>
	<?php if ( ! empty( $stm_title_desc ) ) : ?>
		<p><?php echo wp_kses_post( $stm_title_desc ); ?></p>
	<?php endif; ?>
</div>

