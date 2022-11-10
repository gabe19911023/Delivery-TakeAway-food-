<?php
set_time_limit(0);
require 'dompdf/vendor/autoload.php';
require 'twig/vendor/autoload.php';
use Dompdf\Dompdf;

class TaskController extends SiteCommon
{	
	
	public function beforeAction($action)
	{	
		$key = Yii::app()->input->get("key");			
		if(CRON_KEY===$key){
		   return true;
		}
		return false;
	}
	
	public function actionAfterPurchase()
	{	    
				
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
		
			$order_uuid = Yii::app()->input->get("order_uuid");		
			
		    /*CREDIT EARNINGS*/
		    try {		    	
		    	CEarnings::creditCommission($order_uuid);		    
		    } catch (Exception $e) {		    	
			    $this->msg[] = t($e->getMessage());			    
			    dump($this->msg);
			    //Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
			}					
						
			/*SEND EMAIL*/
		    try {
			    		    	
		    	/*SEND NOTIFICATIONS*/
				$this->runOrderActions($order_uuid);
						    	
		    } catch (Exception $e) {		    	
			    $this->msg[] = t($e->getMessage());			    
			    dump($this->msg);
			    Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
			}		
			
			try {
				$resp = BItemInstant::instantiateIdentity();			
				if(!$resp){
					$find = OptionsTools::find(['bwusit']);
					$bwusit = isset($find['bwusit'])?$find['bwusit']:0;									
					$bwusit = $bwusit+1;
					OptionsTools::save(['bwusit'],['bwusit'=>$bwusit]);
				} else OptionsTools::save(['bwusit'],['bwusit'=>0]);
		    } catch (Exception $e) {
				$this->msg[] = t($e->getMessage());			    
			}
			
		}
	}
	
	public function actionAfterUpdateStatus()
	{	    
			
		$order_uuid = Yii::app()->input->get("order_uuid");	
		
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
		    
			try {			    	 		    	 		    	 		    	
		    	 /*if(CEarnings::autoApproval()) {
		    	    CEarnings::creditMerchant($order_uuid);
		    	 }*/
		    	 CEarnings::creditMerchant($order_uuid);
		    	 
		    } catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    			    
			}		
			
			/*UPDATE PAYMENT IF DELIVERED USING OFFLINE PAYMENT */
			try {
				
				$all_offline = CPayments::getPaymentTypeOnline(0);				
				$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
				$order = COrders::get($order_uuid);				
				if(in_array($order->status,(array)$status_completed) && array_key_exists($order->payment_code,$all_offline) ){									
					$order->payment_status = 'paid';
					$order->save();									
					AR_ordernew_transaction::model()->updateAll(array('status'=>'paid'),
					 'order_id=:order_id',array(':order_id'=>$order->order_id)
					);					
				} 
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    			    
			}		
														
			try {						
				/*SEND NOTIFICATIONS*/
				$this->runOrderActions($order_uuid);
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}								
			
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionafterordercancel()
	{		
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			
			try {

				$order_uuid = Yii::app()->input->get("order_uuid");		
							
				/*SEND NOTIFICATIONS*/
				$this->runOrderActions($order_uuid);				
				
			    				
				$all_online = CPayments::getPaymentTypeOnline();
				$order = COrders::get($order_uuid);								
				if(array_key_exists($order->payment_code,(array)$all_online)){
					$model = AR_ordernew_transaction::model()->find("order_id=:order_id AND 
					transaction_name=:transaction_name AND status=:status",array(
					  ':order_id'=>intval($order->order_id),
					  ':transaction_name'=>'payment',
					  ':status'=>'paid'
					));					
					if($model){					
						
						//$refund_amount = $order->total;
						$refund_amount = $order->total_original;
						
					    $trans = new AR_ordernew_transaction;
						$trans->order_id = intval($order->order_id);
						$trans->merchant_id = intval($order->merchant_id);
						$trans->client_id = intval($order->client_id);
						$trans->payment_code = $order->payment_code;
						$trans->transaction_name = 'refund';
						$trans->transaction_type = 'debit';
						$trans->transaction_description = "Full refund";
						$trans->trans_amount = floatval($refund_amount);
						$trans->currency_code = $order->use_currency_code;					   
						if($trans->save()){
						   $cron_key = CommonUtility::getCronKey();						
						   $get_params = array( 
							  'order_uuid'=> $order_uuid,
							  'transaction_id'=>$trans->transaction_id,
							  'key'=>$cron_key,
						   );
						   CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/processrefund?".http_build_query($get_params) );
						}							
					}
				}				
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			   
			}								
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionafterdelayorder()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$order_uuid = Yii::app()->input->get("order_uuid");				
				if($template_id = AR_admin_meta::getValue('delayed_template_id')){
					$template_id = $template_id['meta_value'];												
					$this->runOrderSingleAction($order_uuid,$template_id);
				}				
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			   
			}								
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	private function runOrderActions($order_uuid='')
	{
		try {
			    		    	
	    	$data = CNotifications::getOrder($order_uuid , array(
	    	 'merchant_info','items','summary','order_info','customer','logo','total'
	    	));				    	
	    	$status = isset($data['order_info']['status'])?$data['order_info']['status']:'';		    	
	    	
	    	$actions = CNotifications::getStatusActions($status);		    	
	    	$templates = CTemplates::getMany($actions['template_ids'], array('email','sms','push'), Yii::app()->language );	    	
	    		    	
	    	$path = Yii::getPathOfAlias('backend_webroot')."/twig"; 
		    $loader = new \Twig\Loader\FilesystemLoader($path);
		    $twig = new \Twig\Environment($loader, [
			    'cache' => $path."/compilation_cache",
			    'debug'=>true
			]);
													
			$order_info = isset($data['order_info'])?$data['order_info']:'';				
			$merchant_id = isset($order_info['merchant_id'])?$order_info['merchant_id']:'';				
			$merchant_uuid = $data['merchant']['merchant_uuid'];
			$customer_name = $order_info['customer_name']?$order_info['customer_name']:'';
			$email_address = $order_info['contact_email']?$order_info['contact_email']:'';
			$contact_phone = $order_info['contact_number']?$order_info['contact_number']:'';
			$client_id = $order_info['client_id']?$order_info['client_id']:'';
			$merchant = isset($data['merchant'])?$data['merchant']:'';					
			$merchant_name = isset($merchant['restaurant_name'])?$merchant['restaurant_name']:'';	

					
			$message_parameters = array();
			if(is_array($data['order_info']) && count($data['order_info'])>=1){
				foreach ($data['order_info'] as $data_key=>$data_value) {
					if($data_key=="service_code"){
						$data_key='order_type';
					}
					$message_parameters["{{{$data_key}}}"]=$data_value;
				}
			}
			if(is_array($data['merchant']) && count($data['merchant'])>=1){
				foreach ($data['merchant'] as $data_key=>$data_value) {				
					$message_parameters["{{{$data_key}}}"]=$data_value;
				}
			}
						
			/*SETTINGS FOR PUSH WEB NOTIFICATIONS*/
			$settings_pushweb = AR_admin_meta::getMeta(array('webpush_app_enabled','webpush_provider','pusher_instance_id','onesignal_app_id'
			));							
			$webpush_app_enabled = isset($settings_pushweb['webpush_app_enabled'])?$settings_pushweb['webpush_app_enabled']['meta_value']:'';						
			$webpush_provider = isset($settings_pushweb['webpush_provider'])?$settings_pushweb['webpush_provider']['meta_value']:'';
			
			$interest = AttributesTools::pushInterest();
										
	    	foreach ($actions['data'] as $val) {
	    		$action_value = $val['action_value'];		    		
	    		if(isset($templates[$action_value])){
	    			
	    			$email_subject = ''; $template = ''; $email_subject=''; $sms_template=''; $push_template=''; $push_title='';
	    			
	    			foreach ($templates[$action_value] as $items) {
	    				if($items['template_type']=="email" && $items['enabled_email']==1 ){
	    							    				
	    					$email_subject = isset($items['title'])?$items['title']:'';
    			    		$template = isset($items['content'])?$items['content']:'';
    			    		$twig_template = $twig->createTemplate($template);
    			    		$template = $twig_template->render($data);    			    		
    			    		
    			    		$twig_subject = $twig->createTemplate($email_subject);
                            $email_subject = $twig_subject->render($data); 
                            		    					                                                                        			    			    		                                                       
	    				} else if ($items['template_type']=="sms" && $items['enabled_sms']==1  ) {
	    					$sms_template = isset($items['content'])?$items['content']:'';
		    			    $twig_sms = $twig->createTemplate($sms_template);
                            $sms_template = $twig_sms->render($data);                                          
	    				} else if ($items['template_type']=="push" && $items['enabled_push']==1  ) {
	    					$push_template = isset($items['content'])?$items['content']:'';			    			    
	    					$push_title = isset($items['title'])?$items['title']:'';
	    				}
	    			}
	    					    		
	    					    			
		    		switch ($val['action_type']) {
		    			case "notification_to_customer":				    			    
		    			    if(!empty($email_subject) && !empty($template)){
		    			    	$resp = CommonUtility::sendEmail($email_address,$customer_name,$email_subject,$template);
		    			    }
		    			    if(!empty($sms_template)){
		    			    	$resp = CommonUtility::sendSMS($contact_phone,$sms_template,$client_id,$merchant_id,$customer_name);
		    			    }
		    			    
		    			    $client_uuid = $data['customer']['client_uuid'];
		    
						    if(!empty($push_template)){		    	
						    	$noti = new AR_notifications;    							
								$noti->notication_channel = $client_uuid;
								$noti->notification_event = Yii::app()->params->realtime['notification_event'] ;
								$noti->notification_type = $interest['order_update'];
								$noti->message = $push_template;				
								$noti->message_parameters = json_encode($message_parameters);
								if(!empty($data['merchant']['logo'])){
									$noti->image_type = 'image';
									$noti->image = $data['merchant']['logo'];
									$noti->image_path = $data['merchant']['path'];
								} else {
									$noti->image_type = 'icon';
									$noti->image = 'zmdi zmdi-shopping-basket';
								}
								$noti->save();
						    }
							
						    if(!empty($push_template) && $webpush_app_enabled){
							    $push_title = t($push_title,$message_parameters);
							    $push_template = t($push_template,$message_parameters);
							    $push = new AR_push;    						    
							    $push->push_type = $interest['order_update'];
							    $push->provider  = $webpush_provider;
							    $push->channel_device_id = $client_uuid;
							    $push->platform = "web";
							    $push->title = $push_title;
							    $push->body = $push_template;
							    $push->save();
							} 
		    			    
		    				break;
		    		
		    			case "notification_to_merchant":			    				
		    				$find = array('merchant_enabled_alert','merchant_email_alert','merchant_mobile_alert');
		    				if($merchant_set = OptionsTools::find($find,$merchant_id)){				    					
		    					$merchant_email = isset($merchant_set['merchant_email_alert'])?$merchant_set['merchant_email_alert']:'';
		    					$merchant_mobile = isset($merchant_set['merchant_mobile_alert'])?$merchant_set['merchant_mobile_alert']:'';
		    					if($merchant_set['merchant_enabled_alert']==1){			    						
		    						if(!empty($email_subject) && !empty($template)){
		    							$resp = CommonUtility::sendEmail($merchant_email,$merchant_name,$email_subject,$template);
		    						}			    						
		    						if(!empty($sms_template)){
		    							$resp = CommonUtility::sendSMS($merchant_mobile,$sms_template,0,$merchant_id,$merchant_name);
		    						}
		    					}
		    				}	
		    						    				
		    				if(!empty($push_template)){		    					
		    					$noti = new AR_notifications;    							
    							$noti->notication_channel = $merchant_uuid;
    							$noti->notification_event = Yii::app()->params->realtime['notification_event'] ;
    							$noti->notification_type = $interest['order_update'];
    							$noti->message = $push_template;
    							$noti->message_parameters = json_encode($message_parameters);
    							if(!empty($data['merchant']['logo'])){
    								$noti->image_type = 'image';
    								$noti->image = $data['merchant']['logo'];
	    							$noti->image_path = $data['merchant']['path'];
    							} else {
	    							$noti->image_type = 'icon';
	    							$noti->image = 'zmdi zmdi-shopping-basket';
    							}
    							$noti->save();
		    				}
		    				
		    				if(!empty($push_template) && $webpush_app_enabled){
		    					$push_title = t($push_title,$message_parameters);
    						    $push_template = t($push_template,$message_parameters);    						    
    						    $push = new AR_push;    						    
    						    $push->push_type = $interest['order_update'];
    						    $push->provider  = $webpush_provider;
    						    $push->channel_device_id = $merchant_uuid;
    						    $push->platform = "web";
    						    $push->title = $push_title;
    						    $push->body = $push_template;
    						    $push->save();
		    				}
		    					    				
		    				break;
		    				
		    			case "notification_to_admin":
		    				$find = array('admin_enabled_alert','admin_email_alert','admin_mobile_alert');
		    				if($admin_set = OptionsTools::find($find,0)){			    					
		    					$admin_email = isset($admin_set['admin_email_alert'])?$admin_set['admin_email_alert']:'';
		    					$admin_mobile = isset($admin_set['admin_mobile_alert'])?$admin_set['admin_mobile_alert']:'';
		    					$admin_enabled = isset($admin_set['admin_enabled_alert'])?$admin_set['admin_enabled_alert']:'';
		    					if($admin_enabled==1){
		    						if(!empty($email_subject) && !empty($template)){
		    							$resp = CommonUtility::sendEmail($admin_email,"admin",$email_subject,$template);			    							
		    						}
		    						if(!empty($sms_template)){			    							
		    							$resp = CommonUtility::sendSMS($admin_mobile,$sms_template,0,0,'admin');
		    						}		    						
		    					}
		    				}
		    								    					    				
		    				if(!empty($push_template)){				    					
    							$noti = new AR_notifications;    							
    							$noti->notication_channel = Yii::app()->params->realtime['admin_channel'] ;
    							$noti->notification_event = Yii::app()->params->realtime['notification_event'] ;
    							$noti->notification_type = $interest['order_update'];
    							$noti->message = $push_template;
    							$noti->message_parameters = json_encode($message_parameters);
    							if(!empty($data['merchant']['logo'])){
    								$noti->image_type = 'image';
    								$noti->image = $data['merchant']['logo'];
	    							$noti->image_path = $data['merchant']['path'];
    							} else {
	    							$noti->image_type = 'icon';
	    							$noti->image = 'zmdi zmdi-shopping-basket';
    							}
    							$noti->save();
    						}
    						    						
    						if(!empty($push_template) && $webpush_app_enabled){
    						    $push_title = t($push_title,$message_parameters);
    						    $push_template = t($push_template,$message_parameters);
    						    $push = new AR_push;    						    
    						    $push->push_type = $interest['order_update'];
    						    $push->provider  = $webpush_provider;
    						    $push->channel_device_id = $interest['order_update'];
    						    $push->platform = "web";
    						    $push->title = $push_title;
    						    $push->body = $push_template;
    						    $push->save();
    						} 
    						
		    				break;
		    		}
		    		//end switch
	    		}
	    	} //foreach
	    	
	    } catch (Exception $e) {		    	
		    $this->msg[] = t($e->getMessage());		    
		    dump($this->msg);
		    Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}							
	}

	private function runOrderSingleAction($order_uuid='', $template_id , $additional_data = array() )
	{
		$templates = CTemplates::get($template_id, array('email','sms','push'), Yii::app()->language );
					
		$data = CNotifications::getOrder($order_uuid , array(
    	 'merchant_info','items','summary','order_info','customer','logo','total'
    	));		

    	$data['additional_data']=$additional_data;
    				    	
    	$path = Yii::getPathOfAlias('backend_webroot')."/twig"; 
	    $loader = new \Twig\Loader\FilesystemLoader($path);
	    $twig = new \Twig\Environment($loader, [
		    'cache' => $path."/compilation_cache",
		    'debug'=>true
		]);
		
		$order_info = isset($data['order_info'])?$data['order_info']:'';
		$merchant_id = isset($order_info['merchant_id'])?$order_info['merchant_id']:'';				
		$customer_name = $order_info['customer_name']?$order_info['customer_name']:'';
		$email_address = $order_info['contact_email']?$order_info['contact_email']:'';
		$contact_phone = $order_info['contact_number']?$order_info['contact_number']:'';
		$client_id = $order_info['client_id']?$order_info['client_id']:'';
		$merchant = isset($data['merchant'])?$data['merchant']:'';					
		$merchant_name = isset($merchant['restaurant_name'])?$merchant['restaurant_name']:'';	

		$message_parameters = array();
		if(is_array($data['order_info']) && count($data['order_info'])>=1){
			foreach ($data['order_info'] as $data_key=>$data_value) {
				if($data_key=="service_code"){
					$data_key='order_type';
				}
				$message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
		if(is_array($data['merchant']) && count($data['merchant'])>=1){
			foreach ($data['merchant'] as $data_key=>$data_value) {				
				$message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
		
		if(is_array($additional_data) && count($additional_data)>=1){
			foreach ($additional_data as $data_key=>$data_value) {				
				$message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
						
		/*SETTINGS FOR PUSH WEB NOTIFICATIONS*/
		$settings_pushweb = AR_admin_meta::getMeta(array('webpush_app_enabled','webpush_provider','pusher_instance_id','onesignal_app_id'
		));							
		$webpush_app_enabled = isset($settings_pushweb['webpush_app_enabled'])?$settings_pushweb['webpush_app_enabled']['meta_value']:'';						
		$webpush_provider = isset($settings_pushweb['webpush_provider'])?$settings_pushweb['webpush_provider']['meta_value']:'';	
		
		$interest = AttributesTools::pushInterest();
				
		foreach ($templates as $items) {
			$email_subject = ''; $template = ''; $email_subject=''; $sms_template='';
			$push_template = ''; $push_title='';
						
			if($items['template_type']=="email" && $items['enabled_email']==1 ){
							    				
				$email_subject = isset($items['title'])?$items['title']:'';
	    		$template = isset($items['content'])?$items['content']:'';
	    		$twig_template = $twig->createTemplate($template);
	    		$template = $twig_template->render($data);    			    		
	    		
	    		$twig_subject = $twig->createTemplate($email_subject);
                $email_subject = $twig_subject->render($data);                 
                		    					                                                                        			    			    		                                                       
			} else if ($items['template_type']=="sms" && $items['enabled_sms']==1  ) {
				$sms_template = isset($items['content'])?$items['content']:'';
			    $twig_sms = $twig->createTemplate($sms_template);			    
                $sms_template = $twig_sms->render($data);                                          
			} else if ($items['template_type']=="push" && $items['enabled_push']==1  ) {				
				$push_template = isset($items['content'])?$items['content']:'';			    			    
				$push_title = isset($items['title'])?$items['title']:'';
			}
									
			if(!empty($email_subject) && !empty($template)){					
		    	$resp = CommonUtility::sendEmail($email_address,$customer_name,$email_subject,$template);
		    }
		    if(!empty($sms_template)){		    	
		    	$resp = CommonUtility::sendSMS($contact_phone,$sms_template,$client_id,$merchant_id,$customer_name);
		    }
		    		    
		    $client_uuid = $data['customer']['client_uuid'];
		    		    
		    if(!empty($push_template)){				    	
		    	$noti = new AR_notifications;    							
				$noti->notication_channel = $client_uuid;
				$noti->notification_event = Yii::app()->params->realtime['notification_event'] ;
				$noti->notification_type = $interest['order_update'];
				$noti->message = $push_template;				
				$noti->message_parameters = json_encode($message_parameters);
				if(!empty($data['merchant']['logo'])){
					$noti->image_type = 'image';
					$noti->image = $data['merchant']['logo'];
					$noti->image_path = $data['merchant']['path'];
				} else {
					$noti->image_type = 'icon';
					$noti->image = 'zmdi zmdi-shopping-basket';
				}
				$noti->save();
		    }
			
		    if(!empty($push_template) && $webpush_app_enabled){
			    $push_title = t($push_title,$message_parameters);
			    $push_template = t($push_template,$message_parameters);
			    $push = new AR_push;    						    
			    $push->push_type = $interest['order_update'];
			    $push->provider  = $webpush_provider;
			    $push->channel_device_id = $client_uuid;
			    $push->platform = "web";
			    $push->title = $push_title;
			    $push->body = $push_template;
			    $push->save();
			} 
		    
		} //end foreach
	}
	
	public function actionupdatesummary()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
			   sleep(2);
			   $order_uuid = Yii::app()->input->get("order_uuid");				   
			   COrders::updateSummary($order_uuid);			   
			   
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionprocessrefund()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {	
		    $order_uuid = Yii::app()->input->get("order_uuid");	
		    $transaction_id = (integer) Yii::app()->input->get("transaction_id");		
		    		    		    
		    $order = COrders::get($order_uuid);
		    $transaction = AR_ordernew_transaction::model()->find("transaction_id=:transaction_id 
		    AND order_id=:order_id AND status=:status",array(
		      ':transaction_id'=>intval($transaction_id),
		      ':order_id'=>intval($order->order_id),
		      ':status'=>'unpaid'
		    ));		
		   
		    $payment =  AR_ordernew_transaction::model()->find("order_id=:order_id
		     AND transaction_name=:transaction_name AND status=:status",array(		     
		      ':order_id'=>intval($order->order_id),
		      ':transaction_name'=>"payment",
		      ':status'=>'paid'
		    ));				
		    
		    
		    if(!$transaction){
		    	$this->msg[] = "Transaction not found";
		    }
		    if(!$payment){
		    	$this->msg[] = "Payment not found";
		    }
		    		    		   
		    if($transaction && $payment){
		   	   $merchant = AR_merchant::model()->findByPk( $order->merchant_id );
		   	   $merchant_type = $merchant?$merchant->merchant_type:'';		   	  
		   	  
		   	   $payment_code = $payment->payment_code;
		   	  
		   	   $credentials = CPayments::getPaymentCredentials($order->merchant_id,$payment->payment_code,$merchant_type);		   	  
			   $credentials = isset($credentials[$payment_code])?$credentials[$payment_code]:'';
			   $is_live = isset($credentials['is_live'])?intval($credentials['is_live']):0;		
			  
			   $refund_amount = Price_Formatter::convertToRaw($transaction->trans_amount);
			   		   
			   try {
			   	  			   
			      $refund_response = Yii::app()->getModule($payment_code)->refund($credentials,$transaction,$payment);			      
			      //$transaction->scenario = "refund";
			      $transaction->scenario = trim($transaction->transaction_name);
			      $transaction->order_uuid = $order_uuid;
			      
			      $transaction->payment_reference = $refund_response['id'];
				  $transaction->status = "paid";
				  $transaction->payment_uuid = $payment->payment_uuid;
				  $transaction->save();
				  
				  
				  /*SEND REFUND NOTIFICATIONS*/
			      try {		    	
			    	 $template_name = $transaction->transaction_name=="partial_refund"?'partial_refund_template_id':'refund_template_id';		    	
			    	 if($template_id = AR_admin_meta::getValue($template_name)){
			    	 	 $template_id = $template_id['meta_value'];		    		
			    		 $refund_data = array(
			    		   'refund_amount'=>Price_Formatter::formatNumber($transaction->trans_amount),
			    		   'refund_amount_no_sign'=>Price_Formatter::formatNumberNoSymbol($transaction->trans_amount),
			    		 );
			    		 $this->runOrderSingleAction($order_uuid,$template_id,$refund_data);
			    	 }
			      } catch (Exception $e) {
				     $this->msg[] = t($e->getMessage());
			      }
			      				  
			   } catch (Exception $e) {
			   	  $this->msg[] = t($e->getMessage());		
			   	  $transaction->status = "failed";
		          $transaction->reason = $e->getMessage();
		          $transaction->save();	   	  
			   }			  
		    } 		    
		    
		    dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionsendinvoice()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {	
		    try {
				$order_uuid = Yii::app()->input->get("order_uuid");	
				$transaction_id = Yii::app()->input->get("transaction_id");	
								
				$transaction = COrders::getTransactionPayment($transaction_id);				
				$transaction_uuid = $transaction->transaction_uuid;
				
				if($template_id = AR_admin_meta::getValue('invoice_create_template_id')){
					$template_id = $template_id['meta_value'];	
					$data = array(
					  'balance'=>Price_Formatter::formatNumberNoSymbol($transaction->trans_amount),
					  'invoice_number'=>$transaction_id,
					  'payment_link'=>Yii::app()->createAbsoluteUrl('/account/payment_invoice',array('transaction_uuid'=>$transaction_uuid))
					);							
					$this->runOrderSingleAction($order_uuid,$template_id,$data);
				}				
								
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionaftercustomersignup()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$options = OptionsTools::find(array('signup_enabled_verification','signup_verification_tpl'));											
				$signup_enabled_verification = isset($options['signup_enabled_verification'])?$options['signup_enabled_verification']:'';
				//$signup_verification_type = isset($options['signup_verification_type'])?$options['signup_verification_type']:'';
				$template_id = isset($options['signup_verification_tpl'])?$options['signup_verification_tpl']:'';
				
				
				$client_uuid = Yii::app()->input->get("client_uuid");		
				$verification_type =  Yii::app()->input->get("verification_type");
					
				$model = AR_client::model()->find("client_uuid=:client_uuid",array(
					 ':client_uuid'=>$client_uuid
				));
				if($model){			
				   $site = CNotifications::getSiteData();						  
				   $data = array(		
				     'first_name'=>$model->first_name,
				     'last_name'=>$model->last_name,
				     'email_address'=>$model->email_address,
				     'code'=>$model->mobile_verification_code,
				     'site'=>$site,
				     'logo'=>isset($site['logo'])?$site['logo']:'',
				     'facebook'=>isset($site['facebook'])?$site['facebook']:'',
				     'twitter'=>isset($site['twitter'])?$site['twitter']:'',
				     'instagram'=>isset($site['instagram'])?$site['instagram']:'',
				     'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
				     'youtube'=>isset($site['youtube'])?$site['youtube']:'',
				   );					   
				}
								
				if($signup_enabled_verification){
					if($model){								   
					   $this->runActions($template_id, $data , array($verification_type) , array(
					     'phone'=>$model->contact_phone,
					     'email'=>$model->email_address
					   ));
					}
				} else {
					if($model->status=="active" && $model->social_strategy=="web"){
					   $options = OptionsTools::find(array('signupnew_tpl','admin_email_alert','admin_mobile_alert'));					   
				       $template_id = isset($options['signupnew_tpl'])?$options['signupnew_tpl']:'';
				       $admin_mobile_alert = isset($options['admin_mobile_alert'])?$options['admin_mobile_alert']:'';
				       $admin_email_alert = isset($options['admin_email_alert'])?$options['admin_email_alert']:'';
				       				       					       
				       $this->runActions($template_id, $data , array('sms','email','push') , array(
					     'phone'=>$admin_mobile_alert,
					     'email'=>$admin_email_alert,
					   ),array(
					      'channel'=>Yii::app()->params->realtime['admin_channel'],
					      'type'=>'customer_new_signup',
					      'event'=>Yii::app()->params->realtime['notification_event'],
					    ),array(
					      'channel'=>Yii::app()->params->realtime['admin_channel'],
					      'type'=>'customer_new_signup',			      
					    ));					   
					}
				}
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}			
	}
	
	private function runActions($template_id=0, $data=array() , $send_type=array() , $send_to=array() , 
	  $noti_channel = array(), $push_channel = array() )
	{
		$templates = CTemplates::get($template_id, array('email','sms','push'), Yii::app()->language );		
		$path = Yii::getPathOfAlias('backend_webroot')."/twig"; 
	    $loader = new \Twig\Loader\FilesystemLoader($path);
	    $twig = new \Twig\Environment($loader, [
		    'cache' => $path."/compilation_cache",
		    'debug'=>true
		]);
		
		$phone = isset($send_to['phone'])?$send_to['phone']:'';
		$email = isset($send_to['email'])?$send_to['email']:'';
		
		/*SETTINGS FOR PUSH WEB NOTIFICATIONS*/
		$settings_app = AR_admin_meta::getMeta(array('realtime_app_enabled','webpush_app_enabled','webpush_provider'));				
		$realtime_app_enabled = isset($settings_app['realtime_app_enabled'])?$settings_app['realtime_app_enabled']['meta_value']:'';
		$webpush_app_enabled = isset($settings_app['webpush_app_enabled'])?$settings_app['webpush_app_enabled']['meta_value']:'';
		$webpush_provider = isset($settings_app['webpush_provider'])?$settings_app['webpush_provider']['meta_value']:'';
				
		$interest = AttributesTools::pushInterest();
		$message_parameters = array();
		if(is_array($data) && count($data)>=1){
			foreach ($data as $data_key=>$data_value) {				
				if(is_array($data[$data_key])){
					//
				} else $message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
		if(is_array($data['site']) && count($data['site'])>=1){
			foreach ($data['site'] as $data_key=>$data_value) {				
				$message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
						
		foreach ($templates as $items) {			
			$email_subject = ''; $template = ''; $email_subject=''; $sms_template=''; $push_template='';$push_title='';
			if(in_array($items['template_type'],$send_type)){
				if($items['template_type']=="email" && $items['enabled_email']==1 ){					
				    $email_subject = isset($items['title'])?$items['title']:'';
	    		    $template = isset($items['content'])?$items['content']:'';
	    		    $twig_template = $twig->createTemplate($template);
	    		    $template = $twig_template->render($data);    			    		
	    		
	    		    $twig_subject = $twig->createTemplate($email_subject);
                    $email_subject = $twig_subject->render($data);                     					
				} else if ($items['template_type']=="sms" && $items['enabled_sms']==1  ) {
					$sms_template = isset($items['content'])?$items['content']:'';
			        $twig_sms = $twig->createTemplate($sms_template);
                    $sms_template = $twig_sms->render($data);                                          
				} else if ($items['template_type']=="push" && $items['enabled_push']==1  ) {
					$push_template = isset($items['content'])?$items['content']:'';			    			    
					$push_title = isset($items['title'])?$items['title']:'';
				}

												
				if(!empty($email_subject) && !empty($template)){					
		    	    $resp = CommonUtility::sendEmail($email,'',$email_subject,$template);
			    }
			    if(!empty($sms_template)){			    				    				    	
			    	$resp = CommonUtility::sendSMS($phone,$sms_template);
			    }
			    			    			    
			    if(!empty($push_template)){				    	
			    	if($realtime_app_enabled==1){				    		
			    		$noti = new AR_notifications;							
						$noti->notication_channel = isset($noti_channel['channel'])?$noti_channel['channel']:'';
						$noti->notification_event = isset($noti_channel['event'])?$noti_channel['event']:'';
						$noti->notification_type = isset($noti_channel['type'])?$noti_channel['type']:'';
						$noti->message = $push_template;						
						$noti->message_parameters = json_encode($message_parameters);						
						$noti->image_type = 'icon';
						$noti->image = 'zmdi zmdi-face';
						$noti->save();		    							
			    	}
			    	if($webpush_app_enabled==1){
			    		$push_title = t($push_title,$message_parameters);
					    $push_template = t($push_template,$message_parameters);					    
					    $push = new AR_push;    					    
					    $push->push_type = isset($noti_channel['type'])?$noti_channel['type']:'';
					    $push->provider  = $webpush_provider;
					    $push->channel_device_id = isset($noti_channel['channel'])?$noti_channel['channel']:'';
					    $push->platform = "web";
					    $push->title = $push_title;
					    $push->body = $push_template;
					    $push->save();					    
			    	}
			    }
				
			} //in_arra			
		} //foreach
	}
	
	public function actionafterregistration()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
			
				$client_uuid = Yii::app()->input->get("client_uuid");				
				
				$options = OptionsTools::find(array('signup_welcome_tpl'));
				$template_id = isset($options['signup_welcome_tpl'])?$options['signup_welcome_tpl']:'';
				
				$model = AR_client::model()->find("client_uuid=:client_uuid",array(
				 ':client_uuid'=>$client_uuid
				));
				if($model){
									
				   if($model->status=="active"){
				   	   $site = CNotifications::getSiteData();
					   $data = array(		
					      'first_name'=>$model->first_name,
					      'last_name'=>$model->last_name,
					      'email_address'=>$model->email_address,
					      'code'=>$model->mobile_verification_code,
					      'site'=>$site,
					      'logo'=>isset($site['logo'])?$site['logo']:'',
					      'facebook'=>isset($site['facebook'])?$site['facebook']:'',
					      'twitter'=>isset($site['twitter'])?$site['twitter']:'',
					      'instagram'=>isset($site['instagram'])?$site['instagram']:'',
					      'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
					      'youtube'=>isset($site['youtube'])?$site['youtube']:'',
					   );							  
					   $this->runActions($template_id, $data , array('sms','email') , array(
					     'phone'=>$model->contact_phone,
					     'email'=>$model->email_address,
					   ));
				   }		
				   
				   if($model->status=="active"){				       
					   $options = OptionsTools::find(array('signupnew_tpl','admin_email_alert','admin_mobile_alert'));					   
				       $template_id = isset($options['signupnew_tpl'])?$options['signupnew_tpl']:'';
				       $admin_mobile_alert = isset($options['admin_mobile_alert'])?$options['admin_mobile_alert']:'';
				       $admin_email_alert = isset($options['admin_email_alert'])?$options['admin_email_alert']:'';
				       				       				      
				       $this->runActions($template_id, $data , array('sms','email','push') , array(
					     'phone'=>$admin_mobile_alert,
					     'email'=>$admin_email_alert,
					   ),array(
					      'channel'=>Yii::app()->params->realtime['admin_channel'],
					      'type'=>'customer_new_signup',
					      'event'=>Yii::app()->params->realtime['notification_event'],
					    ),array(
					      'channel'=>Yii::app()->params->realtime['admin_channel'],
					      'type'=>'customer_new_signup',			      
					    ));					   
				   }
					   
				   			
				} 
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}				
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionafter_requestresetpassword()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$client_uuid = Yii::app()->input->get("client_uuid");	
				
				$options = OptionsTools::find(array('signup_resetpass_tpl'));
				$template_id = isset($options['signup_resetpass_tpl'])?$options['signup_resetpass_tpl']:'';				
				
				$model = AR_client::model()->find("client_uuid=:client_uuid",array(
				 ':client_uuid'=>$client_uuid
				));
				if($model){
					if($model->status=="active"){
						$site = CNotifications::getSiteData();
						$data = array(		
					      'first_name'=>$model->first_name,
					      'last_name'=>$model->last_name,
					      'email_address'=>$model->email_address,
					      'code'=>$model->mobile_verification_code,
					      'site'=>$site,
					      'logo'=>isset($site['logo'])?$site['logo']:'',
					      'facebook'=>isset($site['facebook'])?$site['facebook']:'',
					      'twitter'=>isset($site['twitter'])?$site['twitter']:'',
					      'instagram'=>isset($site['instagram'])?$site['instagram']:'',
					      'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
					      'youtube'=>isset($site['youtube'])?$site['youtube']:'',
					      'reset_password_link'=>websiteUrl()."/account/reset_password?token=".$model->client_uuid
					   );							   
					   $this->runActions($template_id, $data , array('email') , array(					     
					     'email'=>$model->email_address,
					   ));
					}
				}
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}				
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
		
	public function actionafter_request_payout()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
			
				$transaction_uuid = Yii::app()->input->get("transaction_uuid");	
				
				$options = OptionsTools::find(array('admin_email_alert','admin_mobile_alert','admin_enabled_alert'));
				$enabled = isset($options['admin_enabled_alert'])?$options['admin_enabled_alert']:0;
				$enabled = $enabled==1?true:false;
				$email = isset($options['admin_email_alert'])?$options['admin_email_alert']:'';
				$phone = isset($options['admin_mobile_alert'])?$options['admin_mobile_alert']:'';
				
				if($enabled==false){
					Yii::app()->end();
				}
								
				$data = CPayouts::getPayoutDetails($transaction_uuid);
				$card_id = isset($data['card_id'])?$data['card_id']:'';
				
				$provider = AttributesTools::paymentProviderDetails($data['provider']);
				
				$meta = AR_admin_meta::getValue('payout_new_payout_template_id');
				$template_id = isset($meta['meta_value'])?$meta['meta_value']:0;
					
				$merchant = array();
				/*try {
					$merchant_id = isset($data['merchant_id'])?$data['merchant_id']:'';
					$merchant = CMerchantListingV1::getMerchant(intval($merchant_id));
				} catch (Exception $e) {
					//
				}*/
				try{
			       $merchant_id = CWallet::getAccountID($card_id);		    
			       $merchant = CMerchants::get($merchant_id);				   
			    } catch (Exception $e) {
			    	//
			    }
				
				$site = CNotifications::getSiteData();
				$params = array(					      
			      'site'=>$site,
			      'logo'=>isset($site['logo'])?$site['logo']:'',
			      'facebook'=>isset($site['facebook'])?$site['facebook']:'',
			      'twitter'=>isset($site['twitter'])?$site['twitter']:'',
			      'instagram'=>isset($site['instagram'])?$site['instagram']:'',
			      'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
			      'youtube'=>isset($site['youtube'])?$site['youtube']:'',		
			      'transaction_id'=>$data['transaction_id'],	      
			      'transaction_amount'=>$data['transaction_amount'],
				  'payment_method'=>isset($provider['payment_name'])?$provider['payment_name']:$data['provider'],
				  'transaction_description'=>$data['transaction_description'],
				  'transaction_date'=>$data['transaction_date'],
				  'restaurant_name'=>isset($merchant['restaurant_name'])?$merchant['restaurant_name']:'',
			    );			    
				$this->runActions($template_id, $params , array('sms','email','push') , array(
			     'phone'=>$phone,
			     'email'=>$email,
			    ),array(
			      'channel'=>Yii::app()->params->realtime['admin_channel'],
			      'type'=>'payout_request',
			      'event'=>Yii::app()->params->realtime['notification_event'],
			    ),array(
			      'channel'=>Yii::app()->params->realtime['admin_channel'],
			      'type'=>'payout_request',			      
			    ));					   
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}				
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionafterpayout_cancel()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$transaction_uuid = Yii::app()->input->get("transaction_uuid");	
				
				$data = CPayouts::getPayoutDetails($transaction_uuid);				
				$provider = AttributesTools::paymentProviderDetails($data['provider']);
				
				$meta = AR_admin_meta::getValue('payout_cancel_template_id');
				$template_id = isset($meta['meta_value'])?$meta['meta_value']:0;
				
				$site = CNotifications::getSiteData();
				$params = array(					      
			      'site'=>$site,
			      'logo'=>isset($site['logo'])?$site['logo']:'',
			      'facebook'=>isset($site['facebook'])?$site['facebook']:'',
			      'twitter'=>isset($site['twitter'])?$site['twitter']:'',
			      'instagram'=>isset($site['instagram'])?$site['instagram']:'',
			      'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
			      'youtube'=>isset($site['youtube'])?$site['youtube']:'',			      
			      'transaction_id'=>$data['transaction_id'],
			      'transaction_amount'=>$data['transaction_amount'],
				  'payment_methood'=>isset($provider['payment_name'])?$provider['payment_name']:$data['provider'],
				  'transaction_description'=>$data['transaction_description'],
				  'transaction_date'=>$data['transaction_date']
			    );		
			    			    			    
			    $card_id = isset($data['card_id'])?$data['card_id']:'';		    
			    try{
			       $merchant_id = CWallet::getAccountID($card_id);		    
			       $merchant = CMerchants::get($merchant_id);	
			       $params['restaurant_name'] = Yii::app()->input->xssClean($merchant->restaurant_name);		   
			    } catch (Exception $e) {
			    	//
			    }
			    
			    $options = OptionsTools::find(array('merchant_enabled_alert','merchant_email_alert','merchant_mobile_alert'),$merchant_id);
			    			    
				$enabled = isset($options['merchant_enabled_alert'])?$options['merchant_enabled_alert']:0;
				$enabled = $enabled==1?true:false;
				$email = isset($options['merchant_email_alert'])?$options['merchant_email_alert']:'';
				$phone = isset($options['merchant_mobile_alert'])?$options['merchant_mobile_alert']:'';
			    
				$this->runActions($template_id, $params , array('sms','email','push') , array(
			     'phone'=>$phone,
			     'email'=>$email,
			    ),array(
			      'channel'=>$merchant->merchant_uuid,
			      'type'=>'payout_cancelled',
			      'event'=>Yii::app()->params->realtime['notification_event'],
			    ),array(
			      'channel'=>$merchant->merchant_uuid,
			      'type'=>'payout_cancelled',			      
			    ));					
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
		
	public function actionsendnotifications()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$realtime = AR_admin_meta::getMeta(array('realtime_provider','realtime_app_enabled',
				  'pusher_app_id','pusher_key','pusher_secret','pusher_cluster','ably_apikey',
				  'piesocket_clusterid','piesocket_api_key','piesocket_api_secret'
				));						
				
				$realtime_app_enabled = isset($realtime['realtime_app_enabled'])?$realtime['realtime_app_enabled']['meta_value']:'';
				$realtime_provider = isset($realtime['realtime_provider'])?$realtime['realtime_provider']['meta_value']:'';
				
				$pusher_app_id = isset($realtime['pusher_app_id'])?$realtime['pusher_app_id']['meta_value']:'';
				$pusher_key = isset($realtime['pusher_key'])?$realtime['pusher_key']['meta_value']:'';
				$pusher_secret = isset($realtime['pusher_secret'])?$realtime['pusher_secret']['meta_value']:'';
				$pusher_cluster = isset($realtime['pusher_cluster'])?$realtime['pusher_cluster']['meta_value']:'';				
				$ably_apikey = isset($realtime['ably_apikey'])?$realtime['ably_apikey']['meta_value']:'';
				
				$piesocket_clusterid = isset($realtime['piesocket_clusterid'])?$realtime['piesocket_clusterid']['meta_value']:'';
				$piesocket_api_key = isset($realtime['piesocket_api_key'])?$realtime['piesocket_api_key']['meta_value']:'';
				$piesocket_api_secret = isset($realtime['piesocket_api_secret'])?$realtime['piesocket_api_secret']['meta_value']:'';
				
				if($realtime_app_enabled!=1){
					Yii::app()->end();
				}
				
				$notification_uuid = Yii::app()->input->get("notification_uuid");	
				$item = CNotificationData::getData($notification_uuid);
				
				$image=''; $url = '';
				if($item->image_type=="icon"){
					$image = !empty($item->image)?$item->image:'';
				} else {
					if(!empty($item->image)){
						$image = CMedia::getImage($item->image,$item->image_path,
						Yii::app()->params->size_image_thumbnail ,
						CommonUtility::getPlaceholderPhoto('item') );
					}
				}
				
				$params = !empty($item->message_parameters)?json_decode($item->message_parameters,true):'';
				
				$data=array(
				  'notification_type'=>$item->notification_type,
				  'message'=>t($item->message,(array)$params),
				  'date'=>PrettyDateTime::parse(new DateTime($item->date_created)),				  
				  'image_type'=>$item->image_type,
				  'image'=>$image,
				  'url'=>$url
				);
							
				$settings = array(
				  'notication_channel'=>$item->notication_channel,
				  'notification_event'=>$item->notification_event,
				  'app_id'=>$pusher_app_id,
				  'key'=>$pusher_key,
				  'secret'=>$pusher_secret,
				  'cluster'=>$pusher_cluster,	
				  'ably_apikey'=>$ably_apikey,
				  'piesocket_clusterid'=>$piesocket_clusterid,
				  'piesocket_api_key'=>$piesocket_api_key,
				  'piesocket_api_secret'=>$piesocket_api_secret,
				);
								
				CNotifier::send($realtime_provider,$settings,$data);

				$item->status="process";
				$item->save();
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionsendwebpush()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$settings = AR_admin_meta::getMeta(array('webpush_app_enabled','webpush_provider',
				  'pusher_instance_id','onesignal_app_id','pusher_primary_key','onesignal_rest_apikey'
				));			
				
				$webpush_app_enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';						
				$webpush_app_enabled = $webpush_app_enabled==1?true:false;
				$webpush_provider = isset($settings['webpush_provider'])?$settings['webpush_provider']['meta_value']:'';
				$pusher_instance_id = isset($settings['pusher_instance_id'])?$settings['pusher_instance_id']['meta_value']:'';
				$pusher_primary_key = isset($settings['pusher_primary_key'])?$settings['pusher_primary_key']['meta_value']:'';
				$onesignal_app_id = isset($settings['onesignal_app_id'])?$settings['onesignal_app_id']['meta_value']:'';
				$onesignal_rest_apikey = isset($settings['onesignal_rest_apikey'])?$settings['onesignal_rest_apikey']['meta_value']:'';
				
				$pushweb_config = array(
				  'provider'=>$webpush_provider,
				  'pusher_instance_id'=>$pusher_instance_id,
				  'pusher_instance_id'=>$pusher_instance_id,
				  'pusher_primary_key'=>$pusher_primary_key,
				  'onesignal_app_id'=>$onesignal_app_id,
				  'onesignal_rest_apikey'=>$onesignal_rest_apikey,				  
				);		
								
				$push_uuid = Yii::app()->input->get("push_uuid");	
				$model = AR_push::model()->find("push_uuid=:push_uuid",array(
				 ':push_uuid'=>$push_uuid
				));								
				if($model){							
					$pushweb_config['channel'] = $model->channel_device_id;
					$params = array(					  
					  'title'=>$model->title,
					  'body'=>$model->body,
					);								
					if($model->provider=="onesignal"){
						if($device_ids = CNotificationData::getDeviceInterest(array($model->channel_device_id))){						   				
						   $params['device_ids'] = $device_ids;						
						}
					}	
					try {				
					  $resp = CPushweb::send($pushweb_config,$params);
					} catch (Exception $e) {
						$resp = $e->getMessage();
					}					
					$model->response = $resp;
					$model->status = 'process';
					$model->save();
				} else $this->msg[] = "no results";
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}	
	
	public function actionTestPusher()
	{
			$realtime = AR_admin_meta::getMeta(array('realtime_provider','realtime_app_enabled',
			  'pusher_app_id','pusher_key','pusher_secret','pusher_cluster','ably_apikey',
			  'piesocket_clusterid','piesocket_api_key','piesocket_api_secret'
			));						
			
			$realtime_app_enabled = isset($realtime['realtime_app_enabled'])?$realtime['realtime_app_enabled']['meta_value']:'';
			$realtime_provider = isset($realtime['realtime_provider'])?$realtime['realtime_provider']['meta_value']:'';
			
			$pusher_app_id = isset($realtime['pusher_app_id'])?$realtime['pusher_app_id']['meta_value']:'';
			$pusher_key = isset($realtime['pusher_key'])?$realtime['pusher_key']['meta_value']:'';
			$pusher_secret = isset($realtime['pusher_secret'])?$realtime['pusher_secret']['meta_value']:'';
			$pusher_cluster = isset($realtime['pusher_cluster'])?$realtime['pusher_cluster']['meta_value']:'';				
			$ably_apikey = isset($realtime['ably_apikey'])?$realtime['ably_apikey']['meta_value']:'';
			
			$piesocket_clusterid = isset($realtime['piesocket_clusterid'])?$realtime['piesocket_clusterid']['meta_value']:'';
			$piesocket_api_key = isset($realtime['piesocket_api_key'])?$realtime['piesocket_api_key']['meta_value']:'';
			$piesocket_api_secret = isset($realtime['piesocket_api_secret'])?$realtime['piesocket_api_secret']['meta_value']:'';
			
			$settings = array(
			  'notication_channel'=>'7695f9c5-23f7-11ec-bc4b-9c5c8e164c2c',
			  'notification_event'=>'event-tracking-order',
			  'app_id'=>$pusher_app_id,
			  'key'=>$pusher_key,
			  'secret'=>$pusher_secret,
			  'cluster'=>$pusher_cluster,	
			  'ably_apikey'=>$ably_apikey,
			  'piesocket_clusterid'=>$piesocket_clusterid,
			  'piesocket_api_key'=>$piesocket_api_key,
			  'piesocket_api_secret'=>$piesocket_api_secret,
			);
			dump($realtime_provider);
			dump($settings);
			$data = array('order_progress'=>2);	
			dump($data);
							
			CNotifier::send($realtime_provider,$settings,$data);
			
	}
	
	public function actiontrackorder()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$order_uuid = Yii::app()->input->get("order_uuid");							
				$data = CTrackingOrder::getProgress($order_uuid);				
				$client_uuid = isset($data['customer'])?$data['customer']['client_uuid']:'';
				if($data['customer']){unset($data['customer']);}
				$settings = CNotificationData::getRealtimeSettings();
				$provider = isset($settings['provider'])?$settings['provider']:'';				
				
				$settings['notication_channel'] = $client_uuid;
				$settings['notification_event'] = Yii::app()->params->realtime['event_tracking_order'];				
								
				//dump($data);die();				
				CNotifier::send($provider,$settings,$data);
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionaftermerchantsignup()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$merchant_uuid = Yii::app()->input->get("merchant_uuid");
				$merchant = CMerchants::getByUUID($merchant_uuid);
								
		        try {
				    CWallet::getCardID( Yii::app()->params->account_type['merchant'] , $merchant->merchant_id );
				} catch (Exception $e) {
				    $wallet = new AR_wallet_cards;	
				    $wallet->account_type = Yii::app()->params->account_type['merchant'] ;
			        $wallet->account_id = intval($merchant->merchant_id);
			        $wallet->save();
				}	
				
				/*ADD DEFAULT PAYMENT GATEWAY*/
				$payment_list = $list = CommonUtility::getDataToDropDown("{{payment_gateway}}",'payment_code','payment_code',
		        "WHERE status='active'","ORDER BY sequence ASC");
				if(is_array($payment_list) && count($payment_list)>=1){				
					$payment_data  = array();
					foreach ($payment_list as $payment_item) {
						$payment_data[]=$payment_item;
					}							
					MerchantTools::saveMerchantMeta($merchant->merchant_id,$payment_data,'payment_gateway');
				}
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionaftermerchantpayment()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$merchant_uuid = Yii::app()->input->get("merchant_uuid");
				$merchant = CMerchants::getByUUID($merchant_uuid);
				
				$options = OptionsTools::find(array('registration_confirm_account_tpl','merchant_registration_welcome_tpl'));
				$template_id = isset($options['registration_confirm_account_tpl'])?$options['registration_confirm_account_tpl']:'';
				
				$confirm_link = Yii::app()->createAbsoluteUrl("/merchant/confirm-account?uuid=".$merchant->merchant_uuid);
				
				$plans = Cplans::planDetails($merchant->package_id,Yii::app()->language);				
				
				$site = CNotifications::getSiteData();
				$params = array(					      
			      'site'=>$site,
			      'logo'=>isset($site['logo'])?$site['logo']:'',
			      'facebook'=>isset($site['facebook'])?$site['facebook']:'',
			      'twitter'=>isset($site['twitter'])?$site['twitter']:'',
			      'instagram'=>isset($site['instagram'])?$site['instagram']:'',
			      'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
			      'youtube'=>isset($site['youtube'])?$site['youtube']:'',
			      'restaurant_name'=>$merchant->restaurant_name,
			      'contact_phone'=>$merchant->contact_phone,
			      'contact_email'=>$merchant->contact_email,
			      'address'=>$merchant->address,	
			      'confirm_link'=>$confirm_link,
			      'plan_title'=>$plans->title,
			    );		
			    
			    $email = $merchant->contact_email;
			    $phone = $merchant->contact_phone;
			    
			    $this->runActions($template_id, $params , array('sms','email','push') , array(
			     'phone'=>$phone,
			     'email'=>$email,
			    ),array(
			      'channel'=>$merchant->merchant_uuid,
			      'type'=>'merchant_confirm_account',
			      'event'=>Yii::app()->params->realtime['notification_event'],
			    ),array(
			      'channel'=>$merchant->merchant_uuid,
			      'type'=>'merchant_confirm_account',			      
			    ));		
			    
			    
			    /*SEND EMAIL TO ADMIN*/
			    
			    $options = OptionsTools::find(array('admin_email_alert','admin_mobile_alert','admin_enabled_alert','merchant_registration_new_tpl'));
				$enabled = isset($options['admin_enabled_alert'])?$options['admin_enabled_alert']:0;
				$enabled = $enabled==1?true:false;
				$email = isset($options['admin_email_alert'])?$options['admin_email_alert']:'';
				$phone = isset($options['admin_mobile_alert'])?$options['admin_mobile_alert']:'';
																
				if($enabled){
					$template_id = isset($options['merchant_registration_new_tpl'])?$options['merchant_registration_new_tpl']:'';								
					$this->runActions($template_id, $params , array('sms','email','push') , array(
				      'phone'=>$phone,
				      'email'=>$email,
				    ),array(
				        'channel'=>Yii::app()->params->realtime['admin_channel'],
				        'type'=>'merchant_new_signup',
				        'event'=>Yii::app()->params->realtime['notification_event'],
				    ),array(
				        'channel'=>Yii::app()->params->realtime['admin_channel'],
				        'type'=>'merchant_new_signup',			      
				    ));			
				}
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionafter_plan_past_due()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$merchant_uuid = Yii::app()->input->get("merchant_uuid");
				$merchant = CMerchants::getByUUID($merchant_uuid);				
				
				$options = OptionsTools::find(array('merchant_plan_expired_tpl'));
                $template_id = isset($options['merchant_plan_expired_tpl'])?$options['merchant_plan_expired_tpl']:'';
                
                $site = CNotifications::getSiteData();
	            $params = array(					      
	              'site'=>$site,
	              'logo'=>isset($site['logo'])?$site['logo']:'',
	              'facebook'=>isset($site['facebook'])?$site['facebook']:'',
	              'twitter'=>isset($site['twitter'])?$site['twitter']:'',
	              'instagram'=>isset($site['instagram'])?$site['instagram']:'',
	              'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
	              'youtube'=>isset($site['youtube'])?$site['youtube']:'',
	              'restaurant_name'=>$merchant->restaurant_name,
	              'contact_phone'=>$merchant->contact_phone,
	              'contact_email'=>$merchant->contact_email,
	              'address'=>$merchant->address	              
	            );		
	            
	            $email = $merchant->contact_email;
                $phone = $merchant->contact_phone;
                
                $this->runActions($template_id, $params , array('sms','email','push') , array(
	             'phone'=>$phone,
	             'email'=>$email,
	            ),array(
	              'channel'=>$merchant->merchant_uuid,
	              'type'=>'merchant_plan_expired',
	              'event'=>Yii::app()->params->realtime['notification_event'],
	            ),array(
	              'channel'=>$merchant->merchant_uuid,
	              'type'=>'merchant_plan_expired',			      
	            ));		
            
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionmerchant_trial_end()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$merchant_uuid = Yii::app()->input->get("merchant_uuid");
				$trial_end = Yii::app()->input->get("trial_end");
				$merchant = CMerchants::getByUUID($merchant_uuid);				
				
				$options = OptionsTools::find(array('merchant_plan_near_expired_tpl'));
                $template_id = isset($options['merchant_plan_near_expired_tpl'])?$options['merchant_plan_near_expired_tpl']:'';
                
                $site = CNotifications::getSiteData();
	            $params = array(					      
	              'site'=>$site,
	              'logo'=>isset($site['logo'])?$site['logo']:'',
	              'facebook'=>isset($site['facebook'])?$site['facebook']:'',
	              'twitter'=>isset($site['twitter'])?$site['twitter']:'',
	              'instagram'=>isset($site['instagram'])?$site['instagram']:'',
	              'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
	              'youtube'=>isset($site['youtube'])?$site['youtube']:'',
	              'restaurant_name'=>$merchant->restaurant_name,
	              'contact_phone'=>$merchant->contact_phone,
	              'contact_email'=>$merchant->contact_email,
	              'address'=>$merchant->address,
	              'expiration_date'=>Date_Formatter::date($trial_end)
	            );		
	            	            
	            $email = $merchant->contact_email;
                $phone = $merchant->contact_phone;
                
                $this->runActions($template_id, $params , array('sms','email','push') , array(
	             'phone'=>$phone,
	             'email'=>$email,
	            ),array(
	              'channel'=>$merchant->merchant_uuid,
	              'type'=>'merchant_trial_end',
	              'event'=>Yii::app()->params->realtime['notification_event'],
	            ),array(
	              'channel'=>$merchant->merchant_uuid,
	              'type'=>'merchant_trial_end',			      
	            ));		
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	public function actionadminpassword()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {

				$options = OptionsTools::find(array('backend_forgot_password_tpl'));
				$template_id = isset($options['backend_forgot_password_tpl'])?$options['backend_forgot_password_tpl']:'';				
				
				$admin_token = Yii::app()->input->get("admin_token");
				$model = AR_AdminUser::model()->find("admin_id_token=:admin_id_token AND status=:status",[
					':admin_id_token'=>$admin_token,
					':status'=>'active'
				]);				
				if($model){					
					$site = CNotifications::getSiteData();
					$data = array(		
						'first_name'=>$model->first_name,
						'last_name'=>$model->last_name,						
						'site'=>$site,
						'logo'=>isset($site['logo'])?$site['logo']:'',
						'facebook'=>isset($site['facebook'])?$site['facebook']:'',
						'twitter'=>isset($site['twitter'])?$site['twitter']:'',
						'instagram'=>isset($site['instagram'])?$site['instagram']:'',
						'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
						'youtube'=>isset($site['youtube'])?$site['youtube']:'',
						'reset_password_link'=>websiteUrl()."/".BACKOFFICE_FOLDER."/forgotpassword/reset?token=".$model->admin_id_token
					);							
					$this->runActions($template_id, $data , array('email') , array(					     
						'email'=>$model->email_address,
					));
				}
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}				
		}
	}
	
	public function actionmerchantpassword()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {

				$options = OptionsTools::find(array('backend_forgot_password_tpl'));
				$template_id = isset($options['backend_forgot_password_tpl'])?$options['backend_forgot_password_tpl']:'';				
				
				$user_uuid = Yii::app()->input->get("user_uuid");
				
				$model = AR_merchant_login::model()->find("user_uuid=:user_uuid AND status=:status",[
					':user_uuid'=>$user_uuid,
					':status'=>'active'
				]);	
				if($model){				
					$site = CNotifications::getSiteData();
					$data = array(		
						'first_name'=>$model->first_name,
						'last_name'=>$model->last_name,						
						'site'=>$site,
						'logo'=>isset($site['logo'])?$site['logo']:'',
						'facebook'=>isset($site['facebook'])?$site['facebook']:'',
						'twitter'=>isset($site['twitter'])?$site['twitter']:'',
						'instagram'=>isset($site['instagram'])?$site['instagram']:'',
						'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
						'youtube'=>isset($site['youtube'])?$site['youtube']:'',
						'reset_password_link'=>websiteUrl()."/".BACKOFFICE_FOLDER."/resetpswd/reset?token=".$model->user_uuid
					);		
					$this->runActions($template_id, $data , array('email') , array(					     
						'email'=>$model->contact_email,
					));
				}				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);			
		}
	}

	public function actionresend_otp()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
				$options = OptionsTools::find(array('signup_verification_tpl'));															
				$template_id = isset($options['signup_verification_tpl'])?$options['signup_verification_tpl']:'';

				$client_uuid = Yii::app()->input->get("client_uuid");		
				$verification_type =  Yii::app()->input->get("verification_type");				
				$model = AR_client::model()->find("client_uuid=:client_uuid",array(
					':client_uuid'=>$client_uuid
			   ));
			   if($model){
					$site = CNotifications::getSiteData();						  
					$data = array(		
					'first_name'=>$model->first_name,
					'last_name'=>$model->last_name,
					'email_address'=>$model->email_address,
					'code'=>$model->mobile_verification_code,
					'site'=>$site,
					'logo'=>isset($site['logo'])?$site['logo']:'',
					'facebook'=>isset($site['facebook'])?$site['facebook']:'',
					'twitter'=>isset($site['twitter'])?$site['twitter']:'',
					'instagram'=>isset($site['instagram'])?$site['instagram']:'',
					'whatsapp'=>isset($site['whatsapp'])?$site['whatsapp']:'',
					'youtube'=>isset($site['youtube'])?$site['youtube']:'',
					);	
					$this->runActions($template_id, $data , array($verification_type) , array(
					   'phone'=>$model->contact_phone,
					   'email'=>$model->email_address
					));
			   }

			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}
	
	/*public function actionafterregistration()
	{
		Yii::import('ext.runactions.components.ERunActions');
		if (ERunActions::runBackground()) {
			try {
				
			} catch (Exception $e) {
			    $this->msg[] = t($e->getMessage());			    			    			    
			}	
			dump($this->msg);
			Yii::log( json_encode($this->msg) , CLogger::LEVEL_ERROR);
		}
	}*/
	
}
/*end class*/