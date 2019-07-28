<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

namespace liveopencart;
class poip {
	
	private $registry;
	
	private static $extension_library_id = 'liveopencart_poip';
	private $extension_code 	= 'poip3';
	private $extension_version 	= '3.0.5';
	private $extension_installed = null;
	private $extension_model;
	private $extension_language;
	private $extension_paths;
	private $extension_caches = array();
	
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($name) {
		return $this->registry->get($name);
	}
	
	public static function initLibrary($registry) {
		if ( !$registry->has( self::$extension_library_id ) ) {
			$registry->set(self::$extension_library_id, new poip($registry));
			
			$registry->get(self::$extension_library_id)->init(); 
		}
	}
	
	public static function getLibrary($registry) {
		self::initLibrary($registry);
		return $registry->get(self::$extension_library_id);
	}
	
	// separate init, but not in __construct to have no trouble with endless loop on possible calls for initLibrary from models loaded in the library
	private function init() {
		
		$this->extension_paths = array();
		if ( $this->inAdminSection() ) { // admin section
			$this->extension_paths['model_path'] 		= 'extension/module/product_option_image_pro';
			$this->extension_paths['language_path'] 	= 'extension/module/product_option_image_pro';
		} else { // customer section
			$this->extension_paths['model_path'] 		= 'extension/liveopencart/product_option_image_pro';
			$this->extension_paths['language_path'] 	= 'extension/liveopencart/product_option_image_pro';
		}
		$this->extension_paths['model_id'] = 'model_'.str_replace('/', '_', $this->extension_paths['model_path']);
		
		// library also loads the model (accessible standard way from registry or by $this->liveopencart_poip->getModel() )
		$this->loadModel();
		$this->loadLanguageOwn();
		$this->loadLanguageNewOnly();
	}
	
	private function inAdminSection() {
		return defined('DIR_CATALOG');
	}
	
	private function loadModel() {
		$this->load->model( $this->extension_paths['model_path'] );
		$this->extension_model = $this->registry->get( $this->extension_paths['model_id'] );
	}
	
	private function loadLanguageOwn() {
		if ( !$this->extension_language ) {
			$this->language->load( $this->extension_paths['language_path'], $this->extension_code.'_language' ); // Put the language into a sub key
			$this->extension_language = $this->language->get( $this->extension_code.'_language' );
		}
	}
	
	public function loadLanguage() {
		$this->language->load( $this->extension_paths['language_path'] );
	}
	
	public function loadLanguageNewOnly() {
		$existing_lang_vars = $this->language->all();
		$extension_lang_vars = $this->getLanguageOwn()->all();
		$new_lang_keys = array_diff( array_keys($extension_lang_vars), array_keys($existing_lang_vars) );
		foreach ( $new_lang_keys as $new_lang_key ) {
			$this->language->set($new_lang_key, $extension_lang_vars[$new_lang_key]);
		}
	}
	
	public function getLanguageOwn() {
		return $this->extension_language;
	}
	
	public function getExtensionCode() {
		return $this->extension_code;
	}
	
	public function getCurrentVersion() {
    return $this->extension_version;
	}
	
	public function getModel() {
		return $this->extension_model;
	}
	
	public function installed() {
		if ( is_null($this->extension_installed) ) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'module' AND `code` = 'product_option_image_pro'");
			$this->extension_installed = $query->num_rows;
		}
		return $this->extension_installed;
  }
	
	public function hasCachedValue($cache_name, $cache_key=0) {
		return isset($this->extension_caches[$cache_name][$cache_key]);
	}
	
	public function getCachedValue($cache_name, $cache_key=0) {
		if ( $this->hasCachedValue($cache_name, $cache_key) ) {
			return $this->extension_caches[$cache_name][$cache_key];
		}
	}
	
	public function setCachedValue() {
		$args = func_get_args();
		if ( count($args) == 2 ) {
			$cache_name = $args[0];
			$cache_key = 0;
			$cache_value = $args[1];
		} elseif ( count($args) == 3) {
			$cache_name = $args[0];
			$cache_key = $args[1];
			$cache_value = $args[2];
		}
		$this->extension_caches[$cache_name][$cache_key] = $cache_value;
	}
	
	// some thing should be caches in the customer section only
	private function hasCachedValueCatalog($cache_name, $cache_key=0) {
		return !$this->inAdminSection() && $this->hasCachedValue($cache_name, $cache_key);
	}
	
	private function getCachedValueCatalog($cache_name, $cache_key=0) {
		if ( !$this->inAdminSection() ) {
			return $this->getCachedValue($cache_name, $cache_key);
		}
	}
	private function setCachedValueCatalog() {
		if ( !$this->inAdminSection() ) {
			return call_user_func_array( array($this, 'setCachedValue') , func_get_args());
		}
	}
	
	public function getModuleSettingsIds($for_option_or_product_page=true) {
    $settings = array();
    
    if (!$for_option_or_product_page) {
      $settings[] = "options_images_edit";
      $settings[] = "img_hover";
      $settings[] = "img_main_to_additional";
      //$settings[] = "img_gal";
    }
    
    $settings[] = "img_change";
    
    $settings[] = "img_use";
    $settings[] = "img_limit";
    
    $settings[] = "img_option";
    
    $settings[] = "img_first";
    $settings[] = "dependent_thumbnails";
		$settings[] = "img_radio_checkbox";
    $settings[] = "img_cart";
		
		$settings[] = "img_category";
		if (!$for_option_or_product_page) {
			$settings[] = "img_category_click";
		}
    
    
    return $settings;
  }
	
	public function getResourceLinkPathWithVersion($path, $prefix='') {
		//$basic_dir = 'catalog/';
		return $prefix.$path.'?v='.filemtime(DIR_APPLICATION.$path);
	}
	
	public function getOptionSettings($product_option_id) {
    
    if (!$this->liveopencart_poip->installed()) return array();
    
    $query = $this->db->query(" SELECT PMOS.*, PS.product_option_id
                                FROM ".DB_PREFIX."poip_main_option_settings PMOS, ".DB_PREFIX."product_option PS
                                WHERE PS.product_option_id = ".(int)$product_option_id."
                                  AND PS.option_id = PMOS.option_id
                                ");
    
    if ($query->num_rows) {
      return $query->row;
    }
    
    return array();
    
  }
	
	// exact (determined) options settings
  public function getProductOptionSettings($product_option_id) {
    
    $option_settings = array();
    if (!$this->installed()) return $option_settings;
		
		//if ( !$this->inAdminSection() || !isset($this->extension_cache['product_option_settings'][$product_option_id]) ) {
    
			$poip_settings = $this->config->get('poip_module');
			$poip_option_settings = $this->getOptionSettings($product_option_id);
			
			$query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_option_settings WHERE product_option_id = ".(int)$product_option_id." ");
			
			$settings_names = $this->getModuleSettingsIds();
			
			foreach ($settings_names as $setting_name) {
				if ($query->row && isset($query->row[$setting_name]) && $query->row[$setting_name] != 0) {
					$option_settings[$setting_name] = $query->row[$setting_name]-1;
					
				} elseif (isset($poip_option_settings[$setting_name]) && $poip_option_settings[$setting_name] != 0) {
					$option_settings[$setting_name] = $poip_option_settings[$setting_name]-1;
					
				} elseif (isset($poip_settings[$setting_name])) {
					$option_settings[$setting_name] = $poip_settings[$setting_name];
					
				} else {  
					$option_settings[$setting_name] = false;
				}
			}
			
			//$this->cache_product_option_settings[$product_option_id] = $option_settings;
		//}
	
		return $option_settings;
    
  }
	
	// all product settings together (for all options)
  public function getProductSettings($product_id) {
		
		if ( $this->hasCachedValueCatalog(__FUNCTION__, $product_id) ) {
			return $this->getCachedValueCatalog(__FUNCTION__, $product_id);
		}
		
    $query = $this->db->query("SELECT product_option_id FROM ".DB_PREFIX."product_option WHERE product_id = ".(int)$product_id."  ");
		//$poip_settings = $this->config->get('poip_module');
    $poip_settings = array();
    foreach ($query->rows as $row) {
      $poip_settings[$row['product_option_id']] = $this->getProductOptionSettings($row['product_option_id']);
    }
		
		$this->setCachedValueCatalog(__FUNCTION__, $product_id, $poip_settings);
		
    return $poip_settings;
  }
	
}