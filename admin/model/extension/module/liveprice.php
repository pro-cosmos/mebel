<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ModelExtensionModuleLivePrice extends Model {

	public function __construct() {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		\liveopencart\ext\liveprice::getInstance($this->registry);
	}


	// save data and unset it in POST array
	public function saveDBSettings($post) {
		
		if ( $this->liveopencart_ext_liveprice->versionPRO() ) {
		
			//$this->liveopencart_ext_liveprice->checkTables(); //checked on lib construct
			
			$this->db->query("TRUNCATE TABLE ".DB_PREFIX."liveprice_global_discount ");
			$this->db->query("TRUNCATE TABLE ".DB_PREFIX."liveprice_global_special ");
			
			if ( isset($post['settings']['discounts']) ) {
				$discounts = $post['settings']['discounts'];
				$sort_order = 0;
				foreach ( $discounts as $discount ) {
					$sort_order++;
					$this->db->query("INSERT INTO ".DB_PREFIX."liveprice_global_discount
														SET category_id = ". ( (int)$discount['category_id']==0 ? -1 : (int)$discount['category_id'] ) ."
															, manufacturer_id = ". ( (int)$discount['manufacturer_id']==0 ? -1 : (int)$discount['manufacturer_id'] ) ."
															, customer_group_id = ". $discount['customer_group_id']."
															, quantity = ".(int)$discount['quantity']."
															, priority = ".(int)$discount['priority']."
															, price_prefix = '".$this->db->escape($discount['price_prefix'])."'
															, price = ".(float)$discount['price']."
															, date_start = '".$this->db->escape($discount['date_start'])."'
															, date_end = '".$this->db->escape($discount['date_end'])."'
															, sort_order = ".(int)$sort_order."
														");
				}
				unset($post['settings']['discounts']);
			}
			
			if ( isset($post['settings']['specials']) ) {
				$specials = $post['settings']['specials'];
				$sort_order = 0;
				foreach ( $specials as $special ) {
					$sort_order++;
					$this->db->query("INSERT INTO ".DB_PREFIX."liveprice_global_special
														SET category_id = ". ( (int)$special['category_id']==0 ? -1 : (int)$special['category_id'] ) ."
															, manufacturer_id = ". ( (int)$special['manufacturer_id']==0 ? -1 : (int)$special['manufacturer_id'] ) ."
															, customer_group_id = ". $special['customer_group_id']."
															, priority = ".(int)$special['priority']."
															, price_prefix = '".$this->db->escape($special['price_prefix'])."'
															, price = ".(float)$special['price']."
															, date_start = '".$this->db->escape($special['date_start'])."'
															, date_end = '".$this->db->escape($special['date_end'])."'
															, sort_order = ".(int)$sort_order."
														");
				}
				unset($post['settings']['specials']);
			}
		}
		return $post;
	}
	
	public function readDBSettings($settings=array()) {
		
		$settings = array();
		if ( $this->liveopencart_ext_liveprice->versionPRO() ) {
		
			$query = $this->db->query("	SELECT LGP.*, CD.name category, M.name manufacturer
																	FROM ".DB_PREFIX."liveprice_global_discount LGP
																			LEFT JOIN ".DB_PREFIX."category_description CD
																				ON (LGP.category_id = CD.category_id AND CD.language_id = ".(int)$this->config->get('config_language_id').")
																			LEFT JOIN ".DB_PREFIX."manufacturer M
																				ON (LGP.manufacturer_id = M.manufacturer_id)
																	ORDER BY sort_order ASC
																	");
			foreach ( $query->rows as &$row ) {
				if ( !$row['category']) {
					$row['category'] = '';
				}
				if ( !$row['manufacturer']) {
					$row['manufacturer'] = '';
				}
			}
			unset($row);
			
			$settings['discounts'] = $query->rows;
			
			
			$query = $this->db->query("	SELECT LGP.*, CD.name category, M.name manufacturer
																	FROM ".DB_PREFIX."liveprice_global_special LGP
																			LEFT JOIN ".DB_PREFIX."category_description CD
																				ON (LGP.category_id = CD.category_id AND CD.language_id = ".(int)$this->config->get('config_language_id').")
																			LEFT JOIN ".DB_PREFIX."manufacturer M
																				ON (LGP.manufacturer_id = M.manufacturer_id)
																	ORDER BY sort_order ASC
																	");
			foreach ( $query->rows as &$row ) {
				if ( !$row['category']) {
					$row['category'] = '';
				}
				if ( !$row['manufacturer']) {
					$row['manufacturer'] = '';
				}
			}
			unset($row);
			
			$settings['specials'] = $query->rows;
		}
		return $settings;
	}
	
	
	public function uninstall() {
		
		if ( $this->liveopencart_ext_liveprice->versionPRO() ) {
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "liveprice_global_discount`;");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "liveprice_global_special`;");
		}
	}

}

