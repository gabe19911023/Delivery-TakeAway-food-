<?php
class NotificationsController extends CommonController
{
	public $layout='backend';
		
	public function beforeAction($action)
	{										
		return true;
	}
		
	public function actionprovider()
	{
		$this->pageTitle=t("Email Provider");
		$action_name='email_provider_list';
		$delete_link = Yii::app()->CreateUrl("notifications/email_provider_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'email_provider_list_app';
		} else $tpl = 'email_provider_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/provider_create")
		));
	}
	
	public function actionprovider_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Provider") : t("Update Provider");
		CommonUtility::setMenuActive('.notifications',".notifications_provider");
		
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_email_provider::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
			
			$model->email_provider_id  = $model->provider_id;
		} else {			
			$model=new AR_email_provider;	
			$model->email_provider_id = "new";
			$model->scenario = 'create';
		}

		if(isset($_POST['AR_email_provider'])){
			$model->attributes=$_POST['AR_email_provider'];
			if($model->validate()){						
				if($model->save()){
					if(!$update){
					   $this->redirect(array('notifications/provider'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
							
		$this->render("provider_create_".$model->email_provider_id,array(
		    'model'=>$model,	
		    'links'=>array(
	            t("All Provider")=>array('notifications/provider'),        
                $this->pageTitle,
		    ),	    
		    'secured_connection'=>AttributesTools::SecureConnection()
		));
	}
	
	public function actionprovider_update()
	{		
		$this->actionprovider_create(true);
	}	
	
	public function actionemail_provider_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_email_provider::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('notifications/provider'));			
		} else $this->render("error");
	}
	
	public function actiontemplate()
	{
		$this->pageTitle=t("Templates");
		$action_name='templates_list';
		$delete_link = Yii::app()->CreateUrl("notifications/template_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'templates_list_app';
		} else $tpl = 'templates_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/template_create")
		));		
	}
	
	public function actiontemplate_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Template") : t("Update Template");
		CommonUtility::setMenuActive('.notifications',".notifications_template");
		
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_templates::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
			
		} else {			
			$model=new AR_templates;			
		}

		$multi_language = CommonUtility::MultiLanguage();
		$language = AttributesTools::getLanguageAll();
		
		if(!$multi_language){
			$language = array();
			$language['en']='en';
			$multi_language=true;			
		}
				
		$model->multi_language = $multi_language;		
		
		if(isset($_POST['AR_templates'])){
			$model->attributes=$_POST['AR_templates'];
			if($model->validate()){						
				if($model->save()){
					if(!$update){
					   $this->redirect(array('notifications/template'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		$data  = array();
		$data['email']=array();$data['sms']=array();$data['push']=array();
		if($update){
			$ar=new AR_templates;	
			if($email = $ar->getData($model->template_id,'email')){
				$data['email']['email_title_translation'] = isset($email['title'])?$email['title']:'';			
				$data['email']['email_content_translation'] = isset($email['content'])?$email['content']:'';
			}
			if($email = $ar->getData($model->template_id,'sms')){
				$data['sms']['sms_title_translation'] = isset($email['title'])?$email['title']:'';			
				$data['sms']['sms_content_translation'] = isset($email['content'])?$email['content']:'';
			}
			if($email = $ar->getData($model->template_id,'push')){
				$data['push']['push_title_translation'] = isset($email['title'])?$email['title']:'';			
				$data['push']['push_content_translation'] = isset($email['content'])?$email['content']:'';
			}
		}		
				
					
		$fields_email = $model->getFields('email');				
		$fields_sms = $model->getFields('sms');				
		$fields_push = $model->getFields('push');
		
		$this->render("template_create",array(
		    'model'=>$model,	
		    'links'=>array(
	            t("All Templates")=>array('notifications/template'),        
                $this->pageTitle,
		    ),		    
		    'multi_language'=>$multi_language,
		    'language'=>$language,
		    'fields_email'=>$fields_email,		    
		    'fields_sms'=>$fields_sms,	
		    'fields_push'=>$fields_push,
		    'data'=>$data
		));
	}
	
	public function actiontemplate_update()
	{		
		$this->actiontemplate_create(true);
	}	
	
	public function actiontemplate_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_templates::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('notifications/template'));			
		} else $this->render("error");
	}	
	
	public function actiontemplate_restore()
	{
		$CreateTables = new MG_templates;
		$CreateTables->up();
		$this->redirect(array('notifications/template'));
	}
	
	public function actionemail_logs()
	{
		$this->pageTitle=t("Email Logs");

		$table_col = array(
		 'date_created'=>array(
		    'label'=>t("Date"),
		    'width'=>'8%'
		  ),		  
		  'email_address'=>array(
		    'label'=>t("to"),
		    'width'=>'15%'
		  ),
		  'subject'=>array(
		    'label'=>t("Content"),
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
		  array('data'=>'email_address'),
		  array('data'=>'subject'),
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
		
		$this->render('email_logs',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>0,
          'sortby'=>'desc',		  
		));	
	}	
	
	public function actiondelete_email()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$model = AR_email_logs::model()->findByPk( $id );		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('notifications/email_logs'));				
		} else $this->render("error");
	}	
	
	public function actionemail_view()
	{
		$this->pageTitle = t("Email View");
		CommonUtility::setMenuActive('.notifications',".notifications_email_logs");
		
	    $id = (integer) Yii::app()->input->get('id');					
		$model = AR_email_logs::model()->findByPk( $id );		
		if($model){			
			$this->render("email_view",array(
			  'model'=>$model,	
			  'links'=>array(
	            t("All Email")=>array(Yii::app()->controller->id.'/email_logs'),        
                $this->pageTitle,
		    ),	    
			));
		} else $this->render("error");
	}

	public function actionall_notification()
	{
		$this->pageTitle=t("All notifications");
				
		$table_col = array(		  
		  'date_created'=>array(
		    'label'=>t("Date"),
		    'width'=>'15%'
		  ),		  
		  'message'=>array(
		    'label'=>t("Message"),
		    'width'=>'60%'
		  ),
		);
		$columns = array(
		  array('data'=>'date_created'),	
		  array('data'=>'message'),		  
		);				
		
		$this->render('notifications_all',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',		  
		));
	}
	
	public function actionpush_logs()
	{
		$this->pageTitle=t("Push logs");
				
		$table_col = array(
		 'date_created'=>array(
		    'label'=>t("Date"),
		    'width'=>'10%'
		  ),		  
		  'platform'=>array(
		    'label'=>t("Platform"),
		    'width'=>'10%'
		  ),
		  'body'=>array(
		    'label'=>t("Message"),
		    'width'=>'25%'
		  ),		  
		  'channel_device_id'=>array(
		    'label'=>t("Channel/Device"),
		    'width'=>'10%'
		  ),		  		  
		  'actions'=>array(
		    'label'=>t("Actions"),
		    'width'=>'10%'
		  ),		  		  
		);
		$columns = array(
		  array('data'=>'date_created'),
		  array('data'=>'platform'),
		  array('data'=>'body'),
		  array('data'=>'channel_device_id'),	
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_view normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
			    <a class="ref_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>
			 </div>
		     '
		  ),	  
		);				
		
		$this->render('push_logs',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>0,
          'sortby'=>'desc',		  
		));
	}
	
	public function actiondelete_push()
	{
		$id = trim(Yii::app()->input->get('id'));							
		$model = AR_push::model()->findByPk( $id );		
		if($model){			
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('notifications/push_logs'));	
		} else $this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));
	}
	
}
/*end class*/