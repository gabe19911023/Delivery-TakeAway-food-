<?php
class managestripeComponents extends CWidget 
{
	public $data;
	public $credentials;
	
	public function run() {			
		$this->render('stripe-manage-components',array(
		  'payment_code'=>$this->data['payment_code'],
		  'credentials'=>$this->credentials
		));
	}
	
}
/*end class*/