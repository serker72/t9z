jQuery(document).ready(function($) {

    $('#woocommerce-t9z-shipping-boxes').sortable({

        axis: 'y',
        update: function (event, ui) {

            reset_boxes();

        }
    });

    function reset_boxes()
    {

        $('#woocommerce-t9z-shipping-container .wpf-umf-upload-box').each(function(index) {

            var new_id = index + 1;

            $(this).attr('data-id', new_id).attr('id', 'woocommerce-t9z-shipping-box-'+new_id);

            // Add new names / ids to fields

            $(this).find('input, textarea, select').each(function( index ) {

                // Name
                var input_name = String($(this).attr('name'));
                $(this).attr('name', input_name.replace(/\[(\d*)\]/g, '['+new_id+']'));

                // Id
                var input_id = String($(this).attr('id'));
                $(this).attr('id', input_id.replace(/woocommerce_t9z_shipping_(\d*)/g, 'woocommerce_t9z_shipping_'+new_id));

            });

            $(this).find('label').each(function( index ) {

                var label_for = String($(this).attr('for'));
                $(this).attr('for', label_for.replace(/woocommerce_t9z_shipping_(\d*)/g, 'woocommerce_t9z_shipping_'+new_id));

            });

        });

    }


    $('a#woocommerce-t9z-shipping-add-set').click(function(e) {

        e.preventDefault();

        var old_upload_box  =   $('#woocommerce-t9z-shipping-container .wpf-umf-upload-box').last();
        var new_upload_box  =   old_upload_box.clone();

        new_upload_box.appendTo('#woocommerce-t9z-shipping-boxes');
        reset_boxes();

    });

    $('#woocommerce-t9z-shipping-boxes').on('click', '.wpf-umf-upload-box-advanced', function(e) {

        e.preventDefault();

        var clicked_element = $(this);
        var toggle_element = $(this).parent('.wpf-umf-upload-box').find('.wpf-umf-upload-box-collapse');

        toggle_element.slideToggle(function(){

            if (toggle_element.is(':visible')) {
              clicked_element.html('<span class="dashicons dashicons-arrow-up"></span>' + uploadset_message.less_info);
            } else {
              clicked_element.html('<span class="dashicons dashicons-arrow-down"></span> '  + uploadset_message.more_info);
            }

        });

    });

    $('#woocommerce-t9z-shipping-boxes').on('click', '.wpf-umf-upload-box-delete', function(e) {

        e.preventDefault();

        var elements_count = $('#woocommerce-t9z-shipping-boxes .wpf-umf-upload-box').length;
        var clicked_element = $(this);

        if (elements_count == 1) {

            alert(uploadset_message.cant_delete_upload_box);

        } else {

            $(this).parent('.wpf-umf-upload-box').remove();
            reset_boxes();

        }


    });


});