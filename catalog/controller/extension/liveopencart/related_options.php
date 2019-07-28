<?php
//  Related Options / Связанные опции 
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ControllerExtensionLiveopencartRelatedOptions extends Controller {
	
  public function get_ro_free_quantity() {
    

		$this->load->model('extension/liveopencart/related_options');
		
		$json = $this->model_extension_liveopencart_related_options->get_ro_free_quantity();
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
      

  }
  
}
