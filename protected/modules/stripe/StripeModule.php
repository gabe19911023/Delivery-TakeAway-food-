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
	
	public function delete($data)
	{		
		AR_payment_method_meta::model()->deleteAll("payment_method_id=:payment_method_id",array(
		  ':payment_method_id'=>$data->payment_method_id
		));		
	}
	
	public function deletePaymentMerchant($data)
	{
		AR_merchant_payment_method::model()->deleteAll("payment_method_id=:payment_method_id",array(
		  ':payment_method_id'=>$data->payment_method_id
		));		
	}
	
	public function refund($credentials=array(), $transaction=array(), $payment = array())
	{
		try {
						
			require 'stripe/vendor/autoload.php';
			\Stripe\Stripe::setApiKey( isset($credentials['attr1'])?$credentials['attr1']:'' );		
			
			$refund_amount = Price_Formatter::convertToRaw($transaction->trans_amount);
			
			if($transaction->transaction_name=="partial_refund"){
				$refund_resp = \Stripe\Refund::create([
				  'amount' => ($refund_amount*100),
				  'payment_intent' => $payment->payment_reference ,
				]);								
			} else {
			    $refund_resp = \Stripe\Refund::create([						  
			     'payment_intent' => $payment->payment_reference ,
			    ]);									    
			}
			return array(
			  'id'=>$refund_resp->id
			);						
		} catch (Exception $e) {			
			throw new Exception( $e->getMessage() );
		}
	}
	
}
/*end class*/