(function($){
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
	})

	// Hide location selector
	$(document).on('click',function(){
		locationSelectorParent.removeClass('active')
	})


	// Items num selector
	$('.print-options-photo-upload-image-item-num-selector').each(function(){
		var selector = $(this),
			selectorField = selector.find('input[type="text"]')

		selector.append('<span class="selector-minus selector" data-type="minus"/><span class="selector-plus selector" data-type="plus"/>')

		var selectorToggler = selector.find('.selector')

		selectorToggler.on('click', function(){
			var toggler = $(this),
				valueCurrent = Number( selectorField.val() ),
				toggleType = toggler.data('type')

			if ( toggleType == 'minus' ) {
				if ( valueCurrent > 1 ) selectorField.val( valueCurrent -1 )
			}
			else {
				selectorField.val( valueCurrent +1 )
			}

			selectorField.trigger('change')
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