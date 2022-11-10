<?php
require 'php-jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminController extends CommonController
{
		
	public function beforeAction($action)
	{				
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
			
		return true;
	}
		
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else	    	    
	        	$this->render('error', array(
	        	 'error'=>$error
	        	));
	    }
	}
	
	public function actionlogout()
	{
		Yii::app()->user->logout(false);
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionIndex()
	{	
		$this->redirect(array(Yii::app()->controller->id.'/dashboard'));		
	}	
	
	public function actiondashboard()
	{							
		$this->render('dashboard',array(
		  'orders_tab'=>AttributesTools::dashboardOrdersTab(),
		  'item_tab'=>AttributesTools::dashboardItemTab(),
		  'popular_merchant_tab'=>AttributesTools::dashboardPopularMerchantTab(),
		  'limit'=>5,
		  'months'=>6,		  
		  'ajax_url'=>Yii::app()->createUrl("/api"),		  
		));
	}
		
	public function actionprofile()
	{
		$this->pageTitle = CommonUtility::t("Profile");
		
		$model = AR_AdminUser::model()->findByPk( Yii::app()->user->id );
		if(!$model){
			$this->render("error");
			Yii::app()->end();
		}
		
		$model->scenario='profile_update';
		$upload_path = CMedia::adminFolder();
				
		if(isset($_POST['AR_AdminUser'])){
			$model->attributes=$_POST['AR_AdminUser'];			
			if($model->validate()){				
												
				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->profile_photo = $_POST['photo'];
						Yii::app()->user->avatar = $_POST['photo'];					
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->profile_photo = '';
				} else $model->profile_photo = '';			
				
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t("Profile updated"));
					$this->refresh();
				} else {
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}				
			} 
		}
							
		$avatar = CMedia::getImage($model->profile_photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('customer'));
		
		$settings = AR_admin_meta::getMeta(array('webpush_app_enabled'));			
		$webpush_app_enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';		
		
		WidgetUserMenu::$ctr[0] = Yii::app()->controller->id."/profile";
		WidgetUserMenu::$ctr[1] = Yii::app()->controller->id."/change_password";
		if($webpush_app_enabled){
		   WidgetUserMenu::$ctr[2] = Yii::app()->controller->id."/web_notifications";
		}
		
		$this->render("submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"profile",
			'widget'=>'WidgetUserMenu',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,		
			   'upload_path'=>$upload_path,	   
			   'links'=>array(		            
			   ),
			 )
		));
	}
	
	public function actionprofile_remove_image()
	{
	    $id =  (integer)  Yii::app()->user->id;		    
	    $model = AR_AdminUser::model()->findByPk($id);
	    if($model){
			Yii::app()->user->setState("avatar",'');
			$model->profile_photo = '';
			$model->save();
			$this->redirect(array(Yii::app()->controller->id.'/profile'));			
		} else $this->render("error");
	}
	
	public function actionchange_password()
	{
		$this->pageTitle = CommonUtility::t("Change Password");
						
		$model = AR_AdminUser::model()->findByPk( Yii::app()->user->id );
		if(!$model){
			$this->render("error");
			Yii::app()->end();
		}
		
		$model->scenario='update_password';
		
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
		
		$settings = AR_admin_meta::getMeta(array('webpush_app_enabled'));			
		$webpush_app_enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';		
		WidgetUserMenu::$ctr[0] = Yii::app()->controller->id."/profile";
		WidgetUserMenu::$ctr[1] = Yii::app()->controller->id."/change_password";
		if($webpush_app_enabled){
		   WidgetUserMenu::$ctr[2] = Yii::app()->controller->id."/web_notifications";
		}
		
		$this->render("submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"profile_changepass",
			'widget'=>'WidgetUserMenu',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,			   
			   'links'=>array(		            
			   ),
			 )
		));
	}	
	
	public function actionweb_notifications()
	{
		$this->pageTitle = CommonUtility::t("Change Password");
		
		$model = AR_AdminUser::model()->findByPk( Yii::app()->user->id );
		if(!$model){
			$this->render("error");
			Yii::app()->end();
		}
		
		$avatar = CMedia::getImage($model->profile_photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('customer'));
		
		WidgetUserMenu::$ctr[0] = Yii::app()->controller->id."/profile";
		WidgetUserMenu::$ctr[1] = Yii::app()->controller->id."/change_password";		
		
		$settings = AR_admin_meta::getMeta(array('webpush_provider','pusher_instance_id','webpush_app_enabled'));			
		$webpush_provider = isset($settings['webpush_provider'])?$settings['webpush_provider']['meta_value']:'';
		$pusher_instance_id = isset($settings['pusher_instance_id'])?$settings['pusher_instance_id']['meta_value']:'';
		$webpush_app_enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';
		$webpush_app_enabled = $webpush_app_enabled==1?true:false;
		
		if($webpush_app_enabled){
		   WidgetUserMenu::$ctr[2] = Yii::app()->controller->id."/web_notifications";
		}

		if($webpush_app_enabled){
		$this->render("submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"profile_webpush",
			'widget'=>'WidgetUserMenu',		
			'avatar'=>$avatar,
			'params'=>array(  
			   'model'=>$model,			   
			   'iterest_list'=>AttributesTools::pushInterestList(),
			   'pusher_instance_id'=>$pusher_instance_id,
			   'webpush_provider'=>$webpush_provider,			   
			   'links'=>array(		            
			   ),
			 )
		));
		} else $this->render('//tpl/error',array(  
		  'error'=>array(
		    'message'=>t("Web push is not enabled")
		  )
		));
	}
	
	public function actionmigrate()
	{
		/*$CreateTables = new CreateTables;
		$CreateTables->up();*/
		//MigrateTables::run();
	}	
	
	public function actionallorder()
	{
		$this->render('allorder');
	}
	
	public function actioncancelorders()
	{
		$this->render('allorder');
	}
	
	public function actionsite_information()
	{		
		$this->pageTitle = t("Site configuration");		
		ItemIdentity::instantiateIdentity();
		
		$model=new AR_option;
		$model->scenario='site_config';
		
		$options = array('website_title','website_logo','mobilelogo','admin_country_set',
		'website_address','website_contact_phone','website_contact_email');
		
		$upload_path = CMedia::adminFolder();
		
		if(isset($_POST['AR_option'])){
										
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
								
				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->website_logo = $_POST['photo'];
					} else $model->website_logo = '';
				} else $model->website_logo = '';
								
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$country_list = AttributesTools::CountryList();
					
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_information",
		  'params'=>array(  
		   'model'=>$model,
		   'country_list'=>$country_list,
		   'upload_path'=>$upload_path,
		  )
		));
	}
	
	public function actioncurrency()
	{
		$this->pageTitle = t("Site configuration");
		
		$model=new AR_option;		
		
		$options = array('admin_currency_set','admin_currency_position','admin_decimal_place',
		'admin_thousand_separator','admin_decimal_separator');
				
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];			
			if($model->validate()){					
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$country_list = AttributesTools::CountryList();
					
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_currency",
		  'params'=>array(  
		   'model'=>$model,	
		   'currency'=>AttributesTools::CurrencyList(),
		   'currency_position'=>AttributesTools::CurrencyPosition(),
		  )
		));
	}
	
	public function actionmap_keys()
	{
		$this->pageTitle = t("Map API Keys");
		
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('map_provider','google_geo_api_key','google_maps_api_key'
		,'mapbox_access_token');
		
		if(isset($_POST['AR_option'])){
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				if(DEMO_MODE){
					$model[$name] = CommonUtility::mask(date("Ymjhs"));
				} else $model[$name]=$val;				
			}			
		}		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_mapkeys",
		  'params'=>array(  
		   'model'=>$model,
		   'map_provider'=>AttributesTools::mapsProvider()
		  )
		));
	}
	
	public function actionrecaptcha()
	{
		$this->pageTitle = t("Google Recaptcha");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		/*$options = array('captcha_site_key','captcha_secret','captcha_lang',
		'captcha_customer_signup','captcha_merchant_signup','captcha_customer_login',
		'captcha_merchant_login','captcha_admin_login','captcha_order','captcha_driver_signup',
		'captcha_contact_form','capcha_admin_login_enabled','capcha_merchant_login_enabled');*/
		
		$options = array('captcha_site_key','captcha_secret','captcha_lang','capcha_admin_login_enabled','capcha_merchant_login_enabled');
		
		if(isset($_POST['AR_option'])){
			
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}

			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_recaptcha",
		  'params'=>array(  
		   'model'=>$model		   
		  )
		));
	}
	
	public function actionprintingOLD()
	{
		$this->pageTitle = t("Printing Options");
		
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('admin_printing_receipt_width','admin_printing_receipt_size','website_enabled_rcpt','website_receipt_logo');
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				
				
				$model->image=CUploadedFile::getInstance($model,'image');
				if($model->image){
					$model->website_receipt_logo =  time()."-".$model->image->name;				
					$path = CommonUtility::uploadDestination('')."/".$model->website_receipt_logo;								
					$model->image->saveAs( $path );
				}
				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_printing",
		  'params'=>array(  
		   'model'=>$model,		   
		  )
		));
	}	
	
	public function actionprinting()
	{
		$model = new AR_admin_meta;
		$model->scenario = "with_translation";
		$upload_path = CMedia::adminFolder();
		
		if(isset($_POST['AR_admin_meta'])){			
									
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$post = Yii::app()->input->post('AR_admin_meta');		
			$receipt_logo = Yii::app()->input->post('receipt_logo');
			$receipt_logo_path = Yii::app()->input->post('path');
									
			$model->saveMeta('receipt_logo', $receipt_logo, $receipt_logo_path );
			$model->saveMeta('receipt_thank_you', isset($post['receipt_thank_you'])?$post['receipt_thank_you']:'','','with_translation');
			$model->saveMeta('receipt_footer', isset($post['receipt_footer'])?$post['receipt_footer']:'','','with_translation');
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_success));
			$this->refresh();			
		} else {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('meta_name',(array) array('receipt_logo','receipt_thank_you','receipt_footer') );
			$find = AR_admin_meta::model()->findAll($criteria);		
			if($find){
				foreach ($find as $items) {					
					$model[$items->meta_name] = $items->meta_value;
				}
			}			
		}
		
		$fields = array(); $data = array();		
		if(!isset($_POST['AR_admin_meta'])){
			$model_trans = AR_admin_meta::model()->find("meta_name=:meta_name",array(
		     ':meta_name'=>'receipt_thank_you'
		    )); 		    
		    if($model_trans){
				$translation = AttributesTools::GetFromTranslation($model_trans->meta_id,'{{admin_meta}}',
				'{{admin_meta_translation}}',
				'meta_id',
				array('meta_id','meta_value'),
				array('meta_value'=>'meta_value_trans')
				);					
				$data['receipt_thank_you_trans'] = isset($translation['meta_value'])?$translation['meta_value']:'';
		    }
		    
		    $model_trans1 = AR_admin_meta::model()->find("meta_name=:meta_name",array(
		     ':meta_name'=>'receipt_footer'
		    )); 		    
		    if($model_trans1){
				$translation = AttributesTools::GetFromTranslation($model_trans1->meta_id,'{{admin_meta}}',
				'{{admin_meta_translation}}',
				'meta_id',
				array('meta_id','meta_value'),
				array('meta_value'=>'meta_value_trans')
				);					
				$data['receipt_footer_trans'] = isset($translation['meta_value'])?$translation['meta_value']:'';
		    }
		}		
		
		$fields[]=array(
		  'name'=>'receipt_thank_you_trans',
		  'placeholder'=>"Receipt Thank you text - [lang]",
		  'type'=>"textarea"
		);
		$fields[]=array(
		  'name'=>'receipt_footer_trans',
		  'placeholder'=>"Receipt Footer text - [lang]",
		  'type'=>"textarea"
		);		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_printing",
		  'params'=>array(  
		     'model'=>$model,
		     'upload_path'=>$upload_path,
		     'language'=>AttributesTools::getLanguage(),
		     'fields'=>$fields,
		     'data'=>$data,
		  )
		));
	}
	
    public function actionlogin_sigup()
	{
		$this->pageTitle = t("Login & Signup");
		
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('signup_enabled_verification','signup_verification_type',
		'blocked_email_add','blocked_mobile', 'signup_type','signup_enabled_terms','signup_terms',
		'signup_enabled_capcha','signup_welcome_tpl','signup_verification_tpl','signup_resetpass_tpl',
		'signup_resend_counter','signupnew_tpl'
		);
		
		if(isset($_POST['AR_option'])){
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){					
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$template_list =  CommonUtility::getDataToDropDown("{{templates}}",'template_id','template_name',
		"","ORDER BY template_name ASC");
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,		  
		  'template_name'=>"site_login_sigup",
		  'params'=>array(  
		     'model'=>$model,
		     'signup_type_list'=>AttributesTools::signupTypeList(),	
		     'verification_list'=>AttributesTools::verificationType(),
		     'template_list'=>$template_list
		  )
		));
	}	
	
	
	public function actionphone_settings()
	{
		$this->pageTitle = t("Phone Settings");
		
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('mobilephone_settings_country','mobilephone_settings_default_country');
		
		if(isset($_POST['AR_option'])){
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){					
				$model->mobilephone_settings_country = !empty($model->mobilephone_settings_country)? json_encode($model->mobilephone_settings_country):'';
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
			$model->mobilephone_settings_country = !empty($model->mobilephone_settings_country)? json_decode(stripslashes($model->mobilephone_settings_country),true):'';			
		}		
				
		$this->render('site_config_tpl',array( 
		  'model'=>$model,		  
		  'template_name'=>"site_phone_settings",
		  'params'=>array(  
		     'model'=>$model,
		     'country_list'=>AttributesTools::CountryList(),				     
		  )
		));
	}	
	
    public function actionreviews()
	{
		$this->pageTitle = t("Reviews");		
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('website_review_type','review_baseon_status','earn_points_review_status','publish_review_status',
		'website_reviews_actual_purchase','merchant_can_edit_reviews','review_template_id','review_send_after',
		'review_template_enabled','review_image_resize_width'
		);
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				
				$model->review_baseon_status = !empty($model->review_baseon_status)? json_encode($model->review_baseon_status):'';
				$model->earn_points_review_status = !empty($model->earn_points_review_status)? json_encode($model->earn_points_review_status):'';
				$model->publish_review_status = !empty($model->publish_review_status)? json_encode($model->publish_review_status):'';
				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}									
			$model->review_baseon_status = !empty($model->review_baseon_status)? json_decode(stripslashes($model->review_baseon_status),true):'';			
			$model->earn_points_review_status = !empty($model->earn_points_review_status)? json_decode(stripslashes($model->earn_points_review_status),true):'';			
			$model->publish_review_status = !empty($model->publish_review_status)? json_decode(stripslashes($model->publish_review_status),true):'';
		}		
		
		$template_list =  CommonUtility::getDataToDropDown("{{templates}}",'template_id','template_name',
		"","ORDER BY template_name ASC");
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_reviews",
		  'params'=>array(  
		   'model'=>$model,
		   'review_type'=>AttributesTools::reviewType(),
		   'status_list'=>AttributesTools::StatusList(),
		   'template_list'=>$template_list
		  )
		));
	}	
	
	public function actionsecurity()
	{
		$this->pageTitle = t("Security");
		
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('website_admin_mutiple_login','website_merchant_mutiple_login');
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_security",
		  'params'=>array(  
		   'model'=>$model,
		   'map_provider'=>AttributesTools::mapsProvider()
		  )
		));
	}
	
    public function actiontimezone()
	{
		$this->pageTitle = t("Timezone");
		
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('website_timezone_new','website_date_format_new','website_time_format_new','website_time_picker_interval');
		
		if(isset($_POST['AR_option'])){
									
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_timezone",
		  'params'=>array(  
		   'model'=>$model,
		   'timezone'=>AttributesTools::timezoneList(),
		   'date_format'=>AttributesTools::DateFormat(),
		   'time_format'=>AttributesTools::TimeFormat()
		  )
		));
	}
	
    public function actionordering()
	{
		$this->pageTitle = t("Ordering");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('disabled_website_ordering','website_hide_foodprice','website_disbaled_auto_cart',
		'website_disabled_cart_validation','enabled_merchant_check_closing_time','disabled_order_confirm_page',
		'restrict_order_by_status','enabled_map_selection_delivery','admin_service_fee','admin_service_fee_applytax',
		'cancel_order_enabled','cancel_order_days_applied','cancel_order_hours','cancel_order_status_accepted',
		'website_review_approved_status','enabled_website_ordering'
		);
		
		if(isset($_POST['AR_option'])){
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}

			$model->attributes=$_POST['AR_option'];
			if($model->validate()){		
				
				$model->restrict_order_by_status = !empty($model->restrict_order_by_status)? json_encode($model->restrict_order_by_status):'';
				$model->cancel_order_status_accepted = !empty($model->cancel_order_status_accepted)? json_encode($model->cancel_order_status_accepted):'';
						
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
			$model->restrict_order_by_status = !empty($model->restrict_order_by_status)? json_decode(stripslashes($model->restrict_order_by_status),true):'';
			$model->cancel_order_status_accepted = !empty($model->cancel_order_status_accepted)? json_decode(stripslashes($model->cancel_order_status_accepted),true):'';
		}		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_ordering",
		  'params'=>array(  
		   'model'=>$model,		   
		   'status_list'=>AttributesTools::StatusList()
		  )
		));
	}	
	
	public function actionmenu_options()
	{
		$this->pageTitle = t("Menu Options");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('admin_menu_allowed_merchant','admin_menu_lazyload','mobile2_hide_empty_category',
		'admin_activated_menu','enabled_food_search_menu');
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_menu_options",
		  'params'=>array(  
		   'model'=>$model,
		   'menu_style'=>AttributesTools::MenuStyle()
		  )
		));
	}
	
	public function actionmerchant_registration()
	{
		$this->pageTitle = t("Merchant Registration");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
				
		$options = array('merchant_enabled_registration',
		'merchant_default_country','merchant_specific_country','pre_configure_size','merchant_enabled_registration_capcha',
		'registration_program','registration_confirm_account_tpl','registration_welcome_tpl',
		'registration_terms_condition','merchant_registration_new_tpl','merchant_registration_welcome_tpl','merchant_plan_expired_tpl',
		'merchant_plan_near_expired_tpl'
		);
		
		if(isset($_POST['AR_option'])){			
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				
				$model->merchant_specific_country = !empty($model->merchant_specific_country)? json_encode($model->merchant_specific_country):'';
				$model->registration_program = !empty($model->registration_program)? json_encode($model->registration_program):'';
				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
			$model->merchant_specific_country = !empty($model->merchant_specific_country)? json_decode(stripslashes($model->merchant_specific_country),true):'';			
			$model->registration_program = !empty($model->registration_program)? json_decode(stripslashes($model->registration_program),true):'';			
		}		
		
		
		$template_list =  CommonUtility::getDataToDropDown("{{templates}}",'template_id','template_name',
		"","ORDER BY template_name ASC");
		
		$program_list = AttributesTools::ListMerchantType();
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_merchant_registration",
		  'params'=>array(  
		   'model'=>$model,		 
		   'status_list'=>(array)AttributesTools::StatusManagement('customer'),
		   'country_list'=>AttributesTools::CountryList(),
		   'template_list'=>$template_list,
		   'program_list'=>$program_list
		  )
		));
	}	
	
	public function actionsearch_settings()
	{
		$this->pageTitle = t("Search");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		/*$options = array('home_search_mode','enabled_advance_search','enabled_share_location','google_default_country',
		'admin_zipcode_searchtype','location_default_country','search_enabled_select_from_map','search_default_country');		*/
		
		$options = array('merchant_specific_country','home_search_mode','search_enabled_select_from_map','search_default_country','location_searchtype','location_default_country');		
		
		if(isset($_POST['AR_option'])){
			
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}

			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				$model->merchant_specific_country = !empty($model->merchant_specific_country)? json_encode($model->merchant_specific_country):'';								
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
			$model->merchant_specific_country = !empty($model->merchant_specific_country)? json_decode(stripslashes($model->merchant_specific_country),true):'';			
		}		
		
		
		$country_list = CommonUtility::getDataToDropDown("{{location_countries}}",'shortcode','country_name',
		"WHERE 1","ORDER BY country_name ASC");
		$all = array('all' => t("All Country") );
		$country_list = $all + $country_list;		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_search_settings",
		  'params'=>array(  
		   'model'=>$model,	
		   'search_type'=>AttributesTools::SearchType(),
		   'location_search'=>AttributesTools::LocationSearchType(),
		   'country_list'=>$country_list
		  )
		));
	}
	
	public function actionbooking_settings()
	{
		$this->pageTitle = t("Booking");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('merchant_tbl_book_enabled','booking_cancel_days','booking_cancel_hours');		
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){												
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
		}		
		
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_booking_settings",
		  'params'=>array(  
		   'model'=>$model,		   
		  )
		));
	}
	
	public function actionsite_others()
	{
		$this->pageTitle = t("Others");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('backend_forgot_password_tpl','allow_return_home','image_resizing','runactions_method');
		
		if(isset($_POST['AR_option'])){

			if(DEMO_MODE){
				$this->render('//tpl/error',array(  
					 'error'=>array(
					   'message'=>t("Modification not available in demo")
					 )
				   ));	
			   return false;
		   }			    

			$model->attributes=$_POST['AR_option'];
			if($model->validate()){												
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
		}		
		
		$template_list =  CommonUtility::getDataToDropDown("{{templates}}",'template_id','template_name',
		"","ORDER BY template_name ASC");
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_others",
		  'params'=>array(  
		   'model'=>$model,		   
		   'template_list'=>$template_list,
		   'runactions_method'=>[
			   'touchUrl'=>t("touchUrl"),
			   'touchUrlExt'=>t("touchUrlExt"),
		   ]
		  )
		));
	}	
	
	public function actionnotifications()
	{
		$this->pageTitle = t("Notifications");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		/*$options = array('noti_new_signup_email','noti_new_signup_sms','noti_receipt_email',
		'noti_receipt_sms','noti_booked_admin_email','order_idle_admin_email','order_cancel_admin_email',
		'order_cancel_admin_sms','order_idle_admin_minutes','merchant_near_expiration_day',
		'admin_enabled_order_notification','admin_enabled_order_notification_sounds'
		);*/
		
		$options = array('admin_enabled_alert','admin_email_alert','admin_mobile_alert');
		
		if(isset($_POST['AR_option'])){
									
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}

			$model->attributes=$_POST['AR_option'];			
			if($model->validate()){												
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
		}
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_notifications",
		  'params'=>array(  
		   'model'=>$model,		   
		  )
		));
	}
	
	public function actionlanguage_settings()
	{
		$this->pageTitle = t("Languages");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('enabled_multiple_translation_new','enabled_language_admin',
		'enabled_language_merchant','enabled_language_front');		
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){												
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
		}
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_language_settings",
		  'params'=>array(  
		   'model'=>$model,		   
		  )
		));
	}
	
	public function actionsocial_settings()
	{
		$this->pageTitle = t("Social Settings");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('fb_flag','fb_app_id','fb_app_secret',
		'google_login_enabled','google_client_id','google_client_secret','google_client_redirect_url');		
		
		if(isset($_POST['AR_option'])){
						
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}

			$model->attributes=$_POST['AR_option'];
			if($model->validate()){												
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
		}
		$model['google_client_redirect_url']=websiteUrl();
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_social",
		  'params'=>array(  
		   'model'=>$model,		   
		  )
		));
	}
	
	public function actioncontact_settings()
	{
		$this->pageTitle = t("Contact Settings");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('enabled_contact_form','contact_email_receiver','contact_field','contact_content');		
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){						

				$model->contact_field = !empty($model->contact_field)? json_encode($model->contact_field):'';
							
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
			$model->contact_field = !empty($model->contact_field)? json_decode(stripslashes($model->contact_field),true):'';			
		}
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_contact",
		  'params'=>array(  
		   'model'=>$model,	
		   'contact_fields'=>AttributesTools::ContactFields()
		  )
		));
	}	
	
	public function actionanalytics_settings()
	{
	    $this->pageTitle = t("Analytics");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		
		$options = array('enabled_fb_pixel','fb_pixel_id','enabled_google_analytics','google_analytics_tracking_id');		
		
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){												
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		if($data = OptionsTools::find($options)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}					
		}
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"site_analytics",
		  'params'=>array(  
		   'model'=>$model,		   
		  )
		));
	}
		
	public function actionapi_access()
	{
		$this->pageTitle = t("API Access");
		CommonUtility::setMenuActive('.admin_site_information',".admin_site_information");
		$jwt_token = AttributesTools::JwtMainTokenID();
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
				
		$options = array($jwt_token);
						
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];			
			if($model->validate()){		
				$payload = [
					'iss'=>Yii::app()->request->getServerName(),
					'sub'=>CommonUtility::generateUIID(),					
					'iat'=>time(),						
				];						
				$jwt = JWT::encode($payload, CRON_KEY, 'HS256');
				$model->website_jwt_token = $jwt;				
				if(OptionsTools::save($options, $model)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else dump($model->getErrors());
		}
		
		$data_found = false;
		if($data = OptionsTools::find($options)){
			$data_found = true;
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}		
		}		
		
		$template_list =  CommonUtility::getDataToDropDown("{{templates}}",'template_id','template_name',
		"","ORDER BY template_name ASC");
		
		$this->render('site_config_tpl',array( 
		  'model'=>$model,
		  'template_name'=>"api_access",
		  'params'=>array(  
		   'model'=>$model,		   
		   'data_found'=>$data_found,
		   'template_list'=>$template_list
		  )
		));
	}		

	public function actiondelete_apikeys()
	{
		$jwt_token = AttributesTools::JwtMainTokenID();
		$model = AR_option::model()->find("option_name=:option_name",[':option_name'=>$jwt_token]);
		if($model){
			$model->delete();
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('admin/api_access'));			
		} else $this->render("error");
	}

}
/*end class*/