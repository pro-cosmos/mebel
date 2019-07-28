//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

var poip_list = {
	
	proxied : false,
	custom_methods : {},
	module_settings : {},
	
	wait_server_answer : false,
	image_cache : {},
	url_get_images : 'index.php?route=extension/liveopencart/product_option_image_pro/getProductListImages',
	product_count : 0,
	timer_get_product_list_images : false,

	init : function() {
		
		if ( typeof(poip_settings) != 'undefined' ) {
			poip_list.module_settings = $.extend(true, {}, poip_settings);
		}
		
		poip_list.checkProductListImages();
		setInterval( function(){ // page could be reloaded partially (using ajax by a filter extension or something like this)
			poip_list.checkProductListImages();
		}, 500);	
	},
	
	changeProductImageByThumb : function(thumb_elem) { // showThumb
		
		var $thumb_elem = $(thumb_elem);
						
		if ( $thumb_elem.attr('data-poip-thumb') && $thumb_elem.attr('data-poip-product-index')) {
						
			var $main_img = $('img[data-poip-product-index="'+$thumb_elem.attr('data-poip-product-index')+'"]');
			
			$main_img.attr('src', $thumb_elem.attr('data-poip-thumb'));
			$main_img.closest('a').attr('href', $thumb_elem.attr('href'));
						
		}
	},
	
	eventThumbMouseOver : function(thumb_elem) {
		if ( !poip_list.module_settings.img_category_click ) {
			poip_list.changeProductImageByThumb(thumb_elem);
		}
	},
	
	eventThumbClick : function(thumb_elem) {
		if ( poip_list.module_settings.img_category_click ) {
			poip_list.changeProductImageByThumb(thumb_elem);
			return false;
		}
	},
  
  eventThumbMouseOut : function(thumb_elem) {
		// dummy for some themes
	},
	
	getProductListImages : function(poip_products_ids) {
		
		clearTimeout( poip_list.timer_get_product_list_images );
		if ( poip_list.wait_server_answer ) {
			poip_list.timer_get_product_list_images = setTimeout(function(){
				poip_list.getProductListImages(poip_products_ids);
			}, 500);
		
		} else {
			poip_list.wait_server_answer = true;
		
			var params = {products_ids: poip_products_ids};
			$.ajax({
				type: 'POST',
				url: poip_list.url_get_images,
				data: params,
				dataType: 'json',
				//dataType: 'text',
				beforeSend: function() {},
				complete: function() {},
				success: function(json) {
					poip_list.wait_server_answer = false;
					if (json && typeof(json.products)!='undefined' && json.products) {
						poip_list.displayProductListImages(json);
					}
				},
				error: function(error) {
					console.log(error);
					poip_list.wait_server_answer = false;
				}
			});
		}
	},
	
	checkProductListImages : function() {
		
		var poip_products_ids = [];
		var images_from_cache = {};
		
		$('[data-poip-product-id][data-poip-status!="loaded"]').each(function(){
			var poip_product_id = $(this).attr('data-poip-product-id');
			var poip_element_images = $(this).attr('data-poip-images');
			if ( poip_element_images ) { // used stored in the data stored in the element
				poip_list.displayOneProductImages( $(this), JSON.parse(poip_element_images) );
			} else { // get
				if ( typeof(poip_list.image_cache[poip_product_id]) != 'undefined' ) {
					images_from_cache[poip_product_id] = poip_list.image_cache[poip_product_id];
				} else if (poip_product_id && $.inArray(poip_product_id,poip_products_ids)==-1 ) {
					poip_products_ids.push(poip_product_id);
				}
			}
		});
		
		if ( Object.keys(images_from_cache).length ) {
			poip_list.displayProductListImages(images_from_cache);
		}
		
		if (poip_products_ids.length) {
			poip_list.getProductListImages(poip_products_ids);
		}
	},
	
	displayProductListImages : function(server_response) {
		
		var	products = server_response.products;
		
		for (var poip_product_id in products) {
			
			var poip_data = products[poip_product_id];
			
			if ( typeof(poip_list.image_cache[poip_product_id]) == 'undefined' ) {
				poip_list.image_cache[poip_product_id] = poip_data;
			}
			
			$('[data-poip-product-id="'+poip_product_id+'"][data-poip-status!="loaded"]').each(function(){
				poip_list.displayOneProductImages($(this), poip_data);
			});
		}
	},
	
	displayOneProductImages : function($product_image_element, poip_data) {
		
		$product_image_element.attr('data-poip-status', 'loaded'); // with or without images
		
		if ( !poip_data || $.isEmptyObject(poip_data) ) {
			return;
		}
		
		var $product_anchor = $product_image_element.is('img') ? $product_image_element.closest('a') : $product_image_element; 
		var product_href 		= encodeURI( $product_anchor.attr('href') );
		
		var current_product_index = poip_list.product_count++; // increments the variable but returns an old value (all in one step)
		$product_image_element.attr('data-poip-product-index', current_product_index );
	
		
		poip_common.each(poip_data, function(poip_dt, product_option_id){
		//for (var product_option_id in poip_data) {
		//	if ( !poip_data.hasOwnProperty(product_option_id) ) continue;
		
			var html = '';
      poip_common.each(poip_dt, function(option_image){
			//for (var poip_data_i in poip_data[product_option_id]) {
			//	if ( !poip_data[product_option_id].hasOwnProperty(poip_data_i) ) continue;
			
			//	var option_image = poip_data[product_option_id][poip_data_i];
				var product_option_value_id = option_image.product_option_value_id;
				
				var title = (typeof(option_image.title)!='undefined' && option_image.title) ? option_image.title : '';
				var current_href = product_href+(product_href.indexOf('?')==-1?'?':'&amp;')+'poip_ov='+product_option_value_id;
				
        html+= poip_list.getThumbHtml($product_image_element, option_image, current_product_index, current_href, title);
        
				//html+='<a onmouseover="poip_list.eventThumbMouseOver(this)" onclick="return poip_list.eventThumbClick(this);" ';
				////html+='<a onmouseover="poip_list.changeProductImageByThumb(this);" ';
				//html+=' href="'+current_href+'"';
				//html+=' title="'+title+'"';
				//html+=' data-poip-thumb="'+option_image.thumb+'"';
				//html+=' data-poip-product-index="'+current_product_index+'"';
				//html+=' style="display:inline;"';
				//html+='>';
				//html+='<img class="img-thumbnail"';
				//html+=' src="'+option_image.icon+'" ';
				//html+=' alt="'+title+'"';
				//html+=' style="width:'+option_image.width+'px; height:'+option_image.height+'px;"';
				//html+='>';
				//html+='</a>';

      });
			if ( html ) {
		
        html = poip_list.wrapThumbsHtml(html);
				//html='<div data-poip_id="poip_img" style="  text-align: center; margin-top: 3px;">'+html;
				//html+='</div>';
				
        poip_list.displayThumbs($product_anchor, html);
        
			}
		});

		
	},
  
  getThumbHtml : function($product_image_element, option_image, current_product_index, current_href, title) {
    
    var html = '';
    html+='<a onmouseover="poip_list.eventThumbMouseOver(this)" onmouseout="poip_list.eventThumbMouseOut(this)" onclick="return poip_list.eventThumbClick(this);" ';
    html+=' href="'+current_href+'"';
    html+=' title="'+title+'"';
    html+=' data-poip-thumb="'+option_image.thumb+'"';
    html+=' data-poip-product-index="'+current_product_index+'"';
    html+=' style="display:inline;"';
    html+='>';
    html+='<img class="img-thumbnail"';
    html+=' src="'+option_image.icon+'" ';
    html+=' alt="'+title+'"';
    html+=' style="width:'+option_image.width+'px; height:'+option_image.height+'px;"';
    html+='>';
    html+='</a>';
    return html;
  },
  
  wrapThumbsHtml : function(html) {
    return '<div data-poip_id="poip_img" style="  text-align: center; margin-top: 3px;">'+html+'</div>';
  },
	
  displayThumbs : function($product_anchor, html) {
    $product_anchor.closest('.image').after(html);
  },
	
}

poip_common.initObject(poip_list);


