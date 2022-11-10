<?php
class ApibackendController extends CommonServices
{		
	public function beforeAction($action)
	{								
		$method = Yii::app()->getRequest()->getRequestType();
		if($method=="PUT"){
			$this->data = Yii::app()->input->xssClean(json_decode(file_get_contents('php://input'), true));			
		} else $this->data = Yii::app()->input->xssClean($_POST);				
		return true;
	}
	
	/*public function beforeAction($action)
	{								
		$method = Yii::app()->getRequest()->getRequestType();
		if($method=="PUT"){			
			$this->data = Yii::app()->input->xssClean(json_decode( Yii::app()->request->rawBody , true));		
		} else $this->data = Yii::app()->input->xssClean( json_decode( Yii::app()->request->rawBody , true) );				
		return true;
	}*/
	
	public function actionorderList()
	{				
		$merchant_id = Yii::app()->merchant->merchant_id;
		$order_status = isset($this->data['order_status'])?$this->data['order_status']:'';
		$schedule = isset($this->data['schedule'])?$this->data['schedule']:'';
		$schedule = $schedule==1?true:false;
		$filter = isset($this->data['filter'])?$this->data['filter']:'';		
							
		try {			 
			$data = AOrders::getOrderAll($merchant_id, $order_status, $schedule , date("Y-m-d") , date("Y-m-d g:i:s a") , $filter );				
			$meta = AOrders::getOrderMeta( $data['all_order'] );	
			$status = COrders::statusList(Yii::app()->language);    	
    	    $services = COrders::servicesList(Yii::app()->language);
    	        	            	    
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$data['data'],
			  'total'=>$data['total'],
			  'meta'=>$meta,
			  'status'=>$status,
			  'services'=>$services,
			);						
		} catch (Exception $e) {
		   $this->msg[] = t($e->getMessage());
		}		
		$this->responseJson();
	}
	
	public function actionorderDetails()
	{	
		
		$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
		$group_name = isset($this->data['group_name'])?$this->data['group_name']:'';		
		$filter_buttons = isset($this->data['filter_buttons'])?$this->data['filter_buttons']:'';
		$payload = isset($this->data['payload'])?$this->data['payload']:'';
		$modify_order = isset($this->data['modify_order'])?intval($this->data['modify_order']):'';
				
		try {
						
			COrders::getContent($order_uuid,Yii::app()->language);
		    $merchant_id = COrders::getMerchantId($order_uuid);
		    $merchant_info = COrders::getMerchant($merchant_id,Yii::app()->language);
		    $items = COrders::getItems();		     
		    $summary = COrders::getSummary();	
		    //dump($summary);die();
		    $summary_total = COrders::getSummaryTotal();
		    
		    $summary_changes = array(); $summary_transaction = array();
		    if($modify_order==1){
		       $summary_changes = COrders::getSummaryChanges();
		    } else $summary_transaction = COrders::getSummaryTransaction();
		    		    
		    $order = COrders::orderInfo(Yii::app()->language, date("Y-m-d") );		
		    $order_type = isset($order['order_info'])?$order['order_info']['order_type']:'';
		    $payment_code = isset($order['order_info'])?$order['order_info']['payment_code']:'';
		    $client_id = $order?$order['order_info']['client_id']:0;		
		    $order_id = $order?$order['order_info']['order_id']:'';
			$origin_latitude = $order?$order['order_info']['latitude']:'';
			$origin_longitude = $order?$order['order_info']['longitude']:'';    
		    
			$delivery_direction = isset($merchant_info['restaurant_direction'])?$merchant_info['restaurant_direction']:'';
			if($order_type=="delivery"){
				$delivery_direction = isset($merchant_info['restaurant_direction'])?$merchant_info['restaurant_direction']:'';
				$delivery_direction.="&origin="."$origin_latitude,$origin_longitude";
			} 

			$order['order_info']['delivery_direction'] = $delivery_direction;			

		    $customer = COrders::getClientInfo($client_id);				    
		    $count = COrders::getCustomerOrderCount($client_id,$merchant_id);
		    $customer['order_count'] = $count;
		    		    
		    $buttons = array(); $link_pdf = '';  $print_settings = array(); $payment_history = array();
		    
		    if(in_array('buttons',(array)$payload)){		 
		      if($filter_buttons){
		      	 $buttons = AOrders::getOrderButtons($group_name,$order_type);
		      } else $buttons = AOrders::getOrderButtons($group_name);		      
		    }
		    		    		   
		    if(in_array('print_settings',(array)$payload)){
			    $link_pdf = array(
			      'pdf_a4'=>Yii::app()->CreateUrl("print/pdf",array('order_uuid'=>$order_uuid,'size'=>"a4")),
			      'pdf_receipt'=>Yii::app()->CreateUrl("print/pdf",array('order_uuid'=>$order_uuid,'size'=>"thermal")),
			    );		    
			    $print_settings = AOrderSettings::getPrintSettings();
		    }
		    		    
		    if(in_array('payment_history',(array)$payload)){    
		       $payment_history = COrders::paymentHistory($order_id);
		    }
		    
		    /*CHECK IF ORDER IS NEW AND OFFLINE PAYMENT AND MERCHANT IS COMMISSION*/		    
		    $all_offline = CPayments::getPaymentTypeOnline(0);
		    if(!$summary_changes && $group_name=="new_order" && array_key_exists($payment_code,(array)$all_offline) && $merchant_info['merchant_type']==2 ){
		    	$summary_changes = array(
		    	  'method'=>'less_on_account'
		    	);
            }
		    		    		    		    		    		  
		    $data = array(
		       'merchant'=>$merchant_info,
		       'order'=>$order,
		       'items'=>$items,
		       'summary'=>$summary,		
		       'summary_total'=>$summary_total,		       
		       'summary_changes'=>$summary_changes,
		       'summary_transaction'=>$summary_transaction,
		       'customer'=>$customer,
		       'buttons'=>$buttons,
		       'sold_out_options'=>AttributesTools::soldOutOptions(),
		       'link_pdf'=>$link_pdf,
		       'print_settings'=>$print_settings,
		       'payment_history'=>$payment_history
		    );		
		    
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(			 		      
		       'data'=>$data,		      
		    );		  
		    		    
		    //dump($summary);
		    //die();
		    		    		    		   
		    $model = COrders::get($order_uuid);
		    $model->is_view = 1;
		    $model->save();		  
		    		        
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   		    
		}			
		$this->responseJson();
	}
	
	public function actionupdateOrderStatus()
	{
		try {
						
			$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
			$rejetion_reason = isset($this->data['reason'])?$this->data['reason']:'';
							
			$status = AOrders::getOrderButtonStatus($uuid);
			$do_actions = AOrders::getOrderButtonActions($uuid);
					
			$model = COrders::get($order_uuid);	
			
			if($do_actions=="reject_form"){
				$model->scenario = "reject_order";
			} else $model->scenario = "change_status";			
			
			if($model->status==$status){
				$this->msg = t("Order has the same status");
				$this->responseJson();
			}
			
			
			/*dump($status);
			dump($do_actions);
			dump($model->payment_code);
			die();*/			
			
			/*CHECK IF HAS EXISTING REFUND*/
			/*if($do_actions=="reject_form"){
			   COrders::getExistingRefund($model->order_id);
			}*/
						
			$model->status = $status;			
			$model->remarks = $rejetion_reason;
			$model->change_by = Yii::app()->merchant->first_name;
			if($model->save()){
			   $this->code = 1;
			   $this->msg = t("Status Updated");
			   
			   if(!empty($rejetion_reason)){
			   	  COrders::savedMeta($model->order_id,'rejetion_reason',$rejetion_reason);
			   }
			   
			} else $this->msg = CommonUtility::parseError( $model->getErrors());
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}		
		$this->responseJson();
	}
	
	public function actioncreateRefund()
	{
		try {
			
			
			$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
			
			$order = COrders::get($order_uuid);
			COrders::getContent($order_uuid,Yii::app()->language);
			$summary = COrders::getSummary();
			$summary_changes = COrders::getSummaryChanges();			
			$refund_due = isset($summary_changes['refund_due'])?floatval($summary_changes['refund_due']):0;

			$status = AOrders::getOrderButtonStatus($uuid);		
			$order->scenario = "change_status";			
						
			if($refund_due>0){
				$model = new AR_ordernew_summary_transaction;
				$model->scenario = "refund";
				$model->order = $order;
				$model->order_id = $order->order_id;
				$model->transaction_description = "Refund";
				$model->transaction_amount = floatval($refund_due);
				$model->save();			
				
				$order->status = $status;
				$order->save();
								
				$this->code = 1; $this->msg = "OK";					
			} else $this->msg = t("Amount to refund cannot be less than 0");
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}		
		$this->responseJson();
	}
	
	public function actioncreateInvoice()
	{
		try {
						
			$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
			
			$order = COrders::get($order_uuid);
			COrders::getContent($order_uuid,Yii::app()->language);
			$summary = COrders::getSummary();
			$summary_changes = COrders::getSummaryChanges();			
			$refund_due = isset($summary_changes['refund_due'])?floatval($summary_changes['refund_due']):0;

			$status = AOrders::getOrderButtonStatus($uuid);		
			$order->scenario = "change_status";			
						
			if($refund_due>0){
				$model = new AR_ordernew_summary_transaction;
				$model->scenario = "invoice";
				$model->order = $order;
				$model->order_id = $order->order_id;
				$model->transaction_type = "credit";
				$model->transaction_description = "Collect payment";
				$model->transaction_amount = floatval($refund_due);
				$model->status = "process";
				$model->save();			
				
				$order->status = $status;
				//$order->save();
				
				$this->code = 1; $this->msg = "OK";					
			} else $this->msg = t("Amount to refund cannot be less than 0");
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}		
		$this->responseJson();
	}
	
	public function actionlessOnAccount()
	{
		try {
						
			$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
			
			$order = COrders::get($order_uuid);
			COrders::getContent($order_uuid,Yii::app()->language);
			$summary = COrders::getSummary();
			$summary_changes = COrders::getSummaryChanges();			
			$refund_due = isset($summary_changes['refund_due'])?floatval($summary_changes['refund_due']):0;

			$status = AOrders::getOrderButtonStatus($uuid);		
			$order->scenario = "change_status";		
			
			$card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] , $order->merchant_id );
			$balance = CWallet::getBalance($card_id);
			$balance = Price_Formatter::convertToRaw($balance);
			$refund_due = Price_Formatter::convertToRaw($refund_due);
						
			if($balance<$refund_due){
				$this->msg = t("You don't have enough balance in your account. please load your account to process this order.");
			   	$this->responseJson();			   	
            }
									
			if($refund_due>0){
				$model = new AR_ordernew_summary_transaction;
				$model->scenario = "less_account";
				$model->card_id = $card_id;
				$model->order = $order;
				$model->order_id = $order->order_id;
				$model->transaction_type = "debit";
				$model->transaction_description = "Less on account";
				$model->transaction_amount = floatval($refund_due);
				$model->status = "process";
				$model->save();			
				
				$order->status = $status;
				$order->save();
				
				$this->code = 1; $this->msg = "OK";		
			} else $this->msg = t("Amount to less cannot be less than 0");
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}		
		$this->responseJson();
	}
	
	public function actionlesscashonaccount()
	{
		try {
			
			$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
			
			$order = COrders::get($order_uuid);
			$status = AOrders::getOrderButtonStatus($uuid);		
			$order->scenario = "change_status";		
			
			$amount = floatval($order->commission);
			
			$card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] , $order->merchant_id );
			$balance = CWallet::getBalance($card_id);
			$balance = Price_Formatter::convertToRaw($balance);
			
			if($balance<$amount){
				$this->msg = t("You don't have enough balance in your account. please load your account to process this order.");
			   	$this->responseJson();			   	
            }
            
            $card_admin_id = CWallet::getCardID( Yii::app()->params->account_type['admin'] , 0);            
            
            $params = array(
			  'merchant_id'=>$order->merchant_id,					  
			  'transaction_description'=>"Payment to order #{{order_id}}",
			  'transaction_description_parameters'=>array('{{order_id}}'=>$order->order_id),					  
			  'transaction_type'=>"debit",
			  'transaction_amount'=>floatval($amount),
			  'meta_name'=>"order",
			  'meta_value'=>$order->order_id,
			  'status'=>"paid"
			);			
			CWallet::inserTransactions($card_id,$params);	
			
			$order->status = $status;
			$order->save();
             
			$this->code = 1; $this->msg = "OK";
                       
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}		
		$this->responseJson();
	}
	
	public function actionupdateOrderStatusManual()
	{		
		try {
			$stats_id = isset($this->data['stats_id'])?intval($this->data['stats_id']):'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
			
			$model = COrders::get($order_uuid);	
			$model->scenario = "change_status";
			
			$model_status = AR_status::model()->find("stats_id=:stats_id",array(
			 ':stats_id'=>intval($stats_id)
			));
			if($model_status){
				$status = $model_status->description;
				
				if($model->status==$status){
					$this->msg = t("Order has the same status");
					$this->responseJson();
				}
				
				$model->status = $status;				
				if($model->save()){
					$this->code = 1;
			        $this->msg = t("Status Updated");
				} else $this->msg = CommonUtility::parseError( $model->getErrors());
			} else $this->msg =  t("Status not found");
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}		
		$this->responseJson();
	}
	
	public function actioncancelOrder()
	{		
		try {
												
			$model = AR_admin_meta::model()->find('meta_name=:meta_name', 
			  array(':meta_name'=>'status_cancel_order')
			);			
			if($model){				
				$status_cancelled = $model->meta_value ;
			} else $status_cancelled = 'cancelled';

					
			$reason = isset($this->data['reason'])?trim($this->data['reason']):'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
			$model = COrders::get($order_uuid);	
			$model->scenario = "cancel_order";
			
			if($model->status==$status_cancelled){
				$this->msg = t("Order has the same status");
				$this->responseJson();
			}
			
			/*CHECK IF HAS EXISTING REFUND*/
			//COrders::getExistingRefund($model->order_id);
							
			$model->status = $status_cancelled;
			$model->remarks = $reason;
			
			if($model->save()){
			   $this->code = 1;
			   $this->msg = t("Order is cancelled");			   
			   if(!empty($reason)){
			   	  COrders::savedMeta($model->order_id,'rejetion_reason',$reason);
			   }			   
			} else $this->msg = CommonUtility::parseError( $model->getErrors());
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actionorderRejectionList()
	{
		try {
			$data = AOrders::rejectionList();		
			$this->code = 1;
			$this->msg = "ok";
			$this->details = $data;
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}		
		$this->responseJson();	
	}
	
	public function actiongetOrderStatusList()
	{		
		if ($data = AttributesTools::getOrderStatusList(Yii::app()->language)){
			$this->code =1; $this->msg = "ok";
			$this->details = $data;
		} else $this->msg = t("No results");
		$this->responseJson();	
	}
	
	public function actiongetDelayedMinutes()
	{
		$times = AttributesTools::delayedMinutes();
		$this->code = 1;
		$this->msg = "ok";
		$this->details = $times;
		$this->responseJson();	
	}
	
	public function actionsetDelayToOrder()
	{		
		try {
			
			$time_delay = isset($this->data['time_delay'])?intval($this->data['time_delay']):'';
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
			$model = COrders::get($order_uuid);	
			$model->scenario = "delay_order";
			
			//$model->status = "delayed";
			$model->remarks = "Order is delayed by [mins]min(s)";
			$model->ramarks_trans = json_encode(array('[mins]'=>$time_delay));
			if($model->save()){
			   $this->code = 1;
			   $this->msg = t("Customer is notified about the delayed.");					   
			   COrders::savedMeta($model->order_id,'delayed_order', t($model->remarks,array('[mins]'=>$time_delay)) );			   	   
			   COrders::savedMeta($model->order_id,'delayed_order_mins',$time_delay );			   	   
			} else $this->msg = CommonUtility::parseError( $model->getErrors());
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actiongetOrderHistory()
	{
		try {			
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
			$data = AOrders::getOrderHistory($order_uuid);
			$order_status = AttributesTools::getOrderStatus(Yii::app()->language);
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$data,
			  'order_status'=>$order_status
			);						
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actionitemChanges()
	{		
		try {
						
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
			$item_row = isset($this->data['item_row'])?$this->data['item_row']:'';		
			$out_stock_options = isset($this->data['out_stock_options'])?intval($this->data['out_stock_options']):0;	
			$item_changes = isset($this->data['item_changes'])?$this->data['item_changes']:'';		
								
			$model = COrders::get($order_uuid);			
			
			$items = AR_ordernew_item::model()->find("item_row=:item_row",array(
			 ':item_row'=>$item_row
			));		
												
			$refund_item_details = array();
			if($item_changes=="refund" || $item_changes=="out_stock"){	
				
				/*$item_count = COrders::itemCount($model->order_id);
				if($item_count<=1){
					$this->msg = t("Failed. order items cannot be empty.");
					$this->responseJson();
                }*/
				
				$refund_item_details = COrders::getRefundItemTotal($item_changes,$model->tax,$items->order_id,$items->item_row);				
			}
									
			if($items){				
				$items->scenario = $item_changes;
				$items->item_changes = $item_changes;
				$items->order_uuid = $order_uuid; 		
				$items->refund_item_details = $refund_item_details;	
				if($items->delete()){
					$this->code = 1;
			        $this->msg = t("Succesful");			        
				} else $this->msg = CommonUtility::parseError( $model->getErrors());
			} else $this->msg = t("Item row not found");			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actionadditionalCharge()
	{
		try {			
			$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			
			$item_row = isset($this->data['item_row'])?$this->data['item_row']:'';		
			$additional_charge = isset($this->data['additional_charge'])?$this->data['additional_charge']:'';		
			$additional_charge_name = isset($this->data['additional_charge_name'])?$this->data['additional_charge_name']:'';
			$additional_charge = floatval($additional_charge);
			
			$additional_charge_name = !empty($additional_charge_name)?$additional_charge_name:'Additional charge applied';
						
			$model = COrders::get($order_uuid);	
			
			if($additional_charge>0){
				$item = new AR_ordernew_additional_charge;
				$item->order_id = $model->order_id;
				$item->item_row = $item_row;								
				$item->charge_name = $additional_charge_name;				
				$item->additional_charge = $additional_charge;
				$item->order_uuid = $order_uuid;
				if($item->save()){
					$this->code = 1;
			        $this->msg = t("Succesful");			        
				} else $this->msg = CommonUtility::parseError( $model->getErrors());
			} else $this->msg = t("Additional charge must be greater than zero");
						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actionupdateOrderSummary()
	{
		try {
		   		   
		   $order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';		   
		   COrders::updateSummary($order_uuid);
		   $this->code = 1;
		   $this->msg = "OK";
		   
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actiongetCategory()
	{
		try {
			
		   //$merchant_id = Yii::app()->merchant->merchant_id;
		   $merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;		   
		   $category = CMerchantMenu::getCategory(intval($merchant_id),Yii::app()->language);				   
		   $data = array(
		     'category'=>$category,		     
		   );		   		   
		   $this->code = 1; $this->msg = "OK";
		   $this->details = array(		     		    
		     'data'=>$data
		   );		   
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();	
	}
	
	public function actioncategoryItem()
	{
		try {
											
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;	
		    $cat_id = isset($this->data['cat_id'])?intval($this->data['cat_id']):0;
		    $page  = isset($this->data['page'])?(integer)$this->data['page']:0;
		    $search = isset($this->data['q'])?trim($this->data['q']):'';
		    $items = array();		   
		    $items  = CMerchantMenu::CategoryItem(intval($merchant_id),$cat_id,$search,$page,Yii::app()->language);			    		    
		    $this->code = 1; $this->msg = "OK";
		    $this->details = $items;    
		    		    
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		  
		}
		$this->responseJson();	
	}
	
	public function actiongetMenuItem()
	{		
		try  {
			
			$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:'';
			$item_uuid = isset($this->data['item_uuid'])?trim($this->data['item_uuid']):'';
			$cat_id = isset($this->data['cat_id'])?(integer)$this->data['cat_id']:0;
			
			$items = CMerchantMenu::getMenuItem($merchant_id,$cat_id,$item_uuid,Yii::app()->language);
			$addons = CMerchantMenu::getItemAddonCategory($merchant_id,$item_uuid,Yii::app()->language);
			$addon_items = CMerchantMenu::getAddonItems($merchant_id,$item_uuid,Yii::app()->language);	
			$meta = CMerchantMenu::getItemMeta($merchant_id,$item_uuid);
			$meta_details = CMerchantMenu::getMeta($merchant_id,$item_uuid,Yii::app()->language);	
							
			$data = array(
			  'items'=>$items,
			  'addons'=>$addons,
			  'addon_items'=>$addon_items,
			  'meta'=>$meta,
			  'meta_details'=>$meta_details
			);
			
			$this->code = 1; $this->msg = "ok";
		    $this->details = array(		      
		      'sold_out_options'=>AttributesTools::soldOutOptions(),
		      'data'=>$data
		    );		    		    
		    
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $this->details = array(
		      'data'=>array()
		    );		    	   
		}
		$this->responseJson();	
	}
	
	public function actionaddCartItems()
	{			
		$cart_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
		$order_uuid = $cart_uuid;
		$cart_row = CommonUtility::createUUID("{{ordernew_item}}",'item_row');
		
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
		$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:'';
		$cat_id = isset($this->data['cat_id'])?(integer)$this->data['cat_id']:'';
		$item_token = isset($this->data['item_token'])?$this->data['item_token']:'';
		$old_item_token = isset($this->data['old_item_token'])?$this->data['old_item_token']:'';
		$item_row = isset($this->data['item_row'])?$this->data['item_row']:'';
		$item_size_id = isset($this->data['item_size_id'])?(integer)$this->data['item_size_id']:0;
		$item_qty = isset($this->data['item_qty'])?(integer)$this->data['item_qty']:0;
		$special_instructions = isset($this->data['special_instructions'])?$this->data['special_instructions']:'';
		$if_sold_out = isset($this->data['if_sold_out'])?$this->data['if_sold_out']:'';		
		$inline_qty = isset($this->data['inline_qty'])?(integer)$this->data['inline_qty']:0;
				
		if($old_item_token==$item_token){
			$this->msg = t("Cannot replace this item with the same item.");
			$this->responseJson();	
		}
				
		$addons = array();
		$item_addons = isset($this->data['item_addons'])?$this->data['item_addons']:'';
		if(is_array($item_addons) && count($item_addons)>=1){
			foreach ($item_addons as $val) {				
				$multi_option = isset($val['multi_option'])?$val['multi_option']:'';
				$subcat_id = isset($val['subcat_id'])?(integer)$val['subcat_id']:0;
				$sub_items = isset($val['sub_items'])?$val['sub_items']:'';
				$sub_items_checked = isset($val['sub_items_checked'])?(integer)$val['sub_items_checked']:0;				
				
				if($multi_option=="one" && $sub_items_checked>0){
									
					$addon_price = 0;	
					foreach ($sub_items as $sub_items_items) {
						if($sub_items_items['sub_item_id']==$sub_items_checked){
							$addon_price = $sub_items_items['price'];
						}
					}
					
					$addons[] = array(
					  'cart_row'=>$cart_row,
					  'cart_uuid'=>$cart_uuid,
					  'subcat_id'=>$subcat_id,
					  'sub_item_id'=>$sub_items_checked,					 
					  'qty'=>1,
					  'price'=>$addon_price,
					  'multi_option'=>$multi_option,
					);
				} else {
					foreach ($sub_items as $sub_items_val) {
						if($sub_items_val['checked']==1){							
							$addons[] = array(
							  'cart_row'=>$cart_row,
							  'cart_uuid'=>$cart_uuid,
							  'subcat_id'=>$subcat_id,
							  'sub_item_id'=>isset($sub_items_val['sub_item_id'])?(integer)$sub_items_val['sub_item_id']:0,							  
							  'qty'=>isset($sub_items_val['qty'])?(integer)$sub_items_val['qty']:0,
							  'price'=>isset($sub_items_val['price'])?(integer)$sub_items_val['price']:0,
							  'multi_option'=>$multi_option,
							);
						}
					}
				}
			}
		}
		
		
		$attributes = array();
		$meta = isset($this->data['meta'])?$this->data['meta']:'';
		if(is_array($meta) && count($meta)>=1){
			foreach ($meta as $meta_name=>$metaval) {				
				if($meta_name!="dish"){
					foreach ($metaval as $val) {
						if($val['checked']>0){	
							$attributes[]=array(
							  'cart_row'=>$cart_row,
							  'cart_uuid'=>$cart_uuid,
							  'meta_name'=>$meta_name,
							  'meta_id'=>$val['meta_id']
							);
						}
					}
				}
			}
		}
		
				
		try {
			
			$model = COrders::get($order_uuid);
			
			$criteria=new CDbCriteria();	
	        $criteria->alias = "a";
	        $criteria->select = "a.item_id,a.item_token,
	        b.item_size_id, b.price as item_price, b.discount, b.discount_type, b.discount_start,
	        b.discount_end,
	        (
		     select count(*) from {{view_item_lang_size}}
		     where item_size_id = b.item_size_id 		  
		     and CURDATE() >= discount_start and CURDATE() <= discount_end
		    ) as discount_valid
	        
	        ";
	        $criteria->condition = "a.merchant_id = :merchant_id AND a.item_token=:item_token
	        AND b.item_size_id=:item_size_id
	        ";
	        $criteria->params = array ( 
	          ':merchant_id'=>$merchant_id,
	          ':item_token'=>$item_token,
	          ':item_size_id'=>$item_size_id
	        );
	        $criteria->mergeWith(array(
			  'join'=>'LEFT JOIN {{item_relationship_size}} b ON a.item_id = b.item_id',				
		    ));
	        $item = AR_item::model()->find($criteria);	      
	        
	        if(!$item){
	        	$this->msg = t("Price is not valid");
	        	$this->responseJson();		
            }
                                    
	        $scenario = 'update_cart';
	        
			$items = array(
			  'order_uuid'=>$order_uuid,
			  'order_id'=>$model->order_id,
			  'merchant_id'=>$merchant_id,
			  'cart_row'=>$cart_row,
			  'cart_uuid'=>$cart_uuid,
			  'cat_id'=>$cat_id,
			  'item_id'=>$item->item_id,
			  'item_token'=>$item_token,
			  'item_size_id'=>$item_size_id,
			  'qty'=>$item_qty,
			  'special_instructions'=>$special_instructions,
			  'if_sold_out'=>$if_sold_out,
			  'addons'=>$addons,
			  'attributes'=>$attributes,
			  'inline_qty'=>$inline_qty,
			  'price'=>floatval($item->item_price),
			  'discount'=>$item->discount_valid>0?$item->discount:0,
			  'discount_type'=>$item->discount_valid>0?$item->discount_type:'',
			  'item_row'=>$item_row,
			  'old_item_token'=>$old_item_token,
			  'scenario'=>$scenario
			);	
			
						
			/*GET TAX*/
			$tax_settings = array(); $tax_use = array();
			try {
				$tax_settings = CTax::getSettings($merchant_id);							
				if($tax_settings['tax_type']=="multiple"){					
					$tax_use = CTax::getItemTaxUse($merchant_id,$item->item_id);
                } else $tax_use = isset($tax_settings['tax']) ? $tax_settings['tax'] : '';			   	
			} catch (Exception $e) {					
				 //echo $e->getMessage();
			}
			$items['tax_use'] = $tax_use;
			
			COrders::add($items);
			
			$this->code = 1 ; $this->msg = T("Item added to order");			
	        $this->details = array( 
	         'order_uuid'=>$order_uuid
	        );		 	        	        
			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $this->details = array(
		      'data'=>array()
		    );		    	   
		}
		$this->responseJson();		
	}
	
	public function actionupdateOrderDeliveryInformation()
	{
				
		try {
			
		   $order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
		   $customer_name = isset($this->data['customer_name'])?$this->data['customer_name']:'';
		   $contact_number = isset($this->data['contact_number'])?$this->data['contact_number']:'';
		   $delivery_address = isset($this->data['delivery_address'])?$this->data['delivery_address']:'';
		   $latitude = isset($this->data['latitude'])?$this->data['latitude']:'';
		   $longitude = isset($this->data['longitude'])?$this->data['longitude']:'';
		   		   
		   $model = COrders::get($order_uuid);
		   $order_type = $model->service_code;		   
		   
		   $error = array();
		   if(empty($customer_name)){
		   	  $error[] = t("Customer name is requied");
		   }
		   if(empty($contact_number)){
		   	  $error[] = t("Customer contact number is requied");
		   }
		   
		   switch ($order_type) {
		   	case "delivery":
		   	    if(empty($delivery_address)){
			   	   $error[] = t("Delivery address is requied");
			    }
			    if(empty($latitude)){
			   	   $error[] = t("Delivery coordinates is requied");
			    }
			    if(empty($longitude)){
			   	   $error[] = t("Delivery coordinates is requied");
			    }
		   		break;
		   
		   	default:
		   		break;
		   }
		   
		   if(is_array($error) && count($error)>=1){		   	  
		   	  $this->msg = "Error";
		   	  $this->details = $error;
		   	  $this->responseJson();
		   }
		   
		   COrders::savedAttributes($model->order_id,'customer_name',$customer_name);
		   COrders::savedAttributes($model->order_id,'contact_number',$contact_number);
		   COrders::savedAttributes($model->order_id,'formatted_address',$delivery_address);
		   COrders::savedAttributes($model->order_id,'latitude',$latitude);
		   COrders::savedAttributes($model->order_id,'longitude',$longitude);
		   
		   $model->formatted_address = $delivery_address;
		   $model->save();
		   
		   $this->code = 1;
		   $this->msg = t("Order Information updated");
		
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();	
	}
	
	public function actiongetCustomerDetails()
	{
		try {
					   
		   $client_id = isset($this->data['client_id'])?intval($this->data['client_id']):0;
		   //$merchant_id = Yii::app()->merchant->merchant_id;		   
		   $merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
		   		   
		   $addresses = array();		   
		   
		   if($data = COrders::getClientInfo($client_id)){
			   try {
			      $addresses = ACustomer::getAddresses($client_id);
			   } catch (Exception $e) {
			   	  //
			   }			   
			   $this->code = 1;
			   $this->msg = "OK";
			   $this->details = array(
			     'customer'=>$data,
			     'block_from_ordering'=>ACustomer::isBlockFromOrdering($client_id,$merchant_id),
			     'addresses'=>$addresses,
			   );		  		   
		   } else $this->msg = t("Client information not found");
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();	
	}
	
	public function actiongetCustomerOrders()
	{
		
		$data = array();		
		//$merchant_id = Yii::app()->merchant->merchant_id;
		$merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
		$client_id = isset($this->data['client_id'])?$this->data['client_id']:0;
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?$this->data['order'][0]:'';	
				
		$sortby = "order_id"; $sort = 'DESC';
		if(array_key_exists($order['column'],(array)$columns)){			
			$sort = $order['dir'];
			$sortby = $columns[$order['column']]['data'];
		}
		
		
		$page = intval($page)/intval($length);		
		
		$initial_status = AttributesTools::initialStatus();
		$status = COrders::statusList(Yii::app()->language);		
					
		$criteria=new CDbCriteria();	
		$criteria->alias = "a";
		$criteria->select="order_id,order_uuid,total,status";
		$criteria->condition = "merchant_id=:merchant_id AND client_id=:client_id ";
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),
		  ':client_id'=>intval($client_id)
		);
		$criteria->order = "$sortby $sort";
		
		if (is_string($search) && strlen($search) > 0){
		   $criteria->addSearchCondition('order_id', $search );
		   $criteria->addSearchCondition('status', $search , true , 'OR' );
		}
		$criteria->addNotInCondition('status', array($initial_status) );
				
		$count = AR_ordernew::model()->count($criteria); 
		$pages=new CPagination($count);
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_ordernew::model()->findAll($criteria);
        
$buttons = <<<HTML
<div class="btn-group btn-group-actions small" role="group">
 <a href="{{view_order}}" target="_blank" class="btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
 <a href="{{print_pdf}}" target="_blank"  class="btn btn-light tool_tips"><i class="zmdi zmdi-download"></i></a>
</div>
HTML;
        foreach ($models as $val) {        	        	
        	$status_html = $val->status;
        	if(array_key_exists($val->status,(array)$status)){
        		$new_status = $status[$val->status]['status'];
        		$inline_style="background:".$status[$val->status]['background_color_hex'].";";
        		$inline_style.="color:".$status[$val->status]['font_color_hex'].";";
        		$status_html = <<<HTML
<span class="badge" style="$inline_style" >$new_status</span>
HTML;
        	}
        	        	
        	$_buttons = str_replace("{{view_order}}",
        	Yii::app()->createUrl('/orders/view',array('order_uuid'=>$val->order_uuid))
        	,$buttons);
        	
        	$_buttons = str_replace("{{print_pdf}}",
        	Yii::app()->createUrl('/print/pdf',array('order_uuid'=>$val->order_uuid))
        	,$_buttons);
        	
        	$data[]=array(
        	 'order_id'=>$val->order_id,
        	 'total'=>Price_Formatter::formatNumber($val->total),
        	 'status'=>$status_html,
        	 'order_uuid'=>$_buttons
        	);
        }        
                
		$datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
		
		/*header('Content-type: application/json');
		echo CJSON::encode($datatables);
		Yii::app()->end();*/
		$this->responseTable($datatables);
	}
	
	public function actionblockCustomer()
	{
		try {
						
			$meta_name = 'block_customer';
						
			//$merchant_id = Yii::app()->merchant->merchant_id;
			$merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
			$client_id = isset($this->data['client_id'])?$this->data['client_id']:0;
			$block = isset($this->data['block'])?$this->data['block']:0;
			
			$model = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND 
			meta_name=:meta_name AND meta_value=:meta_value",array(
			 ':merchant_id'=>intval($merchant_id),
			 ':meta_name'=>$meta_name,
			 ':meta_value'=>$client_id
			));
			
			if($model){
				if($block!=1){
					$model->delete();
				}
			} else {				
				if($block==1){
					$model = new AR_merchant_meta;
					$model->merchant_id = $merchant_id;
					$model->meta_name = $meta_name;
					$model->meta_value = $client_id;
					$model->save();
				}
			}
			
			$this->code = 1;
			$this->msg = t("Successful");
			$this->details = intval($block);
			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		
		}	
		$this->responseJson();	
	}
	
	public function actiongetCustomerSummary()
	{
		try {		  
					    			
		    $client_id = isset($this->data['client_id'])?$this->data['client_id']:0;
		    //$merchant_id = Yii::app()->merchant->merchant_id;
		    $merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;

		    $initial_status = AttributesTools::initialStatus();			    
		    
		    $not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));		    
		    $orders = ACustomer::getOrdersTotal($client_id,$merchant_id,array(),$not_in_status);
		    
		    $status_cancel = AOrderSettings::getStatus(array('status_cancel_order'));		    
		    $order_cancel = ACustomer::getOrdersTotal($client_id,$merchant_id,$status_cancel);
		    
		    $status_delivered = AOrderSettings::getStatus(array('status_delivered'));	
		    $total = ACustomer::getOrderSummary($client_id,$merchant_id,$status_delivered);
		    $total_refund = ACustomer::getOrderRefundSummary($client_id,$merchant_id,AttributesTools::refundStatus());
		    		    
		    $data = array(
		     'orders'=>$orders,
		     'order_cancel'=>$order_cancel,
		     'total'=>Price_Formatter::formatNumberNoSymbol($total),
		     'total_refund'=>Price_Formatter::formatNumberNoSymbol($total_refund),
		     'price_format'=>array(
		       'symbol'=>Price_Formatter::$number_format['currency_symbol'],
		       'decimals'=>Price_Formatter::$number_format['decimals'],
		       'decimal_separator'=>Price_Formatter::$number_format['decimal_separator'],
		       'thousand_separator'=>Price_Formatter::$number_format['thousand_separator'],
		       'position'=>Price_Formatter::$number_format['position'],
		     )
		    );
		    
		    $this->code = 1;
		    $this->msg = "OK";
		    $this->details = $data;		    
		    
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		
		}	
		$this->responseJson();	
	}
	
	public function actiongetGroupname()
	{
		try {
						
			$group_name=''; $modify_order = false;	$filter_buttons = false;		
		    $order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			    
		    
		    $model = COrders::get($order_uuid);		    
		    $group_name = AOrderSettings::getGroup($model->status);		    
		    if($group_name=="new_order"){
				$modify_order = true;
			}
			if($group_name=="order_ready"){
				$filter_buttons = true;
			}
			
			$manual_status = isset(Yii::app()->params['settings']['enabled_manual_status'])?Yii::app()->params['settings']['enabled_manual_status']:false;
						
			$data = array(
			  'client_id'=>$model->client_id,
			  'merchant_id'=>$model->merchant_id,
			  'group_name'=>$group_name,
			  'manual_status'=>$manual_status,
			  'modify_order'=>$modify_order,
			  'filter_buttons'=>$filter_buttons
			);
						
			$this->code = 1;
			$this->msg = "OK";
			$this->details = $data;			

		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		
		}	
		$this->responseJson();		
	}
	
	public function actiongetOrdersCount()
	{
		try {
			
			$merchant_id = Yii::app()->merchant->merchant_id;
			$new_order = AOrders::getOrderTabsStatus('new_order');	
			$order_processing = AOrders::getOrderTabsStatus('order_processing');	
			$order_ready = AOrders::getOrderTabsStatus('order_ready');
			$completed_today = AOrders::getOrderTabsStatus('completed_today');			

			
			$status_scheduled = (array) $new_order;				
			
			if($order_processing){				
				foreach ($order_processing as $order_processing_val) {
					array_push($status_scheduled,$order_processing_val);
				}
			}
													
			$new = AOrders::getOrderCountPerStatus($merchant_id,$new_order,date("Y-m-d"));
			$processing = AOrders::getOrderCountPerStatus($merchant_id,$order_processing,date("Y-m-d"));
			$ready = AOrders::getOrderCountPerStatus($merchant_id,$order_ready,date("Y-m-d"));
			$completed = AOrders::getOrderCountPerStatus($merchant_id,$completed_today,date("Y-m-d"));
			$scheduled = AOrders::getOrderCountSchedule($merchant_id,$status_scheduled,date("Y-m-d"));
			$all_orders = AOrders::getAllOrderCount($merchant_id);
			
			$not_viewed = AOrders::OrderNotViewed($merchant_id,$new_order,date("Y-m-d"));			
			
			$data = array(
			  'new_order'=>$new,
			  'order_processing'=>$processing,
			  'order_ready'=>$ready,
			  'completed_today'=>$completed,
			  'scheduled'=>$scheduled,
			  'all_orders'=>$all_orders,
			  'not_viewed'=>$not_viewed,
			);
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = $data;
					
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		
		}	
		$this->responseJson();		
	}
	
	public function actiontransactionHistory()
	{
		$data = array(); $card_id = 0;	
		try {	
		    $card_id = CWallet::getCardID(Yii::app()->params->account_type['merchant'],Yii::app()->merchant->merchant_id);	
		} catch (Exception $e) {
		    // do nothing    
		}	
				
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
				
		$sortby = "transaction_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();
		$criteria->condition = "card_id=:card_id";
		$criteria->params  = array(
		  ':card_id'=>intval($card_id),		  
		);
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(transaction_date,'%Y-%m-%d')", $date_start , $date_end );
		}
		if(is_array($transaction_type) && count($transaction_type)>=1){
			$criteria->addInCondition('transaction_type',(array) $transaction_type );
		}		
		
		$criteria->order = "$sortby $sort";
		$count = AR_wallet_transactions::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_wallet_transactions::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {
        		$description = Yii::app()->input->xssClean($item->transaction_description);        		
        		$parameters = json_decode($item->transaction_description_parameters,true);        		
        		if(is_array($parameters) && count($parameters)>=1){        			
        			$description = t($description,$parameters);
        		}
        		
        		$transaction_amount = Price_Formatter::formatNumber($item->transaction_amount);        		
        		switch ($item->transaction_type) {
        			case "debit":
        			case "payout":
        				$transaction_amount = "(".Price_Formatter::formatNumber($item->transaction_amount).")";
        				break;        		        			
        		}
        		
        		
$trans_html = <<<HTML
<p class="m-0 $item->transaction_type">$transaction_amount</p>
HTML;
        		
        		
        		$data[]=array(
        		  'transaction_date'=>Date_Formatter::date($item->transaction_date),
        		  'transaction_description'=>$description,
        		  'transaction_amount'=>$trans_html,
        		  'running_balance'=>Price_Formatter::formatNumber($item->running_balance),
        		);
        	}
        }
        
        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
		
		/*header('Content-type: application/json');
		echo CJSON::encode($datatables);
		Yii::app()->end();*/
		$this->responseTable($datatables);
	}		
	
	public function actiongetGetMerchantBalance()
	{
		try {									
			$card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] , Yii::app()->merchant->merchant_id );
			$balance = CWallet::getBalance($card_id);
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $balance = 0;		
		}	
		
		$this->code = 1;
		$this->msg = "OK";
		$this->details = array(
		  'balance'=>Price_Formatter::formatNumberNoSymbol($balance),
		  'price_format'=>array(
	         'symbol'=>Price_Formatter::$number_format['currency_symbol'],
	         'decimals'=>Price_Formatter::$number_format['decimals'],
	         'decimal_separator'=>Price_Formatter::$number_format['decimal_separator'],
	         'thousand_separator'=>Price_Formatter::$number_format['thousand_separator'],
	         'position'=>Price_Formatter::$number_format['position'],
	      )
		);
		
		$this->responseJson();		
    }
    
    public function actionwithdrawalsHistory()
    {
    	$data = array();		
				
		$merchant_id = Yii::app()->merchant->merchant_id;
		$card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] ,$merchant_id);
		
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
				
		$sortby = "transaction_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();
		$criteria->condition = "card_id=:card_id  AND transaction_type=:transaction_type";
		$criteria->params  = array(
		  ':card_id'=>intval($card_id),
		  ':transaction_type'=>"payout"
		);
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(transaction_date,'%Y-%m-%d')", $date_start , $date_end );
		}
		
		$status_trans = AttributesTools::statusManagementTranslationList('payment', Yii::app()->language );
		
		$criteria->order = "$sortby $sort";
		$count = AR_wallet_transactions::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_wallet_transactions::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {
        		$description = Yii::app()->input->xssClean($item->transaction_description);        		
        		$parameters = json_decode($item->transaction_description_parameters,true);        		
        		if(is_array($parameters) && count($parameters)>=1){        			
        			$description = t($description,$parameters);
        		}
        		
        		$transaction_amount = Price_Formatter::formatNumber($item->transaction_amount);
        		if($item->transaction_type=="debit"){
        			$transaction_amount = "(".Price_Formatter::formatNumber($item->transaction_amount).")";
        		}
        		
        		$trans_status = $item->status;
        		if(array_key_exists($item->status,(array)$status_trans)){
        			$trans_status = $status_trans[$item->status];
                }
        		$description = '<p class="m-0">'. $description .'</p>';
        		$description.= '<div class="badge payment '.$item->status.'">'.$trans_status.'</div>';
        		
        		$data[]=array(
        		  'transaction_amount'=>$transaction_amount,
        		  'transaction_description'=>$description,
        		  'transaction_date'=>Date_Formatter::date($item->transaction_date),          		  
        		);
        	}
        }
        
        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
		
		/*header('Content-type: application/json');
		echo CJSON::encode($datatables);
		Yii::app()->end();   	*/
		$this->responseTable($datatables);
    }
    
    public function actiongetPayoutSettings()
    {
   
		try {			
			$provider = AttributesTools::PaymentPayoutProvider();
			$country_list = AttributesTools::CountryList();
			$currency_list = AttributesTools::currencyListSelection();
			
			$account_type['individual'] = t("Individual");
			$account_type['company'] = t("Company");
			
			$default_currency = AttributesTools::defaultCurrency();		
			$default_country = OptionsTools::find(array('admin_country_set'));
			
			$data = array(
			  'provider'=>$provider,
			  'country_list'=>$country_list,
			  'account_type'=>$account_type,
			  'currency_list'=>$currency_list,
			  'default_currency'=>$default_currency,
			  'default_country'=>isset($default_country['admin_country_set'])?$default_country['admin_country_set']:'',
			);
			$this->code = 1;
		    $this->msg = "OK";
		    $this->details = $data;
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();		
    }
    
    public function actionSetPayoutAccount()
    {
    	try {
    		
    		$merchant_id = Yii::app()->merchant->merchant_id;
    		$meta_name = 'payout_provider';
    			    	
	    	$payment_provider = isset($this->data['payment_provider'])?$this->data['payment_provider']:'';
	    	$email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
	    	
	    	$model = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name",array(
	    	  ':merchant_id'=>intval($merchant_id),
	    	  ':meta_name'=>$meta_name,	    	  
	    	));
	    	if(!$model){
	    	    $model = new AR_merchant_meta;	
	    	}	    	
	    	
	    	$model->merchant_id = intval($merchant_id);
	    	$model->meta_name = $meta_name;
	    	$model->meta_value = $payment_provider;
	    		    	
	    	switch ($payment_provider) {
	    		case "paypal":	    
	    		    if(!empty($email_address)){
	    			$model->meta_value1 = $email_address;
	    		    } else {
	    		    	$this->msg[] = t("Invalid email address");
	    		    	$this->responseJson();
	    		    }
	    			break;
	    			
	    		case "stripe":	    	    		    		
	    		   $account_number  = isset($this->data['account_number'])?$this->data['account_number']:'';
	    		   if(empty($account_number)){
	    		   	  $this->msg[] = t("Account number is required");	    		      
	    		   }
	    		   $account_holder_name  = isset($this->data['account_holder_name'])?$this->data['account_holder_name']:'';
	    		   if(empty($account_holder_name)){
	    		   	  $this->msg[] = t("Account name is required");	    		      
	    		   }
	    		   
	    		   if(is_array($this->msg) && count($this->msg)>=1){
	    		   	  $this->responseJson();
	    		   }
	    		   
	    		   $model->meta_value1  = json_encode(array(	    		     
	    		     'account_number'=>isset($this->data['account_number'])?$this->data['account_number']:'',
	    		     'account_holder_name'=>isset($this->data['account_holder_name'])?$this->data['account_holder_name']:'',
	    		     'account_holder_type'=>isset($this->data['account_holder_type'])?$this->data['account_holder_type']:'',
	    		     'currency'=>isset($this->data['currency'])?$this->data['currency']:'',
	    		     'routing_number'=>isset($this->data['routing_number'])?$this->data['routing_number']:'',
	    		     'country'=>isset($this->data['country'])?$this->data['country']:'',
	    		   ));
	    		   break;
	    		   
	    		case "bank":	  
	    		   $account_number_iban  = isset($this->data['account_number_iban'])?$this->data['account_number_iban']:'';
	    		   if(empty($account_number_iban)){
	    		   	  $this->msg[] = t("Account number is required");	    		      
	    		   }  	    		    		
	    		   $account_name  = isset($this->data['account_name'])?$this->data['account_name']:'';
	    		   if(empty($account_name)){
	    		   	  $this->msg[] = t("Account name is required");	    		      
	    		   }  	    		    		
	    		   /*$full_name  = isset($this->data['full_name'])?$this->data['full_name']:'';
	    		   if(empty($full_name)){
	    		   	  $this->msg[] = t("Full name is required");	    		      
	    		   } */
	    		   /*$billing_address1  = isset($this->data['billing_address1'])?$this->data['billing_address1']:'';
	    		   if(empty($billing_address1)){
	    		   	  $this->msg[] = t("Billing address is required");	    		      
	    		   } */
	    		   $bank_name  = isset($this->data['bank_name'])?$this->data['bank_name']:'';
	    		   if(empty($bank_name)){
	    		   	  $this->msg[] = t("Bank name is required");	    		      
	    		   } 
	    		   $swift_code  = isset($this->data['swift_code'])?$this->data['swift_code']:'';
	    		   if(empty($swift_code)){
	    		   	  $this->msg[] = t("Swift code is required");	    		      
	    		   } 
	    		   $country  = isset($this->data['country'])?$this->data['country']:'';
	    		   if(empty($swift_code)){
	    		   	  $this->msg[] = t("Country is required");	    		      
	    		   } 
	    		   
	    		   if(is_array($this->msg) && count($this->msg)>=1){
	    		   	  $this->responseJson();
	    		   }
	    		   
	    		   $model->meta_value1  = json_encode(array(
	    		     /*'full_name'=>isset($this->data['full_name'])?$this->data['full_name']:'',
	    		     'billing_address1'=>isset($this->data['billing_address1'])?$this->data['billing_address1']:'',
	    		     'billing_address2'=>isset($this->data['billing_address2'])?$this->data['billing_address2']:'',
	    		     'city'=>isset($this->data['city'])?$this->data['city']:'',
	    		     'state'=>isset($this->data['state'])?$this->data['state']:'',
	    		     'post_code'=>isset($this->data['post_code'])?$this->data['post_code']:'',
	    		     'country'=>isset($this->data['country'])?$this->data['country']:'',*/
	    		     'account_name'=>isset($this->data['account_name'])?$this->data['account_name']:'',
	    		     'account_number_iban'=>isset($this->data['account_number_iban'])?$this->data['account_number_iban']:'',
	    		     'swift_code'=>isset($this->data['swift_code'])?$this->data['swift_code']:'',
	    		     'bank_name'=>isset($this->data['bank_name'])?$this->data['bank_name']:'',
	    		     'bank_branch'=>isset($this->data['bank_branch'])?$this->data['bank_branch']:'',
	    		   ));
	    		   break;   
	    	}
	    	
	    	if($model->save()){
	    		$this->code = 1; $this->msg = t("Payout account saved");	    		
	    	} else $this->msg = CommonUtility::parseError( $model->getErrors());
	    	
    	} catch (Exception $e) {
		   $this->msg[] = t($e->getMessage());		
		}	
		$this->responseJson();		
    }
    
    public function actiongetPayoutAccount()
    {
       try {
       	
       	  $merchant_id = Yii::app()->merchant->merchant_id;
       	  $account = CPayouts::getPayoutAccont($merchant_id);
       	  $this->code = 1; $this->msg = "OK";
       	  $this->details = $account;
       	  
       } catch (Exception $e) {
		   $this->msg[] = t($e->getMessage());		
	   }	
	   $this->responseJson();		
    }
    
    public function actionrequestPayout()
    {
    	try {
    		        	   
    	   $account = array();
    	   $merchant_id = Yii::app()->merchant->merchant_id;
    	   $amount = isset($this->data['amount'])?floatval($this->data['amount']):0;  

		   if(DEMO_MODE){
			   if($amount>10){
                  $this->msg[] = t("Maximum amount of payout in demo is 10");
				  $this->responseJson();
			   }
		   }
    	       	   
    	   $accounts = CPayouts::getPayoutAccont($merchant_id);    	       	   
    	   $account = isset($accounts['account'])?$accounts['account']:'';
    	       	   
    	   //$transaction_id = CMerchantEarnings::requestPayout($merchant_id,$amount , $account , $accounts );    	   
    	   
    	   $card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] , $merchant_id);
    	   $transaction_id = CEarnings::requestPayout($card_id,$amount , $account , $accounts );
    	   
    	   $params = array();
    	   foreach ($accounts as $itemkey=>$item) {    	   	  
    	   	  $params[]=array(
    	   	    'transaction_id'=>intval($transaction_id),
    	   	    'meta_name'=>$itemkey,
    	   	    'meta_value'=>$item,
    	   	    'date_created'=>CommonUtility::dateNow(),
    	   	    'ip_address'=>CommonUtility::userIp(),
    	   	  );
    	   }    	   
    	   $builder=Yii::app()->db->schema->commandBuilder;
		   //$command=$builder->createMultipleInsertCommand('{{merchant_earnings_meta}}', $params );
		   $command=$builder->createMultipleInsertCommand('{{wallet_transactions_meta}}', $params );
		   $command->execute();
    	   
    	   $this->code = 1; $this->msg = t("Payout request successfully logged");
    	   $this->details = $transaction_id;
    	   
    	} catch (Exception $e) {
		   $this->msg[] = t($e->getMessage());		   
	   }	
	   $this->responseJson();		
    }

    public function actionorderHistory()
    {
    	$data = array();		
    	$status = COrders::statusList(Yii::app()->language);    	
    	$services = COrders::servicesList(Yii::app()->language);
    	$payment_list = AttributesTools::PaymentProvider();	
    	    	
		$merchant_id = Yii::app()->merchant->merchant_id;
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = (isset($this->data['order']) && isset($this->data['order'][0]))?$this->data['order'][0]:'';	
		$filter = isset($this->data['filter'])?$this->data['filter']:'';
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
				
		$sortby = "order_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = $page>0? intval($page)/intval($length) : 0;	
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "a.order_id, a.client_id, a.status, a.order_uuid , 
		a.payment_code, a.service_code,a.total, a.date_created,
		b.meta_value as customer_name, 
		(
		   select sum(qty)
		   from {{ordernew_item}}
		   where order_id = a.order_id
		) as total_items,
		
		c.avatar as logo, c.path
		";
		$criteria->join='
		LEFT JOIN {{ordernew_meta}} b on  a.order_id=b.order_id 
		LEFT JOIN {{client}} c on  a.client_id = c.client_id 
		';
		$criteria->condition = "a.merchant_id=:merchant_id AND meta_name=:meta_name ";
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),		  
		  ':meta_name'=>'customer_name'
		);
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(a.date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		$initial_status = AttributesTools::initialStatus();
		$criteria->addNotInCondition('a.status', (array) array($initial_status) );
		$pos_code = AttributesTools::PosCode();	
		$criteria->addNotInCondition('a.service_code', array($pos_code) );
		
		if(is_array($filter) && count($filter)>=1){
		    $filter_order_status = isset($filter['order_status'])?$filter['order_status']:'';
		    $filter_order_type = isset($filter['order_type'])?$filter['order_type']:'';
		    $filter_client_id = isset($filter['client_id'])?intval($filter['client_id']):'';
		    
			if(!empty($filter_order_status)){
				$criteria->addSearchCondition('a.status', $filter_order_status );
			}
			if(!empty($filter_order_type)){
				$criteria->addSearchCondition('a.service_code', $filter_order_type );
			}
			if($filter_client_id>0){
				$criteria->addSearchCondition('a.client_id', intval($filter_client_id) );
			}
		}
				
		$criteria->order = "$sortby $sort";
		$count = AR_ordernew::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
                
        $models = AR_ordernew::model()->findAll($criteria);
                
        if($models){
         	foreach ($models as $item) {         		

         	$item->total_items = intval($item->total_items);
         	$item->total_items = t("{{total_items}} items",array(
         	 '{{total_items}}'=>$item->total_items
         	));
         	
         	$trans_order_type = $item->service_code;
         	if(array_key_exists($item->service_code,$services)){
         		$trans_order_type = $services[$item->service_code]['service_name'];
         	}
         	
         	$order_type = t("Order Type.");
         	$order_type.="<span class='ml-2 services badge $item->service_code'>$trans_order_type</span>";
         	
         	$total = t("Total. {{total}}",array(
         	 '{{total}}'=>Price_Formatter::formatNumber($item->total)
         	));
         	$place_on = t("Place on {{date}}",array(
         	 '{{date}}'=>Date_Formatter::dateTime($item->date_created)
         	));
         	
         	$status_trans = $item->status;
         	if(array_key_exists($item->status, (array) $status)){
         		$status_trans = $status[$item->status]['status'];
         	}
         	
         	$view_order = Yii::app()->createUrl('orders/view',array(
         	  'order_uuid'=>$item->order_uuid
         	));
         	
         	$print_pdf = Yii::app()->createUrl('print/pdf',array(
         	  'order_uuid'=>$item->order_uuid
         	));
         	
         	$status_class = str_replace(" ","_",$item->status);
         	         	
         	if(array_key_exists($item->payment_code,(array)$payment_list)){
	            $item->payment_code = $payment_list[$item->payment_code];
	        }
			        
	        $avatar = CMedia::getImage($item->logo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('customer'));
		         
         		
$information = <<<HTML
$item->total_items<span class="ml-2 badge order_status $status_class">$status_trans</span>
<p class="dim m-0">$item->payment_code</p>
<p class="dim m-0">$order_type</p>
<p class="dim m-0">$total</p>
<p class="dim m-0">$place_on</p>
HTML;

$buttons = <<<HTML
<div class="btn-group btn-group-actions" role="group">
 <a href="$view_order" target="_blank" class="btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
 <a href="$print_pdf" target="_blank"  class="btn btn-light tool_tips"><i class="zmdi zmdi-download"></i></a>
</div>
HTML;

         		$data[]=array(
         		  'logo'=>'<img class="img-60 rounded-circle" src="'.$avatar.'">',
        		  'order_id'=>$item->order_id,
        		  'client_id'=>$item->customer_name,
        		  'status'=>$information,
        		  'order_uuid'=>$buttons
        		);
         	}
        }	
        
        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
		$this->responseTable($datatables);		
    }
    
    public function actiongetFilterData()
    {
    	try {
    	   
    		$data = array(
    		   'status_list'=>AttributesTools::getOrderStatus(Yii::app()->language),
    		   'order_type_list'=>AttributesTools::ListSelectServices(),
    		);
    		$this->code = 1; $this->msg = "OK";
    		$this->details = $data;
    		
    	} catch (Exception $e) {
		   $this->msg[] = t($e->getMessage());		   
	    }	
	    $this->responseJson();		
    }
    
    public function actionsearchCustomer()
    {     	 
    	 $search = isset($this->data['search'])?$this->data['search']:''; 
    	 $is_pos = isset($this->data['POS'])?$this->data['POS']:false;
    	 $is_pos = $is_pos==1?true:false;
    	     	 
    	 if($is_pos && empty($search)){
    	 	$data[] = array(
    	 	  'id'=>"walkin",
    	 	  'text'=>t("Walk-in Customer")
    	 	);
    	 } else $data = array();    	 
    	 
    	 $criteria=new CDbCriteria();
    	 $criteria->select = "client_id,first_name,last_name";
    	 $criteria->condition = "status=:status";
    	 $criteria->params = array(
    	   ':status'=>'active'
    	 );
    	 if(!empty($search)){
			$criteria->addSearchCondition('first_name', $search );
			$criteria->addSearchCondition('last_name', $search , true , 'OR' );
		 }
		 $criteria->limit = 10;
		 if($models = AR_client::model()->findAll($criteria)){		 	
		 	foreach ($models as $val) {
		 		$data[]=array(
				  'id'=>$val->client_id,
				  'text'=>$val->first_name." ".$val->last_name
				);
		 	}
		 }
		 
		$result = array(
    	  'results'=>$data
    	);	       	
    	$this->responseSelect2($result);
    }
    
    public function actiongetordersummary()
    {    	
    	try {	
	    	$merchant_id = Yii::app()->merchant->merchant_id;
	    	$initial_status = AttributesTools::initialStatus();
	    	$refund_status = AttributesTools::refundStatus();	
	    	$orders = 0; $order_cancel = 0; $total=0;
	    	
	    	$not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));
	    	array_push($not_in_status,$initial_status);    		    	
	    	$orders = AOrders::getOrdersTotal($merchant_id,array(),$not_in_status);
	    	
	    	$status_cancel = AOrderSettings::getStatus(array('status_cancel_order'));		    	    	
		    $order_cancel = AOrders::getOrdersTotal($merchant_id,$status_cancel);
		    
		    $status_delivered = AOrderSettings::getStatus(array('status_delivered','status_completed'));
						
		    $total = AOrders::getOrderSummary($merchant_id,$status_delivered);
		    $total_refund = AOrders::getTotalRefund($merchant_id,$refund_status);
	    	
	    	$data = array(
		     'orders'=>$orders,
		     'order_cancel'=>$order_cancel,
		     'total'=>Price_Formatter::formatNumberNoSymbol($total),
		     'total_refund'=>Price_Formatter::formatNumberNoSymbol($total_refund),
		     'price_format'=>array(
		       'symbol'=>Price_Formatter::$number_format['currency_symbol'],
		       'decimals'=>Price_Formatter::$number_format['decimals'],
		       'decimal_separator'=>Price_Formatter::$number_format['decimal_separator'],
		       'thousand_separator'=>Price_Formatter::$number_format['thousand_separator'],
		       'position'=>Price_Formatter::$number_format['position'],
		     )
		    );
		    
		    $this->code = 1;
			$this->msg = "OK";
			$this->details = $data;		    
		    
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		
		}	
		$this->responseJson();	
    	
    }
       
    public function actiongetOrderFilterSettings()
    {
    	try {
    	
    	    $data = array(
    		   'status_list'=>AttributesTools::getOrderStatus(Yii::app()->language),
    		   'order_type_list'=>AttributesTools::ListSelectServices(),
    		   'payment_status_list'=>AttributesTools::statusManagementTranslationList('payment',Yii::app()->language),
    		   'sort_list'=>AttributesTools::orderSortList()
    		);    		
    		$this->code = 1; $this->msg = "OK";
    		$this->details = $data;
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		
		}	
		$this->responseJson();		
    }
    
    public function actiongetWebpushSettings()
	{
		try {						
						
			$settings = AR_admin_meta::getMeta(array('webpush_app_enabled','webpush_provider','pusher_instance_id','onesignal_app_id'
			));		
						
			$enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';
			$provider = isset($settings['webpush_provider'])?$settings['webpush_provider']['meta_value']:'';
			$pusher_instance_id = isset($settings['pusher_instance_id'])?$settings['pusher_instance_id']['meta_value']:'';			
			$onesignal_app_id = isset($settings['onesignal_app_id'])?$settings['onesignal_app_id']['meta_value']:'';	
			
			$user_settings = array();
			
			try {
			   $user_settings = CNotificationData::getUserSettings(Yii::app()->merchant->id,'merchant');		
			} catch (Exception $e) {
			   //
			}
			
			$data = array(
			  'enabled'=>$enabled,
			  'provider'=>$provider,
			  'pusher_instance_id'=>$pusher_instance_id,			  
			  'onesignal_app_id'=>$onesignal_app_id,
			  'safari_web_id'=>'',
			  'channel'=>Yii::app()->merchant->id,
			  'user_settings'=>$user_settings,
			);			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = $data;
						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
	
    public function actiongetNotifications()
	{
		try {								
			$data = CNotificationData::getList( Yii::app()->merchant->merchant_uuid );			
			$this->code = 1; $this->msg = "ok";
			$this->details = $data;
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
    
	public function actionclearNotifications()
	{
		try {						
						
			AR_notifications::model()->deleteAll('notication_channel=:notication_channel',array(
			 ':notication_channel'=> Yii::app()->merchant->merchant_uuid
			));
			$this->code = 1; $this->msg = "ok";
						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
	
	public function actiongetwebnotifications()
	{
		try {
						
			$data = CNotificationData::getUserSettings(Yii::app()->merchant->id,'merchant');
			$this->code = 1;
		    $this->msg = "OK";
		    $this->details = $data;
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
	
	public function actionsavewebnotifications()
	{
		try {		
					    			
		    $webpush_enabled = isset($this->data['webpush_enabled'])?intval($this->data['webpush_enabled']):0;	
		    $interest = isset($this->data['interest'])?$this->data['interest']:'';
		    $device_id = isset($this->data['device_id'])?$this->data['device_id']:'';
		    		    
		    $model = AR_device::model()->find("user_id=:user_id AND user_type=:user_type",array(
		      ':user_id'=>intval(Yii::app()->merchant->id),
		      ':user_type'=>"merchant"
		    ));
		    if(!$model){
		       $model = new AR_device;			       
		    } 		    		    
		    $model->interest = array(Yii::app()->merchant->merchant_uuid);
		    $model->user_type = 'merchant';
	    	$model->user_id = intval(Yii::app()->merchant->id);
	    	$model->platform = "web";
	    	$model->device_token = $device_id;
	    	$model->browser_agent = $_SERVER['HTTP_USER_AGENT'];
	    	$model->enabled = $webpush_enabled;
	    	if($model->save()){
		   	   $this->code = 1;
			   $this->msg = t("Setting saved");		    
		    } else $this->msg = CommonUtility::parseError( $model->getErrors());
		    		   		    		    
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
	}	
	
	public function actionMerchantOrderingStatus()
	{		
		try {
						
			$data = AR_merchant_meta::getMeta(Yii::app()->merchant->merchant_id,array(
			 'accepting_order','pause_time','pause_interval'
			));
			$accepting_order = isset($data['accepting_order'])?$data['accepting_order']['meta_value']:true;
			$accepting_order = $accepting_order==1?true:false;
			$pause_time = isset($data['pause_time'])?trim($data['pause_time']['meta_value']):'';			
			
			if(!$accepting_order){							
				$pause_time = Date_Formatter::dateTime($pause_time,"yyyy-MM-ddTHH:mm",true);
			} else $pause_time = Date_Formatter::dateTime(date("c"),"yyyy-MM-ddTHH:mm",true);
		
			$this->code = 1; $this->msg = "ok";
			$this->details = array(
			  'accepting_order'=>$accepting_order,
			  'pause_time'=>$pause_time,			  
			);					
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
    }
    
    public function actiongetPauseOptions()
    {
    	try {
    		
    		 $times = AttributesTools::delayedMinutes();
    		 $pause_reason = AOrders::rejectionList('pause_reason');
    		 
    		 $array = array(
    		  'id'=>"other",
    		  'value'=>t("Other")
    		 );
    		 array_push($times,$array);
    		     		 
    		 $this->code = 1;
    		 $this->msg = "ok";
    		 $this->details = array(
    		   'times'=>$times,
    		   'pause_reason'=>$pause_reason
    		 );    		 
    		     		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
    }
    
    public function actionsetPauseOrder()
    {
    	try {    		
    		
    		$now = time(); $pause_time=0;
    		$time_delay = isset($this->data['time_delay'])?$this->data['time_delay']:0;
    		$pause_hours = isset($this->data['pause_hours'])?intval($this->data['pause_hours']):0;
    		$pause_minutes = isset($this->data['pause_minutes'])?intval($this->data['pause_minutes']):0;
    		$reason = isset($this->data['reason'])?$this->data['reason']:'';    	
    		
    		if($time_delay=="other"){    			
    			$pause_time = date('Y-m-d H:i:s',strtotime("+$pause_hours hour +$pause_minutes minutes",$now));
    		} else {
    			$time_delay = intval($time_delay);     			
    		    $pause_time = date("Y-m-d H:i:s", strtotime("+$time_delay minutes", $now));
    		}
    		    		
    		    	
    		AR_merchant_meta::saveMeta(Yii::app()->merchant->merchant_id,'pause_time',$pause_time);
    		AR_merchant_meta::saveMeta(Yii::app()->merchant->merchant_id,'pause_reason',$reason);
    		AR_merchant_meta::saveMeta(Yii::app()->merchant->merchant_id,'accepting_order',false);
    		
    		try {
    		   $merchant = CMerchants::get(Yii::app()->merchant->merchant_id);
    		   $merchant->pause_ordering = true;    		   
    		   $merchant->save();
    		} catch (Exception $e) {
    		   //	
            }
    		
    		$pause_time = Date_Formatter::dateTime($pause_time,"yyyy-MM-ddTHH:mm",true);
    		
    		$this->code = 1;
    		$this->msg = "ok";
    		$this->details = array(
    		  'pause_time'=>$pause_time,
    		  'accepting_order'=>false,
    		);
    		    		    	
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
    }
    
    public function actionUpdateOrderingStatus()
    {
    	try {    		
    		
    		$accepting_order = isset($this->data['accepting_order'])?$this->data['accepting_order']:false;
			$accepting_order = $accepting_order==1?true:false;
			AR_merchant_meta::saveMeta(Yii::app()->merchant->merchant_id,'accepting_order',$accepting_order);
			AR_merchant_meta::saveMeta(Yii::app()->merchant->merchant_id,'pause_time','');
    		AR_merchant_meta::saveMeta(Yii::app()->merchant->merchant_id,'pause_reason','');
    		
    		try {
    		   $merchant = CMerchants::get(Yii::app()->merchant->merchant_id);
    		   $merchant->pause_ordering = false;    		   
    		   $merchant->save();
    		} catch (Exception $e) {
    		   //	
            }
    		
			$this->code = 1;
    		$this->msg = "ok";
    		$this->details = array(
    		   'pause_time'=>'',
    		   'accepting_order'=>$accepting_order
    		);
			
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
    }
	
	public function actionallNotifications()
	{
		$data = array();		
						
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
				
		$sortby = "date_created"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();
		$criteria->condition="notication_channel=:notication_channel";
		$criteria->params = array(':notication_channel'=> Yii::app()->merchant->merchant_uuid );
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		
		$criteria->order = "$sortby $sort";
		$count = AR_notifications::model()->count($criteria); 
		
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_notifications::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {
        		
        		$params = !empty($item->message_parameters)?json_decode($item->message_parameters,true):'';        		
        		$data[]=array(				 
        		  'date_created'=>Date_Formatter::dateTime($item->date_created),
				  'message'=>t($item->message,(array)$params),				  
				);
        	}        	
        }
        
         $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);				
		$this->responseTable($datatables);
	}
	
	public function actiondefaultPaymentGateway()
	{
		try {
			$merchant_id = Yii::app()->merchant->merchant_id;
			$payment_code = '';
			$meta = AR_merchant_meta::getValue($merchant_id,'subscription_payment_method');
			/*if($meta){				
			} else $this->msg = t("Payment code not set");*/
			$payment_code = isset($meta['meta_value'])?$meta['meta_value']:'';	
			$payment_code = !empty($payment_code)?$payment_code:'stripe';
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'payment_code'=>$payment_code
			);
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		
		}
		$this->responseJson();	
    }
    
	public function actiongetmerchantplan()
	{
		try {
			
			$merchant_id = Yii::app()->merchant->merchant_id;
			$merchant = CMerchants::get($merchant_id);
			$plans = Cplans::get($merchant->package_id);
			$this->code = 1;
			$this->msg = "ok";
			$this->details = array(
			 'plan_title'=>Yii::app()->input->xssClean($plans->title)
			);
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
    }
    
    public function actionplanInvoiceList()
    {
    
    	$data = array();
    	$merchant_id = Yii::app()->merchant->merchant_id;
    	
    	$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
				
		$sortby = "created"; $sort = 'DESC';
    		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select="a.invoice_number,a.invoice_ref_number,a.created,a.amount,a.status,a.payment_code,b.title";
		$criteria->join='LEFT JOIN {{plans_translation}} b on  a.package_id=b.package_id ';
				
		$criteria->addCondition('a.merchant_id=:merchant_id AND b.language=:language');
		$criteria->params = array( 
		  ':merchant_id'=>intval($merchant_id),
		  ':language'=>Yii::app()->language
		);
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(a.created,'%Y-%m-%d')", $date_start , $date_end );
		}
		
		$criteria->order = "$sortby $sort";
		$count = AR_plans_invoice::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_plans_invoice::model()->findAll($criteria);        
        if($models){
        	foreach ($models as $item) {
        		$data[]=array(
        		  'payment_code'=>$item->payment_code,
        		  'invoice_number'=>$item->invoice_number,
        		  'invoice_ref_number'=>$item->invoice_ref_number,
        		  'created'=>Date_Formatter::date($item->created),
        		  'package_id'=>Yii::app()->input->xssClean($item->title),
        		  'amount'=>Price_Formatter::formatNumber($item->amount),
        		  'status'=>'<span class="badge payment '.$item->status.' ">'.$item->status.'</span>',
        		);
        	}
        }
        
        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
        
        $this->responseTable($datatables);
    }
    
    public function actiongetPlanList()
    {
    	try {
			
			$details = array(); $package_uuid = ''; $payment_code ='';
			$merchant_id = Yii::app()->merchant->merchant_id;			
			$merhant = CMerchants::get($merchant_id);		
			
			try {
			   $plan = Cplans::get($merhant->package_id);			
			   $package_uuid = $plan->package_uuid;
			} catch (Exception $e) {
				//
			}
			
			$data = CPlan::listing( Yii::app()->language , true);		
			
			$meta = AR_merchant_meta::getValue($merchant_id,'subscription_payment_method');
			if($meta){
				$payment_code = isset($meta['meta_value'])?$meta['meta_value']:'';
			}
			
			/*try {
			    $details = CPlan::Details();		
			} catch (Exception $e) {
				
			}*/
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(			
			  'data'=>$data,
			  'package_uuid'=>$package_uuid,
			  'payment_code'=>!empty($payment_code)?$payment_code:'stripe',
			  //'plan_details'=>$details,			  
			);										
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		
		}
		$this->responseJson();	
    }
    
    public function actiongetplandetails()
    {
    	try {
    		    	   
    	   $package_uuid = isset($this->data['package_uuid'])?$this->data['package_uuid']:'';
    	   $plans = Cplans::getByUUID($package_uuid);	    	   
    	   $data = array(
    	     'title'=>Yii::app()->input->xssClean($plans->title),
    	     'price'=> Price_Formatter::formatNumber($plans->price),
    	     'price_raw'=>$plans->price,
    	     'promo_price'=> Price_Formatter::formatNumber($plans->promo_price),
    	     'promo_price_raw'=>$plans->promo_price,
    	     'package_period'=>$plans->package_period,
    	   );
    	   $this->code = 1;
    	   $this->msg = "OK";
    	   $this->details = $data;
    	   
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		
		}
		$this->responseJson();	
    }
    
    public function actionPaymenPlanList()
	{
		 try {
		 	
		 	$payment_list = AttributesTools::PaymentPlansProvider(); 
		 	$this->code = 1;
		 	$this->msg = "ok";
		 	$this->details = $payment_list; 
		 	
		 } catch (Exception $e) {
		    $this->msg = t($e->getMessage());		
		 }
		 $this->responseJson();	
	}
	
	public function actionmerchantPlanStatus()
	{
		try {
			
			$merchant_id = Yii::app()->merchant->merchant_id;
			
			$merchant = CMerchants::get($merchant_id);
			$status_list = AttributesTools::statusManagementTranslationList('customer',Yii::app()->language);			
			$data = array(
			  'restaurant_name'=>Yii::app()->input->xssClean($merchant->restaurant_name),			  
			  'status'=>isset($status_list[$merchant->status])?$status_list[$merchant->status]:$merchant->status,
			  'status_raw'=>$merchant->status,
			);			
			Yii::app()->merchant->setState("status",$merchant->status);
			$this->code = 1;
			$this->msg = "ok";
			$this->details = $data;
			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());	
		   Yii::app()->merchant->logout(false);		
		}
		$this->responseJson();	
	}
	
	public function actiontaxlist()
	{
		$data = array(); 
				
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';		
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
				
		$sortby = "tax_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
					
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();		
		
		$criteria->addCondition("merchant_id=:merchant_id AND tax_type=:tax_type");
		$criteria->params = array( 
		   ':merchant_id'=>intval(Yii::app()->merchant->merchant_id),
		   ':tax_type'=>$transaction_type
		);
				
		$criteria->order = "$sortby $sort";
		$count = AR_tax::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_tax::model()->findAll($criteria);
        
        $tax_price_list = CommonUtility::taxPriceList();
        
        if($models){
        	foreach ($models as $item) {    
        		$default = "";    		
        		
        		if($item->tax_type=="standard"){
        		if($item->default_tax==1){
        			$default = '<div class="badge badge-light">'.t("Default").'</div>';    	
        			$default.='<div class="font11">'.$tax_price_list[$item->tax_in_price].'</div>';	
        		}
        		}
        		
        		$data[]=array(
        		  'tax_uuid'=>$item->tax_uuid,
        		  'tax_name'=>"<div>$item->tax_name</div>".$default,
        		  'tax_rate'=>$item->tax_rate,
        		  'active'=>$item->active==1? '<span class="badge badge-success">'.t("Active").'</span>' : '<span class="badge badge-danger">'.t("Inactive").'</span>' ,
        		  'tax_id'=>$item->tax_id
        		);
        	}
        }
        
        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
				
		$this->responseTable($datatables);
	}			
    
	public function actionsavetax()
	{
		 try {
		 	
		 	$merchant_id = Yii::app()->merchant->merchant_id;
		 	
		 	$tax_uuid = isset($this->data['tax_uuid'])?$this->data['tax_uuid']:'';
		 	$tax_name = isset($this->data['tax_name'])?$this->data['tax_name']:'';
		 	$tax_type = isset($this->data['tax_type'])?$this->data['tax_type']:'';
		 	$tax_rate = isset($this->data['tax_rate'])?$this->data['tax_rate']:'';
		 	$default_tax = isset($this->data['default_tax'])?$this->data['default_tax']:'';
		 	$active = isset($this->data['active'])?$this->data['active']:'';
		 	$tax_in_price = isset($this->data['tax_in_price'])?$this->data['tax_in_price']:'';
		 	
		 	$model = AR_tax::model()->find("tax_uuid=:tax_uuid",array(
		 	 ':tax_uuid'=>$tax_uuid
		 	));
		 	if(!$model){
		 		$model = new AR_tax;
		 	}
		 			 	
		 	$model->merchant_id = intval($merchant_id);
		 	$model->tax_name = $tax_name;
		 	$model->tax_type = $tax_type;
		 	$model->tax_rate = floatval($tax_rate);
		 	$model->default_tax = intval($default_tax);
		 	$model->active = intval($active);
		 	$model->tax_in_price = intval($tax_in_price);
		 	if($model->save()){
		 		$this->code = 1;
		 		$this->msg = t("Successful");
		 	} else $this->msg = CommonUtility::parseError( $model->getErrors());		 	
		 	
		 } catch (Exception $e) {
		   $this->msg[] = t($e->getMessage());			   
		}
		$this->responseJson();	
    }
    
    public function actiongetTax()
    {
    	try {
    		
    		
    		$tax_uuid = Yii::app()->input->post('tax_uuid'); 
    		$model = AR_tax::model()->find("merchant_id=:merchant_id AND tax_uuid=:tax_uuid",array(
    		 ':merchant_id'=>Yii::app()->merchant->merchant_id,
    		 ':tax_uuid'=>$tax_uuid
    		));
    		if($model){
    			$this->code = 1; $this->msg = "ok";
    			$this->details = array(
    			  'tax_uuid'=>$model->tax_uuid,
    			  'tax_name'=>$model->tax_name,
    			  'tax_in_price'=>$model->tax_in_price,
    			  'tax_rate'=>$model->tax_rate,
    			  'default_tax'=>$model->default_tax,
    			  'active'=>$model->active,
    			);
    		} else $this->msg = t("Tax not found");
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   
		}
		$this->responseJson();	
    }
    
    public function actiontaxDelete()
    {
    	try {
    		
    		$tax_uuid = Yii::app()->input->post('tax_uuid');     	
    		$model = AR_tax::model()->find("tax_uuid=:tax_uuid",array(
    		 ':tax_uuid'=>$tax_uuid
    		));
    		if($model){
    			$model->delete();
    			$this->code = 1; $this->msg = "ok";    			
    		} else $this->msg = t("Tax not found");
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   
		}
		$this->responseJson();	
    }
    
    public function actiongetLastTenOrder()
    {
    	try {
        
    		$merchant_id = Yii::app()->merchant->merchant_id;
    		$settings = OptionsTools::find(array('merchant_order_critical_mins'),$merchant_id);
    		$critical_mins = isset($settings['merchant_order_critical_mins'])?$settings['merchant_order_critical_mins']:0;
    		$critical_mins = intval($critical_mins);    		
    		
    		$data = array(); $order_status = array(); $datetime=date("Y-m-d g:i:s a");    		
    		$filter_by = Yii::app()->input->post('filter_by'); 
    		$limit = Yii::app()->input->post('limit'); 
    		    		  
    		if($filter_by!="all"){
	    		$order_status = AOrders::getOrderTabsStatus($filter_by);			    		
    		}
    				    		
    		$status = COrders::statusList(Yii::app()->language);    	
            $services = COrders::servicesList(Yii::app()->language);
            $payment_status = COrders::paymentStatusList2(Yii::app()->language,'payment');  
            
            /*$status_not_in = AOrderSettings::getStatus(array('status_delivered','status_completed',
              'status_cancel_order','status_rejection','status_delivery_fail','status_failed'
            ));*/						
            $status_in = AOrders::getOrderTabsStatus('new_order');  
            
            $payment_list = AttributesTools::PaymentProvider();	                           
                        
    		$criteria=new CDbCriteria();
		    $criteria->alias = "a";
		    $criteria->select = "a.order_id, a.order_uuid, a.client_id, a.status, a.order_uuid , 
		    a.payment_code, a.service_code,a.total, a.delivery_date, a.delivery_time, a.date_created, a.payment_code, a.total,
		    a.payment_status, a.is_view, a.is_critical, a.whento_deliver,		    		    		    		    
		    b.meta_value as customer_name, 
		    
		    IF(a.whento_deliver='now', 
		      TIMESTAMPDIFF(MINUTE, a.date_created, NOW())
		    , 
		     TIMESTAMPDIFF(MINUTE, concat(a.delivery_date,' ',a.delivery_time), NOW())
		    ) as min_diff
		    
		    ,
		    (
		       select sum(qty)
		       from {{ordernew_item}}
		       where order_id = a.order_id
		    ) as total_items
		    ";
		    $criteria->join='LEFT JOIN {{ordernew_meta}} b on  a.order_id=b.order_id ';
		    $criteria->condition = "a.merchant_id=:merchant_id AND meta_name=:meta_name ";
		    $criteria->params  = array(
		      ':merchant_id'=>intval($merchant_id),		  
		      ':meta_name'=>'customer_name'
		    );
		    
		    if(is_array($order_status) && count($order_status)>=1){
		    	$criteria->addInCondition('status',(array) $order_status );
		    } else {
		    	$draft = AttributesTools::initialStatus();		    	
		    	$criteria->addNotInCondition('status', array($draft) );
            }
            
            $pos_code = AttributesTools::PosCode();	
		    $criteria->addNotInCondition('a.service_code', array($pos_code) );
            
		    $criteria->order = "date_created DESC";		    
		    $criteria->limit = intval($limit);
		    		    
		    $models = AR_ordernew::model()->findAll($criteria);   
		    
		    PrettyDateTime::$category='backend';
		    
		    if($models){		    	
		    	foreach ($models as $item) {
		    		
		    		$status_trans = $item->status;
		            if(array_key_exists($item->status, (array) $status)){
		               $status_trans = $status[$item->status]['status'];
		            }
		            
		            $trans_order_type = $item->service_code;
			        if(array_key_exists($item->service_code,(array)$services)){
			            $trans_order_type = $services[$item->service_code]['service_name'];
			        }
			        			        
			        $payment_status_name = $item->payment_status;
			        if(array_key_exists($item->payment_status,(array)$payment_status)){
			            $payment_status_name = $payment_status[$item->payment_status]['title'];
			        }
			        
			        if(array_key_exists($item->payment_code,(array)$payment_list)){
			            $item->payment_code = $payment_list[$item->payment_code];
			        }
		    					        
			        $is_critical =  0;		
			        /*if($item->whento_deliver=="schedule"){
			        	if($item->min_diff>0){
			        		$is_critical = true;
			        	}
			        } else if ($item->min_diff>$critical_mins && !in_array($item->status,(array)$status_not_in) ) {
			        	$is_critical = true;
			        }*/
			        if($item->whento_deliver=="schedule"){
			        	if($item->min_diff>0){
			        		$is_critical = true;
			        	}
			        } else if ($critical_mins>0 && $item->min_diff>$critical_mins && in_array($item->status,(array)$status_in) ) {
			        	$is_critical = true;
			        }
			        
		    		$data[]=array(
		    		  'order_id'=>$item->order_id,
		    		  'order_id'=>t("Order #{{order_id}}",array('{{order_id}}'=>$item->order_id)),
		    		  'order_uuid'=>$item->order_uuid,
		    		  'client_id'=>$item->client_id,
		    		  'customer_name'=>$item->customer_name,
		    		  'status'=>$status_trans,
		    		  'status_raw'=>str_replace(" ","_",$item->status),
		    		  'order_type'=>$trans_order_type,
		    		  'payment_code'=>$item->payment_code,
		    		  'total'=>Price_Formatter::formatNumber($item->total),
		    		  'payment_status'=>$payment_status_name,
		    		  'payment_status_raw'=>str_replace(" ","_",$item->payment_status),
		    		  'view_order'=> Yii::app()->createAbsoluteUrl('orders/view',array(
			         	  'order_uuid'=>$item->order_uuid
			           )),
			          'print_pdf'=>Yii::app()->createAbsoluteUrl('print/pdf',array(
			         	  'order_uuid'=>$item->order_uuid
			           )),
			           'is_view'=>$item->is_view,
			           'is_critical'=>$is_critical,
			           'min_diff'=>$item->min_diff,
			           'whento_deliver'=>$item->whento_deliver,
			           'delivery_date'=>$item->delivery_date,
			           'delivery_time'=>$item->delivery_time,
			           'date_created'=>PrettyDateTime::parse(new DateTime($item->date_created)),
		    		);
		    	}
		    	
		    	$this->code = 1; $this->msg = "ok";
		    	$this->details = $data;
		    	   	    
		    } else {
		    	$this->msg = t("You don't have current orders.");
		    	$this->details = array(
		    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/order-best-food@2x.png"
		    	);
		    }    	
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		   $this->details = array(
	    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/order-best-food@2x.png"
	    	);
		}
		$this->responseJson();	
    }
     
    public function actionmostPopularItems()
	{		
		try {
			
			$data = array();
			$merchant_id = Yii::app()->merchant->merchant_id;
			$limit = Yii::app()->input->post('limit'); 
			$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));						
			
			$criteria=new CDbCriteria();
			$criteria->alias = "a";
			$criteria->select="a.item_id, a.cat_id, sum(qty) as total_sold,
			b.photo , b.path,
			(
			  select item_name from {{item_translation}}
			  where item_id = a.item_id and language=".q(Yii::app()->language)."
			) as item_name,
			(
			  select category_name from {{category_translation}}
			  where cat_id=a.cat_id and language=".q(Yii::app()->language)."
			) as category_name
			";
			$criteria->join='LEFT JOIN {{item}} b on  a.item_id = b.item_id 
			LEFT JOIN {{ordernew}} c on a.order_id = c.order_id 
			';						
			$criteria->condition = "c.merchant_id=:merchant_id";
			$criteria->params = array( 
			   ':merchant_id'=>$merchant_id			   
			);
			if(is_array($status_completed) && count($status_completed)>=1){			
			   $criteria->addInCondition('c.status', (array) $status_completed );
		    }		
			
			$criteria->group="a.item_id";
			$criteria->order = "sum(qty) DESC";	
			$criteria->limit = intval($limit);
									
		    $model = AR_ordernew_item::model()->findAll($criteria); 
		    
		    if($model){
		       foreach ($model as $item) {		       	  
		       	  $total_sold = number_format($item->total_sold,0,'',',');
		       	  $data[] = array(
		       	    'item_name'=>Yii::app()->input->xssClean(htmlspecialchars_decode($item->item_name)),
		       	    'category_name'=>Yii::app()->input->xssClean(htmlspecialchars_decode($item->category_name)),
		       	    'total_sold'=>t("{{total_sold}} sold", array('{{total_sold}}'=>$total_sold) ),
		       	    'image_url'=>CMedia::getImage($item->photo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('item')),
		       	    'item_link' => Yii::app()->createAbsoluteUrl('/food/item_update',array('item_id'=>$item->item_id)),
		       	  );
		       }
		       
		       $this->code = 1; $this->msg = "ok";
		       $this->details = $data;
		    	   	    
            } else {
            	$this->msg = t("No item solds yet");   
            	$this->details = array(
		    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/order-best-food@2x.png"
		    	);
            }         
            
			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   
		   $this->details = array(
		    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/order-best-food@2x.png"
		    	);
		}
		$this->responseJson();	
    }   
    
    public function actionmostPopularCustomer()
    {
    	try {
    		
    		$data = array();
			$merchant_id = Yii::app()->merchant->merchant_id;
			$limit = Yii::app()->input->post('limit'); 
			$not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));		    
			
			$criteria=new CDbCriteria();
			$criteria->alias = "a";
			$criteria->select="a.client_id, count(*) as total_sold,
			b.first_name,b.last_name,b.date_created, b.avatar as logo, b.path
			";
			$criteria->join='LEFT JOIN {{client}} b on  a.client_id=b.client_id ';
			$criteria->condition = "a.merchant_id=:merchant_id and b.client_id IS NOT NULL";
			$criteria->params = array(':merchant_id'=>$merchant_id);
			
			if(is_array($not_in_status) && count($not_in_status)>=1){			
			   $criteria->addNotInCondition('a.status', (array) $not_in_status );
		    }		
			
			$criteria->group="a.client_id";
			$criteria->order = "count(*) DESC";	
			$criteria->limit = intval($limit);		    
			
		    $model = AR_ordernew::model()->findAll($criteria); 
		    if($model){		    	
		    	foreach ($model as $item) {
		    		$total_sold = number_format($item->total_sold,0,'',',');
		    		$data[] = array(
		    		  'client_id'=>$item->client_id,
		    		  'first_name'=>$item->first_name,
		    		  'last_name'=>$item->last_name,
		    		  'total_sold'=>t("{{total_sold}} orders", array('{{total_sold}}'=>$total_sold) ),
		    		  'member_since'=> t("Member since {{date_created}}" , array('{{date_created}}'=>Date_Formatter::dateTime($item->date_created)) ),
		    		  'image_url'=>CMedia::getImage($item->logo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('customer')),
		    		);
		    	}
		    	$this->code = 1; $this->msg = "ok";
		        $this->details = $data;
		    } else $this->msg = t("You don't have customer yet");
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   
		}
		$this->responseJson();	
    }
    
    public function actionsalesOverview()
    {
    	try {
    	
    		$data = array();
    		$merchant_id = Yii::app()->merchant->merchant_id;
    		$months = intval(Yii::app()->input->post('months')); 
    		
    		$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));		        		
    		$date_start = date("Y-m-d", strtotime(date("c")." -$months months"));
    		$date_end = date("Y-m-d");
    		
    		$criteria=new CDbCriteria();
    		$criteria->select = "
    		DATE_FORMAT(date_created, '%b') AS month , SUM(total) as monthly_sales
    		";
    		$criteria->group="DATE_FORMAT(date_created, '%b')";
			$criteria->order = "date_created DESC";	
			
			$criteria->condition = "merchant_id=:merchant_id";
			$criteria->params = array(':merchant_id'=>$merchant_id);
			
			if(is_array($status_completed) && count($status_completed)>=1){			
			   $criteria->addInCondition('status', (array) $status_completed );
		    }				    
		    if(!empty($date_start) && !empty($date_end)){
				$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
			}
		        				    
    		$model = AR_ordernew::model()->findAll($criteria); 
    		if($model){
    			$category = array(); $sales = array();
    			foreach ($model as $item) {    				
    				$category[] = t($item->month);
    				$sales[] = floatval($item->monthly_sales);
    			}
    			
    			$data = array(
    			  'category'=>$category,
    			  'data'=>$sales
    			);
    			
    			$this->code = 1; $this->msg = "ok";
		        $this->details = $data;
		        
    		} else {
    			$this->msg = t("You don't have sales yet");
    			$this->details = array(
		    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/no-results2.png"
		    	);
    		}    	
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   
		   $this->details = array(
		    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/order-best-food@2x.png"
		    	);
		}
		$this->responseJson();	
    }
    
    public function actionlatestReview()
    {
    	try {
    		
    		$data = array();
    		$merchant_id = Yii::app()->merchant->merchant_id;
    		$limit = intval(Yii::app()->input->post('limit')); 
    		
    		$criteria=new CDbCriteria();
    		$criteria->alias = "a";
    		$criteria->select = "a.client_id, a.review, a.rating,
    		b.first_name,b.last_name,b.date_created, b.avatar as logo, b.path
    		";
    		$criteria->join='LEFT JOIN {{client}} b on  a.client_id=b.client_id ';
    		$criteria->condition = "a.merchant_id=:merchant_id";
			$criteria->params = array(':merchant_id'=>$merchant_id);
    		    		
    		$model = AR_review::model()->findAll($criteria); 
    		if($model){
    			foreach ($model as $item) {    				
    				$data[] = array(
    				   'client_id'=>$item->client_id,
    				   'first_name'=>$item->first_name,
    				   'last_name'=>$item->last_name,
    				   'review'=>$item->review,
    				   'rating'=>$item->rating,
    				   'date_created'=>Date_Formatter::dateTime($item->date_created),
    				   'image_url'=>CMedia::getImage($item->logo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('customer')),
    				);
    			}
    			$this->code = 1; $this->msg = "ok";
		        $this->details = $data;
    		} else $this->msg = t("You don't have reviews yet");
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   
		}
		$this->responseJson();	
    }
    
    public function actionOverviewReview()
    {
    	try {
    	
    	    $data = array(); $total = 0;
    		$merchant_id = Yii::app()->merchant->merchant_id;
    		
    		$total = CReviews::reviewsCount($merchant_id);
    		$start = date('Y-m-01'); $end = date("Y-m-d");
    		$this_month = CReviews::totalCountByRange($merchant_id,$start,$end);
    		$user = CReviews::userAddedReview($merchant_id,4);
    		$review_summary = CReviews::summaryCount($merchant_id,$total);
    		
    		$data = array(
    		  'total'=>$total,
    		  'this_month'=>$this_month,
    		  'this_month_words'=>t("This month you got {{count}} New Reviews",array('{{count}}'=>$this_month)),
    		  'user'=>$user,
    		  'review_summary'=>$review_summary,
    		  'link_to_review'=>Yii::app()->createAbsoluteUrl('/customer/reviews')
    		);    		
    		
    		$this->code = 1; $this->msg = "ok";
		    $this->details = $data;
		        
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   
		}
		$this->responseJson();	
    }
    
    public function actionitemSales()
    {
    	try {
    		    		
    		$data = array();
    	    $merchant_id = Yii::app()->merchant->merchant_id;
    		$period = Yii::app()->input->post('period'); 
    		
    		$data = CReports::ItemSales($merchant_id,$period);
    		$items = CReports::popularItems($merchant_id,$period);
    		/*dump($data);
    		die();*/
    		
			$this->code = 1; $this->msg = "ok";
	        $this->details = array(
	          'sales'=>$data,
	          'items'=>$items,
	        );
	        
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   
		   $this->details = array(
	    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/order-best-food@2x.png"
	    	);
		}
		$this->responseJson();	
    }
    
    public function actionsalesSummary()
    {
    	try {
    		    	    	   
    	   $card_id = 0;
    	   $merchant_id = Yii::app()->merchant->merchant_id;   
    	   
    	   $sales_week = CReports::SalesThisWeek($merchant_id);
    	   
    	   try {									
			    $card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] , Yii::app()->merchant->merchant_id );
				$balance = CWallet::getBalance($card_id);
		   } catch (Exception $e) {			   
			   $balance = 0;		
		   }	
		   
		   $earning_week = CReports::EarningThisWeek($card_id);    	   
    	   
    	   $data = array(
    	     'sales_week'=>$sales_week,
    	     'earning_week'=>$earning_week,
    	     'balance'=>$balance,
    	     'price_format'=>array(
		         'symbol'=>Price_Formatter::$number_format['currency_symbol'],
		         'decimals'=>Price_Formatter::$number_format['decimals'],
		         'decimal_separator'=>Price_Formatter::$number_format['decimal_separator'],
		         'thousand_separator'=>Price_Formatter::$number_format['thousand_separator'],
		         'position'=>Price_Formatter::$number_format['position'],
		      )
    	   );
    	   
    	   $this->code = 1; $this->msg = "ok";
    	   $this->details = $data;
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}
		$this->responseJson();	
    }
    
    public function actionreportSales()
    {
    	$merchant_id = Yii::app()->merchant->merchant_id;
    	$data = array();		
	    $status = COrders::statusList(Yii::app()->language);    	
	    $services = COrders::servicesList(Yii::app()->language);
	    
	    $payment_list = array();
        try {
           $payment_list = CPayments::PaymentList($merchant_id,true);            
        } catch (Exception $e) {
        	//
        }
	            	    
	    $page = isset($this->data['start'])?$this->data['start']:0;	
	    $length = isset($this->data['length'])?$this->data['length']:0;	
	    $draw = isset($this->data['draw'])?$this->data['draw']:0;	
	    $search = isset($this->data['search'])?$this->data['search']['value']:'';	
	    $columns = isset($this->data['columns'])?$this->data['columns']:'';
	    $order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
	    $filter = isset($this->data['filter'])?$this->data['filter']:'';
	    
	    $date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
	    $date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
	            
	    $sortby = "order_id"; $sort = 'DESC';
	    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }
	            
	    if($page>0){
	       $page = intval($page)/intval($length);	
	    }
	    $criteria=new CDbCriteria();
	    $criteria->alias = "a";
	    $criteria->select = "a.order_id, a.client_id, a.status, a.order_uuid , 
	    a.payment_code, a.service_code,a.total, a.date_created,
	    b.meta_value as customer_name, 
	    (
	       select sum(qty)
	       from {{ordernew_item}}
	       where order_id = a.order_id
	    ) as total_items,
	    
	    c.avatar as logo, c.path
	    ";
	    $criteria->join='LEFT JOIN {{ordernew_meta}} b on  a.order_id=b.order_id 
	    LEFT JOIN {{client}} c on  a.client_id=c.client_id
	    ';	         
	    
	    $criteria->condition = "a.merchant_id=:merchant_id AND b.meta_name=:meta_name ";
	    $criteria->params  = array(
	      ':merchant_id'=>intval($merchant_id),		  
	      ':meta_name'=>'customer_name'
	    );
	    if(!empty($date_start) && !empty($date_end)){
	        $criteria->addBetweenCondition("DATE_FORMAT(a.date_created,'%Y-%m-%d')", $date_start , $date_end );
	    }
	    $initial_status = AttributesTools::initialStatus();
	    $criteria->addNotInCondition('a.status', (array) array($initial_status) );
	    
	    if(is_array($filter) && count($filter)>=1){
	        $filter_order_status = isset($filter['order_status'])?$filter['order_status']:'';
	        $filter_order_type = isset($filter['order_type'])?$filter['order_type']:'';
	        $filter_client_id = isset($filter['client_id'])?intval($filter['client_id']):'';
	        
	        if(!empty($filter_order_status)){
	            $criteria->addSearchCondition('a.status', $filter_order_status );
	        }
	        if(!empty($filter_order_type)){
	            $criteria->addSearchCondition('a.service_code', $filter_order_type );
	        }
	        if($filter_client_id>0){
	            $criteria->addSearchCondition('a.client_id', intval($filter_client_id) );
	        }
	    }
	            
	    $criteria->order = "$sortby $sort";	    
	    $count = AR_ordernew::model()->count($criteria); 
	    $pages=new CPagination( intval($count) );
	    $pages->setCurrentPage( intval($page) );        
	    $pages->pageSize = intval($length);
	    $pages->applyLimit($criteria);     	    
	    $models = AR_ordernew::model()->findAll($criteria);   	    
	    if($models){
	         foreach ($models as $item) {         		
			         
		         $trans_order_type = $item->service_code;
		         if(array_key_exists($item->service_code,$services)){
		             $trans_order_type = $services[$item->service_code]['service_name'];
		         }
		         
		         $payment_name = $item->payment_code;
		         if(array_key_exists($item->payment_code,(array)$payment_list)){
		         	$payment_name =$payment_list[$item->payment_code]['payment_name'];
		         }
		         
		         $avatar = CMedia::getImage($item->logo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('customer'));
		         
		         $item->total_items = intval($item->total_items);
		         $item->total_items = t("{{total_items}} items",array(
		          '{{total_items}}'=>$item->total_items
		         ));
		         
		         $status_trans = $item->status;
		         if(array_key_exists($item->status, (array) $status)){
		             $status_trans = $status[$item->status]['status'];
		         }
         
		         $status_class = str_replace(" ","_",$item->status);
		         
		         $place_on = t("Place on {{date}}",array(
		          '{{date}}'=>Date_Formatter::dateTime($item->date_created)
		         ));
		         
		         $view_order = Yii::app()->createUrl('orders/view',array(
		           'order_uuid'=>$item->order_uuid
		         ));
		         
		         $order_type_class = str_replace(" ","_",$item->service_code);
				         
		         
$information = <<<HTML
$item->total_items<span class="ml-2 badge order_status $status_class">$status_trans</span>
<p class="dim m-0">$place_on</p>
HTML;
		         

	             $data[]=array(	              
	              'client_id'=>'<img class="img-60 rounded-circle" src="'.$avatar.'">',
	              'order_id'=>'<a href="'.$view_order.'">'.$item->order_id.'</a>',
	              'order_uuid'=>$information,
	              'service_code'=>'<span class="badge services '.$order_type_class.' ">'.$trans_order_type.'</span>',
	              'payment_code'=>$payment_name,
	              'total'=>Price_Formatter::formatNumber($item->total),
	            );
	         }
	    }	
	    
	    $datatables = array(
	      'draw'=>intval($draw),
	      'recordsTotal'=>intval($count),
	      'recordsFiltered'=>intval($count),
	      'data'=>$data
	    );
	    $this->responseTable($datatables);		
    }
    
    public function actionreportSalesSummary()
    {
    
    	$merchant_id = Yii::app()->merchant->merchant_id;
    	$data = array();		
    	$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
    	$size_list = AttributesTools::sizeList($merchant_id,Yii::app()->language);    	    	
    	$item_name_list = AttributesTools::itemNameList($merchant_id,Yii::app()->language);
    	
    	$page = isset($this->data['start'])?$this->data['start']:0;	
	    $length = isset($this->data['length'])?$this->data['length']:0;	
	    $draw = isset($this->data['draw'])?$this->data['draw']:0;	
	    $search = isset($this->data['search'])?$this->data['search']['value']:'';	
	    $columns = isset($this->data['columns'])?$this->data['columns']:'';
	    $order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
	    $filter = isset($this->data['filter'])?$this->data['filter']:'';
	    
	    $date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
	    $date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
	    
	    $transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
	            
	    $sortby = "b.item_name"; $sort = 'ASC';
	    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }
	            
	    if($page>0){
	       $page = intval($page)/intval($length);	
	    }
	    $criteria=new CDbCriteria();
	    $criteria->alias = "a";
	    $criteria->select ="
	    a.item_id, a.size_id, a.price,
	    b.item_name, b.photo, b.path,
	    
	    (
	      select 
	      concat(
	        (price * SUM(qty)/SUM(qty)),';',
	        SUM(qty),';',	        
	        ((price * SUM(qty)/SUM(qty)) * SUM(qty))
	      )
	        
	      from {{ordernew_item}}
	      where item_id = a.item_id 
	      and item_size_id = a.item_size_id
	      and order_id IN (
	        select order_id from {{ordernew}}
	        where merchant_id = a.merchant_id
	        and status in (".CommonUtility::arrayToQueryParameters($status_completed).") 	        
	      )
	    ) as item_group
	    
	    ";
	    
	    $criteria->join='LEFT JOIN {{item}} b on  a.item_id = b.item_id	';	  
	    	    
		$criteria->condition = "a.merchant_id=:merchant_id AND b.item_name IS NOT NULL";
		$criteria->params = array(':merchant_id'=>$merchant_id);    
	    
		if(is_array($transaction_type) && count($transaction_type)>=1){
			$criteria->addInCondition('a.item_id',(array) $transaction_type );
		}		
	    
	    $criteria->order = "$sortby $sort";	    
	    $count = AR_item_relationship_size::model()->count($criteria); 
	    $pages=new CPagination( intval($count) );
	    $pages->setCurrentPage( intval($page) );        
	    $pages->pageSize = intval($length);
	    $pages->applyLimit($criteria);     	    
	    
	    if($model = AR_item_relationship_size::model()->findAll($criteria)){	    		    	
	    	foreach ($model as $item) {	    	    		
	    			    		
	    		$item_group = explode(";",$item->item_group);
	    		$average_price = isset($item_group[0])?$item_group[0]:0;
	    		$total_qty = isset($item_group[1])?$item_group[1]:0;	    		
	    		$total = isset($item_group[2])?$item_group[2]:0;
	    		
	    		$photo = CMedia::getImage($item->photo,$item->path,'@thumbnail',
		        CommonUtility::getPlaceholderPhoto('item'));
		        
		        $size_name = '';
		        if(array_key_exists($item->size_id,(array)$size_list)){
		        	$size_name = $size_list[$item->size_id];
		        }
		        
		        $item_name = $item->item_name;
		        if(array_key_exists($item->item_id,(array)$item_name_list)){
		        	$item_name = $item_name_list[$item->item_id];		        	
		        }
	    				
	    		$data[] = array(
	    		  'item_id'=>$item->item_id,	    		  
	    		  'photo'=>'<img class="img-60 rounded-circle" src="'.$photo.'">',
	    		  'item_name'=>$item_name."<p class=\"m-0 text-muted font11\">$size_name</p>",
	    		  'price'=>Price_Formatter::formatNumber($average_price),
	    		  'qty'=>Price_Formatter::convertToRaw($total_qty,0),
	    		  'total'=>Price_Formatter::formatNumber($total)
	    		);
	    	}	    	
	    }
	    	    
	    $datatables = array(
	      'draw'=>intval($draw),
	      'recordsTotal'=>intval($count),
	      'recordsFiltered'=>intval($count),
	      'data'=>$data
	    );
	    $this->responseTable($datatables);		
    }
    
    public function actionitemSalesSummary()
    {
    	try {
    	
    	    $data = array();
    	    $merchant_id = Yii::app()->merchant->merchant_id;
    		$period = Yii::app()->input->post('period'); 
    		
    		$data = CReports::ItemSalesSummary($merchant_id,$period);    		
    		$items = CReports::popularItems($merchant_id,$period);
    		
    		$this->code = 1; $this->msg = "ok";
	        $this->details = array(
	          'sales'=>$data,
	          'items'=>$items,
	        );
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   
		   $this->details = array(
	    	  'image_url'=>CMedia::themeAbsoluteUrl()."/assets/images/no-results0.png"
	    	);
		}
		$this->responseJson();	
    }
    
    public function actionsupplierList()
    {
    	$data = array();
    	$merchant_id = Yii::app()->merchant->merchant_id;
	    $page = isset($this->data['start'])?$this->data['start']:0;	
	    $length = isset($this->data['length'])?$this->data['length']:0;	
	    $draw = isset($this->data['draw'])?$this->data['draw']:0;	
	    $search = isset($this->data['search'])?$this->data['search']['value']:'';	
	    $columns = isset($this->data['columns'])?$this->data['columns']:'';
	    $order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
	    $filter = isset($this->data['filter'])?$this->data['filter']:'';
	    
	    $date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
	    $date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
	            
	    $sortby = "supplier_id"; $sort = 'DESC';
	    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }
	    
	    $page = $page>0? intval($page)/intval($length) : 0;	
        $criteria=new CDbCriteria();
        $criteria->condition = "merchant_id=:merchant_id ";
	    $criteria->params  = array(
	      ':merchant_id'=>intval($merchant_id)	      
	    );
    	
	    if(!empty($date_start) && !empty($date_end)){
	        $criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
	    }
	    
	    if (is_string($search) && strlen($search) > 0){
		   $criteria->addSearchCondition('supplier_name', $search );
		   $criteria->addSearchCondition('contact_name', $search , true , 'OR' );
		}
	    
	    $criteria->order = "$sortby $sort";
	    $count = AR_supplier::model()->count($criteria); 
	    $pages=new CPagination( intval($count) );
	    $pages->setCurrentPage( intval($page) );        
	    $pages->pageSize = intval($length);
	    $pages->applyLimit($criteria);        	    
	    if($model = AR_supplier::model()->findAll($criteria)){
	    	foreach ($model as $item) {
	    		

$information = <<<HTML
$item->contact_name
<p class="dim m-0">$item->email</p>
<p class="dim m-0">$item->phone_number</p>
HTML;


	    		$data[] = array(
	    		  'supplier_id'=>$item->supplier_id,
	    		  'supplier_name'=>Yii::app()->input->xssClean($item->supplier_name),
	    		  'contact_name'=>$information,
	    		  'created_at'=>$item->created_at,
	    		  'update_url'=>Yii::app()->createAbsoluteUrl('/supplier/update',array('id'=>$item->supplier_id)),
	    		  'delete_url'=>Yii::app()->createAbsoluteUrl('/supplier/delete',array('id'=>$item->supplier_id)),
	    		);
	    	}
	    }
	    $datatables = array(
	      'draw'=>intval($draw),
	      'recordsTotal'=>intval($count),
	      'recordsFiltered'=>intval($count),
	      'data'=>$data
	    );
	    $this->responseTable($datatables);
    }
    
    public function actionarchiveOrderList()
    {
    	$data = array();
    	$merchant_id = Yii::app()->merchant->merchant_id;
    	$status_list = COrders::statusList(Yii::app()->language);     
	    $services = COrders::servicesList(Yii::app()->language);
	    $payment_list = AttributesTools::PaymentProvider();	    	    
	    
	    $page = isset($this->data['start'])?$this->data['start']:0;	
	    $length = isset($this->data['length'])?$this->data['length']:0;	
	    $draw = isset($this->data['draw'])?$this->data['draw']:0;	
	    $search = isset($this->data['search'])?$this->data['search']['value']:'';	
	    $columns = isset($this->data['columns'])?$this->data['columns']:'';
	    $order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
	    $filter = isset($this->data['filter'])?$this->data['filter']:'';
	    
	    $date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
	    $date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
	    
	    $sortby = "order_id"; $sort = 'DESC';
    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }
	    
	    $page = $page>0? intval($page)/intval($length) : 0;	
	    $criteria=new CDbCriteria();
	    $criteria->alias = "a";
	    $criteria->select = "
	    a.order_id, a.client_id, a.json_details, a.total_w_tax, a.status, a.trans_type, a.date_created,
	    a.payment_type,
	    b.first_name, b.last_name , b.avatar, b.path,
	    (
	      select count(*) from {{order_details}}
	      where order_id = a.order_id
	    ) as total_items
	    ";
	    
	    $criteria->join='LEFT JOIN {{client}} b on  a.client_id = b.client_id';
	                
	    $criteria->condition = "a.merchant_id=:merchant_id ";
	    $criteria->params  = array(
	      ':merchant_id'=>intval($merchant_id),	      
	    );
    
	    if(!empty($date_start) && !empty($date_end)){
	        $criteria->addBetweenCondition("DATE_FORMAT(a.date_created,'%Y-%m-%d')", $date_start , $date_end );
	    }	    
	    $criteria->addNotInCondition('a.status', array('initial_order') );
	    
	    
	    if(is_array($filter) && count($filter)>=1){	    
		    $filter_order_status = isset($filter['order_status'])?$filter['order_status']:'';
		    $filter_order_type = isset($filter['order_type'])?$filter['order_type']:'';
		    $filter_client_id = isset($filter['client_id'])?intval($filter['client_id']):'';
		    
			if(!empty($filter_order_status)){
				$criteria->addSearchCondition('a.status', $filter_order_status );
			}
			if(!empty($filter_order_type)){
				$criteria->addSearchCondition('a.trans_type', $filter_order_type );
			}
			if($filter_client_id>0){
				$criteria->addSearchCondition('a.client_id', intval($filter_client_id) );
			}
		}
	    
	    
	    $criteria->order = "$sortby $sort";	    
	    $count = AR_order::model()->count($criteria); 
	    $pages=new CPagination( intval($count) );
	    $pages->setCurrentPage( intval($page) );        
	    $pages->pageSize = intval($length);
	    $pages->applyLimit($criteria);     
	    
	    if($model = AR_order::model()->findAll($criteria)){	    	
	    	foreach ($model as $item) { 	    	

	    		$item->total_items = intval($item->total_items);
			    $item->total_items = t("{{total_items}} items",array(
			     '{{total_items}}'=>$item->total_items
			    ));
    
	    		$avatar = CMedia::getImage($item->avatar,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('customer'));
		         
		        $status_class = str_replace(" ","_",$item->status);
		        $status_trans = $item->status;
			    if(array_key_exists($item->status, (array) $status_list)){
			        $status_trans = $status_list[$item->status]['status'];
			    }
			    
			    $trans_order_type = $item->trans_type;
			    if(array_key_exists($item->trans_type,$services)){
			        $trans_order_type = $services[$item->trans_type]['service_name'];
			    }
    
			    $order_type = t("Order Type.");
                $order_type.="<span class='ml-2 services badge $item->trans_type'>$trans_order_type</span>";
                
                $total = t("Total. {{total}}",array(
			     '{{total}}'=>Price_Formatter::formatNumber($item->total_w_tax)
			    ));
			    $place_on = t("Place on {{date}}",array(
			     '{{date}}'=>Date_Formatter::dateTime($item->date_created)
			    ));
			    
			    $payment_type = $item->payment_type;
			    if(array_key_exists($item->payment_type,(array)$payment_list)){
			    	$payment_type = $payment_list[$item->payment_type];
			    }
			    
		         
$information = <<<HTML
$item->total_items<span class="ml-2 badge order_status $status_class">$status_trans</span>
<p class="dim m-0">$payment_type</p>
<p class="dim m-0">$order_type</p>
<p class="dim m-0">$total</p>
<p class="dim m-0">$place_on</p>
HTML;


	    		    		
		    	$data[] = array(		    	  
		    	  'avatar'=>'<img class="img-60 rounded-circle" src="'.$avatar.'">',
		    	  'order_id'=>$item->order_id,
		    	  'client_id'=>$item->first_name." ".$item->last_name,
		    	  'json_details'=>$information,
		    	);
	    	}
	    }
	    	    
	    $datatables = array(
	      'draw'=>intval($draw),
	      'recordsTotal'=>intval($count),
	      'recordsFiltered'=>intval($count),
	      'data'=>$data
	    );
	    $this->responseTable($datatables);		
    }
    
    public function actionDailyStatistic()
    {   
    	try	{
    		
    		$merchant_id = Yii::app()->merchant->merchant_id;
    		$status_new = AOrderSettings::getStatus(array('status_new_order'));
    		$status_delivered = AOrderSettings::getStatus(array('status_delivered','status_completed'));
    		
    		$order_received = CReports::OrderTotalByStatus($merchant_id,$status_new);
    		$today_delivered = CReports::OrderTotalByStatus($merchant_id,$status_delivered);
    		$total_refund = CReports::TotalRefund($merchant_id);
    		$today_sales =  CReports::SalesThisWeek($merchant_id,0,$status_delivered);
    		
    		$data = array(
    		  'order_received'=>$order_received,
    		  'today_delivered'=>$today_delivered,    		  
    		  'total_refund'=>$total_refund,
    		  'today_sales'=>$today_sales,
    		  'price_format'=>AttributesTools::priceFormat()
    		);    		
    		$this->code = 1; $this->msg = "ok";
		    $this->details = $data;
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actioncreateOrder()
    {
    	try {	
    		    		
    		$order_uuid = CPos::createOrder(Yii::app()->merchant->merchant_id);		
    		$this->code = 1; $this->msg = "ok";
		    $this->details = array(
		      'order_uuid'=>$order_uuid,
		      'order_type'=>AttributesTools::PosCode(),
		    );
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionresetPos()
    {
        try {	
        	
        	$order_uuid = Yii::app()->input->post('order_uuid'); 
        	CPos::resetPos($order_uuid);
        	
        	$this->code = 1;
        	$this->msg = "ok";
       	
       	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionremoveItem(){
    	try {	
        	
        	$item_row = Yii::app()->input->post('item_row');         	
        	$model = AR_ordernew_item::model()->find("item_row=:item_row",array(
        	 ':item_row'=>$item_row
        	));
        	
        	if($model){
        	   $model->scenario = "remove";
        	   $model->delete();
        	   $this->code = 1;
        	   $this->msg = "ok";
        	} else $this->msg = t("Item row not found");        	        	
       	
       	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionupdatePosQty()
    {
    	try {	
    		        	        	
        	$qty = intval(Yii::app()->input->post('qty'));  
        	$item_row = Yii::app()->input->post('item_row'); 
        	
        	$model = AR_ordernew_item::model()->find("item_row=:item_row",array(
        	 ':item_row'=>$item_row
        	));
        	if($model){      
        		$model->scenario = "update_item_qty";  		        		
        		$model->qty = $qty;
        		if($model->save()){
        		   $this->code = 1;
        	       $this->msg = "ok";
        		} else $this->msg = CommonUtility::parseError( $model->getErrors());
        	} else $this->msg = t("Item row not found"); 
        	
       	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionapplyPromoCode()
    {
    	try {
    		
    		$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
    		$promo_code = isset($this->data['promo_code'])?$this->data['promo_code']:'';
    		
    		$order = COrders::get($order_uuid);
    		
    		$model = AR_voucher::model()->find("merchant_id=:merchant_id AND voucher_name=:voucher_name",array(
    		  ':merchant_id'=>$order->merchant_id,
    		  ':voucher_name'=>$promo_code
    		));
    		if($model){
    			$promo_id = $model->voucher_id;
    			$now = date("Y-m-d");
    			$sub_total = $order->sub_total;
    			
    			$resp = CPromos::applyVoucher( $order->merchant_id, $promo_id, $order->client_id , $now , $sub_total);
    			
    			$less_amount = $resp['less_amount'];    
    			$promo_type = "voucher";
				$params = array(
				  'name'=>"less voucher",
				  'type'=>$promo_type,
				  'id'=>$promo_id,
				  'target'=>'subtotal',
				  'value'=>"-$less_amount",
				);		
				
				$order->promo_code = isset($resp['voucher_name'])?$resp['voucher_name']:'';
				$order->promo_total = isset($resp['less_amount'])?floatval($resp['less_amount']):0;
				if($order->save()){
				   COrders::savedAttributes($order->order_id,'promo',json_encode($params));
			       COrders::savedAttributes($order->order_id,'promo_type',$promo_type);
			       COrders::savedAttributes($order->order_id,'promo_id',$promo_id);    			
			       $this->code = 1;
        	       $this->msg = "ok";
				} else {					 
					 $this->msg = CommonUtility::parseError($order->getErrors());
				}
    		} else $this->msg = t("Voucher code not found");    		    		
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionremovevoucher()
    {
    	try {
    		
    		$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
    		$order = COrders::get($order_uuid);
    		$order->promo_code = '';
			$order->promo_total = 0;
			if($order->save()){
			   $this->code = 1;
        	   $this->msg = "ok";
        	           	   
        	   $criteria=new CDbCriteria();
        	   $criteria->condition = "order_id=:order_id";
        	   $criteria->params = array(':order_id'=>$order->order_id);
        	   $criteria->addInCondition('meta_name', array('promo_id','promo_type','promo'));        	  
        	   AR_ordernew_meta::model()->deleteAll($criteria);
        	   
			} else $this->msg = CommonUtility::parseError($order->getErrors());
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actioncreatecustomer()
    {
    	try {
    		    		
    		$first_name = isset($this->data['first_name'])?$this->data['first_name']:'';
    		$last_name = isset($this->data['last_name'])?$this->data['last_name']:'';
    		$email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
    		$contact_phone = isset($this->data['contact_phone'])?$this->data['contact_phone']:'';
    		
    		$model = new AR_client();
    		$model->first_name = $first_name;
    		$model->last_name = $last_name;
    		$model->email_address = $email_address;
    		$model->contact_phone = $contact_phone;
    		if($model->save()){
    		   $this->code = 1;
        	   $this->msg = t("Customer succesfully created");
        	   $this->details = array(
        	     'client_id'=>$model->client_id,
        	     'client_uuid'=>$model->client_uuid,
        	     'client_name'=>"$first_name $last_name"
        	   );
    		} else {    			
    			$this->msg = CommonUtility::parseModelErrorToString($model->getErrors(),"<br/>");
    		}
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionpaymentList()
    {
    	try {
					   
		   $data = CPayments::PaymentList(Yii::app()->merchant->merchant_id);		   		   
		   $payment_code = isset($data[0])? $data[0]['payment_code'] : '';		   
		   $this->code = 1;
		   $this->msg = "ok";
		   $this->details = array(		     
		     'data'=>$data,
		     'default_payment'=>$payment_code
		   );		   
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }
    
    public function actionsubmitPOSOrder()
    {
    	try {
    		    		
    		$stats = AOrderSettings::getStatus(array('status_completed'));
    		$status_completed = isset($stats[0])?$stats[0]:'complete';
    		
    		$order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';
    		$receive_amount = isset($this->data['receive_amount'])?$this->data['receive_amount']:0;    		
    		$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
    		$order_notes = isset($this->data['order_notes'])?$this->data['order_notes']:'';    		
    		$client_id = isset($this->data['client_id'])?$this->data['client_id']:'';
    		$order_change = isset($this->data['order_change'])?$this->data['order_change']:0;    		
    		
    		$model = COrders::get($order_uuid);
    		$model->status = $status_completed;
    		$model->payment_status = 'paid';
    		$model->payment_code  = $payment_code;
    		$model->client_id = intval($client_id);
    		$model->use_currency_code = Price_Formatter::$number_format['currency_code'];
			$model->base_currency_code = Price_Formatter::$number_format['currency_code'];
			$model->exchange_rate = 1;	
			
			$args = array(); $customer_name = '';
			try {
				$customer = ACustomer::get($client_id);
				$customer_name = $customer->first_name." ".$customer->last_name;				
			} catch (Exception $e) {
				$customer_name = 'Walk-in Customer';
			}	
			
			$metas = array();
			$metas['customer_name'] = $customer_name;
			if(!empty($order_notes)){
			   $metas['order_notes'] = $order_notes;
			}
			if($order_change>0){
			   $metas['order_change'] = floatval($order_change);
			}
			if($receive_amount>0){
			   $metas['receive_amount'] = floatval($receive_amount);
			}
			
			$model->meta = $metas;	
			$model->remarks = "Order created by {{merchant_user}}";
			$args = array(
			  '{{merchant_user}}'=> Yii::app()->merchant->first_name,
			);
			$model->ramarks_trans = json_encode($args);		
			$model->change_by = Yii::app()->merchant->first_name;
					
			$model->scenario = "pos_entry";
    		if($model->save()){
    			$this->code = 1;
		        $this->msg = "ok";		        		        
    		} else $this->msg = CommonUtility::parseModelErrorToString($model->getErrors(),"<br/>");
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }
    
    public function actionposhistory()
    {
    	$data = array();		
    	$status = COrders::statusList(Yii::app()->language);    	
    	$services = COrders::servicesList(Yii::app()->language);
    	$payment_list = AttributesTools::PaymentProvider();	
    	    	
		$merchant_id = Yii::app()->merchant->merchant_id;
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		$filter = isset($this->data['filter'])?$this->data['filter']:'';
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
				
		$sortby = "order_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = $page>0? intval($page)/intval($length) : 0;	
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "a.order_id, a.client_id, a.status, a.order_uuid , 
		a.payment_code, a.service_code,a.total, a.date_created,
		b.meta_value as customer_name, 
		(
		   select sum(qty)
		   from {{ordernew_item}}
		   where order_id = a.order_id
		) as total_items,
		
		c.avatar as logo, c.path
		";
		$criteria->join='
		LEFT JOIN {{ordernew_meta}} b on  a.order_id=b.order_id 
		LEFT JOIN {{client}} c on  a.client_id = c.client_id 
		';
		$criteria->condition = "a.merchant_id=:merchant_id AND meta_name=:meta_name ";
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),		  
		  ':meta_name'=>'customer_name'
		);
		
		$criteria->addInCondition('a.service_code', array('pos') );
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(a.date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		$initial_status = AttributesTools::initialStatus();
		$criteria->addNotInCondition('a.status', (array) array($initial_status) );
		
		if(is_array($filter) && count($filter)>=1){
		    $filter_order_status = isset($filter['order_status'])?$filter['order_status']:'';
		    $filter_order_type = isset($filter['order_type'])?$filter['order_type']:'';
		    $filter_client_id = isset($filter['client_id'])?intval($filter['client_id']):'';
		    
			if(!empty($filter_order_status)){
				$criteria->addSearchCondition('a.status', $filter_order_status );
			}
			if(!empty($filter_order_type)){
				$criteria->addSearchCondition('a.service_code', $filter_order_type );
			}
			if($filter_client_id>0){
				$criteria->addSearchCondition('a.client_id', intval($filter_client_id) );
			}
		}
				
		$criteria->order = "$sortby $sort";
		$count = AR_ordernew::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
                        
        $models = AR_ordernew::model()->findAll($criteria);
                
        if($models){
         	foreach ($models as $item) {         		

         	$item->total_items = intval($item->total_items);
         	$item->total_items = t("{{total_items}} items",array(
         	 '{{total_items}}'=>$item->total_items
         	));
         	
         	$trans_order_type = $item->service_code;
         	if(array_key_exists($item->service_code,$services)){
         		$trans_order_type = $services[$item->service_code]['service_name'];
         	}
         	
         	$order_type = t("Order Type.");
         	$order_type.="<span class='ml-2 services badge $item->service_code'>$trans_order_type</span>";
         	
         	$total = t("Total. {{total}}",array(
         	 '{{total}}'=>Price_Formatter::formatNumber($item->total)
         	));
         	$place_on = t("Place on {{date}}",array(
         	 '{{date}}'=>Date_Formatter::dateTime($item->date_created)
         	));
         	
         	$status_trans = $item->status;
         	if(array_key_exists($item->status, (array) $status)){
         		$status_trans = $status[$item->status]['status'];
         	}
         	
         	$view_order = Yii::app()->createUrl('orders/view',array(
         	  'order_uuid'=>$item->order_uuid
         	));
         	
         	$print_pdf = Yii::app()->createUrl('print/pdf',array(
         	  'order_uuid'=>$item->order_uuid
         	));
         	
         	$status_class = str_replace(" ","_",$item->status);
         	         	
         	if(array_key_exists($item->payment_code,(array)$payment_list)){
	            $item->payment_code = $payment_list[$item->payment_code];
	        }
			        
	        $avatar = CMedia::getImage($item->logo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('customer'));
		         
         		
$information = <<<HTML
$item->total_items<span class="ml-2 badge order_status $status_class">$status_trans</span>
<p class="dim m-0">$item->payment_code</p>
<p class="dim m-0">$order_type</p>
<p class="dim m-0">$total</p>
<p class="dim m-0">$place_on</p>
HTML;

$buttons = <<<HTML
<div class="btn-group btn-group-actions" role="group">
 <a href="$view_order" target="_blank" class="btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
 <a href="$print_pdf" target="_blank"  class="btn btn-light tool_tips"><i class="zmdi zmdi-download"></i></a>
</div>
HTML;

         		$data[]=array(
         		  'logo'=>'<img class="img-60 rounded-circle" src="'.$avatar.'">',
        		  'order_id'=>$item->order_id,
        		  'client_id'=>$item->customer_name,
        		  'status'=>$information,
        		  'order_uuid'=>$buttons
        		);
         	}
        }	
        
        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
		$this->responseTable($datatables);		
    }    
    

    public function actionrefundreport()
    {
    
    	$merchant_id = Yii::app()->merchant->merchant_id;
    	$status = COrders::statusList(Yii::app()->language);    	
    	$payment_list = AttributesTools::PaymentProvider();       
    	 
        
    	$data = array();		
    	
    	$page = isset($this->data['start'])?$this->data['start']:0;	
	    $length = isset($this->data['length'])?$this->data['length']:0;	
	    $draw = isset($this->data['draw'])?$this->data['draw']:0;	
	    $search = isset($this->data['search'])?$this->data['search']['value']:'';	
	    $columns = isset($this->data['columns'])?$this->data['columns']:'';
	    $order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
	    $filter = isset($this->data['filter'])?$this->data['filter']:'';
	    
	    $date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
	    $date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
	    
	    $transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';	    
	            
	    $sortby = "a.date_created"; $sort = 'DESC';
	    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }
	    
	            
	    if($page>0){
	       $page = intval($page)/intval($length);	
	    }
	    $criteria=new CDbCriteria();
	    $criteria->alias = "a";
	    $criteria->select ="a.client_id,a.order_id,a.transaction_description,a.payment_code,
	    a.trans_amount, a.status, a.payment_reference, a.date_created,
	    b.avatar as photo, b.path,
	    c.order_uuid
	    ";	    
	    $criteria->join='
	    LEFT JOIN {{client}} b on  a.client_id = b.client_id
	    LEFT JOIN {{ordernew}} c on  a.order_id = c.order_id
	    ';	  
	    
		$criteria->condition = "a.merchant_id=:merchant_id";
		$criteria->params = array(':merchant_id'=>$merchant_id);    
		$criteria->addInCondition('a.transaction_name', array('refund','partial_refund') );
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(a.date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		if(is_array($transaction_type) && count($transaction_type)>=1){
			$criteria->addInCondition('a.status',(array) $transaction_type );
		}
			    		
	    $criteria->order = "$sortby $sort";	    
	    $count = AR_ordernew_transaction::model()->count($criteria); 
	    $pages=new CPagination( intval($count) );
	    $pages->setCurrentPage( intval($page) );        
	    $pages->pageSize = intval($length);
	    $pages->applyLimit($criteria);     	    
	    	    
	    if($model = AR_ordernew_transaction::model()->findAll($criteria)){	    		    		    	
	    	foreach ($model as $item) {	  

	    		$avatar = CMedia::getImage($item->photo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('customer'));		         
		        $date = t("Refund on {{date}}",array(
		         '{{date}}'=>Date_Formatter::dateTime($item->date_created)
		        ));
		        $status_class = CommonUtility::removeSpace($item->status);
		        $status_trans = $item->status;
		         if(array_key_exists($item->status, (array) $status)){
		             $status_trans = $status[$item->status]['status'];
		         }
		        $transaction_description = Yii::app()->input->xssClean($item->transaction_description);
		        $reference = t("Payment reference# {{payment_reference}}",array(
		          '{{payment_reference}}'=>$item->payment_reference
		        ));
		        
		        $view_order = Yii::app()->createUrl('orders/view',array(
		           'order_uuid'=>$item->order_uuid
		         ));
	    		    		
$information = <<<HTML
$transaction_description<span class="ml-2 badge payment $status_class">$status_trans</span>
<p class="font12 dim m-0">$date</p>
<p class="font12 dim m-0">$reference</p>
HTML;
		         		         
	    		$data[] = array(	    	
	    		  'date_created'=>$item->date_created,
	    		  'client_id'=>'<img class="img-60 rounded-circle" src="'.$avatar.'">',
	    		  'order_id'=>'<a href="'.$view_order.'">'.$item->order_id.'</a>',
	    		  'transaction_description'=>$information,
	    		  'payment_code'=> isset($payment_list[$item->payment_code])?$payment_list[$item->payment_code]:$item->payment_code ,
	    		  'trans_amount'=>Price_Formatter::formatNumber($item->trans_amount),	    		  
	    		);
	    	}	    	
	    }
	    	    
	    $datatables = array(
	      'draw'=>intval($draw),
	      'recordsTotal'=>intval($count),
	      'recordsFiltered'=>intval($count),
	      'data'=>$data
	    );
	    $this->responseTable($datatables);		
    }    
   
    public function actionMerchantPaymentPlans()
    {
    	
        $data = array();    	
        $payment_gateway = AttributesTools::PaymentProvider();
		
    	$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
		$filter = isset($this->data['filter'])?$this->data['filter']:'';	
		$merchant_id = Yii::app()->merchant->merchant_id;
				
		$sortby = "created"; $sort = 'DESC';
    		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select="a.merchant_id, a.invoice_number,a.invoice_ref_number,a.created,a.amount,a.status,
		a.payment_code,
		b.title , c.restaurant_name , c.logo, c.path
		";
		$criteria->join='
		LEFT JOIN {{plans_translation}} b on  a.package_id=b.package_id 
		LEFT JOIN {{merchant}} c on  a.merchant_id = c.merchant_id 
		';				
		
		$params = array();
		$criteria->addCondition("b.language=:language and c.restaurant_name IS NOT NULL AND TRIM(c.restaurant_name) <> ''");
		$params['language'] = Yii::app()->language;
		
		$criteria->addCondition('a.merchant_id=:merchant_id');
        $params['merchant_id']  = intval($merchant_id);
        
		$criteria->params = $params;
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(a.created,'%Y-%m-%d')", $date_start , $date_end );
		}
		if(is_array($transaction_type) && count($transaction_type)>=1){
			$criteria->addInCondition('a.status',(array) $transaction_type );
		}		       
				
		$criteria->order = "$sortby $sort";
		$count = AR_plans_invoice::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);                
        $models = AR_plans_invoice::model()->findAll($criteria);        
        if($models){
        	foreach ($models as $item) {
        		$avatar = CMedia::getImage($item->logo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('merchant'));
		         
		         $status = $item->status;
		         $created = t("Created {{date}}",array(
		           '{{date}}'=>Date_Formatter::dateTime($item->created)
		         )); 
		         
		         $plan_title = Yii::app()->input->xssClean($item->title);
		         $amount = Price_Formatter::formatNumber($item->amount);
		         

		         $view_merchant =  Yii::app()->createUrl('/vendor/edit',array(
				    'id'=>$item->merchant_id
				  ));
		         
$invoice = <<<HTML
<p class="m-0">$item->invoice_ref_number</p>
<div class="badge customer $item->status payment">$status</div>
HTML;

$plan = <<<HTML
<p class="m-0">$plan_title</p>
<p class="m-0 text-muted font11">$amount</p>
HTML;


        		$data[]=array(        		  
        		  'logo'=>'<a href="'.$view_merchant.'"><img class="img-60 rounded-circle" src="'.$avatar.'"></a>',
        		  'created'=>Date_Formatter::dateTime($item->created),        		  
        		  'payment_code'=>isset($payment_gateway[$item->payment_code])?$payment_gateway[$item->payment_code]:$item->payment_code,
        		  'invoice_ref_number'=>$invoice,
        		  'package_id'=>$plan,           		  
        		);
        	}
        }
        
        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
        
        $this->responseTable($datatables);
    }    
    
	public function actionbannerList()
	{
		$data = array();		
						
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
						
		$sortby = "date_created"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();
		$criteria->condition="owner=:owner AND meta_value1=:meta_value1";
		$criteria->params = array(':owner'=> 'merchant',':meta_value1'=>Yii::app()->merchant->merchant_id);

		if(!empty($search)){
			$criteria->addSearchCondition('title', $search );
			$criteria->addSearchCondition('banner_type', $search );
		}		
				
		$criteria->order = "$sortby $sort";
		$count = AR_banner::model()->count($criteria); 
		
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_banner::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {        	
				
				$photo = CMedia::getImage($item->photo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('customer'));

				 $checkbox = Yii::app()->controller->renderPartial('/attributes/html_checkbox',array(
					'id'=>"banner[$item->banner_uuid]",
					'check'=>$item->status==1?true:false,
					'value'=>$item->banner_uuid,
					'label'=>'',		
					'class'=>'set_banner_status'
				),true);

        		$data[]=array(				 
			      'banner_id'=>$item->banner_id,
				  'photo'=>'<img class="img-60" src="'.$photo.'">',
				  'status'=>$checkbox, 
				  'title'=>$item->title,				  
				  'banner_type'=>$item->banner_type,				  
        		  'date_created'=>Date_Formatter::dateTime($item->date_created),
				  'update_url'=>Yii::app()->createUrl("/merchant/banner_update/",array('id'=>$item->banner_uuid)),
        		  'delete_url'=>Yii::app()->createUrl("/merchant/banner_delete/",array('id'=>$item->banner_uuid)),				  
				);
        	}        	
        }
        
         $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);				
		$this->responseTable($datatables);
	}

	public function actionpages_list()
	{
		$data = array();		
		$status_list = AttributesTools::StatusManagement('post');
		
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
						
		$sortby = "date_created"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = intval($page)/intval($length);		
		$criteria=new CDbCriteria();
		$criteria->condition="owner=:owner AND merchant_id=:merchant_id";
		$criteria->params = array(':owner'=> 'merchant',':merchant_id'=>Yii::app()->merchant->merchant_id);

		if(!empty($search)){
			$criteria->addSearchCondition('title', $search );			
		}		
				
		$criteria->order = "$sortby $sort";
		$count = AR_pages::model()->count($criteria); 
		
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_pages::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {        	
							
				$title = '<h6>[title] <span class="badge ml-2 post [status]">[status_title]</span></h6>
				<p class="dim">[short_content]</p>';
				$title = t($title,[
					'[title]'=>$item->title,
					'[status]'=>$item->status,
					'[status_title]'=>isset($status_list[$item->status])?$status_list[$item->status]:$item->status,
					'[short_content]'=>$item->short_content,
				]);

        		$data[]=array(				 
			      'page_id'=>$item->page_id,
				  'title'=>$title,
				  'update_url'=>Yii::app()->createUrl("/merchant/page_update/",array('id'=>$item->page_id)),
        		  'delete_url'=>Yii::app()->createUrl("/merchant/pages_delete/",array('id'=>$item->page_id)),				  
				);
        	}        	
        }
        
         $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);				
		$this->responseTable($datatables);
	}

	public function actionMenuList()
    {
    	try {
    		     		
    		$data = array();
			try {
			    $data = MMenumerchant::getMenu(0,PPages::menuMerchantType(),Yii::app()->merchant->merchant_id);
			} catch (Exception $e) {
			   //	
            }
    		
    		$current_menu = AR_merchant_meta::getValue(Yii::app()->merchant->merchant_id,PPages::menuActiveKey() );
    		$current_menu = isset($current_menu['meta_value'])?$current_menu['meta_value']:0;
    		
    		$this->code = 1;
    		$this->msg = "ok";
    		$this->details = array(
    		  'data'=>$data,
    		  'current_menu'=>intval($current_menu)
    		);
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }

	public function actionAllPages()
    {
    	try {    		
    		$data = PPages::merchantPages(Yii::app()->language,Yii::app()->merchant->merchant_id);
    		$this->code = 1;
    		$this->msg = "ok";
    		$this->details = $data;
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }

    public function actiongetMenuDetails()
    {
    	try {
    		
    		$current_menu = Yii::app()->input->post('current_menu');     		
    		$model = AR_menu::model()->findByPk(intval($current_menu));
    		if($model){
    			
    			$data = array();
    			try {
    			    $data = MMenumerchant::getMenu($current_menu,PPages::menuMerchantType(),Yii::app()->merchant->merchant_id);
    			} catch (Exception $e) {
    			   //	
                }
    			
	    		$this->code = 1;
	    		$this->msg = "ok";
	    		$this->details = array(
	    		  'menu_name'=>$model->menu_name,
	    		  'sequence'=>$model->sequence,
	    		  'data'=>$data
	    		);
    		} else $this->msg = t(Helper_not_found);
    		    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }
    	
    public function actioncreateMenu()
    {
    	try {
    		    		
    		$menu_name = isset($this->data['menu_name'])?$this->data['menu_name']:'';
    		$menu_id = isset($this->data['menu_id'])?intval($this->data['menu_id']):0;
    		$child_menu = isset($this->data['child_menu'])?$this->data['child_menu']:'';
			
    		if($menu_id>0){    			 
    			 $model = MMenumerchant::get($menu_id,PPages::menuMerchantType(),Yii::app()->merchant->merchant_id);
    		} else $model = new AR_menu();    		
    		
    		$model->scenario = "theme_menu_merchant";
    		
    		$model->menu_type = PPages::menuMerchantType();
    		$model->menu_name = $menu_name;
			$model->meta_value1 = intval(Yii::app()->merchant->merchant_id);
    		$model->child_menu = $child_menu;
    		if($model->save()){
    			$this->code = 1;
		        $this->msg = t("Succesful");
    		} else $this->msg = CommonUtility::parseModelErrorToString($model->getErrors(),"<br/>");
    		
    	} catch (Exception $e) {			
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }	

    public function actionaddpagetomenu()
    {
    	try {
    		    		
    		$menu_id = isset($this->data['menu_id'])?intval($this->data['menu_id']):0;
    		$pages = isset($this->data['pages'])?$this->data['pages']:array();    		
    		if(is_array($pages) && count($pages)>=1){
    			foreach ($pages as $page_id) {
    				$page = PPages::get($page_id);    
    						
    				$model = new AR_menu();
    				$model->menu_type=PPages::menuMerchantType();
    				$model->menu_name = $page->title;
    				$model->parent_id = $menu_id;
    				$model->link = '{{site_url}}/'.$page->slug;
					$model->meta_value1 = intval(Yii::app()->merchant->merchant_id);
    				$model->save();
    			}
    		}
    		
    		$this->code = 1;
	    	$this->msg = t(Helper_success);
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }    

	public function actiondeletemenu()
    {
    	try {

    		$menu_id = intval(Yii::app()->input->post('menu_id'));  
    		
    		$model = AR_menu::model()->find("menu_id=:menu_id AND menu_type=:menu_type AND meta_value1=:meta_value1",array(
			   ':menu_id'=>intval($menu_id),
			   ':menu_type'=>PPages::menuMerchantType(),
			   ':meta_value1'=>intval(Yii::app()->merchant->merchant_id)
			 ));
			 			
			if($model){			   
			   $model->scenario = "theme_menu_merchant";		
			   $model->delete();
			   $this->code = 1;
	    	   $this->msg = t(Helper_success);
			} else $this->msg = t(Helper_not_found);
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }
    	
    public function actionaddCustomPageToMenu()
    {
    	try {
    		    		
    		$menu_id = isset($this->data['menu_id'])?intval($this->data['menu_id']):0;
    		$custom_link_text = isset($this->data['custom_link_text'])?trim($this->data['custom_link_text']):'';
    		$custom_link = isset($this->data['custom_link'])?trim($this->data['custom_link']):'';
    		
    		$model = new AR_menu();
    		$model->scenario = "custom_link";
    		$model->menu_type=PPages::menuMerchantType();
			$model->menu_name = $custom_link_text;
			$model->parent_id = $menu_id;
			$model->link = $custom_link;
			$model->meta_value1 = intval(Yii::app()->merchant->merchant_id);

			if($model->save()){
			   $this->code = 1;
	    	   $this->msg = t(Helper_success);
			} else $this->msg = CommonUtility::parseModelErrorToString($model->getErrors(),"<br/>");
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }    	

	public function actionremoveChildMenu()
    {
    	try {
    		    		
    		$menu_id = intval(Yii::app()->input->post('menu_id'));  
    		$model = MMenumerchant::get($menu_id,PPages::menuMerchantType(),intval(Yii::app()->merchant->merchant_id));
    		if($model){
    			$model->delete();
    			$this->code = 1;
	    		$this->msg = "ok";
    		} else $this->msg = t(Helper_not_found);
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }

	public function actionitemList()
	{
		$data = array();
		
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		$filter = isset($this->data['filter'])?$this->data['filter']:'';
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';		
				
		$sortby = "zone_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}

		$merchant_id = Yii::app()->merchant->merchant_id;
		
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();		
		$criteria->alias = "a";
		$criteria->select = "
		a.* ,
		(
			select GROUP_CONCAT(cat_id)
			from {{item_relationship_category}}
			where merchant_id = a.merchant_id
			and item_id = a.item_id
		) as group_category,

		(
			select GROUP_CONCAT(CONCAT_WS(';', size_id, price , discount, discount_type,discount_start,discount_end))
			from {{item_relationship_size}}
			where merchant_id = a.merchant_id
			and item_id = a.item_id
		) as prices

		";
		$criteria->condition = "a.merchant_id=:merchant_id";		
		$criteria->params = [
			':merchant_id'=>intval($merchant_id)
		];
		
		if (is_string($search) && strlen($search) > 0){
			$criteria->addSearchCondition('item_name', $search );
			//$criteria->addSearchCondition('status', $search , true , 'OR' );
		 }
				
		$criteria->order = "$sortby $sort";
		$count = AR_item::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);      
		
		$fallback_image = CommonUtility::getPlaceholderPhoto('item_photo');
                        		
        $models = AR_item::model()->findAll($criteria);
        if($models){        				
			$category_list = AttributesTools::Category($merchant_id);			
			$size_list = AttributesTools::sizeList($merchant_id,Yii::app()->language);
			$date_now = date("Y-m-d");

        	foreach ($models as $item) {   								
				$pic = CMedia::getImage($item->photo,$item->path,'@thumbnail',$fallback_image);							
				$checkbox = Yii::app()->controller->renderPartial('/attributes/html_checkbox',array(
					'id'=>"available[$item->item_id]",
					'check'=>$item->available==1?true:false,
					'value'=>$item->item_id,
					'label'=>'',
					'class'=>'set_item_available'
				  ),true);

				$category_group = '';
				$category = explode(",",$item->group_category);
				if(is_array($category) && count($category)>=1){
					foreach ($category as $cat_id) {
						if(isset($category_list[$cat_id])){
							$category_group.=$category_list[$cat_id].",";
						}						
					}
					$category_group = substr($category_group,0,-1);
				}

				$size_group = '';
				$size = explode(",",$item->prices);								
				if(is_array($size) && count($size)>=1){
					foreach ($size as $size_val) {
						$size_item = explode(";",$size_val);												
						$size_id = isset($size_item[0])?$size_item[0]:0;
						$price = isset($size_item[1])?$size_item[1]:0;
						$discount = isset($size_item[2])?$size_item[2]:0;
						$discount_type = isset($size_item[3])?$size_item[3]:'';
						$discount_start = isset($size_item[4])?$size_item[4]:'';
						$discount_end = isset($size_item[5])?$size_item[5]:'';

						$price_after_discount = 0;
						if(!empty($discount_start) && !empty($discount_end)){
							if ($date_now >= $discount_start && $date_now <=$discount_end ) {
								if($discount_type=="percentage"){
									$price_after_discount = $price - (($discount/100)*$price);
								} else $price_after_discount = $price-$discount;
							}
						}
						
						if($size_id>0){
							if(isset($size_list[$size_id])){
								if($price_after_discount>0){
									$size_group.='<p class="m-0 text-small">'.$size_list[$size_id].' <del>'.Price_Formatter::formatNumber($price) .'</del>'. Price_Formatter::formatNumber($price_after_discount) .'</p>';								
								} else $size_group.='<p class="m-0 text-small">'.$size_list[$size_id]." ".Price_Formatter::formatNumber($price).'</p>';								
							}
						} else {							
							if($price_after_discount>0){
								$size_group.='<p class="m-0 text-small"><del>'.Price_Formatter::formatNumber($price).'</del></p>';
							} else $size_group.='<p class="m-0 text-small">'.Price_Formatter::formatNumber($price).'</p>';							
						}
					}
				}

        		$data[]=array(
        		  'item_id'=>'<img src="'.CHtml::encode($pic).'" class="img-60 rounded-circle" />',  
				  'available'=>$checkbox,
				  'item_name'=>'<h6>'.$item->item_name.'<span class="badge ml-2 post '.$item->status.'">'.$item->status.'</span></h6>
				  <p class="dim">				 	   		 	   
					'. t('Last Modified. [date_modified]',[
						'[date_modified]'=>Date_Formatter::dateTime($item->date_modified)
					]) .'<br>
					'.t("SKU#[sku]",[
						'[sku]'=>$item->sku
					]).'
				  </p>
				  ',
				  'category_group'=>'<p class="font-weight-bold">'.$category_group.'</p>',
				  'price'=>$size_group,		
				  'update_url'=>Yii::app()->createUrl("/food/item_update/",array('item_id'=>$item->item_id)),
        		  'delete_url'=>Yii::app()->createUrl("/food/item_delete/",array('id'=>$item->item_id)),				  
        		);
        	}
        }

        $datatables = array(
		  'draw'=>intval($draw),
		  'recordsTotal'=>intval($count),
		  'recordsFiltered'=>intval($count),
		  'data'=>$data
		);
				
		$this->responseTable($datatables);
	}

}
/*end class*/