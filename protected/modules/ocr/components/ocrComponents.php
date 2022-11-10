<?php
class ocrComponents extends CWidget 
{
	
	public $data;
	public $credentials;
	
	public function run() {				
		$this->render('ocr-components',array(
		    'payment_code'=>$this->data['payment_code'],
		    'credentials'=>$this->credentials
		));
	}
	
}
/*end class*/