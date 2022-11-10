<?php
require 'razorpay/vendor/autoload.php';
use Razorpay\Api\Api;

class RazorpayModule extends CWebModule
{	
	
	public function init()
	{
		$this->setImport(array(			
			'razorpay.components.*',
			'razorpay.models.*'
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
	
	public function delete($data)
	{		
		AR_payment_method_meta::model()->deleteAll("payment_method_id=:payment_method_id",array(
		  ':payment_method_id'=>$data->payment_method_id
		));		
	}
	
	public function refund($credentials=array(), $transaction=array(), $payment = array())
	{
		try {
			
			$refund_amount = Price_Formatter::convertToRaw($transaction->trans_amount);
			
			$api = new Api($credentials['attr1'], $credentials['attr2']);
			
			$refund_resp = $api->payment->fetch($payment->payment_reference)->refund(array(
				'amount'=>($refund_amount*100),
				'speed'=>"normal",
				'notes'=>array(
				  'order_id'=>$transaction->order_id,			  	   	      	      
				),
				'receipt'=>$transaction->order_id
			 ));			  	   	      	  
			 return array(
			   'id'=>$refund_resp->id
			 );						
		} catch (Exception $e) {			
			throw new Exception( $e->getMessage() );
		}
	}
	
}
/*end class*/