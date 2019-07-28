//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

var poip = {
	
	initialized : false,
	option_image_row : 0,
	call_on_init : [],
	
	init : function( params ) {
		
		var params_names = ['option_values', 'product_options', 'texts', 'settings_details', 'settings_enable_disable_options', 'saved_settings', 'image_placeholder'];
		for ( var i_params_names in params_names ) {
			if ( !params_names.hasOwnProperty(i_params_names) ) continue;
			
			var param_name = params_names[i_params_names];
			if ( typeof(params[param_name]) != 'undefined' ) {
				poip[param_name] = params[param_name];
			} else {
				poip[param_name] = {};
			}
		}
		
		$(document).on('change', ':checkbox[name^="product_image["][name*="[poip]["][value!="0"]', function(){
			poip.setAvailabilityOfNoValueByName( $(this).attr('name') );
		});
		
		poip.initialized = true;
		for (var i_call_on_init in poip.call_on_init) {
			if ( !poip.call_on_init.hasOwnProperty(i_call_on_init) ) continue;
			var call_on_init_item = poip.call_on_init[i_call_on_init];
			poip.callOnInitApply(call_on_init_item.fn, call_on_init_item.args);
		}
	},
	
	callOnInit : function(fn, args){
		if ( poip.initialized ) {
			poip.callOnInitApply(fn, args);
		} else {
			poip.call_on_init.push( {fn: fn, args: args} );
		}
	},
	
	callOnInitApply : function(fn, args) {
		fn.apply(null, args);
	},
	
	notIntializedWillCallLater : function(fn, p_arguments) {
		if ( !poip.initialized ) {
			poip.callOnInit(fn, p_arguments);
			return true;
		}
		return false;
	},
	
	getProductOptionValueName : function getProductOptionValueName(option_id, option_value_id) { // poip_get_product_option_value_name
		for (var i in poip.option_values[option_id]) {
			if (poip.option_values[option_id][i]['option_value_id'] == option_value_id) {
				return poip.option_values[option_id][i]['name'];
			}
		}
	},
	
	
	displayImageOptions : function displayImageOptions(row, data) { // poip_show_image_options
		
		if ( poip.notIntializedWillCallLater( displayImageOptions, arguments) ) {
			return;
		}
		
		if ( !$('#image-row'+row).length ) return;
		if ( !$('#image-row'+row+' td[data-poip-options]').length ) {
			$('#image-row'+row+' td:first').after('<td class="poip-options-to-image text-left" data-poip-options ></td>');
		}
		
		var html = '';
		var checkbox_names = [];
		
		for (var i in poip.product_options) {
			if ( !poip.product_options.hasOwnProperty(i) ) continue;
			
			var product_option = poip.product_options[i];
			
			if ( $.inArray(product_option['type'], ['select', 'radio', 'image', 'checkbox', 'color', 'block']) != -1) {
				
				html+= '<div class="text-left poip-option-to-image">';
				html+= '<b>'+product_option['name']+'</b><br>';
				
				var checkbox_name = 'product_image['+row+'][poip]['+product_option['option_id']+'][]';
				checkbox_names.push(checkbox_name);
				
				for (var j in product_option['product_option_value']) {
					if ( !product_option['product_option_value'].hasOwnProperty(j) ) continue;
				
					var product_option_value = product_option['product_option_value'][j];
					
					html+= '<div class="checkbox" >';
					html+= '<label>';
					html+= '<input type="checkbox" name="'+checkbox_name+'" value="'+product_option_value['option_value_id']+'"';
					if (data && data['poip'] && data['poip'][product_option['option_id']]) {
						if ( $.inArray(product_option_value['option_value_id'], data['poip'][product_option['option_id']]) != -1 ) {
							html+= ' checked ';
						}
					}
					html+= '>&nbsp;'+poip.getProductOptionValueName(product_option['option_id'], product_option_value['option_value_id']);
					html+= '</label>';
					html+= '</div>';
					
					html+= '';
					html+= '';
				}
				
				// no value
				html+= '<div class="checkbox" >';
				html+= '<i><label title="'+poip.texts.entry_no_value+'">';
				html+= '<input type="checkbox" name="'+checkbox_name+'" value="0"';
				if (data && data['poip'] && data['poip'][product_option['option_id']]) {
					if ( $.inArray(0, data['poip'][product_option['option_id']]) != -1 ) {
						html+= ' checked ';
					}
				}
				html+= '>&nbsp;'+poip.texts.entry_no_value;
				html+= '</label></i>';
				html+= '</div>';
				
				html+= '</div>';
			}
		}
		$('#image-row'+row+' td[data-poip-options]').html(html);
		
		for ( var i_checkbox_names in checkbox_names ) {
			if ( !checkbox_names.hasOwnProperty(i_checkbox_names) ) continue;
			var checkbox_name = checkbox_names[i_checkbox_names];
			poip.setAvailabilityOfNoValueByName(checkbox_name);
		}
		
	},

	// << visibility of the checkbox "no value"
	setAvailabilityOfNoValueByName : function(name) { //poip_setAvailabilityOfNoValueByName
		var no_value_disabled = ( $(':checkbox[name="'+name+'"][value!="0"]:checked').length == 0 );
		$(':checkbox[name="'+name+'"][value="0"]').prop('disabled', no_value_disabled);
		$(':checkbox[name="'+name+'"][value="0"]').parent().fadeTo('fast', (no_value_disabled ? 0.1 : 1) ); // label
	},
	// >> visibility of the checkbox "no value"
	
	displayProductOptionSettings : function displayProductOptionSettings(option_num, option_type, product_option_id) {
		
		if ( poip.notIntializedWillCallLater( displayProductOptionSettings, arguments) ) {
			return;
		}
		
		if ( $.inArray(option_type, ['select', 'radio', 'checkbox', 'color', 'block']) == -1 ) return;
		
		if ( typeof(product_option_id) != 'undefined' && typeof(poip.saved_settings[product_option_id]) != 'undefined' ) {
			var product_option_settings = poip.saved_settings[product_option_id];
		} else {
			var product_option_settings = false;
		}
		
		var html = '';
		html+= '<div class="form-group">';
		html+= '<div data-poip="settings">';
		html+= '<label class="col-sm-2 control-label">'+poip.texts.poip_module_name+'</label>';
		html+= '<div class="col-sm-10"><div class="row">';
		
		for ( var i_settings_details in poip.settings_details) {
			if ( !poip.settings_details.hasOwnProperty(i_settings_details) ) continue;
			var setting_details = poip.settings_details[i_settings_details];
			
			html+= '<div class="col-sm-4">';
			html+= setting_details.title;
			html+= '<select name="product_option['+option_num+'][poip_settings]['+setting_details.name+']" class="form-control">';
			if ( setting_details.values ) {
				html+= '<option value="0">'+poip.settings_enable_disable_options[0]+'</option>';
				for ( var setting_value in setting_details.values ) {
					if ( !setting_details.values.hasOwnProperty(setting_value) ) continue;
					
					setting_value = parseInt(setting_value);
					
					var setting_title = setting_details.values[setting_value];
					html+= '<option value="'+(setting_value+1)+'"';
					if ( product_option_settings && typeof(product_option_settings[setting_details.name] != 'undefined') && product_option_settings[setting_details.name] == (setting_value+1) ) {
						html+= ' selected ';
					}
					html+= '>'+setting_title+'</option>';
				}
			} else {
				for ( var setting_enable_disable_options_value in poip.settings_enable_disable_options ) {
					if ( !poip.settings_enable_disable_options.hasOwnProperty(setting_enable_disable_options_value) ) continue;
					
					var setting_enable_disable_options_value_title = poip.settings_enable_disable_options[setting_enable_disable_options_value];
					html+= '<option value="'+setting_enable_disable_options_value+'"';
					if ( product_option_settings && typeof(product_option_settings[setting_details.name] != 'undefined') && product_option_settings[setting_details.name] == setting_enable_disable_options_value ) {
						html+= ' selected ';
					}
					html+= '>'+setting_enable_disable_options_value_title+'</option>';
				}
			}
			html+= '</select>';
			html+= '</div>';
		}
				
		html+= '</div></div>';
		html+= '</div>';

		$('#tab-option'+option_num).prepend(html);
	},
	
	addProductOptionImage : function addProductOptionImage(option_row, option_value_row, thumb, image, srt) { // add_option_image
		
		if ( poip.notIntializedWillCallLater( addProductOptionImage, arguments) ) {
			return;
		}

		var html = '';
		
		html += '<div id="div_option_image'+poip.option_image_row+'" style="float:left;">';
		if (image && thumb) {
			var current_thumb = thumb;
			var current_image = image;
			var current_srt = srt;
		} else {
			var current_thumb = poip.image_placeholder;
			var current_image = '';
			var current_srt = 0;
			$('#option_images'+option_row+'_'+option_value_row).find('[name*="[srt]"]').each(function() {
				current_srt = Math.max(current_srt, ( parseInt($(this).attr('value')) || 0 ) );
			});
			current_srt++;
		}
		
		
		html += '<a href="" id="thumb-option-image'+poip.option_image_row+'" data-toggle="image" class="img-thumbnail" >';
		html += '<img height="100" width="100" src="'+current_thumb+'" alt="" title="" data-placeholder="'+poip.image_placeholder+'">';
		html += '</a>';
		html += '<input type="hidden" id="option_image'+poip.option_image_row+'" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][images]['+poip.option_image_row+'][image]" value="'+current_image+'">';
		html += '<div>';
		html += '<input  type="text" class="form-control" style="width:72px;float:left;" title="'+poip.texts.entry_sort_order+'" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][images]['+poip.option_image_row+'][srt]" value="'+current_srt+'" size="3">';
		html += '<button class="btn btn-default" title="'+poip.button_remove+'" onclick="$(\'#div_option_image' + poip.option_image_row + '\').remove();"><i class="fa fa-trash-o"></i></button>';
		html += '</div>';
		
		html += '</div>';
		
		$('#option_images'+option_row+'_'+option_value_row).append(html);
		
		poip.option_image_row++;
		
	},


	
}