(function($){
        // KSK
	$(document).ready(function(){
            checkout_href = $("a.checkout-button").attr("href");
            //$('#natsenka-30').on('click', function(){ kskNatsenkaClick(); });
            $('#natsenka-30').on('click', function(e){ ksk_wc_cart_add_amount_calc(); });
            $('#user-bonus').on('click', function(e){ ksk_wc_cart_add_amount_calc(); });
            
            // Скрытие/отображение пунктов доставки
            //$('input[name=t9z_shipping_1]').on('click', function(e){ kskT9zShippingClick(e); });
            $('input[name=t9z_shipping_1]').on('click', function(e){ ksk_wc_cart_add_amount_calc(); });
            
            jQuery("#ksk-wc-proceed-to-checkout").on('click', function(e){ ksk_wc_proceed_to_checkout(e); });
            
            // Если мы в корзине
            if (jQuery("table").is(".cart")) {
                //jQuery("table.cart")
                //kol1 = jQuery("span.amount").html();
                //kol2 = kol.split(' ');
                //kol = kol[0];
                //jQuery("input[name=update_cart]").trigger( "click" );
                ksk_free_shipping_check();
            }
            
            // Оформление заказа
            if (jQuery("input").is("#place_order") && jQuery("div").is("#ksk-checkout-submit")) {
                jQuery("#place_order").appendTo("#ksk-checkout-submit");
                //jQuery("#payment_method_cod").attr("disabled", "disabled");
                //jQuery("#payment_method_robokassa").attr("disabled", "disabled");
                jQuery('#payment_method_cod').prop('checked', (jQuery('#pay-method').attr("value") == 1));
                jQuery('#payment_method_robokassa').prop('checked', (jQuery('#pay-method').attr("value") == 2));
                jQuery('#payment').hide();
            }
        });
        
	// Mobile menu
	var headerMenuWrap = $('.top-panel-menu-wrap')

	headerMenuWrap.append('<span class="top-panel-menu-toggler"/>')

	var headerMenuToggler = $('.top-panel-menu-toggler')

	headerMenuToggler.on('click', function(){
		headerMenuWrap.toggleClass('active')
	})


	// Location selector
	var locationSelectorToggle = $('.header-location-select-toggle'),
		locationSelectorParent = $('.header-location-select-wrap'),
		locationSelectorOpts = $('.header-location-select-opts li'),
		locationValue = $('.header-location-select-v')

	locationSelectorToggle.on('click',function(e){
		e.stopPropagation()

		locationSelectorParent.toggleClass('active')
	})

	locationSelectorOpts.on('click',function(){
		var item = $(this),
			itemSiblings = item.siblings(),
			itemValue = item.text()

		item.addClass('active')
		itemSiblings.removeClass('active')
		locationValue.text(itemValue)
                
                // KSK
                data_str = "shipping_city=" + itemValue;
                //alert('data_str=' + data_str);
                
                // Если мы в корзине
                if (jQuery("table").is(".cart")) {
                    data_str = data_str + "&cart=1";
                    //jQuery("#ksk_woocommerce_t9z_shipping_info").html('Подождите...Выполняется обновление способов доставки после смены города...');
                    //jQuery("#ksk_woocommerce_t9z_shipping_info").show();
                }
                
                jQuery.ajax({
                    url: "/wp-admin/admin-ajax.php?action=ksk_shipping_city_session_set",
                    type: "POST",
                    data: data_str,
                    dataType: 'json',
                    //timeout: 27000,
                    success: function(data){
                        if ((data.shipping_method != undefined) && (data.shipping_method != '')) {
                            jQuery("#shipping-amount").attr("value", data.shipping_cost);
                            jQuery("#woocommerce_t9z_shipping_settings").html(data.shipping_method);
                            // Скрытие/отображение пунктов доставки
                            //jQuery('input[name=t9z_shipping_1]').on('click', function(e){ kskT9zShippingClick(e); });
                            jQuery('input[name=t9z_shipping_1]').on('click', function(e){ ksk_wc_cart_add_amount_calc(); });
                        }
                        //alert(data);
                        ksk_wc_cart_add_amount_calc();
                    },
                    error: function(data){
                        if ((data.responseText != undefined) && (data.responseText != '')) {
                            //alert('Ошибка записи города доставки: ' + data.responseText);
                        }
                        
                        if ((data.statusText != undefined) && (data.statusText != '')) {
                            //alert('Ошибка записи города доставки: ' + data.statusText);
                        }
                    }			
                });
                
                // Если мы в корзине
                /*if (jQuery("table").is(".print-cart-table")) {
                    jQuery("#ksk_woocommerce_t9z_shipping_info").html();
                    jQuery("#ksk_woocommerce_t9z_shipping_info").hide();
                }*/
                //jQuery('input[name=t9z_shipping_1]').click();
                //ksk_wc_t9z_shipping_cart_print("shipping_city="+itemValue);
	})

	// Hide location selector
	$(document).on('click',function(){
		locationSelectorParent.removeClass('active')
	})

        // KSK
	function kskNatsenkaClick() {
            // Отмечена наценка
            if ($('#natsenka-30').is(':checked')) {
                url_add = 'natsenka-30=on';
            } else {
                url_add = '';
            }
            
            // Обновим URL
            checkout_href_new = checkout_href + '?' + url_add;
            $("a.checkout-button").attr("href", checkout_href_new);
	}
        
        $('.print-options-item').each(function(){
            var selector = $(this),
                selectorField = selector.find('div.print-options-photo-upload-image-item-num');
            
            if (selectorField != undefined) {
                selectorField.hide();
            }
        });
        // KSK
        
	// Items num selector
	$('.print-options-photo-upload-image-item-num-selector').each(function(){
		var selector = $(this),
			selectorField = selector.find('input[type="text"]'),
                                togglerId = selectorField[0].id

                        if ((togglerId.indexOf('copies_') > -1) || (togglerId.indexOf('pages_') > -1)) {
                            ksk_cart_quantity_calc(togglerId)
                        }

		selector.append('<span class="selector-minus selector" data-type="minus"/><span class="selector-plus selector" data-type="plus"/>')

		var selectorToggler = selector.find('.selector')

		selectorToggler.on('click', function(){
			var toggler = $(this),
				valueCurrent = Number( selectorField.val() ),
				toggleType = toggler.data('type')
                                
                                //alert('togglerId='+togglerId);

			if ( toggleType == 'minus' ) {
				if ( valueCurrent > 1 ) selectorField.val( valueCurrent -1 )
			}
			else {
				selectorField.val( valueCurrent +1 )
			}

			selectorField.trigger('change')
                        
                        if ((togglerId.indexOf('copies_') > -1) || (togglerId.indexOf('pages_') > -1)) {
                            ksk_cart_quantity_calc(togglerId)
                        }
		})
	})


	// QA
	var QAItemTitles = $('.qa-title')

	QAItemTitles.on('click', function(e){
		var title = $(this),
			titleParent = title.parent(),
			titleParentSiblings = titleParent.siblings()

		titleParent.toggleClass('active')
		titleParentSiblings.removeClass('active')

		e.stopPropagation()
	})


	// Popups
	popupCommonParams = {
		opacity: 0.5,
		close: '×'
	}

	$('.popup-inline-show').each(function(){
		var popupLink = $(this),
			popupTargetSelector = '#'+ popupLink.data('target-popup') +'-popup'

		popupLink.colorbox( $.extend( {}, popupCommonParams, {
			className: 'popup-inline-content',
			inline: true,
			href: popupTargetSelector
		}) )
	})


	// Change password block
	$('.lk-info-change-password').on('click', function(){
		var toggler = $(this),
			block = $('.lk-info-change-password-content')

		toggler.hide()
		block.show()
	})
})(jQuery)

function close_popup(){
	jQuery.colorbox({
		opacity: 0.5,
		close: '×',
		html: '<h3 class="popup-title">Ваш запрос успешно отправлен</h3><div class="popup-content" style="width:auto">Мы свяжемся с Вами в ближайшее время.<br/><br/>Спасибо за Ваше обращение!</div>'
	})
}

function ksk_cart_quantity_calc(id) {
    //alert('id='+id);
    if (id != '' && id != undefined) {
        name_array = id.split('_');
        qty = 0;
        i = 0;
        name1 = name_array[0] + '_' + name_array[1] + '_' + i;
        if (name_array[0] == 'copies') {
            name2 = 'pages_' + name_array[1] + '_' + i;
        } else {
            name2 = 'copies_' + name_array[1] + '_' + i;
        }
        name3 = 'copies_' + name_array[1];
        while (jQuery("input[type=text]").is("#" + name1)) {
            qty = qty + jQuery("#" + name1).val() * jQuery("#" + name2).val();
            i++;
            name1 = name_array[0] + '_' + name_array[1] + '_' + i;
            if (name_array[0] == 'copies') {
                name2 = 'pages_' + name_array[1] + '_' + i;
            } else {
                name2 = 'copies_' + name_array[1] + '_' + i;
            }
        }
        
        //if (jQuery("#" + name3).attr('value') != qty) {
            jQuery("#" + name3).attr('value', qty);
        //}
    }
}

function kskT9zShippingClick(e) {
    var id = e.target.id;
    var cost = e.target.dataset.cost;
    if (id == 't9z_shipping_1_office') {
        jQuery('div.print-cart-item-subfields').show();
    } else {
        jQuery('div.print-cart-item-subfields').hide();
    }
    
    jQuery.ajax({
        url: "/wp-admin/admin-ajax.php?action=ksk_wc_t9z_shipping_cart_calc",
        type: "POST",
        data: "shipping_cost="+cost,
        dataType: 'json',
        success: function(data){
            if ((data.total != undefined) && (data.total != '')) {
                //jQuery(".print-cart-sum").html(data.total);
            }
            if ((data.after_cart_html != undefined) && (data.after_cart_html != '')) {
                //jQuery(".cart-collaterals").html(data.after_cart_html);
            }
        },
        error: function(data){
            //jQuery("#ksk_woocommerce_t9z_shipping_info").html();
            if ((data.responseText != undefined) && (data.responseText != '')) {
                //jQuery("#ksk_woocommerce_t9z_shipping_error").html(data.responseText);
                //jQuery("#ksk_woocommerce_t9z_shipping_error").show();
            }
        }		
    });
}

function ksk_wc_t9z_shipping_cart_print(shipping_city) {
    jQuery("#ksk_woocommerce_t9z_shipping_info").html('Подождите...Выполняется обновление способов доставки после смены города...');
                
    jQuery.ajax({
        url: "/wp-admin/admin-ajax.php?action=ksk_wc_t9z_shipping_cart_print",
        type: "POST",
        data: shipping_city,
        dataType: 'json',
        success: function(data){
            if ((data.shipping_method != undefined) && (data.shipping_method != '')) {
                jQuery("#woocommerce_t9z_shipping_settings").html(data.shipping_method);
                // Скрытие/отображение пунктов доставки
                jQuery('input[name=t9z_shipping_1]').on('click', function(e){ kskT9zShippingClick(e); });
            }
            if ((data.bonus_amount != undefined) && (data.bonus_amount != '')) {
                jQuery("#bonus_amount").html(data.bonus_amount);
            }
            if ((data.total != undefined) && (data.total != '')) {
                jQuery(".print-cart-sum").html(data.total);
            }
            if ((data.after_cart_html != undefined) && (data.after_cart_html != '')) {
                jQuery(".cart-collaterals").html(data.after_cart_html);
            }
            
            jQuery("#ksk_woocommerce_t9z_shipping_info").html();
            jQuery("#ksk_woocommerce_t9z_shipping_info").hide();
        },
        error: function(data){
            jQuery("#ksk_woocommerce_t9z_shipping_info").html();
            jQuery("#ksk_woocommerce_t9z_shipping_info").hide();
            if ((data.responseText != undefined) && (data.responseText != '')) {
                jQuery("#ksk_woocommerce_t9z_shipping_error").html(data.responseText);
                jQuery("#ksk_woocommerce_t9z_shipping_error").show();
                //alert('Ошибка записи города доставки: ' + data.responseText);
            }
        }		
    });
}

function ksk_wc_cart_add_amount_calc() {
    //var id = e.target.id;
    //var cost = e.target.dataset.cost;
    ksk_free_shipping_check();
        
    d1 = jQuery("#subtotal-amount").attr("value");
    d3 = jQuery("#shipping-amount").attr("value");
    
    p2 = jQuery("#natsenka-percent").attr("value");
    p5 = jQuery("#bonus-percent").attr("value");
    
    if (jQuery('#natsenka-30').is(':checked')) {
        d2 = (d1 * p2)/100;
        jQuery('#total-amount-label').html('и наценки за срочность');
    } else {
        d2 = 0;
        jQuery('#total-amount-label').html('');
    }
    
    if (jQuery('#user-bonus').is(':checked')) {
        d6 = jQuery("#user-bonus-amount").attr("value");;
        //jQuery('#user-bonus-amount-label').html(d6);
    } else {
        d6 = 0;
        //jQuery('#user-bonus-amount-label').html('0');
    }
    
    if (jQuery('#t9z_shipping_1_office').is(':checked')) {
        jQuery('div.print-cart-item-subfields').show();
    } else {
        jQuery('div.print-cart-item-subfields').hide();
    }
    
    if (jQuery('#t9z_shipping_1_city').is(':checked')) {
        d3 = jQuery('#t9z_shipping_1_city')[0].dataset.cost;
    } else {
        d3 = 0;
    }
    
    d4 = d1*1 + d2*1 + d3*1;
    d5 = (d1 * p5)/100;
    
    if (d6 > 0) {
        if (d4 > d6) {
            d4 = d4 - d6*1;
        } else {
            d4 = 0;
        }
    }

    //alert('d1='+d1+', d2='+d2+', d3='+d3+', d4='+d4+', d5='+d5);

    jQuery("#natsenka-amount").attr("value", d2);
    jQuery("#shipping-amount").attr("value", d3);
    jQuery("#total-amount").attr("value", d4);
    jQuery("#bonus-amount").attr("value", d5);
    
    jQuery("#natsenka-30-amount").html(d2 + ' руб.');
    jQuery("#bonus_amount").html(d5);
    jQuery(".print-cart-sum").html(d4 + ' руб.');
}

function ksk_wc_proceed_to_checkout(e) {
    var id = e.target.id;
    
    if (jQuery('input[name="wpf_umf_uploads_needed"]').val() == 1) {
        e.preventDefault();
    } else {
        if (jQuery('#t9z_shipping_1_free').is(':checked')) {
            jQuery("#shipping-text-1").attr("value", jQuery('#t9z_shipping_1_free')[0].dataset.label);
            jQuery("#shipping-text-2").attr("value", "");
        }

        if (jQuery('#t9z_shipping_1_city').is(':checked')) {
            jQuery("#shipping-text-1").attr("value", jQuery('#t9z_shipping_1_city')[0].dataset.label);
            jQuery("#shipping-text-2").attr("value", "");
        }

        if (jQuery('#t9z_shipping_1_office').is(':checked')) {
            jQuery("#shipping-text-1").attr("value", jQuery('#t9z_shipping_1_office')[0].dataset.label);
            id2 = jQuery("input[name=t9z_shipping_2]").attr("value");
            jQuery("#shipping-text-2").attr("value", jQuery('#t9z_shipping_2_' + id2)[0].dataset.label);
        }

        // Сохранима новые поля формы в $_SESSION
        formData = jQuery("#ksk_wc_cart_form").serialize();
        //alert('formData = ' + formData);
        jQuery.ajax({
            url: "/wp-admin/admin-ajax.php?action=ksk_save_t9z_cart_new_field_to_session",
            type: "POST",
            data: formData,
            //timeout: 25000,
            success: function(data){
                //alert('Успешно записаны поля: ' + data);
                jQuery("#ksk_wc_cart_form").attr("action", "/checkout");
                jQuery("#ksk_wc_cart_form").submit();
            },
            error: function(data){
                if ((data.responseText != undefined) && (data.responseText != '')) {
                    alert('Ошибка записи полей: ' + data.responseText);
                }
            }
        });

        //jQuery("#ksk_wc_cart_form").attr("action", "/checkout");
        //jQuery('#ksk_wc_cart_form').append('<input type="hidden" name="proceed" value="1" />');
        //jQuery("#ksk_wc_cart_form").submit();
    }
}

function ksk_free_shipping_check() {
    // Если мы в корзине
    if (jQuery("table").is(".cart")) {
        d1 = jQuery("#subtotal-amount").attr("value");
        d2 = jQuery("#free-shipping-amount").attr("value");
        
        if (d1*1 >= d2*1) {
            //jQuery('#t9z_shipping_1_city').prop('checked', false);
            jQuery("#t9z_shipping_1_city").attr("disabled", "disabled");
            jQuery("#t9z_shipping_1_free").removeAttr("disabled");
            jQuery('#t9z_shipping_1_free').prop('checked', (jQuery('#t9z_shipping_1_office').is(':checked') != true));
       } else {
            //jQuery('#t9z_shipping_1_free').prop('checked', false);
            jQuery("#t9z_shipping_1_free").attr("disabled", "disabled");
            jQuery("#t9z_shipping_1_city").removeAttr("disabled");
            jQuery('#t9z_shipping_1_city').prop('checked', (jQuery('#t9z_shipping_1_office').is(':checked') != true));
        }
    }
}

// Desi4ik
function SelectorClick (name, idinp) {
    ksk_cart_quantity_calc(idinp);
    form = jQuery('#ksk_wc_cart_form');
    //curel=jQuery('.selector-minus').prev('input');
    curel = jQuery('#'+name);
    //alert('!'+name3+'!');

    // emulates button Update cart click
    jQuery("<input type='hidden' name='update_cart' id='update_cart' value='1'>").appendTo(form);

    // plugin flag
    jQuery("<input type='hidden' name='is_wac_ajax' id='is_wac_ajax' value='1'>").appendTo(form);

    el_qty = curel;
    matches = curel.attr('name').match(/cart\[(\w+)\]/);
    cart_item_key = matches[1];
    form.append( jQuery("<input type='hidden' name='cart_item_key' id='cart_item_key'>").val(cart_item_key) );

    // get the form data before disable button...
    formData = form.serialize();

    jQuery("input[name='update_cart']").val('Updating…').prop('disabled', true);

    jQuery("a.checkout-button.wc-forward").addClass('disabled').html('Updating…');

    jQuery.post( form.attr('action'), formData, function(resp) {
        // ajax response
        jQuery('.cart-collaterals').html(resp.html);

        el_qty.closest('.cart_item').find('.product-subtotal').html(resp.price);

        jQuery('#update_cart').remove();
        jQuery('#is_wac_ajax').remove();
        jQuery('#cart_item_key').remove();

        jQuery("input[name='update_cart']").val(resp.update_label).prop('disabled', false);

        jQuery("a.checkout-button.wc-forward").removeClass('disabled').html(resp.checkout_label);
        //alert('!'+resp.price_subtotal);
        jQuery('#subtotal-amount').attr("value", resp.price_subtotal);
        jQuery('.product-price').html(resp.special_price);

        ksk_wc_cart_add_amount_calc();

        // when changes to 0, remove the product from cart
        if ( el_qty.val() == 0 ) {
            el_qty.closest('tr').remove();
        }
    },
    'json'
    );
} 

function ChangeCount (obj) {
    ids = obj.attr('id').split('_');
    names = ids[0] +'_'+ids[1];
    obj2 = document.getElementById(obj.attr('id'));
    obj2.setAttribute('value', obj.val());
    SelectorClick(names, obj.attr('id'));
}
