<?php
class codComponents extends CWidget 
{
	public $data;
	public $credentials;
	
	public function run() {				
		$this->render('cod-components',array(
		    'payment_code'=>$this->data['payment_code'],
		    'credentials'=>$this->credentials
		));
	}
	
}
/*end class*/