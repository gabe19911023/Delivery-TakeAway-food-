<?php
class CashinController extends SiteCommon
{
	
	public function beforeAction($action)
	{							
		
		$method = Yii::app()->getRequest()->getRequestType();
		if($method!="PUT"){
			//return false;
		}
			
		Price_Formatter::init();
		$method = Yii::app()->getRequest()->getRequestType();
		if($method=="PUT"){
			$this->data = Yii::app()->input->xssClean(json_decode(file_get_contents('php://input'), true));
		} else $this->data = Yii::app()->input->xssClean($_POST);				
		
		require 'stripe/vendor/autoload.php';
		
		return true;
	}
	
	public function actionIndex()
	{
		//
	}
	
	public function actioncreatecustomermerchant()
	{
		try {
										
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;		
            $payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';		
            $merchant_type = isset($this->data['merchant_type'])?$this->data['merchant_type']:'';
            $merchant_uuid = isset($this->data['reference'])?$this->data['reference']:'';
            
            $credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
            $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
            $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
                        
            $merchant = CMerchants::getByUUID($merchant_uuid);
            
            $full_name = $merchant->restaurant_name;
            $email_address = $merchant->contact_email;
            
            $model = AR_merchant_meta::model()->find('merchant_id=:merchant_id AND meta_name=:meta_name AND meta_value=:meta_value', 
	        array( 
	          ':merchant_id'=>intval($merchant->merchant_id),
	          ':meta_name'=>$payment_code,
	          ':meta_value'=>$is_live,	          
	        )); 		
	        	        
	        \Stripe\Stripe::setApiKey( isset($credentials['attr1'])?$credentials['attr1']:'' );
	        
	         $create = false; $customer_id='';	         
	         if($model){
	            if(empty($model->meta_value1)){
	                $create = true;
	            } else $customer_id = $model->meta_value1;
	         } else $create = true;
	         	         
	         if($create){
	         	 $customer = \Stripe\Customer::create([
	              'email'=>$email_address,
	              'name'=>$full_name
	            ]);			            
	            $customer_id = $customer->id;	            
	            $model = new AR_merchant_meta;
	            $model->merchant_id = intval($merchant->merchant_id);
	            $model->meta_name = $payment_code;
	            $model->meta_value = $is_live;	            
	            $model->meta_value1 = $customer_id;
	            $model->save();			
	         } 
	         
	         $client_secret = '';
	         
	         $intent = \Stripe\SetupIntent::create([
	            'customer' => $customer_id
	         ]);
	        
	         $client_secret = $intent->client_secret;
	        
	         $this->code = 1;
	         $this->msg = "OK";
	         $this->details = array(
	           'client_secret'=>$client_secret,
	           'customer_id'=>$customer_id
	         );
	                 			
		} catch (Exception $e) {
			$this->msg = t($e->getMessage());		
		}					
		$this->responseJson();	
	}
	
	public function actionsavepaymentmerchant()
	{
		try {
						
			$payment_method_id = isset($this->data['payment_method_id'])?$this->data['payment_method_id']:'';				
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
			$merchant_type = isset($this->data['merchant_type'])?$this->data['merchant_type']:2;
			$merchant_uuid = isset($this->data['reference'])?$this->data['reference']:'';
									
			$credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;
			
			$merchant = CMerchants::getByUUID($merchant_uuid);
			
			\Stripe\Stripe::setApiKey($credentials['attr1']);
		    $payment = \Stripe\PaymentMethod::retrieve($payment_method_id,[]);		    
		    
		    $mask_card = CommonUtility::mask("111111111111".$payment->card->last4);		    
		    $exp_month = $payment->card->exp_month;
		    $exp_year = $payment->card->exp_year;
		    $finger_print = $payment->card->fingerprint;
		    
		    $gateway =  AR_payment_gateway::model()->find('payment_code=:payment_code', 
			    array(':payment_code'=>$payment_code)); 

			$model_method = new AR_merchant_payment_method;
		    $model_method->merchant_id = intval($merchant->merchant_id);
		    $model_method->payment_code = $payment_code;
		    $model_method->as_default = 1;
		    $model_method->attr1 = $gateway?$gateway->payment_name:'';
		    $model_method->attr2 = $mask_card;		    
		    $model_method->attr3 = $exp_month;		    
		    $model_method->attr4 = $exp_year;		    
		    $model_method->attr5 = $finger_print;	
		    $model_method->payment_refence = $payment_method_id;	
		    
		    if($model_method->save()){
		    	$this->code = 1; 
		    	$this->msg = "OK";			    	
		    } else $this->msg = CommonUtility::parseError( $model_method->getErrors());	
			
		} catch (Exception $e) {
			$this->msg = t($e->getMessage());					
		}							
		$this->responseJson();	
	}
	
	public function actionpaymentintentcashin()
	{
		try {			
				            
            $payment_uuid = isset($this->data['payment_uuid'])?$this->data['payment_uuid']:'';	
            $merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';	
            $payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';	
            $amount = isset($this->data['amount'])?floatval($this->data['amount']):0;	        
            $cashin_min = AttributesTools::CashinMinimumAmount();
            
            if($cashin_min>$amount){
            	$this->msg = t("Cash in minimum amount is {{amount}}",array(
            	  '{{amount}}'=>Price_Formatter::formatNumber($cashin_min)
            	));
            	$this->responseJson();	
            }            
            
            $merchant = CMerchants::getByUUID($merchant_uuid);            
            $payment = CPayments::getMerchantPayment($merchant->merchant_id,$payment_uuid);                        
            
            $credentials = CPayments::getPaymentCredentials(0,$payment_code,2);
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;

			$customer = AR_merchant_meta::model()->find('merchant_id=:merchant_id AND meta_name=:meta_name AND meta_value=:meta_value', 
	        array( 
	          ':merchant_id'=>intval($merchant->merchant_id),
	          ':meta_name'=>$payment_code,
	          ':meta_value'=>$is_live,	          
	        )); 		
	        
	        if($customer){
		        $payment_description = t("Cash in by merchant {{restaurant_name}}",
	             array('{{restaurant_name}}'=>$merchant->restaurant_name));	
	                         
	            \Stripe\Stripe::setApiKey($credentials['attr1']);		
	             
		        $payment_intent = \Stripe\PaymentIntent::create([
		            'amount' => ($amount*100),
		            'currency' => Price_Formatter::$number_format['currency_code'],
		            'customer' =>  $customer->meta_value1,
		            'payment_method' => $payment->payment_refence,
		            'off_session' => true,
		            'confirm' => true,
		            'description'=> $payment_description
		        ]);  
		        $transaction_id = $payment_intent->id;		  
		        
		        $card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'], $merchant->merchant_id );
		        
		        $params = array(					  		 
			      'transaction_description'=>"Cash in",			      
			      'transaction_type'=>"cashin",
			      'transaction_amount'=>$amount,
			      'status'=>'paid',		
			      'meta_name'=>"transaction_reference",
			      'meta_value'=>$transaction_id,
			    );
		        $resp = CWallet::inserTransactions($card_id,$params);	    	
		        
		        $this->code = 1;
			    $this->msg = t("Payment successful. please wait while we redirect you.");				
			    $this->details = array(  					  
				   'redirect'=>Yii::app()->createAbsoluteUrl("merchant/cashin-successful")
				);
		        
	        } else $this->msg = t("Customer payment id not found");
            			
		} catch (Exception $e) {
			$this->msg = t($e->getMessage());					
		}		
		$this->responseJson();	
	}
		
	
}
/*end class*/