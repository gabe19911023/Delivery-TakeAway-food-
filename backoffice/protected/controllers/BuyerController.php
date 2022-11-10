<?php
class BuyerController extends CommonController
{
	
	public function beforeAction($action)
	{										
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		return true;
	}
	
	public function actioncustomers()
	{		
		$this->pageTitle=t("Customer list");
		$action_name='customer_list';
		$delete_link = Yii::app()->CreateUrl("buyer/customer_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'customer_list_app';
		} else $tpl = 'customer_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/customer_create")
		));
	}	
	
    public function actioncustomer_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Customer") : t("Update Customer");
		CommonUtility::setMenuActive('.buyer',".buyer_customers");
		
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_client::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
			$model->password='';
		} else {			
			$model=new AR_client;				
		}

		if(isset($_POST['AR_client'])){
			$model->attributes=$_POST['AR_client'];
			if($model->validate()){
				if(!empty($model->password)){
				    $model->password = md5($model->password);
				}
				if($model->save()){
					if(!$update){
					   $this->redirect(array('buyer/customers'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}

		$upload_path = CMedia::adminFolder();		
		
		$this->render("customer_create",array(
		    'model'=>$model,	
		    'hide_nav'=>false,
		    'links'=>array(
	            t("All Customer")=>array('buyer/customers'),        
                $this->pageTitle,
		    ),	    		    
		    'status_list'=>(array)AttributesTools::StatusManagement('customer'),
		    'upload_path'=>$upload_path,
		));		
	}
	
	public function actioncustomer_update()
	{		
		$this->pageTitle = t("Update Customer");
		CommonUtility::setMenuActive('.buyer',".buyer_customers");
		
		$id='';		
		
		$id = (integer) Yii::app()->input->get('id');	
		$model = AR_client::model()->findByPk( $id );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		if(isset($_POST['AR_client'])){
			$model->attributes=$_POST['AR_client'];
			if($model->validate()){
												
				if(isset($_POST['avatar'])){
					if(!empty($_POST['avatar'])){
						$model->avatar = isset($_POST['avatar'])?$_POST['avatar']:'';
						$model->path = isset($_POST['path'])?$_POST['path']:'';						
					} else $model->avatar = '';
				} else $model->avatar = '';
								
				if(!empty($model->npassword)){					
				    $model->password = md5($model->npassword);				    
				}
								
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
				
		$avatar = CMedia::getImage($model->avatar,$model->path,Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('customer'));
				
		$upload_path = CMedia::adminFolder();		
									
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,		    
			'template_name'=>"customer_create",
			'widget'=>'WidgetCustomer',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,
			   'upload_path'=>$upload_path,
			   'status_list'=>(array)AttributesTools::StatusManagement('customer'),	
			   'hide_nav'=>true,
			   'links'=>array(
		            t("All Customer")=>array('buyer/customers'),  
	                $this->pageTitle,
			     ),
			 )
		));
	}	
	
    public function actioncustomer_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_client::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('buyer/customers'));			
		} else $this->render("error");
	}	
	
	public function actionsubscribers_list()
	{		
		$this->pageTitle=t("Subscriber List");
		$action_name='subscribers_list';
		$delete_link = Yii::app()->CreateUrl("buyer/subscriber_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'subscribers_list_app';
		} else $tpl = 'subscribers_list';
		
		$this->render( $tpl ,array(
			'link'=>''
		));
	}	
	
	public function actionsubscriber_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_newsletter::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('buyer/subscribers_list'));			
		} else $this->render("error");
	}	
	
	public function actionaddress()
	{
		$this->pageTitle=t("Address list");
		CommonUtility::setMenuActive('.buyer',".buyer_customers");
				
		$action_name='address_list';
		$delete_link = Yii::app()->CreateUrl("buyer/address_delete");			
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		
        $client_id = (integer) Yii::app()->input->get('id');	
		$model = AR_client::model()->findByPk( $client_id );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		$avatar = CMedia::getImage($model->avatar,$model->path,Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('customer'));
				
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'address_list',
			'widget'=>'WidgetCustomer',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,			   
			   'country_list'=>AttributesTools::CountryList(),
			   'hide_nav'=>false,
			   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/address_create",array(
			    'create'=>1,
	            'id'=>$model->client_id
			   )),
			   'links'=>array(
		            t("All Customer")=>array('buyer/customers'),  
	                $this->pageTitle,
			    ),
			   'links2'=>array(
		            t("All Address")=>array('buyer/address','id'=>$model->client_id),
	                $this->pageTitle,
			    ), 
			 )
		));
	}
	
	public function actionaddress_create($update=false)
	{		
		$this->pageTitle = $update==false? t("Add Address") :  t("Update Address");
		CommonUtility::setMenuActive('.buyer',".buyer_customers");		
		CommonUtility::setSubMenuActive('.customer-menu','.customer-address');		
						
        $client_id = (integer) Yii::app()->input->get('id');	
		$model = AR_client::model()->findByPk( $client_id );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
				
       $avatar = CMedia::getImage($model->avatar,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('customer'));
				
		$page_title = t("Add Address");
		$model = new AR_client_address;
		
		if($update){
			$ad_id = (integer) Yii::app()->input->get('ad_id');	
			$model = AR_client_address::model()->findByPk( $ad_id );	
			if(!$model){				
				 $this->render('//tpl/error',array(
				 'error'=>array(
				   'message'=>t(Helper_not_found)
				 )
				));
				return ;
			}	
		}

		if(isset($_POST['AR_client_address'])){
			$model->attributes=$_POST['AR_client_address'];
			if($model->validate()){								
				$model->client_id = $client_id;				
				if($model->save()){					
					if(!$update){
					   $this->redirect(array('buyer/address','id'=>$client_id));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}					
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString($model->getErrors(),'<br/>') );	
			//} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		} 
								
		$this->render("submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'address_create',
			'widget'=>'WidgetCustomer',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,			   
			   /*'country_list'=>AttributesTools::CountryList(),
			   'location_nickname'=>AttributesTools::locationNickName(),*/
			   'delivery_option'=>CCheckout::deliveryOption(),
			   'address_label'=>CCheckout::addressLabel(),
			   'hide_nav'=>false,
			   'links'=>array(
		            t("All Customer")=>array('buyer/customers'),  
		            t("All Address")=>array('buyer/address','id'=>$client_id),
	                $this->pageTitle,
			    ),
			   'links2'=>array(		            
	                $this->pageTitle,
			    ), 
			 )
		));
	}	
	
	public function actionaddress_update()
	{		
		$this->actionaddress_create(true);
	}
	
	public function actionaddress_delete()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$model = AR_client_address::model()->findByPk( $id );
		if($model){			
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('buyer/address','id'=>$model->client_id));			
		} else $this->render("error");
	}
	
	public function actionorder_history()
	{
		$this->pageTitle=t("Order list");
		CommonUtility::setMenuActive('.buyer',".buyer_customers");
				
		$action_name='customer_order_list';
		$delete_link = Yii::app()->CreateUrl("buyer/order_delete");			
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		
        $client_id = (integer) Yii::app()->input->get('id');	
		$model = AR_client::model()->findByPk( $client_id );				
		if(!$model){				
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t(Helper_not_found)
			 )
			));
			return ;
		}	
				
		$avatar = CMedia::getImage($model->avatar,$model->path,Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('customer'));
				
				
		$table_col = array(
		  'merchant_id'=>array(
		    'label'=>"",
		    'width'=>'10%'
		  ),		  
		  'client_id'=>array(
		    'label'=>t("Order Information"),
		    'width'=>'25%'
		  ),
		  'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'12%'
		  ),
		  'restaurant_name'=>array(
		    'label'=>t("Merchant"),
		    'width'=>'15%'
		  ),
		  'order_uuid'=>array(
		    'label'=>t("Actions"),
		    'width'=>'10%'
		  ),
		  'view'=>array(
		    'label'=>t("view"),
		    'width'=>'10%'
		  ),
		);
		$columns = array(
		  array('data'=>'merchant_id','orderable'=>false),
		  array('data'=>'client_id','orderable'=>false),		  
		  array('data'=>'order_id'),
		  array('data'=>'restaurant_name','orderable'=>false),
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_view_order normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
			    <a class="ref_pdf_order normal btn btn-light tool_tips"><i class="zmdi zmdi-download"></i></a>
			 </div>
		     '
		  ),
		  array('data'=>null,'orderable'=>false,'visible'=>false),
		);						
		
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'customer_order_list',
			'widget'=>'WidgetCustomer',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,				   
			   'hide_nav'=>false,
			   'links'=>array(
		            t("All Customer")=>array('buyer/customers'),  
	                $this->pageTitle,
			    ),
			   'links2'=>array(
		            t("All Customer")=>array('buyer/address','id'=>$model->client_id),
	                $this->pageTitle,
			    ), 
			   'table_col'=>$table_col,
			   'columns'=>$columns,
			   'order_col'=>2,
	           'sortby'=>'desc',
	           'transaction_type'=>array(),
	           'client_id'=>$client_id
			 )
		));
	}

	public function actionbooking_history()
	{
		$this->pageTitle=t("Booking list");
		CommonUtility::setMenuActive('.buyer',".buyer_customers");
				
		$action_name='customer_booking';
		$delete_link = Yii::app()->CreateUrl("buyer/booking_delete");			
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		
        $client_id = (integer) Yii::app()->input->get('id');	
		$model = AR_client::model()->findByPk( $client_id );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		$avatar = CMedia::getImage($model->avatar,$model->path,Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('customer'));
				
		$this->render("submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'customer_booking',
			'widget'=>'WidgetCustomer',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,			   
			   'country_list'=>AttributesTools::CountryList(),
			   'hide_nav'=>false,
			   'links'=>array(
		            t("All Customer")=>array('buyer/customers'),  
	                $this->pageTitle,
			    ),
			   'links2'=>array(
		            t("All Address")=>array('buyer/address','id'=>$model->client_id),
	                $this->pageTitle,
			    ), 
			 )
		));
	}
		
	public function actionbooking_delete()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$model = AR_booking::model()->findByPk( $id );
		if($model){			
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('buyer/booking_history','id'=>$model->client_id));			
		} else $this->render("error");
	}
	
	public function actionbooking_update($update=true)
	{
		$this->pageTitle = $update==false? t("Add Booking") : t("Update Booking");
		CommonUtility::setMenuActive('.buyer',".buyer_customers");
		CommonUtility::setSubMenuActive('.customer-menu','.customer-booking-history');		
		
		$client_id = (integer) Yii::app()->input->get('id');	
		$model = AR_client::model()->findByPk( $client_id );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		
        $avatar = CMedia::getImage($model->avatar,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
		
		if($update){
			$bk_id = (integer) Yii::app()->input->get('bk_id');	
			$model = AR_booking::model()->findByPk( $bk_id );	
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
		}
		
		if(isset($_POST['AR_booking'])){
			$model->attributes=$_POST['AR_booking'];
			if($model->validate()){					
				if($model->save()){					
					if(!$update){
					   $this->redirect(array('buyer/booking_history','id'=>$client_id));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}					
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		} 
		
		$this->render("submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'booking_create',
			'widget'=>'WidgetCustomer',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,
			   'status_list'=>(array)AttributesTools::StatusManagement('booking'),
			   'hide_nav'=>false,
			   'links'=>array(
		            t("All Customer")=>array('buyer/customers'),  
	                $this->pageTitle,
			    ),
			   'links2'=>array(
		            t("All Booking")=>array('buyer/booking_history','id'=>$client_id),
	                $this->pageTitle,
			    ), 
			 )
		));
	}
	
	public function actionreview_list()
	{		
		$this->pageTitle=t("Reviews");
		$action_name='review_list';
		$delete_link = Yii::app()->CreateUrl("buyer/review_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'review_list_app';
		} else $tpl = 'review_list';
		
		$this->render( $tpl ,array(
			'link'=>''
		));
	}	
	
	public function actionreview_delete()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$model = AR_review::model()->findByPk( $id );
		if($model){			
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('buyer/review_list'));			
		} else $this->render("error");
	}
	
	public function actionreview_update()
	{
	
		$this->pageTitle =  t("Update Review");
		CommonUtility::setMenuActive('.buyer',".buyer_review_list");
		
		$id='';	$role_access = array();
		
		$id = (integer) Yii::app()->input->get('id');				
		$model = AR_review::model()->findByPk( $id );				
		if(!$model){				
			$this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));				
			Yii::app()->end();
		}				
	
		if(isset($_POST['AR_review'])){
		    $model->attributes=$_POST['AR_review'];			    	
		    if($model->validate()){						    				    	
				if($model->save()){						
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else {					
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}				
			}
		}
				
		$this->render("review_create",array(
		  'model'=>$model,	
		  'status_list'=>(array)AttributesTools::StatusManagement('post'),
		  'links'=>array(
	            t("All Review")=>array('buyer/review_list'),  
                $this->pageTitle,
		    ),	
	    ));
	}

}
/*end class*/