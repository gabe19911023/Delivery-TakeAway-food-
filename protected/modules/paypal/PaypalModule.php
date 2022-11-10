<?php
class PaypalModule extends CWebModule
{	
	
	public function init()
	{
		$this->setImport(array(			
			'paypal.components.*',
			'paypal.models.*'
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
		 	
		 	$payment_code = $payment->payment_code;
		 	$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;				 	
		 	
		 	$refund_amount = Price_Formatter::convertToRaw($transaction->trans_amount);
		 	
		 	CPaypalTokens::setProduction($is_live);
	        CPaypalTokens::setCredentials($credentials,$payment_code);
	        $token = CPaypalTokens::getToken(date("c"));
	        
	        CPaypal::setProduction($is_live);
	        CPaypal::setToken($token);		
	        
	        if($transaction->transaction_name=="partial_refund"){
				$refund_resp = CPaypal::refund( $payment->payment_reference , $refund_amount, $transaction->currency_code );				
			} else {
				$refund_resp = CPaypal::refund( $payment->payment_reference );				
			}					 
	        return array(
			  'id'=> isset($refund_resp['id'])?$refund_resp['id']:''
			);						
		 } catch (Exception $e) {			
			throw new Exception( $e->getMessage() );
		}
	}
	
}
/*end class*/