
(function ($) {
	let live_price_data_key = 'liveopencart.live_price';
	let live_price_common = {
		instances: [],
		initialized: false,
		init : function(){
			if ( !live_price_common.initialized ) {
				live_price_common.initialized = true;
				$(document).ajaxSuccess(function(event, request, settings) {
					if ( settings.url && settings.url.indexOf('common/cart/info') != -1 ) {
						live_price_common.updateAllPrices();
					}
				});
			}
		},
		getInstanceClosestForElement : function($elem){
			while ( $elem.length ) {
				if ( $elem.data(live_price_data_key) ) {
					return $elem.data(live_price_data_key);
				}
				$elem = $elem.parent();
			}
		},
		getInstances : function() {
			return $.extend([], live_price_common.instances);
		},
		updateAllPrices : function() {
			let lp_instancies = live_price_common.getInstances();
			for ( let i in lp_instancies ) {
				if ( !lp_instancies.hasOwnProperty(i) ) continue;
				let lp_instance = lp_instancies[i];
				lp_instance.updatePrice(10);
			}
		},
	};
	
	window.liveopencart = window.liveopencart || {};
	window.liveopencart.live_price = window.liveopencart.live_price || live_price_common;
	
	
	//liveopencart_getLivePriceGetInstance = function($elem){
	//	while ( $elem.length ) {
	//		if ( $elem.data(live_price_data_key) ) {
	//			return $elem.data(live_price_data_key);
	//		}
	//		$elem = $elem.parent();
	//	}
	//};
	
	//liveopencart_getLivePriceInstances = function() {
	//	return $.extend([], window.liveopencart.live_price_instances);
	//};

	
	//liveopencart_getLivePriceInstances = function() {
	//	var lp_instancies = [];
	//	$('*').filter( function(){ return $(this).data(live_price_data_key); } ).each(function(){
	//		lp_instancies.push( $(this).data(live_price_data_key) ); 
	//	});
	//	return lp_instancies;
	//};
	
	$.fn.liveopencart_LivePrice = function(p_params){
		
		$this = this;
		
		var params = $.extend( {
			'lp_settings' 				: {},
			'theme_name'					: 'default',
			'request_url'					: 'index.php?route=extension/liveopencart/liveprice/price',
			'product_id'					: 0,
			'get_custom_methods'	: '',
		}, p_params);
		
		
		var extension = {
			
			$container : $this,
			params : params,
			custom_methods : {},
			timer_update_price : 0,
			timer_init : 0,
			update_price_call_id : 0,
			option_prefix : 'option',
			initialized : false,
			
			setCustomMethods : function() {
				if ( typeof(extension.params.get_custom_methods) == 'function' ) {
					extension.custom_methods = extension.params.get_custom_methods(extension);
				}
			},
			
			containerExists : function() {
				// document.body.contains - fix for IE11
				return ( extension.$container && extension.$container.length && ( (document.contains && document.contains(extension.$container[0])) || (document.body.contains && document.body.contains(extension.$container[0])) ) );
			},
			
			getElement : function(selector) {
				if ( selector.substr(0,1) == '#' && extension.$container.is(selector) ) {
					return extension.$container;
				} else { // find children
					return extension.$container.find(selector);
				}
			},
			
			getOptionElements : function(){
				return extension.getElement('select[name^="'+extension.option_prefix+'["], input[name^="'+extension.option_prefix+'["], textarea[name^="'+extension.option_prefix+'["]');
			},
			
			getQuantityElement : function() {
				return extension.getElement('#input-quantity, #qty-input, input#qty-input, input#qty[name="quantity"], #product input[name="quantity"], .ajax-quickview-cont .quantity [name="quantity"], #quantity_wanted, select#quantity, .product-info .cart input[name=quantity], .product-info .pokupka input[name=quantity], #popup-order-okno input[name=quantity], #quantity-set, .product-info .quantity-control input[name=quantity], select[name="quantity"], input[name=quantity]').first();
			},
			
			getQuantity : function() { 
				return extension.getQuantityElement().val();
			},
			
			getContainer : function() { // is needed only for some specific themes uses detailed selectors for containers (usually only for options), for the main container access use extension.$container
				var $container = extension.$container;
				if ( typeof(extension.custom_methods.getContainer) == 'function' ) {
					$custom_container = extension.custom_methods.getContainer();
					if ( $custom_container ) {
						$container = $custom_container;
					}
				}
				return $container;
			},
			
			getRequestURL : function() {
				var request_url = extension.params.request_url;
				if ( typeof(extension.custom_methods.getRequestURL) == 'function' ) {
					var custom_request_url = extension.custom_methods.getRequestURL();
					if ( custom_request_url ) {
						request_url = custom_request_url;
					}
				}
				return request_url;
			},
			
			getElementsToAnimateOnPriceUpdate : function() { // returns array
				var elements_to_anim = [];
				var custom_elements_to_anim = false;
				if ( typeof(extension.custom_methods.getElementsToAnimateOnPriceUpdate) == 'function' ) {
					custom_elements_to_anim = extension.custom_methods.getElementsToAnimateOnPriceUpdate();
				}
				if ( custom_elements_to_anim ) {
					if ( Array.isArray(custom_elements_to_anim) ) {
						elements_to_anim = custom_elements_to_anim;
					} else {
						elements_to_anim.push(custom_elements_to_anim);
					}
				} else { // default
					var lp_infos = $('#product').parent().find('.list-unstyled');
					if (lp_infos.length >= 2 ) {
						elements_to_anim.push( $(lp_infos[1]) );
					}
				}
				return elements_to_anim;
			},
			
			init : function(call_for_no_options) {
				
				if ($('#mijoshop').length && extension.getElement('[name^="option_oc["]').length) { // 
					extension.option_prefix = "option_oc";
				}
				
				// for some themes. options may be not available on this stage, so lets call for init on document.ready
				if ( !call_for_no_options ) {
					if ( !extension.getElement('input[name^="'+extension.option_prefix+'["],select[name^="'+extension.option_prefix+'["],textarea[name^="'+extension.option_prefix+'["]').length ) {
						$(document).ready(function(){
							extension.init(true);
						});
						return;
					}
				}
				
				extension.setCustomMethods();
				
				var $option_elements = extension.getOptionElements();
				$option_elements.on('change', function(){
					extension.updatePrice(10);
				});
				
				$option_elements.each(function(){
					var $option_element = $(this);
					if ( $option_element.is(':radio:checked, :checkbox:checked') ) {
						extension.updatePrice(10);
						return false;
					} else {
						if ( $option_element.val() ) {
							extension.updatePrice(10);
							return false;
						}
					}
				});
					
				var $quantity_elements = extension.getQuantityElement();
				$quantity_elements.on('input propertychange change paste', function(){
					extension.updatePrice(10);
				});
				
				if ( $quantity_elements.filter('select').length ) {
					extension.updatePrice(10);
				}
				
				extension.getElement('[name^="quantity_per_option["]').on('input propertychange change paste', function(){
					extension.updatePrice(10);
				});
				
				if ( typeof(extension.custom_methods.getSelectorForElementsToUpdatePriceOnClick) == 'function' ) { // this way is needed for some specific themes
					var selector_to_update_price_on_click = extension.custom_methods.getSelectorForElementsToUpdatePriceOnClick();
					extension.getContainer().on('click', selector_to_update_price_on_click, function(){ 
						extension.updatePrice(100);
					});
				}
				
				if ( typeof(extension.custom_methods.getElementsToUpdatePriceOnClick) == 'function' ) {
					var $custom_elements_to_update_price_on_click = extension.custom_methods.getElementsToUpdatePriceOnClick();
					if ( $custom_elements_to_update_price_on_click ) {
						$custom_elements_to_update_price_on_click.click(function(){
							extension.updatePrice(100);
						});
					}
				}
				
				if ( extension.getElement('input[name^="quantity_per_option["][value]:not([value="0"])').length ) {
					extension.updatePrice(10); // initial recalc for default values of quantity per option inputs
				}
				
				// << compatibility Option Price by Char Pro
				if ( extension.getElement('input[name^="'+extension.option_prefix+'["][ppc_current_price], textarea[name^="'+extension.option_prefix+'["][ppc_current_price]').length) {
					extension.getElement('input[name^="'+extension.option_prefix+'["][ppc_current_price], textarea[name^="'+extension.option_prefix+'["][ppc_current_price]').on('input propertychange change paste', function(){
						extension.updatePrice(500);
					});
				}
				// >> compatibility Option Price by Char Pro
				
				
				// Product Size Option
				// replace function
				var fix_updatePriceBySize = function(){
					if ( typeof(updatePriceBySize) == 'function') {
						updatePriceBySize = function(){
							extension.updatePrice(100);
						};
						extension.updatePrice(100);
					}
				}
				if ( typeof(updatePriceBySize) == 'function' ) {
					fix_updatePriceBySize();
				} else {
					$(document).on( 'load', fix_updatePriceBySize );
				}
				
				// quantity_list_pro compatibility
				$(document).on('mouseup', 'body #qty_list', function(){
					setTimeout(function () {
						$('#input-quantity').change();
					}, 50);
				});
				
				if ( typeof(extension.custom_methods.init) == 'function' ) {
					extension.custom_methods.init();
				}
				
				
				// for Yandex sync module by Toporchillo 
				var hash = window.location.hash;
				if (hash) {
					var hashpart = hash.split('#');
					var hashvals = hashpart[1].split('-');
					for (var hashvals_i=0; hashvals_i<hashvals.length; hashvals_i++) {
						var hashval = hashvals[hashvals_i];
						
						if ( hashval ) {
							extension.getElement('select[name^="'+extension.option_prefix+'["] option[value="'+hashval+'"]:first').each(function(){
								$(this).parent().val($(this).attr('value'));
								$(this).parent().change();
							});
							
							extension.getElement('input[name^="'+extension.option_prefix+'["][value="'+hashval+'"]').each( function(){
								$(this).prop('checked', true);
								$(this).change();
							});
						}
					}
				}
				
				extension.$container.data(live_price_data_key, extension);
				//if ( !window.liveopencart ) {
				//	window.liveopencart = {};
				//}
				//if ( !window.liveopencart.live_price_instances ) {
				//	window.liveopencart.live_price_instances = [];
				//}
				
				window.liveopencart.live_price.instances.push(extension);
				window.liveopencart.live_price.init();
				
				extension.initialized = true;
			},
			
			animateElements : function(fadeTo) {
				
				if ( extension.params.lp_settings['animation'] ) {
				
					var elements_to_animate = extension.getElementsToAnimateOnPriceUpdate(); // array
					
					if ( elements_to_animate.length ) {
						for ( var i_elements_to_animate in elements_to_animate ) {
							if ( !elements_to_animate.hasOwnProperty(i_elements_to_animate) ) continue;
							var element_to_animate = elements_to_animate[i_elements_to_animate];
							if ( fadeTo!=1 ) {
								element_to_animate.stop(true); // stop current and clear the queue of animations on new price update request
							}
							element_to_animate.fadeTo('fast', fadeTo);
						}
					}
				}
				
			},
			
			updatePrice : function(liveprice_delay) {
				
				if ( !extension.containerExists() ) {
					return;
				}
				
				if ( typeof(extension.custom_methods.updatePrice_before) == 'function' ) {
					extension.custom_methods.updatePrice_before();
				}
				
				clearTimeout(extension.timer_update_price);
				
				if ( !extension.initialized ) {
					extension.timer_update_price = setTimeout(function(){
						extension.updatePrice(liveprice_delay);
					}, 100);
					return;
				}
				
				if ( liveprice_delay ) {
					extension.timer_update_price = setTimeout(function(){
						extension.updatePrice(0);
					}, liveprice_delay);
					return;
				}
				
				extension.update_price_call_id = (new Date()).getTime();
				var current_update_price_call_id = extension.update_price_call_id;
				var request_url = extension.getRequestURL();
				
				request_url += '&product_id='+extension.params.product_id;
				
				request_url += '&quantity='+extension.getQuantity();
				
				var $container_of_options = extension.getContainer();
				
				options_data = $container_of_options.find('select[name^="'+extension.option_prefix+'["], input[name^="'+extension.option_prefix+'["][type=\'radio\']:checked, input[name^="'+extension.option_prefix+'["][type=\'checkbox\']:checked, textarea[name^="'+extension.option_prefix+'["], input[name^="'+extension.option_prefix+'["][type="text"], [name^="quantity_per_option["]');
				
				$.ajax({
					type: 'POST',
					url: request_url,
					data: options_data,
					dataType: 'json',
					beforeSend: function() {
						extension.animateElements(0.1);
					},
					complete: function() {},
					success: function(json) {
						
						if (json && current_update_price_call_id == extension.update_price_call_id) {
							
							if ( typeof(extension.custom_methods.setPriceHTML) == 'function' ) {
								extension.custom_methods.setPriceHTML(json);
								
							}	else { // default theme
							
								var lp_infos = extension.getElement('#product').parent().find('.list-unstyled');
								if (lp_infos.length >= 2 ) {
									$(lp_infos[1]).html(json.htmls.html);
									//$(lp_infos[1]).replaceWith(json.htmls.html);
								} else if ( lp_infos.length == 1 ) {
									lp_infos.html(json.htmls.html);
								}
							}
							extension.animateElements(1);
						}
					},
					error: function(error) {
						console.log(error);
					}
				});
				
			},
			
		};
		
		extension.init();
		
		return extension;
		
	};
})(jQuery)