<?php
class AOrders
{
	public static function getOrderAll($merchant_id=0, $status=array(), $schedule=false, $date='',$datetime='',$filter=array(), $limit=100)
	{		
		
		$merchant_id = Yii::app()->merchant->merchant_id;
		$settings = OptionsTools::find(array('merchant_order_critical_mins'),$merchant_id);
		$critical_mins = isset($settings['merchant_order_critical_mins'])?$settings['merchant_order_critical_mins']:0;
		$critical_mins = intval($critical_mins);    

		/*$status_not_in = AOrderSettings::getStatus(array('status_delivered','status_completed',
              'status_cancel_order','status_rejection','status_delivery_fail','status_failed'
            ));*/
		$status_in = AOrders::getOrderTabsStatus('new_order');  
            
    		
		$criteria = new CDbCriteria;
		$criteria->select = "order_id,order_uuid,merchant_id,
		client_id,status,payment_status,service_code,formatted_address,
		whento_deliver,delivery_date,delivery_time,delivery_time_end,
		is_view,is_critical,date_created,
		(
		 select sum(qty) 
		 from {{ordernew_item}}
		 where order_id = t.order_id
		) as total_items,
		
	    IF(whento_deliver='now', 
	      TIMESTAMPDIFF(MINUTE, date_created, NOW())
	    , 
	     TIMESTAMPDIFF(MINUTE, concat(delivery_date,' ',delivery_time), NOW())
	    ) as min_diff
		
		";		
		$criteria->order = "order_id ASC";
		
		if($schedule){
			$status_scheduled = (array) $status;				
			$status_accepted = AOrders::getOrderTabsStatus('order_processing');	
			if($status_accepted){				
				foreach ($status_accepted as $status_accepted_val) {
					array_push($status_scheduled,$status_accepted_val);
				}
			}
						
			$criteria->condition = "merchant_id=:merchant_id AND whento_deliver=:whento_deliver";		    
			$criteria->params  = array(
			  ':merchant_id'=>intval($merchant_id),		  
			  ':whento_deliver'=>"schedule"
			);						
			$criteria->addInCondition('status', (array) $status_scheduled );					
			$criteria->addCondition('delivery_date > "'.$date.'" ');
		} else {
			$criteria->addCondition('merchant_id =:merchant_id');
			$criteria->params = array(':merchant_id' => intval($merchant_id) );
			$criteria->addInCondition('status',(array) $status );		
			$criteria->addSearchCondition('delivery_date', $date );
		}
		
		if(is_array($filter) && count($filter)>=1){
			
			if(isset($filter['search_filter'])){
				$search_filter = trim($filter['search_filter']);
				if(is_numeric($search_filter) && !empty($search_filter)){
				    $criteria->addSearchCondition('order_id', $search_filter );
				} else if (!empty($search_filter)) {									
					$criteria->addCondition("
					 order_id IN (
					   select order_id from {{ordernew_meta}}
					   where meta_name='customer_name'
					   and meta_value LIKE ".q("%$search_filter%")."
					 )
					");
				}
			}
			
			if(isset($filter['order_type'])){
				if(is_array($filter['order_type']) && count($filter['order_type'])>=1){					
					$criteria->addInCondition('service_code',(array) $filter['order_type'] );
				}
			}
			if(isset($filter['payment_status'])){
				if(is_array($filter['payment_status']) && count($filter['payment_status'])>=1){					
					$criteria->addInCondition('payment_status',(array) $filter['payment_status'] );
				}
			}
			if(isset($filter['sort'])){
				if(!empty($filter['sort'])){
					$sort = $filter['sort'];
					switch ($sort) {
						case "order_id_asc":		
						    $criteria->order = "order_id ASC";					
							break;											
						case "order_id_desc":
							$criteria->order = "order_id DESC";
							break;												
						case "delivery_time_asc":
							$criteria->order = "delivery_time ASC";
							break;	
						case "delivery_time_desc":
							$criteria->order = "delivery_time DESC";
							break;		
					}
				}
			}
		}
		
		$criteria->limit = $limit;
		//dump($criteria);die();
		$model=AR_ordernew::model()->findAll($criteria);				
		if($model){							
			$data = array(); $all_merchant = array(); $all_order = array();
			foreach ($model as $item) {
				$delivery_date = '';
				$all_merchant[] = $item->merchant_id;
				$all_order[] = $item->order_id;			
				if($item->whento_deliver=="now"){
			    	$delivery_date = t("Asap");
			    } else {
			    	if($item->delivery_date==$date){
			    		$date = Date_Formatter::Time( $item->delivery_date." ".$item->delivery_time );
				    	$delivery_date = t("Due at [delivery_date], Today",array(
				    	 '[delivery_date]'=>$date
				    	));
			    	} else {
				    	$date = Date_Formatter::dateTime( $item->delivery_date." ".$item->delivery_time );
				    	$delivery_date = t("Scheduled at [delivery_date]",array(
				    	 '[delivery_date]'=>$date
				    	));
			    	}
			    }
			    
			    $items = t("[item_count] items",array('[item_count]'=>$item->total_items));
			    if($item->total_items<=1){
			    	$items = t("[item_count] item",array('[item_count]'=>$item->total_items));
			    }
			    
			    $is_critical =  0;			    
			    if($item->whento_deliver=="schedule"){
		        	if($item->min_diff>0){
		        		$is_critical = true;
		        	}
		        } else if ($critical_mins>0 && $item->min_diff>$critical_mins && in_array($item->status,(array)$status_in) ) {
		        	$is_critical = true;
		        }	    
			    /*if($item->whento_deliver=="schedule"){
			    	$delivery_datetime = $item->delivery_date ." ".$item->delivery_time;			    	
			    	$delivery_datetime = date("Y-m-d g:i:s a",strtotime($delivery_datetime));			    	
			    	$diff = CommonUtility::dateDifference($delivery_datetime,$datetime);
			    	if(is_array($diff) && count($diff)>=1){
			    		$is_critical = 1;
			    	}
			    } elseif ($item->whento_deliver=="now"){			    	
			    	$delivery_datetime = date("Y-m-d g:i:s a",strtotime($item->date_created));
			    	$diff = CommonUtility::dateDifference($delivery_datetime,$datetime);
			    	if(is_array($diff) && count($diff)>=1){
			    		if($diff['hours']>0){
			    			$is_critical = 1;
			    		}
			    		if($diff['minutes']>10){
			    			$is_critical = 1;
			    		}
			    	}
			    }*/
			    
			    
				$data[]=array(
				  'order_name'=>t("Order #[order_id]",array('[order_id]'=>$item->order_id)),
				  'order_id'=>$item->order_id,
				  'order_uuid'=>$item->order_uuid,
				  'client_id'=>$item->order_id,
				  'status'=>$item->status,
				  'payment_status'=>$item->payment_status,
				  'service_code'=>$item->service_code,
				  'formatted_address'=>$item->formatted_address,
				  'delivery_date'=>$delivery_date,
				  'total_items'=>$items,
				  'is_view'=>$item->is_view,
				  'is_critical'=>$is_critical
				);
			}
			return array(
			 'data'=>$data,
			 'all_merchant'=>$all_merchant,
			 'all_order'=>$all_order,
			 'total'=>count($model)
			);
		}
		throw new Exception( 'No results' );
	}		
	
	public static function getOrderMeta($order_id=array())
	{
		$criteria = new CDbCriteria;		
		$criteria->order = "order_id ASC";				
		$criteria->addColumnCondition(array('meta_name' => 'customer_name'));
		$criteria->addInCondition('order_id', (array)$order_id );		
		$model=AR_ordernew_meta::model()->findAll($criteria);		
		if($model){
			$data = array();
			foreach ($model as $item) {
				$data[$item->order_id][$item->meta_name] = $item->meta_value;
			}
			return $data;
		}
		return false;
	}
	
	public static function getAllTabsStatus()
	{
		$new_order = AOrders::getOrderTabsStatus("new_order");
		$order_processing = AOrders::getOrderTabsStatus("order_processing");
		$order_ready = AOrders::getOrderTabsStatus("order_ready");
		$completed_today = AOrders::getOrderTabsStatus("completed_today");
		return array(
		  'new_order'=>$new_order,
		  'order_processing'=>$order_processing,
		  'order_ready'=>$order_ready,
		  'completed_today'=>$completed_today,
		);
	}
	
	public static function getOrderTabsStatus($group_name='')
	{
		$stmt="
		SELECT description as status
		FROM {{order_status}}
		WHERE 
		stats_id IN (
		 select stats_id from {{order_settings_tabs}}
		 where group_name =".q($group_name)."
		)
		";	
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {				
				array_push($data,$val['status']);
			}
			return $data;
		}
		return false;
	}
	
	public static function getOrderButtons($group_name='', $order_type='')
	{		
		$criteria = new CDbCriteria;
		$criteria->order = "id ASC";						
		if($order_type){
			$criteria->addCondition("group_name=:group_name AND order_type=:order_type");
			$criteria->params = array(
			  ':group_name'=>$group_name,
			  ':order_type'=>trim($order_type)
			);
		} else $criteria->addColumnCondition(array('group_name' => $group_name ));
		
		$model = AR_order_settings_buttons::model()->findAll($criteria);	
		if($model){
			$data = array();
			foreach ($model as $items) {
				$data[]=array(
				  'button_name'=>t($items->button_name),
				  'uuid'=>$items->uuid,
				  'class_name'=>$items->class_name.' btn-'.strtolower($items->button_name),
				  'do_actions'=>$items->do_actions,					  			 
				);
			}
			return $data;
		}
		return false;
	}
	
	public static function getOrderButtonStatus($uuid='')
	{
		$stmt="
		SELECT a.description as status
		FROM {{order_status}} a
		LEFT JOIN {{order_settings_buttons}} b
		ON
		a.stats_id = b.stats_id	
		WHERE b.uuid = ".q($uuid)."
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){			
			return $res['status'];
		}
		throw new Exception( 'no results' );
	}
	
	public static function getOrderButtonActions($uuid='')
	{
		$model = AR_order_settings_buttons::model()->find("uuid=:uuid",array(
		 ':uuid'=>$uuid
		));
		if($model){
		   return $model->do_actions;	
		}
		throw new Exception( 'no results' );
	}
	
	public static function rejectionList($meta_name='rejection_list')
	{
		$model=AR_admin_meta::model()->findAll("meta_name=:meta_name",array(
		  ':meta_name'=>$meta_name
		));
		if($model){			
			$data = array();
			foreach ($model as $items) {
				$data[] = $items->meta_value;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getOrderHistory($order_uuid='' , $lang=KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT created_at,order_id,status,change_by,
		remarks,ramarks_trans 
		FROM {{ordernew_history}}
		WHERE order_id = (
		 select order_id from {{ordernew}}
		 where order_uuid=".q($order_uuid)."
		)
		ORDER BY id DESC
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $item) {		
				$remarks = $item['remarks'];
				if(!empty($item['ramarks_trans'])){
					$ramarks_trans = json_decode($item['ramarks_trans'],true);
					$remarks = t($item['remarks'],(array)$ramarks_trans);
				}
				
				$change_by = '';
				if(!empty($item['change_by'])){
					$change_by = t("change status by {{user}}",array(
					  '{{user}}'=>Yii::app()->input->xssClean($item['change_by'])
					));
				}
				
				$data[] = array(
				  'created_at'=>Date_Formatter::dateTime($item['created_at'],"dd MMM yyyy h:mm:ss a"),
				  'status'=>$item['status'],
				  'remarks'=>$remarks,
				  'change_by'=>$change_by
				);
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getOrderCountPerStatus($merchant_id=0, $status=array() , $date = '')
	{		
		$criteria=new CDbCriteria();	    
	    $criteria->condition = "merchant_id=:merchant_id";		    
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),		  
		);
		$criteria->addInCondition('status', (array) $status );
		//$criteria->addSearchCondition('date_created', $date );
		$criteria->addSearchCondition('delivery_date', $date );
		
		$count = AR_ordernew::model()->count($criteria); 
		return intval($count);
	}
	
	public static function getOrderCountSchedule($merchant_id=0, $status=array() , $date = '')
	{		
		$criteria=new CDbCriteria();	    
	    $criteria->condition = "merchant_id=:merchant_id AND whento_deliver=:whento_deliver";		    
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),		  
		  ':whento_deliver'=>"schedule"
		);
		$criteria->addInCondition('status', (array) $status );					
		$criteria->addCondition('delivery_date > "'.$date.'" ');
		
		$count = AR_ordernew::model()->count($criteria); 		
		return intval($count);
	}
	
	public static function getAllOrderCount($merchant_id=0)
	{
		$criteria=new CDbCriteria();	    
	    $criteria->condition = "merchant_id=:merchant_id";		    
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),		  
		);
		$not_in_status = AttributesTools::initialStatus();		
		$criteria->addNotInCondition('status', (array) array($not_in_status) );		
		
		$count = AR_ordernew::model()->count($criteria); 
		return intval($count);
	}
	
	public static function OrderNotViewed($merchant_id=0, $status=array() , $date = '')
	{
		$criteria=new CDbCriteria();	    
	    $criteria->condition = "merchant_id=:merchant_id AND is_view=:is_view";		    
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),		  
		  ':is_view'=>0,		  
		);
		$criteria->addInCondition('status', (array) $status );
		$criteria->addSearchCondition('delivery_date', $date );
		
		$count = AR_ordernew::model()->count($criteria); 
		return intval($count);
	}
	
	public static function getOrdersTotal($merchant_id=0, $status=array(), $not_in_status=array() )
	{				
		$criteria=new CDbCriteria();
	    $criteria->select="order_id,order_uuid,total,status";
	    
	    if($merchant_id>0){
		    $criteria->condition = "merchant_id=:merchant_id";		    
			$criteria->params  = array(
			  ':merchant_id'=>intval($merchant_id),		  
			);
	    }
		if(is_array($status) && count($status)>=1){
			$criteria->addInCondition('status', (array) $status );
		}
		if(is_array($not_in_status) && count($not_in_status)>=1){
			$criteria->addNotInCondition('status', (array) $not_in_status );
		}		
		$count = AR_ordernew::model()->count($criteria); 
		if($count){
			return $count;
		}
		return 0;
	}
	
	public static function getOrderSummary($merchant_id=0, $status=array())
	{
		$criteria=new CDbCriteria();
		$criteria->select="sum(total) as total";
		
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id";		    
			$criteria->params  = array(
			  ':merchant_id'=>intval($merchant_id)		  
			);
		}
		$criteria->addInCondition('status', (array) $status );
		$model = AR_ordernew::model()->find($criteria); 
		if($model){
			return $model->total;
		}
		return 0;
	}
	
	public static function getTotalRefund($merchant_id=0, $status= array())
	{
		$criteria=new CDbCriteria();
		$criteria->select="sum(trans_amount) as total";
		
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id AND status=:status";		    
			$criteria->params  = array(
			  ':merchant_id'=>intval($merchant_id),
			  ':status'=>"paid"
			);		
		} else {
			$criteria->condition = "status=:status";		    
			$criteria->params  = array(			  
			  ':status'=>"paid"
			);		
		}
		
		$criteria->addInCondition('transaction_name', (array) $status );
		$model = AR_ordernew_transaction::model()->find($criteria); 		
		if($model){
			return $model->total;
		}
		return 0;
	}
}
/*end class */