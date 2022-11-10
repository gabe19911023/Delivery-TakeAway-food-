<?php
class MercadopagoModule extends CWebModule
{	
	
	public function init()
	{
		$this->setImport(array(			
			'mercadopago.components.*',
			'mercadopago.models.*'
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
			
			$acess_token = isset($credentials['attr2'])?trim($credentials['attr2']):'';
			require_once 'mercadopago/vendor/autoload.php';
			MercadoPago\SDK::setAccessToken($acess_token);			
						
			$data = AR_ordernew_trans_meta::model()->find("order_id=:order_id AND meta_name=:meta_name",array(
			  ':order_id'=>$transaction->order_id,
			  ':meta_name'=>'status'
			));
			
			if($data){
			   $status = trim($data->meta_value);			   
			   switch ($status) {
			   	case "approved":					   	    
			   	    if($transaction->transaction_name=="partial_refund"){
			   	       $refund_resp = MercadoPago\Payment::find_by_id($payment->payment_reference);
			   	       $refund_resp->refund($refund_amount);
			   	    } else {
			   	       $refund_resp = MercadoPago\Payment::find_by_id($payment->payment_reference);
			   	       $refund_resp->refund();
			   	    }			   	    
			   	    return array(
					  'id'=>$refund_resp->id
					);						
			   		break;		
			   			   			   	
			   	case "pending":			   		
			   	case "in_process":
			   		break;		
			   			
			   }
			} else throw new Exception( "Data not found" );
		} catch (Exception $e) {			
			throw new Exception( $e->getMessage() );
		}
	}
	
	private function getPayment($payment_reference='',$access_token='')
	{				
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/v1/payments/'.$payment_reference);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
				
		$headers = array();
		$headers[] = 'Authorization: Bearer '.$access_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($ch);		
		if (curl_errno($ch)) {		   
		    throw new Exception( 'Error:' . curl_error($ch) );
		}
		curl_close($ch);
		
		if($json=json_decode($result,true)){			
			$status = isset($json['status'])?$json['status']:'';
			$message = isset($json['message'])?$json['message']:'';			
			if(!empty($message) && $status>0){
				throw new Exception( $message );
			} else return $json;
		}
		throw new Exception( 'no results' );
	}
}
/*end class*/