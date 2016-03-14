(function($){
        // KSK
	$(document).ready(function(){
            checkout_href = $("a.checkout-button").attr("href");
            $('#natsenka-30').on('click', function(){ kskNatsenkaClick(); });
            $('input[name=t9z_shipping_1]').on('click', function(e){
                var id = e.target.id;
                if (id == 't9z_shipping_1_office') {
                    jQuery('div.print-cart-item-subfields').show();
                } else {
                    jQuery('div.print-cart-item-subfields').hide();
                }
            });
            kskNatsenkaClick();
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
                jQuery.ajax({
                    url: "/wp-admin/admin-ajax.php?action=ksk_shipping_city_session_set",
                    type: "POST",
                    data: "shipping_city="+itemValue,
                    success: function(data){
                        //alert(data);
                    },
                    error: function(data){
                        if (data.responseText !== undefined) {
                            //alert('Ошибка записи города доставки: ' + data.responseText);
                        }
                    }			
                });
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
			selectorField = selector.find('input[type="text"]')

		selector.append('<span class="selector-minus selector" data-type="minus"/><span class="selector-plus selector" data-type="plus"/>')

		var selectorToggler = selector.find('.selector')

		selectorToggler.on('click', function(){
			var toggler = $(this),
				valueCurrent = Number( selectorField.val() ),
				toggleType = toggler.data('type'),
                                togglerId = selectorField[0].id
                                
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
        
        jQuery("#" + name3).attr('value', qty);
    }
}