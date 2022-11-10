<?php
class StripeModule extends CWebModule
{	
	public static function paymentCode()
	{
		return 'stripe';
	}
	
	public function init()
	{
		$this->setImport(array(			
			'stripe.components.*',
			'stripe.models.*'
		));
	}
		
	public function beforeControllerAction($controller, $action)
	{									
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here									
			return true;
		}
		else
			return false;
	}
	
	public function paymentInstructions()
	{
		return array(
		  'method'=>"online",
		  'redirect'=>''
		);
	}
	
	public function savedTransaction($data)
	{					
		
	}
		
}
/*end class*/