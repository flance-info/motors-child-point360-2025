/*Butterbean fields*/
( function($) {


    var currentTarget = '';

    /*Multiselect*/
    /*
    butterbean.views.register_control( 'multiselect', {

        // Adds custom events.
        events : {
            'keydown .stm-multiselect-wrapper .stm_add_new_optionale input'    : 'preventsubmit',
            'click .stm-multiselect-wrapper .fa-plus'    : 'addfield',
            'click .stm-multiselect-wrapper .fa-plus'    : 'addfield',
        },

        ready: function() {
            jQuery("#butterbean-control-stm_rental_office").find('.stm_add_new_optionale').hide();
        },

        preventsubmit: function(e) {
            if( (event.keyCode == 13) ) {
                event.preventDefault();
                this.addfield(e);

                jQuery('.stm_checkbox_adder').focus();

                return false;
            }
        },

        addfield: function(m) {
            var $ = jQuery;
            var $input = $(m.currentTarget).closest('.stm_add_new_inner').find('input');
            var inputVal = $input.val();
            var $preloader = $input.closest('.stm_add_new_inner').find('i');

		    if(inputVal !== '') {
                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    context: this,
                    data: 'term=' + inputVal + '&category=' + this.model.attributes.name + '&action=stm_listings_add_category_in',
                    beforeSend: function () {
                        $input.closest('.stm-multiselect-wrapper').addClass('stm_loading');
                        $preloader.addClass('fa-pulse fa-spinner');
                    },
                    complete: $.proxy(function(data) {
                        data = data.responseJSON;
                        $input.closest('.stm-multiselect-wrapper').removeClass('stm_loading');
                        $input.val('');
                        $preloader.removeClass('fa-pulse fa-spinner');
						jQuery(this.el).find('select').multiSelect('addOption', { value: data.slug, text: data.name});
						jQuery(this.el).find('select').multiSelect('select', [data.slug]);
                    })
                })
            }
        }

    } );
    */


} )(jQuery);

(function($) {

    $(document).ready(function () {
        jQuery('.stm-multiselect').multiSelect({
			'keepOrder' : true
		});
    });

    $(window).load(function(){

    });
})(jQuery);