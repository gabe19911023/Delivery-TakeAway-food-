<?php
require 'razorpay/vendor/autoload.php';
use Razorpay\Api\Api;

class RazorpayapiController extends SiteCommon
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
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
								
			$credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
								
			$full_name = Yii::app()->user->first_name." ".Yii::app()->user->last_name;
			
			$api = new Api($credentials['attr1'], $credentials['attr2']);
			
			
			$model = AR_client_meta::model()->find('client_id=:client_id AND meta1=:meta1 AND meta2=:meta2 
			AND meta3=:meta3 ', 
		    array( 
		      ':client_id'=>intval(Yii::app()->user->id),
		      ':meta1'=>$payment_code,
		      ':meta2'=>$is_live,
		      ':meta3'=>isset($credentials['merchant_id'])?$credentials['merchant_id']:'',
		    )); 	
		    
		    $create = false; $customer_id='';
		    if($model){
		    	if(empty($model->meta4)){
		    		$create = true;
		    	} else $customer_id = $model->meta4;
		    } else $create = true;
			
		    if($create){
				try {
					
					$client = AR_client::model()->findbyPk(intval(Yii::app()->user->id));
					
					$customer = $api->customer->create(array(
					   'name' => $full_name,
					   'email' => $client? $client->email_address : Yii::app()->user->email_address,
					   'contact'=>$client? $client->contact_phone : Yii::app()->user->contact_number,
					   'fail_existing'=>0,
					   'notes'=> array(
					          'client_id'=> Yii::app()->user->id
					       )
					));					
					$customer_id = $customer->id;
				} catch (Exception $e) {
					$this->msg[] = $e->getMessage();
					$this->responseJson();						
				}
				
				
				if(!empty($customer_id)){			    
					$model = new AR_client_meta;
			    	$model->client_id = intval(Yii::app()->user->id);
			    	$model->meta1 = $payment_code;
			    	$model->meta2 = $is_live;
			    	$model->meta3 = $credentials['merchant_id'];
			    	$model->meta4 = $customer_id;
			    	$model->save();			
				} 							
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
	
	public function actionCreateOrder()
	{
		try {
					
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;		
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';		
			$merchant_type = isset($this->data['merchant_type'])?$this->data['merchant_type']:'';
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
								
			$credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
			$credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			$is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
			
			$data = AR_ordernew::model()->find('order_uuid=:order_uuid',array(':order_uuid'=>$order_uuid));
			if($data){
				$total = floatval(Price_Formatter::convertToRaw($data->total));
				$amount = ($total*100);
				$merchant = CMerchantListingV1::getMerchant($data->merchant_id);
				
				$payment_description = t("Payment to merchant [merchant]. Order#[order_id]",
		         array('[merchant]'=>$merchant->restaurant_name,'[order_id]'=>$data->order_id ));	
		         		        
		        $api = new Api($credentials['attr1'], $credentials['attr2']);
		        $data->use_currency_code = "INR";
		        
		        $order  = $api->order->create([
		          'receipt'=>$order_uuid,
		          'amount'=>$amount,
		          'currency'=>$data->use_currency_code,
		          'notes'=>array(
		            'order_uuid'=>$order_uuid,	            
		          )
				]);
				
				$model = AR_client_meta::model()->find('client_id=:client_id AND meta1=:meta1 AND meta2=:meta2 
				AND meta3=:meta3 ', 
			    array( 
			      ':client_id'=>intval(Yii::app()->user->id),
			      ':meta1'=>$payment_code,
			      ':meta2'=>$is_live,
			      ':meta3'=>isset($credentials['merchant_id'])?$credentials['merchant_id']:'',
			    )); 				    
				
				$options = array(				  
				  'amount'=>$amount,
				  'currency'=>$data->use_currency_code,
				  'name'=>$merchant->restaurant_name,
				  'description'=>$payment_description,
				  'order_id'=>$order->id,				  
				  'customer_id'=>$model?$model->meta4:''
				);
				
				$this->code = 1;
				$this->msg = "ok";
				$this->details = $options;				
				
			} else $this->msg = t("Order id not found");
               
		 } catch (Exception $e) {
			$this->msg[] = t($e->getMessage());							
		}			
		$this->responseJson();	
	}
	
	public function actionverifypayment()
	{
		try {
					  
		   $merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;		
		   $payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';		
		   $merchant_type = isset($this->data['merchant_type'])?$this->data['merchant_type']:'';
		   $cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		   $order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
		   $razorpay_payment_id = isset($this->data['razorpay_payment_id'])?$this->data['razorpay_payment_id']:'';
		   $razorpay_order_id = isset($this->data['razorpay_order_id'])?$this->data['razorpay_order_id']:'';
		   $razorpay_signature = isset($this->data['razorpay_signature'])?$this->data['razorpay_signature']:'';
		   
		   $data = AR_ordernew::model()->find('order_uuid=:order_uuid',array(':order_uuid'=>$order_uuid));
		   if($data){
		   	
		   	   $credentials = CPayments::getPaymentCredentials($merchant_id,$payment_code,$merchant_type);
			   $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			   $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;
			   
			   $api = new Api($credentials['attr1'], $credentials['attr2']);			   			   			 
			   $attributes  = array(
			       'razorpay_signature'  => $razorpay_signature,
			       'razorpay_payment_id'  => $razorpay_payment_id,
			       'razorpay_order_id' => $razorpay_order_id
			   );			   
			   $api->utility->verifyPaymentSignature($attributes);
			   
			   
			   /*CAPTURE PAYMENT*/
			   $amount = Price_Formatter::convertToRaw($data->total);			   
			   $capture = $api->payment->fetch($razorpay_payment_id)->capture(array(
			     'amount'=>($amount*100),
			     'currency' => Price_Formatter::$number_format['currency_code']
			   ));
			   
			   
			   $transaction_id = $razorpay_payment_id;
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
			   if($model->save()){
			   	  /*INSERT NOTES FOR PAYMENT*/
					$params = array(  
					   array('transaction_id'=>$model->transaction_id,'order_id'=>$data->order_id, 
					   'meta_name'=>'razorpay_payment_id', 'meta_value'=>$razorpay_payment_id ),
					   					   
					   array('transaction_id'=>$model->transaction_id,'order_id'=>$data->order_id, 
					   'meta_name'=>'razorpay_order_id', 'meta_value'=>$razorpay_order_id ),
					   
					   array('transaction_id'=>$model->transaction_id,'order_id'=>$data->order_id, 
					   'meta_name'=>'razorpay_signature', 'meta_value'=>$razorpay_signature ),
					    
					);
					$builder=Yii::app()->db->schema->commandBuilder;
				    $command=$builder->createMultipleInsertCommand('{{ordernew_trans_meta}}',$params);
				    $command->execute();
			   }
			   
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
			$this->msg[] = t($e->getMessage());							
		}			
		$this->responseJson();	
	}
	
}
/*end index*/