<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

namespace liveopencart\ext;

class liveprice extends \liveopencart\lib\v0004\extension {
  
  protected $extension_code = 'lp3';
	protected $version = '3.1.0';
  protected $theme_details = '';
  protected $resource_route_catalog = 'view/theme/extension_liveopencart/live_price/';
  protected $resource_subroute_catalog_theme = 'theme/';
  protected $sub_instances;
  
  public function __construct() {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		if ( $this->installed() ) {
			$this->checkTables();
      //$this->checkOldSettings();
		}
	}
  
  public function installed() {
    return $this->getExtensionInstalledStatus('liveprice', 'liveprice_installed');
  }
  
  public function versionPRO() {
		return ($this->getExtensionCode() == 'lppro3');
	}
  
  public function getThemeName() {
    
    if ( !$this->theme_details ) {
      $params = 
       array(
        'themes_shorten' => $this->getAdaptedThemes(),
        'sibling_dir' => $sibling_file_name = $this->getBasicDirOfExtension().'theme_sibling/',
      );
      ///$this->theme_details = new \liveopencart\lib\v0004\theme_details($params);
      
      $this->theme_details = $this->getOuterLibraryInstanceByName('theme_details', $params);
      
    } 
    return $this->theme_details->getThemeName();
  }
  
  public function getCalc() {
    return $this->getSubInstance('calculation');
    //if ( !$this->calculation ) {
    //  $this->calculation = new \liveopencart\ext\liveprice\calculation($this->registry);
    //}
    //return $this->calculation;
  }
  
  public function getSubPSO() {
    return $this->getSubInstance('pso');
  }
  public function getSubIO() {
    return $this->getSubInstance('io');
  }
  public function getSubRO() {
    return $this->getSubInstance('ro');
  }
  
  protected function getSubInstance($sub_instance_name) {
    if ( !isset($sub_instances[$sub_instance_name]) ) {
      $sub_instance_class_name = '\\liveopencart\\ext\\liveprice\\'.$sub_instance_name;
      $sub_instances[$sub_instance_name] = new $sub_instance_class_name($this->registry);
    }
    return $sub_instances[$sub_instance_name];
  }
  
  public function getBasicDirOfExtension() {
		return DIR_APPLICATION.$this->resource_route_catalog;
	}
	public function getBasicDirOfThemes() {
		return $this->getBasicDirOfExtension().$this->resource_subroute_catalog_theme;
	}
  public function getResourceRouteCatalog($resource) {
		return $this->resource_route_catalog.$resource;
	}
  public function getResourceRouteCatalogCurrentTheme($resource) {
    return $this->resource_route_catalog.$this->getSubRouteOfCurrentTheme().$resource;
  }
  protected function getSubRouteOfCurrentTheme() {
    return substr($this->getDirOfCurrentTheme(), strlen($this->getBasicDirOfExtension()));
  }
	//public function getResourcePathWithVersionCatalog($resource) {
	//	return $this->getResourceLinkWithVersion( $this->getResourceRouteCatalog($resource) );
	//}
  
  protected function getDirOfCurrentTheme() {
		$theme_dir = $this->getBasicDirOfThemes().$this->getThemeName().'/';
		if ( !file_exists($theme_dir) || !is_dir($theme_dir) ) {
			$theme_dir = $this->getBasicDirOfThemes().'default/';
		} 
		return $theme_dir;
	}
  
  public function getPriceTemplates() {
		
		$files = array();
		$theme_dir = $this->getDirOfCurrentTheme();
		$files = $this->getPriceTemplatesFromDirectory($theme_dir);

		return $files;
	}
	private function getPriceTemplatesFromDirectory($dir_theme) {
		$files = array();
		foreach ( glob($dir_theme.'*.twig') as $file_name ) {
			$files[basename($file_name, '.twig')] = $this->getTemplateFileRoute($file_name);
		}
		
		if ( !$files ) { // check for tpl
			foreach ( glob($dir_theme.'*.tpl') as $file_name ) {
				$files[basename($file_name, '.tpl')] = $this->getTemplateFileRoute($file_name, '.tpl');
			}
		}
		
		return $files;
	}
	private function getTemplateFileRoute($file_name, $file_ext='.twig') {
		$route = dirname($file_name).'/'.basename($file_name, $file_ext);
		if ( substr($route, 0, strlen(DIR_TEMPLATE)) == DIR_TEMPLATE ) {
			$route = substr($route, strlen(DIR_TEMPLATE));
		}
		return $route;
	}
  
  public function getPathToMainJS() {
		return $this->getResourceLinkWithVersionCatalog( $this->getResourceRouteCatalog('liveopencart.live_price.js'));
	}
	
	public function getPathToCustomJS() {
    return $this->getResourceLinkWithVersionIfExistsCatalog( $this->getResourceRouteCatalogCurrentTheme('code.js'));
		//return $this->getResourceLinkWithVersion( $this->getDirOfCurrentTtheme().'code.js');
		//return $this->getScriptPathWithVersion('view/theme/extension_liveopencart/live_price/theme/'.$this->getThemeName().'/code.js');
	}
  
  protected function getAdaptedThemes() {
		
		$dir_of_themes = $this->getBasicDirOfThemes();
		
		$themes = glob($dir_of_themes . '*' , GLOB_ONLYDIR);
		$themes = array_map( 'basename', $themes );
		
		if ( ($default_key = array_search('default', $themes)) !== false ) {
			unset($themes[$default_key]);
		}
		
		usort($themes, function($a, $b) {
			return strlen($b) - strlen($a);
		});
		
		return $themes;
	}
  
  public function getSettings() {
    $old_setting_key = 'liveprice_settings';
    $new_setting_key = 'module_liveprice_settings';
    if ( !empty($this->config->get($old_setting_key)) && empty($this->config->get($new_setting_key)) ) {
      $settings = $this->config->get($old_setting_key);
    } else {
      $settings = $this->config->get($new_setting_key);
    }
    
    return $settings ? $settings : array();
  }
  
  public function getSetting($key, $default_value=false) {
    $settings = $this->getSettings();
    return isset($settings[$key]) ? $settings[$key] : $default_value;
  }
  
  public function getProductPageAdditionalData() {
		
		$data = array();
		
		$data['liveprice_installed'] = $this->installed();
				
		if ( $data['liveprice_installed'] ) {
		
			$lp_product_id = 0;
			if ( isset($this->request->get['product_id']) ) {
				$lp_product_id = $this->request->get['product_id'];
			} elseif ( isset($this->request->post['product_id']) ) {
				$lp_product_id = $this->request->post['product_id'];
			} elseif ( isset($this->request->get['pid']) ) {
				$lp_product_id = $this->request->get['pid'];
			} elseif ( isset($this->request->get['id']) ) {
				$lp_product_id = $this->request->get['id'];
			}
			
			$data['lp_product_id'] = $lp_product_id; // in some cases it is needed even without correct product_id
			$data['lp_theme_name'] = $this->getThemeName();
			$data['liveprice_settings'] = $this->config->get('liveprice_settings');
		
		}
		return $data;
	}
	
	public function getProductPageScripts() {
		
		$scripts = array();
		
		if ( $this->installed() ) {
			$liveprice_custom_js = $this->getPathToCustomJS();
			if ( $liveprice_custom_js ) {
				$scripts[] = $liveprice_custom_js;
			}
			$scripts[] = $this->getPathToMainJS();
		}
		
		return $scripts;
	}
  
  public function checkTables() {
		
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` WHERE field='price_prefix' ");
		if ( $query->num_rows && strtolower($query->row['Type']) == 'varchar(1)' ) {
			$this->db->query("ALTER TABLE `".DB_PREFIX."product_option_value` MODIFY `price_prefix` varchar(2) NOT NULL");
		}
    
		if ( $this->versionPRO() ) {
		
      $this->addTableColumnIfNotExists('product_discount', 'price_prefix', 'VARCHAR(1) NOT NULL');
      $this->addTableColumnIfNotExists('product_special', 'price_prefix', 'VARCHAR(1) NOT NULL');
      
      $this->db->query(
          "CREATE TABLE IF NOT EXISTS
            `" . DB_PREFIX . "liveprice_global_discount` (
              `category_id` int(11) NOT NULL,
              `manufacturer_id` int(11) NOT NULL,
              `customer_group_id` int(11) NOT NULL,
              `quantity` int(4) NOT NULL,
              `priority` int(5) NOT NULL,
              `price_prefix` VARCHAR(1) NOT NULL,
              `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
              `date_start` date NOT NULL DEFAULT '0000-00-00',
              `date_end` date NOT NULL DEFAULT '0000-00-00',
              `sort_order` int(11) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
      );
      
      $this->db->query(
          "CREATE TABLE IF NOT EXISTS
            `" . DB_PREFIX . "liveprice_global_special` (
              `category_id` int(11) NOT NULL,
              `manufacturer_id` int(11) NOT NULL,
              `customer_group_id` int(11) NOT NULL,
              `priority` int(5) NOT NULL,
              `price_prefix` VARCHAR(1) NOT NULL,
              `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
              `date_start` date NOT NULL DEFAULT '0000-00-00',
              `date_end` date NOT NULL DEFAULT '0000-00-00',
              `sort_order` int(11) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
      );
    }
  }
  
  public function getChangedViewOfDiscounts($discounts, $product_info) {
		if ( $this->installed() ) {
			$data_discounts = array();
			$mod_settings = $this->config->get('liveprice_settings');
			if ( !empty($mod_settings['percent_discount_to_total']) ) { 
				foreach ($discounts as $discount) {
					if ( empty($discount['price_prefix']) || $discount['price_prefix'] == '=' ) {
						$data_discounts[] = array(
							'quantity' => $discount['quantity'],
							'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
						);
					} else {
						$data_discounts[] = array(
							'quantity' => $discount['quantity'],
							'price'    => ''.$discount['price_prefix'].' '.(float)$discount['price']
						);
					}
				}
			}
			return $data_discounts;
		}
	}
  
  public function modifyProductSpecialQuery($p_sql) {
		
		$sql_special = "
			SELECT PS.product_id, PS.customer_group_id, PS.date_start, PS.date_end, PS.priority, PS.price
			FROM ".DB_PREFIX."product_special PS
		UNION
			SELECT P.product_id, PGS.customer_group_id, PGS.date_start, PGS.date_end, PGS.priority, PGS.price
			FROM ".DB_PREFIX."liveprice_global_special PGS, ".DB_PREFIX."product P
			WHERE (P.manufacturer_id = PGS.manufacturer_id OR PGS.manufacturer_id = -1)
				AND (P.product_id IN (SELECT PTC.product_id FROM ".DB_PREFIX."product_to_category PTC WHERE PTC.category_id = PGS.category_id ) OR PGS.category_id = -1)
		";
		
		$sql = str_replace("FROM ".DB_PREFIX."product_special ps", "FROM (".$sql_special.") ps", $p_sql);
		
		return $sql;
	}
  
  public function changeOptionPriceFormat($price, $option_value) {
		$result = $price;
		if ( in_array($option_value['price_prefix'], array('*','/','%','+%','-%','=%')) && (!isset($option_value['hide']) || !$option_value['hide'] ) ) {
			// special way
			$result = (float)$option_value['price'];
		}
		return $result;
	}
  
}