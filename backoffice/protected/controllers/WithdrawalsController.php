<?php
class WithdrawalsController extends CommonController
{
		
	public function beforeAction($action)
	{				
		InlineCSTools::registerStatusCSS();
		/*InlineCSTools::registerOrder_StatusCSS();
		InlineCSTools::registerServicesCSS();*/
		return true;
	}
	
	public function actionmerchant()
	{
		$this->pageTitle=t("Withdrawals");
		
		$transaction_type = AttributesTools::paymentStatus();
		
		$table_col = array(
		  /*'merchant_id'=>array(
		    'label'=>t("Merchant ID"),
		    'width'=>'12%'
		  ),*/
		  'logo'=>array(
		    'label'=>t(""),
		    'width'=>'10%'
		  ),		  
		  'transaction_date'=>array(
		    'label'=>t("Date"),
		    'width'=>'15%'
		  ),		  
		  'restaurant_name'=>array(
		    'label'=>t("Merchant"),
		    'width'=>'30%'
		  ),
		  'transaction_amount'=>array(
		    'label'=>t("Amount"),
		    'width'=>'20%'
		  ),
		  'transaction_uuid'=>array(
		    'label'=>t("Actions"),
		    'width'=>'15%'
		  ),
		);
		$columns = array(
		  //array('data'=>'merchant_id','visible'=>false),
		  array('data'=>'logo','orderable'=>false),
		  array('data'=>'transaction_date'),
		  array('data'=>'restaurant_name'),
		  array('data'=>'transaction_amount'),		  
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_payout normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>			    
			 </div>
		     '
		  ),
		);		
		
		$this->render('//finance/merchant_withdrawals',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'transaction_type'=>$transaction_type
		));
	}
	
	public function actionsettings()
	{
		$this->pageTitle = t("Settings");
		
		CommonUtility::setMenuActive('.admin_withdrawals',".withdrawals_settings");
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
			$model->saveMeta('payout_request_auto_process', isset($post['payout_request_auto_process'])?$post['payout_request_auto_process']:'' );
			$model->saveMeta('payout_request_enabled', isset($post['payout_request_enabled'])?$post['payout_request_enabled']:'' );
			$model->saveMeta('payout_minimum_amount', isset($post['payout_minimum_amount'])?floatval($post['payout_minimum_amount']):0 );
			$model->saveMeta('payout_process_days', isset($post['payout_process_days'])?floatval($post['payout_process_days']):0 );
			$model->saveMeta('payout_number_can_request', isset($post['payout_number_can_request'])?intval($post['payout_number_can_request']):0 );
			
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_success));
			$this->refresh();					
		} else {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('meta_name',(array) array('payout_request_auto_process','payout_request_enabled',
			'payout_minimum_amount','payout_process_days','payout_number_can_request') );
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
			'template_name'=>"//finance/settings_withdrawals",
			'widget'=>'WidgetPayoutSettings',					
			'params'=>array(  			     
			   'template_list'=>$template_list,
			   'model'=>$model,			   
			   'links'=>array(		 
			      t("Withdrawals")=>array('withdrawals/settings'),  
                  $this->pageTitle,                           
			   ),
			 )
		));				
	}
	
	public function actionsettings_template()
	{	
		$this->pageTitle = t("Template");
		
		CommonUtility::setMenuActive('.admin_withdrawals',".withdrawals_settings");
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
			$model->saveMeta('payout_new_payout_template_id', isset($post['payout_new_payout_template_id'])?$post['payout_new_payout_template_id']:'' );			
			$model->saveMeta('payout_paid_template_id', isset($post['payout_paid_template_id'])?$post['payout_paid_template_id']:'' );			
			$model->saveMeta('payout_cancel_template_id', isset($post['payout_cancel_template_id'])?$post['payout_cancel_template_id']:'' );						
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_success));
			$this->refresh();			
		} else {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('meta_name',(array) array('payout_new_payout_template_id','payout_paid_template_id','payout_cancel_template_id') );
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
			'template_name'=>"//finance/settings_withdrawals_tpl",
			'widget'=>'WidgetPayoutSettings',					
			'params'=>array(  			     
			   'template_list'=>$template_list,
			   'model'=>$model,			   
			   'links'=>array(		 
			      t("Withdrawals")=>array('withdrawals/settings'),        			      
                  $this->pageTitle,                           
			   ),
			 )
		));				
	}
	
} 
/*end class*/