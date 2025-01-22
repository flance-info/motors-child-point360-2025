"use strict";

(function ($) {
	$(document).ajaxComplete(function (event, xhr, settings) {
		if (Array.isArray(settings.data))
		{
			if ((settings.url === ajaxurl && settings.data.indexOf('action=stm_custom_register') !== -1) ||
				settings.url === ajaxurl && settings.data.indexOf('action=stm_custom_login') !== -1) {
				if (xhr.status === 200 && xhr.responseJSON) {
					let data = xhr.responseJSON;
					console.log('Success response:', data);
					if (data.user_html) {
						$('.stm-login-display').show();
						$('.stm-login-hide').hide();
					}
					if (data.user_plans) {

						let userPlansSet = new Set(data.user_plans);

						$('.pricing-item').each(function () {
							let planOption = $(this).data('option');
							if (!userPlansSet.has(planOption)) {
								$(this).hide();
							}
						});
					}
				}
			}
		}
	});
})(jQuery);

(function ($) {
	$(document).on('click', '#buy-car-online-options', function (e) {
		e.preventDefault();

		var thisBtn = $(this);

		var carId = $(this).data('id');
		var price = $(this).data('price');

		var options = [];
		$('.stm-motors-woocommerce-cart__option--checkbox input[type="checkbox"]:checked').each(function () {
			options.push($(this).val());
		});


		var optionsStr = options.join(',');
		var dataString = 'car_id=' + carId + '&price=' + price + '&options=' + optionsStr + '&action=stm_ajax_buy_car_online_options&security=' + stm_security_nonce;

		$.ajax({
			url: ajaxurl,
			type: "POST",
			dataType: 'json',
			context: this,
			data: dataString,
			beforeSend: function () {
				thisBtn.addClass('buy-online-load');
			},
			success: function (data) {

				thisBtn.removeClass('buy-online-load');

				if (data.status == 'success') {
					window.location = data.redirect_url;
				}
			}
		});
	});

})(jQuery);

