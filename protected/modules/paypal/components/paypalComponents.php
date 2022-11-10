<?php
class paypalComponents extends CWidget 
{
	public $data;
	public $credentials;
	
	public function run() {			
		$this->render('paypal-components',array(
		  'payment_code'=>$this->data['payment_code'],
		  'credentials'=>$this->credentials
		));
	}
	
}
/*end class*/