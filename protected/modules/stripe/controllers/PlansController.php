<?php
class PlansController extends SiteCommon
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
		
	public function actioncreateMerchantAccount()
	{
		try {
						
			$merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
								
			$merchant = CMerchants::getByUUID($merchant_uuid);
			
			$credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
			
			$model = AR_merchant_meta::model()->find('merchant_id=:merchant_id AND meta_name=:meta_name AND meta_value=:meta_value ', 
		    array( 
		      ':merchant_id'=>intval($merchant->merchant_id),
		      ':meta_name'=>$payment_code,		      
		      ':meta_value'=>$is_live,		      
		    )); 	
		    		    
		    $create = false; $customer_id='';
		    if($model){
		    	if(empty($model->meta_value1)){
		    		$create = true;
		    	} else $customer_id = $model->meta_value1;
		    } else $create = true;
			
		    if($create){		    	   
		       \Stripe\Stripe::setApiKey( isset($credentials['attr1'])?$credentials['attr1']:'' );
		       $customer = \Stripe\Customer::create([
				  'email'=>$merchant->contact_email,
				  'name'=>Yii::app()->input->xssClean($merchant->restaurant_name) 
				]);			
				$customer_id = $customer->id;
				$model = new AR_merchant_meta;
		    	$model->merchant_id = intval($merchant->merchant_id);
		    	$model->meta_name = $payment_code;
		    	$model->meta_value = $is_live;
		    	$model->meta_value1 = $customer_id;		    	
		    	$model->save();		
		    }
		    
		    $this->code = 1;
		    $this->msg = "OK";
		    $this->details = array(		      
		      'customer_id'=>$customer_id
		    );			
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());			
		}					
		$this->responseJson();	
	}
	
	public function actionsubscribeAccount()
	{
		try {
			
			$merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
			$package_uuid = isset($this->data['package_uuid'])?$this->data['package_uuid']:'';
			$customer_id = isset($this->data['customer_id'])?$this->data['customer_id']:'';
				
			$merchant = CMerchants::getByUUID($merchant_uuid);
			$plans = Cplans::getByUUID($package_uuid);			
			$meta_name = "plan_price_$payment_code";			
			
			$price = Cplans::planPriceID($meta_name,$plans->package_id);
			$price_id = $price->meta_value;
			
			$credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
			
			
			$meta_name = "subscription_$payment_code";
			
			$model = AR_merchant_meta::model()->find('
			merchant_id=:merchant_id AND 			
			meta_name=:meta_name AND 
			meta_value=:meta_value AND 
			meta_value1=:meta_value1 ', 
		    array( 
		      ':merchant_id'=>intval($merchant->merchant_id),
		      ':meta_name'=>$meta_name,		      
		      ':meta_value'=>$plans->package_id,		      
		      ':meta_value1'=>$is_live,		      
		    )); 	
		    		    
		    $create = false; $subscriber_id=''; $client_secret='';
		    if($model){
		    	if(empty($model->meta_value3)){
		    		$create = true;
		    	} else $client_secret = $model->meta_value3;
		    } else $create = true;
		    
		    		    
		    $end = '';
		    if($plans->trial_period>0){		    	
			    $dateTime = new DateTime(date('c'));			    
			    $dateTime->modify("+$plans->trial_period days");		    
			    $end = $dateTime->format( "Y-m-d H:i");			    			    
			    $end = strtotime($end);			    			    
		    }
		    		    
		    if($create){		
		    	$model = new AR_merchant_meta;		    	
		    	$stripe = new \Stripe\StripeClient(isset($credentials['attr1'])?$credentials['attr1']:'');	    			    	
		    	
		    	if($plans->trial_period>0){
		    		$subscription = $stripe->subscriptions->create([
				        'customer' => $customer_id,
				        'items' => [[
				            'price' => $price_id,
				        ]],
				        'metadata'=>array(
				          'merchant_uuid'=>$merchant_uuid,
				        ),
				        'payment_behavior' => 'default_incomplete',
				        'expand' => ['latest_invoice.payment_intent'],
				        'trial_end'=>$end
				    ]);		
		    	} else {
			    	$subscription = $stripe->subscriptions->create([
				        'customer' => $customer_id,
				        'items' => [[
				            'price' => $price_id,
				        ]],
				        'metadata'=>array(
				          'merchant_uuid'=>$merchant_uuid,
				        ),
				        'payment_behavior' => 'default_incomplete',
				        'expand' => ['latest_invoice.payment_intent'],				        
				    ]);		
		    	}				    			   
			    				
			    $client_secret='';
			    if(isset($subscription->latest_invoice->payment_intent->client_secret)){
			    	$client_secret = $subscription->latest_invoice->payment_intent->client_secret;
			    } else {			    	
			    	$this->code = 3;
			    	$this->msg = "ok";
			    	$this->details = array(
			    	  'subscriber_id'=>$subscription->id
			    	);			    	
			    }
			    
			    $model->merchant_id = $merchant->merchant_id;
				$model->meta_name = $meta_name;
				$model->meta_value = $plans->package_id;
				$model->meta_value1 = $is_live;
				$model->meta_value2 = $subscription->id;			
				$model->meta_value3 = $client_secret;
				$model->save();
		    }
		    
		    if($this->code==2){
			    $this->code = 1;
			    $this->msg = "OK";
			    $this->details = array(		      
			      'client_secret'=>$client_secret
			    );		
		    }
				
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());						
		}					
		$this->responseJson();	
	}

	public function actionvalidate()
	{
		try {
						
			$merchant_uuid = Yii::app()->input->get('merchant_uuid');  
			$package_uuid = Yii::app()->input->get('package_uuid');  
			$payment_code = Yii::app()->input->get('payment_code');  
			$payment_intent = Yii::app()->input->get('payment_intent');  
			$payment_intent_client_secret = Yii::app()->input->get('payment_intent_client_secret');  
			$redirect_status = Yii::app()->input->get('redirect_status');  
			
			$merchant = CMerchants::getByUUID($merchant_uuid);
			$plans = Cplans::getByUUID($package_uuid);			
			
			$credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
			
			$stripe = new \Stripe\StripeClient(isset($credentials['attr1'])?$credentials['attr1']:'');
			$resp = $stripe->paymentIntents->retrieve($payment_intent,[]);
									
			switch ($resp->status) {
				case "succeeded":				
				    $merchant->scenario = "after_payment_validate";
					$merchant->status = 'active';
					$merchant->package_id = intval($plans->package_id);
					$merchant->save();						
					$this->redirect(Yii::app()->createUrl('/merchant/thankyou'));			
					break;			
					
				case "processing":					    
				    $this->redirect(Yii::app()->createUrl('/merchant/payment-processing'));
				    break;
				    
				case "requires_payment_method":		
				    $message = t("Payment failed. Please try another payment method.");			    
				    $this->redirect(Yii::app()->createUrl('/merchant/choose_plan',array(
				      'uuid'=>$merchant_uuid,
				      'message'=>$message
				    )));
				    break;
				        
				default:					
				    $this->redirect(Yii::app()->createUrl('/merchant/signup-failed'));
					break;
			}
			
		} catch (Exception $e) {
			$error = t($e->getMessage());			
			$this->redirect(Yii::app()->createUrl('/merchant/error',array('message'=>$error)));
		}			
	}
	
	public function actiontrial_validate()
	{
		try {
						
			$merchant_uuid = Yii::app()->input->get('merchant_uuid');  
			$package_uuid = Yii::app()->input->get('package_uuid');  
			$payment_code = Yii::app()->input->get('payment_code');  
			$subscriber_id = Yii::app()->input->get('subscriber_id');  

			$merchant = CMerchants::getByUUID($merchant_uuid);		
			$plans = Cplans::getByUUID($package_uuid);	
			
			$credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
			
			$stripe = new \Stripe\StripeClient(isset($credentials['attr1'])?$credentials['attr1']:'');
			
			$subscription = $stripe->subscriptions->retrieve($subscriber_id,[]);
			
			switch ($subscription->status) {
				case "active":
				case "trialing":
					$merchant->scenario = "after_payment_validate";
					$merchant->status = 'active';
					$merchant->package_id = intval($plans->package_id);
					$merchant->save();						
					$this->redirect(Yii::app()->createUrl('/merchant/thankyou'));
					break;
			
				default:
					echo $subscription->status;
					die();
					$this->redirect(Yii::app()->createUrl('/merchant/signup-failed'));
					break;
			}
			
		} catch (Exception $e) {
			$error = t($e->getMessage());			
			dump($error); 
			//$this->redirect(Yii::app()->createUrl('/merchant/error',array('message'=>$error)));
		}			
	}
	
}
/*end index*/