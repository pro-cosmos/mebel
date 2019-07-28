//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

var poip_common = {
	
	events_suffixes : {
		before 	: '.before',
		instead : '.instead',
		after 	: '.after',
	},
	
	initObject : function(obj_to_init, params, debug, call_cnt ) {
		
		poip_common.proxyObjectMethods(obj_to_init, debug);
		
		call_cnt = call_cnt || 0;
		if ( typeof($) == 'undefined' ) {
			if ( call_cnt == 100 ) {
				console.debug('POIP: jQuery($) is not found');
			}
			setTimeout(function(){
				poip_common.initObject(obj_to_init, params, debug, call_cnt+1);
			}, 100);
			return;
		}
		
		$().ready(function(){
			
			obj_to_init.init(params);
			
		});
	},
	
	proxyObjectMethods : function(obj_to_proxy, debug) {
		
		if ( !obj_to_proxy.proxied ) {
			for ( var _method_name in obj_to_proxy ) {
				if ( !obj_to_proxy.hasOwnProperty(_method_name) ) continue;
				var _method = obj_to_proxy[_method_name];
				if ( typeof(_method) == 'function' ) {
					
					poip_common.proxyObjectMethod(obj_to_proxy, _method_name, _method, debug);
					
				}
			}
			obj_to_proxy.proxied = true;
		}
		return obj_to_proxy;
	},
	
	debugInfo : function(data, debug) {
		if ( debug ) {
			console.debug(data);
		}
	},
	
	proxyObjectMethod : function(obj_to_proxy, _method_name, _method, debug) {
		
		obj_to_proxy[_method_name] = function(){
			
			poip_common.debugInfo('call proxied method: '+_method_name, debug);
			poip_common.debugInfo(arguments, debug);
			
			if ( typeof(obj_to_proxy.custom_methods[_method_name + poip_common.events_suffixes.before]) == 'function' ) {
				
				poip_common.debugInfo('call '+poip_common.events_suffixes.before, debug);
				
				obj_to_proxy.custom_methods[_method_name + poip_common.events_suffixes.before].apply(this, arguments);
			}
			
			if ( typeof(obj_to_proxy.custom_methods[_method_name + poip_common.events_suffixes.instead]) == 'function' ) {
				
				poip_common.debugInfo('call '+poip_common.events_suffixes.instead, debug);
				
				return obj_to_proxy.custom_methods[_method_name + poip_common.events_suffixes.instead].apply(this, arguments);
			}
			
			
			
			poip_common.debugInfo('call original', debug);
			
			var result = _method.apply(this, arguments);
			
			if ( typeof(obj_to_proxy.custom_methods[_method_name + poip_common.events_suffixes.after]) == 'function' ) {
				
				poip_common.debugInfo('call '+poip_common.events_suffixes.after, debug);
				
				obj_to_proxy.custom_methods[_method_name + poip_common.events_suffixes.after].apply(this, arguments);
			}
			
			return result;
			
		};
		
	},
	
	addToArrayIfNotExists : function(val, arr) { // addToArrayIfNotExists
		if ( $.inArray(val, arr) == -1 ) {
			arr.push(val);
		}
	},
	
	getIntersectionOfArrays : function(arr1, arr2) { // uses order of the first array
		var match = [];
		$.each(arr1, function (i, val1) {
			if ($.inArray(val1, arr2) != -1 && $.inArray(val1, match) == -1) {
				match.push(val1);
			}
		});
		return match;
	},
	
	existsIntersectionOfArrays : function(arr1, arr2) {
		var result = false;
		poip_common.each(arr1, function(item){
			if ( $.inArray(item, arr2) != -1 ) {
				result = true;
				return false;
			}
		});
		return result;
	},
	
	getConcatenationOfArraysUnique : function(arr1, arr2) {
		var arr = arr1.slice();
		for ( var i_arr2 in arr2 ) {
			var value = arr2[i_arr2];
			if ( $.inArray(value, arr) == -1 ) {
				arr.push(value);
			}
		}
		return arr;
	},
	
	getOuterHTML : function($elem) {
		var str = $('<div>').append($elem.clone()).html();
		return str;
	},
	
	each : function(collection, fn){
		for ( var i_item in collection ) {
			if ( !collection.hasOwnProperty(i_item) ) continue;
			if ( fn(collection[i_item], i_item) === false ) {
				return;
			}
		}
	},
	
}