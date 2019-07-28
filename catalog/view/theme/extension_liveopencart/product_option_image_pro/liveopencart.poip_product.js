//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

function getPoipProduct() {

	var poip_product = {
		
		proxied : false,
		custom_methods : {},
		custom_data : {},
		$container : false,
		
		product_option_ids : [],
		images : [],
		images_by_povs: [],
		module_settings : {},
		options_settings : {},
		theme_settings : {},
		default_image_title : '',
		
		option_prefix : "option",
		option_prefix_length : 0,
		main_image_default_src : '', 	// std_src
		main_image_default_href : '', // std_href
		
		timers : {
			update_images_on_option_change: 0,
		},
		works : {
			update_images_on_option_change: false,
			set_visible_images : false,
		},
		poip_ov : false,
		
	
		init : function(params) {
			
			poip_common.each(poip_product, function(setting_val, setting_key){
				if ( typeof(params[setting_key]) != 'undefined' && typeof(setting_val) != 'function' ) {
					poip_product[setting_key] = params[setting_key];
				}
			});
			
			if ( !poip_product.$container ) {
				poip_product.$container = poip_product.getDefaultContainer();
			}
			poip_product.$container.data('poip_product', poip_product);
			
			poip_product.option_prefix_length = poip_product.option_prefix.length;
			
			poip_product.main_image_default_src 	= poip_product.getMainImage().attr('src');
			poip_product.main_image_default_href 	= poip_product.getMainImage().closest('a').attr('href');
			
			poip_product.getContainerOfOptions().on('change', poip_product.getSelectorOfOptions(), function(e){
				poip_product.eventOptionValueChange(e, $(this));
			});
			
			if ( poip_product.product_option_ids.length ) {
				if ( !poip_product.setDefaultOptionsByURL() ) {
					poip_product.updateImagesOnProductOptionChange(poip_product.product_option_ids[0]);
				}
			} else { // no product option images
				poip_product.initActionWithNoOptionImages();
				
			}
		},
		
		initActionWithNoOptionImages : function(svi_called){ // no product option images
			if ( !svi_called ) {
				poip_product.setVisibleImages( poip_product.getAdditionalImagesToDisplay() );
			}
			if ( poip_product.works.set_visible_images ) {
				setTimeout(function(){
					poip_product.initActionWithNoOptionImages(true);
				}, 10);
				return;
			}
			poip_product.updateImageAdditionalMouseOver();
			poip_product.updatePopupImages();
		},
		
		getDefaultContainer : function(){
			return $('body');
		},
		
		getElement : function(selector){
			if ( selector.indexOf(' ') == -1 && poip_product.$container.is(selector) ) {
				return poip_product.$container;
			} else {
				return poip_product.$container.find(selector);
			}
		},
		
		getContainerOfOptions : function(){
			return poip_product.getElement('#product');
		},
		
		getSelectorOfOptions : function(select_addition, radio_addition, checkbox_addition){
			
			select_addition 	= select_addition || '';
			radio_addition 		= radio_addition || '';
			checkbox_addition = checkbox_addition || '';
			
			var selector = '';
			selector+= 'select[name^="'+poip_product.option_prefix+'["]'+select_addition;
			selector+= ', input:radio[name^="'+poip_product.option_prefix+'["]'+radio_addition;
			selector+= ', input:checkbox[name^="'+poip_product.option_prefix+'["]'+checkbox_addition;
			return selector;
		},
		
		getSelectorOfOptionsChecked : function(){
			return poip_product.getSelectorOfOptions('', ':checked', ':checked');
		},
		
		getProductOptionIdByName : function(name) {
			return name.substr(poip_product.option_prefix_length+1, name.indexOf(']')-(poip_product.option_prefix_length+1) );
		},
		
		eventOptionValueChange : function( event, $option_element ) { // changeVisibleImages
		
			var product_option_id = poip_product.getProductOptionIdByName( $option_element.attr('name') );
			
			if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
				poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element);
			}
			
		},
		
		updateImagesOnProductOptionChange : function(product_option_id, $option_element, param_visible_images) { // $option_element - optional
			
			clearTimeout( poip_product.timers.update_images_on_option_change );
			
			//if ( poip_product.works.update_images_on_option_change || poip_product.works.set_visible_images ) {
			if ( (!param_visible_images && poip_product.works.update_images_on_option_change) || poip_product.works.set_visible_images ) {
				
				if ( param_visible_images ) {
					clearTimeout( poip_product.timers.update_images_on_option_change_second_stage );
					poip_product.timers.update_images_on_option_change_second_stage = setTimeout(function(){
						poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element, param_visible_images);
					},10);
				} else {
					poip_product.timers.update_images_on_option_change = setTimeout(function(){
						poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element, param_visible_images);
					},10);
				}	
				return;
			}
			
			poip_product.works.update_images_on_option_change = true;
			
			if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
				
				if ( !param_visible_images ) {
					
					let visible_additional_images = poip_product.updateAdditionalImages(product_option_id);
					
					// specific call to wait for possible delays in updateAdditionalImages (control by poip_product.works.set_visible_images)
					poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element, visible_additional_images); 
					return;
				
				} else {
          
          let visible_additional_images = poip_product.updateAdditionalImages(product_option_id);
          
          // if list of visible images changed - restart updating (it can be changed with no onchange event, for example Journal3 set first option values selected on page loading)
          if ( visible_additional_images.toString() != param_visible_images.toString() ) {
            poip_product.works.update_images_on_option_change = false;
            poip_product.updateImagesOnProductOptionChange(product_option_id, $option_element);
            return;
          }
          
          poip_product.updateImageAdditionalMouseOver();
          poip_product.updatePopupImages();
          poip_product.updateMainImage(product_option_id, param_visible_images);
          poip_product.updateImagesBelowOption(product_option_id, $option_element);
          poip_product.updateDependentThumbnails(param_visible_images);
        
				}
			}
			
			poip_product.works.update_images_on_option_change = false;
		},
		
		getSelectedProductOptionValueIds : function(product_option_id, p_product_options) { // getSelectedOptionValues
	
			var product_options = p_product_options || poip_product.getSelectedProductOptions();
			var values = [];
			
			if ( product_option_id ) {
				if ( typeof(product_options[product_option_id]) != 'undefined' ) {
					values = product_options[product_option_id];
				}
			} else {
				
				poip_common.each(product_options, function(pov_ids){
					values = poip_common.getConcatenationOfArraysUnique(values, pov_ids);
				});
			}
			return values;
		},
		
		getSelectedProductOptions : function(){
			var product_options = {};
			poip_product.getElement( poip_product.getSelectorOfOptionsChecked() ).each(function () {
				var product_option_id = poip_product.getProductOptionIdByName($(this).attr('name'));
				
				if ( $(this).val() ) {
					if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
						if ( typeof(product_options[product_option_id]) == 'undefined' ) {
							product_options[product_option_id] = [];
						}
						product_options[product_option_id].push($(this).val());
					}
				}
			});
			return product_options;
		},
	
		getBasicVisibleAdditionalImages : function() { // additional images (incliding the main image, if enabled)
			
			var images = [];
			poip_common.each(poip_product.images, function(poip_img){
				if ( typeof(poip_img.product_image_id) != 'undefined' ) { // main or additional image
					poip_common.addToArrayIfNotExists(poip_img.popup, images);
				}
			});
			return images;
			
		},
	
		getBasicImagesForMainImage : function() { // ???
			
			var images_for_main_image = poip_product.getBasicVisibleAdditionalImages(true);
			if ( poip_product.std_href ) {
				if ( $.inArray(poip_product.std_href, images_for_main_image) == -1 ) {
					images_for_main_image = [poip_product.std_href].concat(images_for_main_image);
				}
			}
			return images_for_main_image;
		},
		
		getImagesForProductOptionValueIds : function(pov_ids) {
			
			var images = [];
			poip_common.each(poip_product.images, function(poip_img){
				if ( poip_img.product_option_value_id ) {
					if ( poip_common.existsIntersectionOfArrays(poip_img.product_option_value_id, pov_ids) ) {
						poip_common.addToArrayIfNotExists(poip_img.popup, images);
					}
				}
			});
			return images;
		},
		
		getImagesForProductOption : function(product_option_id) {
			var images = [];
			poip_common.each(poip_product.images, function(poip_img){
				if ( poip_img.product_option_id && $.inArray(product_option_id, poip_img.product_option_id) != -1 ) {
					poip_common.addToArrayIfNotExists(poip_img.popup, images);
				}
			});
			return images;
		},
		
		getImagesForNotSelectedProductOption : function(product_option_id) {
			
			var images = [];
			poip_common.each(poip_product.images, function(poip_img){
				if ( poip_img.product_option_value_id ) {
					// -product_option_id is used for images checked to be shown when option is not selected
					if ( $.inArray(-product_option_id, poip_img.product_option_value_id) != -1 ) {
						poip_common.addToArrayIfNotExists(poip_img.popup, images);
					}
				}
			});
			return images;
		},
		
		getImagesNotLinkedToProductOption : function(product_option_id) {
			
			var images = [];
			poip_common.each(poip_product.images, function(poip_img){
				if ( poip_img.product_option_value_id ) {
					if ( !poip_img.product_option_id || $.inArray(product_option_id, poip_img.product_option_id) == -1 ) {
						poip_common.addToArrayIfNotExists(poip_img.popup, images);
					}
				}
			});
			return images;
		},
		
		updatePopupImages : function() {
			
			if ( poip_product.getElement('li.image-additional').length ) { // default
				
				if ( typeof($.magnificPopup) != 'undefined' ) {
					poip_product.getElement('.thumbnails').magnificPopup({
						type:'image',
						delegate: '.image-additional a:visible',
						gallery: {
							enabled:true
						}
					});
				}
				
				poip_product.getMainImage().off('click');
				poip_product.getMainImage().on('click', function(event) {
					poip_product.eventMainImgClick(event, this); }
				);
				if ( poip_product.getMainImage().closest('a').length ) {
					poip_product.getMainImage().closest('a').off('click').on('click', function(event){
						event.preventDefault();
						poip_product.getMainImage().click();
					});
				}
			}
		},
		
		eventMainImgClick : function(event, element) { // ???
			event.preventDefault();
			event.stopPropagation();
			
			var main_href = $(element).parent().attr('href');
			var $additional_image = poip_product.getAdditionalImagesBlock().find('a[href="'+main_href+'"]');
			
			if ( $additional_image.length )	{
				$additional_image.click();
			}
		},
		
		sortImagesBySelectedOptions : function(p_images) {
			
			var images = [];
			
			if ( poip_product.module_settings['options_images_edit'] == 1 ) { // use basic sort order (set on the Image tab)
				
				poip_common.each(poip_product.images, function(poip_img){
					if ( $.inArray(poip_img['popup'], p_images) != -1 ) {
						poip_common.addToArrayIfNotExists(poip_img.popup, images);
					}
				});
				
			} else { // use option sort order (from the Option tab)
			
				// standard/basic images first
				images = poip_common.getIntersectionOfArrays( poip_product.getBasicVisibleAdditionalImages(), p_images);
			
				var pov_ids = poip_product.getSelectedProductOptionValueIds();
				
				poip_common.each(pov_ids, function(pov_id){
				
					if (poip_product.images_by_povs && poip_product.images_by_povs[pov_id] && poip_product.images_by_povs[pov_id].length) {
						poip_common.each(poip_product.images_by_povs[pov_id], function(poip_img_by_pov){
						
							var poip_img = poip_product.getImageBySrc(poip_img_by_pov['image'],'image');
							if ( poip_img && $.inArray(poip_img.popup, p_images) != -1) {
								poip_common.addToArrayIfNotExists(poip_img.popup, images);
							}
						});
					}
				});
				
			}
			
			for (var i in p_images) {
				if ( !p_images.hasOwnProperty(i) ) continue;
			
				if ( $.inArray(p_images[i], images) == -1 ) {
					images.push(p_images[i]);
				}
			}
		
			return images;
		},
		
		getCurrentlyVisibleImages : function(){
			images = [];
			poip_product.getAdditionalImagesBlock().find('a:visible').each(function(){
				var href = $(this).attr('href');
				if ( href ) {
					images.push(href);
				}
			});
			return images;
		},
		
		// fn_svi
		setVisibleImages : function(images) {
			
			poip_product.works.set_visible_images = true;
			
			var currently_visible_images = poip_product.getCurrentlyVisibleImages();
			if ( currently_visible_images.toString() != images.toString() ) {
				var $elements_to_remove = poip_product.getAdditionalImagesBlock();
			
				var html = '';
				poip_common.each(images, function(image){
					
					var poip_img = poip_product.getImageBySrc(image, 'popup');
					var title = poip_img.title || poip_product.default_image_title;
					html+= '<li class="image-additional" >';
					
					html+= '<a class="thumbnail" href="'+image+'" title="'+title+'"> <img src="'+poip_img.thumb+'" title="'+title+'" alt="'+title+'"></a>';
					html+= '</li>';
					
				});
				
				$elements_to_remove.remove();
				poip_product.getElement('.thumbnails').append(html);
				
			}
			
			poip_product.works.set_visible_images = false;
		},
		
		getDocumentReadyState : function() {
			return document.readyState == 'complete';
		},
		
		/*
		updateZoomImage : function(img_click, call_cnt) { // ???
			
			if ( poip_product.update_zoom_image_timer_id ) {
				clearTimeout(poip_product.update_zoom_image_timer_id);
			}
		
			if ( !poip_product.getDocumentReadyState() ) {
				if ( typeof(call_cnt) == 'undefined' ) {
					call_cnt = 1;
				}  
				if ( call_cnt <= 100 ) {
					poip_product.update_zoom_image_timer_id = setTimeout(function(){
						poip_product.updateZoomImage(img_click, call_cnt+1)
					}, 100);
				}
				return;
			}
	
		}
		*/
		
		setMainImage : function(image) { // // updateZoomImage should be here too
			
			var poip_img = poip_product.getImageBySrc(image, 'popup', 'main');
			
			poip_product.getMainImage().attr('src', poip_img.main);
			poip_product.getMainImage().closest('a').attr('href', poip_img.popup);
	
		},
		
		recreateContainerOfImagesBelowOption : function(product_option_id, images, $p_option_element) {
			
			var $option_element = $p_option_element || poip_product.getElement(poip_product.getSelectorOfOptions()).filter('[name*="['+product_option_id+']"]').first();
			var below_container_id = '#option-images-'+product_option_id;
			
			poip_product.getElement(below_container_id).remove();
			if ( images.length ) {
				
				var html = '';
				poip_common.each(images, function(image){
					var poip_img = poip_product.getImageBySrc(image, 'popup');
					html+= '<a href="'+poip_img['popup']+'" class="image-additional" style="margin: 5px;"><img src="'+poip_img['thumb']+'" ></a>';
				});
			
				$option_element.parent().append('<div id="option-images-'+product_option_id+'" style="margin-top: 10px;">'+html+'</div>');
				
				if ( poip_product.getElement(below_container_id+' a').length ) {
					if ( $.magnificPopup ) {
						poip_product.getElement(below_container_id).magnificPopup({
							type:'image',
							delegate: 'a',
							gallery: {
								enabled:true
							}
						});
					}
				}
			}
		},
		
		updateImagesBelowOption : function(product_option_id, $option_element) {
			
			if ( poip_product.options_settings[product_option_id]['img_option'] ) {
				var images = poip_product.getImagesForProductOptionValueIds( poip_product.getSelectedProductOptionValueIds(product_option_id) );
				poip_product.recreateContainerOfImagesBelowOption(product_option_id, images, $option_element);
			}
		},
		
		getProductOptionImagesToDisplay(product_option_id, p_selected_product_options) {
			
			var selected_product_options = p_selected_product_options || poip_product.getSelectedProductOptions();
			var images = [];
			if ( typeof(selected_product_options[product_option_id]) == 'undefined' ) { // no selected values
				images = poip_product.getImagesForNotSelectedProductOption(product_option_id);
			} else { // has selected values
				images = poip_product.getImagesForProductOptionValueIds( selected_product_options[product_option_id] );
			}
			return images;
		},
		
		getAdditionalImagesToDisplay : function(){
			
			var basic_visible_images_initial = poip_product.getBasicVisibleAdditionalImages();
			var basic_visible_images = basic_visible_images_initial.slice();
			
			var selected_product_options = poip_product.getSelectedProductOptions();
			
			var images_to_filter_all_images			= false;
			var images_to_filter_option_images	= false;
			var option_images_add_to_additional = [];
			
			poip_common.each(poip_product.product_option_ids, function(product_option_id){
				var product_option_settings = poip_product.options_settings[product_option_id];
				
				var current_option_images_add_to_additional = [];
				if ( product_option_settings.img_use == 1 ) {
					current_option_images_add_to_additional = poip_product.getImagesForProductOption(product_option_id);
					
				} else if ( product_option_settings.img_use == 2 ) {
					current_option_images_add_to_additional = poip_product.getProductOptionImagesToDisplay(product_option_id, selected_product_options);
				}
				
				option_images_add_to_additional = poip_common.getConcatenationOfArraysUnique(option_images_add_to_additional, current_option_images_add_to_additional);
				
				current_images_to_filter_images = poip_product.getProductOptionImagesToDisplay(product_option_id, selected_product_options);
				if ( current_images_to_filter_images.length ) {
					
					if ( product_option_settings.img_limit == 1 ) { // filter all additionail images
						
						if ( images_to_filter_all_images === false ) {
							images_to_filter_all_images = current_images_to_filter_images;
						} else {
							images_to_filter_all_images = poip_common.getIntersectionOfArrays(images_to_filter_all_images, current_images_to_filter_images);
						}
						
					} else if ( product_option_settings.img_limit == 2 ) { // filter only option images
						if ( images_to_filter_option_images === false ) {
							images_to_filter_option_images = current_images_to_filter_images;
						} else {
							images_to_filter_option_images = poip_common.getIntersectionOfArrays(images_to_filter_option_images, current_images_to_filter_images);
						}
					}
				}
			});
			
			if ( images_to_filter_all_images ) {
				basic_visible_images 				= poip_common.getIntersectionOfArrays(basic_visible_images, images_to_filter_all_images);
				option_images_add_to_additional = poip_common.getIntersectionOfArrays(option_images_add_to_additional, images_to_filter_all_images);
			}
			if ( images_to_filter_option_images ) {
				option_images_add_to_additional = poip_common.getIntersectionOfArrays(option_images_add_to_additional, images_to_filter_option_images);
			}
			
			var all_visible_images = poip_common.getConcatenationOfArraysUnique(basic_visible_images, option_images_add_to_additional);
			
			if ( !all_visible_images.length ) {
				all_visible_images = basic_visible_images_initial.slice();
			}
			
			return poip_product.sortImagesBySelectedOptions(all_visible_images);
		},
		
		updateAdditionalImages : function(product_option_id) { // changeAvailableImages
			
			if ($.inArray(product_option_id, poip_product.product_option_ids)==-1) {
				return;
			}
			
			var additional_images_to_display = poip_product.getAdditionalImagesToDisplay();
			poip_product.setVisibleImages( additional_images_to_display );
			
			return additional_images_to_display;
		},
		
		updateMainImage : function(product_option_id, p_visible_additional_images) {
			
			if ( $.inArray(product_option_id, poip_product.product_option_ids) != -1 ) {
				if ( poip_product.options_settings[product_option_id].img_change ) {
					var visible_additional_images = p_visible_additional_images || poip_product.getAdditionalImagesToDisplay();
					var selected_values = poip_product.getSelectedProductOptionValueIds(product_option_id);
					var image_to_display = poip_product.main_image_default_src;
					if ( selected_values.length ) {
						
						var current_option_images = poip_product.getImagesForProductOptionValueIds(selected_values);
						var current_option_visible_images = poip_common.getIntersectionOfArrays(visible_additional_images, current_option_images);
						if ( current_option_visible_images.length ) {
							image_to_display = current_option_visible_images[0];
						} else if ( current_option_images.length ) {
							image_to_display = current_option_images[0];
						}
						
					} else {
						if ( visible_additional_images.length ) {
							image_to_display = visible_additional_images[0];
						}
					}
					poip_product.setMainImage(image_to_display);
				}
			}
		},
		
		
		/*
		getProductOptionImages : function(product_option_id) {
			
			var images = [];
			
			for (var product_option_value_id in poip_product.images_by_povs) {
				for (var i in poip_product.images_by_povs[product_option_value_id]) {
					if (poip_product.images_by_povs[product_option_value_id][i]['product_option_id'] == product_option_id) {
						images.push(poip_product.images_by_povs[product_option_value_id][i]['image']);
					}
				}	
			}
	
			return images;
		},
		*/
		
		getProductOptionValueImages : function(product_option_value_id) {
			
			var images = [];
			poip_common.each(poip_product.images, function(poip_img){
				if ( poip_img.product_option_value_id && $.inArray(product_option_value_id, poip_img.product_option_value_id) !=-1) {
					poip_common.addToArrayIfNotExists(poip_img.popup, images);
				}
			});
			return images;
		},
		
		getImageSrc : function(image, src) {
			
			for (var i in poip_product.images) {
				if (poip_product.images[i].image == image) {
					return poip_product.images[i][src];
				}
			}
			return '';
		},
		
		getImageBySrc : function(image, src1, src2, src3, src4) {
			
			if ( !src1 && !src2 && !src3 && !src4 ) {
				src1 = 'popup';
				src2 = 'main';
				src3 = 'thumb';
				src4 = 'option_thumb';
			}
			
			let poip_img_found = '';
			poip_common.each(poip_product.images, function(poip_img){
			//for (var i in poip_product.images) {
				//var poip_img = poip_product.images[i];
				if ( (src1 && poip_img[src1] == image) || (src2 && poip_img[src2] == image) || (src3 && poip_img[src3] == image) || (src4 && poip_img[src4] == image) ) {
					poip_img_found = poip_img;
					return false;
				}
			});
			return poip_img_found;
		},
    
		getImagesBySrc : function(images, src1, src2, src3, src4) {
		  let poip_imgs = [];
		  poip_common.each(images, function(image){
			let poip_img = poip_product.getImageBySrc(image, src1, src2, src3, src4);
			if (poip_img) {
			  poip_imgs.push(poip_img);
			}
		  });
		  return poip_imgs;
		},
		
		getSrcImagesBySrc : function(src, images, src1, src2, src3, src4) {
			let poip_imgs = poip_product.getImagesBySrc(images, src1, src2, src3, src4);
			let src_images = [];
			poip_common.each(poip_imgs, function(poip_img){
				src_images.push(poip_img[src]);
			});
			return src_images;
		},
		
		getProductOptionValueIds(product_option_id) {
			var values = [];
			poip_product.getElement( poip_product.getSelectorOfOptions() ).filter(':radio, :checkbox').filter('[name*="['+product_option_id+']"]').each(function(){
				values.push( $(this).val() );
			});
			return values;
		},
		
		getProductOptionIdsDependentByImages(product_option_id) {
			
			var po_ids = [];
			poip_common.each( poip_product.images, function(poip_img){
				if ( poip_img.product_option_id && poip_img.product_option_id.length > 1 && $.inArray(product_option_id, poip_img.product_option_id) != -1 ) {
					poip_common.each(poip_img.product_option_id, function(current_po_id){
						if ( current_po_id != product_option_id ) {
							poip_common.addToArrayIfNotExists(current_po_id, po_ids);
						}
					});
				}
			});
			return po_ids;
		},
		
		getImagesRelevantToProductOptions : function(product_options){
			var images = false;
			
			poip_common.each(product_options, function(pov_ids){
				
				var current_images = poip_product.getImagesForProductOptionValueIds(pov_ids);
				if ( images === false ) {
					images = current_images;
				} else {
					images = poip_common.getIntersectionOfArrays(images, current_images);
				}
				
			});
			
			return images;
		},
		
		setVisibleProductOptionValueThumb : function(product_option_value_id, thumb){
			
			var $element = poip_product.getElement('[value="'+product_option_value_id+'"]').filter(':radio, :checkbox'); // usually the necessary element is already received by value, so this way works a bit faster
			//var $element = poip_product.getElement(':radio[value="'+product_option_value_id+'"], :checkbox[value="'+product_option_value_id+'"]');
			if ( $element.next().is('img') ) {
				$element.next().attr('src', thumb);
			}
			
		},
		
		updateDependentThumbnails : function(visible_additional_images) {
			
			var common_images_for_selected_options = false;
			poip_common.each(poip_product.product_option_ids, function(product_option_id){
	
				var product_option_settings = poip_product.options_settings[product_option_id];
	
				if ( product_option_settings.dependent_thumbnails ) {
	
					var pov_ids = poip_product.getProductOptionValueIds(product_option_id);
	
					if ( pov_ids.length ) {
						
						var dependend_product_option_ids = poip_product.getProductOptionIdsDependentByImages(product_option_id);
						var selected_product_options = poip_product.getSelectedProductOptions();
						
						
						
						var selected_dependent_product_options = {};
						poip_common.each(dependend_product_option_ids, function(dependent_po_id){
							if ( selected_product_options[dependent_po_id] ) {
								selected_dependent_product_options[dependent_po_id] = selected_product_options[dependent_po_id];
							}
						});
						
						var images_of_dependend_product_options = poip_product.getImagesRelevantToProductOptions(selected_dependent_product_options);
						
						poip_common.each(pov_ids, function(product_option_value_id){
							var pov_images 	= poip_product.getProductOptionValueImages(product_option_value_id);
							var pov_image 	= pov_images[0];
							
							if ( pov_images.length ) {
	
								var pov_images_visible = poip_common.getIntersectionOfArrays(visible_additional_images, pov_images);
								if ( pov_images_visible.length ) {
									pov_image = pov_images_visible[0];
									
								} else if ( images_of_dependend_product_options.length ) {
	
									var pov_images_common = poip_common.getIntersectionOfArrays(pov_images, images_of_dependend_product_options);
									if ( pov_images_common && pov_images_common.length ) {
										pov_image = pov_images_common[0];
									}
								}
							}
							if ( pov_image ) {
								var poip_img = poip_product.getImageBySrc(pov_image, 'popup');
								poip_product.setVisibleProductOptionValueThumb(product_option_value_id, poip_img.option_thumb);
							}
						});
					}
				}
			});
			
		},
		
		// return IMG element relevant to main image
		getMainImage : function() {
			return poip_product.getElement('ul.thumbnails li').not('.image-additional').find('a img');
		},
		
		// returns element/elements (div, ul, li etc, depend on theme) containing links to additional images (а)
		getAdditionalImagesBlock : function() {
			
			return poip_product.getElement('li.image-additional');
			/*
			if ( $('li.image-additional').length ) { // OC 2.0 default
				return $('li.image-additional');
			}
			
			if ( !$('div.image-additional').length ) {
				$('div.product-info div.image').after('<div class="image-additional"></div>');
			}
			*/
			
		
		},
		
		hrefIsVideo : function(href) {
			
			if ( href ) {
				if ( href.indexOf('https://www.youtube.com')==0 || href.indexOf('http://www.youtube.com')==0
				|| href.indexOf('https://youtube.com')==0 || href.indexOf('http://youtube.com')==0
				|| href.indexOf('https://www.vimeo.com')==0 || href.indexOf('http://www.vimeo.com')==0
				|| href.indexOf('https://vimeo.com')==0 || href.indexOf('http://vimeo.com')==0
				|| href.indexOf('www.youtube.com')==0
				|| href.indexOf('youtube.com')==0
				|| href.indexOf('//www.youtube.com')==0
				|| href.indexOf('//youtube.com')==0
				|| href.indexOf('www.vimeo.com')==0
				|| href.indexOf('vimeo.com')==0 ) {
					return true;
				}
			}
			return false;
		},
		
		getAdditionalImageSrc : function($element, attr_name) {
			
			var href = '';
			if ( attr_name ) {
				href = $element.attr(attr_name);
			} else {
				if ( $element.is('img') ) {
					href = $element.attr('src');
				} else {
					href = $element.attr('href');
				}
			}
			return href;
		},
		
		eventAdditionalImageMouseover : function(event, $element) {
      
			var image = poip_product.getAdditionalImageSrc($element);
			
			if ( image ) {
				if ( poip_product.hrefIsVideo(image) ) {
					return;
				}
				poip_product.setMainImage(image);
			}
		},
		
		updateImageAdditionalMouseOver : function() {
			if ( poip_product.module_settings.img_hover ) {
				poip_product.getAdditionalImagesBlock().find('a').off('mouseover');
				poip_product.getAdditionalImagesBlock().find('a').on('mouseover', function(e){
					poip_product.eventAdditionalImageMouseover(e, $(this));
				});
			}
		},
		
		setDefaultOptionsByURL : function() {
			
      let pov_ids = poip_product.getDefaultOptionValuesToSet();
      
      if ( pov_ids.length ) {
        poip_product.setDefaultOptionValues(pov_ids);
        return true;
      }
      
			//var result = false;
			//if (poip_product.poip_ov) {
			//	result = result || poip_product.setProductOptionValue(poip_product.poip_ov);
			//}
			//
			//// for Yandex sync module by Toporchillo
			//var hash = window.location.hash;
			//if (hash) {
			//	var hashpart = hash.split('#');
			//	var hashvals = hashpart[1].split('-');
			//	for (i=0; i<hashvals.length; i++) {
			//		if ( !hashvals.hasOwnProperty(i) ) continue;
			//		
			//		result = result || poip_product.setProductOptionValue(hashvals[i]);
			//		
			//	}
			//}
			//return result;
		},
    
    setDefaultOptionValues : function(pov_ids) {
      poip_common.each(pov_ids, function(pov_id){
        poip_product.setProductOptionValue(pov_id);
      });
    },
    
    getDefaultOptionValuesToSet : function(){
		let pov_ids = [];
		if (poip_product.poip_ov) {
		  pov_ids.push(poip_product.poip_ov);
		}
		
		// for Yandex sync module by Toporchillo
		var hash = window.location.hash;
		if (hash) {
			var hashpart = hash.split('#');
			var hashvals = hashpart[1].split('-');
			for (i=0; i<hashvals.length; i++) {
				if ( !hashvals.hasOwnProperty(i) ) continue;
				  
				if ( $.inArray(hashvals[i], pov_ids) == -1 ) {
				  pov_ids.push(hashvals[i]);
				}
			}
		}
		return pov_ids;
    },
		
		setProductOptionValue : function(value) {
		
			var $option_element = poip_product.getElement( poip_product.getSelectorOfOptions(' option') ).filter('[value="'+value+'"]:not(:disabled)');
			if ( !$option_element.length ) {
				return;
			}
			
			if ( $option_element.is('option') ) { // select
				$option_element.parent().val(value);
				$option_element.parent().trigger('change');
			} else { // radio or checkbox
				$option_element.prop('checked', true);
				$option_element.trigger('change');
			}
			return true;
		},
		
		
		externalOptionChange : function() {
			if ( poip_product.product_option_ids.length ) {
				poip_product.updateImagesOnProductOptionChange(poip_product.product_option_ids[0]);
			}
		},
		
		elevateZoomDirectChange : function(img_click, timeout, elem_img, update_src) {
		
			if ( timeout ) {
				setTimeout(function(){
					poip_product.elevateZoomDirectChange(img_click, 0, elem_img);
				}, timeout);
			} else {
				$('.zoomContainer').find('.zoomWindowContainer').find('div').css({"background-image": 'url("'+img_click+'")'});
				$('.zoomContainer').find('.zoomLens').css({"background-image": 'url("'+img_click+'")'});
			}
		},
		
		theme_adaptation : {
			getStoredImagesContainer : function($put_before_element, $carousel_items, data_key='images') {
				var selector = '[data-poip="'+data_key+'"]';
				if ( !poip_product.getElement(selector).length ) {
					$put_before_element.before('<div data-poip="'+data_key+'" style="display:none;!important"></div>');
					$carousel_items.each(function(){
						poip_product.getElement(selector).append( poip_common.getOuterHTML($(this)) );
					});
				}
				return poip_product.getElement(selector);
			},
		
			updateShouldBeProcessed : function($carousel_items, href_attr_name, images_to_check, images, counter, carousel_is_ready, ignore_image_coincidence) {
				
				if ( !poip_product.custom_data.set_visible_images_is_called ) {
					if ( !carousel_is_ready ) {
						poip_product.timers.set_visible_images = setTimeout(function(){
							poip_product.custom_methods['setVisibleImages.instead'](images, counter+1);
						}, 100);
						return false;
					}
					poip_product.custom_data.set_visible_images_is_called = true;
				} else {
				
					if ( !ignore_image_coincidence ) {
						var current_imgs = [];
						$carousel_items.each( function(){
							current_imgs.push($(this).attr(href_attr_name));
						});
						
						if ( current_imgs.toString() == images_to_check.toString() ) {
							poip_product.works.set_visible_images = false;
							return false; // nothing to change
						}
					}
				}
				
				return true;
			},
		},
	};
	
	if ( typeof(setPoipProductCustomMethods) == 'function' ) {
		setPoipProductCustomMethods(poip_product);
	}
	
	return poip_product;
}
