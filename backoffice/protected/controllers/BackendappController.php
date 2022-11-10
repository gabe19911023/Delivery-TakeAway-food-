<?php
class BackendappController extends CommonController
{	
	public $code=2,$msg,$details,$data;	
	public $page,$search,$limit,$resp_data = array(), $post_data=array();
	
	public function beforeAction($action)
	{						
		$this->data = Yii::app()->input->xssClean($_POST);				   		  		 
		return true;
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
			
	public function actionmerchant_list()
	{		
		$this->preparePaginate();
		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'merchant_id',
	 	  'value'=>'logo',
	 	  'action'=>"merchant_logo", 	  
	 	);		 	 	
	 	$cols[]=array(
	 	  'key'=>'street',
	 	  'value'=>'street',
	 	  'action'=>"format",
	 	  'format'=>'<h6>[restaurant_name] <span class="badge ml-2 customer [status]">[status_title]</span></h6>
	 	    <p class="dim">
	 	    [merchant_address]<br/>
	 	    '.t("E. [contact_email]").'<br/>
	 	    '.t("C. [contact_phone]").'<br/>
	 	    [merchant_type]	 	    
	 	    </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[restaurant_name]'=>'restaurant_name',
	 	    '[merchant_address]'=>'merchant_address',
	 	    '[merchant_type]'=>'merchant_type',
	 	    '[contact_email]'=>'contact_email',
	 	    '[contact_phone]'=>'contact_phone',
	 	    '[status]'=>'status',
	 	    '[status_title]'=>"status_title"
	 	  )
	 	);	 		 	
	 	$cols[]=array(
	 	  'key'=>'a.date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'merchant_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'merchant_id'),
	 	  array('key'=>'street'),
	 	  array('key'=>'restaurant_name'),
	 	  array('key'=>'street'),
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.merchant_id,a.restaurant_name,a.street,a.package_id,a.date_created,a.logo,a.path,
		 a.contact_phone,a.contact_email,a.status,
		 concat(street,' ',city,' ',state,' ',post_code,' ',country_code) as merchant_address,	
		 
		 IFNULL((
		 select type_name from {{merchant_type_translation}}
		 where type_id = a.merchant_type
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.merchant_type) as merchant_type,
		 
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='customer'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
		 			 			 
		 FROM {{merchant}} a			 
		 
		 WHERE 1
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 					
		
		AppListing::$action_edit_path = "vendor/edit";
		$this->outputListingData($cols,$fields,$stmt);
	}
	
	public function actioncsv_list()
	{
		
		$this->preparePaginate();
		$cols = array();
		
		$buttons[]='
		  <a href="javascript:;" class="btn btn-light view_delete_process tool_tips process_csv" 
		  data-id="[csv_id]" 
		  data-toggle="tooltip" data-placement="top" title="'.t("Process").'"
		   >
		   <i class="zmdi zmdi-mail-send"></i>
	     </a>		
		';
		$buttons[]='
		<a href="'.Yii::app()->createUrl("vendor/csv_view").'/id/[csv_id]" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
		  >
		  <i class="zmdi zmdi-eye"></i>
		 </a>
		';
		$buttons[]='
		 <a href="javascript:;" data-id="[csv_id]"									  
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		';
		
		$cols[]=array(
	 	  'key'=>'csv_id',
	 	  'value'=>'csv_id',	 	  
	 	);		 	 	
	 	$cols[]=array(
	 	  'key'=>'filename',
	 	  'value'=>'filename',
	 	  'action'=>"format",
	 	  'format'=>'<h6>[filename]</h6>
	 	    <p class="dim">	 	   
	 	    [count] out of [process_count]<br/>
	 	    [date_created]
	 	    </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[filename]'=>'filename',
	 	    '[count]'=>'count',
	 	    '[process_count]'=>'process_count',
	 	    '[date_created]'=>array(
	 	      'value'=>'date_created',
		 	  'display'=>"datetime"
	 	    )
	 	  )
	 	);	 		 	
	 	$cols[]=array(
	 	  'key'=>'a.date_created',
	 	  'value'=>'date_created',	 	  
	 	  'id'=>'csv_id',
	 	  'action'=>"custom_buttons",
	 	  'buttons'=>$buttons,
	 	  'format_value'=>array(
	 	    '[csv_id]'=>'csv_id'
	 	  )
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'csv_id'),
	 	  array('key'=>'filename'),	 	  
	 	);	 	
	 		 	 		
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 (
		 select count(*) from {{merchant_csv_details}}
		 where csv_id = a.csv_id
		 ) as count,
		 
		 (
		 select count(*) from {{merchant_csv_details}}
		 where csv_id = a.csv_id
		 and process_status != 'pending'
		 ) as process_count
		 
		 FROM {{merchant_csv}} a	 
		 WHERE 1
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
		
		AppListing::$action_edit_path = "vendor/csv_view";
		$this->outputListingData($cols,$fields,$stmt);						
	}
	
	public function actioncsv_list_details()
	{
	
		$id = Yii::app()->input->get('id');		
		$this->preparePaginate();
		
		$cols = array(); $buttons = array();
		
		$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',	 	  
	 	);		 	 	
	 	$cols[]=array(
	 	  'key'=>'restaurant_name',
	 	  'value'=>'restaurant_name',
	 	  'action'=>"format",
	 	   'format'=>'<h6>[restaurant_name] <span class="badge ml-2 customer [status]">[status_title]</span></h6>
	 	    <p class="dim">	 	
	 	     '.t("E. [contact_email]").'<br/>
	 	     '.t("M. [contact_phone]").'<br/>
	 	     [merchant_address]<br/>	 	      	 	    
	 	     [merchant_type]
	 	    </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[restaurant_name]'=>'restaurant_name',	
	 	    '[status]'=>'status',	
	 	    '[status_title]'=>'status_title',	
	 	    '[merchant_address]'=>'merchant_address',
	 	    '[merchant_type]'=>'merchant_type',
	 	    '[contact_email]'=>'contact_email',
	 	    '[contact_phone]'=>'contact_phone',
	 	  )
	 	);	 		 	
	 	$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',	 	  
	 	  'id'=>'id',
	 	  'action'=>"custom_buttons",
	 	  'buttons'=>$buttons,
	 	  'format_value'=>array(
	 	    '[id]'=>'id'
	 	  )
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'id'),
	 	  array('key'=>'restaurant_name'),	 	  
	 	  array('key'=>'contact_email'),	 	  
	 	  array('key'=>'contact_phone'),	 	  
	 	  array('key'=>'contact_name'),	 	  
	 	);	 	
	 		 	 		
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 concat(street,' ',city,' ',state,' ',post_code,' ',country_code) as merchant_address,
		 (
		 select type_name from {{merchant_type}}
		 where type_id=a.merchant_type
		 ) as merchant_type	,
		 
		IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.process_status and group_name='transaction'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title	 
		 		 		 
		 FROM {{merchant_csv_details}} a	
		 WHERE csv_id = ".q($id)." 
		 [and]
		 [search]
		 [order]
		 [limit]
		 ";
		
		AppListing::$action_edit_path = "vendor/csv_view";				
		$this->outputListingData($cols,$fields,$stmt);
	}
	
		public function actionprocess_csv()
	{
		$id = (integer) Yii::app()->input->post('id');
		$process_count = 0;
		$msg = '<h6 class="csv_progress">[count] out of [process_count]</h6>
		 	    <p class="dim">'.t("process").'</p>';
				
		if($id>0){
			
			$stmt="
			SELECT SQL_CALC_FOUND_ROWS a.*,
			(
			 select count(*) from 
			 {{merchant_csv_details}}
			 where csv_id = a.csv_id
			) as total_records,

			(
			 select count(*) from 
			 {{merchant_csv_details}}
			  where csv_id = a.csv_id
			  and process_status!='pending'
			) as total_process
			
			FROM {{merchant_csv_details}} a
			WHERE
			csv_id = ".q($id)."
			and process_status='pending'
			ORDER BY id ASC
			LIMIT 0,1
			";			
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				
				$model=new AR_merchant;
		        $model->scenario='information';
				
				foreach ($res as $val) {
					
					$process_status = '';
					$line_id = (integer)$val['id'];
					
					$model->restaurant_name = isset($val['restaurant_name'])?$val['restaurant_name']:'';
					$model->restaurant_slug = isset($val['restaurant_slug'])?$val['restaurant_slug']:'';
					$model->restaurant_phone = isset($val['restaurant_phone'])?$val['restaurant_phone']:'';
					$model->contact_name = isset($val['contact_name'])?$val['contact_name']:'';
					$model->contact_phone = isset($val['contact_phone'])?$val['contact_phone']:'';
					$model->contact_email = isset($val['contact_email'])?$val['contact_email']:'';					
					$model->tags = isset($val['tags'])?$val['tags']:'';
					$model->delivery_distance_covered = isset($val['delivery_distance_covered'])?(float)$val['delivery_distance_covered']:0;
					$model->distance_unit = isset($val['distance_unit'])?$val['distance_unit']:'';
					$model->is_ready = isset($val['is_ready'])?(integer)$val['is_ready']:1;
					$model->status = isset($val['status'])?$val['status']:'';			
					
					$cuisine = isset($val['cuisine'])?explode("-",$val['cuisine']):array();
					$services = isset($val['service'])?explode("-",$val['service']):array();
					$tags = isset($val['tags'])?explode("-",$val['tags']):array();
					
					$model->service = MerchantTools::legacyServices( (array) $services);
					$model->cuisine = json_encode((array)$cuisine);
					
					$model->cuisine2 = (array)$cuisine;
					$model->service2 = (array)$services;
					$model->tags = (array)$tags;
					
					$model->merchant_type = isset($val['merchant_type'])?(integer)$val['merchant_type']:1;
					$model->street = isset($val['street'])?$val['street']:'';
					$model->city = isset($val['city'])?$val['city']:'';
					$model->state = isset($val['state'])?$val['state']:'';
					$model->post_code = isset($val['post_code'])?$val['post_code']:'';
					$model->country_code = isset($val['country_code'])? substr($val['country_code'],0,3) :'';
					
					if($model->validate()){
						if($model->save()){
							$process_status = 'process';
						} else {									
							if(is_array($model->errors) && count($model->errors)>=1){
								foreach ($model->errors as $field=>$err) {
									$process_status.= t("'[field]' [error]\n",array(
									  '[field]'=>$field,
									  '[error]'=>$err[0]
									));
									
								}
							} else $process_status = t("Cannot insert records");
						}
					} else {
						if(is_array($model->errors) && count($model->errors)>=1){
							foreach ($model->errors as $field=>$err) {
								$process_status.= t("[error]\n",array(								  
								  '[error]'=>$err[0]
								));
								
							}
						} else $process_status = t("Failed validating data");
					}
										
					$process_count = (integer)$val['total_process']+1;		
					
										
					Yii::app()->db->createCommand()->update("{{merchant_csv_details}}",array(
					 'process_status'=>$process_status
					),
			  	    'id=:id',
				  	    array(
				  	      ':id'=>$line_id
				  	    )
			  	    );
				} //end foreach		
				
				$this->code = 1;
				$this->msg = t($msg,array(
				  '[count]'=>$res[0]['total_records'],
				  '[process_count]'=>$process_count
				));				
				$this->details = array(
				  'next_action'=>"csv_continue",
				  'id'=>$id
				);
			} else {
				$this->code = 1;
				$this->msg = t("Done");
				$this->details = array(
				  'next_action'=>"csv_done",
				  'id'=>$id
				);
			}
						
		} else {
			$this->code = 1;
			$this->msg = t("Invalid ID");
			$this->details = array(
			  'next_action'=>"csv_done",
			  'id'=>$id
			);
		}
		$this->jsonResponse();
	}
	
	public function actionsponsored_list()
	{
		$this->preparePaginate();
				
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'merchant_id',
	 	  'value'=>'logo',
	 	  'action'=>"merchant_logo", 	  
	 	);		 	 	
	 	$cols[]=array(
	 	  'key'=>'restaurant_name',
	 	  'value'=>'restaurant_name',
           'action'=>"format",
	 	   'format'=>'<h6>[restaurant_name]</h6>
	 	   <p class="dim">
	 	   [sponsored_expiration]
	 	   </p>
	 	   ',
	 	   'format_value'=>array(
	 	    '[restaurant_name]'=>'restaurant_name',	
	 	    '[sponsored_expiration]'=>array(
	 	      'value'=>'sponsored_expiration',
		 	  'display'=>"datetime"
	 	    )
	 	   )
	 	);		
	 	$cols[]=array(
	 	  'key'=>'a.date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'merchant_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'merchant_id'),
	 	  array('key'=>'street'),
	 	  array('key'=>'restaurant_name'),
	 	  array('key'=>'street'),
	 	);	 	
	 	
	 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 merchant_id,logo,restaurant_name,sponsored_expiration,date_created
		 			 
		 FROM {{merchant}} a
		 WHERE is_sponsored='2'
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 
		
		AppListing::$action_edit_path = "vendor/edit_sponsored";
		$this->outputListingData($cols,$fields,$stmt);						
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
	
	public function actionplan_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'package_id',
	 	  'value'=>'package_id',	 	  
	 	);		 	 	
	 	$cols[]=array(
	 	  'key'=>'title',
	 	  'value'=>'title',
           'action'=>"format",
	 	   'format'=>'<h6>[title] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	   <p class="dim">	 	 
	 	   [description]<br/>
	 	   '.t("Price").' [price]
	 	   <br>'.t("Promo").'. [promo_price]
		 	<br>'.t("Expiration").'. [expiration] [expiration_type]
	 	   </p>
	 	   ',
	 	   'format_value'=>array(
	 	    '[title]'=>'title',	
	 	    '[status]'=>'status',
	 	    '[status_title]'=>'status_title',	 	    
	 	    '[description]'=>array(
	 	      'value'=>'description',
		 	  'display'=>"short_text"
	 	    ),
	 	     '[price]'=>array(
	 	      'value'=>'price',
	 	      'display'=>"price"
	 	    ),		 	    		 	    
	 	    '[promo_price]'=>array(
	 	      'value'=>'promo_price',
	 	      'display'=>"price"
	 	    ),
	 	    '[expiration]'=>'expiration',
	 	    '[expiration_type]'=>'expiration_type',
	 	   )
	 	);		
	 	$cols[]=array(
	 	  'key'=>'package_id',
	 	  'value'=>'package_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'package_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'title'),
	 	  array('key'=>'description'),	 	  
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
		 		
		 FROM {{packages}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	
		
		AppListing::$action_edit_path = "plans/update";
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
		 
		 WHERE a.status NOT IN (".q( AttributesTools::initialStatus() ).")
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
		
		AppListing::$action_edit_path = "plans/update";				
		$this->outputListingData($cols,$fields,$stmt);
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
		 
		 WHERE a.status NOT IN (".q( AttributesTools::initialStatus() ).")
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
	           	      $remarks = Yii::t("default",$val['remarks2'],$new_arrgs);            	   
	           	    }
				}
				$data[]=array(
				 'status'=>t($val['status']),
				 'remarks'=>$remarks,
				 'date_created'=>Date_Formatter::date($val['date_created'])
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
	
	public function actioncuisine_list()
	{
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'cuisine_id',
	 	  'value'=>'featured_image',	
	 	  'action'=>'item_photo' 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'cuisine_name',
	 	  'value'=>'cuisine_name',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h6>[cuisine_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>',
	 	  'format_value'=>array(
	 	    '[cuisine_name]'=>'cuisine_name',	 	     
	 	    '[status]'=>'status',	 	     	 	     
	 	    '[status_title]'=>'status_title',	 	
	 	  )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'cuisine_id',
	 	  'value'=>'cuisine_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'cuisine_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'cuisine_id'),	
	 	  array('key'=>'cuisine_name'),	 	   	 
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
		 		
		 FROM {{cuisine}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	
		
		AppListing::$action_edit_path = "attributes/cuisine_update";			
		$this->outputListingData($cols,$fields,$stmt);		
	}			
	
	public function actiondishes_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'dish_id',
	 	  'value'=>'photo',	
	 	  'action'=>'item_photo'
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'dish_name',
	 	  'value'=>'dish_name',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h6>[dish_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>',
	 	  'format_value'=>array(
	 	    '[dish_name]'=>'dish_name',	 	     
	 	    '[status]'=>'status',	 	     	 	     
	 	    '[status_title]'=>'status_title',	 	
	 	  )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'dish_id',
	 	  'value'=>'dish_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'dish_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'dish_id'),	
	 	  array('key'=>'dish_name'),	 	   	 
	 	  array('key'=>'status'),	
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
		 		
		 FROM {{dishes}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 				
		
		AppListing::$action_edit_path = "attributes/dish_update";
		$this->outputListingData($cols,$fields,$stmt);						
	}				
	
	public function actiontags_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'tag_id',
	 	  'value'=>'tag_id'	 	   
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'tag_name',
	 	  'value'=>'tag_name',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h6>[tag_name]</h6>
	 	  <p class="dim">[description]</p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[tag_name]'=>'tag_name',	 	     
	 	    '[description]'=>'description',	 	     	 	     	 	    
	 	  )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'tag_id',
	 	  'value'=>'tag_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'tag_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'tag_id'),	
	 	  array('key'=>'tag_name'),	 	   	 
	 	  array('key'=>'description'),	
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{tags}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	 				
		
		AppListing::$action_edit_path = "attributes/tags_update";
		$this->outputListingData($cols,$fields,$stmt);						
	}					
	
	public function actionstatus_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'stats_id',
	 	   'value'=>'background_color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[background_color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[background_color_hex]'=>'background_color_hex',
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'description',
	 	  'value'=>'description',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h6>[description]</h6>',
	 	  'format_value'=>array(	 	    
	 	    '[description]'=>'description',	 	     	 	     	 	    
	 	  )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'stats_id',
	 	  'value'=>'stats_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'stats_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'stats_id'),	
	 	  array('key'=>'description'),		 	  
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{order_status}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	 				
		
		AppListing::$action_edit_path = "attributes/status_update";			
		$this->outputListingData($cols,$fields,$stmt);
	}						
	
	public function actionpages_list()
	{
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'page_id',
	 	  'value'=>'page_id'	 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'title',
	 	  'value'=>'title',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h6>[title] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    	 	    
	 	    <p class="dim">[short_content]</p>
	 	   ',
	 	  'format_value'=>array(	 	    
	 	    '[title]'=>'title',	 	
	 	    '[status]'=>'status',	 	     
	 	     '[status_title]'=>'status_title',	 	
	 	     '[short_content]'=>array(
	 	       'value'=>'short_content',
	 	       'display'=>"short_text"
	 	     ),	     	 	     	 	    
	 	  )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'page_id',
	 	  'value'=>'page_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'page_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'page_id'),	
	 	  array('key'=>'title'),
	 	  array('key'=>'short_content'),
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
		 	 		 		
		 FROM {{pages}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 		 				
		
		AppListing::$action_edit_path = "attributes/page_update";
		$this->outputListingData($cols,$fields,$stmt);		
	}							
	
	public function actionlanguage_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id'	 
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'title',
	 	  'value'=>'title',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h6>[title] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    	 	    
	 	    <p class="dim">[description]</p>
	 	   ',
	 	  'format_value'=>array(	 	    
	 	    '[title]'=>'title',	 	
	 	    '[status]'=>'status',	 	     
	 	     '[status_title]'=>'status_title',	 	
	 	     '[description]'=>array(
	 	       'value'=>'description',
	 	       'display'=>"short_text"
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
	 	  array('key'=>'title'),
	 	  array('key'=>'description'),
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
 		
		 FROM {{language}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 				
		
		AppListing::$action_edit_path = "attributes/language_update";
		$this->outputListingData($cols,$fields,$stmt);						
	}				

	public function actionstatus_mgt_list()
	{
        
		$this->preparePaginate();		
		$cols = array();
		
		$cols[]=array(
	 	  'key'=>'status_id',
	 	   'value'=>'color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[color_hex]'=>'color_hex',
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'group_name',
	 	  'value'=>'group_name',	 	  
	 	  'action'=>"format",
	 	  'format'=>'<h6>[group_name]</h6>		 	    
	 	   <p class="dum">
	 	   [title]<br/>
	 	   '.t("Date Created. [date_created]").'
	 	   </p>
	 	   ',
	 	  'format_value'=>array(	 	    
	 	    '[group_name]'=>'group_name',
	 	    '[title]'=>'title',
	 	    '[date_created]'=>array(
	 	         'value'=>'date_created',
		 	     'display'=>"datetime"
	 	      )
	 	  )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'status_id',
	 	  'value'=>'status_id',
	 	  'action'=>"editdelete",
	 	  'id'=>'status_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'status_id'),	
	 	  array('key'=>'group_name'),
	 	  array('key'=>'title'),
	 	);	 	

	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 
		 		 		 
		 FROM {{status_management}} a			
		 		 
		 WHERE 1
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";				 				
		
		AppListing::$action_edit_path = "attributes/status_mgt_update";
		$this->outputListingData($cols,$fields,$stmt);						
	}					
	
	public function actionservices_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'service_id',
	 	   'value'=>'color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[color_hex]'=>'color_hex',
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'service_name',
	 	    'value'=>'service_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[service_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[service_name]'=>'service_name',	 	     
	 	     '[status]'=>'status',	 	     	 	     
	 	     '[status_title]'=>'status_title',	 	
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'service_id',
	 	   'value'=>'service_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'service_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'service_id'),	
	 	  array('key'=>'service_name'),	 	  
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
		 			
		 FROM {{services}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";					 				
		
		AppListing::$action_edit_path = "attributes/services_update";
		$this->outputListingData($cols,$fields,$stmt);		
	}						
		
	public function actionmerchant_type_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	    'key'=>'id',
	 	   'value'=>'color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[color_hex]'=>'color_hex',
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'type_name',
	 	    'value'=>'type_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[type_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>
	 	    <p class="dim">
	 	    '.t("ID. [id]").'
	 	    </p>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[type_name]'=>'type_name',	 	     
	 	     '[status]'=>'status',	 	     	 	     
	 	     '[status_title]'=>'status_title',	 	
	 	     '[id]'=>'id',	
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
	 	  array('key'=>'type_name'),	 	  
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
		 			
		 FROM {{merchant_type}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 				 				
		
		AppListing::$action_edit_path = "attributes/merchant_type_update";
		$this->outputListingData($cols,$fields,$stmt);		
	}						
	
	public function actioncoupon_list()
	{

		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'voucher_id',
	 	   'value'=>'voucher_id'	
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'voucher_name',
	 	    'value'=>'voucher_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[voucher_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    	 	    
	 	    <p class="dim">'.t("Voucher Type").'. [voucher_type]<br>'.t("Discount").'. [amount]<br>'.t("Expiration").'. [expiration]</p>
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
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'voucher_id',
	 	   'value'=>'voucher_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'voucher_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'voucher_id'),	
	 	  array('key'=>'voucher_name'),	 	  
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
		 WHERE voucher_owner = 'admin'		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 		
		
		AppListing::$action_edit_path = "promo/coupon_update";
		$this->outputListingData($cols,$fields,$stmt);					
	}							
	
	public function actionemail_provider_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'as_default',
	 	   'value'=>'as_default',
	 	   'id'=>'id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_email_default_provider" 
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'provider_name',
	 	    'value'=>'provider_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[provider_name]</h6>	 
	 	    <p class="dim">[sender]</p>	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[provider_name]'=>'provider_name',
	 	     '[sender]'=>'sender'
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id',	 	
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'id'),	
	 	  array('key'=>'provider_name'),	 	  
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{email_provider}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 		
		
		AppListing::$action_edit_path = "notifications/provider_update";				
		$this->outputListingData($cols,$fields,$stmt);
	}			

	public function actionupdate_email_provider_default()
	{		
		$id = (integer)Yii::app()->input->post('id');				
		$model = AR_email_provider::model()->findByPk( $id );
		if(!$model){				
			$this->msg = t(HELPER_RECORD_NOT_FOUND);
			$this->jsonResponse();
		}	
		
		$model->as_default = 1;
		$model->save();
		
		$this->code = 1;
		$this->msg = "OK";
		$this->details = array(
		  'next_action'=>"silent",		  
		);
		$this->jsonResponse();
	}
	
	public function actiontemplates_list()
	{
				
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'template_id',
	 	   'value'=>'template_id'	
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'template_name',
	 	    'value'=>'template_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[template_name]</h6>	 
	 	    <p class="dim">[template_key]</p>	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[template_name]'=>'template_name',
	 	     '[template_key]'=>'template_key'
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'template_id',
	 	   'value'=>'template_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'template_id'
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'template_id'),	
	 	  array('key'=>'template_name'),
	 	  array('key'=>'template_key'),
	 	);	 	
	 		 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{templates}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			
		
		AppListing::$action_edit_path = "notifications/template_update";
		$this->outputListingData($cols,$fields,$stmt);		
	}			
	
	public function actionemail_logs()
	{
		$this->preparePaginate();
		$cols = array(); $buttons = array();
		
		$cols[]=array(
	 	   'key'=>'id',
	 	   'value'=>'id'	
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'subject',
	 	    'value'=>'subject',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[subject]</h6>	 
	 	    <p class="dim">	 	    
	 	    '.t("Provider.").' <span class="badge badge-info">[email_provider]</span>
	 	    <br/>
	 	    [content]
	 	    </p>	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[subject]'=>'subject',
	 	     '[email_provider]'=>'email_provider',
	 	     '[content]'=>array(
	 	          'value'=>'content',
		 	      'display'=>"short_text"
	 	       )
	 	    )
	 	);		 	 	

	 	$buttons[]='
		<a href="'.Yii::app()->createUrl("notifications/email_view").'/id/[id]" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
		  >
		  <i class="zmdi zmdi-eye"></i>
		 </a>
		';
		$buttons[]='
		 <a href="javascript:;" data-id="[id]"									  
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		';
		 	
	 	$cols[]=array(
	 	   'key'=>'id',
	 	   'value'=>'id',	 	  
	 	   'id'=>'id',
	 	   'action'=>"custom_buttons",
	 	   'buttons'=>$buttons,
	 	   'format_value'=>array(
	 	     '[id]'=>'id'
	 	   )
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'id'),	
	 	  array('key'=>'subject'),
	 	  array('key'=>'content'),
	 	);	 	
	 		 	
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{email_logs}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		  			
		
		AppListing::$action_edit_path = "notifications/email_logs_update";
		$this->outputListingData($cols,$fields,$stmt);		
	}				
	
	public function actioncustomer_list()
	{
		
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'client_id',
	 	   'value'=>'avatar',	
	 	   'action'=>'customer'
	 	);		 	 		 	
	 	$cols[]=array(
	 	     'key'=>'first_name',
	 	    'value'=>'first_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[first_name] [last_name] <span class="badge ml-2 customer [status]">[status_title]</span></h6>	 
	 	    <p class="dim">'.t("Email").'. [email_address] <br> '.t("Phone").'. [contact_phone]</p>	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[first_name]'=>'first_name',
	 	     '[last_name]'=>'last_name',
	 	     '[status]'=>'status',
	 	     '[status_title]'=>'status_title',
	 	     '[email_address]'=>'email_address',
	 	     '[contact_phone]'=>'contact_phone',
	 	     '[description]'=>array(
	 	          'value'=>'description',
		 	      'display'=>"short_text"
	 	       )
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'client_id',
	 	   'value'=>'client_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'client_id',	 
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'client_id'),	
	 	  array('key'=>'first_name'),
	 	  array('key'=>'last_name'),
	 	  array('key'=>'email_address'),
	 	);	 	
	 		 	
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='customer'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title
 		 		 		
		 FROM {{client}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	 			
		
		AppListing::$action_edit_path = "buyer/customer_update";
		$this->outputListingData($cols,$fields,$stmt);						
	}			
	
	public function actionsubscribers_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'id',
	 	   'value'=>'id',
	 	);		 	 		 	
	 	$cols[]=array(
	 	     'key'=>'email_address',
	 	    'value'=>'email_address',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[email_address]</h6>		 
	 	    <p class="dim">'.t("Date Subscribe").'. [date_created]</p>	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[email_address]'=>'email_address',
	 	     '[date_created]'=>array(
	 	         'value'=>'date_created',
		 	     'display'=>"date"
	 	      )
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
	 	  array('key'=>'email_address'),	 	  
	 	);	 	
	 	 	
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{newsletter}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			
		
		AppListing::$action_edit_path = "notifications/subscriber_update";
		$this->outputListingData($cols,$fields,$stmt);		
	}			
		
	public function actionreview_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
		   'key'=>'id',
	 	   'value'=>'logo',		 	  
	 	   'action'=>"merchant_logo", 	  
		);		 	 		 	
		$cols[]=array(
		    'key'=>'review',
	 	    'value'=>'review',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[review] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    
	 	       <p class="dim">		 	   		 	    
		 	    '.t("Customer. [customer_name]").'
		 	    <br/>
		 	    '.t("Rating. [rating]").'
		 	    <br/>
		 	    '.t("Date. [date_created]").'
		 	    </p>		 
	 	    ',
	 	    'format_value'=>array(
	 	     '[customer_name]'=>'customer_name',
	 	     '[review]'=>'review',
	 	     '[status]'=>'status',	 	     
	 	     '[status_title]'=>'status_title',
	 	     '[rating]'=>'rating',
	 	     '[date_created]'=>array(
	 	         'value'=>'date_created',
		 	     'display'=>"datetime"
	 	      )
	 	    )
		);		 	 		 	
		$cols[]=array(
		  'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id',	
		);
		
		$fields = array(
		  array('key'=>'id'),	
		  array('key'=>'review'),
		  array('key'=>'c.first_name'),
		  array('key'=>'c.last_name'),
		  array('key'=>'a.status'),
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
 
		 IFNULL(b.restaurant_name, a.reply_from) as merchant_name,
		 b.logo,
		 
		 IFNULL( concat(c.first_name,' ',c.last_name) , a.reply_from ) as customer_name
		 		 
		 FROM {{review}} a
		 LEFT JOIN {{merchant}} b
		 ON
		 a.merchant_id = b.merchant_id
		 
		 LEFT JOIN {{client}} c
		 ON
		 a.client_id = c.client_id
		 
		 WHERE 1
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		
		
		AppListing::$action_edit_path = "buyer/review_update";
		$this->outputListingData($cols,$fields,$stmt);					
	}		

	public function actionplans_list()
	{
		
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'sms_package_id',
	 	   'value'=>'sms_package_id',		 	  
	 	   'action'=>"sms_package_id", 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'title',
	 	    'value'=>'title',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[title] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    
	 	       <p class="dim">[description]		 	    
		 	    <br/>
		 	    '.t("Price. [price]").'
		 	    <br/>
		 	    '.t("Credit. [sms_limit]").'
		 	    </p>		 
	 	    ',
	 	    'format_value'=>array(
	 	     '[status]'=>'status',
	 	     '[status_title]'=>'status_title',
	 	     '[title]'=>'title',	 	     
	 	     '[sms_limit]'=>'sms_limit',	 	     	 	     
	 	     '[description]'=>array(
	 	         'value'=>'description',
		 	     'display'=>"short_text"
	 	      ),
	 	      '[price]'=>array(
	 	         'value'=>'price',
		 	     'display'=>"price"
	 	      )
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'sms_package_id',
	 	   'value'=>'sms_package_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'sms_package_id',		 	   
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'sms_package_id'),	
	 	  array('key'=>'title'),
	 	  array('key'=>'description'),
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
 		 		 
		 FROM {{sms_package}} a			 
		 WHERE 1
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 					
		
		AppListing::$action_edit_path = "sms/plan_update";
		$this->outputListingData($cols,$fields,$stmt);		
	}			
	
	public function actionsms_provider_list()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'id',
	 	   'value'=>'as_default',
	 	   'id'=>'id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_default_smsprovider" 
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'provider_name',
	 	    'value'=>'provider_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[provider_name]',
	 	    'format_value'=>array(
	 	     '[provider_name]'=>'provider_name',	 	     
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id',		
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'id'),	
	 	  array('key'=>'provider_name'),	 	  
	 	);	 	
	 	
	 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 
		 FROM {{sms_provider}} a			 
		 WHERE 1
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 		
		
		AppListing::$action_edit_path = "notifications/template_update";
		$this->outputListingData($cols,$fields,$stmt);						
	}			
		
    public function actionset_default_smsprovider()
	{		
		$id = isset($this->data['id'])?(integer)$this->data['id']:0;				
		$model = AR_sms_provider::model()->findByPk( $id );		
		if($model){		
			$model->as_default=1;
			if($model->save()){
				$this->code = 1;
				$this->msg = t(Helper_success);
				$this->details = array(
				);
			} else $this->msg = t(Helper_failed_update);
		} else $this->msg =  t(Helper_not_found);
		$this->jsonResponse();
	}	
	
	public function actionsms_logs()
	{
		$this->preparePaginate();
		$cols = array(); $buttons = array();
		
		$buttons[]='
		<a href="'.Yii::app()->createUrl("sms/sms_view").'/id/[id]" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
		  >
		  <i class="zmdi zmdi-eye"></i>
		 </a>
		';
		$buttons[]='
		 <a href="javascript:;" data-id="[id]"									  
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		';
		
		$cols[]=array(
	 	   'key'=>'a.id',
	 	   'value'=>'id',		 	  	 	   
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'sms_message',
	 	    'value'=>'sms_message',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[sms_message]</h6>
	 	     <p class="dim">
	 	     [contact_phone] / [provider_name]<br/>	 	     
	 	     [status]
	 	     </p>
	 	    ',
	 	    'format_value'=>array(
	 	     '[sms_message]'=>array(
	 	         'value'=>'sms_message',
		 	     'display'=>"short_text"
	 	      ),
	 	     '[status]'=>'status',
	 	     '[contact_phone]'=>'contact_phone',
	 	     '[provider_name]'=>'provider_name',
	 	     '[status]'=>array(
	 	        'value'=>'status',
		 	    'display'=>"short_text"
	 	     ),
	 	    )
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',	 	  
	 	  'id'=>'id',
	 	  'action'=>"custom_buttons",
	 	  'buttons'=>$buttons,
	 	  'format_value'=>array(
	 	    '[id]'=>'id'
	 	  )
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'a.id'),	
	 	  array('key'=>'sms_message'),
	 	  array('key'=>'status'),
	 	);	 	
	 	
	 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 IFNULL(b.provider_name,a.gateway) as provider_name
		 		 		 
		 FROM {{sms_broadcast_details}} a			
		 LEFT JOIN {{sms_provider}} b
		 ON
		 a.gateway = b.provider_id
		  
		 WHERE 1
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 				
		
		AppListing::$action_edit_path = "sms/provider_update";
		$this->outputListingData($cols,$fields,$stmt);						
	}				
	
	public function actionmerchant_registration()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	   'key'=>'merchant_id',
		    'value'=>'logo',
		   'action'=>"merchant_logo", 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	    'key'=>'street',
		 	  'value'=>'street',
		 	  'action'=>"format",
		 	  'format'=>'<h6>[restaurant_name] <span class="badge ml-2 customer [status]">[status_title]</span></h6>		 	   
		 	    <p class="dim">
		 	    [merchant_address]<br/>
		 	    '.t("E.").' [contact_email]<br>'.t("M.").' [contact_phone]<br/>
		 	    '.t("Date Registered.").' [date_created]<br/>
		 	    '.t("Charge Type.").' [merchant_type]
		 	    </p>
		 	  ',
		 	  'format_value'=>array(
		 	    '[restaurant_name]'=>'restaurant_name',
		 	    '[merchant_address]'=>'merchant_address',
		 	    '[contact_email]'=>'contact_email',
		 	    '[contact_phone]'=>'contact_phone',
		 	    '[merchant_type]'=>'merchant_type',
		 	    '[status]'=>'status',
		 	    '[status_title]'=>'status_title',
		 	     '[date_created]'=>array(
		 	         'value'=>'date_created',
			 	     'display'=>"datetime"
		 	     )
		 	  )
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'merchant_id',
	 	   'value'=>'merchant_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'merchant_id',	 	
	 	   'hide_edit'=>true,
	 	   'hide_delete'=>true
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'merchant_id'),	
	 	  array('key'=>'restaurant_name'),	 	  
	 	);	 	
	 		 	 
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 merchant_id,restaurant_name,street,package_id,date_created,logo,
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
		
    public function actionmerchant_payment()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	    'key'=>'a.merchant_id',
	 	    'value'=>'merchant_logo',		 	  
	 	    'action'=>"merchant_logo", 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'a.package_id',
	 	   'value'=>'plan_name',
	 	   'action'=>"format",	
	 	   'format'=>'<h6>[merchant_name] <span class="badge payment [status]">[status_title]</span></h6>
	 	   <p class="dim">
	 	    [plan_name] [price]<br/>
	 	    [payment_name]<br/>
	 	    '.t("Trans. Date").'. [date_created]
	 	   </p>
	 	   ',
	 	   'format_value'=>array( 
	 	     '[merchant_name]'=>'merchant_name',
	 	     '[plan_name]'=>'plan_name',
	 	     '[payment_name]'=>'payment_name',
	 	     '[status]'=>'status',
		 	 '[status_title]'=>'status_title',
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
	 	   'key'=>'merchant_id',
	 	   'value'=>'merchant_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'merchant_id',	 	
	 	   'hide_edit'=>true,
	 	   'hide_delete'=>true
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'a.merchant_id'),	
	 	  array('key'=>'c.title'),	 	  
	 	  array('key'=>'b.restaurant_name'),	 	  
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
		
		AppListing::$action_edit_path = "";

		$this->data = Yii::app()->input->xssClean($_GET);
		
		$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
		$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
		 	
	 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'a.date_created');
	 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');	
		
	 	if(!empty($filter_stmt)){
			$this->search = true;
		}		
		$this->outputListingData($cols,$fields,$stmt,$filter_stmt);
	}			
	
    public function actionmerchant_sales_report()
	{
		
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	    'key'=>'a.merchant_id',
	 	    'value'=>'merchant_logo',		 	  
	 	    'action'=>"merchant_logo", 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'a.client_id',
	 	   'value'=>'customer_name',
	 	   'action'=>"format",	
	 	   'format'=>'<h6>[merchant_name] <span class="badge ml-2 payment [status]">[status_title]</span></h6>	 	   
	 	   <p class="dim">
	 	     #[order_id] [items]<br/>
	 	     [service_name] / [payment_name] / [total_w_tax]<br/>	 	     
	 	     [date_created]
	 	   </p>
	 	   ',
	 	   'format_value'=>array(
	 	     '[order_id]'=>'order_id',
	 	     '[merchant_name]'=>'merchant_name',
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
	 	        'display'=>'price',
	 	        'value'=>'total_w_tax'
	 	     )
	 	   )
	 	);		 	 		 	
	 	$cols[]=array(
	 	   'key'=>'merchant_id',
	 	   'value'=>'merchant_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'merchant_id',	 	
	 	   'hide_edit'=>true,
	 	   'hide_delete'=>true
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'a.order_id'),	 	  
	 	  array('key'=>'a.merchant_id'),	 	  
	 	);	 	
	 		
		$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 b.logo as merchant_logo,
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
		
		AppListing::$action_edit_path = "";

		$this->data = Yii::app()->input->xssClean($_GET);
		
		$filter_stmt='';
	 	$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
	 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
	 	$filter_merchant=isset($this->data['filter_merchant'])?(integer)$this->data['filter_merchant']:0;
	 	
	 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'a.date_created');
	 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
	 	
	 	if($filter_merchant>0){
	 		$filter_stmt.="\n AND a.merchant_id=".q($filter_merchant)."";
	 	}
		
	 	if(!empty($filter_stmt)){
			$this->search = true;
		}
				
		$this->outputListingData($cols,$fields,$stmt,$filter_stmt);
	}				
	
    public function actionbooking_summary()
	{
		$this->preparePaginate();
		$cols = array();
		
		$cols[]=array(
	 	    'key'=>'a.merchant_id',
	 	    'value'=>'merchant_logo',		 	  
	 	    'action'=>"merchant_logo", 	  
	 	);		 	 		 	
	 	$cols[]=array(
	 	  'key'=>'a.merchant_name',
	 	   'value'=>'merchant_name',
	 	   'action'=>"format",	
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
	 	   'key'=>'merchant_id',
	 	   'value'=>'merchant_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'merchant_id',	 	
	 	   'hide_edit'=>true,
	 	   'hide_delete'=>true
	 	);
	 	
	 	$fields = array(
	 	  array('key'=>'a.merchant_id'),	
	 	  array('key'=>'b.restaurant_name'),
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

		
		 WHERE 1
		 [and]
		 [search]
		 group by a.merchant_id
		 [order]			 
		 [limit]
	 	";				 			
		
		AppListing::$action_edit_path = "";

		$this->data = Yii::app()->input->xssClean($_GET);
		
		$filter_stmt='';
 		$filter_date=isset($this->data['date_filter'])?$this->data['date_filter']:'';
	 	$status_filter=isset($this->data['status_filter'])?$this->data['status_filter']:'';
	 	$filter_merchant=isset($this->data['filter_merchant'])?(integer)$this->data['filter_merchant']:0;
	 	
	 	$filter_stmt = DatatablesTools::FilterDates($filter_date,'a.date_created');
	 	$filter_stmt.= DatatablesTools::FilterStatus($status_filter,'a.status');
	 	
	 	if($filter_merchant>0){
	 		$filter_stmt.="\n AND a.merchant_id=".q($filter_merchant)."";
	 	}
	 	
	 	if(!empty($filter_stmt)){
			$this->search = true;
		}		
		$this->outputListingData($cols,$fields,$stmt,$filter_stmt);
	}			
	
	public function actionpayment_history()
	{
		$id = (integer) Yii::app()->input->get('id');			
		
		$this->preparePaginate();
		$cols = array();
		
		$fields = array(
		  array('key'=>'a.package_id'),	
		  array('key'=>'b.title'),	 	  
		);	 	
		
		$cols[]=array(
	 	  'key'=>'id',
	 	  'value'=>'id',	 	 
	 	);		 
	 	
		$cols[]=array(
	 	  'key'=>'package_id',
	 	  'value'=>'package_name',
	 	  'action'=>'format',
	 	  'format'=>'<h6>[package_name]</h6>
	 	  <p class="dim">
	 	   [price]<br/>
	 	   [membership_expired]<br/>
	 	   [payment_type]<br/>
	 	   [status]<br/>	 	   
	 	  </p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[package_name]'=>'package_name',
	 	    '[price]'=>array(
	 	      'value'=>'price',
	 	      'display'=>"price"
	 	    ),
	 	    '[membership_expired]'=>array(
	 	      'value'=>'membership_expired',
	 	      'display'=>"date"
	 	    ),
	 	    '[payment_type]'=>'payment_type',
	 	    '[status]'=>'status',
	 	  )
	 	);		 
	 		 				 
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"datetime"		 	  
	 	);	
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.package_id,
		 a.price, a.membership_expired, a.payment_type, a.status, a.date_created,			 
		 IFNULL(b.title, a.package_id ) as package_name
		 			 
		 FROM {{package_trans}} a
		 LEFT JOIN {{packages}} b
		 ON
		 a.package_id = b.package_id
		 
		 WHERE a.merchant_id = ".q($id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 
				
		AppListing::$action_edit_path = "";		
		$this->outputListingData($cols,$fields,$stmt);						
	}					
		
}
/*end class*/