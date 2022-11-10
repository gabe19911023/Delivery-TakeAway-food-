<?php
class BackendreportsController extends CommonController
{	
	public $code=2;
	public $msg;
	public $details;
	public $data;
	
	public function beforeAction($action)
	{						
		//if(Yii::app()->request->isAjaxRequest){	
		   $this->data = Yii::app()->input->xssClean($_POST);		   
		   return true;
		//} 		
		return false;
	}
	
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules()
	{
		return array(			
			array('allow', // allow authenticated users to access all actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}		
	
	public function actionmerchant_registration()
	{		
		$view_link = Yii::app()->createUrl("/reports/view_merchant",array(		  
		  'merchant_id'=>'-merchant_id-',				 
		));   
				
		$html='
		<div class="btn-group btn-group-actions" role="group" >								  
		  <a href="'.$view_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
		  >
		  <i class="zmdi zmdi-eye"></i>
		  </a>				  		 		  
		</div>
		';	
		
		   $cols[]=array(
		 	  'key'=>'merchant_id',
		 	  'value'=>'logo',
		 	  'action'=>"merchant_logo", 	  
		 	);		 
		 	$cols[]=array(
		 	  'key'=>'restaurant_name',
		 	  'value'=>'restaurant_name'		 	  
		 	);		 
		 	
		 	$cols[]=array(
		 	  'key'=>'street',
		 	  'value'=>'street',
		 	  'action'=>"format",
		 	  'format'=>'<h6>[merchant_address] <span class="badge ml-2 customer [status]">[status_title]</span></h6>
		 	    <p class="dim">'.t("E").'. [contact_email]<br>'.t("M").'. [contact_phone]<br/>
		 	    '.t("Date Registered").'. [date_created]
		 	    </p>
		 	  ',
		 	  'format_value'=>array(
		 	    '[merchant_address]'=>'merchant_address',
		 	    '[contact_email]'=>'contact_email',
		 	    '[contact_phone]'=>'contact_phone',
		 	    '[status]'=>'status',
		 	    '[status_title]'=>'status_title',
		 	     '[date_created]'=>array(
		 	         'value'=>'date_created',
			 	     'display'=>"datetime"
		 	     )
		 	  )
		 	);
		 	
		 	$cols[]=array(
		 	  'key'=>'merchant_type',
		 	  'value'=>'merchant_type'		 	  
		 	);		 
		 	$cols[]=array(
		 	   'key'=>'merchant_id',
		 	   'value'=>'merchant_id',
		 	   'action'=>"format",	 	   
		 	   'format'=>$html,
		 	   'format_value'=>array(
		 	     '-merchant_id-'=>'merchant_id',		 	     
		 	   )
		 	);
		 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 merchant_id,restaurant_name,street,package_id,date_created,logo,path,
			 contact_phone,contact_email,status,
			 concat(street,' ',city,' ',state,' ',post_code,' ',country_code) as merchant_address,
			 
			 IFNULL((
			 select type_name from {{merchant_type}}
			 where type_id=a.merchant_type
			 ),a.merchant_type) as merchant_type,

			 IFNULL((
			 select title_trans from {{view_status_management}}
			 where
			 status=a.status and group_name='customer'
			 and language=".q(Yii::app()->language)."
			 ),a.status) as status_title
			 			 
			 FROM {{merchant}} a			 
			 
			 WHERE 1
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		 	
		 			 	
		 	$filter_stmt='';		 	
		 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
		 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
		 	
		 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'date_created');
		 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
		 	
		 			 			 			 			
		 	DatatablesTools::$action_edit_path = "vendor/edit";
		 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data,$filter_stmt)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();
	}
	
	public function actionmerchant_payment()
	{		

			$cols=array(
			  array(
			   'key'=>'a.id',
		 	   'value'=>'id',		 	  	 	   
			  ),
			  array(
			    'key'=>'a.merchant_id',
	 	        'value'=>'merchant_logo',		 	  
	 	        'action'=>"merchant_logo", 	  
			  ),
			  array(
			   'key'=>'a.merchant_id',
		 	   'value'=>'merchant_name',
		 	   'action'=>"format",	
		 	   'format'=>'<h6>[merchant_name]</h6>',
		 	   'format_value'=>array( 
		 	     '[merchant_name]'=>'merchant_name'
		 	   )
			  ),
			  array(
			   'key'=>'a.package_id',
		 	   'value'=>'plan_name',
		 	   'action'=>"format",	
		 	   'format'=>'<h6>[plan_name]</h6>
		 	   <p class="dim">
		 	    '.t("Trans. Date").'. [date_created]
		 	   </p>
		 	   ',
		 	   'format_value'=>array( 
		 	     '[plan_name]'=>'plan_name',
		 	     '[date_created]'=>array(
		 	         'value'=>'date_created',
			 	     'display'=>"datetime"
		 	     )
		 	   )
			  ),
			  array(
			   'key'=>'payment_type',
		 	   'value'=>'payment_name',		 	  	 	   
			  ),
			  array(
			   'key'=>'a.price',
		 	   'value'=>'price',
		 	   'action'=>"price"		 	   
			  ),
			  array(
			   'key'=>'a.status',
		 	   'value'=>'status',
		 	   'action'=>"format",	
		 	   'format'=>'<span class="badge payment [status]">[status_title]</span>',
		 	   'format_value'=>array( 
		 	     '[status]'=>'status',
		 	     '[status_title]'=>'status_title'
		 	   )
			  ),
			);
		 
			$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*,			 
			 IFNULL(b.restaurant_name, a.merchant_id ) as merchant_name,	
			 b.logo as merchant_logo,		
			 IFNULL(c.title, a.package_id ) as plan_name,
			 IFNULL(d.payment_name, a.payment_type ) as payment_name,
			 
			 IFNULL((
			 select title_trans from {{view_status_management}}
			 where
			 status=a.status and group_name='payment'
			 and language=".q(Yii::app()->language)."
			 ),a.status) as status_title
			 		 		 
			 FROM {{package_trans}} a			
			 LEFT JOIN {{merchant}} b
			 ON
			 a.merchant_id = b.merchant_id
			 
			 LEFT JOIN {{packages}} c
			 ON
			 a.package_id = c.package_id
			 
			 LEFT JOIN {{payment_gateway}} d
			 ON
			 a.payment_type = d.payment_code
			  
			 WHERE 1
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		 		
			
		 	$filter_stmt='';
		 		 			 			 			 		
		 	DatatablesTools::$action_edit_path = "vendor/edit";
		 	
		 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
		 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
		 	
		 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'a.date_created');
		 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
		 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data,$filter_stmt)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();
	}	
	
	public function actionmerchant_sales_report()
	{
			
		$cols=array(
			  array(
			   'key'=>'a.order_id',
		 	   'value'=>'order_id',		 	  	 	   
			  ),
			  array(
			    'key'=>'a.merchant_id',
	 	        'value'=>'merchant_logo',		 	  
	 	        'action'=>"merchant_logo", 	  
			  ),			 
			  array(
			    'key'=>'a.merchant_id',
	 	        'value'=>'merchant_name',		 	  	 	        
			  ),			 
			  array(
			   'key'=>'a.client_id',
		 	   'value'=>'customer_name',
		 	   'action'=>"format",	
		 	   'format'=>'<h6>[customer_name] <span class="badge ml-2 order_status [status]">[status_title]</span></h6>
		 	   <p class="dim">[date_created]</p>
		 	   ',
		 	   'format_value'=>array(
		 	     '[customer_name]'=>'customer_name',
		 	     '[status]'=>'status',
		 	     '[status_title]'=>'status_title',
		 	     '[date_created]'=>array(
		 	      'display'=>'datetime',
		 	      'value'=>'date_created'
		 	     )
		 	   )
			  ),
			  array(
			   'key'=>'a.merchant_id',
		 	   'value'=>'items',		 	   
			  ),			 
			  array(
			   'key'=>'a.trans_type',
		 	   'value'=>'service_name',		 	   
			  ),			 
			  array(
			   'key'=>'a.payment_type',
		 	   'value'=>'payment_name',		 	   
			  ),			 			 
			  array(
			   'key'=>'a.total_w_tax',
		 	   'value'=>'total_w_tax',
		 	   'action'=>"price"		 	   
			  ),			  
			);
		 
			$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.*,
			 b.logo as merchant_logo, b.path,
			 IFNULL(b.restaurant_name,a.merchant_id) as merchant_name,
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
			 LEFT JOIN {{merchant}} b
			 ON
			 a.merchant_id = b.merchant_id
			 
			 LEFT JOIN {{order_delivery_address}} c
			 ON
			 a.client_id = c.client_id
			 
			 LEFT JOIN {{services}} s
			 ON
			 a.trans_type = s.service_code
			 
			 LEFT JOIN {{payment_gateway}} p
			 ON
			 a.payment_type = p.payment_code
			 
			 WHERE a.status NOT IN (".q( AttributesTools::initialStatus() ).")
			 AND a.order_id = c.order_id
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		
			
			
		 	$filter_stmt='';
		 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
		 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
		 	$filter_merchant=isset($this->data['filter_merchant'])?(integer)$this->data['filter_merchant']:0;
		 	
		 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'a.date_created');
		 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
		 	
		 	if($filter_merchant>0){
		 		$filter_stmt.="\n AND a.merchant_id=".q($filter_merchant)."";
		 	}
		 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data,$filter_stmt)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();
	}
	
	public function actionsearch_merchant()
	{
		$res = array();
		try {
			$search = isset($this->data['search'])?$this->data['search']:'';
			$res  = MerchantAR::search($search);			
		} catch (Exception $e) {
			//echo $e->getMessage();
		}		
		$result = array(
    	  'results'=>$res
    	);	    	
    	$this->responseJson($result);
	}
	
	public function actionbooking_summary()
	{
	
			 $cols=array(			  
			  array(
			    'key'=>'a.merchant_id',
	 	        'value'=>'merchant_logo',		 	  
	 	        'action'=>"merchant_logo", 	  
			  ),
			  array(
			   'key'=>'a.merchant_name',
		 	   'value'=>'merchant_name',
		 	   'action'=>"format",	
		 	   'format'=>'<h6>[merchant_name]</h6>',
		 	   'format_value'=>array( 
		 	     '[merchant_name]'=>'merchant_name'
		 	   )
			  ),
			 array(
			    'key'=>'total_approved',
	 	        'value'=>'total_approved',		 	  	 	        
			  ),
			  array(
			   'key'=>'total_denied',
		 	   'value'=>'total_denied',		 	  	 	   
			  ),			  
			  array(
			   'key'=>'total_pending',
		 	   'value'=>'total_pending',		 	  	 	   
			  ),			  
			);
		 
		    $stmt = "
		 	SELECT SQL_CALC_FOUND_ROWS
			a.merchant_id,
            b.restaurant_name as merchant_name,
            b.logo as merchant_logo, b.path,
             
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

			
			 WHERE 1
			 [and]
			 [search]
			 group by a.merchant_id
			 [order]			 
			 [limit]
		 	";		 		
						
		 	$filter_stmt='';
		 		 			 			 			 		
		 	DatatablesTools::$action_edit_path = "vendor/edit";
		 	
		 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
		 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
		 	$filter_merchant=isset($this->data['filter_merchant'])?(integer)$this->data['filter_merchant']:0;
		 	
		 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'a.date_created');
		 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
		 	
		 	if($filter_merchant>0){
		 		$filter_stmt.="\n AND a.merchant_id=".q($filter_merchant)."";
		 	}
		 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data,$filter_stmt)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();
		
	}
	
} 
/*end class*/