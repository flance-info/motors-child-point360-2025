<div class="col-md-4 col-sm-6">
	<div class="stm_price_input">
		<div class="stm_label heading-font"><?php esc_html_e( 'Leasingydelse pr. mdr: Bil', 'motors-child' ); ?>
			(<?php echo wp_kses_post( stm_get_price_currency() ); ?>)
		</div>
		<input type="number" class="heading-font" name="stm_leasing_car_price" value="<?php echo esc_attr( $stm_leasing_car_price ); ?>" required/>
	</div>
</div>
<div class="col-md-4 col-sm-6">
	<div class="stm_price_input">
		<div class="stm_label heading-font"><?php esc_html_e( 'Leasingydelse pr. mdr: Varebil', 'motors-child' ); ?>
			(<?php echo wp_kses_post( stm_get_price_currency() ); ?>)
		</div>
		<input type="number" class="heading-font" name="stm_leasing_van_price" value="<?php echo esc_attr( $stm_leasing_van_price ); ?>" required/>
	</div>
</div>



