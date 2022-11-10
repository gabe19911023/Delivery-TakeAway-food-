<?php
class WebhooksController extends SiteCommon
{
	public $enableCsrfValidation = false;
	
	public function beforeAction($action)
	{							
		
		 Yii::app()->request->enableCsrfValidation = false;
		    
		$method = Yii::app()->getRequest()->getRequestType();
		if($method!="PUT"){
			//return false;
		}
									
		if($method=="PUT"){
			$this->data = Yii::app()->input->xssClean(json_decode(file_get_contents('php://input'), true));
		} 
		
		require 'stripe/vendor/autoload.php';
		
		return true;
	}
	
	public function actionIndex()
	{
		
		$payment_code = StripeModule::paymentCode();
	
		$credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
		$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
		$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;			
			
		$endpoint_secret = isset($credentials['attr3'])?$credentials['attr3']:'';
		
		$payload = @file_get_contents('php://input');
			
		$sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE'])?$_SERVER['HTTP_STRIPE_SIGNATURE']:'';
		
		$event = null;
					
		try {
		    $event = \Stripe\Webhook::constructEvent(
		    $payload, $sig_header, $endpoint_secret
		  );
		} catch(\UnexpectedValueException $e) {
		    // Invalid payload
		    dump($e->getMessage());
		    Yii::log( json_encode($e->getMessage()) , CLogger::LEVEL_ERROR);
		    http_response_code(400);
		    exit();
		} catch(\Stripe\Exception\SignatureVerificationException $e) {
		    // Invalid signature
		    dump($e->getMessage());
		    Yii::log( json_encode($e->getMessage()) , CLogger::LEVEL_ERROR);
		    http_response_code(400);
		    exit();
		}
		
		
		$stripe = new \Stripe\StripeClient(isset($credentials['attr1'])?$credentials['attr1']:'');
		
		$logs = array();
		$logs['event'] = $event->type;
		
		switch ($event->type) {
			case "invoice.payment_succeeded":
				try {
					$invoice = $event->data->object; 
					$subscription_id = $invoice->subscription;
	                $payment_intent_id = $invoice->payment_intent;
	                
	                
	                $payment_intent = $stripe->paymentIntents->retrieve(
				     $payment_intent_id,
				     []
				    );
				    
				    $logs['payment_method'] = $payment_intent->payment_method;
				    				    				   
				    $stripe->subscriptions->update($subscription_id,array(
				     'default_payment_method'=>$payment_intent->payment_method
				    ));
				    				    	                
	                $meta = AR_merchant_meta::model()->find('meta_value2=:meta_value2',array(
	                  ':meta_value2'=>$subscription_id,
	                ));
	                if($meta){
	                	AR_merchant_meta::saveMeta($meta->merchant_id,"payment_method_stripe", $payment_intent->payment_method);
	                } 
	                
	                $logs[] = "successful";
	                
                } catch (Exception $e) {
                	$logs[]  = $e->getMessage();
                }
				break;
				
			case "invoice.paid":	
			case "invoice.created":
			   $invoice = $event->data->object; 		
			   $subscription_id = $invoice->subscription;
			   $customer_id = $invoice->customer;
			   			   	  
			   $meta_name = "subscription_$payment_code";
			   
			   $find = AR_merchant_meta::model()->find("meta_value2=:meta_value2 ",array(			     
			     ':meta_value2'=>$subscription_id
			   ));
			   			   			   
			   if($find){			   	  
			   	  $find_invoice = AR_plans_invoice::model()->find("invoice_ref_number=:invoice_ref_number",array(
			   	   ':invoice_ref_number'=>$invoice->number
			   	  ));
			   	  if(!$find_invoice){
				   	  $model = new AR_plans_invoice;
				   	  $model->scenario = 'invoice_paid';
				   	  $model->payment_code = $payment_code;
				   	  $model->package_id = $find->meta_value;					  
					  $model->amount = floatval($invoice->total)/100;
					  $model->status = $invoice->status;
					  $model->invoice_ref_number = $invoice->number;
					  $model->payment_ref1 = $invoice->id;
					  $model->merchant_id = intval($find->merchant_id);
					  $model->created  = Date_Formatter::date($invoice->created,"yyyy-MM-dd H:mm:ss");
					  $model->due_date  = Date_Formatter::date($invoice->due_date,"yyyy-MM-dd H:mm:ss");
					  $model->save();
					  $logs['msg']="successful";
			   	  } else {
			   	  	  $logs['msg']="invoice already exist";
			   	  	  $find_invoice->status = $invoice->status;
			   	  	  $find_invoice->save();
			   	  }
			   } else $logs['msg']="subscription id not found";			   
			   break;
			   
			case 'invoice.payment_failed':
				
				$invoice = $event->data->object; 		
			    $subscription_id = $invoice->subscription;			    
			    
			    $find_invoice = AR_plans_invoice::model()->find("invoice_ref_number=:invoice_ref_number",array(
			   	   ':invoice_ref_number'=>$invoice->number
			   	));
			   	if($find_invoice){
			   		$find_invoice->status = 'failed';
			   		$find_invoice->save();
			   	} else $logs['msg']="invoice not found";			    			  
				
			 	break; 	   
			   			 
			case "customer.subscription.updated": 
			   
			    $invoice = $event->data->object; 		
			    $subscription_id = $invoice->id;			    
			    
			    $find = AR_merchant_meta::model()->find("meta_value2=:meta_value2 ",array(			     
			       ':meta_value2'=>$subscription_id
			    ));
			    if($find){
			    				    
			    	$logs['found'] = $find->merchant_id;
			    	$logs['status'] = $invoice->status;
			    	$merchant = CMerchants::get($find->merchant_id);
			    	
			    	if($invoice->status=="trialing" || $invoice->status=="active" ){
			    		if($merchant->status!='active'){	
			    			$merchant->scenario = 'after_payment_validate';		    						    		
				    		$merchant->status="active";
				    		$merchant->package_id = intval($find->meta_value);
				    		$merchant->save();
				    		$logs['actions']='active';
			    		}
			    	} else {
			    		$merchant->scenario = "plan_past_due";
			    		$merchant->status="expired";
			    		$merchant->save();
			    		$logs['actions']='expired';
			    	}
			    				    	
			    } else $logs['msg']="subscription id not found";		
			    
			    break;
			 	
			case "customer.subscription.trial_will_end": 	
			    $invoice = $event->data->object; 		
			    $subscription_id = $invoice->id;
			    
			    $find = AR_merchant_meta::model()->find("meta_value2=:meta_value2 ",array(			     
			       ':meta_value2'=>$subscription_id
			    ));
			    if($find){
			    	$logs['merchant_id'] = $find->merchant_id;			    	
			    	$merchant = CMerchants::get($find->merchant_id);
			    	
			    	$merchant->scenario = 'trial_will_end';
			    	$merchant->trial_end = $invoice->trial_end;
			    	$merchant->date_modified = CommonUtility::dateNow();
			    	$merchant->ip_address = CommonUtility::userIp();
			    	if($merchant->save()){
			    		$logs['msg']="updated";
			    	} else $logs['msg']="updated failed";
			    	
			    } else $logs['msg']="subscription id not found $subscription_id";
			    		    
			    break; 	
			 	
			case 'customer.subscription.deleted':
				try {
				   
					$invoice = $event->data->object; 
					$subscriber_id = $invoice->id;
					$customer_id = $invoice->customer;
					
					$subscription = AR_merchant_meta::model()->find('meta_value2=:meta_value2',array(
					  ':meta_value2'=>$subscriber_id
					));
					if($subscription){
						$subscription->scenario = 'cancel_subscription';
		                $subscription->delete();	
		                $logs['msg']="successful deleted";        
					} else {
						$logs['customer_id'] = $customer_id;
						$customer = AR_merchant_meta::model()->find('meta_value1=:meta_value1',array(
						  ':meta_value1'=>$customer_id
						));
						if($customer){
							$merchant = CMerchants::get($customer->merchant_id);
							$merchant->package_id = 0;
							$merchant->status = 'expired';
							$merchant->save();
							$logs['msg']="successful";
						}
					}
						
				} catch (Exception $e) {
                	$logs[]  = $e->getMessage();
                }
			 	break;			  

			case "payment_intent.succeeded":	
			    $payment_intent = $event->data->object; 			    
			    $model = AR_ordernew_transaction::model()->find("payment_reference=:payment_reference",array(
			      ':payment_reference'=>$payment_intent->id
			    ));
			    if($model){			    	
			    	try {
			    	   $order = COrders::getByID($model->order_id);			    	
			    	   if($order->payment_status!="paid"){
			    	   	  $order->payment_status = "paid";
			    	   	  $order->save();
			    	   } else $logs['msg'] = "status already paid";
			    	} catch (Exception $e) {
	                	$logs[]  = $e->getMessage();
	                }
			    } else $logs['msg'] = "Payment reference not found";
			    break;
		}
				
		Yii::log( json_encode($logs) , CLogger::LEVEL_ERROR);
		http_response_code(200);
	}
	
}
/*end class*/