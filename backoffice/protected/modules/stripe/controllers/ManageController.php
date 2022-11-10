<?php
class ManageController extends CommonServices
{		
	
	public $stripe;
	
	public function beforeAction($action)
	{								
		$method = Yii::app()->getRequest()->getRequestType();
		if($method=="PUT"){
			$this->data = Yii::app()->input->xssClean(json_decode(file_get_contents('php://input'), true));			
		} else $this->data = Yii::app()->input->xssClean($_POST);				
				
		$path=Yii::getPathOfAlias('home_dir');
		
		Yii::app()->setImport(array(			
		   'home_dir.protected.vendor.*',
	    ));
		
		require 'stripe/vendor/autoload.php';
		
		
		$merchant_id = Yii::app()->merchant->merchant_id;    
	    $payment_code = StripeModule::paymentCode();
	   
	    $credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
        $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:''; 
        $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;	
               
        try {
            $this->stripe = new \Stripe\StripeClient(isset($credentials['attr1'])?$credentials['attr1']:'');		
        } catch (Exception $e) {
		   $this->msg = $e->getMessage();		
		   $this->responseJson();   
		}		     
		
		return true;
	}
	
	public function actiongetmerchantplan()
	{
		$subscribe_link='';
		
		try {
					
		   $payment_code = StripeModule::paymentCode();
		   $merchant_id = Yii::app()->merchant->merchant_id;
		   
		   $credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
	       $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:''; 
	       $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;			  
						   
		   $merchant = CMerchants::get($merchant_id);		   
		   $subscribe_link = CMedia::homeUrl()."/merchant/choose_plan?".http_build_query(array('uuid'=>$merchant->merchant_uuid));
		   
		   $subscription = Cplans::getMechantSubcriptions($merchant_id,$is_live);	       
	       $subcriber_id = $subscription->meta_value2;	    
	       		     
		   $plans = Cplans::get($merchant->package_id);
		  			  
		   $paymen_method_id = ''; $last4 = ''; $brand='';
		   /*
		   $payment_method = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name",array(
		     ':merchant_id'=>intval($merchant_id),
		     ':meta_name'=>'payment_method_stripe'
		   ));
		  
		   if($payment_method){
		   	  $paymen_method_id = isset($payment_method['meta_value'])?$payment_method['meta_value']:'';
		  	  $last4 = isset($payment_method['meta_value1'])?$payment_method['meta_value1']:'';
		  	  $brand = isset($payment_method['meta_value2'])?$payment_method['meta_value2']:'';			  				  				  	
		  	  if(empty($last4) || empty($brand)){
		  	 	
		  	 	$stripe = new \Stripe\StripeClient(isset($credentials['attr1'])?$credentials['attr1']:'');
			  	$payment_details = $stripe->paymentMethods->retrieve(
				   $paymen_method_id,
				   []
				 );
			  	
			  	$last4 = $payment_details->card->last4;
			    $brand = $payment_details->card->brand;					    
			    
			    $payment_method->meta_value1 = $last4;
			    $payment_method->meta_value2 = $brand;
			    $payment_method->save();
		  	  }
		   }*/
		   
		   try {			   			   
		   	    $subscriptions = $this->stripe->subscriptions->retrieve(
				  $subcriber_id,
				  []
				);
				if(!empty($subscriptions->default_payment_method)){					
					$cards = $this->stripe->paymentMethods->retrieve($subscriptions->default_payment_method,[]);
					$last4 = $cards->card->last4;
					$brand = $cards->card->brand;
				}
			} catch (Exception $e) {
				//
			}

		  
		   $this->code = 1;
		   $this->msg = "ok";
		   $this->details = array(
			 'plan_title'=>Yii::app()->input->xssClean($plans->title),	
			 'package_uuid'=>$plans->package_uuid,
			 'subscribe_link'=>$subscribe_link,
			 'cards'=> array(
			   'last4'=>$last4,
			   'brand'=>$brand
			 )			 
		   );				   
		   
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		 
		   $this->details = array(
		     'subscribe_link'=>$subscribe_link,
		   );
		}		
		$this->responseJson();
	}
	
	public function actionchangePlan()
	{
		try {

		   $payment_code = StripeModule::paymentCode();
		   $merchant_id = Yii::app()->merchant->merchant_id;  
		   $package_uuid = isset($this->data['package_uuid'])?$this->data['package_uuid']:'';
		   
		   $merchant = CMerchants::get($merchant_id);
		   $plans = Cplans::getByUUID($package_uuid);
		   $meta_name = "plan_price_$payment_code";			
		   
		   $price = Cplans::planPriceID($meta_name,$plans->package_id);
		   $price_id = $price->meta_value;
		   		   
		   $credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
	       $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:''; 
	       $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;	
		      
	       $subscription = Cplans::getMechantSubcriptions($merchant_id,$is_live);	       
	       $subcriber_id = $subscription->meta_value2;	    	       
	       	       
	       $subscriptions = $this->stripe->subscriptions->retrieve($subcriber_id);
	       $id = $subscriptions->items->data[0]->id;
	       
	       $end = '';
		   if($plans->trial_period>0){
			    $dateTime = new DateTime(date('c'));
			    $dateTime->modify('+2 minutes');		    
			    $end = $dateTime->format( "Y-m-d H:i");			    
			    $end = strtotime($end);			    
		   }		  
	       		   
		   if($plans->trial_period>0){
		   	  $update_subscription = $this->stripe->subscriptions->update($subcriber_id, [
			    'items' => [
			      [
			        'id' => $id,
			        'price' => $price_id,
			      ],
			    ],
			    'trial_end'=>$end
			   ]);
		   } else {
		       $update_subscription = $this->stripe->subscriptions->update($subcriber_id, [
			    'items' => [
			      [
			        'id' => $id,
			        'price' => $price_id,
			      ],
			    ],
			   ]);
		   }
		   
		   $merchant->package_id = intval($plans->package_id);
		   $subscription->meta_value = intval($plans->package_id);
		   if($merchant->save() && $subscription->save() ){
		   	  $this->code = 1;
		      $this->msg = t("Change plan successful");		   
		   } else $this->msg = CommonUtility::parseError( $model->getErrors());
		   
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   
		}		
		$this->responseJson();
	}
	
	
	public function actioncancelSubscription()
	{
		try {
			
			$payment_code = StripeModule::paymentCode();
		    $merchant_id = Yii::app()->merchant->merchant_id;  
		    $package_uuid = isset($this->data['package_uuid'])?$this->data['package_uuid']:'';
		    
		    //$merchant = CMerchants::get($merchant_id);
		    
		    $credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
	        $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:''; 
	        $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;	
		      
	        $subscription = Cplans::getMechantSubcriptions($merchant_id,$is_live);	       
	        $subcriber_id = $subscription->meta_value2;	    	       
	        
	        
	        $subscriptions = $this->stripe->subscriptions->retrieve($subcriber_id);
	        $subscriptions->delete();
	        	        	        
	        $subscription->scenario = 'cancel_subscription';
	        $subscription->delete();	        
	        
	        $this->code = 1;
	        $this->msg = t("Subscription plan cancelled");
			
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   
		}		
		$this->responseJson();
	}
	
	public function actioncreateIntent()
	{
		try {
					   
		   $merchant_id = Yii::app()->merchant->merchant_id;    
		   $payment_code = StripeModule::paymentCode();
		   $package_uuid = Yii::app()->input->post('package_uuid');
		   
		   $credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
	       $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:''; 
	       $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;	
	       
	       
	       $customer = Cplans::getMerchantCustomerID($merchant_id,$is_live);
	       $customer_id = $customer->meta_value1;	
	       
	       $intents = $this->stripe->setupIntents->create(
			  [
			    'customer' => $customer_id,			    
			  ]
			);
			
		   $this->code = 1;
		   $this->msg = "OK";
		   $this->details = array(
			  'client_secret'=>$intents->client_secret
		   );
		   
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   		   
		}		
		$this->responseJson();
	}
	
	public function actionvalidate_card()
	{
		try {
						
			$setup_intent = Yii::app()->input->get('setup_intent');
			$setup_intent_client_secret = Yii::app()->input->get('setup_intent_client_secret');
			$redirect_status = Yii::app()->input->get('redirect_status');			
			switch ($redirect_status) {
				case "succeeded":	
				     $this->redirect(Yii::app()->createUrl('/plan/manage'));
				     Yii::app()->end();
					break;
			
				case "processing":		
				    $this->msg =  t("Processing payment details. We'll update you when processing is complete.");			
					break;
					
				case "requires_payment_method":					
				    $this->msg =  t("Failed to process payment details. Please try another payment method.");			
					break;
							
				default:
					$this->msg =  t("Undefined error");
					break;
			}
			
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   		   
		}				
		
		$this->redirect( Yii::app()->createUrl('/plan/error',array('message'=> $this->msg )) );
	}

	public function actiongetCards()
	{
		try {
			
		   $merchant_id = Yii::app()->merchant->merchant_id;    
		   $payment_code = StripeModule::paymentCode();
		   
		   $credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
	       $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:''; 
	       $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;	
	       	       
	       $customer = Cplans::getMerchantCustomerID($merchant_id,$is_live);
	       $customer_id = $customer->meta_value1;		       
	       
	       $cards = $this->stripe->paymentMethods->all([
			  'customer' => $customer_id,
			  'type' => 'card',
		   ]);

		   $data = array(); $payment_method = '';
		   $meta = AR_merchant_meta::getValue($merchant_id,'payment_method_stripe');
		   $payment_method = isset($meta['meta_value'])?$meta['meta_value']:'';
		   
		   foreach ($cards->data as $items) {		   		   	  
		   	  $expiry = $items->card->exp_year."-".$items->card->exp_month."-01";
		   	  $data[] = array(
		   	    'payment_method'=>$items->id,
		   	    'brand'=>$items->card->brand,
		   	    'last4'=>$items->card->last4,
		   	    'expiry'=> t("Expires {{expiry}}",array('{{expiry}}'=>Date_Formatter::date($expiry,"MMM yyyy"))),
		   	  );
		   }
		   $this->code = 1; $this->msg = "ok";
		   $this->details = array(
		     'default'=>$payment_method,
		     'cards'=>$data
		   );
			
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   		   
		}		
		$this->responseJson();
	}
	
	public function actionsetCardDefault()
	{
		try {
						
			$payment_method = Yii::app()->input->post('payment_method');	
			
			$merchant_id = Yii::app()->merchant->merchant_id;    
		    $payment_code = StripeModule::paymentCode();
		   
		    $credentials = CPayments::getPaymentCredentials(0,$payment_code,0);			
	        $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:''; 
	        $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;	
	       	        
	        $subscription = Cplans::getMechantSubcriptions($merchant_id,$is_live);	       
	        $subcriber_id = $subscription->meta_value2;	
	        	        
	        $update_invoice = $this->stripe->subscriptions->update($subcriber_id,array(
		       'default_payment_method'=>$payment_method
		    ));
		    
		    AR_merchant_meta::saveMeta($merchant_id,"payment_method_stripe", $payment_method);
		    		    
		    $this->code = 1;
		    $this->msg = "ok";		    
	        
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   
		}		
		$this->responseJson();
	}
	
	public function actiondeleteCard()
	{
		try {
			
			$payment_method = Yii::app()->input->post('payment_method');			
			$this->stripe->paymentMethods->detach($payment_method,[]);			
			$this->code = 1;
		    $this->msg = "ok";
			
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   
		}		
		$this->responseJson();
	}
	
	public function actioninvoiceDetails()
	{
		try {

			$invoice_number = Yii::app()->input->post('invoice_number');		
			$plans = AR_plans_invoice::model()->find("invoice_ref_number=:invoice_ref_number",array(
			  ':invoice_ref_number'=>$invoice_number
			));
			if($plans){
				$invoice = $this->stripe->invoices->retrieve(
				  $plans->payment_ref1,
				  []
				);						
				$line_items = array();
				foreach ($invoice->lines->data as $item) {					
					$line_items[] = array(
					  'amount'=> Price_Formatter::formatNumber(($item->amount/100)) ,
					  'currency'=>$item->currency,
					  'currency'=>$item->currency,
					  'description'=>$item->description,
					  'period_start'=>Date_Formatter::date($item->period->start),
				      'period_end'=>Date_Formatter::date($item->period->end),
					);
				}
				$data = array(
				  'id'=>$invoice->id,
				  'number'=>$invoice->number,
				  'status'=>$invoice->status,
				  'customer_email'=>$invoice->customer_email,
				  'customer_name'=>$invoice->customer_name,
				  'customer_phone'=>$invoice->customer_phone,
				  'customer_address'=>$invoice->customer_address,
				  'currency'=>$invoice->currency,
				  'created'=>Date_Formatter::date($invoice->created),
				  'period_start'=>Date_Formatter::date($invoice->period_start),
				  'period_end'=>Date_Formatter::date($invoice->period_end),
				  
				  'subtotal'=>($invoice->subtotal/100),
				  'subtotal0'=>Price_Formatter::formatNumber(($invoice->subtotal/100)),
				  
				  'total'=>($invoice->total/100),
				  'total0'=> Price_Formatter::formatNumber(($invoice->total/100)),
				  
				  'amount_due'=>($invoice->amount_due/100),
				  'amount_due0'=> Price_Formatter::formatNumber(($invoice->amount_due/100)),
				  
				  'amount_paid'=>($invoice->amount_paid/100),
				  'amount_paid0'=> Price_Formatter::formatNumber(($invoice->amount_paid/100)),
				  
				  'amount_remaining'=>($invoice->amount_remaining/100),
				  'amount_remaining0'=> Price_Formatter::formatNumber(($invoice->amount_remaining/100)),
				  
				  'items'=>$line_items,
				);
				
				$this->code = 1;
				$this->msg = "ok";
				$this->details = $data;
			} else $this->msg = t("Invoice number not found");
					
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   		   
		}		
		$this->responseJson();
	}
	
	public function actionpayInvoice()
	{
		try {
						
			$invoice_id = Yii::app()->input->post('invoice_id');		
			$resp = $this->stripe->invoices->pay(
			  $invoice_id,
			  []
			);
			
			if($resp->status=="paid"){
				$this->code = 1;
				$this->msg = "ok";
				Yii::app()->merchant->setState("status",'active');
			} else $this->msg = t("Payment failed reason");
			
		} catch (Exception $e) {
		   $this->msg = $e->getMessage();		   
		}		
		$this->responseJson();
	}
	
}
/*end class*/