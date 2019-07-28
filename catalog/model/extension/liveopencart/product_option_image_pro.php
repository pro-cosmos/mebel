<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ModelExtensionLiveopencartProductOptionImagePro extends Model {
  
	private $theme_name = '';
	private $installed = null;
	private $cache_product_option_settings = array();
	private $theme_methods = null;
	
  public function getThemeName() {
		
		if (!$this->theme_name) {
			$theme_name = '';
			
			$settings = $this->getSettings();
			if ( !empty($settings['custom_theme_id']) ) {
				$theme_name = $settings['custom_theme_id'];
			} else {
			
				if ($this->config->get('config_theme') == 'theme_default' || $this->config->get('config_theme') == 'default') {
					$theme_name = $this->config->get('theme_default_directory');
				} else {
					$theme_name = substr($this->config->get('config_theme'), 0, 6) == 'theme_' ? substr($this->config->get('config_theme'), 6) : $this->config->get('config_theme') ;
				}
				
				// shorten theme name
				$themes_shorten = $this->getAdaptedThemes();
				foreach ( $themes_shorten as $theme_shorten ) {
					$theme_shorten_length = strlen($theme_shorten);
					if ( substr($theme_name, 0, $theme_shorten_length) == $theme_shorten ) {
						$theme_name = substr($theme_name, 0, $theme_shorten_length);
						break;
					}
				}
				
				$theme_name = $this->replaceThemeNameIfSibling($theme_name);
				
			}
			$this->theme_name = $theme_name;
		}
		return $this->theme_name;
  }
	
	protected function replaceThemeNameIfSibling($theme_name) {
		$sibling_file_name = $this->getBasicDirOfExtension().'theme_sibling/'.$theme_name.'.php';
		if ( file_exists($sibling_file_name) ) {
			require($sibling_file_name); // $sibling_main_theme should be defined there
			if ( !empty($sibling_main_theme) ) {
				return $sibling_main_theme;
			}
		}
		return $theme_name;
	}
	
	protected function getAdaptedThemes() {
		
		$dir_of_themes = $this->getBasicDirOfTemplates();
		
		$themes = glob($dir_of_themes . '*' , GLOB_ONLYDIR);
		if ( $themes ) {
			$themes = array_map( 'basename', $themes );
			
			if ( ($default_key = array_search('default', $themes)) !== false ) {
				unset($themes[$default_key]);
			}
			
			usort($themes, function($a, $b) {
				return strlen($b) - strlen($a);
			});
			return $themes;
		} else {
			return array();
		}
		
	}
	
	public function getBasicDirOfExtension() {
		return DIR_TEMPLATE.'extension_liveopencart/product_option_image_pro/';
	}
	public function getBasicDirOfTemplates() {
		return $this->getBasicDirOfExtension().'theme/';
	}
	public function getDirOfCurrentTemplate() {
		$theme_dir = $this->getBasicDirOfTemplates().$this->getThemeName().'/';
		if ( !file_exists($theme_dir) || !is_dir($theme_dir) ) {
			$theme_dir = $this->getBasicDirOfTemplates().'default/';
		} 
		return $theme_dir;
	}
	
	
	private function getThemeMethods() {
		if ( is_null($this->theme_methods) ) {
			$this->theme_methods = false;
			$file_name = $this->getDirOfCurrentTemplate().'theme_methods.php';
			if ( file_exists($file_name) ) {
				require_once($file_name);
				
				$this->theme_methods = new theme_methods($this->registry);
			}
		}
		return $this->theme_methods;
	}
	
	private function hasThemeMethod($method_name) {
		return $this->getThemeMethods() && method_exists($this->getThemeMethods(), $method_name);
	}
	
	private function getThemeSettings() {
		if ( !$this->liveopencart_poip->hasCachedValue('theme_settings') ) {
			if ( $this->hasThemeMethod('getSettings') ) {
				$theme_settings = $this->getThemeMethods()->getSettings();
			} else {
				$file_name = $this->getDirOfCurrentTemplate().'theme_settings.php';
				if ( file_exists($file_name) ) {
					require_once($file_name);
					$theme_settings = $_;
				} else {
					$theme_settings = array();
				}
			}
			$this->liveopencart_poip->setCachedValue('theme_settings', $theme_settings);
		}
		return ( $this->liveopencart_poip->getCachedValue('theme_settings') );
	}
	
	private function getThemeSetting($setting_key) {
		
		$theme_settings = $this->getThemeSettings();
		if ( isset($theme_settings[$setting_key]) ) {
			return $theme_settings[$setting_key];
		}
	}
  
  private function render($route, $data) {
		// $this->registry is added for compatibility with d_twig_manager.xml
		$template = new Template($this->registry->get('config')->get('template_engine'), $this->registry);
				
		foreach ($data as $key => $value) {
			$template->set($key, $value);
		}
		
		$classMethod = new ReflectionMethod($template,'render');
		if ( count($classMethod->getParameters()) > 2 )  { // for some mods ($route, $registry, $cache=false)
			$output = $template->render( $route, $this->registry );
		} else { // std
			$output = $template->render( $route );
		}
		//$output = $template->render( $route );
		
		return $output;
	}

	
	public function addExtraDataToProductOptionValues($product_id, $p_product_option_values) {
		$product_option_values = $p_product_option_values;
		
		if ( $this->liveopencart_poip->installed() ) {
			$product_options_images = $this->liveopencart_poip->getModel()->getProductOptionImages($product_id, false, false);
			$product_options_settings = $this->liveopencart_poip->getProductSettings($product_id);
			
			foreach ($product_option_values as &$product_option_value) {
				if ( isset($product_options_settings[(int)$product_option_value['product_option_id']]['img_first']) && $product_options_settings[(int)$product_option_value['product_option_id']]['img_first'] ) {
					if ( isset($product_options_images[(int)$product_option_value['product_option_id']][$product_option_value['product_option_value_id']][0]['image'])) {
						$product_option_value['image'] = $product_options_images[(int)$product_option_value['product_option_id']][$product_option_value['product_option_value_id']][0]['image'];
					}
				}
				if ( isset($product_options_settings[(int)$product_option_value['product_option_id']]['img_radio_checkbox']) && $product_options_settings[(int)$product_option_value['product_option_id']]['img_radio_checkbox'] ) {
					
					$product_option_value['poip_image'] = $product_option_value['image'];
				
					if ( isset($product_options_settings[(int)$product_option_value['product_option_id']]['img_first']) && $product_options_settings[(int)$product_option_value['product_option_id']]['img_first'] ) {
						if ( isset($product_options_images[(int)$product_option_value['product_option_id']][$product_option_value['product_option_value_id']][0]['image'])) {
							$product_option_value['poip_image'] = $product_options_images[(int)$product_option_value['product_option_id']][$product_option_value['product_option_value_id']][0]['image'];
						}
					}
				}
			}
			unset($product_option_value);
		}
		return $product_option_values;
	}
	
	
	
	/*
	public function getTemplateIncludeFileName($suffix, $default_theme=false) {
		
		if ( $default_theme ) {
			$theme_name = '_default';
		} else { // custom
			$theme_name = $this->getThemeName();
			if ($theme_name == '_default') { // reserved name for basic file
				return '';
			}
		}
		$file_name = DIR_APPLICATION.'view/extension/product_option_image_pro/'.$theme_name.'.'.$suffix.'.tpl';
		if ( file_exists($file_name) ) {
			return $file_name;
		}
		return '';
	}
	*/
	
	private function getLinksForResources($scripts) {
		$results = [];
		foreach ( $scripts as $script ) {
			$results[] = $this->liveopencart_poip->getResourceLinkPathWithVersion($script, 'catalog/');
		}
		return $results;
	}
	
	public function getHeaderScripts() {
		$scripts = array(	'view/theme/extension_liveopencart/product_option_image_pro/liveopencart.poip_common.js',
											'view/theme/extension_liveopencart/product_option_image_pro/liveopencart.poip_list.js',
										);
		$custom_script = 'view/theme/extension_liveopencart/product_option_image_pro/theme/'.$this->getThemeName().'/theme_list.js';
		if ( file_exists( DIR_APPLICATION.$custom_script ) ) {
			$scripts[] = $custom_script;
		}
		return $this->getLinksForResources($scripts);
	}
  
	public function addHeaderResources() {
		$this->addScripts( $this->getHeaderScripts() );
	}
	
	public function getProductPageScripts() {
		$scripts = array();
		if ( $this->liveopencart_poip->installed() ) {
			$scripts[] = 'view/theme/extension_liveopencart/product_option_image_pro/liveopencart.poip_product.js';
			
			$custom_script = 'view/theme/extension_liveopencart/product_option_image_pro/theme/'.$this->getThemeName().'/theme_product.js';
			if ( file_exists( DIR_APPLICATION.$custom_script ) ) {
				$scripts[] = $custom_script;
			}

		}
		return $this->getLinksForResources($scripts);
	}
	
	public function addProductPageResources() {
		$this->addScripts( $this->getProductPageScripts() );
	}
	
	private function addScripts($scripts_links) {
		if ( $this->liveopencart_poip->installed() ) {
			foreach ( $scripts_links as $script_link ) {
				$this->document->addScript( $script_link );
				//$this->document->addScript( $this->liveopencart_poip->getResourceLinkPathWithVersion($script, 'catalog/') );
			}
		}
	}
	
	private function getProductMainImage($p_product_id) {
		$query = $this->db->query("SELECT `image` FROM `".DB_PREFIX."product` WHERE `product_id` = '".(int)$p_product_id."' ");
		if ( $query->num_rows ) {
			return $query->row['image'];
		}
		return '';
	}
  
  private function addMainProductImageToAdditionalIfNeeded($product_id, $product_images) {
  //public function addMainProductImageToAdditional($product_id, &$product_images) {
  
    //$product_images = &$product_images_ref_arr[0];
    
    if ($this->liveopencart_poip->installed()) {
			
      $poip_settings = $this->getSettings();
      
      if ( !empty($poip_settings['img_main_to_additional']) ) {
				
				if ( $poip_settings['img_main_to_additional'] == 1 || ( $poip_settings['img_main_to_additional'] == 2 && $product_images ) ) {
				
        // if there's no the main image in the list of additional images, let's add it
				
					$product_main_image = $this->getProductMainImage($product_id);
					if ( $product_main_image ) {
					//$product_info = $this->model_catalog_product->getProduct($product_id);
					//if (isset($product_info['image']) && trim($product_info['image']) != "" ) {
						$have_image = false;
						foreach ($product_images as $product_image) {
							if ($product_image['image'] == $product_main_image) {
								$have_image = true;
								break;
							}
						}
						if (!$have_image) {
							array_unshift($product_images, array('product_id'=>$product_id, 'image'=>$product_main_image, 'sort_order'=>0, 'product_image_id'=>"-1"));
						}
					}
				}	
      }
    }
    
    return $product_images;
    
  }
	
	private function removeMainProductImageFromAdditionalIfNeeded($results) {
		if ( $this->getThemeSetting('do_not_add_main_image_to_additional_images') ) {
			$new_results = array();
			foreach ( $results as $result ) {
				if ( !(isset($result['product_image_id']) && $result['product_image_id'] == -1) ) {
					$new_results[] = $result;
				}
			}
		} else {
			$new_results = $results;
		}
		return $new_results;
	}
	
	public function getDataForProductPage($product_id, $p_results=array()) {
		
		$results = $this->addMainProductImageToAdditionalIfNeeded($product_id, $p_results);
		
		$data['poip_installed'] = $this->liveopencart_poip->installed();
		//$data['poip_inclide_file_name_default'] = $this->getTemplateIncludeFileName('product', true);
		//$data['poip_inclide_file_name_custom'] = $this->getTemplateIncludeFileName('product');
		
		$data['poip_theme_name'] = $this->getThemeName();
		
		//$data['current_class'] = 'related_products';
		if ( $data['poip_installed'] ) {
			$data['poip_product_settings'] = $this->liveopencart_poip->getProductSettings($product_id);
			$data['poip_settings'] = $this->getSettings();
			
			$poip_results = $this->addOptionImagesToProductImages($results, $product_id, array() );
			
			if ( !$results ) { // fill with option images only if the array additional images is initially empty
				$results = $poip_results['results']; 
			} else {
				// normally leave only standard/initial images, but with all additional data
				// but if the specific theme setting is enabled, then add all images
				if ( $this->getThemeSetting('add_option_images_to_additional_on_server_side') ) {
					//$new_results = array();
					$result_images = array();
					foreach ( $results as $result ) {
						$result_images[] = $result['image'];
					}
					foreach ( $poip_results['results'] as $result) {
						if ( !in_array($result['image'], $result_images) ) {
							$results[] = $result;
							//$new_results[] = $result;
						}
					}
					//$results = $new_results;
					
				} 
				/*
				$new_results = array();
				$result_images = array();
				foreach ( $results as $result ) {
					$result_images[] = $result['image'];
				}
				foreach ( $poip_results['results'] as $result) {
					if ( in_array($result['image'], $result_images) ) {
						$new_results[] = $result;
					}
				}
				$results = $new_results;
				*/
			}
			
			$product_images = $poip_results['product_images'];
			
			$data['poip_images'] = $product_images;
			$data['poip_product_option_ids'] = $this->getProductOptionsIdsWithImages($product_images);
			$data['poip_images_by_povs'] = $this->getProductOptionImagesByValues($product_id);
			
			// for some themes using twig file for custom scripts
			$poip_theme_script_route = 'extension_liveopencart/product_option_image_pro/theme/'.$this->getThemeName().'/theme_product';
			if ( file_exists( DIR_TEMPLATE.$poip_theme_script_route.'.twig' ) ) {
				$data['poip_theme_script'] = $this->render($poip_theme_script_route, $data);
			}
			
			// for old-style themes using .tpl engine (like Fastor)
			$poip_include_tpl = DIR_APPLICATION.'view/theme/extension_liveopencart/product_option_image_pro/theme/'.$this->getThemeName().'/theme_product.tpl';
			if ( file_exists( $poip_include_tpl ) ) {
				$data['poip_include_tpl'] = $poip_include_tpl;
			}
			
			$results = $this->removeMainProductImageFromAdditionalIfNeeded($results);
			
			$poip_ov = $this->getPOIPIdFromRequest();
			if ( $poip_ov ) {
				$data['poip_ov'] = $poip_ov;
			}
			
		}
		
		
		
		return array('data'=>$data, 'results'=>$results);
	}
	
	public function getProductOptionValuePOIPThumbResized($option_value) {
		
		if ( !empty($option_value['poip_image']) ) {
			return $this->model_tool_image->resize($option_value['poip_image'], 50, 50);
		}
		return '';
	}
	
	public function getPOIPIdFromRequest() {
		$poip_ov = false;
		if (isset($_GET['poip_ov'])) {
			if ($_GET['poip_ov']) {
				$poip_ov = $_GET['poip_ov'];
			}
		} elseif ( isset($_SERVER['REQUEST_URI']) ) {
		
			$poip_ov_name = "poip_ov=";
			if (strpos($_SERVER['REQUEST_URI'], $poip_ov_name) !== false) {
			
				$poip_str = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], $poip_ov_name)+strlen($poip_ov_name));
				$poip_ov = "";
				while ($poip_str != "" && strpos("0123456789", substr($poip_str, 0, 1))!==false ) {
					$poip_ov.= substr($poip_str, 0, 1);
					$poip_str = substr($poip_str, 1);
				}
				if ($poip_ov != "") {
					$poip_ov = $poip_ov;
				}
			}
		}
		return $poip_ov;
	}
	
  // module settings
  public function getSettings() {
    
    $poip_settings = $this->config->get('poip_module');
    
    return $poip_settings;
    
  }

  /*
  public function getSettingsNames($for_product=true) {
    $settings = array();
    $settings[] = "img_change";
    if (!$for_product) {
      $settings[] = "img_hover";
    }
    $settings[] = "img_use";
    $settings[] = "img_limit";
    if (!$for_product) {
      $settings[] = "img_gal";
    }
    $settings[] = "img_option";
    $settings[] = "img_category";
    $settings[] = "img_first";
    $settings[] = "img_cart";
    $settings[] = "img_radio_checkbox";
    $settings[] = "dependent_thumbnails";
    
    return $settings;
  }
  */
  

	  
	/*
  // exact (determined) options settings
  public function getProductOptionSettings($product_option_id) {
    
    $option_settings = array();
    if (!$this->liveopencart_poip->installed()) return $option_settings;
		
		if ( !isset($this->cache_product_option_settings[$product_option_id]) ) {
    
			$poip_settings = $this->config->get('poip_module');
			$poip_option_settings = $this->getOptionSettings($product_option_id);
			
			$query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_option_settings WHERE product_option_id = ".(int)$product_option_id." ");
			
			$settings_names = $this->getSettingsNames();
			
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
			
			$this->cache_product_option_settings[$product_option_id] = $option_settings;
		}
	
		return $this->cache_product_option_settings[$product_option_id];
    
  }
  
  // all product settings (for all options)
  public function getProductSettings($product_id) {
    $query = $this->db->query("SELECT product_option_id FROM ".DB_PREFIX."product_option WHERE product_id = ".(int)$product_id."  ");
    $poip_settings = array();
    foreach ($query->rows as $row) {
      $poip_settings[$row['product_option_id']] = $this->getProductOptionSettings($row['product_option_id']);
    }
    return $poip_settings;
  }
  */
  
  
  // if other sort order neede, it should be options (may be needed for categories pages)
  public function getProductOptionImages($product_id, $return_table=false, $stock_control=true) {
    
    $images = array();
    
    if (!$this->liveopencart_poip->installed()) return $images;
    
    $language_id = (int)$this->config->get('config_language_id');
    
    $poip_settings = $this->liveopencart_poip->getProductSettings($product_id);
    
    // add standard options image into option value images list (if needed)
    $query = $this->db->query(" SELECT POV.*, OV.image, OD.name option_name, OVD.name value_name
                                FROM ".DB_PREFIX."product_option_value POV
                                    LEFT JOIN ".DB_PREFIX."option_description OD ON (POV.option_id = OD.option_id AND OD.language_id = ".$language_id.")
                                  , ".DB_PREFIX."option_value OV 
                                    LEFT JOIN ".DB_PREFIX."option_value_description OVD ON (OVD.option_value_id = OV.option_value_id AND OVD.language_id = ".$language_id.")
                                  
                                WHERE POV.option_value_id = OV.option_value_id
                                  AND POV.product_id = ".(int)$product_id."
                                ORDER BY OV.sort_order ASC  
                                ");
    $all_product_option_images = array();
    foreach ($query->rows as $row) {
      if ( isset($poip_settings[$row['product_option_id']]['img_first']) && $poip_settings[$row['product_option_id']]['img_first'] == 2 ) {
        if ($row['image'] != 'no_image.jpg' && trim($row['image']) != '' && file_exists(DIR_IMAGE.$row['image']) ) {
          $all_product_option_images[] = array( 'option_name'=>$row['option_name']
                                               ,'value_name'=>$row['value_name']
                                               ,'product_id'=>$row['product_id']
                                               ,'product_option_id'=>$row['product_option_id']
                                               ,'product_option_value_id'=>$row['product_option_value_id']
                                               ,'image'=>$row['image']
                                               ,'sort_order'=>0
                                               ,'thumb'=>$row['image']
                                               ,'option_id'=>$row['option_id']
                                               ,'option_value_id'=>$row['option_value_id']
                                               );
        }
      }
    }
		
		$query = $this->db->query(" SELECT POIP.*, OV.image thumb, OD.name option_name, OVD.name value_name, PO.option_id, POV.option_value_id
                                  FROM ".DB_PREFIX."poip_option_image POIP
																				LEFT JOIN ".DB_PREFIX."product_option_value POV ON ( POIP.product_option_value_id = POV.product_option_value_id )
                                      	LEFT JOIN ".DB_PREFIX."option_value OV ON (POV.option_value_id = OV.option_value_id)
																				LEFT JOIN ".DB_PREFIX."option_value_description OVD ON (OVD.option_value_id = OV.option_value_id AND OVD.language_id = ".$language_id.")
                                      ,`".DB_PREFIX."option` O LEFT JOIN ".DB_PREFIX."option_description OD ON (OD.option_id = O.option_id AND OD.language_id = ".$language_id.")
                                      ,".DB_PREFIX."product_option PO
                                WHERE POIP.product_id = ".(int)$product_id."
                                  AND PO.product_option_id = POIP.product_option_id
                                  AND PO.option_id = O.option_id
                                  ".($stock_control ? "AND ( (POV.product_option_value_id IS NULL) OR (NOT POV.subtract OR POV.quantity > 0) )" : "")."
                                ORDER BY O.sort_order ASC, POIP.product_option_id ASC, OV.sort_order ASC, POIP.product_option_value_id, POIP.sort_order ASC
                                ");
    
    $existing_images = array();
    foreach ( $query->rows as $row ) {
			
			if ( $row['product_option_value_id'] == 0 ) {
				$row['product_option_value_id'] = -$row['product_option_id']; // display image when the options is not selected ("no value"), leave pov_id=0 does not fit, it should be unique
			}
			
      if ($row['image'] != 'no_image.jpg' && trim($row['image']) != '' && file_exists(DIR_IMAGE.$row['image']) ) {
        $existing_images[] = $row;
      }
    }
		
    $all_product_option_images = array_merge($all_product_option_images, $existing_images);
    //$all_product_option_images = array_merge($all_product_option_images, $query->rows);
    
    // use first image as icon/thumb (if needed)
    $thumbs = array();
    foreach ($all_product_option_images as $row) {
      if (!isset($thumbs[$row['product_option_value_id']])) {
        if ( isset($poip_settings[$row['product_option_id']]['img_first']) && $poip_settings[$row['product_option_id']]['img_first'] ) {
          $thumbs[$row['product_option_value_id']] = $row['image'];
        } else {
          $thumbs[$row['product_option_value_id']] = $row['thumb'];
        }
      }
    }
    
    if ($return_table) return $all_product_option_images;
    
    foreach ($all_product_option_images as $row) {
      if (!isset($images[$row['product_option_id']])) {
        $images[$row['product_option_id']] = array();
      }
      if (!isset($images[$row['product_option_id']][$row['product_option_value_id']])) {
        $images[$row['product_option_id']][$row['product_option_value_id']] = array();
      }
      $images[$row['product_option_id']][$row['product_option_value_id']][] = array(  'image'=>$row['image']
                                                                                    , 'thumb'=>( (isset($thumbs[$row['product_option_value_id']]) && $thumbs[$row['product_option_value_id']]) ? $thumbs[$row['product_option_value_id']] : 'no_image.jpg')
                                                                                    , 'srt'=>$row['sort_order']
                                                                                    , 'product_option_id'=>$row['product_option_id']
                                                                                    , 'option_id'=>$row['option_id']
                                                                                    , 'option_value_id'=>$row['option_value_id']
                                                                                    , 'option_name'=> (isset($row['option_name']) && $row['option_name']) ? $row['option_name'] : ''
                                                                                    , 'value_name'=> (isset($row['value_name']) && $row['value_name']) ? $row['value_name'] : ''
                                                                                    );
    }
		
    return $images;
    
  }
  
  public function getProductOptionImagesByValues($product_id) {
    
    $images = array();
    
    if (!$this->liveopencart_poip->installed()) return $images;
    
    $product_images = $this->getProductOptionImages($product_id);
    foreach ($product_images as $product_option_id => $po_images) {
      
      if ($po_images && is_array($po_images)) {
        foreach ($po_images as $product_option_value_id => $pov_images) {
          $images[$product_option_value_id] = $pov_images;
        }
      }
      
    }
    
    return $images;
    
  }
	
	protected function getArrayColumn($array, $subkey) { // old PHP does not have the function array_column
		$result = array();
		foreach ( $array as $key => $val ) {
			$result[$key] = isset($val[$subkey]) ? $val[$subkey] : null;
		}
		return $result;
	}
	
	protected function getArrayValueBySubKeyValue($array, $subkey, $subval) {
		$key = array_search($subval, $this->getArrayColumn($array, $subkey));
		if ( $key !== false ) {
			return $array[$key];
		}
	}
  
  // $product_images_old - standard product images array
	// $product_id
	// $product_images - empty array
  public function addOptionImagesToProductImages($product_images_old, $product_id, $product_images, $sizes=array()) {
  //public function addOptionImagesToProductImages($product_images_old, $product_id, &$product_images, $popup_width=0, $popup_height=0) {
		
    $poip_settings = $this->getSettings();
    
    $product_images = array();
    $added_images = array();
		
    $options_images = $this->getProductOptionImages($product_id, true);
		
    if ( isset($poip_settings['options_images_edit']) && $poip_settings['options_images_edit'] == 1 ) {
      // add in product images order
			
      foreach ($product_images_old as $image_old) {
        
				$image_is_added_by_option = false;
        foreach ($options_images as $row) {
					
          if ($image_old['image'] == $row['image']) {
						
						$image_is_added_by_option = true;
			
            if ( !in_array($row['image'], $added_images) ) {
							$product_image = $image_old;
							$product_image['sort_order'] = $row['sort_order'];
							$product_images[] = $product_image;
              $added_images[] = $row['image'];
						}
						
            foreach ( $product_images as &$image ) {
              if ($image['image'] == $row['image']) {
                if (!isset($image['product_option_id'])) $image['product_option_id'] = array();
                if ( !isset($image['product_option_value_id']) || !is_array($image['product_option_value_id']) ) $image['product_option_value_id'] = array();
                if (!in_array($row['product_option_id'], $image['product_option_id'])) {
                  $image['product_option_id'][] = $row['product_option_id'];
                }
                
                if ( !in_array($row['product_option_value_id'], $image['product_option_value_id']) ) {
                  $image['product_option_value_id'][] = $row['product_option_value_id'];
                  
                  if (isset($row['option_name']) && $row['option_name'] && isset($row['value_name']) && $row['value_name']) {
                    if (!isset($image['title'])) {
                      $image['title'] = '';
                    }
                    $image['title'] .= trim("\n".$row['option_name'].": ".$row['value_name']);
                  }
                }
                break;
              }
            }
            unset($image);
          }
        }
				if ( !$image_is_added_by_option ) {
					$product_images[] = $image_old;
					$added_images[] = $image_old['image'];
				}
      }
    }
    
    foreach ($options_images as $row) {
      
      if ( !in_array($row['image'], $added_images) ) {
				$product_image = $this->getArrayValueBySubKeyValue($product_images_old, 'image', $row['image']);
				if ( !$product_image ) {
					$product_image = array('product_id'=>$product_id, 'image'=>$row['image']);
				}
        $product_image['sort_order'] = $row['sort_order'];
				$product_images[] = $product_image;
        $added_images[] = $row['image'];
      }
      foreach ($product_images as &$image) {
        if ($image['image'] == $row['image']) {
          if (!isset($image['product_option_id'])) $image['product_option_id'] = array();
          if (!isset($image['product_option_value_id'])) $image['product_option_value_id'] = array();
          if (!in_array($row['product_option_id'], $image['product_option_id'])) {
            $image['product_option_id'][] = $row['product_option_id'];
          }
          
          if ( !in_array($row['product_option_value_id'], $image['product_option_value_id']) ) {
            $image['product_option_value_id'][] = $row['product_option_value_id'];
            
            if (isset($row['option_name']) && $row['option_name'] && isset($row['value_name']) && $row['value_name']) {
              if (!isset($image['title'])) {
                $image['title'] = '';
              }
              $image['title'] .= "\n".$row['option_name'].": ".$row['value_name'];
            }
          }
					break;
        }
      }
      unset($image);
      
    }
    
    
    //foreach ($product_images_old as $product_image) {
    foreach (array_reverse($product_images_old) as $product_image) {
      if (!in_array($product_image['image'], $added_images)) {
        array_unshift ($product_images, $product_image);
      }
    }
    
    $poip_product_settings = $this->liveopencart_poip->getProductSettings($product_id);
    
    $results = array();
    foreach ($product_images as &$result) {
      
      if (isset($result['product_option_id']) && is_array($result['product_option_id'])) {
        $show_image = false;
        foreach ($result['product_option_id'] as $product_option_id) {
          if (isset($poip_product_settings[$product_option_id]) && $poip_product_settings[$product_option_id]['img_use']) {
            $show_image = true;
            break;
          }
        }
      } else {
        $show_image = true;
      }
			
			if ( empty($sizes['popup_width']) || empty($sizes['popup_height']) ) {
				$result['popup'] = $this->image_resize($product_id, $result['image'], $this->getImageSizeSetting('popup_width'), $this->getImageSizeSetting('popup_height'), 'popup');
			} else {
				$result['popup'] = $this->image_resize($product_id, $result['image'], $sizes['popup_width'], $sizes['popup_height'], 'popup');
			}

			if ( empty($sizes['thumb_width']) || empty($sizes['thumb_height']) ) {
				$result['thumb'] = $this->image_resize($product_id, $result['image'], $this->getImageSizeSetting('additional_width'), $this->getImageSizeSetting('additional_height'), 'thumb');
			} else {
				$result['thumb'] = $this->image_resize($product_id, $result['image'], $sizes['thumb_width'], $sizes['thumb_height'], 'thumb');
			}
			
			if ( empty($sizes['main_width']) || empty($sizes['main_height']) ) {
				$result['main'] = $this->image_resize($product_id, $result['image'], $this->getImageSizeSetting('thumb_width'), $this->getImageSizeSetting('thumb_height'), 'main');
			} else {
				$result['main'] = $this->image_resize($product_id, $result['image'], $sizes['main_width'], $sizes['main_height'], 'main');
			}
			
      $result['option_thumb'] = $this->image_resize($product_id, $result['image'], 50, 50, 'thumb');
      
      if ($show_image) {  
        $results[] = $result;
      }
    }
    unset($result);
		
		$results = $this->addVideoIfNeeded($product_images_old, $results);
    
    return array('results'=>$results, 'product_images'=>$product_images);
      
  }
	
	// some other modules may require 'video' element in image array
	private function addVideoIfNeeded($product_images_old, $results) {
		
		// let's think that it's always needed
		$videoNeeded = true;
		/*
		$videoNeeded = false;
		foreach ($product_images_old as $img) {
			if (isset($img['video'])) {
				$videoNeeded = true;
				break;
			}
		}
		*/
		if ($videoNeeded) {
			foreach ($results as &$result) {
				if (!isset($result['video'])) {
					$result['video'] = '';
				}
			}
			unset($result);
		}
		return $results;
	}
  
  public function image_resize($product_id, $image, $width, $height, $image_type='') {
		
		if ( $this->hasThemeMethod('resize') ) {
			return $this->getThemeMethods()->resize($image, $width, $height);
		}
		
    return $this->model_tool_image->resize($image, $width, $height);
    
  }
  
  public function getProductOptionsIdsWithImages($results) {
    
    $ids = array();
    
    foreach ($results as $result) {
      if (isset($result['product_option_id']) && is_array($result['product_option_id'])) {
        foreach ($result['product_option_id'] as $product_option_id) {
          if (!in_array($product_option_id, $ids)) {
            $ids[] = $product_option_id;
          }
        }
      }
    }
    
    return $ids;
  }
	
	//public function 
	/*
	private function themeHasTemplateForProductLists() {
		if ( is_null($this->themeHasTemplateForProductLists) ) {
			$template_file = $this->getTemplateIncludeFileName('list');
			$this->themeHasTemplateForProductLists = $template_file ? true : false;
		}
		return $this->themeHasTemplateForProductLists;
	}
	*/
  
	public function getCategoryImagesForController($product_id, $module_setting=false) { // returns images only if it is needed (old style option images in product lists displaying - not trough additional ajax call)
		// always return option images in hope to do not make extra ajax calls
		return $this->getCategoryImages($product_id, $module_setting);
		/*
		if ( !$this->themeHasTemplateForProductLists() ) {
			return $this->getCategoryImages($product_id, $module_setting);
		} else {
			return array();
		}
		*/
	}
	
	private function getImageSizeSetting($setting_name) {
		return $this->config->get( 'theme_' . $this->config->get('config_theme') . '_image_'.$setting_name );
	}
  
  public function getCategoryImages($product_id, $module_setting=false, $return_ordered=false) { // $return_ordered currently not used
    
    if (!$this->liveopencart_poip->installed()) return false;
    
		if ( !$this->model_tool_image ) {
			$this->load->model('tool/image');
		}
		
		if ( !empty($module_setting) && isset($module_setting['width']) && isset($module_setting['height'])  ) {
				
			$image_product_width = $module_setting['width'];
			$image_product_height = $module_setting['height'];  
		} elseif ($module_setting == "related_products") {
			$image_product_width = $this->getImageSizeSetting('related_width');
			$image_product_height = $this->getImageSizeSetting('related_height');
		} else {
			$image_product_width = $this->getImageSizeSetting('product_width');
			$image_product_height = $this->getImageSizeSetting('product_height');
		}
		
		$poip_settings = $this->config->get('poip_module');
		
		if ( !empty($poip_settings['custom_thumb_width']) && (int)$poip_settings['custom_thumb_width']>0 && !empty($poip_settings['custom_thumb_height']) && (int)$poip_settings['custom_thumb_height']>0 ) {
			
			$icon_width = (int)$poip_settings['custom_thumb_width'];
			$icon_height = (int)$poip_settings['custom_thumb_height'];
			
		} else {
			// base image size used for calculation is 120 (128 with magins/paddings), icon/thumb size is 24 (with margins/paddings 32)
			// (120)/4-6=24
			$icon_width = round(($image_product_width)/4-6);
			$icon_height = round(($image_product_height)/4-6);
		}
		
		$images = $this->getProductOptionImages($product_id);

		if ( !$this->model_catalog_product ) {
			$this->load->model('catalog/product');
		}
    
		// collect standard images of option values
		$basic_option_images = array();
		$product_options = $this->model_catalog_product->getProductOptions($product_id);
		foreach ( $product_options as $product_option ) {
			$product_option_id = $product_option['product_option_id'];
			$basic_option_images[$product_option_id] = array();
			if ( !empty($product_option['product_option_value']) ) {
				foreach ( $product_option['product_option_value'] as $product_option_value ) {
					if ( trim($product_option_value['image']) ) {
						$product_option_value_id = $product_option_value['product_option_value_id'];
						$basic_option_images[$product_option_id][$product_option_value_id] = $product_option_value['image'];
					}
				}
			}
		}
		
    $category_images = array();
		
		$product_settings = $this->liveopencart_poip->getProductSettings($product_id);
		
    foreach ($images as $product_option_id => $image_po) {
      //if (isset($product_settings[$product_option_id]) && $product_settings[$product_option_id]['img_category']) {
        
			if ( !empty($product_settings[$product_option_id]['img_category']) ) {
				
        foreach ($image_po as $product_option_value_id => $image_pov) {
          if (count($image_pov) > 0) {
            if (!isset($category_images[$product_option_id])) {
              $category_images[$product_option_id] = array();
            }
            //$image_pov[0];
						
						$option_icon = '';
						if ( !empty($basic_option_images[$product_option_id][$product_option_value_id]) ) {
							$option_icon = $basic_option_images[$product_option_id][$product_option_value_id];
						}
						if ( !$option_icon || !is_file(DIR_IMAGE.$option_icon) || ( !empty($product_settings[$product_option_id]['img_first']) && $product_settings[$product_option_id]['img_first'] == 1 ) ) {
							$option_icon = $image_pov[0]['image'];
						}
												
						$category_image_data = array(	'icon'=>$this->image_resize($product_id, $option_icon, $icon_width, $icon_height),
																					'thumb'=>$this->image_resize($product_id, $image_pov[0]['image'], $image_product_width, $image_product_height),
						//$category_image_data = array(	'icon'=>$this->model_tool_image->resize($option_icon, $icon_width, $icon_height),
						//															'thumb'=>$this->model_tool_image->resize($image_pov[0]['image'], $image_product_width, $image_product_height),
																					//'image'=>$image_pov[0]['image'],
																					//'option_id'=>$image_pov[0]['option_id'],
																					//'option_value_id'=>$image_pov[0]['option_value_id'],
																					'product_option_value_id'=>$product_option_value_id,
																					'width' => $icon_width,
																					'height' => $icon_height,
																				);
						//if ( $this->hasThemeMethod('getProductListImageExtras') ) {
						//	$category_image_data['extras'] = $this->getThemeMethods()->getProductListImageExtras($image_pov[0]['image'], $product_option_value_id);
						//}
						
						if (isset($image_pov[0]['option_name']) && $image_pov[0]['option_name'] && isset($image_pov[0]['value_name']) && $image_pov[0]['value_name'] ) {
              $category_image_data['title'] = "".$image_pov[0]['option_name'].": ".$image_pov[0]['value_name'];
            }
          
						if ($return_ordered) {
							$category_images[$product_option_id][] = $category_image_data;
						} else {
							$category_images[$product_option_id][$product_option_value_id] = $category_image_data;
						}   
          }
        }   
      }
    }
    return $category_images;
  }
  
   
  
  public function getProductCartImage($product_id, $option_data, $image) {
    
    if (!$this->liveopencart_poip->installed()) {
      return $image;
    }
    
    $selected_product_option = array();
    $selected_product_option_value = array();
    foreach ($option_data as $option_value_data) {
      if (!in_array($option_value_data['product_option_id'], $selected_product_option)) {
				$selected_product_option[] = $option_value_data['product_option_id'];
			}
      if (!in_array($option_value_data['product_option_value_id'], $selected_product_option_value)) {
				$selected_product_option_value[] = $option_value_data['product_option_value_id'];
			}
    }
    
    
    $product_images = $this->getProductOptionImages($product_id);
    if ( count($product_images) > 0 ) {
      
      $product_settings = $this->liveopencart_poip->getProductSettings($product_id);
      
      $cart_options = array();
      $filter_options = array();
      foreach ($product_images as $product_option_id => $product_option_images ) {
        
        if ( in_array($product_option_id, $selected_product_option) ) { // option value is selected
          
          $images_count = 0;
          
          foreach ($product_option_images as $product_option_value => $product_option_value_images) {
            $images_count = $images_count + count($product_option_value_images);
          }
          
          if ($images_count > 0) {
            if ( !empty($product_settings[$product_option_id]['img_cart']) ) {
              $cart_options[] = $product_option_id;
							/*
              if ($product_settings[$product_option_id]['img_limit']) {
								$filter_options[] = $product_option_id;
							}
							*/
            }
          }
        }
      }
			
			
			$images = false;
			$all_option_images = array();
			
			if ( $cart_options ) { 
      
				// for main image in the shopping cart always try to use filtering (get image relevant to all selected options)
        foreach ($product_images as $product_option_id => $product_option_images) {
          if ( in_array($product_option_id, $cart_options) ) {
            $current_images = array();
            foreach ( $product_images[$product_option_id] as $product_option_value_id => $product_option_value_images ) {
              if ( in_array($product_option_value_id, $selected_product_option_value) ) { // selected option value
                foreach ($product_option_value_images as $image_info) {
                  if (!in_array($image_info['image'], $current_images)) {
                    $current_images[] = $image_info['image'];
                  }
                }
              } 
            }
						
						$all_option_images = array_values( array_unique( array_merge($all_option_images, $current_images) ) );
            
            if (count($current_images) > 0) {
              if ($images === false) {
                $images = $current_images;
              } else {
                $images = array_values(array_intersect($images, $current_images));
              }
            }
            
          }
        }
				
				if ( !$images ) {
					$images = $all_option_images;
				}
					
        if ($images && count($images)>0) {
          $image = $images[0];
        }
        
      }
			
			/*
      if (count($filter_options)>0) {
				
        $images = false;
        foreach ($product_images as $product_option_id => $product_option_images) {
          if (in_array($product_option_id, $filter_options)) {
            $current_images = array();
            foreach ($product_images[$product_option_id] as $product_option_value_id => $product_option_value_images) {
              if ( in_array($product_option_value_id, $selected_product_option_value) ) { // selected option value
                foreach ($product_option_value_images as $image_info) {
                  if (!in_array($image_info['image'], $current_images)) {
                    $current_images[] = $image_info['image'];
                  }
                }
              } 
            }
            
            if (count($current_images) > 0) {
              if ($images === false) {
                $images = $current_images;
              } else {
                $images = array_values(array_intersect($images, $current_images));
              }
            }
            
          }
        }
        
        if ($images && count($images)>0) {
          $image = $images[0];
        }
        
      } elseif (count($cart_options)>0) { // get first option image
				
        $product_option_id = $cart_options[0];
        $image_set = false;
        foreach ($product_images[$product_option_id] as $product_option_value_id => $product_option_value_images) {
          if ( in_array($product_option_value_id, $selected_product_option_value) ) { // selected option value
            foreach ($product_option_value_images as $image_info) {
              $image = $image_info['image'];
              $image_set = true;
              break;
            }
          }
          if ( $image_set ) break;
        }
      }
      */
    }
    
    return $image;
    
  }
  
  
  
}