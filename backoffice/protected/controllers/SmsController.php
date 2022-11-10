<?php
class SmsController extends CommonController
{
	public $layout='backend';
		
	public function beforeAction($action)
	{										
		InlineCSTools::registerStatusCSS();
		return true;
	}
		
	public function actionplans_list()
	{		
		$this->pageTitle=t("Plan List");
		$action_name='plans_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/plan_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'plans_list_app';
		} else $tpl = 'plans_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/plan_create")
		));
	}		
	
	public function actionplan_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Plan") : t("Update Plan");
		CommonUtility::setMenuActive('.sms',".sms_plans_list");
		
		$id='';		
		$multi_language = CommonUtility::MultiLanguage();
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_sms::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}				
		} else {			
			$model=new AR_sms;				
		}
		
		$model->multi_language = $multi_language;

		if(isset($_POST['AR_sms'])){
			$model->attributes=$_POST['AR_sms'];
			if($model->validate()){
				
				$model->price = (float) $model->price;	
				$model->promo_price = (float) $model->promo_price;	
				$model->sms_limit = (integer) $model->sms_limit;	
				$model->sequence = (integer) $model->sequence;	
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/plans_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_save));
			}
		}
		
		$model->price = Price_Formatter::convertToRaw($model->price);
		$model->promo_price = Price_Formatter::convertToRaw($model->promo_price);

		$data  = array();
		if($update && !isset($_POST['AR_sms'])){
			$translation = AttributesTools::smsPackageTranslation($id);			
			$data['title_translation'] = isset($translation['title'])?$translation['title']:'';
			$data['description_translation'] = isset($translation['description'])?$translation['description']:'';
		}		
		
		$fields[]=array(
		  'name'=>'title_translation',
		  'placeholder'=>"Enter [lang] title here"
		);
		$fields[]=array(
		  'name'=>'description_translation',
		  'placeholder'=>"Enter [lang] content here",
		  'type'=>"textarea",
		  'class'=>"summernote"
		);
			
		$this->render("plan_create",array(
		    'model'=>$model,	
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'links'=>array(
	            t("All Plan")=>array(Yii::app()->controller->id.'/plans_list'),        
                $this->pageTitle,
		    ),			    
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data
		));		
	}
	
	public function actionplan_update()
	{
		$this->actionplan_create(true);
	}
		
	public function actionplan_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_sms::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/plans_list'));			
		} else $this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));				
	}
	
	public function actionsettings($update=false)
	{
		$this->pageTitle=t("SMS Provider List");
		$action_name='sms_provider_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/provider_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'sms_provider_list_app';
		} else $tpl = 'sms_provider_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/provider_create")
		));

	}

	public function actionprovider_restore()
	{
	    $CreateTables = new MG_sms_provider;
		$CreateTables->up();
		$this->redirect(array(Yii::app()->controller->id.'/settings'));
	}
	
	public function actionprovider_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Provider") : t("Update Provider");
		CommonUtility::setMenuActive('.sms',".sms_settings");
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_sms_provider::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}				
			$model->providerid = $model->provider_id;
		} else {			
			$model=new AR_sms_provider;			
			$model->providerid = 'new';
		}
		
		if(isset($_POST['AR_sms_provider'])){
			$model->attributes=$_POST['AR_sms_provider'];
			if($model->validate()){
				
				$model->as_default = (integer) $model->as_default;	
				$model->provider_id = CommonUtility::SeoURL( $model->provider_id );
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/settings'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_save));
			}
		}
		
		$this->render("sms_provider_".$model->providerid,array(
		    'model'=>$model,			    
		    'links'=>array(
	            t("All Provider")=>array(Yii::app()->controller->id.'/settings'),        
                $this->pageTitle,
		    ),			    
		    'bhash_sms_type'=>AR_sms_provider::Bhash_smstype(),
		    'bhash_priority'=>AR_sms_provider::Bhash_priority(),
		    'spothit_sms_type'=>AR_sms_provider::Spothit_smstype(),
		));		
	}
	
	public function actionprovider_update()
	{
		$this->actionprovider_create(true);
	}
	
	public function actionprovider_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_sms_provider::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/settings'));			
		} else $this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));				
	}
	
	public function actiontransactions($update=false)
	{
		$this->pageTitle=t("SMS Transactions");
		$action_name='sms_transaction';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/transaction_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'sms_transaction_app';
		} else $tpl = 'sms_transaction';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/transaction_create")
		));

	}
	
	public function actionlogs($update=false)
	{
		$this->pageTitle=t("SMS Logs");
		
		$table_col = array(
		 'date_created'=>array(
		    'label'=>t("Date"),
		    'width'=>'10%'
		  ),		  
		  'gateway'=>array(
		    'label'=>t("Provider"),
		    'width'=>'10%'
		  ),
		  'contact_phone'=>array(
		    'label'=>t("to"),
		    'width'=>'15%'
		  ),		  
		 'sms_message'=>array(
		    'label'=>t("Message"),
		    'width'=>'15%'
		  ),		  
		  'status'=>array(
		    'label'=>t("Status"),
		    'width'=>'15%'
		  ),		  		  
		  'actions'=>array(
		    'label'=>t("Actions"),
		    'width'=>'10%'
		  ),		  		  
		);
		$columns = array(
		  array('data'=>'date_created'),
		  array('data'=>'gateway'),
		  array('data'=>'contact_phone'),
		  array('data'=>'sms_message'),	
		  array('data'=>'status'),	
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_view normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
			    <a class="ref_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>
			 </div>
		     '
		  ),	  
		);				
		
		$this->render('sms_logs',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>0,
          'sortby'=>'desc',		  
		));
		
	}
	
	public function actiondelete()
	{
		$id = trim(Yii::app()->input->get('id'));							
		$model = AR_sms_broadcast_details::model()->findByPk( intval($id) );		
		if($model){			
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('sms/logs'));	
		} else $this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));
	}
	
	public function actionsms_create()
	{
		$this->pageTitle = t("Send SMS");
		CommonUtility::setMenuActive('.sms',".sms_logs");
		
		$model=new AR_sms_broadcast;
		
		if(isset($_POST['AR_sms_broadcast'])){
			$model->attributes=$_POST['AR_sms_broadcast'];
			if($model->validate()){								
				if($model->save()){
					$this->redirect(array(Yii::app()->controller->id.'/logs'));		
				} else Yii::app()->user->setFlash('error',t(Helper_failed_save));
			}
		}
		
		$this->render("sms_create",array(
		    'model'=>$model,	
		    'provider'=>AttributesTools::SMSProvider(),
		    'links'=>array(
	            t("All Logs")=>array(Yii::app()->controller->id.'/logs'),        
                $this->pageTitle,
		    ),		    
		));		
	}
	
	public function actionlogs_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_sms_broadcast::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/logs'));			
		} else $this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));				
	}
	
	public function actionsms_view()
	{
		$this->pageTitle = t("Send View");
		CommonUtility::setMenuActive('.sms',".sms_logs");
		
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_sms_broadcast::model()->findByPk( $id );
		if($model){
			$this->render("sms_view",array(
		    'model'=>$model,			    
		    'links'=>array(
	            t("All Logs")=>array(Yii::app()->controller->id.'/logs'),        
                $this->pageTitle,
		    ),		    
		));					
		} else $this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));		
	}
	
}
/*end class*/