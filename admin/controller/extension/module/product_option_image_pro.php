<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ControllerExtensionModuleProductOptionImagePro extends Controller {
	private $error = array();
	
	private $lang;
	
	public function __construct() {
		
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		//$this->lang = $this->load->language('extension/module/product_option_image_pro');
		
		liveopencart\poip::initLibrary($this->registry); // library also loads the model (accessible standard way from registry or by $this->liveopencart_poip->getModel  )
		$this->liveopencart_poip->loadLanguage();
	}
	
	private function getLinks() {
		
		$data = array();
		
		$route_home_page 			= 'common/dashboard';
		$route_extensions			= 'marketplace/extension';
		$route_extension_type	= '&type=module';
		$route_module 				= 'extension/module/product_option_image_pro';
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link($route_home_page, 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link($route_extensions, 'user_token=' . $this->session->data['user_token'].$route_extension_type, 'SSL'),
			'separator' => ' :: '
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('module_name'),
			'href'      => $this->url->link($route_module, 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);
		
		$data['action'] = $this->url->link($route_module, 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['action_export'] = $this->url->link($route_module.'/export', '&user_token=' . $this->session->data['user_token'], 'SSL');
	
		$data['cancel'] = $this->url->link($route_extensions, 'user_token=' . $this->session->data['user_token'].$route_extension_type, 'SSL');
		
		$data['redirect'] = $this->url->link($route_module, 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		return $data;
	}
	
	public function index() {   
		
		$links = $this->getLinks();

		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('poip_module', $this->request->post);		
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($links['redirect']);
		}
				
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } 
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['breadcrumbs'] 		= $links['breadcrumbs'];
		$data['action'] 				= $links['action'];
		$data['action_export'] 	= $links['action_export'];
		$data['cancel'] 				= $links['cancel'];
		
		$data['fields'] = $this->liveopencart_poip->getModel()->getModuleSettingsDetails();
		
		
		$data['PHPExcelExists']							= file_exists($this->PHPExcelPath());
		$data['PHPExcelPath']				 				= $this->PHPExcelPath(true);

		$data['modules'] = array();
		
		if (isset($this->request->post['poip_module'])) {
			$data['modules'] = $this->request->post['poip_module'];
		} elseif ($this->config->get('poip_module')) {
			$data['modules'] = $this->config->get('poip_module');
		}
		
		
		$data['extension_code'] 				= $this->liveopencart_poip->getExtensionCode();
		$data['module_version'] 				= $this->liveopencart_poip->getCurrentVersion();
		$data['config_admin_language']	= $this->config->get('config_admin_language');
		
		$this->document->setTitle($this->language->get('module_name'));
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
				
		$this->response->setOutput($this->load->view('extension/module/product_option_image_pro', $data));
		
	}
	
	
	
	
	private function PHPExcelPath($short = false) {
		if ($short) {
			return './system/PHPExcel/Classes/PHPExcel.php';
		} else {
			return DIR_SYSTEM . '/PHPExcel/Classes/PHPExcel.php';
		}
	}
	
	
	public function import() {
		
		//$this->load->model('module/product_option_image_pro');
		
		$json = array();
		
		if (!empty($this->request->files['file']['name'])) {
			
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			
			require_once $this->PHPExcelPath();
			
			$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp; //PHPExcel_CachedObjectStorageFactory::cache_to_discISAM ; //
			$cacheSettings = array( 'memoryCacheSize' => '32MB');
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			
			$excel = PHPExcel_IOFactory::load($this->request->files['file']['tmp_name']); // PHPExcel
			$sheet = $excel->getSheet(0);
			
			$data = $sheet->toArray();
			
			
			if (count($data) > 1) {
				
				if (isset($this->request->post['import_delete_before']) && $this->request->post['import_delete_before'] == 1) {
					$this->liveopencart_poip->getModel()->deleteAllImages();
				}
				
				foreach ($data[0] as $head_key => &$head_val) {
					$head_val = trim($head_val);
				}
				unset($head_val);
				$head = array_flip($data[0]);
				
				
				if (!isset($head['product_id'])) {
					$json['error'] = "product_id not found";
				}
				
				if (!isset($head['image'])) {
					$json['error'] = "image not found";
				}
				
				if (!isset($head['option_value_id'])) {
					$json['error'] = "option_value_id not found";
				}
				
				if (!isset($json['error'])) {
					
					$images = 0;
					$json['added'] = array();
					$json['skipped'] = array();
					$json['not_found'] = array();
					$json['already_exist'] = array();
					$json['skipped'] = array();
					
					for ($i=1;$i<count($data);$i++) {
						
						$row = $data[$i];
						
						if (trim((string)$row[$head['image']]) != "") {
							
							$result = $this->liveopencart_poip->getModel()->addProductOptionValueImage((int)$row[$head['product_id']], (int)$row[$head['option_value_id']], (string)$row[$head['image']]);
							if ( $result == 1 ) { // added
								$json['added'][] = $i;
							} elseif ( $result == 0 ) {
								$json['not_found'][] = $i;
							} elseif ( $result == -1 ) {
								$json['already_exist'][] = $i;
							} else {
								$json['skipped'][] = $i;
							}
						
						}
						
					}
					
					$json['rows'] = count($data)-1;
					
					
				}
				
			} else {
				$json['error'] = "empty table";
			}
			
			
			
		} else {
			$json['error'] = "file not uploaded";
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function export() {
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
		
			ini_set('display_errors', 1);
			error_reporting(E_ERROR|E_PARSE);
			
			$include_options_without_images = isset($this->request->post['include_options_without_images']) ? $this->request->post['include_options_without_images'] : false;
			$include_names = isset($this->request->post['include_names']) ? $this->request->post['include_names'] : false;
			$data = $this->liveopencart_poip->getModel()->getAllImages($include_options_without_images, $include_names);
			
			require_once $this->PHPExcelPath();
			
			PHPExcel_Shared_File::setUseUploadTempDirectory(true);
			
			$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM; //PHPExcel_CachedObjectStorageFactory::cache_to_discISAM ; //
			$cacheSettings = array( 'memoryCacheSize' => '32MB');
			if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings)) {
				$this->log->write("Product Options Images PRO: PHPExcel cache error");
			}
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'product_id');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'option_value_id');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'image');
			if ( $include_names ) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'product_name');
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, 'option_name');
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, 'option_value_name');
			}
			
			
			$objPHPExcel->getActiveSheet()->fromArray($data,null,'A2');
			unset($data);

			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			
			$file = DIR_CACHE."/poip_export.xls";
			
			$objWriter->save($file);
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			// read file and send to user
			readfile($file);
			exit;
			
		}
	}
	
	
	public function install() {
    $this->liveopencart_poip->getModel()->install();
		
		$this->model_setting_setting->editSetting('module_product_option_image_pro', array('module_product_option_image_pro_status'=>1)); // status = enabled
  }
  
  public function uninstall() {
		$this->liveopencart_poip->getModel()->uninstall();
  }
	
	private function validate() {
		if ( !$this->user->hasPermission('modify', 'extension/module/product_option_image_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
