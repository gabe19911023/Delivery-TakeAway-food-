<?php
class UsermerchantController extends Commonmerchant
{
	
	public function beforeAction($action)
	{										
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		return true;
	}
	
	public function actionIndex()
	{	
		$this->redirect(array(Yii::app()->controller->id.'/user_list'));		
	}	
	
	public function actionuser_list()
	{
		$this->pageTitle=t("User List");
		$action_name='user_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/user_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');		
		
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//user/user_list';

		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/user_create")
		));	
	}
	
	public function actionuser_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add User") : t("Update User");
		CommonUtility::setMenuActive('.merchan_user','.usermerchant_user_list');			
				
		$merchant_id =  (integer)  Yii::app()->merchant->merchant_id;
		$upload_path = CMedia::merchantFolder();
		
		if($update){
			$id = Yii::app()->input->get('id');	
			$model = AR_merchant_user::model()->find("merchant_id=:merchant_id AND user_uuid=:user_uuid",array(
			  ':merchant_id'=>$merchant_id,
			  ':user_uuid'=>$id
			));				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}		
		} else {
			$model=new AR_merchant_user;
			$model->status = 'active';
			$model->scenario='create_user';
		}
		
		if(isset($_POST['AR_merchant_user'])){
			$model->attributes=$_POST['AR_merchant_user'];			
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				
				$model->main_account = 0;		   
		    	if(!empty($model->new_password) && !empty($model->new_password)){					
					//$model->password = md5(trim($model->new_password));
					$model->password = trim($model->new_password);
				}
				
				/*$model->image=CUploadedFile::getInstance($model,'image');
				if($model->image){											
					$model->profile_photo = CommonUtility::uploadNewFilename($model->image->name);					
					$path = CommonUtility::uploadDestination('')."/".$model->profile_photo;								
					$model->image->saveAs( $path );
				}*/	
				
				if(isset($_POST['profile_photo'])){
					if(!empty($_POST['profile_photo'])){
						$model->profile_photo = $_POST['profile_photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->profile_photo = '';
				} else $model->profile_photo = '';
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/user_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		$role = CommonUtility::getDataToDropDown("{{role}}",'role_id','role_name',
		  "WHERE role_type='merchant' AND merchant_id=".q($merchant_id)." ",
		  'order by role_name ASC'
		);		
		
		$params_model = array(
		    'model'=>$model,	
		    'status'=>(array)AttributesTools::StatusManagement('customer'),
		    'role'=>$role,
		    'upload_path'=>$upload_path,
		    'links'=>array(
	            t("All User")=>array(Yii::app()->controller->id.'/user_list'),        
                $this->pageTitle,
		    ),	   
		    'ctr'=>Yii::app()->controller->id."/user_remove_image"
		);
		
		
		if($update){
			
			//$avatar = CommonUtility::getPhoto($model->profile_photo, CommonUtility::getPlaceholderPhoto('customer'));
			
	        $avatar = CMedia::getImage($model->profile_photo,$model->path,'@thumbnail',
			CommonUtility::getPlaceholderPhoto('customer'));			
			
			WidgetUser::$ctr[0] = Yii::app()->controller->id."/user_update";
			WidgetUser::$ctr[1] = Yii::app()->controller->id."/change_password";
			WidgetUser::$ctr[2] = Yii::app()->controller->id."/zone";
			
			$this->render("/admin/submenu_tpl",array(
			  'model'=>$model,
			  'template_name'=>'//merchant/user_update',
			  'widget'=>'WidgetUser',		
			  'params'=>$params_model,
			  'avatar'=>$avatar,
			  'upload_path'=>$upload_path,
			));
		} else $this->render("//merchant/user_create",$params_model);				
	}
	
	public function actionuser_update()
	{
		$this->actionuser_create(true);
	}
	
	public function actionuser_delete()
	{
		$merchant_id =  (integer)  Yii::app()->merchant->merchant_id;		
		$id =  Yii::app()->input->get('id');			
		$model = AR_merchant_user::model()->find("merchant_id=:merchant_id AND user_uuid=:user_uuid",array(		  
		  ':merchant_id'=>$merchant_id,
		  ':user_uuid'=>$id,
		));
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/user_list'));			
		} else $this->render("error");
	}
	
	public function actionuser_remove_image()
	{
		$merchant_id =  (integer)  Yii::app()->merchant->merchant_id;		
		$id =  Yii::app()->input->get('id');			
		$model = AR_merchant_user::model()->find("merchant_id=:merchant_id AND merchant_id_token=:merchant_id_token",array(		  
		  ':merchant_id'=>$merchant_id,
		  ':merchant_id_token'=>$id,
		));
		if($model){
			$model->profile_photo = '';
			$model->save();
			$this->redirect(array(Yii::app()->controller->id.'/user_update','id'=>$id));			
		} else $this->render("error");
	}
	
	public function actionchange_password()
	{
		$this->pageTitle = t("Update Password");
		CommonUtility::setMenuActive('.merchan_user','.usermerchant_user_list');	

		$merchant_id =  (integer)  Yii::app()->merchant->merchant_id;
		$id = Yii::app()->input->get('id');	
		
		$model = AR_merchant_user::model()->find("merchant_id=:merchant_id AND user_uuid=:user_uuid",array(
		  ':merchant_id'=>$merchant_id,
		  ':user_uuid'=>$id
		));				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}		
		
		$model->scenario = 'change_password';	
		
		if(isset($_POST['AR_merchant_user'])){
			$model->attributes=$_POST['AR_merchant_user'];
			if($model->validate()){
				
				if(!empty($model->new_password) && !empty($model->new_password)){					
					$model->password = md5(trim($model->new_password));
				}
				
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		$params_model = array(
		    'model'=>$model,			    		    
		    'links'=>array(
	            t("All User")=>array(Yii::app()->controller->id.'/user_list'),        
                $this->pageTitle,
		    ),	   		    
		);
				
		$avatar = CMedia::getImage($model->profile_photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('customer'));
		
		WidgetUser::$ctr[0] = Yii::app()->controller->id."/user_update";
		WidgetUser::$ctr[1] = Yii::app()->controller->id."/change_password";
		WidgetUser::$ctr[2] = Yii::app()->controller->id."/zone";
		
		$this->render("/admin/submenu_tpl",array(
			  'model'=>$model,
			  'template_name'=>'//merchant/user_change_password',
			  'widget'=>'WidgetUser',		
			  'params'=>$params_model,
			  'avatar'=>$avatar,
			));		
	}
	
	public function actionrole_list()
	{
		$this->pageTitle = t("All Roles");
		
		$action_name='role_list';		
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/role_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//user/role_list';

		 $this->render($tpl,array(
		   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/role_create")
		 ));		
	}
	
	public function actionrole_create($update=false)
	{
	
		$this->pageTitle = $update==false? t("Add Role") : t("Update Role");
		CommonUtility::setMenuActive('.merchan_user',".usermerchant_role_list");
		
		$merchant_id =  (integer)  Yii::app()->merchant->merchant_id;
		
		$id='';	$role_access = array();
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');				
			$model = AR_Role::model()->find("role_id=:role_id AND merchant_id=:merchant_id AND role_type=:role_type",array(
			  ':merchant_id'=>$merchant_id,
			  ':role_type'=>'merchant',
			  ':role_id'=>$id
			));				
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
		    	$model->role_type='merchant';			    				    	
		    	$model->merchant_id = $merchant_id ;		
		    	
				if($model->save()){						
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/role_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else {					
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}				
			}
		}
		
		AdminMenu::buildMenu(0,true,0,'merchant');			
		$this->render("//user/role_create",array(
		  'model'=>$model,		
		  'menu'=>AdminMenu::$items,
		  'role_access'=>$role_access,
		  'links'=>array(
		    t("All Roles")=>array(Yii::app()->controller->id.'/role_list'), 
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
		$merchant_id =  (integer)  Yii::app()->merchant->merchant_id;
		$id = (integer) Yii::app()->input->get('id');		
		
		$model = AR_Role::model()->find("role_id=:role_id AND merchant_id=:merchant_id AND role_type=:role_type",array(
			  ':merchant_id'=>$merchant_id,
			  ':role_type'=>'merchant',
			  ':role_id'=>$id
			));				
			
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/role_list'));			
		} else $this->render("error");
	}
	
}
/*end class*/