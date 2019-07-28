<?php

namespace liveopencart\ext\liveprice;

class ro extends \liveopencart\lib\v0004\sub_library {
  
  use \liveopencart\lib\v0004\traits\cache;
  use \liveopencart\lib\v0004\traits\installed;
  
  public function installed() {
    return $this->getExtensionInstalledStatus('related_options', 'pso_installed');
  }
  
  private function getModule() {
    if ( !$this->hasCacheSimple('module') ) {
      $this->load->model('extension/liveopencart/related_options');
      $this->setCacheSimple('module', $this->model_extension_liveopencart_related_options);
    }
    return $this->getCacheSimple('module');
  }
  
  public function getSettings() {
    return $this->config->get('related_options');
  }
  
  
  public function getProductFirstROPROVariant( $product_id ) {
    $query = $this->db->query(" SELECT ROVP.relatedoptions_variant_product_id
                                FROM ".DB_PREFIX."relatedoptions_variant_product ROVP
                                    LEFT JOIN ".DB_PREFIX."relatedoptions_variant ROV ON (ROV.relatedoptions_variant_id = ROVP.relatedoptions_variant_id)
                                WHERE ROVP.product_id = ".(int)$product_id."
                                ORDER BY ROV.sort_order, ROV.relatedoptions_variant_name, ROVP.relatedoptions_variant_id, ROVP.relatedoptions_variant_product_id
                                LIMIT 1
                              ");
    if ( $query->num_rows ) {
      return $query->row['relatedoptions_variant_product_id'];
    }
  }
  
  public function getROCombination($relatedoptions_id) {
    $query = $this->db->query(" SELECT PO.product_option_id, POV.product_option_value_id
                                FROM ".DB_PREFIX."relatedoptions_option ROO
                                    ,".DB_PREFIX."product_option PO
                                    ,".DB_PREFIX."product_option_value POV
                                WHERE ROO.relatedoptions_id = ".(int)$relatedoptions_id."
                                  AND PO.option_id = ROO.option_id
                                  AND PO.product_id = ROO.product_id
                                  AND POV.option_value_id = ROO.option_value_id
                                  AND POV.product_id = ROO.product_id
                              ");
    $comb = array();
    foreach ( $query->rows as $row ) {
      $comb[$row['product_option_id']] = $row['product_option_value_id'];
    }
    return $comb;
  }
  
  public function getProductStartingFromOptions($product_id, $io_defaults, $product_price, $sql_min_options) {
    
    $ro_minimal = array();
    if ( $this->installed() ) {
        
      $this->load->model('extension/liveopencart/related_options');
      
      $ro_settings = $this->getSettings();
      $ro_options = $this->getModule()->getProductROVariantOptions($product_id);
      
      $ro_ids = false;
      if ( $io_defaults ) {
        $ro_io_combs = $this->getModule()->getROCombsByPOIds($product_id, $io_defaults);
        
        if ( $ro_io_combs ) {
          $ro_ids = array();
          foreach ($ro_io_combs as $ro_io_comb) {
            $ro_ids[] = $ro_io_comb['relatedoptions_id'];
          }
        }
      }
      
      $sql_ro_price = "";
      if ( !empty($ro_settings['spec_price']) ) {
        if ( !empty($ro_settings['spec_price_prefix']) ) {
          $sql_ro_price = "IF(RO.price <> 0,
                            CASE RO.price_prefix
                              WHEN '+' THEN '".(float)$product_price."' + RO.price
                              WHEN '=' THEN RO.price
                              WHEN '-' THEN '".(float)$product_price."' - RO.price
                              ELSE RO.price
                            END, '".(float)$product_price."')";
        } else {
          $sql_ro_price = "IF(RO.price <> 0, RO.price, '".(float)$product_price."')";
        }
      } else {
        $sql_ro_price = "'".(float)$product_price."'";
      }
      
      // get ro_combs taking possible discounts with quantity = 1 into account
      $sql_basic_ro = "
        SELECT 	RO.relatedoptions_id
              , IF(ROD.relatedoptions_id IS NULL, RO.price_prefix, ".
              // basically RO discounts uses only = prefix, but it could be changed by the specific setting of Live Price PRO
              ( $this->liveopencart_ext_liveprice->getSetting('ropro_discounts_addition') ? "RO.price_prefix" : "'='" )
              .") price_prefix
              , IF(ROD.relatedoptions_id IS NULL, RO.price, ROD.price) price
        FROM ".DB_PREFIX."relatedoptions RO
              LEFT JOIN ".DB_PREFIX."relatedoptions_discount ROD
                ON (ROD.relatedoptions_id = RO.relatedoptions_id AND ROD.quantity = 1 AND ROD.customer_group_id = ".(int)$this->config->get('config_customer_group_id').")
        WHERE RO.product_id = ".(int)$product_id."
          ".
          // for ROPRO check only the first variant of related options
          " AND RO.relatedoptions_variant_product_id = ".(int)$this->getProductFirstROPROVariant($product_id)." "
          .
          ($ro_ids ? " AND RO.relatedoptions_id IN (".implode(',', $ro_ids).")" : "" )
          ."
      ";
      
      // calculation of RO prices takes into account standard price modifiers of options values used in ROcombs
      $query = $this->db->query(" SELECT PRICES.* FROM
                                  ( SELECT RO.relatedoptions_id
                                          ,(".$sql_ro_price."
                                            + IF(ROOPR.price IS NULL,0,ROOPR.price)
                                          ) as price
                                    FROM (".$sql_basic_ro.") RO
                                        LEFT JOIN ( SELECT SUM(OPR.price) as price, ROO.relatedoptions_id
                                                    FROM ( ".$sql_min_options." ) as OPR
                                                        ,".DB_PREFIX."relatedoptions_option ROO
                                                    WHERE ROO.product_id = ".(int)$product_id."
                                                      AND ROO.option_value_id = OPR.option_value_id
                                                    GROUP BY (ROO.relatedoptions_id)
                                                  ) as ROOPR ON ( RO.relatedoptions_id = ROOPR.relatedoptions_id )
                                  ) PRICES
                                ORDER BY PRICES.price ASC
                              ");
     
      if ( $query->num_rows ) {
        
        // only of at least one of RO changes product price
        foreach ( $query->rows as $row ) {
          if ( $row['price'] != $product_price ) {
            $ro_minimal = $this->getROCombination( $query->rows[0]['relatedoptions_id'] ); // first row
            break;
          }
        }
        
      }
    }
    return $ro_minimal;
  }
  
  public function hasProductRODiscounts($product_id) {
    
    $ro_settings = $this->getSettings();
    if ( $this->installed() && !empty($ro_settings['spec_price']) && !empty($ro_settings['spec_price_discount']) ) {
      $product_has_ro_discount = $this->db->query("
        SELECT *
        FROM `".DB_PREFIX."relatedoptions_discount`
        WHERE `relatedoptions_id` IN (SELECT `relatedoptions_id` FROM `".DB_PREFIX."relatedoptions` WHERE `product_id`='".(int)$product_id."')
        LIMIT 1
      ")->num_rows;
      // = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_discount WHERE product_id = '" . (int)$product_id . "' LIMIT 1 ")->num_rows;
    } else {
      $product_has_ro_discount = false;
    }
    return $product_has_ro_discount;
  }
  
  public function hasProductROSpecials($product_id) {
    $ro_settings = $this->getSettings();
    if ( $this->installed() && !empty($ro_settings['spec_price']) && !empty($ro_settings['spec_price_special']) ) {
      $product_has_ro_special = $this->db->query("
        SELECT *
        FROM `".DB_PREFIX."relatedoptions_discount`
        WHERE `relatedoptions_id` IN (SELECT `relatedoptions_id` FROM `".DB_PREFIX."relatedoptions` WHERE `product_id`='".(int)$product_id."')
        LIMIT 1
      ")->num_rows;
    } else {
      $product_has_ro_special = false;
    }
	
		return $product_has_ro_special;
	}
  
  public function getProductDiscountQuery($ro_combs, $discount_quantity, $customer_group_id) {
    
    $ro_settings = $this->getSettings();
      
    if ($ro_combs) {
      
      foreach ($ro_combs as $ro_comb) {
        
        if ( !empty($ro_comb['discounts']) ) { // $ro_comb discounts are already received for the currect customer group
          
          if ( $this->liveopencart_ext_liveprice->getSetting('ropro_discounts_addition') && !empty($ro_settings['spec_price_prefix']) && !empty($ro_comb['price_prefix']) && ($ro_comb['price_prefix']=='+' || $ro_comb['price_prefix']=='-') ) {
            continue; // these discount additions are implemented another way (on anoher step)
          }
          
          $ro_discount_query = $this->db->query("	SELECT RD.price
                                                  FROM " . DB_PREFIX . "relatedoptions_discount RD
                                                  WHERE RD.relatedoptions_id = '" . (int)$ro_comb['relatedoptions_id'] . "'
                                                    AND RD.customer_group_id = '" . (int)$customer_group_id . "'
                                                    AND RD.quantity <= '" . (int)$discount_quantity . "'
                                                  ORDER BY RD.quantity DESC, RD.priority ASC, RD.price ASC LIMIT 1");
          //if ($ro_discount_query->num_rows)	{
          // if this combinations of related options has discounts, basic product discounts should be always ignored
          // (even if there is no appropriate discount in RO for the currect quantity)
            return $ro_discount_query;
          //}
          
        }
      }
    }	
    
  }
  
  public function getProductSpecialQuery($ro_combs, $customer_group_id) {
    $ro_settings = $this->getSettings();
    if ($ro_combs) {
      foreach ($ro_combs as $ro_comb) {
        
        if ( !empty($ro_comb['specials']) ) { // $ro_comb specials are already received for the currect customer group
          if ( $this->liveopencart_ext_liveprice->getSetting('ropro_specials_addition') && !empty($ro_settings['spec_price_prefix']) && !empty($ro_comb['price_prefix']) && ($ro_comb['price_prefix']=='+' || $ro_comb['price_prefix']=='-') ) {
            continue; // these special additions are implemented another way (on anoher step)
          }
          
          $ro_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "relatedoptions_special
                                                WHERE relatedoptions_id = '" . (int)$ro_comb['relatedoptions_id'] . "'
                                                  AND customer_group_id = '" . (int)$customer_group_id . "'
                                                ORDER BY priority ASC, price ASC LIMIT 1");
          //if ($ro_special_query->num_rows)	{
            return $ro_special_query;
          //}
        }
      }
    }	
  }
  
  public function getROCombsByPOIds($product_id, $options, $use_cache, $p_allow_zero_quantity, $use_ro_data_cache) {
    if ( $this->installed() ) {
      return $this->getModule()->getROCombsByPOIds($product_id, $options, true, -1, $use_ro_data_cache);
    }
  }
  
  public function calcProductPriceWithRO($product_price, $ro_combs, $special=0, $stock=false, $ro_price_modificator=0, $quantity=false) {
    if ( $this->installed() ) {
      return $this->getModule()->calcProductPriceWithRO($product_price, $ro_combs, $special, $stock, $ro_price_modificator, $quantity);
    }
  }
  
  public function getRODiscounts($customer_group_id, $ro_combs) {
    if ( $this->installed() && $ro_combs ) {
      $ro_settings = $this->getSettings();
      if ( !empty($ro_settings['spec_price']) && !empty($ro_settings['spec_price_discount']) ) {
          
        // for the case when there are standard discounts and RO discounts with setting 'ropro_discounts_addition', this way maybe incorrect
        foreach ($ro_combs as $ro_comb) {
          $ro_discount_query = $this->db->query(" SELECT * FROM " . DB_PREFIX . "relatedoptions_discount
                                                  WHERE relatedoptions_id = '" . (int)$ro_comb['relatedoptions_id'] . "'
                                                    AND customer_group_id = '" . (int)$customer_group_id . "'
                                                  ORDER BY quantity ASC, priority ASC, price ASC");
          if ($ro_discount_query->num_rows)	{
            $ro_discounts = $ro_discount_query->rows;
            foreach ($ro_discounts as &$ro_discount) {
              $ro_discount['ro_comb_price_prefix'] = $ro_comb['price_prefix'];
            }
            unset($ro_discount);
            return $ro_discounts;
          }
        }
      }
    }
  }
  
  public function getCustomFields($product, $ro_combs) {
    if ( $ro_combs ) {
      return $ro_custom_fields = $this->getModule()->getCustomFields($product, $ro_combs);
    }
  }
  
  
}