<?php
class StripeapiController extends SiteCommon
{
	
	public function beforeAction($action)
	{							
		
		$method = Yii::app()->getRequest()->getRequestType();
		if($method!="PUT"){
			return false;
		}
		
		if(Yii::app()->user->isGuest){			
			return false;
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
	
	public function actionCreateCustomer()
	{				
		try {
						
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;		
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';		
			$merchant_type = isset($this->data['merchant_type'])?$this->data['merchant_type']:'';
								
			$credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
			
			$full_name = Yii::app()->user->first_name." ".Yii::app()->user->last_name;
			
			$model = AR_client_meta::model()->find('client_id=:client_id AND meta1=:meta1 AND meta2=:meta2 
			AND meta3=:meta3 ', 
		    array( 
		      ':client_id'=>intval(Yii::app()->user->id),
		      ':meta1'=>$payment_code,
		      ':meta2'=>$is_live,
		      ':meta3'=>isset($credentials['merchant_id'])?$credentials['merchant_id']:'',
		    )); 			    
			
		    \Stripe\Stripe::setApiKey( isset($credentials['attr1'])?$credentials['attr1']:'' );
		    
		    $create = false; $customer_id='';
		    if($model){
		    	if(empty($model->meta4)){
		    		$create = true;
		    	} else $customer_id = $model->meta4;
		    } else $create = true;
			
		    if($create){
		    	$customer = \Stripe\Customer::create([
				  'email'=>Yii::app()->user->email_address,
				  'name'=>$full_name
				]);						    
				$customer_id = $customer->id;
				$model = new AR_client_meta;
		    	$model->client_id = intval(Yii::app()->user->id);
		    	$model->meta1 = $payment_code;
		    	$model->meta2 = $is_live;
		    	$model->meta3 = $credentials['merchant_id'];
		    	$model->meta4 = $customer_id;
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
		      'customer_id'=>$customer_id,
		      'test_card'=>false
		    );
		    
		    if(DEMO_MODE){
				$this->details['test_card'] = array(
				  'card_number'=>'4242424242424242',
				  'expiry'=>'Future date',
				  'cvv'=>'123',
				  'zip'=>'12345',				  
				);
			}
		    
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());							
		}			
		$this->responseJson();	
	}
	
	public function actionsavePayment()
	{
		try {
															
			$payment_method_id = isset($this->data['payment_method_id'])?$this->data['payment_method_id']:'';				
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
			$merchant_type = isset($this->data['merchant_type'])?$this->data['merchant_type']:2;
						
			$credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;
						
			$model = AR_client_meta::model()->find('client_id=:client_id AND meta1=:meta1 AND meta2=:meta2 
			AND meta3=:meta3 ', 
		    array( 
		      ':client_id'=>intval(Yii::app()->user->id),
		      ':meta1'=>$payment_code,
		      ':meta2'=>$is_live,
		      ':meta3'=>$credentials['merchant_id'],
		    )); 	
		    if($model){		    	
		    	
		    	\Stripe\Stripe::setApiKey($credentials['attr1']);
		    	$payment = \Stripe\PaymentMethod::retrieve($payment_method_id,[]);
		    	
		    	$mask_card = CommonUtility::mask("111111111111".$payment->card->last4);
		    	
		    	$gateway =  AR_payment_gateway::model()->find('payment_code=:payment_code', 
			    array(':payment_code'=>$payment_code)); 
			    
			    $model_method = new AR_client_payment_method;
			    $model_method->client_id = intval(Yii::app()->user->id);
			    $model_method->payment_code = $payment_code;
			    $model_method->as_default = 1;
			    $model_method->attr1 = $gateway?$gateway->payment_name:'';
			    $model_method->attr2 = $mask_card;			
			    $model_method->merchant_id = isset($credentials['merchant_id'])? intval($credentials['merchant_id']) :0;
		    	
			    $model_method->method_meta = array(
			      array(
			        'meta_name'=>'payment_customer_id',
			        'meta_value'=>$model->meta4,
			        'date_created'=>CommonUtility::dateNow(),
			      ),
			      array(
			        'meta_name'=>'payment_method_id',
			        'meta_value'=>$payment_method_id,
			        'date_created'=>CommonUtility::dateNow(),
			      ),
			      array(
			        'meta_name'=>'is_live',
			        'meta_value'=>$is_live,
			        'date_created'=>CommonUtility::dateNow(),
			      ),
			    );
			    
			    if($model_method->save()){
			    	$this->code = 1; 
			    	$this->msg = "OK";			    	
			    } else $this->msg = CommonUtility::parseError( $model_method->getErrors());	
			    
		    } else $this->msg = t("Customer payment details not found");
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());							
		}			
		$this->responseJson();	
	}
	
	public function actioncreateintent()
	{
		try {
								
			$customer_id = isset($this->data['customer_id'])?$this->data['customer_id']:'';		
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
			$merchant_type = isset($this->data['merchant_type'])?$this->data['merchant_type']:2;
						
			$credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;
						
					
			\Stripe\Stripe::setApiKey( isset($credentials['attr1'])?$credentials['attr1']:'' );
			
			$intent = \Stripe\SetupIntent::create([
			    'customer' => $customer_id
			]);
			
			$client_secret = $intent->client_secret;
			
		    $this->code = 1;
		    $this->msg = "OK";
		    $this->details = array(
		      'client_secret'=>$client_secret,		      
		    );		    
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());							
		}			
		$this->responseJson();	
	}
	
	public function actionPaymentIntent()
	{
		try {	
					  
		   
		   $order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
		   $cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		   $payment_uuid = isset($this->data['payment_uuid'])?$this->data['payment_uuid']:'';
		   		   		  
		   $data = AR_ordernew::model()->find('order_uuid=:order_uuid',array(':order_uuid'=>$order_uuid)); 		   		  
		   if($data){
		   	
		   	  $merchant = CMerchantListingV1::getMerchant($data->merchant_id);		    	
		      $credentials = CPayments::getPaymentCredentials($data->merchant_id,$data->payment_code,
		      $merchant->merchant_type);
			  $credentials = isset($credentials[$data->payment_code])?$credentials[$data->payment_code]:'';
			  $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;   
			  			  
			  $payment_method = CPayments::getPaymentMethodMeta($payment_uuid, Yii::app()->user->id );
			  
			  $payment_description = t("Payment to merchant [merchant]. Order#[order_id]",
		      array('[merchant]'=>$merchant->restaurant_name,'[order_id]'=>$data->order_id ));	
		      
		      $total = floatval(Price_Formatter::convertToRaw($data->total));
		      
		      \Stripe\Stripe::setApiKey($credentials['attr1']);		
		      
		       $payment_intent = \Stripe\PaymentIntent::create([
			    'amount' => ($total*100),
			    'currency' => Price_Formatter::$number_format['currency_code'],
			    'customer' =>  isset($payment_method['payment_customer_id'])?$payment_method['payment_customer_id']:'' ,
			    'payment_method' => isset($payment_method['payment_method_id'])?$payment_method['payment_method_id']:'' ,
			    'off_session' => true,
			    'confirm' => true,
			    'description'=> $payment_description
			  ]);      
			  
			  $transaction_id = $payment_intent->id;		      	  
		      
		      $data->scenario = "new_order";
	    	  $data->status = COrders::newOrderStatus();
	    	  $data->payment_status = CPayments::paidStatus();
	    	  $data->cart_uuid = $cart_uuid;
	    	  $data->save();
	    			    			    	
	    	  $model = new AR_ordernew_transaction;
			  $model->order_id = $data->order_id;
			  $model->merchant_id = $data->merchant_id;
			  $model->client_id = $data->client_id;
			  $model->payment_code = $data->payment_code;
			  $model->trans_amount = $data->total;
			  $model->currency_code = Price_Formatter::$number_format['currency_code'];				
			  $model->payment_reference = $transaction_id;
			  $model->status = CPayments::paidStatus();
			  $model->reason = '';
			  $model->payment_uuid = $payment_uuid;
			  $model->save();
			  
			  $this->code = 1;
			  $this->msg = t("Payment successful. please wait while we redirect you.");
			
			  $redirect = Yii::app()->createAbsoluteUrl("orders/index",array(
			    'order_uuid'=>$data->order_uuid
			  ));					
			  $this->details = array(  					  
			    'redirect'=>$redirect
			  );
			  			  
		   } else $this->msg = t("Order id not found");
		   
		} catch (Exception $e) {
			$this->msg = t($e->getMessage());							
		}					
		$this->responseJson();	
	}
	
}
/*end index*/