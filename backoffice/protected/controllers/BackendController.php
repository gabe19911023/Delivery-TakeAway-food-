<?php
class BackendController extends CommonController
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
			
	public function actionAlluser()
	{		
						 
	 	$cols[]=array(
	 	  'key'=>'admin_id',
	 	  'value'=>'profile_photo',
	 	  'action'=>"customer",
	 	);
	 	$cols[]=array(
	 	  'key'=>'first_name',
	 	  'value'=>'fullname',
	 	  'action'=>"format",
	 	  'format'=>'<h6>[fullname] <span class="badge ml-2 customer [status]">[status]</span></h6>
	 	    <p class="dim">'.t("E").'. [email_address]<br>'.t("M").'. [contact_number]</p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[fullname]'=>'fullname',
	 	    '[email_address]'=>'email_address',
	 	    '[contact_number]'=>'contact_number',
	 	    '[status]'=>'status',
	 	  )
	 	);
	 	$cols[]=array(
	 	  'key'=>'role',
	 	  'value'=>'role_name'
	 	);
	 	$cols[]=array(
	 	  'key'=>'a.date_created',
	 	  'value'=>'date_created',
	 	  'action'=>"editdelete",
	 	  'id'=>'admin_id_token'
	 	);
	 	
	 	$stmt = "
	 	 SELECT 
		 admin_id,admin_id_token,concat(first_name,' ',last_name) as fullname,role,
		 profile_photo,path,email_address,contact_number,a.date_created,status,
		 b.role_name
		 
		 FROM {{admin_user}} a		 
		 LEFT JOIN {{role}} b
		 ON
		 a.role = b.role_id
		 
		 WHERE main_account=0
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 	
	 	
	 	DatatablesTools::$action_edit_path = "user/update";
	 	
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}		 
		$this->DataTablesNodata();
	}
	
	public function actionRoleList()
	{		
		 	$cols[]=array(
		 	  'key'=>'role_id',
		 	  'value'=>'role_id'		 	  
		 	);		 
		 	$cols[]=array(
		 	  'key'=>'role_name',
		 	  'value'=>'role_name'		 	  
		 	);		 
		 	$cols[]=array(
		 	  'key'=>'role_id',
		 	  'value'=>'access_count',
		 	  'action'=>"format",
		 	  'format'=>'<h6>[access_count]</h6><p class="dim">'.t("permission").'</span>',
		 	  'format_value'=>array(
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
			 WHERE role_type='admin'
			 [and]
			 [search]
			 [order]
			 [limit]
		 	";		 	
		 			 			 	
		 	DatatablesTools::$action_edit_path = "user/role_update";
		 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();
	}		
	
	public function actionmerchant_list()
	{		

		$edit_link = Yii::app()->createUrl("/vendor/edit",array(		  
			'id'=>'-id-',				 
		  ));   
		$auto_login = Yii::app()->createUrl("/vendor/autologin",array(		  
			'merchant_uuid'=>'-merchant_uuid-',				 
		));   
		  
		$html='
		<div class="btn-group btn-group-actions" role="group" >				
						
			<a href="'.$edit_link.'" class="btn btn-light tool_tips"
			data-toggle="tooltip" data-placement="top" title="'.t("Edit").'"
			>
			<i class="zmdi zmdi-border-color"></i>
			</a>		
			
			<a href="javascript:;" data-id="-id-" 
			class="btn btn-light datatables_delete tool_tips"
			data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
			>
			<i class="zmdi zmdi-delete"></i>
			</a>			
		
			<a href="'.$auto_login.'" class="btn btn-light tool_tips"
			data-toggle="tooltip" data-placement="top" title="'.t("Auto Login").'"
			target="_blank"
			>
			<i class="zmdi zmdi-sign-in"></i>
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
		 	  'key'=>'address',
		 	  'value'=>'address',
		 	  'action'=>"format",
		 	  'format'=>'<h6>[address] <span class="badge ml-2 customer [status]">[status_title]</span></h6>
		 	    <p class="dim">'.t("E").'. [contact_email]<br>'.t("M").'. [contact_phone]</p>
		 	  ',
		 	  'format_value'=>array(
		 	    '[address]'=>'address',
		 	    '[contact_email]'=>'contact_email',
		 	    '[contact_phone]'=>'contact_phone',
		 	    '[status]'=>'status',
		 	    '[status_title]'=>"status_title"
		 	  )
		 	);
		 	
		 	$cols[]=array(
		 	  'key'=>'merchant_type',
		 	  'value'=>'merchant_type'		 	  
		 	);		 
		 	// $cols[]=array(
		 	//   'key'=>'a.date_created',
		 	//   'value'=>'date_created',
		 	//   'action'=>"editdelete",
		 	//   'id'=>'merchant_id'
		 	// );
			$cols[]=array(
			   'key'=>'merchant_type',
		 	  'value'=>'merchant_type',
			   'action'=>"format",
			   'format'=>$html,	 	 
			   'format_value'=>array(	 	   
				  '-id-'=>'merchant_id',
				  '-merchant_uuid-'=>'merchant_uuid'
			    )  	 	  
			);
		 			 	
		 	$stmt = "
		 	 SELECT SQL_CALC_FOUND_ROWS
			 a.merchant_id,a.merchant_uuid,a.restaurant_name,a.package_id,a.date_created,a.logo,a.path,
			 a.contact_phone,a.contact_email,a.status,
			 a.address,
			 
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
		 			 			 			 
		 	DatatablesTools::$action_edit_path = "vendor/edit";
		 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();
	}
	
	public function actionsponsored_list()
	{			 
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
		 	  'key'=>'sponsored_expiration',
		 	  'value'=>'sponsored_expiration',
		 	  'action'=>"date"
		 	);		 
		 	
		 	
		 	$cols[]=array(
		 	  'key'=>'date_created',
		 	  'value'=>'date_created',
		 	  'action'=>"editdelete",
		 	  'id'=>'merchant_id'
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
		 			 			 	
		 	DatatablesTools::$action_edit_path = "vendor/edit_sponsored";
		 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();		
	}
	
	public function actionpayment_history()
	{
		    $id = (integer) Yii::app()->input->post('id');	
		    	
		 	$cols[]=array(
		 	  'key'=>'package_id',
		 	  'value'=>'package_name',		 	  
		 	);		 
		 	$cols[]=array(
		 	  'key'=>'price',
		 	  'value'=>'price',
		 	  'action'=>"price"		 	  
		 	);		 
		 			
		 	$cols[]=array(
		 	  'key'=>'membership_expired',
		 	  'value'=>'membership_expired',
		 	  'action'=>"date"
		 	);		 
		 			 	
		 	$cols[]=array(
		 	  'key'=>'payment_type',
		 	  'value'=>'payment_type'		 	  
		 	);		 
		 	
		 	$cols[]=array(
		 	  'key'=>'status',
		 	  'value'=>'status'		 	  
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
			 [order]
			 [limit]
		 	";		 			 	
		 			 			 			 	
		 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
		 		$this->DataTablesData($feed_data);
		 		Yii::app()->end();
		 	}		 
		 $this->DataTablesNodata();		
	}
	
	public function actioncsv_list()
	{
		$cols[]=array(
	 	  'key'=>'csv_id',
	 	  'value'=>'csv_id',		 	  
	 	);		 
	 	$cols[]=array(
	 	  'key'=>'filename',
	 	  'value'=>'filename',	 	
	 	  'action'=>"format",
	 	  'format'=>'<h6>[filename] </h6>
	 	    <p class="dim">[file_path]</p>
	 	  ',
	 	  'format_value'=>array(
	 	    '[filename]'=>'filename',
	 	    '[file_path]'=>'file_path',	 	    
	 	  )  
	 	);		 	 
	 	$cols[]=array(
	 	  'key'=>'date_created',
	 	  'value'=>'date_created',	
	 	  'action'=>"datetime"		 	  
	 	);		 
	 	$cols[]=array(
	 	  'key'=>'csv_id',
	 	  'value'=>'csv_id',	
	 	  'action'=>"format", 	  
	 	  'format'=>'<div class="csv_progress_[id]"><h6>[count] out of [process_count]</h6>
	 	    <p class="dim">'.t("process").'</p></div>
	 	  ',
	 	  'format_value'=>array(
	 	    '[count]'=>'count',
	 	    '[process_count]'=>'process_count',
	 	    '[id]'=>'csv_id'
	 	  )  
	 	);		 
	 	$cols[]=array(
	 	  'key'=>'csv_id',
	 	  'value'=>'csv_id',
	 	  'action'=>"view_delete_process",
	 	  'id'=>'csv_id'
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
	 	
	 	DatatablesTools::$action_edit_path = "vendor/csv_view";	 	
	 	DatatablesTools::$process_link = "process_csv";
	 			 			 			 	
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();		
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
	
	public function actioncsv_list_details()
	{
		
		$id = (integer) Yii::app()->input->post('id');	
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
		  ),
		  array(
		   'key'=>'restaurant_name',
	 	   'value'=>'restaurant_name',		 	  
		  ),
		  array(
		   'key'=>'street',
	 	   'value'=>'street',	
	 	   'action'=>"format",
	 	    'format'=>'<h6>[merchant_address] <span class="badge ml-2 transaction [status]">[status_title]</span></h6>
		 	    <p class="dim">
		 	    '.t("E. [contact_email]").'<br/>
	 	        '.t("M. [contact_phone]").'<br/>
		 	    </p>
		 	  ',
		 	  'format_value'=>array(
		 	    '[merchant_address]'=>'merchant_address',
		 	    '[contact_email]'=>'contact_email',
		 	    '[contact_phone]'=>'contact_phone',
		 	    '[status]'=>'process_status',
		 	    '[status_title]'=>'status_title',
		 	  )	 	  
		  ),
		  array(
		   'key'=>'merchant_type',
	 	   'value'=>'merchant_type',		 	  
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
		  ),
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
	 		 		 		 			 		 	
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();		
	}	
	
	public function actionplan_list()
	{
		$cols=array(
		  array(
		   'key'=>'package_id',
	 	   'value'=>'package_id',		 	  
		  ),
		  array(
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
	 	       )
	 	      )
		  ),
		  array(
		   'key'=>'price',
	 	   'value'=>'description',	
	 	   'action'=>"format",
	 	   'format'=>'
		 	    <p class="dim">'.t("Price").'. [price]
		 	    <br>'.t("Promo").'. [promo_price]		 	    
		 	    </p>
		 	  ',
		 	  'format_value'=>array(			 	     	 	    		 	    		 	 
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
		  ),		  
		  array(
		   'key'=>'package_id',
	 	   'value'=>'package_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'package_id'
		  ),
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
		 		
		 FROM {{plans}} a
		 WHERE plan_type='membership'
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "plans/update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actionorder_list()
	{
	
		$cols=array(
		  array(
		   'key'=>'a.order_id',
	 	   'value'=>'logo',		 	  
	 	   'action'=>"merchant_logo", 	  
		  ),
		  array(
		   'key'=>'a.order_id',
	 	   'value'=>'order_id',		 	  	 	   	 
		  ),
		  array(
		   'key'=>'b.restaurant_name',
	 	   'value'=>'restaurant_name',		 	  	 	   
		  ),
		  array(
		   'key'=>'a.client_id',
	 	   'value'=>'customer_name',		
	 	   'action'=>"format",
	 	   'format'=>'<h6>[customer_name]</h6',
	 	   'format_value'=>array(
	 	     '[status]'=>'status',
	 	     '[customer_name]'=>'customer_name'
	 	   )
		  ),		  
		  array(
		   'key'=>'sub_total',
	 	   'value'=>'sub_total',		 	   
	 	   'action'=>"format",
	 	   'format'=>t("[total_items] items").' <span class="badge ml-2 order_status [status]">[status_title]</span>
	 	    <p class="dim">[payment_type]
	 	    <br>'.t("Transactions. [service_name]").'
	 	    <br>'.t("Total. [total_w_tax]").'	 	    
	 	    <br>'.t("Date. [date_created]").'	 	    
	 	    </p>
	 	  ',
	 	  'format_value'=>array(	
	 	     '[total_items]'=>'total_items',	 	     
	 	     '[payment_type]'=>'payment_name',	 
	 	     '[status]'=>'status',
	 	     '[status_title]'=>'status_title',
	 	     '[service_name]'=>'service_name',
	 	     '[total_w_tax]'=>array(
	 	       'value'=>'total_w_tax',
		 	   'display'=>"price"
	 	     ),
	 	     '[date_created]'=>array(
	 	       'value'=>'date_created',
		 	   'display'=>"datetime"
	 	     )
	 	    )	 	  
		  ),		  
		  array(
		   'key'=>'a.order_id',
	 	   'value'=>'order_id',		 	  
	 	   'action'=>"orders",
	 	   'id'=>'order_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.order_id,a.order_id_token,a.merchant_id,a.client_id,a.sub_total,a.payment_type,a.total_w_tax,a.status,
		 a.date_created,
		 b.restaurant_name,b.logo,b.path,
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "order/update";
	 	DatatablesTools::$action_view_path = "order/view";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
		
	}
	
	public function actionorder_list_cancel()
	{
		$cols=array(
		  array(
		   'key'=>'logo',
	 	   'value'=>'logo',		 	  
	 	   'action'=>"merchant_logo", 	  
		  ),
		  array(
		   'key'=>'a.order_id',
	 	   'value'=>'order_id',		 	  	 	   	 
		  ),
		  array(
		   'key'=>'b.restaurant_name',
	 	   'value'=>'restaurant_name',		 	  	 	   
		  ),
		    array(
		   'key'=>'a.client_id',
	 	   'value'=>'customer_name',		
	 	   'action'=>"format",
	 	   'format'=>'<h6>[customer_name]</h6',
	 	   'format_value'=>array(
	 	     '[status]'=>'status',
	 	     '[customer_name]'=>'customer_name'
	 	   )
		  ),		  
		   array(
		   'key'=>'sub_total',
	 	   'value'=>'sub_total',		 	   
	 	   'action'=>"format",
	 	   'format'=>t("[total_items] items").' <span class="badge ml-2 order_status [status]">[status_title]</span>
	 	    <p class="dim">[payment_type]
	 	    <br>'.t("Transactions. [service_name]").'
	 	    <br>'.t("Total. [total_w_tax]").'	 	    
	 	    <br>'.t("Date. [date_created]").'	 	    
	 	    </p>
	 	  ',
	 	  'format_value'=>array(	
	 	     '[total_items]'=>'total_items',	 	     
	 	     '[payment_type]'=>'payment_name',	 
	 	     '[status]'=>'status',
	 	     '[status_title]'=>'status_title',
	 	     '[service_name]'=>'service_name',
	 	     '[total_w_tax]'=>array(
	 	       'value'=>'total_w_tax',
		 	   'display'=>"price"
	 	     ),
	 	     '[date_created]'=>array(
	 	       'value'=>'date_created',
		 	   'display'=>"datetime"
	 	     )
	 	    )	 	  
		  ),
		  array(
		   'key'=>'a.order_id',
	 	   'value'=>'order_id',		 	  
	 	   'action'=>"orders",
	 	   'id'=>'order_id'
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "order/update";
	 	DatatablesTools::$action_view_path = "order/view";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
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
		$cols=array(
		  array(
		   'key'=>'cuisine_id',
	 	   'value'=>'featured_image',	
	 	   'action'=>'item_photo'
		  ),
		  array(
		    'key'=>'cuisine_name',
	 	    'value'=>'cuisine_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[cuisine_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[cuisine_name]'=>'cuisine_name',	 	     
	 	     '[status]'=>'status',	 	     	 	     
	 	     '[status_title]'=>'status_title',	 	
	 	    )
		  ),
		  array(
		   'key'=>'cuisine_id',
	 	   'value'=>'cuisine_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'cuisine_id'
		  ),
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
	 		 		 	 	
	 	DatatablesTools::$action_edit_path = "attributes/cuisine_update";
	 			 			 
	 	$this->data['path'] = "/upload/0";
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actiondishes_list()
	{
		$cols=array(
		  array(
		   'key'=>'dish_id',
	 	   'value'=>'photo',	
	 	   'action'=>'item_photo'
		  ),
		  array(
		    'key'=>'dish_name',
	 	    'value'=>'dish_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[dish_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[dish_name]'=>'dish_name',	 	     
	 	     '[status]'=>'status',	 	     	 	     
	 	     '[status_title]'=>'status_title',	 	    
	 	    )
		  ),
		  array(
		   'key'=>'dish_id',
	 	   'value'=>'dish_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'dish_id'
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/dish_update";
	 			 		
	 	$this->data['path'] = "/upload/0";	 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actiontags_list()
	{
		$cols=array(
		  array(
		   'key'=>'tag_id',
	 	   'value'=>'tag_id'	 	   
		  ),
		  array(
		    'key'=>'tag_name',
	 	    'value'=>'tag_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[tag_name]</h6>
	 	    <p class="dim">[description]</p>
	 	    ',
	 	    'format_value'=>array(
	 	     '[tag_name]'=>'tag_name',
	 	     '[description]'=>'description'
	 	    )
		  ),
		  array(
		   'key'=>'tag_id',
	 	   'value'=>'tag_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'tag_id'
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/tags_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	
	
	public function actionstatus_list()
	{
		$cols=array(
		  array(
		   'key'=>'stats_id',
	 	   'value'=>'background_color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[background_color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[background_color_hex]'=>'background_color_hex',
	 	    )
		  ),
		  array(
		    'key'=>'description',
	 	    'value'=>'description',
	 	    'action'=>"format",
	 	    'format'=>'<h6>[description]</h6>
	 	    <p class="dim">[date_created]</p>
	 	    ',
	 	    'format_value'=>array(
	 	     '[description]'=>'description',
	 	     '[date_created]'=>array(
	 	          'value'=>'date_created',
		 	     'display'=>"date"
	 	     )
	 	    )
		  ),
		  array(
		   'key'=>'stats_id',
	 	   'value'=>'stats_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'stats_id'
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/status_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actioncurrency_list()
	{
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id'	 	   
		  ),
		  array(
		   'key'=>'as_default',
	 	   'value'=>'as_default',
	 	   'id'=>'id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_default_currency"
		  ),
		   array(
		    'key'=>'currency_code',
	 	    'value'=>'currency_code',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[description] ([currency_code])</h6>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[description]'=>'description',
	 	     '[currency_code]'=>'currency_code'
	 	    )
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{currency}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/currency_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	
	
	public function actionupdate_currency_default()
	{		
		$id = isset($this->data['id'])?(integer)$this->data['id']:0;
		$model = AR_currency::model()->findByPk( $id );		
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
	
	public function actioncoupon_list()
	{
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
		  ),
		  array(
		   'key'=>'total_used',
	 	   'value'=>'total_used',	
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
		 {{ordernew}}
		 where
		 promo_code=a.voucher_name			
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "promo/coupon_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
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
    	
    	header('Content-type: application/json');
		echo CJSON::encode($result);
		Yii::app()->end();
	}
	
	public function actionsearch_customer()
	{
		$res = array();
		try {
			$search = isset($this->data['search'])?$this->data['search']:'';
			$res  = CustomerAR::search($search);			
		} catch (Exception $e) {
			//echo $e->getMessage();
		}		
		$result = array(
    	  'results'=>$res
    	);	    	
    	
    	header('Content-type: application/json');
		echo CJSON::encode($result);
		Yii::app()->end();
	}
	
	public function actionpages_list()
	{
		$cols=array(
		  array(
		   'key'=>'page_id',
	 	   'value'=>'page_id'	 	   
		  ),
		   array(
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
		  ),
		  array(
		   'key'=>'page_id',
	 	   'value'=>'page_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'page_id'
		  ),
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
		 WHERE owner='admin'		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/page_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	

	public function actionemail_provider_list()
	{
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id'	 	   
		  ),		   
		  array(
		   'key'=>'as_default',
	 	   'value'=>'as_default',
	 	   'id'=>'id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_email_default_provider"
		  ),
		  array(
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
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id',	 	   
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "notifications/provider_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	
	
	
	public function actiontemplates_list()
	{
		$cols=array(
		  array(
		   'key'=>'template_id',
	 	   'value'=>'template_id'	 	   
		  ),		   
		   array(
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
		  ),
		   array(
		   'key'=>'enabled_email',
	 	   'value'=>'enabled_email',
	 	   'id'=>'template_id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_template_email"
		  ),
		  array(
		   'key'=>'enabled_sms',
	 	   'value'=>'enabled_sms',
	 	   'id'=>'template_id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_template_sms"
		  ),	
		   array(
		   'key'=>'enabled_push',
	 	   'value'=>'enabled_push',
	 	   'id'=>'template_id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_template_push"
		  ),	
		  array(
		   'key'=>'template_id',
	 	   'value'=>'template_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'template_id'
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "notifications/template_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	
	
	public function actionset_template()
	{		
		$id = (integer) Yii::app()->input->post('id');	
		$checked = (integer) Yii::app()->input->post('checked');	
		$method = (string) Yii::app()->input->post('method');				
		$model = AR_templates::model()->findByPk( $id );			
		if($model){
			switch ($method) {
				case "email":					
					$model->enabled_email=$checked;
					break;
				case "sms":					
					$model->enabled_sms=$checked;
					break;
				case "push":					
					$model->enabled_push=$checked;
					break;				
				default:
					break;
			}		
			if($model->save()){
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array();
			}
		} else $this->msg = t("Record not found");
		$this->jsonResponse();
	}
	
	public function actionemail_logs()
	{
		
		$view_link = Yii::app()->createUrl("/notifications/email_view",array(		  
		  'id'=>'-id-',				 
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
		
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id'	 	   
		  ),		   				
		  array(
		   'key'=>'email_address',
	 	   'value'=>'email_address'	 	   
		  ),
		  array(
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
		  ),
		  array(
		   'key'=>'status',
	 	   'value'=>'status'	 	   
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"format",
	 	   'format'=>$html,	 	 
	 	   'format_value'=>array(	 	   
	 	     '-id-'=>'id',
	 	   )  
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "notifications/email_logs_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}		
	
    public function actionlanguage_list()
	{
							
		$edit_link = Yii::app()->createUrl("/attributes/language_update",array(		  
		  'id'=>'-id-',				 
		));   
		
		$link_admin = Yii::app()->createUrl("attributes/language_translation",array(		  
		  'id'=>'-id-',				 
		  'category'=>'backend'
		));   

		$link_front = Yii::app()->createUrl("attributes/language_translation",array(		  
			'id'=>'-id-',				 
			'category'=>'front'
		  ));   
		
		$link_backend_export = Yii::app()->createUrl("attributes/language_export",array(		  
			'id'=>'-id-',				 
			'category'=>'backend'
		  ));   

		$link_front_export = Yii::app()->createUrl("attributes/language_export",array(		  
			'id'=>'-id-',				 
			'category'=>'front'
		  ));     

		  $link_backend_download = Yii::app()->createUrl("attributes/language_download",array(		  
			'id'=>'-id-',				 
			'category'=>'backend'
		  ));       

		  $link_front_download = Yii::app()->createUrl("attributes/language_download",array(		  
			'id'=>'-id-',				 
			'category'=>'front'
		  ));       
		
		  
		$html='
		<div class="btn-group btn-group-actions" role="group" >				
				  
		  <a href="'.$link_admin.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Backend Translation").'"
		  >
		  <i class="zmdi zmdi-file-text"></i>
		  </a>		

		  <a href="'.$link_front.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Front Translation").'"
		  >
		  <i class="zmdi zmdi-file-text"></i>
		  </a>		
		  
		  <a href="'.$edit_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Edit").'"
		  >
		  <i class="zmdi zmdi-border-color"></i>
		  </a>		
		  
		 <a href="javascript:;" data-id="-id-" 
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>

		  <div class="dropdown dropleft">
			<button class="btn dropdown-togglex" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="zmdi zmdi-more-vert"></i>
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<a class="dropdown-item" target="_blank" href="'.$link_backend_export.'"><i class="zmdi zmdi-cloud-download"></i> '.t("Export Backend translation").'</a>
				<a class="dropdown-item" target="_blank" href="'.$link_front_export.'"><i class="zmdi zmdi-cloud-download"></i> '.t("Export Front translation").'</a>				
				<a class="dropdown-item" target="_blank" href="'.$link_backend_download.'"><i class="zmdi zmdi-cloud-download"></i> '.t("Download Blank Backend translation").'</a>				
				<a class="dropdown-item" target="_blank" href="'.$link_front_download.'"><i class="zmdi zmdi-cloud-download"></i> '.t("Download Blank Front translation").'</a>				
			</div>
			</div>
		  
		</div>
		';	
		
		
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id'	 	   
		  ),		   						  
		  array(
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
	 	       )
	 	    )
		  ),
		  array(
		   'key'=>'sequence',
	 	   'value'=>'sequence'	 	   
		  ),		   						  
		   array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"format",
	 	   'format'=>$html,	 	 
	 	   'format_value'=>array(	 	   
	 	     '-id-'=>'id',
	 	   )  
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/language_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}			
	
	public function actioncustomer_list()
	{
		$cols=array(
		  array(
		   'key'=>'client_id',
	 	   'value'=>'avatar',	
	 	   'action'=>'customer'
		  ),		   				
		  array(
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
		  ),
		  array(
		   'key'=>'client_id',
	 	   'value'=>'client_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'client_id',	 	   
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "buyer/customer_update";
	 			 			 
	 	$this->data['path'] = "/upload/0";
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}			
		
	public function actionsubscribers_list()
	{
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	   
		  ),		   				
		  array(
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
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id',	 	
	 	   'hide_edit'=>true
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "buyer/subscriber_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}				
	
	public function actionaddress_list()
	{
		$id = (integer) Yii::app()->input->post('id');		
		
		$cols=array(
		  array(
		   'key'=>'address_id',
	 	   'value'=>'address_id',		 	   
		  ),		   				
		  array(
		    'key'=>'formatted_address',
	 	    'value'=>'formatted_address',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[formatted_address]</h6>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[formatted_address]'=>'formatted_address',	 	     
	 	     '[date_created]'=>array(
	 	         'value'=>'date_created',
		 	     'display'=>"date"
	 	      )
	 	    )
		  ),
		  array(
		   'key'=>'address_id',
	 	   'value'=>'address_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'address_id',	 
	 	   'primary_key'=>'ad_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{client_address}} a
		 WHERE client_id = ".q(intval($id))." 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "buyer/address_update/id/$id";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actioncustomer_order_list()
	{
	    $id = (integer) Yii::app()->input->post('id');	 
	    
	    $cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'logo',		 	  
	 	   'action'=>"merchant_logo", 	  
		  ),
		  array(
		   'key'=>'a.order_id',
	 	   'value'=>'order_id',		 	  	 	   	 
		  ),
		  array(
		   'key'=>'b.restaurant_name',
	 	   'value'=>'restaurant_name',		 	  	 	   
		  ),
		  array(
		   'key'=>'a.client_id',
	 	   'value'=>'customer_name',		
	 	   'action'=>"format",
	 	   'format'=>'<h6>[customer_name]</h6',
	 	   'format_value'=>array(
	 	     '[status]'=>'status',
	 	     '[customer_name]'=>'customer_name'
	 	   )
		  ),		  
		  array(
		   'key'=>'sub_total',
	 	   'value'=>'sub_total',		 	   
	 	   'action'=>"format",
	 	   'format'=>'[total_items] '.t("items").' <span class="badge ml-2 order_status [status]">[status]</span>
	 	    <p class="dim">[payment_type]
	 	    <br>'.t("Total").'. [total_w_tax]	 	    
	 	    </p>
	 	  ',
	 	  'format_value'=>array(	
	 	     '[total_items]'=>'total_items',	 	     
	 	     '[payment_type]'=>'payment_name',	 
	 	     '[status]'=>'status',
	 	     '[total_w_tax]'=>array(
	 	       'value'=>'total_w_tax',
		 	   'display'=>"price"
	 	     )
	 	    )	 	  
		  ),		  
		  array(
		   'key'=>'a.order_id',
	 	   'value'=>'order_id',		 	  
	 	   'action'=>"orders",
	 	   'id'=>'order_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.order_id,a.order_id_token,a.merchant_id,a.client_id,a.sub_total,a.payment_type,a.total_w_tax,a.status,
		 b.restaurant_name,b.logo,
		 concat(c.first_name,' ',c.last_name) as customer_name,
		 IFNULL(d.payment_name,".q(t('Not available')).") as payment_name,
		 
		 (
		  select count(*)
		  from {{order_details}}
		  where order_id=a.order_id
		 ) as total_items
		 		 
		 
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
		 
		 WHERE a.status NOT IN (".q( AttributesTools::initialStatus() ).")
		 AND a.client_id=".q($id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "order/update";
	 	DatatablesTools::$action_view_path = "order/view";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	  
	}
	
	public function actioncustomer_booking()
	{
		$id = (integer) Yii::app()->input->post('id');		
		
		$cols=array(
		  array(
		   'key'=>'booking_id',
	 	   'value'=>'booking_id',		 	   
		  ),		   						 
		  array(
		    'key'=>'booking_name',
	 	    'value'=>'booking_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[merchant_name] <span class="badge ml-2 booking [status]">[status]</span></h6>
		 	    <p class="dim">		 	   
		 	    '.t("Guest Name. [guest_name]").'
		 	    <br/>
		 	    '.t("E. [email_address]").'
		 	    <br/>
		 	    '.t("No of guest# [number_guest]").'
		 	    <br/>
		 	    '.t("Booking Date/Time [date_booking]").'
		 	    </p>		 	  
		 	',
	 	    'format_value'=>array(
	 	     '[guest_name]'=>'booking_name',	 	     
	 	     '[status]'=>'status',	
	 	     '[number_guest]'=>'number_guest',
	 	     '[mobile_number]'=>'mobile',
	 	     '[email_address]'=>'email',	 	     
	 	     '[merchant_name]'=>'merchant_name',
	 	     '[date_booking]'=>array(
	 	         'value'=>'booking_date',
		 	     'display'=>"datetime"
	 	      )
	 	    )
		  ),
		  array(
		   'key'=>'booking_id',
	 	   'value'=>'booking_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'booking_id',	 
	 	   'primary_key'=>'bk_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*, concat(date_booking,' ',booking_time) as booking_date,
		 a.booking_name as guest_name,
		 b.restaurant_name as merchant_name
		 
		 FROM {{bookingtable}} a
		 LEFT JOIN {{merchant}} b
		 ON
		 a.merchant_id = b.merchant_id
		 
		 WHERE client_id = ".q($id)." 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "buyer/booking_update/id/$id";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actionreview_list()
	{
			
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'logo',		 	  
	 	   'action'=>"merchant_logo", 	  
		  ),		   				
		  array(
		   'key'=>'merchant_id',
	 	   'value'=>'merchant_name',		 	   
		  ),
		  array(
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
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id',		 	   
		  ),
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
		 b.logo, b.path,
		 
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "buyer/review_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actioncountry_list()
	{
		$define_link = Yii::app()->createUrl("/location/state_list",array(
	      'country_id'=>'-country_id-',				 
	    ));   
	    
	    $update_link = Yii::app()->createUrl("/location/country_update",array(
	      'id'=>'-id-',				 
	    ));   
	    
		$html='
		<div class="btn-group btn-group-actions" role="group" >
		
		 <a href="'.$define_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Define Locations").'"
		  >
		  <i class="zmdi zmdi-collection-item"></i>
		  </a>		
		  
		  <a href="'.$update_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		  >
		  <i class="zmdi zmdi-border-color"></i>
		  </a>		
		  
		 <a href="javascript:;" data-id="-country_id-" 
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		  
		</div>
		';
					
		$cols=array(
		  array(
		   'key'=>'country_id',
	 	   'value'=>'country_id',		 	  	 	   
		  ),		   				
		  array(
		   'key'=>'country_name',
	 	   'value'=>'country_name',		 	   
		  ),		 
		  array(
		   'key'=>'country_id',
	 	   'value'=>'country_id',
	 	   'action'=>"format",	 	   
	 	   'format'=>$html,
	 	   'format_value'=>array(
	 	     '-country_id-'=>'country_id',
	 	     '-id-'=>'country_id',
	 	   )
		  ),
		);
	 	
	    $stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{location_countries}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		  			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "location/country_update";	 		 	
	 	DatatablesTools::$action_view_path = "location/state_list";
	 	DatatablesTools::$view_label = t("Define State");
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	
	
	public function actionstate_list()
	{
		$country_id = (integer) Yii::app()->input->post('country_id');			
						
		$update_link = Yii::app()->createUrl("/location/state_update",array(
		  'country_id'=>'-country_id-',		
		  'id'=>'-id-',				 
		));   
		
		$html='
		<div class="btn-group btn-group-actions" role="group" >				
				  
		  <a href="'.$update_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		  >
		  <i class="zmdi zmdi-border-color"></i>
		  </a>		
		  
		 <a href="javascript:;" data-id="-id-" 
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		  
		</div>
		';	
		
		$cols=array(
		  array(
		   'key'=>'state_id',
	 	   'value'=>'state_id',		 	  	 	   
		  ),		   				
		  array(
		   'key'=>'name',
	 	   'value'=>'name',	
	 	   'action'=>"format",
	 	    'format'=>'<h6>[name]</h6>',
		 	'format_value'=>array(
		 	   '[name]'=>'name',		 	    
		 	 )	 	  
		  ),	 	
		  array(
		   'key'=>'sequence',
	 	   'value'=>'sequence',		 	  	 	   
		  ),		   				
		 array( 
		   'key'=>'state_id',
	 	   'value'=>'state_id',
	 	   'action'=>"format",	 	   
	 	   'format'=>$html,
	 	   'format_value'=>array(
	 	     '-country_id-'=>'country_id',
	 	     '-id-'=>'state_id',
	 	   )
		  ),
		);
	 	
	    $stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{location_states}} a
		 WHERE country_id=".q($country_id)."	 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		  			 
	    	 		 		 			 			
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}		
	
	public function actioncity_list()
	{		
		$country_id = (integer) Yii::app()->input->post('country_id');
				
		$update_link = Yii::app()->createUrl("/location/city_update",array(
		  'country_id'=>$country_id,
		  'id'=>'-id-',				 
		));   
		
		$html='
		<div class="btn-group btn-group-actions" role="group" >				
				  
		  <a href="'.$update_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		  >
		  <i class="zmdi zmdi-border-color"></i>
		  </a>		
		  
		 <a href="javascript:;" data-id="-id-" 
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		  
		</div>
		';	
		
		$cols=array(
		  array(
		   'key'=>'city_id',
	 	   'value'=>'city_id',		 	  	 	   
		  ),		   				
		  array(
		   'key'=>'a.name',
	 	   'value'=>'name',	
	 	   'action'=>"format",
	 	    'format'=>'<h6>[name]</h6>
	 	       <p class="dim">[postal_code]</p>		 
	 	    ',
		 	'format_value'=>array(
		 	   '[name]'=>'name',		 	   	 	    
		 	   '[postal_code]'=>'postal_code',
		 	 )	 	  
		  ),	
		  array(
		   'key'=>'a.state_id',
	 	   'value'=>'state_name',		 	  	 	   
		  ), 	
		  array(
		   'key'=>'a.sequence',
	 	   'value'=>'sequence',		 	  	 	   
		  ),		   				
		 array( 
		   'key'=>'city_id',
	 	   'value'=>'city_id',
	 	   'action'=>"format",	 	   
	 	   'format'=>$html,
	 	   'format_value'=>array(
	 	     '-country_id-'=>'country_id',
	 	     '-id-'=>'city_id',
	 	   )
		  ),
		);
	 			
	    $stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*,
		 IFNULL(b.name,'') as state_name,
		 b.country_id
		 		 		 		
		 FROM {{location_cities}} a
		 LEFT JOIN {{location_states}} b
		 ON
		 a.state_id = b.state_id
		 
		 WHERE 
		 a.state_id IN (
		   select state_id from {{location_states}}
		   where country_id =".q($country_id)."
		 )
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		  			 	    	    
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actionarea_list()
	{
		$country_id = (integer) Yii::app()->input->post('country_id');
				
		$update_link = Yii::app()->createUrl("/location/area_update",array(
		  'country_id'=>$country_id,		
		  'id'=>'-id-',				 
		));   
		
		$html='
		<div class="btn-group btn-group-actions" role="group" >				
				  
		  <a href="'.$update_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		  >
		  <i class="zmdi zmdi-border-color"></i>
		  </a>		
		  
		 <a href="javascript:;" data-id="-id-" 
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		  
		</div>
		';	
		
		$cols=array(
		  array(
		   'key'=>'area_id',
	 	   'value'=>'area_id',		 	  	 	   
		  ),		   				
		  array(
		   'key'=>'a.name',
	 	   'value'=>'name',	
	 	   'action'=>"format",
	 	    'format'=>'<h6>[name]</h6>',
		 	'format_value'=>array(
		 	   '[name]'=>'name',		 	   	 	    		 	   
		 	 )	 	  
		  ),	
		  array(
		   'key'=>'a.city_id',
	 	   'value'=>'city_name',		 	  	 	   
		  ), 	
		  array(
		   'key'=>'a.sequence',
	 	   'value'=>'sequence',		 	  	 	   
		  ),		   				
		 array( 
		   'key'=>'area_id',
	 	   'value'=>'area_id',
	 	   'action'=>"format",	 	   
	 	   'format'=>$html,
	 	   'format_value'=>array(
	 	     '-country_id-'=>'country_id',
	 	     '-id-'=>'area_id',
	 	   )
		  ),
		);
	 			
	    $stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*, 
		 IFNULL(b.name,'') as city_name
		 		 
		 FROM {{location_area}} a
		 LEFT JOIN {{location_cities}} b
		 ON
		 a.city_id = b.city_id
		 
		 WHERE b.state_id IN (
		   select state_id from {{location_states}}
		   where country_id =".q($country_id)."
		 )
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		  			 	    
	    //dump($stmt);
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actionplans_list()
	{
		
		$cols=array(
		  array(
		   'key'=>'sms_package_id',
	 	   'value'=>'sms_package_id',		 	  
	 	   'action'=>"sms_package_id", 	  
		  ),		   						  
		  array(
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
		  ),
		  array(
		   'key'=>'sms_package_id',
	 	   'value'=>'sms_package_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'sms_package_id',		 	   
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "sms/plan_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();			
	}
	
	public function actionsms_provider_list()
	{
		
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  	 	   
		  ),		   		
		  array(
		   'key'=>'as_default',
	 	   'value'=>'as_default',
	 	   'id'=>'id',
	 	   'action'=>'checkbox',
	 	   'class'=>"set_default_smsprovider"
		  ),				  
		  array(
		    'key'=>'provider_name',
	 	    'value'=>'provider_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[provider_name]',
	 	    'format_value'=>array(
	 	     '[provider_name]'=>'provider_name',	 	     
	 	    )
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id',		 	   
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "sms/provider_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();			
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
		
		$view_link = Yii::app()->createUrl("/sms/sms_view",array(		  
		  'id'=>'-id-',				 
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
		
		$cols=array(
		  array(
		   'key'=>'a.id',
	 	   'value'=>'id',		 	  	 	   
		  ),		   		
		  array(
		    'key'=>'gateway',
	 	    'value'=>'provider_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[gateway]</h6>',
	 	    'format_value'=>array(
	 	     '[gateway]'=>'provider_name',	 	     
	 	    )
		  ),
		  array(
		   'key'=>'contact_phone',
	 	   'value'=>'contact_phone',	 	  
		  ),				  
		  array(
		    'key'=>'sms_message',
	 	    'value'=>'sms_message',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[sms_message]</h6>',
	 	    'format_value'=>array(
	 	     '[sms_message]'=>array(
	 	         'value'=>'sms_message',
		 	     'display'=>"short_text"
	 	      ),
	 	     '[status]'=>'status',
	 	    )
		  ),
		   array(
		    'key'=>'sms_message',
	 	    'value'=>'sms_message',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<p class="dim">[status]
	 	     <br/>[gateway_response]
	 	    </p>',
	 	    'format_value'=>array(
	 	     '[status]'=>array(
	 	         'value'=>'status',
		 	     'display'=>"short_text"
	 	      ),	 	     
	 	     '[gateway_response]'=>array(
	 	         'value'=>'gateway_response',
		 	     'display'=>"short_text"
	 	      ),
	 	    )
		  ),
		 array(
		   'key'=>'a.id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"format",
	 	   'format'=>$html,	 	 
	 	   'format_value'=>array(	 	   
	 	     '-id-'=>'id',
	 	   )  
		  ),		
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "sms/provider_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();			
	}		
	
	public function actionstatus_mgt_list()
	{
		
		$cols=array(
		  array(
		   'key'=>'status_id',
	 	   'value'=>'color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[color_hex]'=>'color_hex',
	 	    )
		  ),		   				 
		   array(
		    'key'=>'group_name',
	 	    'value'=>'group_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[group_name]</h6>',
	 	    'format_value'=>array(	 	     
	 	     '[group_name]'=>'group_name',
	 	    )
		  ),
		  array(
		    'key'=>'title',
	 	    'value'=>'title',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[title]</h6>
	 	    <p class="dim">[status]	 	    
	 	    <br/>'.t("Date Created. {{date_created}}").'
	 	    </p>
	 	    ',
	 	    'format_value'=>array(	 	     
	 	     '[title]'=>'title',
	 	     '[status]'=>'status',
	 	     '[date_created]'=>array(
	 	         'value'=>'date_created',
		 	     'display'=>"datetime"
	 	      )
	 	    )
		  ),
		  array(
		   'key'=>'status_id',
	 	   'value'=>'status_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'status_id',	 	   
		  ),
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
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/status_mgt_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();			
	}			
	
	public function actionservices_list()
	{
		$cols=array(
		   array(
		   'key'=>'service_id',
	 	   'value'=>'color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[color_hex]'=>'color_hex',
	 	    )
		  ),		  
		  array(
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
		  ),
		  array(
		   'key'=>'service_id',
	 	   'value'=>'services_fee',		
	 	   'action'=>"price",		 	   	 	   
		  ),
		  array(
		   'key'=>'service_id',
	 	   'value'=>'service_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'service_id'
		  ),
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
		 
		 IFNULL((
		 select service_fee from {{services_fee}}
		 where service_id = a.service_id
		 and merchant_id=0
		 ),0) as services_fee
		 			
		 FROM {{services}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	 	
	 	DatatablesTools::$action_edit_path = "attributes/services_update";
	 	DatatablesTools::$debug=false;
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
    public function actionmerchant_type_list()
	{
		$cols=array(
		   array(
		   'key'=>'id',
	 	   'value'=>'color_hex',		 	  	 	   
	 	    'action'=>"format",	 	  
	 	    'format'=>'<div class="color_hex_box img-60 rounded-circle" style="background:[color_hex];"></div>',
	 	    'format_value'=>array(	 	     
	 	     '[color_hex]'=>'color_hex',
	 	    )
		  ),		  
		  array(
		    'key'=>'type_name',
	 	    'value'=>'type_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[type_name] <span class="badge ml-2 post [status]">[status_title]</span></h6>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[type_name]'=>'type_name',	 	     
	 	     '[status]'=>'status',	 	     	 	     
	 	     '[status_title]'=>'status_title',	 	
	 	    )
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id'
		  ),
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
	 		 		 	 	
	 	DatatablesTools::$action_edit_path = "attributes/merchant_type_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
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
	
	public function actionfeatured_loc_list()
	{
		$cols=array(
		  array(
		   'key'=>'id',
	 	   'value'=>'id'	 	   
		  ),
		  array(
		    'key'=>'location_name',
	 	    'value'=>'location_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[location_name] <span class="badge ml-2 post [status]">[status]</span></h6>
	 	    <p class="dim">[featured_name]</p>
	 	    ',
	 	    'format_value'=>array(
	 	     '[location_name]'=>'location_name',
	 	     '[featured_name]'=>'featured_name',
	 	     '[status]'=>'status',
	 	    )
		  ),
		  array(
		   'key'=>'id',
	 	   'value'=>'id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 		 		
		 FROM {{featured_location}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	
	 	DatatablesTools::$action_edit_path = "attributes/featured_loc_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actionpayment_gateway_list()
	{		
		$cols=array(
		  array(
		   'key'=>'payment_id',
	 	   'value'=>'logo_image',	
	 	   'action'=>'photo_or_class',
	 	   'logo_type'=>'logo_type',
	 	   'logo_class'=>'logo_class'
		  ),
		   array(
		   'key'=>'status',
	 	   'value'=>'status',
	 	   'id'=>'payment_id',
	 	   'action'=>'checkbox_active',
	 	   'class'=>"set_payment_provider"
		  ),
		  array(
		    'key'=>'payment_name',
	 	    'value'=>'payment_name',	
	 	    'action'=>"format",	 	  
	 	    'format'=>'<h6>[payment_name] <span class="badge ml-2 gateway [status]">[status]</span></h6>	 	    
	 	    ',
	 	    'format_value'=>array(
	 	     '[payment_name]'=>'payment_name',	 	     
	 	     '[status]'=>'status',	 	     	 	     
	 	     '[status]'=>'status',	 	
	 	    )
		  ),
		  array(
		   'key'=>'payment_id',
	 	   'value'=>'payment_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'payment_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*	
		 		
		 FROM {{payment_gateway}} a
		 WHERE 1		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	 	
	 	DatatablesTools::$action_edit_path = "payment_gateway/update";
	 			 			
	 	$this->data['path'] = "/upload/0"; 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}
	
	public function actionset_payment_provider()
	{		
		$id = isset($this->data['id'])?(integer)$this->data['id']:0;		
		$status = isset($this->data['status'])?$this->data['status']:'';		
		$model = AR_payment_gateway::model()->findByPk( $id );		
		if($model){					
			$model->status=$status;
			if($model->save()){
				$this->code = 1;
				$this->msg = t(Helper_success);
				$this->details = array(
				);
			} else $this->msg = t(Helper_failed_update);
		} else $this->msg =  t(Helper_not_found);
		$this->jsonResponse();
	}
	
    public function actionrejection_list()
	{
		$cols=array(
		   array(
		   'key'=>'meta_id',
	 	   'value'=>'meta_id',	 	    
		  ),		  
		  array(
		    'key'=>'meta_value',
	 	    'value'=>'meta_value',		 	    
		  ),
		  array(
		   'key'=>'meta_id',
	 	   'value'=>'meta_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'meta_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 
		 FROM {{admin_meta}} a
		 WHERE meta_name='rejection_list'		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	 		 	
	 	DatatablesTools::$action_edit_path = "attributes/rejection_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	

	public function actionstatus_actionlist()
	{

		$id = intval(Yii::app()->input->post("id"));
		$update_link = Yii::app()->createUrl("attributes/update_action/id/$id",array(		  
		  'action_id'=>'-action_id-',
		));  
		
		$html='
		<div class="btn-group btn-group-actions" role="group" >				
				  
		  <a href="'.$update_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		  >
		  <i class="zmdi zmdi-border-color"></i>
		  </a>		
		  
		 <a href="javascript:;" data-id="-action_id-" 
		  class="btn btn-light datatables_delete tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
		  <i class="zmdi zmdi-delete"></i>
		  </a>
		  
		</div>
		';	
		
		$cols=array(
		  array(
		   'key'=>'action_id',
	 	   'value'=>'action_id'	 	   
		  ),
		  array(
		    'key'=>'action_type',
		    'value'=>'action_type_name',
		  ),
		  array(
		    'key'=>'action_value',
		    'value'=>'template_name',
		  ),
		   array(
		   'key'=>'action_id',
	 	   'value'=>'action_id',		 	  
	 	   'action'=>"format",	 	   
	 	   'format'=>$html,
	 	   'format_value'=>array(
	 	     '-action_id-'=>'action_id', 	     
	 	     '-stats_id-'=>'stats_id',
	 	   )
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*, 
		 b.meta_value as action_type_name,
		 c.template_name
		 	 
		 FROM {{order_status_actions}} a
		 LEFT JOIN {{admin_meta}} b
		 ON
		 a.action_type=b.meta_value1
		 
		 LEFT JOIN {{templates}} c
		 ON
		 a.action_value = c.template_id
		 
		 WHERE a.stats_id = ".q($id)."
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 	 		 		 
	 	DatatablesTools::$action_edit_path = "status_actions/status_actions_update";	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();		
	}
	
	public function actionpause_reason_list()
	{
		$cols=array(
		   array(
		   'key'=>'meta_id',
	 	   'value'=>'meta_id',	 	    
		  ),		  
		  array(
		    'key'=>'meta_value',
	 	    'value'=>'meta_value',		 	    
		  ),
		  array(
		   'key'=>'meta_id',
	 	   'value'=>'meta_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'meta_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 
		 FROM {{admin_meta}} a
		 WHERE meta_name='pause_reason'		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	 		 	
	 	DatatablesTools::$action_edit_path = "attributes/pause_reason_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	
	
	public function actionaction_list()
	{
		$cols=array(
		   array(
		   'key'=>'meta_id',
	 	   'value'=>'meta_id',	 	    
		  ),		  
		  array(
		    'key'=>'meta_value',
	 	    'value'=>'meta_value',		 	    
		  ),
		  array(
		   'key'=>'meta_id',
	 	   'value'=>'meta_id',		 	  
	 	   'action'=>"editdelete",
	 	   'id'=>'meta_id'
		  ),
		);
	 	
	 	$stmt = "
	 	 SELECT SQL_CALC_FOUND_ROWS
		 a.*		 
		 FROM {{admin_meta}} a
		 WHERE meta_name='action_type'		 
		 [and]
		 [search]
		 [order]
		 [limit]
	 	";		 			 
	 		 		 	 		 	
	 	DatatablesTools::$action_edit_path = "attributes/action_update";
	 			 			 
	 	if( $feed_data = DatatablesTools::getTables($cols,$stmt,$this->data)){		 		
	 		$this->DataTablesData($feed_data);
	 		Yii::app()->end();
	 	}
	 	$this->DataTablesNodata();	
	}	
}
/*end class*/