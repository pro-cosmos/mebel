<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

// model should be loaded only after the poip library (basically it should be loaded by the poip library object itself)
class ModelExtensionModuleProductOptionImagePro extends Model {
  
	public function __construct() {
		
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
	}
  
  public function deleteAllImages() {
    $this->db->query("DELETE FROM ".DB_PREFIX."poip_option_image ");
  }
  
  
  
  
  public function getOptionsForImages($product_id) {
    
    if ( !$this->liveopencart_poip->installed() ) return;
    
    $query = $this->db->query(" SELECT POIP.*, PO.option_id, POV.option_value_id, POIP.product_option_value_id
                                FROM  ".DB_PREFIX."poip_option_image POIP
																		LEFT JOIN ".DB_PREFIX."product_option_value POV ON (POIP.product_option_value_id = POV.product_option_value_id)
                                    , ".DB_PREFIX."product_option PO
                                    
                                WHERE POIP.product_id = ".(int)$product_id."
																	AND POIP.product_option_id = PO.product_option_id
                                ORDER BY sort_order ASC ");
    
    $images = array();
    foreach ($query->rows as $row) {
      
			if ( !$row['option_value_id'] && $row['product_option_value_id']!= 0 ) continue; // product_option_value_id = 0 - display for not selected option
			
			$image = $row['image'];
			$option_id = $row['option_id'];
			$option_value_id = $row['option_value_id'] ? $row['option_value_id'] : 0;
			
      if ( !isset($images[$image]) ) {
        $images[$image] = array();
      }
      if ( !isset($images[$image][$option_id]) ) {
        $images[$row['image']][$option_id] = array();
      }
      
      if ( !in_array($option_value_id, $images[$image][$option_id]) ) {
        $images[$image][$option_id][] = $option_value_id;
      }
      
    }
    
    return $images;
    
  }
  
  public function saveOptionsForImages($product_id, $product_images) {
    
    if (!$this->liveopencart_poip->installed()) return;
    $poip_global_settings = $this->getGlobalSettings();
    if ($poip_global_settings['options_images_edit'] != 1) return;
    
    $this->deleteProductOptionValueImages($product_id);
    
    $query = $this->db->query(" SELECT PO.product_option_id, PO.option_id, POV.product_option_value_id, POV.option_value_id
                                FROM  ".DB_PREFIX."product_option PO
                                    , ".DB_PREFIX."product_option_value POV
                                WHERE PO.product_id = ".(int)$product_id."
                                  AND POV.product_option_id = PO.product_option_id
                                ");
    $product_options = array();
    foreach ($query->rows as $row) {
      if ( !isset($product_options[$row['option_id']]) ) {
        $product_options[$row['option_id']] = array();
        $product_options[$row['option_id']]['values'] = array();
      }
      
      $product_options[$row['option_id']]['product_option_id'] = $row['product_option_id'];
      $product_options[$row['option_id']]['values'][$row['option_value_id']] = $row['product_option_value_id'];
			
      
    }
    
    foreach ($product_images as $product_image) {
      
      if ( isset($product_image['poip']) && $product_image['poip'] ) {
        
        foreach ($product_image['poip'] as $option_id => $option_values) {
          
          foreach ($option_values as $option_value_id) {
            
            if ( !empty( $product_options[$option_id]['values'][$option_value_id] ) || $option_value_id == 0 ) {
            
              $product_option_id = $product_options[$option_id]['product_option_id'];
							if ( $option_value_id == 0 ) {
								$product_option_value_id = 0;
							} else {
								$product_option_value_id = $product_options[$option_id]['values'][$option_value_id];
							}
						
              $this->saveProductOptionValueImages($product_id, $product_option_id, $product_option_value_id, array( array('image'=>$product_image['image'], 'srt'=>(int)$product_image['sort_order']) ));
            }
          }
        }
      }
    }
  }
  
  // new
  public function getGlobalSettings() {
    $poip_settings = $this->config->get('poip_module');
    $settings_names = $this->liveopencart_poip->getModuleSettingsIds(false);
    foreach ($settings_names as $setting_name) {
      if ( !isset($poip_settings[$setting_name]) ) {
        $poip_settings[$setting_name] = 0;
      }
    }
    return $poip_settings;
  }
  
  /*
  // exact (determined) option settings
  public function getProductOptionSettings($product_option_id) {
    
    $option_settings = array();
    if (!$this->liveopencart_poip->installed()) return $option_settings;
    
    $poip_settings = $this->config->get('poip_module');
    $poip_option_settings = $this->getOptionSettings($product_option_id);
    
    $query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_option_settings WHERE product_option_id = ".(int)$product_option_id." ");
    
    $settings_names = $this->liveopencart_poip->getModuleSettingsIds();
    
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
    
    return $option_settings;
    
  }
  
  // all product settings together (for all options)
  public function getProductSettings($product_id) {
    $query = $this->db->query("SELECT product_option_id FROM ".DB_PREFIX."product_option WHERE product_id = ".(int)$product_id."  ");
    $poip_settings = array();
    foreach ($query->rows as $row) {
      $poip_settings[$row['product_option_id']] = $this->getProductOptionSettings($row['product_option_id']);
    }
    return $poip_settings;
  }
  // >> reading calculated settings
	*/
  
  // for export
  public function getAllImages($include_options_without_images=false, $include_names=false) {
		$sql_extra_select = $include_names ? ", PD.name product_name, OD.name option_name, OVD.name option_value_name" : "";
    $query = $this->db->query(" SELECT POIP.product_id, POV.option_value_id, POIP.image ".$sql_extra_select."
                                FROM ".DB_PREFIX."poip_option_image POIP
																		,".DB_PREFIX."product_option_value POV
																			LEFT JOIN ".DB_PREFIX."product_description PD ON (PD.product_id = POV.product_id AND PD.language_id = ".(int)$this->config->get('config_language_id').")
																			LEFT JOIN ".DB_PREFIX."option_description OD ON (OD.option_id = POV.option_id AND OD.language_id = ".(int)$this->config->get('config_language_id').")
																			LEFT JOIN ".DB_PREFIX."option_value_description OVD ON (OVD.option_value_id = POV.option_value_id AND OVD.language_id = ".(int)$this->config->get('config_language_id').")
                                WHERE POIP.product_option_value_id = POV.product_option_value_id
																ORDER BY POIP.product_id ASC, POV.option_value_id ASC, POIP.sort_order ASC
                                ");
		$results = $query->rows;
		if ( $include_options_without_images ) {
			$query = $this->db->query("
				SELECT POV.product_id, POV.option_value_id, '' ".$sql_extra_select."
				FROM ".DB_PREFIX."product_option_value POV
							LEFT JOIN ".DB_PREFIX."product_description PD ON (PD.product_id = POV.product_id AND PD.language_id = ".(int)$this->config->get('config_language_id').")
							LEFT JOIN ".DB_PREFIX."option_description OD ON (OD.option_id = POV.option_id AND OD.language_id = ".(int)$this->config->get('config_language_id').")
							LEFT JOIN ".DB_PREFIX."option_value_description OVD ON (OVD.option_value_id = POV.option_value_id AND OVD.language_id = ".(int)$this->config->get('config_language_id').")
				WHERE NOT POV.product_option_value_id IN (SELECT product_option_value_id FROM ".DB_PREFIX."poip_option_image)
				ORDER BY POV.product_id ASC, POV.option_value_id ASC
			");
			
			$results = array_merge($results, $query->rows);
		}
    return $results;
    //$query = $this->db->query(" SELECT POIP.product_id, POV.option_value_id, POIP.image
    //                            FROM ".DB_PREFIX."poip_option_image POIP, ".DB_PREFIX."product_option_value POV
    //                            WHERE POIP.product_option_value_id = POV.product_option_value_id
    //                            ");
    //return $query->rows;
  }
  
  public function getProductOrderImage($product_id, $option_data, $image) {
    
    if (!$this->liveopencart_poip->installed()) {
      return $image;
    }
    
    $selected_product_option = array();
    $selected_product_option_value = array();
    foreach ($option_data as $option_value_data) {
      if (!in_array($option_value_data['product_option_id'], $selected_product_option)) $selected_product_option[] = $option_value_data['product_option_id'];
      if (!in_array($option_value_data['product_option_value_id'], $selected_product_option_value)) $selected_product_option_value[] = $option_value_data['product_option_value_id'];
    }
    
    
    $product_images = $this->getProductOptionImages($product_id);
    if ( count($product_images) > 0 ) {
      
      $product_settings = $this->liveopencart_poip->getProductSettings($product_id);
      
      $cart_options = array();
      $filter_options = array();
      foreach ($product_images as $product_option_id => $product_option_images ) {
        
        if ( in_array($product_option_id, $selected_product_option) ) { // значение опции выбрано
          
          $images_count = 0;
          
          foreach ($product_option_images as $product_option_value => $product_option_value_images) {
            $images_count = $images_count + count($product_option_value_images);
          }
          
          if ($images_count > 0) {
            if (isset($product_settings[$product_option_id]['img_cart']) && $product_settings[$product_option_id]['img_cart']) {
              $cart_options[] = $product_option_id;
              if ($product_settings[$product_option_id]['img_limit']) $filter_options[] = $product_option_id;
            }
          }
        }
      }
      
      if (count($filter_options)>0) {
        
        $images = false;
        foreach ($product_images as $product_option_id => $product_option_images) {
          if (in_array($product_option_id, $filter_options)) {
            $current_images = array();
            foreach ($product_images[$product_option_id] as $product_option_value_id => $product_option_value_images) {
              if ( in_array($product_option_value_id, $selected_product_option_value) ) { // это выбранное значение опции
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
        
      } elseif (count($cart_options)>0) { // first image of first option
        $product_option_id = $cart_options[0];
        foreach ($product_images[$product_option_id] as $product_option_value_id => $product_option_value_images) {
          if ( in_array($product_option_value_id, $selected_product_option_value) ) { // selected value
            foreach ($product_option_value_images as $image_info) {
              $image = $image_info['image'];
            }
          }
        }
      }
      
    }
    
    return $image;
    
  }
  
  public function addProductOptionValueImage($product_id, $option_value_id, $image) {
    
    $query = $this->db->query("SELECT product_option_id, product_option_value_id FROM ".DB_PREFIX."product_option_value WHERE product_id = ".(int)$product_id." AND option_value_id = ".(int)$option_value_id." ");
    if ( !$query->num_rows ) {
			return 0;
		} else {
      
      $query_i = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_option_image
                                    WHERE product_id = ".(int)$product_id."
                                    AND product_option_id = ".(int)$query->row['product_option_id']."
                                    AND product_option_value_id = ".(int)$query->row['product_option_value_id']."
                                    AND image = '".$this->db->escape((string)$image)."'
                                    ");
      if ( $query_i->num_rows ) {
				return -1;
			} else {
      
        $query_p = $this->db->query("SELECT sort_order FROM ".DB_PREFIX."poip_option_image WHERE product_id = ".(int)$product_id." ORDER BY sort_order DESC LIMIT 1 ");
        if ($query_p->num_rows) {
          $sort_order = 1+$query_p->row['sort_order'];
        } else {
          $sort_order = 1;
        }
        
        $this->db->query("INSERT INTO ".DB_PREFIX."poip_option_image
                              SET product_id = ".(int)$product_id."
                                , product_option_id = ".(int)$query->row['product_option_id']."
                                , product_option_value_id = ".(int)$query->row['product_option_value_id']."
                                , image = '".$this->db->escape((string)$image)."'
                                , sort_order = ".$sort_order."
                                ");
        
        return 1;
        
      }
      
    }
    
    return false;
    
  }
  
  // thumbs to array of images
  public function addThumbsIfNeeded($images) {
    
    if (!$this->liveopencart_poip->installed()) return $images;
    
		if (!$this->model_tool_image) {
			$this->load->model('tool/image');
		}
		
    foreach ($images as &$image) {
      if (!isset($image['thumb'])) {
        $image['thumb'] = $this->model_tool_image->resize($image['image'], 100, 100);
      }
    }
    unset($image);
    
    return $images;
  }
  
  public function getProductOptionImages($product_id) {
    
    $images = array();
    
    if (!$this->liveopencart_poip->installed()) return $images;
    
    if (!$this->model_tool_image) {
			$this->load->model('tool/image');
		}
    
    $query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_option_image WHERE product_id = ".(int)$product_id." ORDER BY sort_order ");
    foreach ($query->rows as $row) {
      if (!isset($images[$row['product_option_id']])) {
        $images[$row['product_option_id']] = array();
      }
      if (!isset($images[$row['product_option_id']][$row['product_option_value_id']])) {
        $images[$row['product_option_id']][$row['product_option_value_id']] = array();
      }
      $images[$row['product_option_id']][$row['product_option_value_id']][] = array(  'image'=>$row['image']
                                                                                    , 'srt'=>$row['sort_order']
                                                                                    , 'thumb'=>$this->model_tool_image->resize($row['image'], 100, 100)
                                                                                    );
    }
    
    return $images;
    
  }
  
  
  /*
 // new
  public function getSettingsValues() {
  
    $settings_values = array();
  
    $settings_names = $this->liveopencart_poip->getModuleSettingsIds();
    foreach ($settings_names as $setting_name ) {
      for ($i=0;$i<10;$i++) {
        $value_key = 'entry_'.$setting_name.'_v'.$i;
        if ( $this->language->get($value_key) != $value_key ) {
          $settings_values[] = $value_key;
        }
      }
    }
    
    return $settings_values;
  }
  */
  
  public function saveProductOptionValueImages($product_id, $product_option_id, $product_option_value_id, $images) {
    
    if (!$this->liveopencart_poip->installed()) return;
    
    if (is_array($images)) {
      foreach ($images as $image) {
        if (is_array($image) && isset($image['image']) && $image['image']) {
          $this->db->query("INSERT INTO ".DB_PREFIX."poip_option_image
                            SET product_id = ".(int)$product_id."
                              , product_option_id = ".(int)$product_option_id."
                              , product_option_value_id = ".(int)$product_option_value_id."
                              , image = '".$this->db->escape((string)$image['image'])."'
                              , sort_order = ".(isset($image['srt']) ? (int)$image['srt'] : 0)."
                              ");
        }
      }
    }
    
  }
  
  public function deleteProductOptionValueImages($product_id) {
    
    if (!$this->liveopencart_poip->installed()) return;
    
    $this->db->query("DELETE FROM ".DB_PREFIX."poip_option_image WHERE product_id = ".(int)$product_id." ");
    
  }
	
	
  
  // Real
  public function getRealOptionSettings($option_id) {
    
    $settings = array();
    
    if (!$this->liveopencart_poip->installed()) return $settings;
    
    if (!$option_id) return $settings;
    
    $settings_names = $this->liveopencart_poip->getModuleSettingsIds();
    
    $query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_main_option_settings WHERE option_id = ".(int)$option_id." ");
    if ($query->num_rows) {
      $row = $query->row;
      foreach ($settings_names as $setting_name) {
        $settings[$setting_name] = isset($row[$setting_name]) ? $row[$setting_name] : 0;
      }
    }
    
    return $settings;
  }
  
  public function getRealProductSettings($product_id) {
    
    $settings = array();
    
    if (!$this->liveopencart_poip->installed()) return $settings;
    
    if (!$product_id) return $settings;
    
    $settings_names = $this->liveopencart_poip->getModuleSettingsIds();
    
    $query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_option_settings WHERE product_id = ".(int)$product_id." ");
    foreach ($query->rows as $row) {
      $settings[$row['product_option_id']] = array();
      foreach ($settings_names as $setting_name) {
        $settings[$row['product_option_id']][$setting_name] = isset($row[$setting_name]) ? $row[$setting_name] : 0;
      }
      
    }
    
    return $settings;
  }
  
  public function deleteRealProductSettings($product_id) {
    
    if (!$this->liveopencart_poip->installed()) return;
    $this->db->query("DELETE FROM ".DB_PREFIX."poip_option_settings WHERE product_id = ".(int)$product_id." ");
    
  }
  
  public function deleteRealOptionSettings($option_id) {
    
    if (!$this->liveopencart_poip->installed()) return;
    $this->db->query("DELETE FROM ".DB_PREFIX."poip_main_option_settings WHERE option_id = ".(int)$option_id." ");
    
  }
  
  public function setRealOptionSettings($option_id, $settings) {
    
    if (!$this->liveopencart_poip->installed()) return;

    $this->deleteRealOptionSettings($option_id);
    
    $sql = "";
    $settings_names = $this->liveopencart_poip->getModuleSettingsIds(true);
    foreach ($settings_names as $setting_name) {
      $sql .= ", ".$setting_name." = ".(isset($settings[$setting_name]) ? (int)$settings[$setting_name] : 0)." ";
    }
    
    $this->db->query("INSERT INTO ".DB_PREFIX."poip_main_option_settings
											SET option_id = ".(int)$option_id."
												".$sql."
										");
    
  }
  
  public function setRealProductSettings($product_id, $product_option_id, $settings) {
    
    if (!$this->liveopencart_poip->installed()) return;
    
    $this->db->query("DELETE FROM ".DB_PREFIX."poip_option_settings WHERE product_option_id = ".(int)$product_option_id." ");
    
    $sql = "";
    $settings_names = $this->liveopencart_poip->getModuleSettingsIds();
    foreach ($settings_names as $setting_name) {
      $sql .= ", ".$setting_name." = ".(isset($settings[$setting_name]) ? (int)$settings[$setting_name] : 0)." ";
    }
    
    $this->db->query("INSERT INTO ".DB_PREFIX."poip_option_settings
                        SET product_id = ".(int)$product_id."
                          , product_option_id = ".(int)$product_option_id."
                          ".$sql."
                          ");
    
  }
	
	
	public function getModuleSettingsDetails($for_option_or_product_page=false) {
	
		$fields = array();
		
		$settings_ids = $this->liveopencart_poip->getModuleSettingsIds($for_option_or_product_page);
		
		$lang = $this->liveopencart_poip->getLanguageOwn()->all();
		
		foreach ($settings_ids as $setting_id) {
			$field = array('name'=>$setting_id);
			$field['title'] = $lang['entry_'.$setting_id];
			$field['help'] = $lang['entry_'.$setting_id.'_help'];
			$i_field_value = 0;
			while ( isset($lang['entry_'.$setting_id.'_v'.$i_field_value]) ) {
				$field['values'][$i_field_value] = $lang['entry_'.$setting_id.'_v'.$i_field_value];
				$i_field_value++;
			}
			$fields[] = $field;
		}
			
		return $fields;
	}
	
	public function getOptionPageData() {
		
		$data['poip_installed'] 				= $this->liveopencart_poip->installed();
		$data['poip_settings_details'] 	= $this->getModuleSettingsDetails(true);
		$data['poip_module_name'] 			= $this->language->get('poip_module_name');
		$data['poip_saved_settings'] 		= $this->liveopencart_poip->getModel()->getRealOptionSettings( isset($this->request->get['option_id']) ? $this->request->get['option_id'] : 0 );
		
		//$data['entry_sort_order_short'] = $this->language->get('entry_sort_order_short');
		$data['poip_settings_enable_disable_options'] = array('0'=>$this->language->get('entry_settings_default'), '2'=>$this->language->get('entry_settings_yes'), '1'=>$this->language->get('entry_settings_no'));
		/*
		$settings_names = $this->liveopencart_poip->getModel()->getSettingsNames(false);
		foreach ($settings_names as $setting_name) {
			$data['entry_'.$setting_name] = $this->language->get('entry_'.$setting_name);
			$data['entry_'.$setting_name.'_help'] = $this->language->get('entry_'.$setting_name.'_help');
		}
		$settings_values = $this->liveopencart_poip->getModel()->getSettingsValues();
		foreach ($settings_values as $setting_value) {
			$data[$setting_value] = $this->language->get($setting_value); // new
		}
		$data['settings_values'] = $settings_values;
		*/
		
		//$data['entry_show_hide'] = $this->language->get('entry_show_hide');
		return $data;
	}
	
	public function getProductPageData() {
		
		$data['poip_installed'] = $this->liveopencart_poip->installed();
		$data['poip_global_settings'] = $this->getGlobalSettings();
		//$data['poip_settings_names'] = $this->liveopencart_poip->getSettingsNames();
		$data['poip_settings_details'] = $this->getModuleSettingsDetails(true);
		$data['poip_module_name'] = $this->language->get('poip_module_name');
		$data['poip_saved_settings'] = $this->liveopencart_poip->getModel()->getRealProductSettings( isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0 );
		
		//$data['entry_sort_order_short'] = $this->language->get('entry_sort_order_short');
		$data['poip_settings_enable_disable_options'] = array('0'=>$this->language->get('entry_settings_default'), '2'=>$this->language->get('entry_settings_yes'), '1'=>$this->language->get('entry_settings_no'));
		
		$data['poip_texts']['entry_no_value'] 			= $this->language->get('entry_no_value');
		$data['poip_texts']['entry_no_value_help'] 	= $this->language->get('entry_no_value_help');
		$data['poip_texts']['poip_module_name'] 		= $this->language->get('poip_module_name');
		$data['poip_texts']['entry_sort_order'] 		= $this->language->get('entry_sort_order');
		$data['poip_texts']['button_remove'] 				= $this->language->get('button_remove');

		
		
		
		/*
		$settings_names = $this->liveopencart_poip->getModel()->getSettingsNames(false);
		foreach ($settings_names as $setting_name) {
			$data['entry_'.$setting_name] = $this->language->get('entry_'.$setting_name);
			$data['entry_'.$setting_name.'_help'] = $this->language->get('entry_'.$setting_name.'_help');
		}
		
		$settings_values = $this->liveopencart_poip->getModel()->getSettingsValues();
		foreach ($settings_values as $setting_value) {
			$data[$setting_value] = $this->language->get($setting_value); // new
		}
		$data['settings_values'] = $settings_values;
		*/
		/*
		$data['entry_show_settings'] 	= $this->language->get('entry_show_settings');
		$data['entry_hide_settings'] 	= $this->language->get('entry_hide_settings');
		
		$data['entry_no_value'] 			= $this->language->get('entry_no_value');
		$data['entry_no_value_help'] 	= $this->language->get('entry_no_value_help');
		*/
		
		return $data;
	}
	
	public function addOptionsDataToAdditionalImages($product_id, $rows) {
		
		if ( $this->liveopencart_poip->installed() ) {
			$poip_global_settings = $this->getGlobalSettings();
			if ( $poip_global_settings['options_images_edit'] == 1 ) {
				
				$poip_options_for_images = $this->getOptionsForImages($product_id);
				
				$included_images = array();
				foreach ($rows as &$row) {
					if ( isset($poip_options_for_images[$row['image']]) ) {
						$row['poip'] = $poip_options_for_images[$row['image']];
						$included_images[] = $row['image'];
					} else {
						$row['poip'] = false;
					}
				}
				unset($row);
				
				// if option images is not set for additional image - add this image in additional images array
				foreach ($poip_options_for_images as $poip_image => $image_options) {
					if ( !in_array($poip_image, $included_images) ) {
						$rows[] = array('product_image_id'=>0, 'product_id'=>$product_id, 'image'=>$poip_image, 'sort_order'=>0, 'poip'=>$image_options);
					}
				}
				
			}
		}
		
		return $rows;
	}
	
	public function addProductPageResources() {
		
		if ( $this->liveopencart_poip->installed() ) {
			$this->document->addScript( $this->liveopencart_poip->getResourceLinkPathWithVersion('view/javascript/liveopencart/product_option_image_pro/poip_product_edit_page.js') );
			$this->document->addStyle( 	$this->liveopencart_poip->getResourceLinkPathWithVersion('view/stylesheet/liveopencart/product_option_image_pro/poip_product_edit_page.css') );
		}
	}
	
	public function reinsertProductOptionValue($product_option_value_id) { // reinsert_product_option_value
        
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = ".(int)$product_option_value_id." ");
		if ( $query->num_rows ) {
			
			$sql_set = "";
			foreach ($query->row as $key => $value) {
				$sql_set .= ", `".$key."` = '".$this->db->escape($value)."' ";
			}
			$sql_set = substr($sql_set, 1);
			$this->db->query("DELETE FROM ".DB_PREFIX."product_option_value WHERE product_option_value_id = ".$product_option_value_id." ");
			$this->db->query("INSERT INTO ".DB_PREFIX."product_option_value SET ".$sql_set);
		}
	
	}
  
  public function install() {
    
    $this->uninstall();
    
    $this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."poip_option_image` (
					`product_id` int(11) NOT NULL,
					`product_option_id` int(11) NOT NULL,
					`product_option_value_id` int(11) NOT NULL,
					`image` varchar(255) NOT NULL,
					`sort_order` int(11) NOT NULL,
					FOREIGN KEY (product_id) REFERENCES `".DB_PREFIX."product`(product_id) ON DELETE CASCADE,
					FOREIGN KEY (product_option_id) REFERENCES `".DB_PREFIX."product_option`(product_option_id) ON DELETE CASCADE,
					FOREIGN KEY (product_option_value_id) REFERENCES `".DB_PREFIX."product_option_value`(product_option_value_id) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
    
    $this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."poip_option_settings` (
					`product_id` int(11) NOT NULL,
					`product_option_id` int(11) NOT NULL,
					`img_change` tinyint(1) NOT NULL,
					`img_hover` tinyint(1) NOT NULL,
					`img_use` tinyint(1) NOT NULL,
					`img_limit` tinyint(1) NOT NULL,
					`img_gal` tinyint(1) NOT NULL,
					`img_option` tinyint(1) NOT NULL,
					`img_category` tinyint(1) NOT NULL,
					`img_first` tinyint(1) NOT NULL,
					`img_from_option` tinyint(1) NOT NULL,
					`img_sort` tinyint(1) NOT NULL,
					`img_select` tinyint(1) NOT NULL,
					`img_cart` tinyint(1) NOT NULL,
					`img_radio_checkbox` tinyint(1) NOT NULL,
					`dependent_thumbnails` tinyint(1) NOT NULL,
					FOREIGN KEY (product_id) REFERENCES `".DB_PREFIX."product`(product_id) ON DELETE CASCADE,
					FOREIGN KEY (product_option_id) REFERENCES `".DB_PREFIX."product_option`(product_option_id) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8
    ");
    
    $this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."poip_main_option_settings` (
					`option_id` int(11) NOT NULL,
					`img_change` tinyint(1) NOT NULL,
					`img_hover` tinyint(1) NOT NULL,
					`img_use` tinyint(1) NOT NULL,
					`img_limit` tinyint(1) NOT NULL,
					`img_gal` tinyint(1) NOT NULL,
					`img_option` tinyint(1) NOT NULL,
					`img_category` tinyint(1) NOT NULL,
					`img_first` tinyint(1) NOT NULL,
					`img_from_option` tinyint(1) NOT NULL,
					`img_sort` tinyint(1) NOT NULL,
					`img_select` tinyint(1) NOT NULL,
					`img_cart` tinyint(1) NOT NULL,
					`img_radio_checkbox` tinyint(1) NOT NULL,
					`dependent_thumbnails` tinyint(1) NOT NULL,
					FOREIGN KEY (`option_id`) REFERENCES `".DB_PREFIX."option`(`option_id`) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8
    ");
    
    
    $this->load->model('setting/setting');
		$msettings = array('poip_module'=>array('img_change'=>1,'img_hover'=>1,'img_main_to_additional'=>1,'img_hover'=>1,'img_use'=>1,'img_limit'=>1,'img_gal'=>1,'img_cart'=>1));
		$this->model_setting_setting->editSetting('poip_module', $msettings);
    
  }
	
  public function uninstall() {
    
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "poip_option_image`;");
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "poip_main_option_settings`;");
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "poip_option_settings`;");
    
  }
  
}
