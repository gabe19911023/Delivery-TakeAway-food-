<?php
class stripeComponents extends CWidget 
{
	public $data;
	public $credentials;
	
	public function run() {									
		$this->render('stripe-components',array(
		  'payment_code'=>$this->data['payment_code'],		  
		  'credentials'=>$this->credentials,		  
		  'prefix'=>isset($this->data['prefix'])?$this->data['prefix']:'',
		  'reference'=>isset($this->data['reference'])?$this->data['reference']:'',
		));
	}
	
}
/*end class*/