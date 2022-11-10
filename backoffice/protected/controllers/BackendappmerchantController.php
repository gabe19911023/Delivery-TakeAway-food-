<?php
class BackendappmerchantController extends Commonmerchant
{	
	public $code=2,$msg,$details,$data;	
	public $next_action,$page,$search,$limit,$resp_data = array(), $post_data=array();
	
	public function beforeAction($action)
	{						
		$this->data = Yii::app()->input->xssClean($_POST);				   		  		 
		return true;
	}
		
	public function actionIndex()
	{
		$this->redirect(array(Yii::app()->controller->id.'/dashboard'));		
	}
	
    private function preparePaginate()
	{
		$this->limit = (integer)Yii::app()->params['list_limit'];
		$this->page = (integer)Yii::app()->input->get('page') - 1; 
		if($this->page>0){
			$this->page = ($this->page*$this->limit);
		}
		$this->search = Yii::app()->input->get('search'); 	
		
		$this->post_data = array(
		  'start'=>$this->page,
		  'search'=>$this->search,
		  'length'=>$this->limit,
		  'order'=>array(
		    array('column'=>0,'dir'=>'DESC')
		  )
		);			
	}
	
	private function outputListingData($cols=array(), $fields=array(), $stmt='' ,$filter_stmt='' )
	{
		
		if ( $data = AppListing::Listing($cols, $fields, $stmt, $this->post_data , $filter_stmt)){			
			$this->code = 1; $this->msg= "OK";
			$this->details = array(
			  'is_search'=>!empty($this->search)?true:false,
			  'data'=>$data,
			  'page'=>(integer)$this->page
			);
		} else {
			$this->msg = t(HELPER_NO_RESULTS);
			$this->details = array(
			  'is_search'=>!empty($this->search)?true:false,
			  'page'=>(integer)$this->page
			);
		}
		$this->jsonResponse();
	}
				
	public function actionstore_hours()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		
		$fields = array(
		  array('key'=>'id'),	
		  array('key'=>'day'),	 	   	 
		);	 	
		
		$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',		 	  
	 	);		 
	 	
	 	$cols[]=array(
	 	  'key'=>'day',
	 	  'value'=>'day',
	 	  'action'=>"format",
	 	  'format'=>'<h6>[day] <span class="badge ml-2 store_hours_[status]">[status]</span></h6>
	 	  <p class="dim">	 	  
	 	  '.t("[start_time] - [end_time]").'<br/>
	 	  '.t("[start_time_pm] - [end_time_pm]").'<br/>
	 	  [custom_text]
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[day]'=>'day',
	 	    '[status]'=>'status',
	 	    '[custom_text]'=>'custom_text',
	 	    '[start_time]'=>array(
	 	       'value'=>'start_time',
		 	   'display'=>"time"
	 	     ),
	 	    '[end_time]'=>array(
	 	       'value'=>'end_time',
		 	   'display'=>"time"
	 	     ),
	 	    '[start_time_pm]'=>array(
	 	       'value'=>'start_time_pm',
		 	   'display'=>"time"
	 	     ),
	 	    '[end_time_pm]'=>array(
	 	       'value'=>'end_time_pm',
		 	   'display'=>"time"
	 	     ),
	 	  )
	 	);		
	 				 		 	
	 	$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',
	 	  'action'=>"editdelete",
	 	  'id'=>'id'
	 	);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*
		 			 
		 FROM {{opening_hours}} a		 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
				
		AppListing::$action_edit_path = "merchant/store_hours_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}							
		
	public function actioncredit_card_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		
		$fields = array(
		  array('key'=>'mt_id'),	
		  array('key'=>'card_name'),	 	   	 
		);	 	
		
		$cols[]=array(
		 	  'key'=>'mt_id',
		 	  'value'=>'mt_id',		 	  
		 	);		 		 	
		 	
	 	$cols[]=array(
	 	  'key'=>'card_name',
	 	  'value'=>'card_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[card_name]</h6>
	 	  <p class="dim">
	 	   [credit_card_number]<br/>
	 	   '.t("Date Created. [date_created]").'
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[card_name]'=>'card_name',
	 	    '[credit_card_number]'=>'credit_card_number',
	 	    '[date_created]'=>array(
 	         'value'=>'date_created',
	 	     'display'=>"datetime"
 	        )
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'mt_id'
	 	);	 	
		 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*			 			
		 FROM {{merchant_cc}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";	
				
		AppListing::$action_edit_path = "merchant/credit_card_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}			

	public function actionorder_list()
	{		
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	    'key'=>'a.order_id',
	 	   'value'=>'logo',		 	  
	 	   'action'=>"merchant_logo", 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'order_id',
	 	  'value'=>'order_id',
	 	  'action'=>"format",
	 	  'format'=>'<h6>[order_id] <span class="badge ml-2 order_status [status]">[status_title]</span></h6>
	 	    <p class="dim">
	 	    [restaurant_name]<br/>	    
	 	    [customer_name]<br/>
	 	    '.t("[total_items] items").'<br/>
	 	    [payment_type]<br/>
	 	    '.t("Transactions. [service_name]").'
	 	    <br>'.t("Total. [total_w_tax]").'	 	    
	 	    <br>'.t("Date. [date_created]").'	 	    
	 	    </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[order_id]'=>'order_id',
	 	    '[restaurant_name]'=>'restaurant_name',	 	    
	 	    '[status]'=>'status',
	 	    '[status_title]'=>"status_title",
	 	    '[customer_name]'=>"customer_name",
	 	    '[service_name]'=>'service_name',
	 	    '[total_items]'=>'total_items',	
	 	    '[payment_type]'=>'payment_name',
	 	    '[total_w_tax]'=>array(
	 	       'value'=>'total_w_tax',
		 	   'display'=>"price"
	 	     ),
	 	     '[date_created]'=>array(
	 	       'value'=>'date_created',
		 	   'display'=>"datetime"
	 	     ), 	     
	 	  )
	 	);
	 	
	 	$buttons[]='
		  <a href="'.Yii::app()->createUrl("order/view").'/id/[order_id_token]" class="btn btn-light view_delete_process tool_tips process_csv" 
		  data-id="[csv_id]" 
		  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
		   >
		   <i class="zmdi zmdi-eye"></i>
	     </a>		
		';
	 	$buttons[]='
		  <a href="'.Yii::app()->createUrl("order/update").'/id/[order_id_token]" class="btn btn-light view_delete_process tool_tips process_csv" 
		  data-id="[csv_id]" 
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		   >
		   <i class="zmdi zmdi-border-color"></i>
	     </a>		
		';
	 	$buttons[]='
		 <a href="javascript:;" data-id="[order_id]"									  
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		';
	 	
	 	$buttons[]='
		 <a href="javascript:;" data-id="[order_id_token]"
		  class="btn btn-light order_history tool_tips"
		   data-toggle="tooltip" data-placement="top" title="'.t("History").'"
		  >
		  <i class="zmdi zmdi-comments"></i>
		  </a>
		';
	 	
	 	$cols[]=array(
	 	  'key'=>'a.order_id',
	 	  'value'=>'order_id',	 	  
	 	  'id'=>'order_id',
	 	  'action'=>"custom_buttons",
	 	  'buttons'=>$buttons,
	 	  'format_value'=>array(
	 	    '[order_id]'=>'order_id',
	 	    '[order_id_token]'=>'order_id_token'
	 	  )
	 	);		
	 
	 	$fields = array(
	 	  array('key'=>'a.order_id'),
	 	  array('key'=>'b.restaurant_name'),	 	  
	 	  array('key'=>'c.first_name'),	 	  
	 	  array('key'=>'c.last_name'),	 	  
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.order_id,a.order_id_token,a.merchant_id,a.client_id,a.sub_total,a.payment_type,a.total_w_tax,a.status,
		 a.date_created,
		 b.restaurant_name,b.logo,
		 concat(c.first_name,' ',c.last_name) as customer_name,
		 
		 IFNULL(d.payment_name, a.payment_type ) as payment_name,
		 
		 (
		  select count(*)
		  from {{order_details}}
		  where order_id=a.order_id
		 ) as total_items,
		 
		 IFNULL((
		 select description_trans from {{view_order_status}}
		 where
		 description=a.status 
		 and language=".q(Yii::app()->language)."
		 ),a.status) as status_title,

		 IFNULL(s.service_name,a.trans_type) as service_name		
	 		 
		 
		 FROM {{order}} a
		 LEFT JOIN {{merchant}} b
		 ON
		 a.merchant_id = b.merchant_id
		 
		 LEFT JOIN {{order_delivery_address}} c
		 ON
		 a.order_id = c.order_id
		 
		 LEFT JOIN {{payment_gateway}} d
		 ON
		 a.payment_type = d.payment_code
		 
		 LEFT JOIN {{services}} s
		 ON
		 a.trans_type = s.service_code
		 
		 WHERE 
		 a.merchant_id = ".q( Yii::app()->merchant->merchant_id )."
		 AND 
		 a.status NOT IN (".q( AttributesTools::initialStatus() ).")
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
		
		AppListing::$action_edit_path = "merchant/order_update";				
		$this->outputListingData($cols,$fields,$stmt);
	}			
	
	public function actionorder_history()
	{		
		$id = Yii::app()->input->post('id');
		try {
			$resp = Orders::getHistory($id);
			$data = array();
			
			foreach ($resp as $val) {	
				$remarks='';			
				if(!empty($val['remarks2']) && !empty($val['remarks_args']) ){
					$args = json_decode($val['remarks_args'],true);		           	   
	           	    if(is_array($args) && count($args)>=1){
	           	   	  $new_arrgs = array();
	           	   	  foreach ($args as $remarks_args_key=>$remarks_args_val) {
	           	   	  	$new_arrgs[$remarks_args_key] = Yii::t("default",$remarks_args_val);
	           	   	  }	           	      
	           	      $remarks = t($val['remarks2'],$new_arrgs);
	           	    }
				}
				$data[]=array(
				 'status'=>t($val['status']),
				 'remarks'=>$remarks,
				 'date_created'=>Date_Formatter::dateTime($val['date_created'])
				);
			}			
			$this->code = 1; $this->msg = "ok";
			$this->details = array(
			  'next_action'=>"order_history",
			  'data'=>$data
			);
		} catch (Exception $e) {
			$this->msg = t($e->getMessage());
			$this->details = array(
			  'next_action'=>"clear_order_history"			  
			);
		}
		$this->jsonResponse();
	}		
	
	public function actionorder_list_cancel()
	{		
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	    'key'=>'a.order_id',
	 	   'value'=>'logo',		 	  
	 	   'action'=>"merchant_logo", 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'order_id',
	 	  'value'=>'order_id',
	 	  'action'=>"format",
	 	  'format'=>'<h6>[order_id] <span class="badge ml-2 order_status [status]">[status_title]</span></h6>
	 	    <p class="dim">
	 	    [restaurant_name]<br/>	    
	 	    [customer_name]<br/>
	 	    '.t("[total_items] items").'<br/>
	 	    [payment_type]<br/>
	 	    '.t("Transactions. [service_name]").'
	 	    <br>'.t("Total. [total_w_tax]").'	 	    
	 	    <br>'.t("Date. [date_created]").'	 	    
	 	    </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[order_id]'=>'order_id',
	 	    '[restaurant_name]'=>'restaurant_name',	 	    
	 	    '[status]'=>'status',
	 	    '[status_title]'=>"status_title",
	 	    '[customer_name]'=>"customer_name",
	 	    '[service_name]'=>'service_name',
	 	    '[total_items]'=>'total_items',	
	 	    '[payment_type]'=>'payment_name',
	 	    '[total_w_tax]'=>array(
	 	       'value'=>'total_w_tax',
		 	   'display'=>"price"
	 	     ),
	 	     '[date_created]'=>array(
	 	       'value'=>'date_created',
		 	   'display'=>"datetime"
	 	     ), 	     
	 	  )
	 	);
	 	
	 	$buttons[]='
		  <a href="'.Yii::app()->createUrl("order/view").'/id/[order_id_token]" class="btn btn-light view_delete_process tool_tips process_csv" 
		  data-id="[csv_id]" 
		  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
		   >
		   <i class="zmdi zmdi-eye"></i>
	     </a>		
		';
	 	$buttons[]='
		  <a href="'.Yii::app()->createUrl("order/update").'/id/[order_id_token]" class="btn btn-light view_delete_process tool_tips process_csv" 
		  data-id="[csv_id]" 
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		   >
		   <i class="zmdi zmdi-border-color"></i>
	     </a>		
		';
	 	$buttons[]='
		 <a href="javascript:;" data-id="[order_id]"									  
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		';
	 	
	 	$buttons[]='
		 <a href="javascript:;" data-id="[order_id_token]"
		  class="btn btn-light order_history tool_tips"
		   data-toggle="tooltip" data-placement="top" title="'.t("History").'"
		  >
		  <i class="zmdi zmdi-comments"></i>
		  </a>
		';
	 	
	 	$cols[]=array(
	 	  'key'=>'a.order_id',
	 	  'value'=>'order_id',	 	  
	 	  'id'=>'order_id',
	 	  'action'=>"custom_buttons",
	 	  'buttons'=>$buttons,
	 	  'format_value'=>array(
	 	    '[order_id]'=>'order_id',
	 	    '[order_id_token]'=>'order_id_token'
	 	  )
	 	);		
	 
	 	$fields = array(
	 	  array('key'=>'a.order_id'),
	 	  array('key'=>'b.restaurant_name'),	 	  
	 	  array('key'=>'c.first_name'),	 	  
	 	  array('key'=>'c.last_name'),	 	  
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.order_id,a.order_id_token,a.merchant_id,a.client_id,a.sub_total,a.payment_type,a.total_w_tax,a.status,
		 a.request_cancel,a.request_cancel_status,
		 b.restaurant_name,b.logo,
		 concat(c.first_name,' ',c.last_name) as customer_name,
		 IFNULL(d.payment_name,".q(t('Not available')).") as payment_name,
		 
		 (
		  select count(*)
		  from {{order_details}}
		  where order_id=a.order_id
		 ) as total_items,
		 
		 IFNULL((
		 select description_trans from {{view_order_status}}
		 where
		 description=a.status 
		 and language=".q(Yii::app()->language)."
		 ),a.status) as status_title,

		 IFNULL(s.service_name,a.trans_type) as service_name	
		 		 
		 
		 FROM {{order}} a
		 LEFT JOIN {{merchant}} b
		 ON
		 a.merchant_id = b.merchant_id
		 
		 LEFT JOIN {{order_delivery_address}} c
		 ON
		 a.order_id = c.order_id
		 
		 LEFT JOIN {{payment_gateway}} d
		 ON
		 a.payment_type = d.payment_code
		 
		 LEFT JOIN {{services}} s
		 ON
		 a.trans_type = s.service_code
		 
		 WHERE a.merchant_id = ".q( Yii::app()->merchant->merchant_id )."
		 AND a.status NOT IN (".q( AttributesTools::initialStatus() ).")
		 AND ( 
		  request_cancel=1
		  OR 
		  request_cancel_status='approved'
		 )
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
				
		AppListing::$action_edit_path = "plans/update";				
		$this->outputListingData($cols,$fields,$stmt);
	}				
	
	public function actiontime_managment_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'group_id',
	 	  'value'=>'group_id',		 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'transaction_type',
	 	  'value'=>'transaction_type',		
	 	  'action'=>"format",
	 	  'format'=>'<h6>[transaction_type] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">
	 	  '.t("[start_time] to [end_time]").'<br/>
	 	  '.t("Nos. Order allowed. [number_order_allowed]").'<br/>
	 	  [days]<br/>
	 	  [order_status]
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[transaction_type]'=>'transaction_type',
	 	    '[status]'=>'status',
		 	 '[status_title]'=>'status_title',		 
	 	    '[start_time]'=>array(
	 	       'value'=>'start_time',
		 	   'display'=>"time"
	 	     ),
	 	     '[end_time]'=>array(
	 	       'value'=>'end_time',
		 	   'display'=>"time"
	 	     ),
	 	     '[number_order_allowed]'=>'number_order_allowed',
	 	     '[days]'=>array(
	 	       'value'=>'days',
		 	   'display'=>"explode"
	 	     ),
	 	     '[order_status]'=>array(
	 	       'value'=>'order_status',
		 	   'display'=>"json"
	 	     ),
	 	  )
	 	);		 
	 		 	
	 	$cols[]=array(
	 	  'key'=>'group_id',
	 	  'value'=>'group_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'group_id'
	 	); 	
	 	
	 	$fields = array(
	 	  array('key'=>'group_id'),	
	 	  array('key'=>'transaction_type'),	 	   	 
	 	);	 	
	 		 	 
		$stmt = "
	 	SELECT a.id,a.group_id,a.transaction_type,a.start_time,
	 	a.end_time,a.number_order_allowed,a.order_status,a.status,
		GROUP_CONCAT(a.days) as days,
		
		IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		),a.status) as status_title	
		
		FROM
		{{order_time_management}} a
		WHERE
		merchant_id= ".q( $merchant_id )."
		[and]
		[search]
		GROUP BY a.group_id
		[order]
		[limit]
	 	"; 		
		
		AppListing::$action_edit_path = "merchant/time_management_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}				
	
	public function actionbooking_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'booking_id',
	 	  'value'=>'booking_id',		 	  
	 	);		 		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'booking_name',
	 	  'value'=>'booking_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[booking_name] <span class="badge ml-2 booking [status]">[status_title]</span></h6>
	 	  <p class="dim">
	 	   '.t("Number Of Guests [number_guest]").'<br/>
	 	   '.t("Date Of Booking. [date_created]").'
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[booking_name]'=>'booking_name',		 	    
	 	    '[number_guest]'=>'number_guest',	
	 	    '[status]'=>'status',		 	    
	 	    '[status_title]'=>'status_title',		 
	 	    '[date_created]'=>array(
 	         'value'=>'date_created',
	 	     'display'=>"datetime"
 	        )
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'booking_id'
	 	);	
		 	
	 	$fields = array(
	 	  array('key'=>'booking_id'),	
	 	  array('key'=>'booking_name'),	 	   	 
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,

		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='booking'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title	
		 			
		 FROM {{bookingtable}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
		
		AppListing::$action_edit_path = "booking/update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}						
	
	public function actiontimeslot_booking()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
		 	  'key'=>'id',
		 	  'value'=>'id',		 	  
		 	);		 		 	
		 	
	 	$cols[]=array(
	 	  'key'=>'days',
	 	  'value'=>'days',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[days]</h6>
	 	  <p class="dim">
	 	   '.t("[start_time] - [end_time]").'<br/>
	 	   '.t("Nos. Order allowed. [number_order_allowed]").'<br/>
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[days]'=>'days',	
	 	    '[number_order_allowed]'=>'number_order_allowed',
	 	    '[start_time]'=>array(
 	          'value'=>'start_time',
		 	   'display'=>"time"
	 	     ),
	 	    '[end_time]'=>array(
	 	       'value'=>'end_time',
		 	   'display'=>"time"
	 	     ),
	 	    //	 	    
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'id'
	 	);	 
		 	
	 	$fields = array(
	 	  array('key'=>'id'),	
	 	  array('key'=>'days'),	 	   	 
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*			 			
		 FROM {{timeslot_booking}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		  			 
		
		AppListing::$action_edit_path = "booking/timeslot_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}							
	
	public function actionsize_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'size_id',
	 	  'value'=>'size_id',		 	  
	 	);		 		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'size_name',
	 	  'value'=>'size_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[size_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">		 	   
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[size_name]'=>'size_name',
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'size_id'
	 	);	 	
	 	
	 	$fields = array(
	 	  array('key'=>'size_id'),	
	 	  array('key'=>'size_name'),	 	   	 
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 
		  
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
		 	
		 FROM {{size}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
		 			 
		AppListing::$action_edit_path = "attrmerchant/size_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}							
	
	public function actioningredients_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'ingredients_id',
	 	  'value'=>'ingredients_id',		 	  
	 	);		 		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'ingredients_name',
	 	  'value'=>'ingredients_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[ingredients_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">		 	   
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[ingredients_name]'=>'ingredients_name',
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'ingredients_id'
	 	);	 		 	
	 	
	 	$fields = array(
	 	  array('key'=>'ingredients_id'),	
	 	  array('key'=>'ingredients_name'),	 	   	 
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,			 			  
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
		 	
		 FROM {{ingredients}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	
		 			 
		AppListing::$action_edit_path = "attrmerchant/ingredients_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}								
	
	public function actioncookingref_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
	 	$fields = array(
	 	  array('key'=>'cook_id'),	
	 	  array('key'=>'cooking_name'),	 	   	 
	 	  array('key'=>'status'),	 
	 	);	 	
	 		 	
 		$cols[]=array(
	 	  'key'=>'cook_id',
	 	  'value'=>'cook_id',		 	  
	 	);		 		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'cooking_name',
	 	  'value'=>'cooking_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[cooking_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">		 	   
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[cooking_name]'=>'cooking_name',
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'cook_id'
	 	);	 	
		 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
		 	
		 FROM {{cooking_ref}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 		
		 			 
		AppListing::$action_edit_path = "attrmerchant/cookingref_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}									
		
	public function actioncategory_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'cat_id'),	
		  array('key'=>'category_name'),
		  array('key'=>'status'),
		);	 	
					
		$cols[]=array(
		 	  'key'=>'cat_id',
		 	  'value'=>'photo',
		 	  'action'=>"item_photo", 	  
		 	);		 		 	
		 	
	 	$cols[]=array(
	 	  'key'=>'category_name',
	 	  'value'=>'category_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[category_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">		
	 	  '.t("[items] Items").'<br/>
	 	  '.t("Date. [date_created]").'<br/>
	 	  [category_description]
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[category_name]'=>'category_name',
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',
	 	    '[items]'=>'items',
	 	    '[category_description]'=>array(
	 	       'value'=>'category_description',
		 	   'display'=>"short_text"
	 	    ),
	 	    '[date_created]'=>array(
	 	       'value'=>'date_created',
		 	   'display'=>"datetime"
	 	     )
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'cat_id'
	 	);	 	
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,		 
		 (
		 select count(*) from {{item_relationship_category}}
		 where cat_id = a.cat_id
		 ) as items,
		  
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
		 	
		 FROM {{category}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";			
				 
		AppListing::$action_edit_path = "food/category_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}				
		
	public function actionaddoncategory_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'subcat_id'),	
		  array('key'=>'subcategory_name'),	 	   	 
		);	 	
				
		$cols[]=array(
	 	  'key'=>'subcat_id',
	 	  'value'=>'featured_image',
	 	  'action'=>"item_photo", 	  
	 	);		 		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'subcategory_name',
	 	  'value'=>'subcategory_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[subcategory_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">		
	 	  '.t("[items] Items").'<br/>
	 	  '.t("Date. [date_created]").'<br/>	
	 	  [subcategory_description]
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[subcategory_name]'=>'subcategory_name',
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',
	 	    '[items]'=>'items',
	 	    '[subcategory_description]'=>array(
	 	       'value'=>'subcategory_description',
		 	   'display'=>"short_text"
	 	    ),
	 	    '[date_created]'=>array(
	 	       'value'=>'date_created',
		 	   'display'=>"datetime"
	 	     )
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'subcat_id'
	 	);	 	
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 
		 (
		 select count(*) from {{subcategory_item_relationships}}
		 where subcat_id = a.subcat_id
		 ) as items,			 
		  
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
		 	
		 FROM {{subcategory}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";				
					 
		AppListing::$action_edit_path = "food/addoncategory_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}		
	
	public function actionaddonitem_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'sub_item_id'),	
		  array('key'=>'sub_item_name'),	 	   	 
		);	 	

         $cols[]=array(
	 	  'key'=>'sub_item_id',
	 	  'value'=>'photo',
	 	  'action'=>"item_photo", 	  
	 	);	
	 	
	 	$cols[]=array(
	 	  'key'=>'sub_item_name',
	 	  'value'=>'sub_item_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[sub_item_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">				 	   		 	   
	 	   '.t("Date. [date_created]").'<br/>	
	 	   [item_description]
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[sub_item_name]'=>'sub_item_name',		
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',
	 	    '[addon_category]'=>'addon_category',
	 	    '[item_description]'=>array(
	 	       'value'=>'item_description',
		 	   'display'=>"short_text"
	 	    ),
	 	    '[date_created]'=>array(
 	         'value'=>'date_created',
	 	     'display'=>"datetime"
 	        ),
	 	    '[price]'=>array(
 	         'value'=>'price',
	 	     'display'=>"price"
 	        )
	 	  )
	 	);		 	
	 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'sub_item_id'
	 	);	 	
	 	
		 $stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 			  
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title,
		 
		 (
		 select GROUP_CONCAT(subcategory_name)
		 from {{subcategory}} b where subcat_id IN 
		  (
		    select subcat_id from {{subcategory_item_relationships}}
		    where subcat_id = b.subcat_id and sub_item_id = a.sub_item_id
		  )
		 ) as addon_category
		 	
		 FROM {{subcategory_item}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";					
					 
		AppListing::$action_edit_path = "food/addonitem_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}					
		
	public function actionitem_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'item_id'),	
		  array('key'=>'item_name'),	 	   	 
		);	 	
						
        $cols[]=array( 
	 	  'key'=>'item_id',
	 	  'value'=>'photo',		 	  
	 	  'action'=>"item_photo", 	  
	 	);	
	 			 	
	 	$cols[]=array(
	 	  'key'=>'item_name',
	 	  'value'=>'item_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[item_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	  <p class="dim">				 	   		 	   
	 	   '.t("Last Modified. [date_modified]").'<br/>
	 	   '.t("SKU#[sku]").'	   
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[item_name]'=>'item_name',		
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',		 	    
	 	    '[sku]'=>'sku',
	 	    '[item_short_description]'=>array(
	 	       'value'=>'item_short_description',
		 	   'display'=>"short_text"
	 	    ),
	 	    '[date_modified]'=>array(
 	         'value'=>'date_modified',
	 	     'display'=>"datetime"
 	        )		 	    
	 	  )
	 	);		 		 	
	 			 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'primary_key'=>"item_id",
	 	  'id'=>'item_id'
	 	);	 	
		 	
		 $stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 
		 (
		 select GROUP_CONCAT(category_name)
		 from {{category}} b where cat_id IN 
		  (
		    select cat_id from {{item_relationship_category}}
		    where cat_id = b.cat_id and item_id = a.item_id
		  )
		 ) as category_group,
		 
		 (
		 select max(price) from {{item_relationship_size}}
		 where item_id = a.item_id
		 limit 0,1
		 ) as item_price,
		 			  
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title	
		 	
		 FROM {{item}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			
					 
		AppListing::$action_edit_path = "food/item_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}				
	
	public function actionitemprice_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'price'),	
		  array('key'=>'size_name'),	 	   	 
		);	 	
						
		$cols[]=array(
	 	  'key'=>'price',
	 	  'value'=>'price',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>'.t("[price] [size_name]").'</h6>		 	   
	 	  <p class="dim">
	 	   [item_available]<br/>
	 	   '.t("SKU#[sku]").'
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[size_name]'=>'size_name',
	 	    '[item_available]'=>'item_available',
	 	    '[sku]'=>'sku',
	 	    '[price]'=>array(
	 	       'value'=>'price',
		 	   'display'=>"price"
	 	     ),
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'cost_price',
	 	  'value'=>'cost_price',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[cost_price]</h6>
	 	   <p class="dim">		 	   
	 	   </p>
	 	  ',
	 	  'format_value'=>array(		 	    
	 	    '[cost_price]'=>array(
	 	       'value'=>'cost_price',
		 	   'display'=>"price"
	 	     ),
	 	  )
	 	);		 	
	 
	 	$cols[]=array(
	 	  'key'=>'created_at',
	 	  'value'=>'created_at',
	 	  'action'=>"editdelete",
	 	  'id'=>'item_size_id'
	 	);	 	
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 IF(a.available>0,'".t("Available")."', '".t("Not Available")."') as item_available,
		 
		 IFNULL(b.size_name,'') as size_name  
		 			 			
		 FROM {{item_relationship_size}} a
		 LEFT JOIN {{size}} b
		 ON
		 a.size_id = b.size_id
		 
		 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 AND item_id = ".q($item_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	 	
	 						 
		AppListing::$action_edit_path = "food/itemprice_update/item_id/$item_id";			
		$this->outputListingData($cols,$fields,$stmt);		
    }			

    public function actionitemaddon_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');		
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'id'),	
		  array('key'=>'subcategory_name'),	 	   	 
		);	 	

		$cols[]=array(
		 	  'key'=>'id',
		 	  'value'=>'subcategory_name',		 	  
		 	  'action'=>'format',
		 	  'format'=>'<h6>[subcategory_name]</h6><p>[price] [size_name]</p>',
		 	  'format_value'=>array(
		 	    '[subcategory_name]'=>'subcategory_name',
		 	    '[size_name]'=>'size_name',
		 	    '[price]'=>array(
		 	      'value'=>'price',
		 	      'display'=>'price'
		 	    )
		 	  )
		 	);		 		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'require_addon',
		 	  'value'=>'require_addon',		 	  
		 	);	
		 			 	
		 	$cols[]=array(
		 	  'key'=>'id',
		 	  'value'=>'id',
		 	  'action'=>"editdelete",
		 	  'id'=>'id'
		 	);	 	
		 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*,	
			 IF(a.require_addon>0,".q(t("Yes")).",".q(t("No")).") as require_addon,
			 IFNULL(b.subcategory_name, '' ) as subcategory_name,
			 
			 IFNULL(c.price,'') as price,
			 IFNULL(c.size_name,'') as size_name
			 
			 FROM {{item_relationship_subcategory}} a	
			 JOIN {{subcategory}} b
			 ON 
			 a.subcat_id  = b.subcat_id
			 
			 JOIN {{view_item_size}} c
			 ON 
			 a.item_size_id  = c.item_size_id
			 
			 WHERE a.merchant_id = ".q($merchant_id)."			 
			 AND a.item_id = ".q($item_id)."
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		
					 
		AppListing::$action_edit_path = "food/itemaddon_update/item_id/$item_id";			
		$this->outputListingData($cols,$fields,$stmt);		
	}		
	
	public function actionitem_promo()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'promo_id'),	
		  array('key'=>'promo_type'),	 	   	 
		);	 	
				
		$cols[]=array(
	 	  'key'=>'promo_id',
	 	  'value'=>'promo_id',		 	 	  			 	 
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'promo_id',
	 	  'value'=>'promo_type',		 
	 	  'action'=>"promo_type",			 	  
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'promo_id'
	 	);	 	
		 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 IFNULL(b.item_name,'') as item_name
		  			 			
		 FROM {{item_promo}} a			 
		 LEFT JOIN {{item}} b
		 ON
		 a.item_id_promo = b.item_id
		 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";			
					 
		AppListing::$action_edit_path = "food/itempromo_update/item_id/$item_id";			
		$this->outputListingData($cols,$fields,$stmt);		
	}			
		
	public function actioncharges_table()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'distance_from'),	
		  array('key'=>'distance_price'),	 	   	 
		  array('key'=>'shipping_type'),
		);	 	
				
	    $cols[]=array(
		 	  'key'=>'distance_from',
		 	  'value'=>'shipping_type',	
		 	  'action'=>"format",		 
		 	  'format'=>'<h6>'.t("[shipping_type]").'
		 	  <p class="dim mt-1">
		 	  '.t("[distance_from] - [distance_to] [shipping_units]").'<br/>
		 	  '.t("[estimation] mins").'
		 	  </p>
		 	  </h6>',
		 	  'format_value'=>array(		 	     	 	    
		 	     '[estimation]'=>'estimation',
		 	     '[distance_from]'=>array(
	 	           'value'=>'distance_from',
		 	       'display'=>"numerical"
	 	         ),
	 	         '[distance_to]'=>array(
	 	           'value'=>'distance_to',
		 	       'display'=>"numerical"
	 	         ),
	 	         '[shipping_units]'=>array(
	 	           'value'=>'shipping_units',
		 	       'display'=>"pattern",
		 	       'data'=>AttributesTools::unit()
	 	         ),
	 	         '[shipping_type]'=>array(
	 	           'value'=>'shipping_type',
		 	       'display'=>"pattern",
		 	       'data'=>AttributesTools::ShippingType()
	 	         )
		 	  )
		 	);	
		 	
		 	$cols[]=array(
		 	  'key'=>'distance_price',
		 	  'value'=>'distance_price',	
		 	  'action'=>"format",		 
		 	  'format'=>'<b>[distance_price]</b>',
		 	  'format_value'=>array(
		 	     '[distance_price]'=>array(
	 	           'value'=>'distance_price',
		 	       'display'=>"price"
	 	          )
		 	  )
		 	);	
		 	
		  	
		 	$cols[]=array(
		 	  'key'=>'id',
		 	  'value'=>'id',
		 	  'action'=>"editdelete",
		 	  'id'=>'id'
		 	);	 	
		 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*			 
			 FROM {{shipping_rate}} a				 
			 WHERE a.merchant_id = ".q($merchant_id)."			
			 and a.charge_type='dynamic'		 
			 and service_code='delivery'
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		

		 			
		AppListing::$action_edit_path = "services/chargestable_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}			

	
	public function actioncoupon_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'voucher_id'),	
		  array('key'=>'voucher_name'),	 	   	 
		);	 	
						
		$cols=array(
		  array(
		   'key'=>'voucher_id',
	 	   'value'=>'voucher_id'	 	   
		  ),		  
		   array(
		    'key'=>'voucher_name',
	 	    'value'=>'voucher_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[voucher_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    	 	    
	 	    <p class="dim">'.t("Voucher Type").'. 
	 	    [voucher_type]<br>'.
	 	    t("Discount").'. [amount]<br>'.
	 	    t("Expiration").'. [expiration]<br/>
	 	    '.t("Last Update. [date_modified]").'
	 	    </p>
	 	    ',
	 	    'format_value'=>array(
	 	     '[voucher_name]'=>'voucher_name',
	 	     '[voucher_type]'=>'voucher_type',
	 	     '[status]'=>'status',
	 	     '[status_title]'=>'status_title',
	 	     '[amount]'=>array(
	 	       'value'=>'amount',
	 	       'display'=>"price"
	 	     ),	
	 	     '[expiration]'=>array(
	 	       'value'=>'expiration',
	 	       'display'=>"date"
	 	     ),	
	 	      '[date_modified]'=>array(
	 	         'value'=>'date_modified',
		 	     'display'=>"datetime"
	 	      ),	
	 	    )
		  ),		  
		  array(
		   'key'=>'voucher_id',
	 	   'value'=>'voucher_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'voucher_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,

		 (
		 select count(*) 
		 from
		 {{order}}
		 where
		 voucher_code=a.voucher_name			
		 ) as total_used,
		 
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='post'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
		 		
		 FROM {{voucher_new}} a
		 WHERE voucher_owner = 'merchant'		 
		 AND merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
					 
		AppListing::$action_edit_path = "merchant/coupon_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}									
		
	public function actionoffer_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'offers_id'),	
		  array('key'=>'offer_percentage'),	 	   	 
		);	 	
						
		$cols[]=array(
		 	  'key'=>'offers_id',
		 	  'value'=>'offers_id',		 	  
		 	);		 		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'offer_percentage',
		 	  'value'=>'offer_percentage',		 
		 	  'action'=>"format",	
		 	  'format'=>'<h6>[offer_percentage] <span class="badge ml-2 post [status]">[status_title]</span></h6>
		 	  <p class="dim">		 
		 	   '.t("Over [offer_price]").'<br/>	   
		 	   '.t("Last Update. [date_modified]").'
		 	  </p>
		 	  ',
		 	  'format_value'=>array(		 	    
		 	    '[offer_percentage]'=>array(		 	       
		 	       'display'=>'percentage',
		 	       'value'=>'offer_percentage',
		 	     ),
		 	    '[status]'=>'status',
	 	        '[status_title]'=>'status_title',
		 	    '[date_modified]'=>array(
	 	         'value'=>'date_modified',
		 	     'display'=>"datetime"
	 	        ),
	 	        '[offer_price]'=>array(
	 	          'value'=>'offer_price',
	 	          'display'=>"price"
	 	        )
		 	  )
		 	);		 
		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'date_created',
		 	  'value'=>'date_created',
		 	  'action'=>"editdelete",
		 	  'id'=>'offers_id'
		 	);	 	
		 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*,
			 
			 IFNULL((
			 select title_trans from {{view_status_management}}
			 where
			 status=a.status and group_name='post'
			 and language=".q(Yii::app()->language)."
			 limit 0,1
			 ),a.status) as status_title
			 
			 FROM {{offers}} a			 
			 WHERE a.merchant_id = ".q($merchant_id)."
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		
					 
		AppListing::$action_edit_path = "merchant/offer_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}				
	
	public function actiongallery_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'cuisine_id',
	 	  'value'=>'filename',	
	 	  'action'=>'gallery' 	  
	 	);		 	 	

	 	$cols[]=array(
	 	  'key'=>'title',
	 	  'value'=>'title',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h5 class="card-title mb-1">[title]</h5><p class="card-text mb-1">[size]</p>',
	 	  'format_value'=>array(
	 	    '[title]'=>array(
	 	       'value'=>'title',
		 	   'display'=>"short_text"
	 	     ),
	 	    '[size]'=>array(
	 	       'value'=>'size',
		 	   'display'=>"filesize"
	 	     ),
	 	  )
	 	); 	
	 			 	
	 	$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',
	 	  'action'=>"editdelete",
	 	  'id'=>'id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'id'),	
	 	  array('key'=>'filename'),	 	   	 
	 	  array('key'=>'title'),	 
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 
		 FROM {{media_files}} a
		 WHERE merchant_id=".q($merchant_id)."	
		 AND meta_name = ".q(AttributesTools::metaMedia())."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	
		
		$this->next_action = "display_gallery";
		AppListing::$action_edit_path = "attributes/cuisine_update";		
		$this->outputListingData($cols,$fields,$stmt);		
	}				

	public function actionmedia_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'cuisine_id',
	 	  'value'=>'filename',	
	 	  'action'=>'gallery' 	  
	 	);		 	 	

	 	$cols[]=array(
	 	  'key'=>'title',
	 	  'value'=>'title',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h5 class="card-title mb-1">[title]</h5><p class="card-text mb-1">[size]</p>',
	 	  'format_value'=>array(
	 	    '[title]'=>array(
	 	       'value'=>'title',
		 	   'display'=>"short_text"
	 	     ),
	 	    '[size]'=>array(
	 	       'value'=>'size',
		 	   'display'=>"filesize"
	 	     ),
	 	  )
	 	); 	
	 			 	
	 	$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',
	 	  'action'=>"editdelete",
	 	  'id'=>'id',
	 	  'hide_edit'=>true
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'id'),	
	 	  array('key'=>'filename'),	 	   	 
	 	  array('key'=>'title'),	 
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 
		 FROM {{media_files}} a
		 WHERE merchant_id=".q($merchant_id)."			 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	
		
		$this->next_action = "display_gallery";
		AppListing::$action_edit_path = "attributes/cuisine_update";		
		$this->outputListingData($cols,$fields,$stmt);		
	}	
		
	public function actionsmsbroadcast_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'broadcast_id'),	
		  array('key'=>'send_to'),	 	   	 
		);	 	
						
		  $view_link = Yii::app()->createUrl("/smsmerchant/broadcast_details",array(		  
			  'broadcast_id'=>'-id-',				 
			));   

			$html='
			<div class="btn-group btn-group-actions" role="group" >				
					  
			  <a href="'.$view_link.'" class="btn btn-light tool_tips"
			  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
			  >
			  <i class="zmdi zmdi-eye"></i>
			  </a>		
			  
			 <a href="javascript:;" data-id="-id-" 
			  class="btn btn-light datatables_delete tool_tips"
			  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
			  >
			  <i class="zmdi zmdi-delete"></i>
			  </a>
			  
			</div>
			';	
		    	
		 	$cols[]=array(
		 	  'key'=>'broadcast_id',
		 	  'value'=>'broadcast_id',		 	  
		 	);		 		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'send_to',
		 	  'value'=>'send_to',		 
		 	  'action'=>"format",	
		 	  'format'=>'<h6>[send_to] <span class="badge ml-2 post [status]">[status_title]</span></h6>
		 	  <p class="dim">		 	   
		 	  '.t("Last Update. [date_modified]").'
		 	  </p>
		 	  ',
		 	  'format_value'=>array(		 	    
		 	    '[status]'=>'status',
	 	        '[status_title]'=>'status_title',	 	   
	 	        '[send_to]'=>array(
	 	           'value'=>'send_to',
		 	       'display'=>"pattern",
		 	       'data'=>AttributesTools::SMSBroadcastType()
	 	         ),
		 	    '[date_modified]'=>array(
	 	         'value'=>'date_modified',
		 	     'display'=>"datetime"
	 	        )
		 	  )
		 	);		 	
		 	
		 	$cols[]=array(
		 	   'key'=>'a.broadcast_id',
		 	   'value'=>'broadcast_id',		 	  
		 	   'action'=>"format",
		 	   'format'=>$html,	 	 
		 	   'format_value'=>array(	 	   
		 	     '-id-'=>'broadcast_id',
		 	   )  
		 	);	 	
		 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*,

			 IFNULL((
			 select description_trans from {{view_order_status}}
			 where
			 description=a.status 
			 and language=".q(Yii::app()->language)."
			 ),a.status) as status_title
			 			
			 FROM {{sms_broadcast}} a			 
			 WHERE a.merchant_id = ".q($merchant_id)."
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		 		
					 
		AppListing::$action_edit_path = "merchant/credit_card_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}		
	
	public function actionsubscriber_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'ingredients_id'),	
		  array('key'=>'ingredients_name'),	 	   	 
		);	 	
						
					 
		AppListing::$action_edit_path = "attrmerchant/ingredients_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}			
	
	public function actioncustomer_review()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'client_id'),	
		  array('key'=>'review'),	 	   	 
		);	 	
				
		$update_link = Yii::app()->createUrl("/customer/reviews_update",array(		  
			  'id'=>'-id-',				 
			));   
			
			$view_link = Yii::app()->createUrl("/customer/review_reply",array(		  
			  'id'=>'-id-',				 
			));   

			$html='
			<div class="btn-group btn-group-actions" role="group" >				
					  
			  <a href="'.$update_link.'" class="btn btn-light tool_tips"
			  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
			  >
			  <i class="zmdi zmdi-border-color"></i>
			  </a>	
			
			  <a href="'.$view_link.'" class="btn btn-light tool_tips"
			  data-toggle="tooltip" data-placement="top" title="'.t("Reply").'"
			  >
			  <i class="zmdi zmdi-mail-reply-all"></i>
			  </a>		
			  			  
			 <a href="javascript:;" data-id="-id-" 
			  class="btn btn-light datatables_delete tool_tips"
			  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
			  >
			  <i class="zmdi zmdi-delete"></i>
			  </a>
			  
			</div>
			';	
		    
		 	$cols[]=array(
		 	  'key'=>'id',
		 	  'value'=>'id',		 	  
		 	);		 		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'client_id',
		 	  'value'=>'client_id',		 
		 	  'action'=>"format",	
		 	  'format'=>'<h6>[customer_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
		 	  <p class="dim">			 	   
		 	   '.t("Date Created. [date_created]").'<br/>
		 	   <a href="javascript:;" class="review_viewcomments" data-id="[id]">'.t("Comments ([number_comments])").'</a>
		 	  </p>
		 	  ',
		 	  'format_value'=>array(
		 	    '[customer_name]'=>'customer_name',		 		 	    
		 	    '[status]'=>'status',
	 	        '[status_title]'=>'status_title',
	 	        '[number_comments]'=>'number_comments',
	 	        '[id]'=>'id',
		 	    '[date_created]'=>array(
	 	         'value'=>'date_created',
		 	     'display'=>"datetime"
	 	        )
		 	  )
		 	);		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'review',
		 	  'value'=>'review',	
		 	  'action'=>"format",
		 	  'format'=>'<b>[rating]</b><br/>[review]',
		 	  'format_value'=>array(
		 	    '[rating]'=>'rating',
		 	    '[review]'=>array(
		 	      'value'=>'review',
		 	      'display'=>"short_text"
		 	    )
		 	  )
		 	);	 	
		 	
		 	$cols[]=array(
		 	   'key'=>'a.id',
		 	   'value'=>'id',		 	  
		 	   'action'=>"format",
		 	   'format'=>$html,	 	 
		 	   'format_value'=>array(	 	   
		 	     '-id-'=>'id',		 	     
		 	   )  
		 	);	 	
		 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*,
			 
			 IFNULL((
			  select concat(first_name,' ',last_name)
			  from {{client}}
			  where client_id = a.client_id
			 ),a.client_id) as customer_name,
			 
			 IFNULL((
			 select description_trans from {{view_order_status}}
			 where
			 description=a.status 
			 and language=".q(Yii::app()->language)."
			 ),a.status) as status_title,
			 
			 (
			 select count(*) from {{review}}
			 where parent_id = a.id
			 ) as number_comments
			 
			 FROM {{review}} a			 
			 WHERE a.merchant_id = ".q($merchant_id)."	
			 AND a.parent_id=0		 
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		 			
					 
		AppListing::$action_edit_path = "backendmerchant/customerreview_update";			
		$this->outputListingData($cols,$fields,$stmt);		
		}		
	
	public function actionuser_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'merchant_user_id'),	
		  array('key'=>'first_name'),	 	   	 
		);	 	
				
		$cols[]=array(
		 	   'key'=>'merchant_user_id',
	 	       'value'=>'profile_photo',
	 	       'action'=>"customer",
		 	);		 		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'first_name',
		 	  'value'=>'first_name',
		 	  'action'=>"format",
		 	  'format'=>'<h6>[fullname] <span class="badge ml-2 customer [status]">[status]</span></h6>
		 	    <p class="dim">'.t("E").'. [contact_email]<br>'.t("M").'. [contact_number]</p>
		 	  ',
		 	  'format_value'=>array(
		 	    '[fullname]'=>'fullname',
		 	    '[contact_email]'=>'contact_email',
		 	    '[contact_number]'=>'contact_number',
		 	    '[status]'=>'status',
		 	  )
		 	);		
		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'date_created',
		 	  'value'=>'date_created',
		 	  'action'=>"editdelete",
		 	  'id'=>'merchant_id_token'
		 	);	 	
		 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*,
			 concat(first_name,' ',last_name) as fullname,

			 IFNULL((
			 select title_trans from {{view_status_management}}
			 where
			 status=a.status and group_name='customer'
			 and language=".q(Yii::app()->language)."
			 limit 0,1
			 ),a.status) as status_title,
			 
			 IFNULL(b.role_name,'') as role_name,
			 
			 IFNULL((
			 select count(*) from {{role_access}}
			 where role_id = a.role
			 ),0) as access_count
			 			
			 FROM {{merchant_user}} a			 
			 LEFT JOIN {{role}} b
			 ON
			 a.role = b.role_id
				 
			 
			 WHERE a.merchant_id = ".q($merchant_id)."
			 AND main_account=0
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		 				
					 
		AppListing::$action_edit_path = "usermerchant/user_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}									
			
	public function actionrole_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'role_id'),	
		  array('key'=>'role_name'),	 	   	 
		);	 	
				
		$cols[]=array(
	 	  'key'=>'role_id',
	 	  'value'=>'role_id'		 	  
	 	);		 
	 	
	 	$cols[]=array(
	 	  'key'=>'role_id',
	 	  'value'=>'access_count',
	 	  'action'=>"format",
	 	  'format'=>'<h6>[role_name]</h6><h6>[access_count]</h6><p class="dim">'.t("permission").'</span>',
	 	  'format_value'=>array(
	 	    '[role_name]'=>'role_name',
	 	    '[access_count]'=>'access_count',
	 	  )
	 	);		 	 
	 		 	
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'role_id'
	 	);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 role_id,role_name,date_created,
		 (
		 select count(*) from {{role_access}}
		 where role_id = a.role_id
		 ) as access_count
		 FROM {{role}} a
		 WHERE merchant_id = ".q($merchant_id)."
		 AND role_type='merchant'
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
					 
		AppListing::$action_edit_path = "usermerchant/role_update";			
		$this->outputListingData($cols,$fields,$stmt);		
		}	
		
	public function actionsales_report()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'a.order_id'),
		  array('key'=>'c.first_name'),
		  array('key'=>'c.last_name'),
		);	 	
						
		$cols[]=array(
	 	   'key'=>'a.order_id',
 	       'value'=>'customer_avatar',		 	  
 	       'action'=>"customer", 	  
	 	);		
	 	
	 	$cols[]=array(
	 	    'key'=>'a.client_id',
	 	   'value'=>'customer_name',
	 	   'action'=>"format",	
	 	   'format'=>'<h6>[customer_name] <span class="badge ml-2 order_status [status]">[status_title]</span></h6>
	 	   <p class="dim">
	 	     [items]<br/>
	 	     [service_name]<br/>
	 	     [payment_name]<br/>
	 	     [total_w_tax]<br/>
	 	     [date_created]
	 	   </p>
	 	   ',
	 	   'format_value'=>array(
	 	     '[customer_name]'=>'customer_name',
	 	     '[status]'=>'status',
	 	     '[status_title]'=>'status_title',
	 	     '[items]'=>'items',
	 	     '[service_name]'=>'service_name',
	 	     '[payment_name]'=>'payment_name',
	 	     '[date_created]'=>array(
	 	      'display'=>'datetime',
	 	      'value'=>'date_created'
	 	     ),
	 	     '[total_w_tax]'=>array(
	 	       'value'=>'total_w_tax',
	 	       'display'=>"price"
	 	     )
	 	   )
	 	);		
	 	
	 	$cols[]=array(
	 	   'key'=>'a.order_id',
	 	   'value'=>'order_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'order_id',	 	
	 	   'hide_edit'=>true,
	 	   'hide_delete'=>true
	 	);
		 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 IFNULL(b.avatar,'') as customer_avatar,			 
		 IFNULL(concat(c.first_name,' ',c.last_name),a.client_id) as customer_name,
		 
		 IFNULL(s.service_name,a.trans_type) as service_name,
		 
		 IFNULL(p.payment_name,a.payment_type) as payment_name,
		 
		 IFNULL((
		 select description_trans from {{view_order_status}}
		 where
		 description=a.status 
		 and language=".q(Yii::app()->language)."
		 ),a.status) as status_title,
		 
		 (
		 select 
		 IF( GROUP_CONCAT(item_name) IS NULL or item_name = '',
		 
		 (
		   select GROUP_CONCAT(item_name) from {{order_details}}
		   where order_id = a.order_id
		 )
		 
		 ,  GROUP_CONCAT(item_name) )
		 
		 from {{item_translation}}
		 where
		 language = ".q(Yii::app()->language)."
		 and
		 item_id IN (
		       select item_id from {{order_details}}
		       where order_id = a.order_id
		   )
		 ) as items
		 
		 FROM {{order}} a			
		 LEFT JOIN {{client}} b
		 ON
		 a.client_id = b.client_id
		 
		 LEFT JOIN {{order_delivery_address}} c
		 ON
		 a.client_id = c.client_id
		 
		 LEFT JOIN {{services}} s
		 ON
		 a.trans_type = s.service_code
		 
		 LEFT JOIN {{payment_gateway}} p
		 ON
		 a.payment_type = p.payment_code
		 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 AND a.status NOT IN (".q( AttributesTools::initialStatus() ).")
		 AND a.order_id = c.order_id
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
					 
		$this->data = Yii::app()->input->xssClean($_GET);
		
		$filter_stmt='';		 	
	 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
	 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
	 	
	 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'date_created');
	 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
	 	
	 	if(!empty($filter_stmt) || $this->page<=0){
			$this->search = true;
		}
				
				 
		AppListing::$action_edit_path = "";									
		$this->outputListingData($cols,$fields,$stmt,$filter_stmt);
	}		

    public function actionsummary_report()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'a.item_id'),
		  array('key'=>'a.item_name'),
		);	 	
					 		 		 	
	 	$cols[]=array(
	 	  'key'=>'a.item_id',
	 	  'value'=>'item_photo',
	 	  'action'=>'item_photo'
	 	);		 		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'a.item_name',
	 	  'value'=>'item_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6 class="m-0">[item_name]</h6>
	 	  <p class="dim m-0">		 	
	 	   [size]<br/>
	 	   '.t("Average Price. [average_price]").'<br/>
	 	   '.t("Total Qty. [qty_sold]").'<br/>
	 	   '.t("Total. [total]").'<br/>
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[item_name]'=>'item_name',		 			 	    
	 	    '[size]'=>'size',
	 	    '[qty_sold]'=>'qty_sold',
	 	    '[average_price]'=>array(
	 	      'value'=>"average_price",
	 	      'display'=>'price'
	 	    ),
	 	    '[total]'=>array(
	 	      'value'=>"total",
	 	      'display'=>'price'
	 	    )
	 	  )
	 	);		 	
		 	
	 
	 	$cols[]=array(
	 	  'key'=>'discounted_price',
	 	  'value'=>'total',	
	 	  'action'=>'price'
	 	);	 	
		 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 SUM(a.qty) as qty_sold,
		 (a.discounted_price * SUM(a.qty)) as net_sale,
		 (a.discounted_price * SUM(a.qty)/SUM(a.qty)) as average_price,
		 ((a.discounted_price * SUM(a.qty)/SUM(a.qty)) * SUM(a.qty)) as total,
		 
		 IFNULL(b.photo,'') as item_photo
		 			
		 FROM {{view_order_details}} a			 
		 LEFT JOIN {{item}} b
		 ON
		 a.item_id = b.item_id
		 
		 WHERE a.merchant_id = ".q($merchant_id)."			 
		 AND a.status NOT IN (".q( AttributesTools::initialStatus() ).")	
		 [and]
		 [search]
		 GROUP BY a.item_id,a.size	 
		 [order]
		 [limit]
	 	";		 			 	
		 	
		$this->data = Yii::app()->input->xssClean($_GET);
		
		$filter_stmt='';		 	
	 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
	 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
	 	
	 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'date_created');
	 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
	 	
	 	if(!empty($filter_stmt) || $this->page<=0){
			$this->search = true;
		}
								 
		AppListing::$action_edit_path = "";									
		$this->outputListingData($cols,$fields,$stmt,$filter_stmt);
	}					
	
    public function actionbooking_summary()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
	 
		$fields = array(
		  array('key'=>'a.merchant_id'),
		  array('key'=>'b.restaurant_name'),
		);	 	

		$cols[]=array(
	 	   'key'=>'a.merchant_id',
	 	   'value'=>'merchant_logo',		 	  
	 	   'action'=>"merchant_logo", 	  
	 	);		 
	 	
	 	$cols[]=array(
	 	   'key'=>'a.merchant_id',
	 	   'value'=>'merchant_id',
	 	   'action'=>'format',
	 	   'format'=>'<h6>[merchant_name]</h6>
	 	   <p>
	 	    '. t("Total Approved. [total_approved]").'<br/>
	 	    '. t("Total Denied. [total_denied]").'<br/>
	 	    '. t("Total Pending. [total_pending]").'<br/>
	 	   </p>
	 	   ',
	 	   'format_value'=>array( 
	 	     '[merchant_name]'=>'merchant_name',
	 	     '[total_approved]'=>'total_approved',
	 	     '[total_denied]'=>'total_denied',
	 	     '[total_pending]'=>'total_pending',
	 	   )
	 	);		 
	 	
	 	$cols[]=array(
	 	   'key'=>'a.merchant_id',
	 	   'value'=>'merchant_id',		 	  	 	   
	 	);		 
		
	    $stmt = "
	 	SELECT SQL_CALC_FOUND_ROWS
		a.merchant_id,
        b.restaurant_name as merchant_name,
        b.logo as merchant_logo,
         
        IFNULL((
		select sum(number_guest) from {{bookingtable}}
		where 
		merchant_id = a.merchant_id 
		and status='approved'
		),0) as total_approved,
		
		IFNULL((
		select sum(number_guest) from {{bookingtable}}
		where 
		merchant_id = a.merchant_id 
		and status='denied'
		),0) as total_denied,
		
		IFNULL((
		select sum(number_guest) from {{bookingtable}}
		where 
		merchant_id = a.merchant_id 
		and status='pending'
		),0) as total_pending
		
		from {{bookingtable}} a
		left join {{merchant}} b
		on
		a.merchant_id = b.merchant_id

		
		 WHERE a.merchant_id=".q($merchant_id)."
		 [and]
		 [search]
		 group by a.merchant_id
		 [order]			 
		 [limit]
	 	";		 		
	    
		$this->data = Yii::app()->input->xssClean($_GET);
		
		$filter_stmt='';		 	
	 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
	 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
	 	
	 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'date_created');
	 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
	 	
	 	if(!empty($filter_stmt) || $this->page<=0){
			$this->search = true;
		}
								 
		AppListing::$action_edit_path = "";									
		$this->outputListingData($cols,$fields,$stmt,$filter_stmt);
	}				
	
	public function actionsupplier_list()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->preparePaginate();		
		$cols = array();
		 	
	 	$fields = array(
	 	  array('key'=>'supplier_id'),	
	 	  array('key'=>'supplier_name'),	 	   	 
	 	);	
	 	
	 	$cols[]=array(
	 	  'key'=>'supplier_id',
	 	  'value'=>'supplier_id',		 	  
	 	);		 		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'supplier_name',
	 	  'value'=>'supplier_name',		 
	 	  'action'=>"format",	
	 	  'format'=>'<h6>[supplier_name]</h6>
	 	  <p class="dim">		
	 	   [email]<br/>
	 	   '.t("Last Update. [updated_at]").'
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[supplier_name]'=>'supplier_name',		 	    
	 	    '[contact_name]'=>'contact_name',
	 	    '[email]'=>'email',
	 	    '[updated_at]'=>array(
 	         'value'=>'updated_at',
	 	     'display'=>"datetime"
 	        )
	 	  )
	 	);		 	
	 	
	 	$cols[]=array(
	 	  'key'=>'updated_at',
	 	  'value'=>'updated_at',
	 	  'action'=>"editdelete",
	 	  'id'=>'supplier_id'
	 	);	 	
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*			 			
		 FROM {{inventory_supplier}} a			 
		 WHERE a.merchant_id = ".q($merchant_id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 	
		 	
		AppListing::$action_edit_path = "supplier/update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}							
	
}
/*end class*/