(function ($) {
    $(document).ready(function () {
       let prefix = 'stm-motors-woocommerce-cart';

       $( '.' + prefix + '__option--checkbox input[type="checkbox"]' ).on('click', function () {
           let $this  = $(this),
               id     = $this.val(),
               parent = $this.parents('.' + prefix + '__options'),
               nonce  = parent.find('input[name="_ajax_nonce"]').val(),
               data = new FormData();

           data.append('action', 'stm_ajax_buy_additional_options');
           data.append('listing_id', id);
           data.append('_ajax_nonce', nonce);

           if ( $this.is(':checked') ) {
               data.append('add', true);
           }
           else {
               data.append('add', false);
           }

           if ( id ) {
               $.ajax({
                   url: ajaxurl,
                   type: "POST",
                   dataType: "json",
                   processData: false,
                   contentType: false,
                   data: data,
                   success: (data) => {
                       $( '.' + prefix + '__basket .woocommerce').html( data['html'] );
                   }
               });
           }
       });

        document.querySelectorAll('.stm-motors-woocommerce-cart__column.stm-motors-woocommerce-cart__options').forEach(element => {
            if (!element.querySelector('div')) {
                element.style.display = 'none';
            }
        });

    });
})(jQuery);