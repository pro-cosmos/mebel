
//  Related Options / Связанные опции
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

function getROInstance(one_tab) {

	var ro_extension = {
		
		each : function(collection, fn){
			for ( var i_item in collection ) {
				if ( !collection.hasOwnProperty(i_item) ) continue;
				if ( fn(collection[i_item], i_item) === false ) {
					return;
				}
			}
		},
		
		init : function(one_tab) {
			
			setInterval(function(){
				ro_extension.checkMaxInputVars();
			}, 1000);
			
			var added_tabs = 0;
			if (ro_data && ro_settings) {
				for (var i in ro_data) {
					var ro_tabs_num = ro_extension.addTab(ro_data[i]);
					added_tabs++;
					
					ro_extension.updateTabStatus(ro_tabs_num);
					
					if ( one_tab ) {
						break;
					}
				}
			}
			if ( ro_settings && added_tabs==0 && one_tab ) {
				ro_extension.addTab();
			}
			
			ro_extension.enableEvents();
			
		},
		
		enableEvents : function(){
			$('#ro_content').on('change', 'select[id^="ro_o_"]', function(){
				var parts = $(this).attr('name').split(/\]|\[/);
				if ( parts.length > 8 && parts[0] == 'ro_data' && parts[7] == 'options' ) {
					var tab_num = parts[1];
					var ro_comb_num = parts[5];
					ro_extension.checkDuplicates(tab_num, ro_comb_num);
				}
			});
			/*
			$('#tab-related_options').on('click', 'button[data-ro-action="remove_comb"]', function(){
				var $button = $(this);
				var tab_num = $button.closest('[data-ro-cnt]').attr('data-ro-cnt');
				var ro_tr_id = $button.closest('tr').attr('data-ro-tr-id');
				var $tr = $button.closest('tr');
				$tr.css('opacity', 0.1);
				setTimeout(function(){
					$tr.remove();
					ro_extension.checkDuplicates(tab_num, false, ro_tr_id);
					ro_extension.updatePagination(tab_num);
				}, 1);	
			});
			*/
			
		},
		
		removeComb : function($button) {
			var tab_num = $button.closest('[data-ro-cnt]').attr('data-ro-cnt');
			var ro_tr_id = $button.closest('tr').attr('data-ro-tr-id');
			var $tr = $button.closest('tr');
			$tr.css('opacity', 0.1);
			setTimeout(function(){
				$tr.remove();
				ro_extension.checkDuplicates(tab_num, false, ro_tr_id);
				ro_extension.updatePagination(tab_num);
			}, 1);	
		},
		
		checkPerformance : function(fn, name) {
			var t0 = performance.now();
			fn();
			var t1 = performance.now();
			console.log('Call to '+name+' took ' + (t1 - t0) + ' milliseconds.');
		},
		
		updatePagination : function(tab_num, action){
			ro_extension.getTabCombElements(tab_num).filter(':hidden').show();
		},
		
		getTabTotalPages : function(tab_num) {
			return ro_extension.getTabElement(tab_num).attr('data-ro-page-total');
		},
		setTabTotalPages : function(tab_num, total_pages) {
			ro_extension.getTabElement(tab_num).attr('data-ro-page-total', total_pages);
		},
		getTabCurrentPage : function(tab_num) {
			return ro_extension.getTabElement(tab_num).attr('data-ro-page-current');
		},
		setTabCurrentPage : function(tab_num, p_current_page) {
			var current_page = (p_current_page || 1);
			current_page = Math.min(current_page, ro_extension.getTabTotalPages(tab_num));
			current_page = Math.max(current_page, 1);
			
			ro_extension.getTabElement(tab_num).attr('data-ro-page-current', current_page);
		},
	
		// ROPRO
		// ro_tab_name_change
		updateTabName : function(ro_tabs_num) {
			
			if ( $('#ro-use-'+ro_tabs_num+'').is(':checked') ) {
				var new_tab_name = $('#rov-'+ro_tabs_num+' option[value="'+$('#rov-'+ro_tabs_num).val()+'"]').html();
			} else {
				var new_tab_name = ro_texts.related_options_title;
			}
			
			$('#ro_nav_tabs a[data-ro-cnt="'+ro_tabs_num+'"]').html(new_tab_name);
			
		},
		
		getTabElement : function(tab_num) {
			return $('#tab-ro-'+tab_num);
		},
		
		getTabTableContainer : function(tab_num) {
			return $('#tab-ro-'+tab_num).find('div.table-responsive:first');
		},
		
		getTabCombElements : function(tab_num) {
			
			// after some testings this way (with 'children') was found as the fastest
			var $trs = $('#tbody-ro-'+tab_num).children('tr');

			return $trs;
		},
		
		// ro_add_tab
		addTab : function(tab_data_param) {
		
			var tab_data = tab_data_param ? tab_data_param : false;
			
			/*
			html = '<li><a href="#tab-ro-'+ro_tabs_cnt+'" data-toggle="tab" data-ro-cnt="'+ro_tabs_cnt+'">ro '+ro_tabs_cnt+'</a></li>';
			$('#ro_add_tab_button').closest('li').before(html);
			html = '<div class="tab-pane" id="tab-ro-'+ro_tabs_cnt+'" data-ro-cnt="'+ro_tabs_cnt+'">'+ro_tabs_cnt+'</div>';
			*/
			var tab_id = 'tab-ro-'+ro_tabs_cnt;
			if ( !$('#'+tab_id).length ) {
				html = '<div id="'+tab_id+'" data-ro-cnt="'+ro_tabs_cnt+'">'+ro_tabs_cnt+'</div>';
				$('#ro_content').append(html);
			}
			
			$('#ro_nav_tabs [data-ro-cnt='+ro_tabs_cnt+']').click();
			
			html = '';
			html+= '<input type="hidden" name="ro_data['+ro_tabs_cnt+'][rovp_id]" value="'+(tab_data['rovp_id'] ? tab_data['rovp_id'] : '0')+'">';
			html+= '<div class="form-group">';
			
			html+= '<label class="col-sm-2 control-label">'+ro_texts.entry_ro_use+'</label>';
			
			html+= '<div class="col-sm-10">';
			html+= '<label class="radio-inline">';
				html+= '<input type="radio" name="ro_data['+ro_tabs_cnt+'][use]" id="ro-use-'+ro_tabs_cnt+'" value="1" '+((tab_data['use'])?('checked'):(''))+' onchange="ro_extension.updateTabStatus('+ro_tabs_cnt+')" />';
				html+= ' '+ro_texts.text_yes;
			html+= '</label>';
			html+= '<label class="radio-inline">';
				html+= '<input type="radio" name="ro_data['+ro_tabs_cnt+'][use]" value="" '+((tab_data['use'])?(''):('checked'))+' onchange="ro_extension.updateTabStatus('+ro_tabs_cnt+')" />';
				html+= ' '+ro_texts.text_no;
			html+= '</label>';
			html+= '</div>';
			
			html+= '</div>';
			
			html+= '<div id="ro-use-data-'+ro_tabs_cnt+'">';
			html+= '	<div class="form-group">';
			html+= '		<label class="col-sm-2 control-label" for="rov-'+ro_tabs_cnt+'" >'+ro_texts.entry_ro_variant+'</label>';
			html+= '		<div class="col-sm-3" >';
			html+= '			<select name="ro_data['+ro_tabs_cnt+'][rov_id]" id="rov-'+ro_tabs_cnt+'" class="form-control" onChange="ro_extension.updateTabName('+ro_tabs_cnt+');">';
			
			if (ro_settings['ro_use_variants']) {
				for (var i in ro_variants_sorted) {
					var ro_variant = ro_variants_sorted[i];
					if (ro_variant['rov_id'] == 0) {
						html+= '				<option value="0">'+ro_texts.text_ro_all_options+'</option>';
					} else {
						html+= '			<option value="'+ro_variant['rov_id']+'" '+(tab_data['rov_id'] && tab_data['rov_id'] == ro_variant['rov_id'] ? 'selected':'')+' >'+ro_variant['name']+'</option>';
					}
				}	
			} else {
				html+= '				<option value="0">'+ro_texts.text_ro_all_options+'</option>';
			}
			
			html+= '			</select>';
			html+= '		</div>';
			html+= '		<button type="button" onclick="ro_extension.fillAllCombinations('+ro_tabs_cnt+');" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="'+ro_texts.entry_add_all_variants+'">'+ro_texts.entry_add_all_variants+'</button>';
			html+= '		<button type="button" onclick="ro_extension.fillAllCombinations('+ro_tabs_cnt+',1);" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="'+ro_texts.entry_add_product_variants+'">'+ro_texts.entry_add_product_variants+'</button>';
			html+= '		<button type="button" onclick="ro_extension.removeAllCombinations('+ro_tabs_cnt+');" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="'+ro_texts.entry_delete_all_combs+'">'+ro_texts.entry_delete_all_combs+'</button>';
			html+= '	</div>';
			
			
			html+= '	<div class="table-responsive">';
			html+= '		<table class="table table-striped table-bordered table-hover">';
			html+= '			<thead>';
			html+= '				<tr>';
			html+= '					<td class="text-left">'+ro_texts.entry_options_values+'</td>';
			html+= '					<td class="text-left" width="90">'+ro_texts.entry_related_options_quantity+':</td>';
					
			var ro_fields = {
				spec_model: ro_texts.entry_model,
				spec_sku: ro_texts.entry_sku,
				spec_upc: ro_texts.entry_upc,
				spec_ean: ro_texts.entry_ean,
				spec_location: ro_texts.entry_location,
				spec_ofs: ro_texts.entry_stock_status,
				spec_weight: ro_texts.entry_weight,
			};
		
			for (var i in ro_fields) {
				if (ro_settings[i] && ro_settings[i] != 0) {
					html+= '<td class="text-left" width="90">'+ro_fields[i]+'</td>';
				}
			}
					
			if (ro_settings['spec_price'] ) {
				html+= '				<td class="text-left" width="90" >'+ro_texts.entry_price+'</td>';
				if (ro_settings['spec_price_discount'] ) {
					html+= '					<td class="text-left" style="90">'+ro_texts.tab_discount+': <font style="font-weight:normal;font-size:80%;">('+ro_texts.entry_customer_group+' | '+ro_texts.entry_quantity+' | '+ro_texts.entry_price+' )</font></td>';
				}
				if (ro_settings['spec_price_special'] ) {
					html+= '					<td class="text-left" style="90">'+ro_texts.tab_special+': <font style="font-weight:normal;font-size:80%;">('+ro_texts.entry_customer_group+' |  '+ro_texts.entry_price+' )</font></td>';
				}
			}
						
			if (ro_settings['select_first'] && ro_settings['select_first'] == 1 ) {
				html+= '				<td class="text-left" width="90" style="white-space:nowrap">'+ro_texts.entry_select_first_short+':</td>';
			}
			
							
			html+= '					<td class="text-left" width="90"></td>';
			
			html+= '				<tr>';			
			html+= '		</thead>';
			html+= '		<tbody id="tbody-ro-'+ro_tabs_cnt+'"></tbody>';
			html+= '	</table>';
			html+= '</div>';
			
			html+= '<div class="form-group"><div class="col-sm-12" >';
			html+= '	<button type="button" onclick="ro_extension.addCombination('+ro_tabs_cnt+', false);" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="'+ro_texts.entry_add_related_options+'">'+ro_texts.entry_add_related_options+'</button>';
			html+= '</div></div>';
			
			html+= '';
			html+= '';
			html+= '</div>';
			
			$('#'+tab_id).html(html);
			ro_extension.updateTabStatus(ro_tabs_cnt);
			
			if (tab_data['ro']) {
				for (var i in tab_data['ro']) {
					ro_extension.addCombination(ro_tabs_cnt, tab_data['ro'][i]);
				}
			}

			// select added tab ROPRO
			$('#ro_nav_tabs a[data-ro-cnt="'+ro_tabs_cnt+'"]').click();
			
			ro_extension.checkDuplicates(ro_tabs_cnt);
			ro_extension.checkDefaultSelectPriority(ro_tabs_cnt);
			ro_extension.updatePagination(ro_tabs_cnt);
			
			return ro_tabs_cnt;
			
		},
		
		// ro_use_check
		updateTabStatus : function(ro_tabs_num) {
			
			$('#ro-use-data-'+ro_tabs_num).toggle( $('input[type=radio][name="ro_data['+ro_tabs_num+'][use]"][value="1"]').is(':checked') );
			ro_extension.updateTabName(ro_tabs_num);
			
		},
		
		// ro_add_combination
		addCombination : function(ro_tabs_num, params) {
		
			var rov_id = $('#rov-'+ro_tabs_num).val();
			var ro_variant = ro_variants[ rov_id ];
		
			var entry_add_discount = ro_texts.entry_add_discount;
			var entry_del_discount_title = ro_texts.entry_del_discount_title;
			
			var entry_add_special = ro_texts.entry_add_special;
			var entry_del_special_title = ro_texts.entry_del_special_title;
			
			
			var str_add = '';
			str_add += '<tr id="related-option'+ro_counter+'" '+(ro_settings.pagination ? 'style="display:none;' : '')+'"><td>';
			
			var div_id = 'ro_status'+ro_counter;
			str_add +='<div id="'+div_id+'" style="color: red"></div>';
			
			for (var i in ro_variant['options']) {
				
				var ro_option = ro_variant['options'][i];
				var option_id = ro_option['option_id'];
			
				str_add += "<div style='float:left;'><label class='col-sm-1 control-label' for='ro_o_"+ro_counter+"_"+option_id+"'> ";
				str_add += "<nobr>"+ro_option['name']+":</nobr>";
				str_add += "</label>";
				str_add += "<select class='form-control' id='ro_o_"+ro_counter+"_"+option_id+"' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][options]["+option_id+"]'>";
				//str_add += "<select class='form-control' id='ro_o_"+ro_counter+"_"+option_id+"' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][options]["+option_id+"]' onChange=\"ro_extension.checkDuplicates("+ro_tabs_num+","+ro_counter+")\">";
				str_add += "<option value=0></option>";
							
					for (var j in ro_all_options[option_id]['values']) {
						if((ro_all_options[option_id]['values'][j] instanceof Function) ) { continue; }
						
						var option_value_id = ro_all_options[option_id]['values'][j]['option_value_id'];
						
						str_add += "<option value='"+option_value_id+"'";
						if (params['options'] && params['options'][option_id] && params['options'][option_id] == option_value_id) str_add +=" selected ";
						str_add += ">"+ro_all_options[option_id]['values'][j]['name']+"</option>";
					}
		
				str_add += "</select>";
				str_add += "</div>";
			}
			
			
			str_add += "</td>";
			str_add += "<td>&nbsp;";
			str_add += "<input type='text' class='form-control' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][quantity]' size='2' value='"+(params['quantity']||0)+"'>";
			str_add += "<input type='hidden' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][relatedoptions_id]' value='"+(params['relatedoptions_id']||"")+"'>";
			str_add += "</td>";
			
			str_add += ro_extension.addTextField(ro_tabs_num, ro_counter, 'spec_model', params, 'model');
			str_add += ro_extension.addTextField(ro_tabs_num, ro_counter, 'spec_sku', params, 'sku');
			str_add += ro_extension.addTextField(ro_tabs_num, ro_counter, 'spec_upc', params, 'upc');
			str_add += ro_extension.addTextField(ro_tabs_num, ro_counter, 'spec_ean', params, 'ean');
			str_add += ro_extension.addTextField(ro_tabs_num, ro_counter, 'spec_location', params, 'location');
			
			if (ro_settings['spec_ofs']) {
				
				str_add += '<td>';
				str_add += '&nbsp;<select name="ro_data['+ro_tabs_num+'][ro]['+ro_counter+'][stock_status_id]" class="form-control">';
				str_add += '<option value="0">-</option>';
				for ( var i_ro_stock_statuses in ro_stock_statuses ) {
					if ( !ro_stock_statuses.hasOwnProperty(i_ro_stock_statuses) ) continue;
					var ro_stock_status = ro_stock_statuses[i_ro_stock_statuses];
					str_add += '<option value="'+ro_stock_status.stock_status_id+'"';
					if (ro_stock_status.stock_status_id == params['stock_status_id']) {
						str_add += ' selected ';
					}
					str_add += '>'+ro_stock_status.name+'</option>';
				}
				str_add += '</select>';
				str_add += '</td>';
			}
			
			if (ro_settings['spec_weight'])	{
				str_add += "<td>&nbsp;";
				str_add += "<select class='form-control' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][weight_prefix]'>";
				str_add += "<option value='=' "+( (params['weight_prefix'] && params['weight_prefix']=='=')? ("selected") : (""))+">=</option>";
				str_add += "<option value='+' "+( (params['weight_prefix'] && params['weight_prefix']=='+')? ("selected") : (""))+">+</option>";
				str_add += "<option value='-' "+( (params['weight_prefix'] && params['weight_prefix']=='-')? ("selected") : (""))+">-</option>";
				str_add += "</select>";
				str_add += "<input type='text' class='form-control' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][weight]' value=\""+(params['weight']||'0.000')+"\" size='5'>";
				str_add += "</td>";
			}
			
			if (ro_settings['spec_price'])	{
				str_add += "<td>&nbsp;";
				if (ro_settings['spec_price_prefix']) {
					str_add += "<select name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][price_prefix]' class='form-control'>";
					var price_prefixes = ['=', '+', '-'];
					for (var i in price_prefixes) {
						if((price_prefixes[i] instanceof Function) ) { continue; }
						var price_prefix = price_prefixes[i];
						str_add += "<option value='"+price_prefix+"' "+(price_prefix==params['price_prefix']?"selected":"")+">"+price_prefix+"</option>";
					}
					str_add += "</select>";
				}
				str_add += "<input type='text' class='form-control' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][price]' value='"+(params['price']||'')+"' size='10'>";
				str_add += "</td>";
			}
			
			
			if (ro_settings['spec_price'] && ro_settings['spec_price_discount'])	{
				str_add += "<td>";
			
				str_add += "<button type='button' onclick=\"ro_extension.addDiscount("+ro_tabs_num+", "+ro_counter+", '');\" data-toggle='tooltip' title='"+entry_add_discount+"' class='btn btn-primary'><i class='fa fa-plus-circle'></i></button>";
				str_add += "<div id='ro_price_discount"+ro_counter+"' >";
				str_add += "</div>";
				str_add += "</td>";	
			}
			
			if (ro_settings['spec_price'] && ro_settings['spec_price_special'])	{
				str_add += "<td>";
				str_add += "<button type='button' onclick=\"ro_extension.addSpecial("+ro_tabs_num+", "+ro_counter+", '');\" data-toggle='tooltip' title='"+entry_add_special+"' class='btn btn-primary'><i class='fa fa-plus-circle'></i></button>";
				str_add += "<div id='ro_price_special"+ro_counter+"'>";
				str_add += "</div>";
				str_add += "</td>";	
			}
			
			if (ro_settings['select_first'] && ro_settings['select_first']==1) {
				str_add += "<td>&nbsp;";
				
				str_add += "<input id='defaultselect_"+ro_counter+"' type='checkbox' onchange='ro_extension.checkDefaultSelectPriority("+ro_tabs_num+");' name='ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][defaultselect]' "+((params && params['defaultselect']==1)?("checked"):(""))+" value='1'>";
				str_add += '<input id="defaultselectpriority_'+ro_counter+'" type="text" class="form-control" title="'+ro_texts.entry_select_first_priority+'" name="ro_data['+ro_tabs_num+'][ro]['+ro_counter+'][defaultselectpriority]"  value="'+((params && params['defaultselectpriority'])?(params['defaultselectpriority']):(''))+'" >';
				str_add += "</td>";	
			}
		
			str_add += '<td><br>';
			str_add += '<button type="button" class="btn btn-danger" onclick="ro_extension.removeComb($(this))" class="btn btn-primary" data-original-title="'+ro_texts.button_remove+'" ><i class="fa fa-minus-circle"></i></button>';
			str_add += '</td></tr>';
			
			$('#tbody-ro-'+ro_tabs_num).append(str_add);
			
			if (ro_settings['spec_price'] && ro_settings['spec_price_discount'])	{
				if (params && params['discounts'] ) {
					for (var i_dscnt in params['discounts']) {
						if ( ! params['discounts'].hasOwnProperty(i_dscnt) ) continue;
						ro_extension.addDiscount(ro_tabs_num, ro_counter, params['discounts'][i_dscnt]);
					}
				}
			}
			
			if (ro_settings['spec_price'] && ro_settings['spec_price_special'])	{
				if (params && params['specials'] ) {
					for (var i_dscnt in params['specials']) {
						if ( ! params['specials'].hasOwnProperty(i_dscnt) ) continue;
						ro_extension.addSpecial(ro_tabs_num, ro_counter, params['specials'][i_dscnt]);
					}
				}
			}
			
			ro_extension.updateCombinationUID(ro_tabs_num,ro_counter);
			
			if (params==false) {
				ro_extension.checkDuplicates(ro_tabs_num);
				ro_extension.checkDefaultSelectPriority(ro_tabs_num);
				
				ro_extension.updatePagination(ro_tabs_num, 'new-combination');
				ro_extension.setTabCurrentPage( ro_tabs_num, ro_extension.getTabTotalPages(ro_tabs_num) );
			}
			
			ro_counter++;
			
		},
		
		// ro_refresh_status
		checkDuplicates : function(ro_tabs_num, ro_num, p_ro_tr_id) {
			
			var ro_tr_id_check = '';
			if ( ro_num || ro_num===0 ) {
				var ro_tr_id_old = ro_extension.getCombinationUID(ro_num);
				ro_tr_id_check = ro_extension.updateCombinationUID(ro_tabs_num, ro_num);
				ro_extension.checkDuplicates(ro_tabs_num, false, ro_tr_id_old);
			} else if ( p_ro_tr_id ) {
				ro_tr_id_check = p_ro_tr_id;
			}
			
			var rov_id = $('#rov-'+ro_tabs_num).val();
			var ro_variant = ro_variants[ rov_id ];
			
			//$('#tab-ro-'+ro_tabs_num+' [data-ro-tr-id="'+ro_tr_id_old+'"] div[id^=ro_status]').filter(':not(:empty)').html('');
			$('#tab-ro-'+ro_tabs_num+' [data-ro-tr-id="'+ro_tr_id_check+'"] div[id^=ro_status]').filter(':not(:empty)').html('');
			
			var opts_combs = [];
			var checked_opts_combs = [];
			
			var $trs = ro_extension.getTabCombElements(ro_tabs_num);
			if ( ro_tr_id_check ) {
				$trs = $trs.filter('[data-ro-tr-id="'+ro_tr_id_check+'"]');
			}
			
			var tr_ids = {};
			var double_tr_ids = [];
			$trs.each( function () {
				var ro_comb_tr_id = $(this).attr('data-ro-tr-id');
				
				if ( !tr_ids[ro_comb_tr_id] ) {
					tr_ids[ro_comb_tr_id] = true;
				} else {
					double_tr_ids.push(ro_comb_tr_id);
				}
				//$trs_doubles = $trs.filter('[data-ro-tr-id="'+ro_comb_tr_id+'"]');
				//if ( $trs_doubles.length > 1 ) {
					//$trs_doubles.each(function(){
					//	$(this).find('div[id^=ro_status]').html(ro_texts.warning_equal_options);
					//});
				//}
				
			});
			
			ro_extension.each(double_tr_ids, function(double_tr_id){
				$trs.filter('[data-ro-tr-id="'+double_tr_id+'"]').each(function(){
					$(this).find('div[id^=ro_status]').html(ro_texts.warning_equal_options);
				});
			});
			
			/*
			$('#tab-ro-'+ro_tabs_num+' tr[id^=related-option]').each( function () {
				var opts_comb = $(this).attr('data-ro-tr-id');
				
				if ( $.inArray(opts_comb, opts_combs) != -1 && $.inArray(opts_comb, checked_opts_combs)==-1 ) {
					$('#tab-ro-'+ro_tabs_num+' tr[data-ro-tr-id='+opts_comb+']').each( function () {
						$(this).find('div[id^=ro_status]').html(ro_texts.warning_equal_options);
					});
					checked_opts_combs.push(opts_comb);
				} else {
					opts_combs.push(opts_comb);
				}
			})
			*/
			
			return;
			
		},
		
		// ro_update_combination
		updateCombinationUID : function(ro_tabs_num, ro_num) {
			
			var rov_id = $('#rov-'+ro_tabs_num).val();
			var ro_variant = ro_variants[ rov_id ];
			var str_opts = "";
			
			for (var i in ro_variant['options']) {
				
				if((ro_variant['options'][i] instanceof Function) ) { continue; }
				
				var option_id = ro_variant['options'][i]['option_id'];
			
				str_opts += "_o"+option_id;
				str_opts += "_"+$('#ro_o_'+ro_num+'_'+option_id).val();
			}
			$('#related-option'+ro_num).attr('data-ro-tr-id', str_opts);
			return str_opts;
		},
		
		getCombinationUID : function(ro_num) {
			return $('#related-option'+ro_num).attr('data-ro-tr-id');
		},
		
		// ro_add_text_field
		addTextField : function(ro_tabs_num, ro_num, setting_name, params, field_name) {
			str_add = "";
			if (ro_settings[setting_name] && ro_settings[setting_name]!='0')	{
				str_add += "<td>&nbsp;";
				str_add += "<input type='text' class='form-control' name='ro_data["+ro_tabs_num+"][ro]["+ro_num+"]["+field_name+"]' value=\""+(params[field_name]||'')+"\">";
				str_add += "</td>";
			}
			return str_add;
		},
		
		// ro_add_discount
		addDiscount : function(ro_tabs_num, ro_counter, discount) {
			
			var first_name = "ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][discounts]["+ro_discount_counter+"]";
			var customer_group_id = (discount=="")?(0):(discount['customer_group_id']);
			
			str_add = "";
			str_add += "<table id='related-option-discount"+ro_discount_counter+"' style='width:300px;'><tr><td>";
			
			str_add += '<select name="'+first_name+'[customer_group_id]" class="form-control" title="'+ro_texts.entry_customer_group+'" style="float:left;width:80px;">';
			for ( var i_ro_customer_groups in ro_customer_groups ) {
				if ( !ro_customer_groups.hasOwnProperty(i_ro_customer_groups)) continue;
				var ro_customer_group = ro_customer_groups[i_ro_customer_groups];
				str_add += '<option value="'+ro_customer_group.customer_group_id+'" '+(customer_group_id==ro_customer_group.customer_group_id ? 'selected' : '')+'>'+ro_customer_group.name+'</option>';
			}
			str_add += '</select>';
			
			str_add += '<input type="text" class="form-control" style="float:left;width:100px;" size="2" name="'+first_name+'[quantity]" value="'+((discount=='')?(''):(discount['quantity']))+'" title="'+ro_texts.entry_quantity+'">';
			str_add += '';
			
			// hidden
			str_add += '<input type="hidden" name="'+first_name+'[priority]" value="'+((discount=='')?(''):(discount['priority']))+'" title="'+ro_texts.entry_priority+'">';
			
			str_add += '<input type="text" class="form-control" style="float:left;width:80px;" size="10" name="'+first_name+'[price]" value="'+((discount=='')?(''):(discount['price']))+'" title="'+ro_texts.entry_price+'">';
			
			str_add += '<button type="button" onclick="$(\'#related-option-discount' + ro_discount_counter + '\').remove();" data-toggle="tooltip" title="'+ro_texts.button_remove+'" class="btn btn-danger" style="float:left;" data-original-title=""><i class="fa fa-minus-circle"></i></button>';
		
			str_add += '</td></tr></table>';
			
			$('#ro_price_discount'+ro_counter).append(str_add);
			
			ro_discount_counter++;
			
		},
		
		// ro_add_special
		addSpecial : function(ro_tabs_num, ro_counter, special) {
			
			var first_name = "ro_data["+ro_tabs_num+"][ro]["+ro_counter+"][specials]["+ro_special_counter+"]";
			var customer_group_id = (special=="")?(0):(special['customer_group_id']);
			
			str_add = "";
			str_add += "<table id='related-option-special"+ro_special_counter+"' style='width:200px;'><tr><td>";
			
			str_add += '<select name="'+first_name+'[customer_group_id]" class="form-control" style="float:left;width:80px;" title="'+ro_texts.entry_customer_group+'">';
			for ( var i_ro_customer_groups in ro_customer_groups ) {
				if ( !ro_customer_groups.hasOwnProperty(i_ro_customer_groups)) continue;
				var ro_customer_group = ro_customer_groups[i_ro_customer_groups];
				str_add += '<option value="'+ro_customer_group.customer_group_id+'" '+(customer_group_id==ro_customer_group.customer_group_id ? 'selected' : '')+'>'+ro_customer_group.name+'</option>';
			}
			str_add += '</select>';
			
			// hidden
			str_add += '<input type="hidden" size="2" name="'+first_name+'[priority]" value="'+((special=='')?(''):(special['priority']))+'" title="'+ro_texts.entry_priority+'">';
			str_add += '<input type="text"  class="form-control" style="float:left;width:80px;" size="10" name="'+first_name+'[price]" value="'+((special=='')?(''):(special['price']))+'" title="'+ro_texts.entry_price+'">';
			str_add += '<button type="button" onclick="$(\'#related-option-special' + ro_special_counter + '\').remove();" data-toggle="tooltip" title="'+ro_texts.button_remove+'" class="btn btn-danger" style="float:left;" data-original-title="'+ro_texts.button_remove+'"><i class="fa fa-minus-circle"></i></button>';
			str_add += "</td></tr></table>";
			
			$('#ro_price_special'+ro_counter).append(str_add);
			
			ro_special_counter++;
			
		},
		
		// ro_delete_all_combinations
		removeAllCombinations : function(ro_tabs_num) {
		
			if ( confirm(ro_texts.text_delete_all_combs) ) {
				// fastest
				$('#tbody-ro-'+ro_tabs_num+' tr').detach().remove();
				//$('#tbody-ro-'+ro_tabs_num).empty();
				//$('#tbody-ro-'+ro_tabs_num+' tr').remove();
				//$('#tbody-ro-'+ro_tabs_num).html('');
				ro_extension.checkDuplicates(ro_tabs_num);
				ro_extension.updatePagination(ro_tabs_num);
			}
		},
		
		numberOfPossibleCombinations : function(ro_variant) {
			var numberOfCombs = 1;
			for (var i in ro_variant['options']) {
				var option_id = ro_variant['options'][i]['option_id'];
				var numberOfValues = ro_all_options[option_id]['values'].length || 1;
				numberOfCombs = numberOfCombs * numberOfValues;
			}
			return numberOfCombs;
		},
		
		confirmNumberOfCombinations : function(number_of_combs) {
			var max_number_of_combinations = ro_texts.max_number_of_combinations;
			var confirm_number_of_combinations = ro_texts.confirm_number_of_combinations;
			if ( number_of_combs > max_number_of_combinations ) {
				alert(ro_texts.text_combs_number+number_of_combs.toString()+ro_texts.text_combs_number_out_of_limit);
				return false;
			} else if ( number_of_combs > confirm_number_of_combinations ) {
				if ( !confirm(ro_texts.text_combs_number+number_of_combs.toString()+ro_texts.text_combs_number_is_big) ) {
					return false;
				}
			} else {
				if ( !confirm(number_of_combs.toString()+ro_texts.text_combs_will_be_added) ) {
					return false;
				}
			}
			return true;
		},
		
		// ro_fill_all_combinations
		fillAllCombinations : function(ro_tabs_num, product_options_only) {
			
			var rov_id = $('#rov-'+ro_tabs_num).val();
			var ro_variant = ro_variants[ rov_id ];
			var all_vars = [];
			
			if (product_options_only) {
				var this_product_options = [];
				$('select[name^=product_option][name*=option_value_id]').each(function() {
					if ( $(this).val() ) {
						this_product_options.push($(this).val());
					}
				});
			}
			
			if (!product_options_only) {
				// if all options used, there may be millinons of combinations, it may freeze script before determination of combinations list
				var numberOfCombs = ro_extension.numberOfPossibleCombinations(ro_variant);
				if (!ro_extension.confirmNumberOfCombinations(numberOfCombs)) {
					return;
				}
			}
				
			var reversed_options = [];	
			for (var i in ro_variant['options']) {
				if((ro_variant['options'][i] instanceof Function) ) { continue; }
				reversed_options.unshift(i);
			}
				
			for (var i_index in reversed_options) {
			
				var i = reversed_options[i_index];
				
				var option_id = ro_variant['options'][i]['option_id'];
				
				var temp_arr = [];
				for (var j in ro_all_options[option_id]['values']) {
					if((ro_all_options[option_id]['values'][j] instanceof Function) ) { continue; }
					
					var option_value_id = ro_all_options[option_id]['values'][j]['option_value_id']
					
					if (product_options_only && $.inArray(option_value_id, this_product_options) == -1 ) { //
						continue;
					}
					if (all_vars.length) {
						for (var k in all_vars) {
							if((all_vars[k] instanceof Function) ) { continue; }
							
							var comb_arr = all_vars[k].slice(0);
							comb_arr[option_id] = option_value_id;
							temp_arr.push( comb_arr );
						}
					} else {
						var comb_arr = [];
						comb_arr[option_id] = option_value_id;
						temp_arr.push(comb_arr);
					}
					
				}
				if (temp_arr && temp_arr.length) {
					all_vars = temp_arr.slice(0);
				}
			}
			
			if (all_vars.length) {
				
				if (product_options_only) {
					var numberOfCombs = all_vars.length;
					if (!ro_extension.confirmNumberOfCombinations(numberOfCombs)) {
						return;
					}
				}
			
				for (var i in all_vars) {
					if((all_vars[i] instanceof Function) ) { continue; }
					
					rop = {};
					for (var j in all_vars[i]) {
						if((all_vars[i][j] instanceof Function) ) { continue; }
						rop[j] = all_vars[i][j];
					}
					
					ro_extension.addCombination(ro_tabs_num, {options: rop});
		
				}
				
				ro_extension.updateTabStatus(ro_tabs_num);
				ro_extension.checkDuplicates(ro_tabs_num);
				ro_extension.checkDefaultSelectPriority(ro_tabs_num);
				ro_extension.updatePagination(ro_tabs_num);
			}
			
		},
		
		// check priority fields (is it available or not) for default options combination
		// ro_check_defaultselectpriority
		checkDefaultSelectPriority : function(ro_tabs_num) {
			
			var dsc = $('#tab-ro-'+ro_tabs_num+' input[type=checkbox][id^=defaultselect_]');
			var dsp;
			for (var i=0;i<dsc.length;i++) {
				dsp = $('#defaultselectpriority_'+dsc[i].id.substr(14));
				if (dsp && dsp.length) {
					if (dsc[i].checked) {
						dsp[0].style.display = '';
						if (isNaN(parseInt(dsp[0].value))) {
							dsp[0].value = 0;
						}
						if (parseInt(dsp[0].value)==0) {
							dsp[0].value = "1";
						}
					} else {
						dsp[0].style.display = 'none';
					}
				}
			}
		},
		
		// check_max_input_vars
		checkMaxInputVars : function() {
			var max_input_vars = ro_texts.max_input_vars;
			if (max_input_vars && !$('#warning_max_input_vars').length) {
				var input_vars = $('select').length + $('input').length  + $('textarea').length; // works faster
				//var input_vars = $('select, input, textarea').length;
				if ( input_vars/max_input_vars*100 > 80 ) {
					var html = '<div class="alert alert-danger" id="warning_max_input_vars"><i class="fa fa-exclamation-circle"></i> '+ro_texts.warning_max_input_vars+'</div>';
					$('div.panel:first').before(html);
				}
			}
		},
	};
	
	ro_extension.init(one_tab);
	
	return ro_extension;

}