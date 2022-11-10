<?php
class mercadopagoComponents extends CWidget 
{
	public $data;
	public $credentials;
	
	public function run() {			
		$this->render('mercadopago-components',array(
		  'payment_code'=>$this->data['payment_code'],
		  'credentials'=>$this->credentials
		));
	}
	
}
/*end class*/