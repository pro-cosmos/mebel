<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

// Heading
$_['module_name']           = 'Product option image PRO';
$_['heading_title']         = 'LIVEOPENCART: '.$_['module_name'];
$_['text_edit']             = 'Edit '.$_['module_name'].' Module';
$_['poip_module_name']      = $_['module_name'];

// Text
$_['text_module']         = 'Modules';
$_['text_success']        = 'Success: "'.$_['module_name'] .'" settings changed!';
$_['text_content_top']    = 'Content Top';
$_['text_content_bottom'] = 'Content Bottom';
$_['text_column_left']    = 'Column Left';
$_['text_column_right']   = 'Column Right';

// Entry
$_['entry_settings']                  = 'Module settings';
$_['entry_custom_theme_id']           = 'Custom theme ID';
$_['entry_custom_theme_id_help']      = 'Should be filled only if an original directory of the used theme is renamed or if the name of the theme directory is not unique';
$_['entry_import']                    = 'Import';
$_['entry_import_description']        = 'Import file format: XLS. Import uses only the first sheet to read the data.
<br>The first row (head) should contain names of fields: product_id, option_value_id, image (not product_option_id)
<br>Next rows should contain the data accordingly to the names in the first row.';
$_['entry_import_nothing_before']     = 'Don\'t delete option images before import';
$_['entry_import_delete_before']      = 'Delete all option images before import';
$_['PHPExcelNotFound']                = '<a href="https://liveopencart.com/PHPExcel" target="_blank">PHPExcel</a> is not found. Not found file: ';
$_['button_upload']		                = 'Import file';
$_['button_upload_help']              = 'import will start immediately after selecting the file';
$_['entry_server_response']           = 'Server answer';
$_['entry_import_result']             = 'Processed rows/images/skipped';
$_['entry_import_result_done']       	= 'Importing finished, please check the details';
$_['entry_import_result_details']			= 'Importing details (row numbers)';
$_['entry_import_result_toggle_details']	= 'Show/hide details';
$_['entry_import_result_rows']       	= 'Processed rows: ';
$_['entry_import_result_added']	     	= 'Added: ';
$_['entry_import_result_skipped']     = 'Skipped (unknown reason): ';
$_['entry_import_result_not_found']   = 'Product option is not found: ';
$_['entry_import_result_already_exist'] = 'Image already set for the option: ';

$_['entry_export']                    = 'Export';
$_['button_export']		                = 'Export data';
$_['entry_export_description']        = 'Export product option images data. File format: XLS. The export uses only the first sheet to place the data.
<br>The first row (head) contains names of fields: product_id, option_value_id, image (not product_option_id)
<br>Next rows contain the data accordingly to the names in the first row.';
$_['entry_export_options_without_images'] = 'Include product options having no images';
$_['entry_export_names'] 							= 'Include product and option names';

$_['entry_layout']        = 'Layout:';
$_['entry_position']      = 'Position:';
$_['entry_status']        = 'Status:';
$_['entry_sort_order']    = 'Sort Order:';
$_['entry_sort_order_short']    = 'sort:';
$_['entry_settings_default']          = 'global settings';
$_['entry_settings_yes']          = 'On';
$_['entry_settings_no']          = 'Off';

$_['entry_no_value'] 			= 'no value';
$_['entry_no_value_help'] = 'display the image if the option is not selected (it makes sense only at least one value of the option is checked for the image)';

$_['entry_options_images_edit']       = 'Way to edit option images';
$_['entry_options_images_edit_help']  = 'set a method (place) to edit option images';
$_['entry_options_images_edit_v0']    = 'Images for options (edit on \'Options\' tab)';
$_['entry_options_images_edit_v1']    = 'Options for images (edit on \'Images\' tab)';

$_['entry_img_use_v0']          = 'Off';
$_['entry_img_use_v1']          = 'On for all';
$_['entry_img_use_v2']          = 'On for selected';

$_['entry_img_first_v0']          = 'Do not touch';
$_['entry_img_first_v1']          = 'Replace with first product option images';
$_['entry_img_first_v2']          = 'Use like product option images';

// Entry Module Settings
$_['entry_img_change']          = 'Change the main product image on option select';
$_['entry_img_change_help']     = 'change the main product image on the product page in the customer section on an option select (use the first option image)';
$_['entry_img_hover']           = 'Change the main product image on mouse over';
$_['entry_img_hover_help']      = 'change the main product image on the product page in the customer section on mouse over an additional image';
$_['entry_img_main_to_additional']           = 'Add the main image to additional';
$_['entry_img_main_to_additional_help']      = 'add the main product image to the list of additional product images on the product page in the customer section';
$_['entry_img_main_to_additional_v0']        = 'Disabled (default)';
$_['entry_img_main_to_additional_v1']        = 'Enabled';
$_['entry_img_main_to_additional_v2']        = 'Enabled only if other additional product images exist';

$_['entry_img_use']             = 'Add product option images to additional';
$_['entry_img_use_help']        = 'add product option images to the list of additional product images on the product page in the customer section';

$_['entry_img_limit']           = 'Filter additional images';
$_['entry_img_limit_help']      = 'display only suitable images (accordingly to selected product options) in the list of additional images on the product page in the customer section<br>
works only with feature "'.$_['entry_img_use'].'"';
$_['entry_img_limit_v0']        = 'Off';
$_['entry_img_limit_v1']        = 'All additional images';
$_['entry_img_limit_v2']        = 'Only images of selected options';

$_['entry_img_gal']             = 'Filter popup gallery';
$_['entry_img_gal_help']        = 'display only suitable images (accordingly to selected product options) in the popup gallery on the product page in the customer section, recommended to use with features "'.$_['entry_img_use'].'" and "'.$_['entry_img_limit'].'"';

$_['entry_img_option']          = 'Display images below option';
$_['entry_img_option_help']     = 'display relevant product option images below selected option value select/radio/checkbox on the product page in the customer section';
//$_['entry_img_select']          = 'Выбор опций по изображениям';
//$_['entry_img_select_help']     = 'добавляет надпись "Выбрать" под каждым изображением опции в списке товаров, при клике на надпись соответствующая опция выбирается автоматически';
$_['entry_img_category']        = 'Display option thumbs in product lists';
$_['entry_img_category_help']   = 'display thumbs of product option values on product lists (category pages, manufaturer pages, standard modules "Latest", "Bestsellers", "Special", "Featured", etc.)';
$_['entry_img_category_click']  = 'Swap image in product lists by click';
$_['entry_img_category_click_help'] = 'change the main product image to appropriate option value image on click (otherwise, by mouseover), makes sense only in case of enabled setting \''.$_['entry_img_category'].'\' ';
$_['entry_custom_thumb_size']  	= 'Custom size of option thumbs in product lists';
$_['entry_custom_thumb_size_help'] = 'set specific width/height for product option value thumbs displayed in product lists (otherwise, the thumb size will be determined automatically), makes sense only in case of enabled setting \''.$_['entry_img_category'].'\' ';
$_['entry_custom_thumb_width']  = 'Width (px)';
$_['entry_custom_thumb_height'] = 'Height (px)';

//$_['entry_img_sort']            = 'Сквозная сортировка изображений';
//$_['entry_img_sort_help']       = 'сортировать изображения в соответствии с указанным порядком вне зависимости от опций к которым они привязаны';
$_['entry_img_first']           = 'Standard images of options';
$_['entry_img_first_help']      = 'use standard option images added on the option edit page (menu Catalog - Options - etc)';
$_['entry_img_cart']            = 'Display option images in the cart';
$_['entry_img_cart_help']       = 'display images relevant to selected options in the shopping cart';

$_['entry_show_settings']       = 'Show individual settings for the current product option';
$_['entry_hide_settings']       = 'Hide individual settings for the current product option';
$_['entry_show_hide']           = 'show/hide';
$_['entry_img_radio_checkbox']  = 'Display thumbnails for checkboxes';
$_['entry_img_radio_checkbox_help']  = 'display thumbnails for options with type \'Checkbox\' like it works by default for the option type \'Radio\' (compatible only with some themes)';
$_['entry_dependent_thumbnails']= 'Dependent option thumbnails';
$_['entry_dependent_thumbnails_help']= 'change option thumbnails on the product page in the customer section depending on other selected options';

$_['text_update_alert']     = '(new version is available)';

$_['entry_about']               = 'About';
$_['module_description']    = '
The module module is designed to improve standard OpenCart functionality of product images. It allows to assign images to product options (from 1 to several images per option value) and use them to better visualize a product together with its options for customers.
<br>Compatible types of options: "Select", "Radio", "Checkbox".
';

$_['text_conversation'] = 'We are open for conversation. If you need to modify or integrate our modules, to add a new functionality or develop a new extension, email as to <b>support@liveopencart.com</b>.';

$_['entry_we_recommend'] = 'We also recommend:';
$_['text_we_recommend'] = '

';
$_['module_copyright'] = '"'.$_['module_name'].'". is a commercial extension. Resell or transfer it to other users is NOT ALLOWED.
<br>By purchasing this module, you get it for use on one site. 
If you want to use the module on multiple sites, you should purchase a separate copy for each site.
<br>Thank you for purchasing the module.
';

// Error
$_['error_permission']    = 'Warning: You do not have а permission to modify the module "'.$_['module_name'] .'"!';

$_['text_module_version'] = $_['module_name'].', version';
$_['text_module_support'] = 'Developer: <a href="http://liveopencart.com" target="_blank">liveopencart.com</a> | Support, questions and suggestions: <a href=\"mailto:support@liveopencart.com\">support@liveopencart.com</a>';
$_['module_page'] 				= '
<a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=32391" target="_blank">'.$_['module_name'].' on opencart.com</a>
';


