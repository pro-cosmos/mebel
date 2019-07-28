<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

// Heading
$_['module_name']         = 'Live Price';
$_['heading_title']       = 'LIVEOPENCART: '.$_['module_name'];
$_['text_edit']           = 'Edit '.$_['module_name'].' Module';

// Text
$_['text_module']         = 'Modules';
$_['text_success']        = 'Module "'.$_['heading_title'].'" successfully updated!';
$_['text_content_top']    = 'Content Top';
$_['text_content_bottom'] = 'Content Bottom';
$_['text_column_left']    = 'Column Left';
$_['text_column_right']   = 'Column Right';
$_['text_category_all']   = '-- all categories --';
$_['text_manufacturer_all'] = '-- all manufacturers --';
$_['liveprice_all_customers_groups']      = '-- all groups --';

$_['text_edit_position']  = 'Edit position';

// VALUES
$_['text_value_disabled']               = 'Disabled';
$_['text_value_starting_from_required'] = 'Enabled for products with required options';
$_['text_value_starting_from_all']      = 'Enabled for all products';
$_['text_value_show_from_min']          = 'For minimum prices';
$_['text_value_show_from_all']          = 'For all products';
$_['text_value_show_from_with_options'] = 'For products having selectable options (like Select, Radio, Checkbox)';
$_['text_value_show_from_with_option_prices'] = 'For products having selectable options with prices';

// Entry
$_['entry_sort_order']    							= 'Sort Order:';
$_['entry_discount_quantity'] 					= 'Quantity for discounts:';
$_['text_discount_quantity_0'] 					= 'total quantity per product';
$_['text_discount_quantity_1'] 					= 'total quantity per combination of product options';
$_['text_discount_quantity_2'] 					= 'total quantity per combination of related product options';
$_['entry_discount_quantity_spec'] 			= 'Quantity for discounts:';
$_['entry_multiplied_price'] 						= 'Show price multiplied by quantity:';
$_['entry_about'] 											= 'About';
$_['entry_settings'] 										= 'Settings';
$_['entry_discounts'] 									= 'Global Discounts';
$_['text_discounts_description'] 				= 'Global discounts apply only for products without their own discounts (when product has empty list of discounts). Category condition works only for products directly linked to selected category.';
$_['entry_specials'] 										= 'Global Specials';
$_['text_specials_description'] 				= 'Global specials apply only for products without their own specials (when product has empty list of specials). Category condition works only for products directly linked to selected category.';
$_['entry_customize_discounts']    			= 'Quantity for discounts (customize)';
$_['entry_add_customize_discounts']    	= 'Add customized product discount settings';
$_['entry_ropro_discounts_addition'] 		= 'Use price prefixes for discounts<br> of Related Options:';
$_['text_ropro_discounts_addition_help']= 'Use price prefixes for discounts of Related Options like it basically works for the prices';
$_['entry_ropro_specials_addition'] 		= 'Use price prefixes for specials<br> of Related Options:';
$_['text_ropro_specials_addition_help'] = 'Use price prefixes for specials of Related Options like it basically works for the prices';


$_['entry_manufacturers_spec'] 					= 'Manufactures';
$_['entry_categories_spec'] 						= 'Categories';
$_['entry_products_spec'] 							= 'Products';

$_['entry_percent_discount_to_total'] 	= 'Apply percent discount to total price';
$_['entry_entry_percent_discount_to_total_help'] = 'Apply percent discount to total price (including option price modifiers)';

$_['entry_percent_special_to_total'] 		= 'Apply percent special to total price';
$_['entry_entry_percent_special_to_total_help'] = 'Apply percent special to total price (including option price modifiers)';

$_['entry_default_price']      					= 'Display prices with default options';
$_['entry_default_price_help'] 					= 'Display product prices including price modifiers of default option values in products lists like the category page, the module "Latest", etc (the Improved Options module is required )';
$_['entry_default_price_mods'] 					= 'Default options should be set by <a href="http://www.opencart.com/index.php?route=extension/extension/info&extension_id=33774" target="_blank">Improved Options</a> module';

$_['entry_starting_from']      					= 'Display prices including options';
$_['entry_starting_from_help'] 					= 'Display minimal product prices including option price modifiers in product lists like the category page, the module "Latest", etc (including specials, but not discounts)';

$_['entry_show_from']          					= 'Display the prefix "from" for prices';
$_['entry_show_from_help']     					= 'Display the prefix "from" for product prices in product lists like the category page, the module "Latest", etc (including specials, but not discounts)';

$_['entry_discount_like_special']       = 'Display discounts is style of specials';
$_['entry_discount_like_special_help']  = 'Display applied discounts using style of specials on the product page in the customer section';

$_['entry_ignore_cart']      						= 'Ignore the quantity added to cart';
$_['entry_ignore_cart_help'] 						= 'Ignore product quantity already added to the shopping cart, on the discount calculation';

$_['entry_hide_tax']      							= 'Hide tax on price update';
$_['entry_hide_tax_help'] 							= 'Do not display product taxes on price update on the product page in the customer section';

$_['entry_calculate_once']      				= 'Live Price: Calculate once';
$_['entry_calculate_once_help'] 				= 'Calculate this option price, weight, points at once. To be not multiplied by product quantity. ';

$_['entry_animation']      							= 'Animation on the price update';
$_['entry_animation_help']      				= 'Fading animation, works not for all themes';

$_['text_success'] 											= 'Settings are modified!';
$_['text_update_alert']     						= '(new version available)';

$_['text_relatedoptions_notify'] 				= 'Required extension: <a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=31606" target="_blank" title="Related Options for OpenCart">Related options</a> or <a href="https://isenselabs.com/products/view/related-options-pro-take-product-options-to-the-next-level?pa=41075" target="_blank" title="Related Options PRO for OpenCart">Related options PRO</a>';
$_['text_relatedoptions_pro_notify'] 		= 'Required extension: <a href="https://isenselabs.com/products/view/related-options-pro-take-product-options-to-the-next-level?pa=41075" target="_blank" title="Related Options PRO for OpenCart">Related options PRO</a>';

$_['module_description']    						= '"'.$_['module_name'].'" module is designed to improve the pricing functionality of OpenCart.<br><br>
Main module features:
<ul>
<li>dynamic price updating on product page in customer section, depending on selected options and quantity</li>
<li>edition of products discounts and specials in percentage, ability to set discounts and specials common for all customers groups</li>
<li>global lists of discounts and specials (for category, manufacturer, customer group)</li>
<li>one time option price usage for product price calculation, without dependency on product quantity (optional, can be enabled/disabled for each option separately)</li>
<li>showing of minimal product prices including options (price starting from) in products list ( category page, manufacturer page, modules like "Latest", "Bestsellers", etc.)</li>
<li>taking product quantity already added to cart into account for determination of available discount on product page (optional)</li>
<li>displaying of price on product page multiplied by quantity (optional)</li>
<li>additional options prices prefixes ( * / = % )</li>
</ul>
';


$_['text_conversation'] 	= 'We are open for conversation. If you need modify or integrate our modules, add new functionality or develop new modules, email as to <b>support@liveopencart.com</b>.';

$_['entry_we_recommend'] 	= 'We also recommend:';
$_['text_we_recommend'] 	= '

';

$_['module_copyright'] 		= '"'.$_['module_name'].'" is a commercial extension. Please do not resell or transfer it to other users. By purchasing this module, you get it for use on one site.<br> 
If you want to use the module on multiple sites, you should purchase a separate copy for each site. Thank you.';

$_['text_module_version'] = $_['module_name'].', version';
$_['text_module_support'] = 'Developer: <a href="https://liveopencart.com" target="_blank">liveopencart.com</a> | Support, questions and suggestions: <a href=\"mailto:support@liveopencart.com\">support@liveopencart.com</a>';
$_['module_page'] 				= '
<a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=31581" target="_blank">'.$_['module_name'].' on opencart.com</a>
';

// Error
$_['error_permission']    = 'Warning: You do not have permission to modify module "'.$_['heading_title'].'"!';
