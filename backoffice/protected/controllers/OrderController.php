<?php
class OrderController extends CommonController
{
	public $layout='backend';
	
	public function beforeAction($action)
	{										
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		InlineCSTools::registerServicesCSS();
					
		return true;
	}
	
	/*public function actionlist()
	{
		$this->pageTitle=t("All Orders");
		$action_name='order_list';
		$delete_link = Yii::app()->CreateUrl("order/delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'list_app';
		} else $tpl = 'list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create")
		));	
	}*/
	
	public function actionlist()
	{
		$this->pageTitle = t("All Orders");
		$transaction_type = AttributesTools::paymentStatus();
		
		
		$table_col = array(
		  'merchant_id'=>array(
		    'label'=>t(""),
		    'width'=>'8%'
		  ),		  
		  'status'=>array(
		    'label'=>t("Order Information"),
		    'width'=>'20%'
		  ),
		  'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'10%'
		  ),
		  'restaurant_name'=>array(
		    'label'=>t("Merchant"),
		    'width'=>'15%'
		  ),
		  'client_id'=>array(
		    'label'=>t("Customer"),
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
		  array('data'=>'status','orderable'=>false),		  
		  array('data'=>'order_id'),
		  array('data'=>'restaurant_name','orderable'=>false),
		  array('data'=>'client_id','orderable'=>false),		  
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

		$this->render("list",array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,		 
		  'order_col'=>2,
		  'sortby'=>'desc',		  
		));
	}
	
	
	public function actionview()
	{				
		
		$this->pageTitle = t("Order Details");		
		CommonUtility::setMenuActive('.admin_orders','.order_list');		
		
		$order_uuid = Yii::app()->input->get('order_uuid'); 
		
		$ajax_url = Yii::app()->createUrl("/api");
		
		ScriptUtility::registerScript(array(
		  "var _order_uuid='$order_uuid';",		  
		  "var _ajax_url='$ajax_url';",	
		),'order_uuid');

		$table_col_trans = array(
		  'transaction_date'=>array(
		    'label'=>t("Date"),
		    'width'=>'15%'
		  ),
		  'transaction_description'=>array(
		    'label'=>t("Transaction"),
		    'width'=>'30%'
		  ),
		  'transaction_amount'=>array(
		    'label'=>t("Debit/Credit"),
		    'width'=>'20%'
		  ),
		  'running_balance'=>array(
		    'label'=>t("Running Balance"),
		    'width'=>'20%'
		  ),
		);
		$columns_trans = array(
		  array('data'=>'transaction_date'),
		  array('data'=>'transaction_description'),
		  array('data'=>'transaction_amount'),
		  array('data'=>'running_balance'),
		);				
		
		$transaction_type = AttributesTools::transactionTypeList(false);
		
		try {
		    $order = COrders::get($order_uuid);		    		    
		    $this->render('order-view-admin',array(		  
			  'order_uuid'=>$order_uuid,		
			  'merchant_id'=>$order->merchant_id,
			  'ajax_url'=>Yii::app()->createUrl("/api"),
			  'view_admin'=>true,
			  'table_col_trans'=>$table_col_trans,
			  'columns_trans'=>$columns_trans,
			  'transaction_type'=>$transaction_type,
			  'sortby'=>'desc',
			));			
		} catch (Exception $e) {
			$this->render('//tpl/error',array(
			  'error'=>array('message'=>$e->getMessage())
			));
		}			
	}
	
	public function actiondelete()
	{		
		$id = Yii::app()->input->get('id');		
		$model = AR_orders::model()->find('order_id_token=:order_id_token', array(':order_id_token'=>$id));
		if($model){				
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('order/list'));			
		} else $this->render("error");
	}
	
	public function actioncancel_list()
	{
		$this->pageTitle=t("Cancel Orders");
		$action_name='order_list_cancel';
		$delete_link = Yii::app()->CreateUrl("order/delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'list_app';
		} else $tpl = 'list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create")
		));	
	}
	
	public function actionsettings()
	{
		$this->pageTitle = t("Settings");
		
		$status = AttributesTools::getOrderStatusList(Yii::app()->language);
		$status_list = AttributesTools::getOrderStatus(Yii::app()->language);		
		if($status){
			$status = AttributesTools::formatAsSelect2($status);
		}
		
		$model = new AR_admin_meta;			
		
		if(isset($_POST['AR_admin_meta'])){			
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
					
			$post = $_POST['AR_admin_meta'];			
			$status_new_order = isset($post['status_new_order'])?$post['status_new_order']:'';			
			$status_cancel_order = isset($post['status_cancel_order'])?$post['status_cancel_order']:'';			
			$status_delivered = isset($post['status_delivered'])?$post['status_delivered']:'';						
						
			$model->saveMeta('status_new_order',$status_new_order);
			$model->saveMeta('status_cancel_order',$status_cancel_order);
			$model->saveMeta('status_delivered',$status_delivered);			
			$model->saveMeta('status_completed', isset($post['status_completed'])?$post['status_completed']:'' );
			$model->saveMeta('status_rejection', isset($post['status_rejection'])?$post['status_rejection']:'' );
			$model->saveMeta('status_delivery_fail', isset($post['status_delivery_fail'])?$post['status_delivery_fail']:'' );
			$model->saveMeta('status_failed', isset($post['status_failed'])?$post['status_failed']:'' );
			
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_success));
			$this->refresh();			
		} else {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('meta_name',(array) array('status_new_order','status_cancel_order',
			  'status_delivered','status_rejection','status_completed','status_delivery_fail','status_failed'
			) );
			$find = AR_admin_meta::model()->findAll($criteria);		
			if($find){
				foreach ($find as $items) {					
					$model[$items->meta_name] = $items->meta_value;
				}
			}			
		}

		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"//order/settings",
			'widget'=>'WidgetOrderSettings',					
			'params'=>array(  
			   'model'=>$model,					   
			   'status_list'=>$status_list,
			   'links'=>array(		 
			      t("Orders")=>array('order/list'),        
                  $this->pageTitle,                           
			   ),
			 )
		));						
	}
	
	public function actionsettings_tabs()
	{
		$this->pageTitle = t("Settings");
		
		CommonUtility::setMenuActive('.admin_orders',".order_settings");
		
		$status = AttributesTools::getOrderStatusList(Yii::app()->language);
		if($status){
			$status = AttributesTools::formatAsSelect2($status);
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//order/settings_tabs",
			'widget'=>'WidgetOrderSettings',					
			'params'=>array(  			     
			   'status'=>$status,
			   'links'=>array(		 
			      t("Orders")=>array('order/list'),        
                  $this->pageTitle,                           
			   ),
			 )
		));			
	}
	
	public function actionsettings_buttons()
	{
		$this->pageTitle = t("Settings");
		
		CommonUtility::setMenuActive('.admin_orders',".order_settings");
		
		$status = AttributesTools::getOrderStatusList(Yii::app()->language);
		if($status){
			$status = AttributesTools::formatAsSelect2($status);
		}
		
		$order_type = AttributesTools::ListSelectServices();
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//order/settings_buttons",
			'widget'=>'WidgetOrderSettings',					
			'params'=>array(  			     
			   'status'=>$status,
			   'order_type'=>$order_type,
			   'do_actions'=>AttributesTools::orderButtonsActions(),
			   'links'=>array(		 
			      t("Orders")=>array('order/list'),        
                  $this->pageTitle,                           
			   ),
			 )
		));			
	}
	
	public function actionsettings_tracking()
	{
		$this->pageTitle = t("Settings");
		
		CommonUtility::setMenuActive('.admin_orders',".order_settings");		
		$status_list = AttributesTools::getOrderStatus(Yii::app()->language);		
		$model = new AR_admin_meta;			
		
		if(isset($_POST['AR_admin_meta'])){
												
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
						
			$post = $_POST['AR_admin_meta'];						
			$model->saveMeta('tracking_status_receive', isset($post['tracking_status_receive'])?$post['tracking_status_receive']:'' );
			$model->saveMeta('tracking_status_process', isset($post['tracking_status_process'])?$post['tracking_status_process']:'' );
			$model->saveMeta('tracking_status_ready', isset($post['tracking_status_ready'])?$post['tracking_status_ready']:'' );
			
			$model->saveMeta('tracking_status_in_transit', isset($post['tracking_status_in_transit'])?$post['tracking_status_in_transit']:'' );
			$model->saveMeta('tracking_status_delivered', isset($post['tracking_status_delivered'])?$post['tracking_status_delivered']:'' );
			$model->saveMeta('tracking_status_delivery_failed', isset($post['tracking_status_delivery_failed'])?$post['tracking_status_delivery_failed']:'' );
			$model->saveMeta('tracking_status_completed', isset($post['tracking_status_completed'])?$post['tracking_status_completed']:'' );
			$model->saveMeta('tracking_status_failed', isset($post['tracking_status_failed'])?$post['tracking_status_failed']:'' );
			
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_success));
			$this->refresh();			
		} else {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('meta_name',(array) array('tracking_status_receive',
			 'tracking_status_process','tracking_status_ready','tracking_status_in_transit','tracking_status_delivered','tracking_status_delivery_failed',
			 'tracking_status_completed','tracking_status_failed'
			) );
			$find = AR_admin_meta::model()->findAll($criteria);		
			if($find){
				foreach ($find as $items) {					
					$model[$items->meta_name] = $items->meta_value;
				}
			}			
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//order/settings_tracking",
			'widget'=>'WidgetOrderSettings',					
			'params'=>array(  			     
			   'status_list'=>$status_list,
			   'model'=>$model,
			   'do_actions'=>AttributesTools::orderButtonsActions(),
			   'links'=>array(		 
			      t("Orders")=>array('order/list'),        
                  $this->pageTitle,                           
			   ),
			 )
		));			
	}
	
	public function actionsettings_template()
	{
		$this->pageTitle = t("Settings");
		
		CommonUtility::setMenuActive('.admin_orders',".order_settings");		
		$status_list = AttributesTools::getOrderStatus(Yii::app()->language);		
		$model = new AR_admin_meta;			
		
		if(isset($_POST['AR_admin_meta'])){
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$post = $_POST['AR_admin_meta'];						
			$model->saveMeta('invoice_create_template_id', isset($post['invoice_create_template_id'])?$post['invoice_create_template_id']:'' );			
			$model->saveMeta('refund_template_id', isset($post['refund_template_id'])?$post['refund_template_id']:'' );			
			$model->saveMeta('partial_refund_template_id', isset($post['partial_refund_template_id'])?$post['partial_refund_template_id']:'' );			
			$model->saveMeta('delayed_template_id', isset($post['delayed_template_id'])?$post['delayed_template_id']:'' );			
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_success));
			$this->refresh();			
		} else {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('meta_name',(array) array('invoice_create_template_id','refund_template_id','partial_refund_template_id','delayed_template_id') );
			$find = AR_admin_meta::model()->findAll($criteria);		
			if($find){
				foreach ($find as $items) {					
					$model[$items->meta_name] = $items->meta_value;
				}
			}			
		}
		
		$template_list =  CommonUtility::getDataToDropDown("{{templates}}",'template_id','template_name',
		"","ORDER BY template_name ASC");
				
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//order/settings_template",
			'widget'=>'WidgetOrderSettings',					
			'params'=>array(  			     
			   'template_list'=>$template_list,
			   'model'=>$model,			   
			   'links'=>array(		 
			      t("Orders")=>array('order/list'),        
                  $this->pageTitle,                           
			   ),
			 )
		));				
	}
	
}
/*end class*/