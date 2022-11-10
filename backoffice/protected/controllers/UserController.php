<?php
class UserController extends CommonController
{
	public $layout='backend';
		
	public function beforeAction($action)
	{										
		InlineCSTools::registerStatusCSS();
		return true;
	}
		
	public function actionlist()
	{
		$this->pageTitle = t("All User");
		$action_name='Alluser';
		$delete_link = Yii::app()->CreateUrl("user/delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		$this->render("user_list",array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create")
		));		
	}
	
	public function actioncreate()
	{
		$this->pageTitle = t("Add new user");
		CommonUtility::setMenuActive('.admin_user',".user_list");
		
		$model=new AR_AdminUser;
		$model->scenario='create';
				
		if(isset($_POST['AR_AdminUser'])){
		    $model->attributes=$_POST['AR_AdminUser'];			    	
		    if($model->validate()){	
		    	$model->main_account = 0;					    				    	
		    	
		    	if(!empty($model->new_password) && !empty($model->new_password)){					
					$model->password = md5(trim($model->new_password));
				}
				
				if($model->save()){			    			
					Yii::app()->user->setFlash('success', t(Helper_success) );										
					$this->redirect(array('user/list'));
					Yii::app()->end();
				} else {					
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}				
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
				
		$role = CommonUtility::getDataToDropDown("{{role}}",'role_id','role_name',"WHERE role_type='admin'",
		'order by role_name ASC'
		);
		
		$this->render("user_add",array(
		  'model'=>$model,
		  'role'=>$role,
		  'status'=>(array)AttributesTools::StatusManagement('customer'),
	    ));
	}
	
	public function actionupdate()
	{
		$this->pageTitle = t("Edit User");
		CommonUtility::setMenuActive('.admin_user',".user_list");
				
		$id =  Yii::app()->input->get('id');		
		$upload_path = CMedia::adminFolder();
		
		if(strlen($id)>1){
			
			$model = AR_AdminUser::model()->find( "admin_id_token=:admin_id_token" ,array(
			 ':admin_id_token'=>$id
			));
			
			if(!$model){
				$this->render("error");
				Yii::app()->end();
			}
			
			$model->scenario='update';	
			
			if(isset($_POST['AR_AdminUser'])){
			    $model->attributes=$_POST['AR_AdminUser'];			
			    if($model->validate()){				
				    			    
			    	if(isset($_POST['photo'])){
						if(!empty($_POST['photo'])){
							$model->profile_photo = $_POST['photo'];
							$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
						} else $model->profile_photo = '';
					} else $model->profile_photo = '';
			    											
					if($model->save()){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					} else {
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				}
			}
			
			$role = CommonUtility::getDataToDropDown("{{role}}",'role_id','role_name',"WHERE role_type='admin'",
			'order by role_name ASC' );
							
							
			$avatar = CMedia::getImage($model->profile_photo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('customer'));		    
			
			WidgetUser::$ctr[0] = Yii::app()->controller->id."/update";
			WidgetUser::$ctr[1] = Yii::app()->controller->id."/change_password";
			WidgetUser::$ctr[2] = Yii::app()->controller->id."/zone";
			
			$this->render("/admin/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"user_edit",
				'widget'=>'WidgetUser',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'role'=>$role,
			       'status'=>(array)AttributesTools::StatusManagement('customer'),
				   'links'=>array(
			            t("All User")=>array('user/list'),        
		                $this->pageTitle,
				    ),	 
				    'upload_path'=>$upload_path,
				 )
		   ));
			
		} else $this->render("error");
	}
	
	public function actionchange_password()
	{
		$this->pageTitle = t("Edit User");
		CommonUtility::setMenuActive('.admin_user',".user_list");
		
		$id =  Yii::app()->input->get('id');		
		if(strlen($id)>1){
			
			$model = AR_AdminUser::model()->find( "admin_id_token=:admin_id_token" ,array(
			 ':admin_id_token'=>$id
			));
			
			if(!$model){
				$this->render("error");
				Yii::app()->end();
			}
			
			$model->scenario='update_user_password';	
			
			if(isset($_POST['AR_AdminUser'])){
			    $model->attributes=$_POST['AR_AdminUser'];			
			    if($model->validate()){				

			    	if(!empty($model->new_password) && !empty($model->new_password)){					
						$model->password = md5(trim($model->new_password));						
					}
											
					if($model->save()){
						Yii::app()->user->setFlash('success',t("Password updated"));
						$this->refresh();
					} else {
						Yii::app()->user->setFlash('error',t(Helper_failed_update));
					}				
				}
			}
			
			$avatar = CMedia::getImage($model->profile_photo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('customer'));		    
			
			WidgetUser::$ctr[0] = Yii::app()->controller->id."/update";
			WidgetUser::$ctr[1] = Yii::app()->controller->id."/change_password";
			WidgetUser::$ctr[2] = Yii::app()->controller->id."/zone";
			
			$this->render("/admin/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"user_changepass",
				'widget'=>'WidgetUser',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   				   			       
				   'links'=>array(
			            t("All User")=>array('user/list'),        
		                $this->pageTitle,
				    ),	 
				 )
		   ));
			
		} else $this->render("error");
	}
	
	public function actiondelete()
	{
		$id =  Yii::app()->input->get('id');				
		$model = AR_AdminUser::model()->find( "admin_id_token=:admin_id_token" ,array(
			 ':admin_id_token'=>$id
			));
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('user/list'));			
		} else $this->render("error");
	}
	
	public function actionroles_list()
	{
		$this->pageTitle = t("All Roles");
		
		$action_name='RoleList';		
		$delete_link = Yii::app()->CreateUrl("user/role_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		 $this->render("role_list",array(
		   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/role_create")
		 ));		
	}
	
	public function actionrole_create($update=false)
	{
	
		$this->pageTitle = $update==false? t("Add Role") : t("Update Role");
		CommonUtility::setMenuActive('.admin_user',".user_roles_list");
		
		$id='';	$role_access = array();
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');				
			$model = AR_Role::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}				
			$role_access = $model->getRoleAccess();
		} else {			
			$model=new AR_Role;		
		}
		
		if(isset($_POST['AR_Role'])){
		    $model->attributes=$_POST['AR_Role'];			    	
		    if($model->validate()){						    				    	
				if($model->save()){						
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/roles_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else {					
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}				
			}
		}
		
		AdminMenu::buildMenu(0,true);
		$this->render("role_create",array(
		  'model'=>$model,		
		  'menu'=>AdminMenu::$items,
		  'role_access'=>$role_access,
		  'links'=>array(
		    t("All Roles")=>array(Yii::app()->controller->id.'/roles_list'), 
		    $this->pageTitle
		  )
	    ));
	}
	
	public function actionrole_update()
	{
		$this->actionrole_create(true);
	}
	
	public function actionrole_delete()
	{
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_Role::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('user/roles_list'));			
		} else $this->render("error");
	}
	
	
	public function actionzone()
	{
		$this->pageTitle = t("Zone");
		CommonUtility::setMenuActive('.admin_user',".user_list");
		
		$id =  Yii::app()->input->get('id');		
		$meta_name = 'zone';
				
		
		$model = AR_AdminUser::model()->find( "admin_id_token=:admin_id_token" ,array(
		 ':admin_id_token'=>$id
		));
		
		if(!$model){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t(Helper_not_found)
			 )
			));
			return ;
		}
		
		$admin_id = $model->admin_id;		
					
		$models = new AR_admin_meta;
		if(isset($_POST['AR_admin_meta'])){

			AR_admin_meta::model()->deleteAll('meta_name=:meta_name AND meta_value=:meta_value',array(
			 ':meta_name'=> $meta_name,
			 ':meta_value'=>intval($admin_id)
			));
			
	    	$post = Yii::app()->input->xssClean($_POST); 
	    	$zone = isset($post['AR_admin_meta']['zone'])?$post['AR_admin_meta']['zone']:'';
	    	if(is_array($zone) && count($zone)>=1){
	    		foreach ($zone as $zone_id) {
	    			$meta = new AR_admin_meta;		    			
	    			$meta->meta_name = $meta_name;
	    			$meta->meta_value = intval($admin_id);
	    			$meta->meta_value1= intval($zone_id);
	    			$meta->save();
	    		}		    		
	    	}	
	    	Yii::app()->user->setFlash('success', t(Helper_success) );
			$this->refresh();							    				   
			 
		} else if ( isset($_POST['yt0']) ) {
			AR_admin_meta::model()->deleteAll('meta_name=:meta_name AND meta_value=:meta_value',array(
			 ':meta_name'=> $meta_name,
			 ':meta_value'=>intval($admin_id)
			));
		}
						
		$zone_data = CommonUtility::getDataToDropDown("{{admin_meta}}",'meta_value1','meta_value1',
		"where meta_name=".q($meta_name)." AND meta_value=".q($admin_id)." " );		    
		
		$models->zone = (array)$zone_data;
		
		$avatar = CMedia::getImage($model->profile_photo,$model->path,'@thumbnail',
	    CommonUtility::getPlaceholderPhoto('customer'));		    
		
		WidgetUser::$ctr[0] = Yii::app()->controller->id."/update";
		WidgetUser::$ctr[1] = Yii::app()->controller->id."/change_password";
		WidgetUser::$ctr[2] = Yii::app()->controller->id."/zone";
		
		$this->render("/admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"zone_user",
			'widget'=>'WidgetUser',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$models,		
			   'zone_list'=>CommonUtility::getDataToDropDown("{{zones}}",'zone_id','zone_name','where merchant_id=0',"Order by zone_name asc"),	   				   			       
			   'links'=>array(
		            t("All User")=>array('user/list'),        
	                $this->pageTitle,
			    ),	 
			 )
	   ));			
		
	}

	
}
/*end class*/