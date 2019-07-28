<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ControllerExtensionLiveopencartLivePrice extends Controller {
	
	public function __construct() {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		\liveopencart\ext\liveprice::getInstance($this->registry);
	}
	
	public function price() {
		
		if ( $this->config->get('config_customer_price') && !$this->customer->isLogged() ) {
			$this->response->setOutput(json_encode(array()));
			return;
		}
		
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			exit;
		}
		
		if (isset($this->request->get['quantity'])) {
			$quantity = (int)$this->request->get['quantity'];
		} else {
			$quantity = 1;
		}
		
		if (isset($this->request->post['option_oc'])) {
			$options = $this->request->post['option_oc'];
		} elseif (isset($this->request->post['option'])) {
			$options = $this->request->post['option'];
		} else {
			$options = array();
		}
		
		
		
		if ( !empty($this->request->post['quantity_per_option']) && is_array($this->request->post['quantity_per_option']) ) {
			// specific calculation for a specific options (quantity is set for each option value)
			$quantity_per_options = $this->request->post['quantity_per_option'];
			$lp_prices = $this->getProductTotalPriceForQuantityPerOptionWithHtml( $product_id, $options, $quantity_per_options);
		} else { // standard way
			$lp_prices = $this->getProductPriceWithHtml( $product_id, max($quantity, 1), $options, true );
		}
		
		// return only required data
		$prices = array('htmls'=>$lp_prices['htmls'], 'ct'=>$lp_prices['ct']);
		if (isset($this->request->get['rnd'])) {
			$prices['rnd'] = $this->request->get['rnd'];
		}
		
		$this->setAllowOriginHeader(); 
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($prices));
		
	}
	
	public function getProductPriceWithHtml($product_id, $current_quantity=0, $options=array(), $multiplied_price=false) {
    
    $prices = $this->liveopencart_ext_liveprice->getCalc()->getProductPrice( $product_id, $current_quantity, $options, 0, $multiplied_price );
    
    $simple_prices = array( 'price'       =>  $prices['f_price_old_opt'],
                            'special'     =>  ( ($prices['special'] || $prices['special_opt']) ? $prices['f_special_opt'] : '' ),
                            
                            'tax'         =>  ( $prices['config_tax'] ? $prices['f_price_opt_notax'] : $prices['config_tax'] ),
                            'discounts'   =>  $prices['discounts'],
                            'points'      =>  $prices['points'],
														'reward'      =>  isset($product_data['reward']) ? $product_data['reward'] : '',
                            'minimum'     =>  isset($product_data['minimum']) ? $product_data['minimum'] : '',
                            
                            'price_val'   =>  $this->tax->calculate($prices['price_old_opt'], $prices['tax_class_id'], $prices['config_tax']),
														'special_val' =>  $this->tax->calculate($prices['special_opt'], $prices['tax_class_id'], $prices['config_tax']),
                            
                            'product_id'  =>  $product_id,
                            );
    
    $prices['htmls'] = $this->getPriceHtmls($simple_prices);
    $prices['ct'] = $this->liveopencart_ext_liveprice->getThemeName();
    
		return $prices;
    //return array('prices'=>$prices, 'product_data'=>$product_data, 'option_data'=>$option_data);
  }
	
	private function getPriceHtmls($prices) {
		
		$lp_settings = $this->config->get('liveprice_settings');
    
    $this->load->language('product/product');
		$data['text_price']         = $this->language->get('text_price');
    $data['text_tax']           = $this->language->get('text_tax');
    $data['text_discount']      = $this->language->get('text_discount');
    $data['text_points']        = $this->language->get('text_points');
    $data['text_reward']        = $this->language->get('text_reward');
    $data['text_stock']         = $this->language->get('text_stock');
    $data['text_minimum']       = sprintf($this->language->get('text_minimum'), $prices['minimum']);
    $data['text_manufacturer']  = $this->language->get('text_manufacturer');
    
		$data['product_id'] = $prices['product_id'];
    $data['price'] = $prices['price'];
    $data['special'] = $prices['special'];
		if ( !empty($lp_settings['hide_tax']) ) {
			$data['tax'] = '';
		} else {
			$data['tax'] = $prices['tax'];
		}
    $data['points'] = $prices['points'];
    $data['discounts'] = $prices['discounts'];
    $data['minimum'] = $prices['minimum'];
    $data['price_val'] = $prices['price_val'];
    $data['special_val'] = $prices['special_val'];
		$data['theme_name'] = $this->liveopencart_ext_liveprice->getThemeName();
    
    $htmls = array();
    
		$templates = $this->liveopencart_ext_liveprice->getPriceTemplates();
    
		foreach ( $templates as $template_name => $template_path ) {
			$htmls[$template_name] = $this->render($template_path, $data);
			//$htmls[$template_name] = $this->load->view($template_path, $data);
		}
		
		return $htmls;
		
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
	
	private function getProductTotalPriceForQuantityPerOptionWithHtml($p_product_id, $p_options, $p_quantity_per_options) {
		
		$total_quantity = 0;
		$total_price_old_opt = 0;
		$total_special_opt = 0;
		$total_price_opt = 0;
		$total_points = 0;
		
		\liveopencart\ext\qpo::getInstance($this->registry);
		
		if ( $this->liveopencart_ext_qpo->installed() ) {
			
			$quantity_per_options = $this->liveopencart_ext_qpo->normalizeArrayOfQPO($p_quantity_per_options);
			$qpo_all_combinations = $this->liveopencart_ext_qpo->getCombinationsOfOptions($quantity_per_options, $p_options);
			
			$qpo_total_quantity = 0;
			foreach ($qpo_all_combinations as $qpo_of_options) {
				$qpo_total_quantity+= $qpo_of_options['quantity'];
			}
			
			$stored_discounts = array();
			foreach ( $qpo_all_combinations as $qpo_of_options ) { // get prices for all combinations of options
				
				if ( $qpo_of_options['quantity'] ) {
				
					$quantity = $qpo_of_options['quantity'];
					
					$qpo_total_quantity_except_current = $qpo_total_quantity - $quantity;
					
					$lp_prices = $this->liveopencart_ext_liveprice->getCalc()->getProductPriceByParamsArray( array(
						'product_id' =>$p_product_id,
						'quantity' => $quantity,
						'options' => $qpo_of_options['options'],
						'multiplied_price' => true, 
						'qpo_discount_quantity_addition' => $qpo_total_quantity_except_current,
					) );
					
					$total_quantity+= $quantity;
					$current_product_data = $lp_data['product_data'];
					$current_tax_class_id = $lp_prices['tax_class_id'];
					$current_config_tax = $lp_prices['config_tax'];
					
					$current_price_old_opt = (float)$lp_prices['price_old_opt'];
					$current_special_opt = $lp_prices['special'] ? (float)$lp_prices['special_opt'] : 0;
					$current_price_opt = (float)$lp_prices['price_opt'];
					$current_points = $lp_prices['points'];
		
					$total_price_old_opt+= $quantity*$current_price_old_opt;
					$total_special_opt+= $quantity*$current_special_opt;
					$total_price_opt+= $quantity*$current_price_opt;
					$total_points+= $current_points; // points are already multiplied
					
					if ( count($stored_discounts) == 0 ) {
						$stored_discounts[] = $lp_prices['discounts'];
					} else {
						if ( $stored_discounts[ count($stored_discounts)-1 ] != $lp_data['prices']['discounts'] ) {
							$stored_discounts[] = $lp_prices['discounts'];
						}
					}
				}
			}
		}
		
		if ( $total_quantity ) {
			
			$prices = array();
			$simple_prices = array(
				'price'       =>  $this->liveopencart_ext_liveprice->getCalc()->format( $this->tax->calculate($total_price_old_opt, $current_tax_class_id, $current_config_tax) ), 
				'special'     =>  $total_special_opt ? $this->liveopencart_ext_liveprice->getCalc()->format( $this->tax->calculate($total_special_opt, $current_tax_class_id, $current_config_tax) ) : '',
				
				'tax'         =>  $current_config_tax ? $this->liveopencart_ext_liveprice->getCalc()->format( $this->tax->calculate($total_price_opt, $current_tax_class_id, $current_config_tax) ) : $current_config_tax,
				'discounts'   =>  (!empty($stored_discounts) && count($stored_discounts) == 1 ? $stored_discounts[0] : array()),
				'points'      =>  $total_points, // $prices['points']
				'reward'      =>  $lp_prices['reward'],
				'minimum'     =>  $lp_prices['minimum'],
				
				'price_val'   =>  $this->tax->calculate($total_price_old_opt, $current_tax_class_id, $current_config_tax),
				'special_val' =>  $this->tax->calculate($total_special_opt, $current_tax_class_id, $current_config_tax),
				
				'product_id'  =>  $p_product_id,
			);
			
			$prices['htmls'] = $this->getPriceHtmls($simple_prices);
			$prices['ct'] = $this->liveopencart_ext_liveprice->getThemeName();
			
			return array('prices'=>$prices);
			
		} else { // no quantity per options, use standard calculation
			return $this->getProductPriceWithHtml( $p_product_id, 1, $p_options, true );
		}
		
	}
	
	// fix for www and non-www requests
	private function setAllowOriginHeader() { 
		
		if ( !empty($this->request->server['HTTP_ORIGIN']) ) {
		
			if ( $this->request->server['HTTPS'] ) { // the HTTPS propety should be set properly in startup.php
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}
			$http_origin = trim($this->request->server['HTTP_ORIGIN'], '/');
			$server = trim($server, '/');
			
			if ( $server != $http_origin ) { 
				$url_beginnings = array('http://www.', 'https://www.', 'http://', 'https://');
				foreach ( $url_beginnings as $url_beginning ) {
					if ( substr($server, 0, strlen($url_beginning)) == $url_beginning ) {
						$server = substr($server, strlen($url_beginning));
					}
					if ( substr($http_origin, 0, strlen($url_beginning)) == $url_beginning ) {
						$http_origin = substr($http_origin, strlen($url_beginning));
					}
				}
				if ( $server == $http_origin ) {
					$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
					$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
				}
			}
		}
	}
	
}
