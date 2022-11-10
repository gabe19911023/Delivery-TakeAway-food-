<?php
class ApiController extends CommonApi
{			

	public function beforeAction($action)
	{								
		$method = Yii::app()->getRequest()->getRequestType();
		if($method=="PUT"){
			$this->data = Yii::app()->input->xssClean(json_decode(file_get_contents('php://input'), true));
		} else $this->data = Yii::app()->input->xssClean($_POST);				
		return true;
	}
	
	public function actiongetOrderTab()
	{		
		$group_name = isset($this->data['group_name'])?$this->data['group_name']:'';				
		$criteria = new CDbCriteria;		
		$criteria->select = "group_name,stats_id";
		$criteria->order = "id ASC";
		$criteria->addCondition('group_name =:group_name');
		$criteria->params = array(':group_name' => trim($group_name) );
		$model=AR_order_settings_tabs::model()->findAll($criteria);		
		if($model){			
			$data = array();
			foreach ($model as $items) {
				array_push($data,$items->stats_id);
			}			
			$this->code = 1; $this->msg = "ok";
			$this->details = $data;
		} else $this->msg = t("No results");
		$this->responseJson();
	}
	
	public function actionsaveOrderTab()
	{				
		if(DEMO_MODE){
		  $this->msg[] = t("Modification not available in demo");
		  $this->responseJson();
        }
        
		$group_name = isset($this->data['group_name'])?$this->data['group_name']:'';
		$status = isset($this->data['status'])?$this->data['status']:'';		
		Yii::app()->db->createCommand("DELETE FROM {{order_settings_tabs}} 
		WHERE group_name=".q($group_name)." ")->query();
		if(is_array($status) && count($status)>=1){
			$params = array();
			foreach ($status as $val) {
				$params[]=array(
				  'group_name'=>$group_name,
				  'stats_id'=>intval($val),
				  'date_modified'=>CommonUtility::dateNow(),
				  'ip_address'=>CommonUtility::userIp()
				);
			}						
			try {			
				$builder=Yii::app()->db->schema->commandBuilder;
				$command=$builder->createMultipleInsertCommand("{{order_settings_tabs}}",$params);
				$command->execute();		
			} catch (Exception $e) {
			   $this->msg[] = $e->getMessage();
			   $this->responseJson();
			}	
		}
		$this->code = 1; $this->msg = t("Setting saved");
		$this->responseJson();
	}
	
	public function actionsaveOrderButtons()
	{		
		if(DEMO_MODE){
		  $this->msg[] = t("Modification not available in demo");
		  $this->responseJson();
        }
		$group_name = isset($this->data['group_name'])?$this->data['group_name']:'';
		$button_name = isset($this->data['button_name'])?$this->data['button_name']:'';
		$status = isset($this->data['status'])?$this->data['status']:'';	
		$order_type = isset($this->data['order_type'])?$this->data['order_type']:'';	
		$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';	
		$do_actions = isset($this->data['do_actions'])?$this->data['do_actions']:'';
		$class_name = isset($this->data['class_name'])?$this->data['class_name']:'';		
			
		if(!empty($uuid)){
			$model=AR_order_settings_buttons::model()->find("uuid=:uuid",array(
			  ':uuid'=>$uuid
			));
			if(!$model){
				$this->msg = t("Record not found");
				$this->responseJson();
			}
		} else $model = new AR_order_settings_buttons;	
			
		$model->group_name = $group_name;	
		$model->button_name = $button_name;
		$model->stats_id = intval($status);
		$model->order_type = trim($order_type);
		$model->do_actions = $do_actions;
		$model->class_name = $class_name;
		if($model->save()){
			$this->code = 1;
			$this->msg = "ok";
		} else $this->msg = CommonUtility::parseError( $model->getErrors());
		$this->responseJson();
	}
	
	public function actiongetOrderButtonList()
	{					
		$group_name = isset($this->data['group_name'])?$this->data['group_name']:'';
		$criteria = new CDbCriteria;
		$criteria->select = "uuid,button_name,order_type,
		(
		  select description 
		  from {{order_status_translation}}
		  where language=".q(Yii::app()->language)."
		  and stats_id = t.stats_id
		) as status		
		";
		$criteria->order = "id ASC";
		$criteria->addCondition('group_name =:group_name');
		$criteria->params = array(':group_name' => trim($group_name) );
		$model=AR_order_settings_buttons::model()->findAll($criteria);		
		if($model){
			$data = array();
			foreach ($model as $item) {
				$data[]=array(
				  'uuid'=>$item->uuid,
				  'button_name'=>$item->button_name,
				  'order_type'=>$item->order_type,
				  'status'=>$item->status
				);
			}
			$this->code = 1;
			$this->msg = "ok";
			$this->details = $data;
		} else $this->msg[] = t("No results");
		$this->responseJson();
	}
	
	public function actiondeleteButtons()
	{	
		 
		if(DEMO_MODE){
		    $this->msg[] = t("Modification not available in demo");
		    $this->responseJson();
		}
 
		$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
		$model=AR_order_settings_buttons::model()->find("uuid=:uuid",array(
		  ':uuid'=>$uuid
		));
		if($model){
			$model->delete();
			$this->code = 1;
			$this->msg = "OK";
		} else $this->msg = t("Record not found");
		$this->responseJson();
	}
	
	public function actiongetButtons()
	{
		$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
		$model=AR_order_settings_buttons::model()->find("uuid=:uuid",array(
		  ':uuid'=>$uuid
		));
		if($model){			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'uuid'=>$model->uuid,
			  'button_name'=>$model->button_name,
			  'stats_id'=>$model->stats_id,
			  'order_type'=>$model->order_type,
			  'do_actions'=>$model->do_actions,
			  'class_name'=>$model->class_name
			);
		} else $this->msg = t("Record not found");
		$this->responseJson();
	}
	
	public function actioncommissionBalance()
	{
	    try {								
	    	$card_id = CWallet::createCard( Yii::app()->params->account_type['admin']);
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
	
	public function actiontransactionHistory()
	{
		$data = array(); $card_id = 0;
		try {	
		    $card_id = CWallet::getCardID(Yii::app()->params->account_type['admin']);	
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
				
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		$criteria->addCondition('card_id=:card_id');
		$criteria->params = array(':card_id'=>intval($card_id));
		
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
				
		$this->responseTable($datatables);
	}
		
	public function actioncommissionadjustment()
	{		
		try {								
			
			$transaction_description = isset($this->data['transaction_description'])?$this->data['transaction_description']:'';
			$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';
			$transaction_amount = isset($this->data['transaction_amount'])?$this->data['transaction_amount']:0;
			
			$params = array(
			  'transaction_description'=>$transaction_description,			  
			  'transaction_type'=>$transaction_type,
			  'transaction_amount'=>floatval($transaction_amount),
			  'meta_name'=>"adjustment",
			  'meta_value'=>CommonUtility::createUUID("{{admin_meta}}",'meta_value')
			);
			
			$card_id = CWallet::createCard( Yii::app()->params->account_type['admin'] );
			CWallet::inserTransactions($card_id,$params);

			$this->code = 1; $this->msg = t("Successful");
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());	
		}	
		$this->responseJson();		
	}
	
	public function actionmerchant_earninglist()
	{
		$data = array();								
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
					
		$sortby = "restaurant_name"; $sort = 'ASC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}			
		
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "a.merchant_id, a.merchant_uuid, a.restaurant_name, a.logo, a.path,
		(
		 select running_balance 
		 from {{wallet_transactions}}
		 where card_id = (
		    select card_id from {{wallet_cards}}
		    where account_type=".q(Yii::app()->params->account_type['merchant'])." and account_id=a.merchant_id		    
		    limit 0,1
		 )
		 order by transaction_id DESC
		 limit 0,1
		) as balance			
		";
		
		$criteria->condition="status=:status";
		$criteria->params = array(
		 ':status'=>'active'
		);

		if(!empty($search)){
		    $criteria->addSearchCondition('a.restaurant_name', $search);
        }
        
		$criteria->order = "$sortby $sort";
		$count = AR_merchant::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_merchant::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {
        		
        	$balance = Price_Formatter::formatNumber($item->balance);
        	$logo_url = CMedia::getImage($item->logo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('merchant'));
        	
        	$view = Yii::app()->createUrl('earnings/transactions',array(
         	  'merchant_uuid'=>$item->merchant_uuid
         	));
         	
        		
$logo_html = <<<HTML
<img src="$logo_url" class="img-60 rounded-circle" />
HTML;

$balance_html = <<<HTML
<b>$balance</b>
HTML;


$actions_html = <<<HTML
<div class="btn-group btn-group-actions" role="group">
 <a href="$view" target="_blank" class="btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
 <a class="btn btn-light tool_tips"><i class="zmdi zmdi-money-off"></i></a>
</div>
HTML;

        	  $data[]=array(
        		'merchant_id'=>$item->merchant_id,
        		'logo'=>$logo_html,
        		'restaurant_name'=>Yii::app()->input->xssClean($item->restaurant_name),
        		'balance'=>$balance_html,
        		'merchant_uuid'=>$item->merchant_uuid,
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
			
	public function actionmerchant_transactions()
	{
		$data = array(); $card_id=0;
				
		$merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';	
		
		try {
		   $merchant = CMerchants::getByUUID($merchant_uuid);		   
		   $card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] , $merchant->merchant_id );
		} catch (Exception $e) {		   
			//		   
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
				
		$page = $page>0? intval($page)/intval($length) :0;
		
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
        		
        		$data[]=array(
        		  'transaction_date'=>Date_Formatter::date($item->transaction_date),
        		  'transaction_description'=>$description,
        		  'transaction_amount'=>$transaction_amount,
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
				
		$this->responseTable($datatables);

	}
	
	public function actiongetordersummary()
	{
		$merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';	
		$merchant = AR_merchant::model()->find("merchant_uuid=:merchant_uuid",array(
		  ':merchant_uuid'=>$merchant_uuid
		));		
		
		$merchant_id = 0;
		if($merchant){
		   
			try {	
				
		    	$merchant_id = $merchant->merchant_id;
		    	$initial_status = AttributesTools::initialStatus();
		    	$refund_status = AttributesTools::refundStatus();	
		    	$orders = 0; $order_cancel = 0; $total=0;
		    	
		    	$not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));
		    	array_push($not_in_status,$initial_status);    		    	
		    	$orders = AOrders::getOrdersTotal($merchant_id,array(),$not_in_status);
		    	
		    	$status_cancel = AOrderSettings::getStatus(array('status_cancel_order'));		    	    	
			    $order_cancel = AOrders::getOrdersTotal($merchant_id,$status_cancel);
			    
			    $status_delivered = AOrderSettings::getStatus(array('status_delivered'));			    
			    $total = AOrders::getOrderSummary($merchant_id,$status_delivered);
			    $total_refund = AOrders::getTotalRefund($merchant_id,$refund_status);
		    	
			    $logo_url = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('merchant'));
			    
		    	$data = array(
		    	 'merchant'=>array(
		    	   'name'=>$merchant->restaurant_name,
		    	   'logo_url'=>$logo_url,
		    	   'contact_phone'=>$merchant->contact_phone,
		    	   'contact_email'=>$merchant->contact_email,
		    	   'member_since'=>Date_Formatter::date($merchant->date_created),
		    	   'merchant_active'=>$merchant->status=='active'?true:false
		    	 ),
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
			
		} else $this->msg = t("Merchant not found");
		$this->responseJson();	
	}
	
	public function actionchangeMerchantStatus()
	{
		$merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';	
		$status = isset($this->data['status'])?$this->data['status']:0;	
		$merchant = AR_merchant::model()->find("merchant_uuid=:merchant_uuid",array(
		  ':merchant_uuid'=>$merchant_uuid
		));		
		if($merchant){
			$status = $status==1?'active':'blocked';
			$merchant->status = $status;
			if($merchant->save()){
				$this->code = 1;
				$this->msg = "ok";	
				$this->details = array(
				  'merchant_active'=>$status=='active'?true:false
				);
			} else $this->msg = CommonUtility::parseError( $model->getErrors());
		} else $this->msg = t("Merchant not found");
		$this->responseJson();	
	}
	
	public function actionmerchantTotalBalance()
	{
		try {								
			$balance = CEarnings::getTotalMerchantBalance();
			$this->msg = "ok";
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $balance = 0;		
		}	
				
		$this->code = 1;		
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
	
	public function actionwithdrawalList()
	{
		$data = array();								
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:'' :'';	
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';	
		$filter = isset($this->data['filter'])?$this->data['filter']:'';	
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
					
		$sortby = "a.transaction_date"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}			
		
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		
		$criteria->select="a.transaction_uuid,a.card_id,a.transaction_amount,a.transaction_date, a.status,
		b.merchant_id, b.restaurant_name , b.logo , b.path";
		
		$criteria->join="LEFT JOIN {{merchant}} b on a.card_id = 
		(
		 select card_id from {{wallet_cards}}
		 where account_type=".q(Yii::app()->params->account_type['merchant'])." and account_id=b.merchant_id
		)
		";		
		
		$criteria->condition="transaction_type=:transaction_type";
		$criteria->params = array(		 
		 ':transaction_type'=>'payout'
		);
				
		if(is_array($transaction_type) && count($transaction_type)>=1){
			$criteria->addInCondition('a.status',(array) $transaction_type );
		}
		
		if(!empty($search)){
		    $criteria->addSearchCondition('a.restaurant_name', $search);
        }
        
        if(is_array($filter) && count($filter)>=1){
        	$filter_merchant_id = isset($filter['merchant_id'])?$filter['merchant_id']:'';
        	$criteria->addSearchCondition('b.merchant_id', $filter_merchant_id );
        }
        
        if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(transaction_date,'%Y-%m-%d')", $date_start , $date_end );
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
        		        	        	
        	$logo_url = CMedia::getImage($item->logo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('merchant'));        	
        	$transaction_amount = Price_Formatter::formatNumber($item->transaction_amount);
        	$status = $item->status;
        	        	
        		
$logo_html = <<<HTML
<img src="$logo_url" class="img-60 rounded-circle" />
HTML;

$amount_html = <<<HTML
<p class="m-0"><b>$transaction_amount</b></p>
<p class="m-0"><span class="badge payment $status">$status</span></p>
HTML;


        	  $data[]=array(
        		'merchant_id'=>$item->merchant_id,        		        		
        		'logo'=>$logo_html,        		
        		'transaction_date'=>Date_Formatter::date($item->transaction_date),
        		'restaurant_name'=>Yii::app()->input->xssClean($item->restaurant_name),        		
        		'transaction_amount'=>$amount_html,
        		'transaction_uuid'=>$item->transaction_uuid,
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

	public function actiongetPayoutDetails()
	{
		try {
			
			$merchant = array(); $merchant_id = 0;
		    $transaction_uuid = isset($this->data['transaction_uuid'])?$this->data['transaction_uuid']:'';		    		    
		    $data = CPayouts::getPayoutDetails($transaction_uuid);
		    $provider = AttributesTools::paymentProviderDetails($data['provider']);		    
		    $card_id = isset($data['card_id'])?$data['card_id']:'';		    
		    try{
		       $merchant_id = CWallet::getAccountID($card_id);		    
		       $merchant_data = CMerchants::get($merchant_id);
			   $merchant = array(
			      'restaurant_name'=>Yii::app()->input->xssClean($merchant_data->restaurant_name)
			   );
		    } catch (Exception $e) {
		    	//
		    }
		    
		    $this->code = 1;
		    $this->msg = "ok";
		    $this->details = array(
		      'data'=>$data,
		      'merchant'=>$merchant,
		      'provider'=>$provider
		    );		    
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   		   
		}	
		$this->responseJson();
	}
	
	public function actionpayoutPaid()
	{
		try {			
			$transaction_uuid = isset($this->data['transaction_uuid'])?$this->data['transaction_uuid']:'';
			$model = AR_wallet_transactions::model()->find("transaction_uuid=:transaction_uuid",array(
			 ':transaction_uuid'=>$transaction_uuid
			));			
			if($model){
				$model->scenario = "payout_paid";
				$model->status = 'paid';
				if($model->save()){
					$this->code = 1;
					$this->msg = t("Payout status set to paid");
				} else $this->msg = CommonUtility::parseError( $model->getErrors());
			} else $this->msg = t("Transaction not found");
			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   		   
		}	
		$this->responseJson();
	}
	
	public function actioncancelPayout()
	{
		try {
			
			$transaction_uuid = isset($this->data['transaction_uuid'])?$this->data['transaction_uuid']:'';
			/*$model = AR_merchant_earnings::model()->find("transaction_uuid=:transaction_uuid",array(
			 ':transaction_uuid'=>$transaction_uuid
			));	*/		
			$model = AR_wallet_transactions::model()->find("transaction_uuid=:transaction_uuid",array(
			 ':transaction_uuid'=>$transaction_uuid
			));			
			if($model){							
				$params = array(				  
				  'transaction_description'=>"Cancel payout reference #{{transaction_id}}",
				  'transaction_description_parameters'=>array('{{transaction_id}}'=>$model->transaction_id),					  
				  'transaction_type'=>"credit",
				  'transaction_amount'=>floatval($model->transaction_amount),				  
				);					
				$model->scenario = "payout_cancel";
				$model->status="cancelled";			
				if($model->save()){
				   CWallet::inserTransactions($model->card_id,$params);	
				   $this->code = 1;
				   $this->msg = t("Payout cancelled");
				} else $this->msg = CommonUtility::parseError( $model->getErrors());
								
				/*CMerchantEarnings::saveTransaction($params);				
				$model->scenario = "payout_cancel";
				$model->status="cancelled";
				if($model->save()){
					$this->code = 1;
					$this->msg = t("Payout cancelled");
				} else $this->msg = CommonUtility::parseError( $model->getErrors());*/
			} else $this->msg = t("Transaction not found");
			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   		   
		}	
		$this->responseJson();
	}
	
	public function actionapprovedPayout()
	{
		try {			
			$transaction_uuid = isset($this->data['transaction_uuid'])?$this->data['transaction_uuid']:'';
			/*$model = AR_merchant_earnings::model()->find("transaction_uuid=:transaction_uuid",array(
			 ':transaction_uuid'=>$transaction_uuid
			));	*/		
			$model = AR_wallet_transactions::model()->find("transaction_uuid=:transaction_uuid",array(
			 ':transaction_uuid'=>$transaction_uuid
			));			
			if($model){				
				$model->scenario = "payout_paid";
				$model->status="paid";
				if($model->save()){					
					$this->code = 1; $this->msg = t("Payout will process in a minute or two");
				} else $this->msg = CommonUtility::parseError( $model->getErrors());
			} else $this->msg = t("Transaction not found");
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   		   
		}	
		$this->responseJson();
		
	}
	
	public function actionpayoutSummary()
	{
		try {
			
			$data = CPayouts::payoutSummary();
			$this->code = 1;
			$this->msg = "ok";
			$this->details = array(
			  'summary'=>$data,
			  'price_format'=>array(
		         'symbol'=>Price_Formatter::$number_format['currency_symbol'],
		         'decimals'=>Price_Formatter::$number_format['decimals'],
		         'decimal_separator'=>Price_Formatter::$number_format['decimal_separator'],
		         'thousand_separator'=>Price_Formatter::$number_format['thousand_separator'],
		         'position'=>Price_Formatter::$number_format['position'],
		      )
			);
			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   		   
		}	
		$this->responseJson();
	}
	
	public function actionsearchMerchant()
    {     	 
    	 $search = isset($this->data['search'])?$this->data['search']:''; 
    	 $data = array();
    	 
    	 $criteria=new CDbCriteria();    	 
    	 $criteria->condition = "status=:status";
    	 $criteria->params = array(
    	   ':status'=>'active'
    	 );
    	 if(!empty($search)){
			$criteria->addSearchCondition('restaurant_name', $search );			
		 }
		 $criteria->limit = 10;
		 if($models = AR_merchant::model()->findAll($criteria)){		 	
		 	foreach ($models as $val) {
		 		$data[]=array(
				  'id'=>$val->merchant_id,
				  'text'=>Yii::app()->input->xssClean($val->restaurant_name)
				);
		 	}
		 }
		 
		$result = array(
    	  'results'=>$data
    	);	    	
    	$this->responseSelect2($result);
    }    
    
    public function actionmerchantEarningAdjustment()
    {
    	try {								
			    		
    		$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:0;    		
    		$card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] , $merchant_id);
    		
			$transaction_description = isset($this->data['transaction_description'])?$this->data['transaction_description']:'';
			$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';
			$transaction_amount = isset($this->data['transaction_amount'])?$this->data['transaction_amount']:0;
			
			$params = array(
			  'card_id'=>intval($card_id),
			  'transaction_description'=>$transaction_description,			  
			  'transaction_type'=>$transaction_type,
			  'transaction_amount'=>floatval($transaction_amount),
			  'meta_name'=>"adjustment",
			  'meta_value'=>CommonUtility::createUUID("{{admin_meta}}",'meta_value')
			);		
			
			CWallet::inserTransactions($card_id,$params);
			$this->code = 1; $this->msg = t("Succesful");
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());	
		}	
		$this->responseJson();		
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
    	 $data = array();
    	 
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
    
	public function actionallOrders()
	{
    	$data = array();		
    	$status = COrders::statusList(Yii::app()->language);    	
    	$services = COrders::servicesList(Yii::app()->language);
    	$payment_gateway = AttributesTools::PaymentProvider();
    	    			
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
				
		$page = $page>0? intval($page)/intval($length) :0;
		
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "a.order_id, a.client_id, a.status, a.order_uuid , a.merchant_id,
		a.payment_code, a.service_code,a.total, a.date_created,
		b.meta_value as customer_name, 
		c.restaurant_name, c.logo, c.path,
		(
		   select sum(qty)
		   from {{ordernew_item}}
		   where order_id = a.order_id
		) as total_items
		";
		$criteria->join='
		LEFT JOIN {{ordernew_meta}} b on  a.order_id = b.order_id 
		LEFT JOIN {{merchant}} c on  a.merchant_id = c.merchant_id 
		';
		
		$criteria->condition = "meta_name=:meta_name ";
		$criteria->params  = array(		  
		  ':meta_name'=>'customer_name'
		);
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(a.date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		$initial_status = AttributesTools::initialStatus();
		$criteria->addNotInCondition('a.status', (array) array($initial_status) );
		
		if(is_array($filter) && count($filter)>=1){
			$filter_merchant_id = isset($filter['merchant_id'])?$filter['merchant_id']:'';
		    $filter_order_status = isset($filter['order_status'])?$filter['order_status']:'';
		    $filter_order_type = isset($filter['order_type'])?$filter['order_type']:'';
		    $filter_client_id = isset($filter['client_id'])?intval($filter['client_id']):'';
		    
		    if($filter_merchant_id>0){
		    	$criteria->addSearchCondition('a.merchant_id', $filter_merchant_id );
		    }		    
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
         	
         	$logo_url = CMedia::getImage($item->logo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('merchant'));
         	
         	$payment_name = isset($payment_gateway[$item->payment_code])?$payment_gateway[$item->payment_code]:$item->payment_code;
         	         	
         	
$logo_html = <<<HTML
<img src="$logo_url" class="img-60 rounded-circle" />
HTML;


$information = <<<HTML
$item->total_items<span class="ml-2 badge order_status $item->status">$status_trans</span>
<p class="dim m-0">$payment_name</p>
<p class="dim m-0">$order_type</p>
<p class="dim m-0">$total</p>
<p class="dim m-0">$place_on</p>
HTML;


         		$data[]=array(
         		  'merchant_id'=>$logo_html,
        		  'order_id'=>$item->order_id,
        		  'restaurant_name'=>$item->restaurant_name,
        		  'client_id'=>$item->customer_name,
        		  'status'=>$information,
        		  'order_uuid'=>$item->order_uuid,
        		  'view_order'=>Yii::app()->createAbsoluteUrl('/order/view',array('order_uuid'=>$item->order_uuid)),
        		  'view_pdf'=>Yii::app()->createAbsoluteUrl('/preprint/pdf',array('order_uuid'=>$item->order_uuid)),
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
	
	public function actiongetNotifications()
	{
		try {								
			$data = CNotificationData::getList( Yii::app()->params->realtime['admin_channel']  );			
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
			 ':notication_channel'=>Yii::app()->params->realtime['admin_channel']
			));
			$this->code = 1; $this->msg = "ok";
						
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
				
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		$criteria->condition="notication_channel=:notication_channel";
		$criteria->params = array(':notication_channel'=>Yii::app()->params->realtime['admin_channel']);
		
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
			   $user_settings = CNotificationData::getUserSettings(Yii::app()->user->id,'admin');		
			} catch (Exception $e) {
			   //
			}
			
			$data = array(
			  'enabled'=>$enabled,
			  'provider'=>$provider,
			  'pusher_instance_id'=>$pusher_instance_id,			  
			  'onesignal_app_id'=>$onesignal_app_id,
			  'safari_web_id'=>'',
			  'channel'=>Yii::app()->params->realtime['admin_channel'],
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
	
	public function actiongetwebnotifications()
	{
		try {
			
			$data = CNotificationData::getUserSettings(Yii::app()->user->id,'admin');
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
		      ':user_id'=>intval(Yii::app()->user->id),
		      ':user_type'=>"admin"
		    ));
		    if(!$model){
		       $model = new AR_device;			       
		    } 		    		    
		    $model->interest = $interest;
		    $model->user_type = 'admin';
	    	$model->user_id = intval(Yii::app()->user->id);
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
	
	public function actionupdatewebdevice()
	{
		try {
						
			$device_id = isset($this->data['device_id'])?$this->data['device_id']:'';
			
			$model = AR_device::model()->find("user_id=:user_id AND user_type=:user_type",array(
		      ':user_id'=>intval(Yii::app()->user->id),
		      ':user_type'=>"admin"
		    ));
		    if($model){
		    	$model->scenario = "update_device_token";
		    	$model->device_token = $device_id;
		    	if($model->save()){
			    	$this->code = 1;
				    $this->msg = t("device updated");		    
		    	} else $this->msg = CommonUtility::parseError( $model->getErrors());
		    } else $this->msg = t("user device not found");
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
	
	public function actionpushlogs()
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
				
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		if(!empty($search)){
			$criteria->addSearchCondition('platform', $search );
			$criteria->addSearchCondition('body', $search , true , 'OR' );
			$criteria->addSearchCondition('channel_device_id', $search , true , 'OR' );
		 }
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		
		$criteria->order = "$sortby $sort";
		$count = AR_push::model()->count($criteria); 
		
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_push::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {
        		        		
        		$data[]=array(				 
        		  'date_created'=>Date_Formatter::dateTime($item->date_created),
				  'platform'=>$item->platform,				  
				  'body'=>'<div class="text-truncate" style="max-width:200px;">'.Yii::app()->input->purify($item->body).'</div>',
				  'channel_device_id'=>$item->channel_device_id,		
				  'delete_url'=>Yii::app()->createUrl("/notifications/delete_push/",array('id'=>$item->push_uuid)),		  
				  'view_id'=>$item->push_uuid,
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
		
	public function actiongetOrderStatusList()
	{		
		if ($data = AttributesTools::getOrderStatusList(Yii::app()->language)){
			$this->code =1; $this->msg = "ok";
			$this->details = $data;
		} else $this->msg = t("No results");
		$this->responseJson();	
	}
	
	public function actiongetGroupname()
	{
		try {
						
			$group_name=''; $modify_order = false;	$filter_buttons = false;		
		    $order_uuid = isset($this->data['order_uuid'])?$this->data['order_uuid']:'';			    
		    
		    try {
		        $model = COrders::get($order_uuid);		    		    		    
			    $group_name = AOrderSettings::getGroup($model->status);		    
			    if($group_name=="new_order"){
					$modify_order = true;
				}
				if($group_name=="order_ready"){
					$filter_buttons = true;
				}
			} catch (Exception $e) {
		    	//
            }            
			
			$manual_status = isset(Yii::app()->params['settings']['enabled_manual_status'])?Yii::app()->params['settings']['enabled_manual_status']:false;
			
			$merchant_uuid='';
			try {
			  $merchant = CMerchants::get($model->merchant_id);
			  $merchant_uuid = $merchant->merchant_uuid;
			} catch (Exception $e) {
			   
			}
						
			$data = array(
			  'client_id'=>$model->client_id,
			  'merchant_id'=>$model->merchant_id,
			  'merchant_uuid'=>$merchant_uuid,
			  'group_name'=>$group_name,
			  'manual_status'=>$manual_status,
			  'modify_order'=>false,
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
		    $summary_total = COrders::getSummaryTotal();
		    
		    $summary_changes = array(); $summary_transaction = array();
		    if($modify_order==1){
		       $summary_changes = COrders::getSummaryChanges();
		    } else $summary_transaction = COrders::getSummaryTransaction();

		    $total_order = CMerchants::getTotalOrders($merchant_id);	
		    $merchant_info['order_count'] = $total_order;	    
		    		    
		    $order = COrders::orderInfo(Yii::app()->language, date("Y-m-d") );		
		    $order_type = isset($order['order_info'])?$order['order_info']['order_type']:'';
		    $client_id = $order?$order['order_info']['client_id']:0;		
		    $order_id = $order?$order['order_info']['order_id']:'';		    
		    $customer = COrders::getClientInfo($client_id);				    
		    
			$origin_latitude = $order?$order['order_info']['latitude']:'';
			$origin_longitude = $order?$order['order_info']['longitude']:'';    		    
			$delivery_direction = isset($merchant_info['restaurant_direction'])?$merchant_info['restaurant_direction']:'';
			if($order_type=="delivery"){
				$delivery_direction = isset($merchant_info['restaurant_direction'])?$merchant_info['restaurant_direction']:'';
				$delivery_direction.="&origin="."$origin_latitude,$origin_longitude";
			} 
			$order['order_info']['delivery_direction'] = $delivery_direction;			
		    		    
		    $draft = AttributesTools::initialStatus();
		    $not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));
		    array_push($not_in_status,$draft);    				   		   
		    $orders = ACustomer::getOrdersTotal($client_id,0,array(), (array)$not_in_status );		    
		    $customer['order_count'] = $orders;
		    
		    		    
		    $buttons = array(); $link_pdf = '';  $print_settings = array(); $payment_history = array();
		    
		    if(in_array('buttons',(array)$payload)){		 
		      if($filter_buttons){
		      	 $buttons = AOrders::getOrderButtons($group_name,$order_type);
		      } else $buttons = AOrders::getOrderButtons($group_name);		      
		    }
		    		    
		    if(in_array('print_settings',(array)$payload)){
			    $link_pdf = array(
			      'pdf_a4'=>Yii::app()->CreateUrl("preprint/pdf",array('order_uuid'=>$order_uuid,'size'=>"a4")),
			      'pdf_receipt'=>Yii::app()->CreateUrl("preprint/pdf",array('order_uuid'=>$order_uuid,'size'=>"thermal")),
			    );		    
			    $print_settings = AOrderSettings::getPrintSettings();
		    }
		    		    
		    if(in_array('payment_history',(array)$payload)){    
		       $payment_history = COrders::paymentHistory($order_id);
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
		    		    		    		   
		    $model = COrders::get($order_uuid);
		    $model->is_view = 1;
		    $model->save();		  
		    		        
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   		    
		}			
		$this->responseJson();
	}	
	
	public function actiongetCustomerDetails()
	{
		try {
					   
		   $client_id = isset($this->data['client_id'])?intval($this->data['client_id']):0;		   	  
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
	
	public function actiongetCustomerSummary()
	{
		try {		  
					    			
		    $client_id = isset($this->data['client_id'])?$this->data['client_id']:0;		    
		    //$merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
		    $merchant_id = 0;

		    $draft = AttributesTools::initialStatus();			    
		    
		    $not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));
		    array_push($not_in_status,$draft);    				   		   
		    $orders = ACustomer::getOrdersTotal($client_id,$merchant_id,array(), (array)$not_in_status );
		    
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
	
	public function actiongetCustomerOrders()
	{
		
		$data = array();				
		//$merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
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
		
		
		$page = $page>0? intval($page)/intval($length) :0;
		
		$initial_status = AttributesTools::initialStatus();
		$status = COrders::statusList(Yii::app()->language);		
					
		$criteria=new CDbCriteria();	
		$criteria->alias = "a";
		$criteria->select="a.order_id,a.order_uuid,a.total,a.status, b.restaurant_name";
		$criteria->join='LEFT JOIN {{merchant}} b on  a.merchant_id=b.merchant_id ';
		/*$criteria->condition = "merchant_id=:merchant_id AND client_id=:client_id ";
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),
		  ':client_id'=>intval($client_id)
		);*/
		$criteria->condition = "client_id=:client_id ";
		$criteria->params  = array(		  
		  ':client_id'=>intval($client_id)
		);
		$criteria->order = "$sortby $sort";
		
		if (is_string($search) && strlen($search) > 0){
		   $criteria->addSearchCondition('order_id', $search );
		   $criteria->addSearchCondition('a.status', $search , true , 'OR' );
		}
		$criteria->addNotInCondition('a.status', array($initial_status) );
				
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
        	Yii::app()->createUrl('/order/view',array('order_uuid'=>$val->order_uuid))
        	,$buttons);
        	
        	$_buttons = str_replace("{{print_pdf}}",
        	Yii::app()->createUrl('/preprint/pdf',array('order_uuid'=>$val->order_uuid))
        	,$_buttons);
        	
        	$data[]=array(
        	 'order_id'=>$val->order_id,
        	 'restaurant_name'=>Yii::app()->input->xssClean($val->restaurant_name),
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
				
		$this->responseTable($datatables);
	}	
	
public function actionblockCustomer()
	{
		try {
						
			$meta_name = 'block_customer';
									
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
	
	public function actiongetAllOrderSummary()
	{
		try {	
					    	
	    	$initial_status = AttributesTools::initialStatus();
	    	$refund_status = AttributesTools::refundStatus();	
	    	$orders = 0; $order_cancel = 0; $total=0;
	    	
	    	$not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));
	    	array_push($not_in_status,$initial_status);    		    	
	    	$orders = AOrders::getOrdersTotal(0,array(),$not_in_status);
	    	
	    	$status_cancel = AOrderSettings::getStatus(array('status_cancel_order'));		    	    	
		    $order_cancel = AOrders::getOrdersTotal(0,$status_cancel);
		    
		    $status_delivered = AOrderSettings::getStatus(array('status_delivered'));			    
		    $total = AOrders::getOrderSummary(0,$status_delivered);
		    $total_refund = AOrders::getTotalRefund(0,$refund_status);
	    	
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
	
	public function actionplans_features()
	{
		
		$ref_id = isset($this->data['ref_id'])?$this->data['ref_id']:0;	
		$page = isset($this->data['start'])?$this->data['start']:0;	
		$length = isset($this->data['length'])?$this->data['length']:0;	
		$draw = isset($this->data['draw'])?$this->data['draw']:0;	
		$search = isset($this->data['search'])?$this->data['search']['value']:'';	
		$columns = isset($this->data['columns'])?$this->data['columns']:'';
		$order = isset($this->data['order'])?  isset($this->data['order'][0])?$this->data['order'][0]:''   :'';	
		
		$date_start = isset($this->data['date_start'])?$this->data['date_start']:'';
		$date_end = isset($this->data['date_end'])?$this->data['date_end']:'';
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
				
		$data = array();
		$sortby = "meta_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
				
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		$criteria->addCondition('meta_name=:meta_name AND meta_value1=:meta_value1');
		$criteria->params = array(':meta_name'=>'plan_features', ':meta_value1'=>$ref_id );		
		
		$criteria->order = "$sortby $sort";
		$count = AR_admin_meta::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
        $models = AR_admin_meta::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {        		
        		$data[]=array(
        		  'meta_id'=>$item->meta_id,
        		  'meta_value'=>$item->meta_value,
        		  'update_url'=>Yii::app()->createUrl("/plans/feature_update/",array('id'=>$item->meta_value1,'meta_id'=>$item->meta_id)),
        		  'delete_url'=>Yii::app()->createUrl("/plans/feature_delete/",array('id'=>$item->meta_value1,'meta_id'=>$item->meta_id)),
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
	
	
	public function actioncustomerOrderList()
	{
		$data = array();
		$status = COrders::statusList(Yii::app()->language);    	
        $services = COrders::servicesList(Yii::app()->language);
        $payment_gateway = AttributesTools::PaymentProvider();
		
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
		$client_id = isset($this->data['ref_id'])?$this->data['ref_id']:'';		
				
		$sortby = "order_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
		
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "a.order_id, a.client_id, a.status, a.order_uuid , a.merchant_id,
		a.payment_code, a.service_code,a.total, a.date_created,
		b.meta_value as customer_name, 
		c.restaurant_name, c.logo, c.path,
		(
		   select sum(qty)
		   from {{ordernew_item}}
		   where order_id = a.order_id
		) as total_items
		";
		$criteria->join='
		LEFT JOIN {{ordernew_meta}} b on  a.order_id = b.order_id 
		LEFT JOIN {{merchant}} c on  a.merchant_id = c.merchant_id 
		';
		
		$criteria->condition = "a.client_id=:client_id AND b.meta_name=:meta_name ";
		$criteria->params  = array(		  
		  ':client_id'=>intval($client_id),
		  ':meta_name'=>'customer_name'
		);    
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(a.date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		
		if(is_array($filter) && count($filter)>=1){
	        $filter_merchant_id = isset($filter['merchant_id'])?$filter['merchant_id']:'';
	        $filter_order_status = isset($filter['order_status'])?$filter['order_status']:'';
	        $filter_order_type = isset($filter['order_type'])?$filter['order_type']:'';
	        $filter_client_id = isset($filter['client_id'])?intval($filter['client_id']):'';
	        
	        if($filter_merchant_id>0){
	            $criteria->addSearchCondition('a.merchant_id', $filter_merchant_id );
	        }		    
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
		         
		        $logo_url = CMedia::getImage($item->logo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('merchant'));
		         
		        $payment_name = isset($payment_gateway[$item->payment_code])?$payment_gateway[$item->payment_code]:$item->payment_code;		        
		        

$logo_html = <<<HTML
<img src="$logo_url" class="img-60 rounded-circle" />
HTML;


$information = <<<HTML
$item->total_items<span class="ml-2 badge order_status $item->status">$status_trans</span>
<p class="dim m-0">$payment_name</p>
<p class="dim m-0">$order_type</p>
<p class="dim m-0">$total</p>
<p class="dim m-0">$place_on</p>
HTML;
	
        		$data[]=array(
        		  'merchant_id'=>$logo_html,        		  
        		  'client_id'=>$information,
        		  'order_id'=>$item->order_id,
        		  'restaurant_name'=>$item->restaurant_name,
        		  'order_uuid'=>$item->order_uuid,
        		  'view_order'=>Yii::app()->createAbsoluteUrl('/order/view',array('order_uuid'=>$item->order_uuid)),
        		  'view_pdf'=>Yii::app()->createAbsoluteUrl('/preprint/pdf',array('order_uuid'=>$item->order_uuid)),
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
	
	
	public function actionzoneList()
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
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';				
				
		$sortby = "zone_id"; $sort = 'DESC';
		
		if(is_array($order) && count($order)>=1){
			if(array_key_exists($order['column'],(array)$columns)){			
				$sort = $order['dir'];
				$sortby = $columns[$order['column']]['data'];
			}
		}
		
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();			
		$criteria->condition = "merchant_id=0";		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
				
		$criteria->order = "$sortby $sort";
		$count = AR_zones::model()->count($criteria); 
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);        
                        
        $models = AR_zones::model()->findAll($criteria);
        if($models){        	
        	foreach ($models as $item) {    
        		$data[]=array(
        		  'zone_id'=>$item->zone_id,
        		  'zone_name'=>$item->zone_name,
        		  'description'=>$item->description,
        		  'zone_id'=>$item->zone_id,
        		  'update_url'=>Yii::app()->createUrl("/attributes/zone_update/",array('id'=>$item->zone_uuid)),
        		  'delete_url'=>Yii::app()->createUrl("/attributes/zone_delete/",array('id'=>$item->zone_uuid)),
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

	public function actiondashboardSummary()
	{
		try {
			
			$balance = 0;
			$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
			
			$total_sales = CReports::totalSales($status_completed);
			$total_merchant = CReports::totalMerchant(array('active'));			
			$total_subscriptions = CReports::totalSubscriptions();			
			
		    try {								
	           $card_id = CWallet::createCard( Yii::app()->params->account_type['admin']);
	           $balance = CWallet::getBalance($card_id);
	        } catch (Exception $e) {
	           //
	        }	
			
			$data = array(
			  'total_sales'=>intval($total_sales),
			  'total_merchant'=>intval($total_merchant),
			  'total_commission'=>floatval($balance),
			  'total_subscriptions'=>floatval($total_subscriptions),
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
	
	public function actioncommissionSummary()
	{
		try {
			
			$card_id = 0;
			try {								
	           $card_id = CWallet::createCard( Yii::app()->params->account_type['admin']);	           
	        } catch (Exception $e) {
	           //
	        }	
	        	        
	        $commission_week = CReports::WalletEarnings($card_id);	        
	        $commission_month = CReports::WalletEarnings($card_id,30);
	        $subscription_month = CReports::PlansEarning(30);
	        
	        $data = array(
	          'commission_week'=>floatval($commission_week),
	          'commission_month'=>floatval($commission_month),
	          'subscription_month'=>floatval($subscription_month),
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
	
    public function actiongetLastTenOrder()
    {
    	try {
        
    		$data = array(); $order_status = array(); $datetime=date("Y-m-d g:i:s a");    		
    		$filter_by = Yii::app()->input->post('filter_by'); 
    		$limit = Yii::app()->input->post('limit'); 
    		    		  
    		if($filter_by!="all"){
	    		$order_status = AOrders::getOrderTabsStatus($filter_by);			    		
    		}
    				    		
    		$status = COrders::statusList(Yii::app()->language);    	
            $services = COrders::servicesList(Yii::app()->language);
            $payment_status = COrders::paymentStatusList2(Yii::app()->language,'payment');  
            $status_not_in = AOrderSettings::getStatus(array('status_delivered','status_completed',
              'status_cancel_order','status_rejection','status_delivery_fail','status_failed'
            ));						
            $payment_list = AttributesTools::PaymentProvider();	                           
                        
    		$criteria=new CDbCriteria();
		    $criteria->alias = "a";
		    $criteria->select = "a.order_id, a.order_uuid, a.client_id, a.status, a.order_uuid , a.merchant_id,
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
		    ) as total_items,
		    
		    c.restaurant_name
		    
		    ";
		    $criteria->join='
		    LEFT JOIN {{ordernew_meta}} b on a.order_id = b.order_id 
		    LEFT JOIN {{merchant}} c on a.merchant_id = c.merchant_id 
		    ';
		    $criteria->condition = "b.meta_name=:meta_name ";
		    $criteria->params  = array(		      
		      ':meta_name'=>'customer_name'
		    );
		    
		    if(is_array($order_status) && count($order_status)>=1){
		    	$criteria->addInCondition('a.status',(array) $order_status );
		    } else {
		    	$draft = AttributesTools::initialStatus();		    	
		    	$criteria->addNotInCondition('a.status', array($draft) );
            }
		    
		    $criteria->order = "a.date_created DESC";		    
		    $criteria->limit = intval($limit);
		    
		    PrettyDateTime::$category='backend';
		    		    		    		    
		    $models = AR_ordernew::model()->findAll($criteria);  		    
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
			        if($item->whento_deliver=="schedule"){
			        	if($item->min_diff>0){
			        		$is_critical = true;
			        	}
			        } else if ($item->min_diff>10 && !in_array($item->status,(array)$status_not_in) ) {
			        	$is_critical = true;
			        }
			        
			        
		    		$data[]=array(
		    		  'order_id'=>$item->order_id,
		    		  'order_id'=>t("Order #{{order_id}}",array('{{order_id}}'=>$item->order_id)),
		    		  'restaurant_name'=>Yii::app()->input->xssClean($item->restaurant_name),
		    		  'order_uuid'=>$item->order_uuid,
		    		  'client_id'=>$item->client_id,
		    		  'customer_name'=>Yii::app()->input->xssClean($item->customer_name),
		    		  'status'=>$status_trans,
		    		  'status_raw'=>str_replace(" ","_",$item->status),
		    		  'order_type'=>$trans_order_type,
		    		  'payment_code'=>$item->payment_code,
		    		  'total'=>Price_Formatter::formatNumber($item->total),
		    		  'payment_status'=>$payment_status_name,
		    		  'payment_status_raw'=>str_replace(" ","_",$item->payment_status),		    		  
			          'is_view'=>$item->is_view,
			          'is_critical'=>$is_critical,
			          'min_diff'=>$item->min_diff,
			          'whento_deliver'=>$item->whento_deliver,
			          'delivery_date'=>$item->delivery_date,
			          'delivery_time'=>$item->delivery_time,
			          'view_order'=>Yii::app()->createAbsoluteUrl('/order/view',array('order_uuid'=>$item->order_uuid)),
        		      'print_pdf'=>Yii::app()->createAbsoluteUrl('/preprint/pdf',array('order_uuid'=>$item->order_uuid)),
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
			) as category_name,
			
			m.restaurant_name
			";
			$criteria->join='
			LEFT JOIN {{item}} b on  a.item_id = b.item_id 
			LEFT JOIN {{ordernew}} c on a.order_id = c.order_id 
			LEFT JOIN {{merchant}} m on c.merchant_id = m.merchant_id 
			';						
						
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
		       	    'restaurant_name'=>Yii::app()->input->xssClean($item->restaurant_name),
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
    
    public function actionitemSales()
    {
    	try {
    		    		
    		$data = array();  $items = array(); $data = array();
    		$period = Yii::app()->input->post('period'); 
    		
    		$data = CReports::ItemSales(0,$period);
    		
    		try {
    		   $items = CReports::popularItems(0,$period);
    		} catch (Exception $e) {
    			//
    		}
    		
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
    
    public function actionsalesOverview()
    {
    	try {
    	
    		$data = array();
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
    
    public function actionmostPopularCustomer()
    {
    	try {
    		
    		$data = array();		
			$limit = Yii::app()->input->post('limit'); 
			$not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));		    
			
			$criteria=new CDbCriteria();
			$criteria->alias = "a";
			$criteria->select="a.client_id, count(*) as total_sold,
			b.first_name,b.last_name,b.date_created, b.avatar as logo, b.path
			";
			$criteria->join='LEFT JOIN {{client}} b on  a.client_id=b.client_id ';
			
			$criteria->condition = "b.client_id IS NOT NULL";			
			
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
	
    public function actionOverviewReview()
    {
    	try {
    	
    	    $data = array(); $total = 0;
    		$merchant_id = 0;
    		
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
    		  'link_to_review'=>Yii::app()->createAbsoluteUrl('/buyer/review_list')
    		);    	
    		
    		$this->code = 1; $this->msg = "ok";
		    $this->details = $data;
		        
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   
		}
		$this->responseJson();	
    }
    
    public function actionpopularMerchant()
    {
    	try {
    		
    		$limit = Yii::app()->input->post('limit'); 
    		$data = CReports::PopularMerchant($limit);
    		$this->code = 1; $this->msg = "ok";
		    $this->details = $data;		    		    
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   
		}
		$this->responseJson();	
    }
    
    public function actionPopularMerchantByReview()
    {
    	try {
    		
    		$limit = Yii::app()->input->post('limit'); 
    		$data = CReports::PopularMerchantByReview($limit);
    		$cuisine_list = AttributesTools::cuisineGroup(Yii::app()->language);
    		
    		$this->code = 1; $this->msg = "ok";
		    $this->details = array(
		      'data'=>$data,
		      'cuisine_list'=>$cuisine_list,
		    );		    		    
		    
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionDailyStatistic()
    {    
    	try	{
    		
    	    $status_new = AOrderSettings::getStatus(array('status_new_order'));								
    	    $status_delivered = AOrderSettings::getStatus(array('status_delivered','status_completed'));								
    	    
    		$order_received = CReports::OrderTotalByStatus(0,$status_new);
    		$today_delivered = CReports::OrderTotalByStatus(0,$status_delivered);
    		$new_customer = CReports::CustomerTotalByStatus(1);
    		$total_refund = CReports::TotalRefund();
    		
    		$data = array(
    		  'order_received'=>$order_received,
    		  'today_delivered'=>$today_delivered,
    		  'new_customer'=>$new_customer,
    		  'total_refund'=>$total_refund,
    		  'price_format'=>AttributesTools::priceFormat()
    		);
    		$this->code = 1; $this->msg = "ok";
		    $this->details = $data;
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }

    public function actionRecentPayout()   
    {
    	try {
    		
    		$data = array();
    	    $limit = Yii::app()->input->post('limit'); 
    	    $criteria=new CDbCriteria();
    	    $criteria->alias = "a";
    	    $criteria->select = "a.transaction_date, a.transaction_amount, a.status, a.transaction_uuid,
    	    (
    	      select concat(restaurant_name,';',logo,';',path) 
    	      from {{merchant}}
    	      where merchant_id = b.account_id
    	    ) as meta_name
    	    ";
    	    $criteria->join = "LEFT JOIN {{wallet_cards}} b on  a.card_id = b.card_id ";
    	    
    	    $criteria->condition = "a.transaction_type=:transaction_type";
			$criteria->params = array(':transaction_type'=>'payout');
			
			$criteria->addNotInCondition('a.status', array('cancelled') );
			$criteria->limit = intval($limit);
			$criteria->order = "a.transaction_date DESC";
			
    	    
    	    if($model = AR_wallet_transactions::model()->findAll($criteria)){
    	    	foreach ($model as $item) {    	    		    	    
    	    		$meta_name = explode(";",$item->meta_name);
    	    		$restaurant_name = isset($meta_name[0])?$meta_name[0]:'';
    	    		$logo = isset($meta_name[1])?$meta_name[1]:'';
    	    		$path = isset($meta_name[2])?$meta_name[2]:'';
    	    		
    	    		$image_url = CMedia::getImage($logo,$path,'@thumbnail',
		             CommonUtility::getPlaceholderPhoto('merchant'));
		             	    		
	    	    	$data[] = array(
	    	    	  'transaction_uuid'=>$item->transaction_uuid,
	    	    	  'restaurant_name'=>Yii::app()->input->xssClean($restaurant_name),  
	    	    	  'transaction_date'=>Date_Formatter::dateTime($item->transaction_date),
	    	    	  'transaction_amount'=>$item->transaction_amount,
	    	    	  'transaction_amount_pretty'=>Price_Formatter::formatNumber($item->transaction_amount),
	    	    	  'status'=>$item->status,    	    	  
	    	    	  'status_class'=>str_replace(" ","_",$item->status),
	    	    	  'image_url'=>$image_url
	    	    	);
    	    	}
    	    	
    	    	$this->code = 1; $this->msg = "ok";
		        $this->details = $data;		    
    	    } else $this->msg = t("No recent payout request");     	    
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionReportsMerchantReg()
    {    	
    	$data = array(); 
    	$status_list = AttributesTools::StatusManagement('customer' , Yii::app()->language );    	
    	$merchant_type_list = AttributesTools::ListMerchantType(Yii::app()->language);    	
    	
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
	            
	    $sortby = "date_created"; $sort = 'DESC';
	    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }	    
	    
	    $page = $page>0? intval($page)/intval($length) :0;
	    
	    $criteria=new CDbCriteria();
	    
	    if(is_array($transaction_type) && count($transaction_type)>=1){
           $criteria->addInCondition('status',(array) $transaction_type );
        }		
        if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}				
        if(is_array($filter) && count($filter)>=1){
        	$filter_merchant_id = isset($filter['merchant_id'])?$filter['merchant_id']:'';
        	$criteria->addSearchCondition('merchant_id', $filter_merchant_id );
        }
	    
	    $criteria->order = "$sortby $sort";
	    $count = AR_merchant::model()->count($criteria); 
	    $pages=new CPagination( intval($count) );
	    $pages->setCurrentPage( intval($page) );        
	    $pages->pageSize = intval($length);
	    $pages->applyLimit($criteria);        
	    
	    $models = AR_merchant::model()->findAll($criteria);
	    if($models){
	    	foreach ($models as $item) {
	    		$avatar = CMedia::getImage($item->logo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('merchant'));
		         
                 $restaurant_name = Yii::app()->input->xssClean($item->restaurant_name);
                 $status = $item->status;
                 if(array_key_exists($item->status,(array)$status_list)){
                 	$status = $status_list[$item->status];
                 }
                 
                 $merchant_type = $item->merchant_type;
                 if(array_key_exists($item->merchant_type,(array)$merchant_type_list)){
                 	$merchant_type = $merchant_type_list[$item->merchant_type];
                 }
                 
                 $view_merchant =  Yii::app()->createUrl('/vendor/edit',array(
				    'id'=>$item->merchant_id
				  ));
                           
$html_resto = <<<HTML
<p class="m-0">$restaurant_name</p>
<div class="badge customer $item->status">$status</div>
HTML;


		    	$data[] = array(		    	 
		    	  'logo'=>'<a href="'.$view_merchant.'"><img class="img-60 rounded-circle" src="'.$avatar.'"></a>',
		    	  'restaurant_name'=>$html_resto,
		    	  'address'=>Yii::app()->input->xssClean($item->address),
		    	  'merchant_type'=>$merchant_type,
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
    
    public function actionReportsMerchantSummary()
    {
    	try {
    		
    		$total_registered = CReports::MerchantTotal(0);    		
    		$commission_total = CReports::MerchantTotal(2, array('active') );
    		$membership_total = CReports::MerchantTotal(1, array('active') );
    		$total_active = CReports::MerchantTotal(0, array('active') );   
    		$total_inactive = CReports::MerchantTotal(0, array('pending','draft','expired') );   
    		
    		$data = array(
    		  'total_registered'=>$total_registered,
    		  'commission_total'=>$commission_total,
    		  'membership_total'=>$membership_total,
    		  'total_active'=>$total_active,
    		  'total_inactive'=>$total_inactive,
    		  'price_format'=>AttributesTools::priceFormat()
    		);    		
    		
    		$this->code = 1; $this->msg = "ok";
		    $this->details = $data;
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionreportsmerchantplan()
    {
    	
    	$payment_gateway = AttributesTools::PaymentProvider();
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
		$filter = isset($this->data['filter'])?$this->data['filter']:'';	
				
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
		$criteria->select="a.merchant_id, a.invoice_number,a.invoice_ref_number,a.created,a.amount,a.status,a.payment_code,
		b.title , c.restaurant_name , c.logo, c.path
		";
		$criteria->join='
		LEFT JOIN {{plans_translation}} b on  a.package_id=b.package_id 
		LEFT JOIN {{merchant}} c on  a.merchant_id = c.merchant_id 
		';
				
		
		$params = array();
		$criteria->addCondition("b.language=:language and c.restaurant_name IS NOT NULL AND TRIM(c.restaurant_name) <> ''");
		$params['language'] = Yii::app()->language;
		
		if(is_array($filter) && count($filter)>=1){
        	$filter_merchant_id = isset($filter['merchant_id'])?$filter['merchant_id']:'';
        	$criteria->addCondition('a.merchant_id=:merchant_id');
        	$params['merchant_id']  = intval($filter_merchant_id);
        }
        
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
        		  'merchant_id'=>$item->restaurant_name,        	
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
    
    public function actionreportsorderearnings()
    {
    	$data = array(); 
    	$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
    	
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
	            
	    $sortby = "order_id"; $sort = 'DESC';
	    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }	    
	    
	    $page = $page>0? intval($page)/intval($length) :0;
	    
	    $criteria=new CDbCriteria();
	    	    
	    if(is_array($status_completed) && count($status_completed)>=1){			
		    $criteria->addInCondition('status', (array) $status_completed );
		}		
		    
        if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}				
		
		if(!empty($search)){
		    $criteria->addSearchCondition('order_id', $search);
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
	    		
	    		$view_order = Yii::app()->createUrl('order/view',array(
				    'order_uuid'=>$item->order_uuid
				));

	    		
		    	$data[] = array(		    	 
		    	  'order_id'=>'<a href="'.$view_order.'">'.$item->order_id."</a>",
		    	  'sub_total'=>Price_Formatter::formatNumber($item->sub_total),
		    	  'total'=>Price_Formatter::formatNumber($item->total),
		    	  'merchant_earning'=>Price_Formatter::formatNumber($item->merchant_earning),
		    	  'commission'=>Price_Formatter::formatNumber($item->commission),
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
    
    public function actionreportsorderearningsummary()
    {
    	try {
    		
    		$date_start = Yii::app()->input->post('date_start');
		    $date_end = Yii::app()->input->post('date_end');		
    		$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
    		
    		$total_count = CReports::EarningTotalCount($status_completed , $date_start , $date_end);   
    		$admin_earning = CReports::EarningByOrder('admin',$status_completed , $date_start , $date_end ); 
    		$merchant_earning = CReports::EarningByOrder('merchant',$status_completed, $date_start , $date_end); 
    		$total_sell = CReports::EarningByOrder('sales',$status_completed, $date_start , $date_end);     		
    		
    		$data = array(
    		  'total_count'=>$total_count,    		  
    		  'admin_earning'=>floatval($admin_earning),    		      		  
    		  'merchant_earning'=>floatval($merchant_earning),    		      		  
    		  'total_sell'=>floatval($total_sell),
    		  'price_format'=>AttributesTools::priceFormat()
    		);    		
    		
    		$this->code = 1; $this->msg = "ok";
		    $this->details = $data;		    
    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionEmailLogs()
    {
    	$data = array(); 
    	$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
    	
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
	            
	    $sortby = "id"; $sort = 'DESC';
	    
	    if(is_array($order) && count($order)>=1){
	        if(array_key_exists($order['column'],(array)$columns)){			
	            $sort = $order['dir'];
	            $sortby = $columns[$order['column']]['data'];
	        }
	    }	    
	    
	    $page = $page>0? intval($page)/intval($length) :0;
	    
	    $criteria=new CDbCriteria();
	    	    
        if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}				
		
		if(!empty($search)){
		    $criteria->addSearchCondition('email_address', $search);		    
			$criteria->addSearchCondition('subject', $search , true , 'OR' );
			$criteria->addSearchCondition('content', $search , true , 'OR' );
        }

		$criteria->order = "$sortby $sort"; 
	    	   
	    $count = AR_email_logs::model()->count($criteria); 
	    $pages=new CPagination( intval($count) );
	    $pages->setCurrentPage( intval($page) );        
	    $pages->pageSize = intval($length);
	    $pages->applyLimit($criteria);        
	    
	    $models = AR_email_logs::model()->findAll($criteria);
	    if($models){
	    	foreach ($models as $item) {	    
	    		
		    	$data[] = array(		    	 
		    	  'date_created'=>$item->date_created,
		    	  'email_address'=>$item->email_address,		    	  
		    	  'subject'=>'<div class="text-truncate" style="max-width:150px;">'.Yii::app()->input->purify($item->subject).'</div>',
		    	  'sms_message'=>'<div class="text-truncate" style="max-width:150px;">'.Yii::app()->input->purify($item->subject).'</div>',
		    	  'status'=>$item->status,
		    	  'delete_url'=>Yii::app()->createUrl("/notifications/delete_email/",array('id'=>$item->id)),
		    	  'view_id'=>$item->id,
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
		$merchant_id = isset($this->data['ref_id'])?$this->data['ref_id']:0;
				
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
    
    public function actionsmslogs()
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
				
		$page = $page>0? intval($page)/intval($length) :0;
		$criteria=new CDbCriteria();
		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		
		if(!empty($search)){
			$criteria->addSearchCondition('contact_phone', $search );
			$criteria->addSearchCondition('sms_message', $search , true , 'OR' );
			$criteria->addSearchCondition('status', $search , true , 'OR' );
		 }
		
		$criteria->order = "$sortby $sort";
				
		//dump($criteria);die();
		$count = AR_sms_broadcast_details::model()->count($criteria); 
		
		$pages=new CPagination( intval($count) );
        $pages->setCurrentPage( intval($page) );        
        $pages->pageSize = intval($length);
        $pages->applyLimit($criteria);                
        $models = AR_sms_broadcast_details::model()->findAll($criteria);
        if($models){
        	foreach ($models as $item) {        
        		$data[]=array(				 
        		  'date_created'=>Date_Formatter::dateTime($item->date_created),
				  'gateway'=>$item->gateway,
				  'contact_phone'=>$item->contact_phone,
				  'sms_message'=>'<div class="text-truncate" style="max-width:150px;">'.Yii::app()->input->purify($item->sms_message).'</div>',
				  'status'=>$item->status,
				  'delete_url'=>Yii::app()->createUrl("/sms/delete/",array('id'=>$item->id)),     
				  'view_id'=>$item->id,
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
    
    public function actiongetSMS()
    {
    	try {
    		
    		$view_id = Yii::app()->input->post('view_id'); 
    		$model = AR_sms_broadcast_details::model()->find("id=:id",array(
    		  ':id'=>intval($view_id)
    		));
    		if($model){
    			$data = array(
    			  'content'=>Yii::app()->input->purify($model->sms_message),
    			  'type'=>"sms"
    			);
    			
    			$this->code = 1; $this->msg = "ok";
		        $this->details = $data;    		
    			
    		} else $this->msg = t(HELPER_RECORD_NOT_FOUND);
    		    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actiongetemail()
    {
    	try {
    		
    		$data = array();
    		$view_id = Yii::app()->input->post('view_id');     		
    		$model = AR_email_logs::model()->find("id=:id",array(
    		  ':id'=>intval($view_id)
    		));
    		if($model){    			
    			$data = array(
    			  'content'=>Yii::app()->input->purify($model->content),
    			  'type'=>"email"
    			);    			
    			$this->code = 1; $this->msg = "ok";
 		        $this->details = $data;    		 
    		} else $this->msg = t(HELPER_RECORD_NOT_FOUND);    		    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actiongetpush()
    {
    	try {
    		
    		$data = array();
    		$view_id = Yii::app()->input->post('view_id');     		
    		$model = AR_push::model()->find("push_uuid=:push_uuid",array(
    		  ':push_uuid'=>$view_id
    		));
    		if($model){    			
    			$data = array(
    			  'content'=>Yii::app()->input->purify($model->body),
    			  'type'=>"sms"
    			);    			
    			$this->code = 1; $this->msg = "ok";
 		        $this->details = $data;    		 
    		} else $this->msg = t(HELPER_RECORD_NOT_FOUND);    		    		
    	} catch (Exception $e) {
		   $this->msg = t($e->getMessage());			   		   		   		
		}
		$this->responseJson();	
    }
    
    public function actionrefundreports()
    {
        	
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
	    $criteria->select ="a.client_id,a.order_id,a.merchant_id,a.transaction_description,a.payment_code,
	    a.trans_amount, a.status, a.payment_reference, a.date_created,
	    b.logo as photo, b.path,
	    c.order_uuid
	    ";	    
	    $criteria->join='
	    LEFT JOIN {{merchant}} b on  a.merchant_id = b.merchant_id
	    LEFT JOIN {{ordernew}} c on  a.order_id = c.order_id
	    ';	  
	    		
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
		        $transaction_description = t(Yii::app()->input->xssClean($item->transaction_description));
		        $reference = t("Payment reference# {{payment_reference}}",array(
		          '{{payment_reference}}'=>$item->payment_reference
		        ));
		        
		        $view_order = Yii::app()->createUrl('order/view',array(
		           'order_uuid'=>$item->order_uuid
		         ));
	    		    		
$information = <<<HTML
$transaction_description<span class="ml-2 badge payment $status_class">$status_trans</span>
<p class="font12 dim m-0">$date</p>
<p class="font12 dim m-0">$reference</p>
HTML;
		         		         
	    		$data[] = array(	    	
	    		  'date_created'=>$item->date_created,
	    		  'merchant_id'=>'<img class="img-60 rounded-circle" src="'.$avatar.'">',
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
        
    public function actionAllPages()
    {
    	try {
    		
    		$data = PPages::all(Yii::app()->language);
    		$this->code = 1;
    		$this->msg = "ok";
    		$this->details = $data;
    		
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
    			 $model = MMenu::get($menu_id,PPages::menuType());
    		} else $model = new AR_menu();    		
    		
    		$model->scenario = "theme_menu";
    		
    		$model->menu_type = PPages::menuType();
    		$model->menu_name = $menu_name;
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
    
    public function actionsortMenu()
    {
    	try {
    		
    		$menu = isset($this->data['menu'])?$this->data['menu']:'';
    		if(is_array($menu) && count($menu)>=1){
    			foreach ($menu as $index=>$item) {    				
    				if($model = MMenu::get($item['menu_id'],PPages::menuType())){    					
    					$model->sequence= intval($index);
    					$model->save();
    				}
    			}
    			$this->code = 1;
		        $this->msg = t("Sort menu saved");
    		} else $this->msg = t("Invalid data");
    		
    	} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
    }
    
    public function actionMenuList()
    {
    	try {
    		     		
    		$data = array();
			try {
			    $data = MMenu::getMenu(0,PPages::menuType());
			} catch (Exception $e) {
			   //	
            }
    		
    		$current_menu = AR_admin_meta::getValue( PPages::menuActiveKey() );
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
    
    public function actiongetMenuDetails()
    {
    	try {
    		
    		$current_menu = Yii::app()->input->post('current_menu');     		
    		$model = AR_menu::model()->findByPk(intval($current_menu));
    		if($model){
    			
    			$data = array();
    			try {
    			    $data = MMenu::getMenu($current_menu,PPages::menuType());
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
    
    public function actiondeletemenu()
    {
    	try {

    		$menu_id = intval(Yii::app()->input->post('menu_id'));  
    		
    		$model = AR_menu::model()->find("menu_id=:menu_id AND menu_type=:menu_type",array(
			   ':menu_id'=>intval($menu_id),
			   ':menu_type'=>PPages::menuType()
			 ));
			 			
			if($model){			   
			   $model->scenario = "theme_menu";		
			   $model->delete();
			   $this->code = 1;
	    	   $this->msg = t(Helper_success);
			} else $this->msg = t(Helper_not_found);
    		
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
    				$model->menu_type=PPages::menuType();
    				$model->menu_name = $page->title;
    				$model->parent_id = $menu_id;
    				$model->link = '{{site_url}}/'.$page->slug;
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
    
    public function actionaddCustomPageToMenu()
    {
    	try {
    		    		
    		$menu_id = isset($this->data['menu_id'])?intval($this->data['menu_id']):0;
    		$custom_link_text = isset($this->data['custom_link_text'])?trim($this->data['custom_link_text']):'';
    		$custom_link = isset($this->data['custom_link'])?trim($this->data['custom_link']):'';
    		
    		$model = new AR_menu();
    		$model->scenario = "custom_link";
    		$model->menu_type=PPages::menuType();
			$model->menu_name = $custom_link_text;
			$model->parent_id = $menu_id;
			$model->link = $custom_link;

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
    		$model = MMenu::get($menu_id,PPages::menuType());
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

	public function actiongetAddons()
	{
		try {

			$data = array();
			$model = AR_addons::model()->findAll();
			if($model){
				foreach ($model as $key => $items) {				
					$data[] = [
                       'id'=>$items->id,					   
					   'uuid'=>$items->uuid,
					   'addon_name'=>CHtml::encode($items->addon_name),
					   'version'=>t("Version {{version}}",['{{version}}'=>$items->version]),
					   'image'=>CMedia::getImage($items->image,$items->path),	
					   'activated'=>$items->activated==1?true:false				   
					];
				}
				$this->code = 1;
				$this->msg = "ok";				
				$this->details = ['data'=>$data];
			} else $this->msg = t("No results");

		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}

	public function actionenableddisabledaddon()
	{

		if(DEMO_MODE){
			$this->msg = t("This action is not available in demo");
			$this->responseJson();
		}

		try {
			$uuid = isset($this->data['uuid'])?$this->data['uuid']:'';
			$activated = isset($this->data['activated'])?$this->data['activated']:0;
			$model = AR_addons::model()->find("uuid=:uuid",[':uuid'=>$uuid]);
			if($model){
				$model->activated = intval($activated);
				$model->save();
				$this->code = 1;
				$this->msg = $model->activated ==1? t("Addon activated") : t("Addon de-activated");				
				$this->details = ['title'=>t("Successful") ];
			} else {
				$this->details = ['title'=>t("Failed") ];
				$this->msg = t("Record not found");
			}
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
			$this->details = ['title'=>t("Failed") ];
		}
		$this->responseJson();
	}
        
}
/*end class*/