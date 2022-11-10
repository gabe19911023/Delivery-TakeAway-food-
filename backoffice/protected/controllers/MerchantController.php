<?php
class MerchantController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
			
		return true;
	}
		
	public function actionaccess_denied()
	{
		$error =array(
		  'code'=>404,
	      'message'=>t(HELPER_ACCESS_DENIED)	    
		);
	    $this->render('//tpl/error',array(
	     'error'=>$error
	    ));
	}
	
	public function actionlogout()
	{
		Yii::app()->merchant->logout(false);		
		$this->redirect(Yii::app()->merchant->loginUrl);		
	}
	
	public function actionIndex()
	{			
		$this->redirect(array(Yii::app()->controller->id.'/dashboard'));		
	}	
	
	public function actiondashboard()
	{				
		$this->pageTitle = t("Dashboard");
		$merchant_type = CMerchants::getMerchantType(Yii::app()->merchant->merchant_id);		
		
		$this->render('dashboard',array(
		  'orders_tab'=>AttributesTools::dashboardOrdersTab(),
		  'item_tab'=>AttributesTools::dashboardItemTab(),
		  'limit'=>5,
		  'months'=>6,
		  'merchant_id'=>Yii::app()->merchant->merchant_id,
		  'ajax_url'=>Yii::app()->createUrl("/apibackend"),
		  'merchant_type'=>$merchant_type,
		));
	}
	
	public function actionprofile()
	{
		$this->pageTitle = t("Profile");
		$id = Yii::app()->merchant->merchant_id;
		$upload_path = CMedia::merchantFolder();
				
		$model = AR_merchant_user::model()->findByPk( Yii::app()->merchant->id );		
		if(!$model){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("There is a problem with the page your viewing.")
			 )
			));
			Yii::app()->end();
		}
		
		if(isset($_POST['AR_merchant_user'])){
			$model->attributes=$_POST['AR_merchant_user'];			
			if($model->validate()){				
								
				/*$model->image=CUploadedFile::getInstance($model,'image');
				if($model->image){											
					$model->profile_photo = CommonUtility::uploadNewFilename($model->image->name);					
					$path = CommonUtility::uploadDestination('')."/".$model->profile_photo;								
					$model->image->saveAs( $path );
					
					Yii::app()->merchant->setState("avatar", $model->profile_photo );
				}*/	

				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->profile_photo = $_POST['photo'];
						Yii::app()->merchant->avatar = $_POST['photo'];
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
		
		$this->render("//admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"//merchant/profile",
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
	
	public function actionchange_password()
	{
		$this->pageTitle = t("Profile");
		$id = Yii::app()->merchant->merchant_id;
		
		$model = AR_merchant_user::model()->findByPk( Yii::app()->merchant->id );		
		if(!$model){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("There is a problem with the page your viewing.")
			 )
			));
			Yii::app()->end();
		}
		
		$model->scenario = 'update_password';
		
		if(isset($_POST['AR_merchant_user'])){
			$model->attributes=$_POST['AR_merchant_user'];
			if($model->validate()){				
								
				if(!empty($model->new_password) && !empty($model->new_password)){					
					$model->password = md5(trim($model->new_password));
				}
												
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t("Password updated"));
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
		
		$this->render("//admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"//merchant/change_password",
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
		$this->pageTitle = t("Change Password");
		
		$model = AR_merchant_user::model()->findByPk( Yii::app()->merchant->id );		
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
		$this->render("//admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"merchant_webpush",
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
	
	public function actionprofile_remove_image()
	{
	    $merchant_id =  (integer)  Yii::app()->merchant->id;					
		$model = AR_merchant_user::model()->findByPk($merchant_id);
		if($model){
			Yii::app()->merchant->setState("avatar",'');
			$model->profile_photo = '';
			$model->save();
			$this->redirect(array(Yii::app()->controller->id.'/profile'));			
		} else $this->render("error");
	}
	
	public function actionedit()
	{
		CommonUtility::setMenuActive('.vendor_list');
		$this->pageTitle = t("Merchant - information");	
		
		$id = Yii::app()->merchant->merchant_id;		
		$model = AR_merchant::model()->findByPk( $id );
		
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}
		
		$model->scenario='information';
		$upload_path = CMedia::merchantFolder();
		
		if(isset($_POST['AR_merchant'])){
		    $model->attributes=$_POST['AR_merchant'];			
		    if($model->validate()){			    	
		    		    	    	    	    
	    	    if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->logo = $_POST['photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->logo = '';
				} else $model->logo = '';
				
				if(isset($_POST['header_image'])){
					if(!empty($_POST['header_image'])){
						$model->header_image = $_POST['header_image'];
						$model->path2 = isset($_POST['path2'])?$_POST['path2']:$upload_path;
					} else $model->header_image = '';
				} else $model->header_image = '';

				if(isset($_POST['footer_image'])){
						if(!empty($_POST['footer_image'])){
							$model->footer_image = $_POST['footer_image'];
							$model->path2 = isset($_POST['path2'])?$_POST['path2']:$upload_path;
						} else $model->footer_image = '';
					} else $model->footer_image = '';
		    	
				if($model->save()){																					
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else {
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}				
			} else{
				// print_r($model->getErrors());
				// exit;
				 Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
			}
		}
		
		if(!isset($_POST['AR_merchant'])){
															
			$model->cuisine2 = MerchantTools::getCuisine($model->merchant_id);
			
			if($services = MerchantTools::getMerchantMeta($model->merchant_id,'services')){
				$model->service2=$services;
			}											
			
			if($featured = MerchantTools::getMerchantMeta($model->merchant_id,'featured')){
				$model->featured=$featured;
			}											
			
			if($tags = MerchantTools::getMerchantOptions($model->merchant_id,'tags')){					
				$model->tags=$tags;
			}											
		}
		
		$model->delivery_distance_covered = Price_Formatter::convertToRaw($model->delivery_distance_covered,0);
		
		$model->restaurant_name = stripslashes($model->restaurant_name);
				
		$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('merchant_logo'));
		
		$nav = array(
		   t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
		   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
		);		
				
		$params_model = array(		
		    'model'=>$model,	
		    'status'=>(array)AttributesTools::StatusManagement('customer'),	    
		    'cuisine'=>(array)AttributesTools::ListSelectCuisine(),
		    'services'=>(array)AttributesTools::ListSelectServices(),
		    'tags'=>(array)AttributesTools::ListSelectTags(),
		    'unit'=>AttributesTools::unit(),	
		    'featured'=>AttributesTools::MerchantFeatured(),
		    'ctr'=>'/merchant',		    
		    'upload_path'=>$upload_path,
		    'links'=>array(
	           t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
		       isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''  
		    ),	    	
		    'show_status'=>false
		);	
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
		   $menu = new WidgetMerchantAttMenu;		   
		   $menu->merchant_type = isset($model->merchant_type)?$model->merchant_type:'';
		   $menu->main_account = Yii::app()->merchant->getState("main_account");
           $menu->init();    
		}
				
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//vendor/merchant_info",
			'widget'=>'WidgetMerchantAttMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu,
			'params_widget'=>array(			   
	           'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
	           'main_account'=>Yii::app()->merchant->getState("main_account")
			)
		));		
	}
	
	public function actiondelete_logo()
	{		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$page = Yii::app()->input->get('page');			
		$model = AR_merchant::model()->findByPk( $id );				
		if($model){		
			$filename = $model->logo;
			$model->logo='';
			$model->save();			
			
			/*DELETE IMAGE FROM UPLOAD FOLDER AND MEDIA*/	
			CommonUtility::deleteMediaFile($filename);
					
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array($page));
		} else $this->render("error");
	}
	
	public function actiondelete_headerbg()
	{		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$page = Yii::app()->input->get('page');			
		$model = AR_merchant::model()->findByPk( $id );				
		if($model){		
			$filename = $model->header_image;
			$model->header_image='';
			$model->save();				
			
			/*DELETE IMAGE FROM UPLOAD FOLDER AND MEDIA*/	
			CommonUtility::deleteMediaFile($filename);
			
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array($page));
		} else $this->render("error");
	}
	
	public function actionlogin()
	{
		CommonUtility::setMenuActive('.merchant','.merchant_edit');
		$this->pageTitle = t("Merchant - login");
		
		$id = (integer)Yii::app()->merchant->merchant_id;		
				
		$model = AR_merchant_user::model()->find("merchant_id=:merchant_id AND main_account=:main_account",array(
		  ':merchant_id'=>$id,
		  ':main_account'=>1
		));		
		
		$main_account =  Yii::app()->merchant->getState("main_account");
		if($main_account<=0){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("This page is not available in your account.")
			 )
			));
			return false;
		}
			
		if($model){			
			
			//$merchant = AR_merchant::model()->findByPk( $id );		
								
			if(isset($_POST['AR_merchant_user'])){				
		        $model->attributes=$_POST['AR_merchant_user'];			    	
			    if($model->validate()){			    
			    					       
			       if(isset($_POST['AR_merchant_user']['new_password'])){
				       if(!empty($_POST['AR_merchant_user']['new_password'])){
					       $model->password = md5($_POST['AR_merchant_user']['new_password']);
					       $model->main_account = 1;
				       }
			       }
			       
			       $model->status = 'active';
			       
			       if($model->save()){			       				       				       	  
			       	
			       	  /*$merchant = AR_merchant::model()->findByPk( $id );
		       	      $merchant->username = $_POST['AR_merchant_user']['username'];
		       	      if(isset($_POST['AR_merchant_user']['password'])){
				         if(!empty($_POST['AR_merchant_user']['password'])){
					         $merchant->password = md5($_POST['AR_merchant_user']['password']);					       
				         }
				      }
		       	      $merchant->save();*/
			       	
			       	  Yii::app()->user->setFlash('success', t(Helper_success) );		
			       	  $this->refresh();			
			       } else {
			       	  Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
			       }
			    }
			}
			
			$model->password='';
						
			$merchant = AR_merchant::model()->findByPk( $id );	
			
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$params_model = array(		
				'model'=>$model,					
				'links'=>array(
				   t("Update Information")=>array(Yii::app()->controller->id.'/login'),		        
				   isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''  
				),	    		   
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantAttMenu;		   
			   $menu->merchant_type = isset($model->merchant_type)?$model->merchant_type:'';
			   $menu->main_account = Yii::app()->merchant->getState("main_account");
			   $menu->init();    
			}
				
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/merchant_login",
				'widget'=>'WidgetMerchantAttMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_type'=>isset($merchant->merchant_type)?$merchant->merchant_type:'',
				   'main_account'=>Yii::app()->merchant->getState("main_account")
				)
			));		
								
		} else {
			
			$models = AR_merchant::model()->findByPk( $id );
			if($models){
			
				$model = new AR_merchant_user;
				$model->merchant_id=$id;
				
				$model->scenario='register';
				
				if(isset($_POST['AR_merchant_user'])){
			        $model->attributes=$_POST['AR_merchant_user'];			    	
				    if($model->validate()){								    	
				       //$model->password = md5($_POST['AR_merchant_user']['password']);
				       $model->password = $_POST['AR_merchant_user']['new_password'];
				       $model->main_account = 1;
				       if($model->save()){		
				       	
				       	  /*$merchant = AR_merchant::model()->findByPk( $id );
			       	      $merchant->username = $_POST['AR_merchant_user']['username'];
			       	      $merchant->password = md5($_POST['AR_merchant_user']['new_password']);
			       	      $merchant->save();*/
			       	  
			       					       		       	
				       	  Yii::app()->user->setFlash('success', t(Helper_success) );		
				       	  $this->redirect(array(Yii::app()->controller->id.'/login','id'=>$model->merchant_id));				       	  
				       } else {
				       	  Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				       }
				    }
				}
								
				$merchant = AR_merchant::model()->findByPk( $model->merchant_id );				
				$avatar='';
				if($merchant){					
					$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
			        CommonUtility::getPlaceholderPhoto('merchant_logo'));
				}

				
				$params_model = array(		
					'model'=>$model,					
					'links'=>array(
					   t("Update Information")=>array(Yii::app()->controller->id.'/login'),		        
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''  
					),	    		   
				);	
				
				$menu = array();
				if(Yii::app()->params['isMobile']==TRUE){
				   $menu = new WidgetMerchantAttMenu;		   
				   $menu->merchant_type = isset($model->merchant_type)?$model->merchant_type:'';
				   $menu->main_account = Yii::app()->merchant->getState("main_account");
				   $menu->init();    
				}
					
				$this->render("//tpl/submenu_tpl",array(		    
					'template_name'=>"//vendor/merchant_login",
					'widget'=>'WidgetMerchantAttMenu',		
					'avatar'=>$avatar,
					'params'=>$params_model,
					'menu'=>$menu,
					'params_widget'=>array(			   
					   'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
					   'main_account'=>Yii::app()->merchant->getState("main_account")
					)
				));			
					
			} else $this->render("error");	
		}
	}	
	
	public function actionaddress()
	{
		CommonUtility::setMenuActive('.merchant','.merchant_edit');
		$this->pageTitle = t("Edit Merchant - Address");
		
		$id = Yii::app()->merchant->merchant_id;
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model->scenario='address';

			if(isset($_POST['AR_merchant'])){
		       $model->attributes=$_POST['AR_merchant'];				       
			    if($model->validate()){						    				    	
			    				    	
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				} else Yii::app()->user->setFlash('error', t(HELPER_CORRECT_FORM) );	
			}		

			$country_list = require_once 'CountryCode.php';
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$params_model = array(		
				'model'=>$model,
				'country' => $country_list,					
				'links'=>array(
				   t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''  
				),	    		   
				'unit'=>AttributesTools::unit(),
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantAttMenu;		   
			   $menu->merchant_type = isset($model->merchant_type)?$model->merchant_type:'';
			   $menu->main_account = Yii::app()->merchant->getState("main_account");
			   $menu->init();    
			}
				
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/address",
				'widget'=>'WidgetMerchantAttMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
				   'main_account'=>Yii::app()->merchant->getState("main_account")
				)
			));		
			
		} else $this->render("error");
	}
	
	public function actionsocial_media()
	{
		CommonUtility::setMenuActive('.merchant','.merchant_edit');
		$this->pageTitle = t("Edit Merchant - Social Media");
		
		$id = Yii::app()->merchant->merchant_id;
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model->scenario='social_media';

			if(isset($_POST['AR_merchant'])){
		       $model->attributes=$_POST['AR_merchant'];				       
			    if($model->validate()){						    				    	
			    				    	
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				} else Yii::app()->user->setFlash('error', t(HELPER_CORRECT_FORM) );	
			}		

			$country_list = require_once 'CountryCode.php';
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$params_model = array(		
				'model'=>$model,
				'country' => $country_list,					
				'links'=>array(
				   t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''  
				),	    		   
				'unit'=>AttributesTools::unit(),
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantAttMenu;		   
			   $menu->merchant_type = isset($model->merchant_type)?$model->merchant_type:'';
			   $menu->main_account = Yii::app()->merchant->getState("main_account");
			   $menu->init();    
			}
				
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/social_media",
				'widget'=>'WidgetMerchantAttMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
				   'main_account'=>Yii::app()->merchant->getState("main_account")
				)
			));		
			
		} else $this->render("error");
	}

	public function actionmembership()
	{		
		CommonUtility::setMenuActive('.merchant','.merchant_edit');
		$this->pageTitle = t("Edit Merchant - Merchant type");
		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model->scenario='membership';

			if(isset($_POST['AR_merchant'])){
		       $model->attributes=$_POST['AR_merchant'];				       
			    if($model->validate()){						    				    	

			    	$model->percent_commision = (float)$model->percent_commision;
			    	
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				}
			}		
			
			$model->percent_commision = number_format( (float) $model->percent_commision,2);
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));

			$params_model = array(		
				'model'=>$model,					
				'links'=>array(
				    t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
			        isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    		   
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantAttMenu;		   
			   $menu->merchant_type = isset($model->merchant_type)?$model->merchant_type:'';
			   $menu->main_account = Yii::app()->merchant->getState("main_account");
			   $menu->init();    
			}
				
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"membership",
				'widget'=>'WidgetMerchantAttMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
				   'main_account'=>Yii::app()->merchant->getState("main_account")
				)
			));		
			
		} else $this->render("error");
	}
	

	public function actionfeatured()
	{
		CommonUtility::setMenuActive('.merchant','.merchant_edit');
		$this->pageTitle = t("Edit Merchant - Featured");
		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model->scenario='featured';

			if(isset($_POST['AR_merchant'])){
		       $model->attributes=$_POST['AR_merchant'];				       
			    if($model->validate()){						    				    	
			    				    	
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				}
			}		
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));

			$params_model = array(		
				'model'=>$model,					
				'links'=>array(
				   t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''  
				),	    		   
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantAttMenu;		   
			   $menu->merchant_type = isset($model->merchant_type)?$model->merchant_type:'';
			   $menu->main_account = Yii::app()->merchant->getState("main_account");
			   $menu->init();    
			}
				
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/featured",
				'widget'=>'WidgetMerchantAttMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
				   'main_account'=>Yii::app()->merchant->getState("main_account")
				)
			));				
			
		} else $this->render("error");
	}
	
	public function actionpayment_history()
	{
		CommonUtility::setMenuActive('.merchant','.merchant_edit');
		$this->pageTitle = t("Merchant - Payment history");
		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			/*$action_name='payment_history';
			$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete");
			
			ScriptUtility::registerScript(array(
			  "var action_name='$action_name';",
			  "var delete_link='$delete_link';",
			),'action_name');*/
			
									
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$nav = array(
			   t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
			   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
			);	
			
			
			$table_col = array(		  			 
			 'created'=>array(
			    'label'=>t("Created"),
			    'width'=>'20%'
			  ),			 
			  'payment_code'=>array(
			    'label'=>t("Payment"),
			    'width'=>'10%'
			  ),		  
			  'invoice_ref_number'=>array(
			    'label'=>t("Invoice #"),
			    'width'=>'20%'
			  ),		  
			  'package_id'=>array(
			    'label'=>t("Plan"),
			    'width'=>'20%'
			  ),		  
			);
			$columns = array(		  			  
			  array('data'=>'created'),			  
			  array('data'=>'payment_code'),		  
			  array('data'=>'invoice_ref_number'),		  
			  array('data'=>'package_id','orderable'=>false),		  
			);		
			
			/*$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/featured",
				'widget'=>'WidgetMerchantAttMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
				   'main_account'=>Yii::app()->merchant->getState("main_account")
				)
			));		*/
			
			$this->render("//tpl/submenu_tpl",array(
			  'template_name'=>"//vendor/payment_history",
			  'widget'=>'WidgetMerchantAttMenu',		
			  'model'=>$model,			  
			  'avatar'=>$avatar,
			  'nav'=>$nav,	
			  'ctr'=>Yii::app()->controller->id,		
			  'params'=>array(
			    'model'=>$model,			    
			    'table_col'=>$table_col,
			    'columns'=>$columns,
			    'order_col'=>1,
	            'sortby'=>'desc', 
	            'merchant_id'=>$id,
	            'ajax_url'=>Yii::app()->createUrl("/apibackend"),	
	            'links'=>array(
				   t("Update Information")=>array(Yii::app()->controller->id.'/edit'),		        
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''  
				),	             
			  ),
			  'params_widget'=>array(			   
				   'merchant_type'=>isset($model->merchant_type)?$model->merchant_type:'',
				   'main_account'=>Yii::app()->merchant->getState("main_account")
				)
		    ));
						
		} else $this->render("error");
	}	
	
	public function actionsettings()
	{		
		$this->pageTitle=t("Basic Settings");
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
			
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$model=new AR_option;
		    $model->scenario=Yii::app()->controller->action->id;		
			
		    $options = array(
			'enabled_private_menu','merchant_two_flavor_option','merchant_tax_number',
			'merchant_extenal','merchant_enabled_voucher',
			'merchant_enabled_tip','merchant_default_tip',
			'merchant_close_store','merchant_disabled_ordering'			
			);
		
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
			
		    if(isset($_POST['AR_option'])){
				$model->attributes=$_POST['AR_option'];

				if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					$this->render('//tpl/error',array(  
						 'error'=>array(
						   'message'=>t("Modification not available in demo")
						 )
					   ));	
				   return false;
			   }			    
			   
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						
						$merchant->close_store = intval($model->merchant_close_store);
						$merchant->disabled_ordering = intval($model->merchant_disabled_ordering);
						$merchant->save();						
						
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
			}
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
			
			$model->merchant_disabled_ordering = $merchant->disabled_ordering;
			$model->merchant_close_store = $merchant->close_store;
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"settings",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),
				   'food_option_listing'=>AttributesTools::foodOptionsListing(),
				   'two_flavor_options'=>AttributesTools::twoFlavorOptions(),
				   'unit'=>AttributesTools::unit(),
				   'tips'=>AttributesTools::Tips(),
				 ),
				 'menu'=>$menu
			));
			
		} else $this->render("error");
	}
	
	public function actionstore_hours()
	{		
		InlineCSTools::registerStoreHours();
		
		$this->pageTitle=t("Store Hours");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');
		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_merchant::model()->findByPk( $id );
		
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$action_name='store_hours';
			$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/store_hours_delete");
			
			ScriptUtility::registerScript(array(
			  "var action_name='$action_name';",
			  "var delete_link='$delete_link';",
			),'action_name');
			
			$menu=array();
			if(Yii::app()->params['isMobile']==TRUE){
				$tpl = '//tpl/lazy_list';
				$menu = new WidgetMerchantSettings;
	            $menu->init();    
			} else $tpl = 'store_hours';
			
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>$tpl,
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			
				   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/store_hours_create"),
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''	            
				   ),
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}
	
	public function actionstore_hours_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Store Hours") : t("Update Store Hours");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');	
		CommonUtility::setSubMenuActive(".merchant-settings",'.store-hours');	
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $merchant_id );
		if($merchant){
			
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$id='';
			
			if($update){
				$id = (integer) Yii::app()->input->get('id');	
				$model = AR_opening_hours::model()->findByPk( $id );				
				if(!$model){				
					$this->render("//admin/error",array(
					 'error'=>array(
					   'message'=>t(HELPER_RECORD_NOT_FOUND)
					 )
					));		
					Yii::app()->end();
				}	
				
			} else {
				$model=new AR_opening_hours;								
			}
			
			$model->mtid = $merchant_id;
			
			if(isset($_POST['AR_opening_hours'])){
				$model->attributes=$_POST['AR_opening_hours'];
				if($model->validate()){
					$model->merchant_id = $merchant_id;					
					$model->status = !empty($model->status)?$model->status:'close';
					if($model->save()){
						if(!$update){
						   $this->redirect(array(Yii::app()->controller->id.'/store_hours'));		
						} else {
							Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
							$this->refresh();
						}
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				}
			}
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"store_hours_create",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,							   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),
				   'days'=>AttributesTools::dayList(),
				 ),
				 'menu'=>$menu		
			));
			
		} else 	$this->render("//admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));		
	}
	
	public function actionstore_hours_update()
	{
		$this->actionstore_hours_create(true);
	}
	
	public function actionstore_hours_delete()
	{
		$id = (integer) Yii::app()->input->get('id');
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;			
		
		$model = AR_opening_hours::model()->find("id=:id AND merchant_id=:merchant_id",array(
		  ':id'=>$id,
		  ':merchant_id'=>$merchant_id
		));		

		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/store_hours'));			
		} else $this->render("error");
	}

	
	public function actiondelivery_times()
	{		
		InlineCSTools::registerDeliveryTimes();
		
		$this->pageTitle=t("Delivery Times");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');
		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_merchant::model()->findByPk( $id );
		
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$action_name='delivery_times';
			$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delivery_times_delete");
			
			ScriptUtility::registerScript(array(
			  "var action_name='$action_name';",
			  "var delete_link='$delete_link';",
			),'action_name');
			
			$menu=array();
			if(Yii::app()->params['isMobile']==TRUE){
				$tpl = '//tpl/lazy_list';
				$menu = new WidgetMerchantSettings;
	            $menu->init();    
			} else $tpl = 'delivery_times';
			
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>$tpl,
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			
				   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/delivery_times_create"),
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''	            
				   ),
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}
	
	public function actiondelivery_times_update()
	{
		$this->actiondelivery_times_create(true);
	}
	
	public function actiondelivery_times_create($update=false)
	{
		$this->pageTitle = $update==false? t("Delivery Times Hours") : t("Update Delivery Hours");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');	
		CommonUtility::setSubMenuActive(".merchant-settings",'.delivery-times');	
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $merchant_id );
		if($merchant){
			
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$id='';
			
			if($update){
				$id = (integer) Yii::app()->input->get('id');	
				$model = AR_delivery_times::model()->findByPk( $id );				
				if(!$model){				
					$this->render("//admin/error",array(
					 'error'=>array(
					   'message'=>t(HELPER_RECORD_NOT_FOUND)
					 )
					));		
					Yii::app()->end();
				}	
				
			} else {
				$model=new AR_delivery_times;								
			}
			
			$model->mtid = $merchant_id;
			
			if(isset($_POST['AR_delivery_times'])){
				$model->attributes=$_POST['AR_delivery_times'];
				if($model->validate()){
					$model->merchant_id = $merchant_id;					
					$model->status = !empty($model->status)?$model->status:'close';
					if($model->save()){
						if(!$update){
						   $this->redirect(array(Yii::app()->controller->id.'/delivery_times'));		
						} else {
							Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
							$this->refresh();
						}
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				}
			}
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"delivery_times_create",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,							   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),
				   'days'=>AttributesTools::dayList(),
				 ),
				 'menu'=>$menu		
			));
			
		} else 	$this->render("//admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));		
	}
	
	public function actiondelivery_times_delete()
	{
		$id = (integer) Yii::app()->input->get('id');
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;			
		
		$model = AR_delivery_times::model()->find("id=:id AND merchant_id=:merchant_id",array(
		  ':id'=>$id,
		  ':merchant_id'=>$merchant_id
		));		

		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/delivery_times'));			
		} else $this->render("error");
	}
	
    public function actiontracking_estimation()
	{		
		$this->pageTitle = t("Tracking initial estimation");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
			
			$avatar = CommonUtility::getPhoto($merchant->logo, CommonUtility::getPlaceholderPhoto('merchant_logo'));			
						
			$model=new AR_option;
		    $model->scenario = 'tracking_estimation';
			
		    $options = array(
			 'tracking_estimation_delivery1','tracking_estimation_delivery2'
			);
				
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
					
		    if(isset($_POST['AR_option'])){
				$model->attributes=$_POST['AR_option'];				
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
			
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"tracking_estimation",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}		
	
	public function actiontaxes()
	{
		$this->pageTitle = t("Taxes");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$merchant_id = intval(Yii::app()->merchant->merchant_id);
		$merchant = AR_merchant::model()->findByPk( $merchant_id );
		
		if(!$merchant){				
			$this->render("//tpl/error",array('error'=>array('message'=>t("merchant not found"))));
			Yii::app()->end();
		}		
				
		$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('merchant_logo'));
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
		   $menu = new WidgetMerchantSettings;
           $menu->init();    
		}
			
		$model = new AR_merchant_meta;
		
	    if(isset($_POST['AR_merchant_meta'])){

			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}

			$post=$_POST['AR_merchant_meta'];								
			AR_merchant_meta::saveMeta($merchant_id,'tax_enabled', isset($post['tax_enabled'])? intval($post['tax_enabled']) :0 );			
			AR_merchant_meta::saveMeta($merchant_id,'tax_on_delivery_fee', isset($post['tax_on_delivery_fee'])? floatval($post['tax_on_delivery_fee']) :0 );						
			AR_merchant_meta::saveMeta($merchant_id,'tax_type', isset($post['tax_type'])? trim($post['tax_type']) :'' );						
			AR_merchant_meta::saveMeta($merchant_id,'tax_service_fee', isset($post['tax_service_fee'])? intval($post['tax_service_fee']) :0 );
			AR_merchant_meta::saveMeta($merchant_id,'tax_packaging', isset($post['tax_packaging'])? intval($post['tax_packaging']) :0 );	
			
			AR_merchant_meta::model()->deleteAll('merchant_id=:merchant_id AND meta_name=:meta_name ',array(
			 ':merchant_id'=> $merchant_id,
			 ':meta_name'=>'tax_for_delivery'
			));
			
			if(isset($post['tax_for_delivery'])){
				if(is_array($post['tax_for_delivery']) && count($post['tax_for_delivery'])>=1){
					foreach ($post['tax_for_delivery'] as $tax_delivery) {
						$models = new AR_merchant_meta;
						$models->merchant_id = $merchant_id;
						$models->meta_name = 'tax_for_delivery';
						$models->meta_value = intval($tax_delivery);
						$models->save();
                    }                    
				}
			}
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
			$this->refresh();
		}
		
		$data = AR_merchant_meta::getMeta($merchant_id,array('tax_enabled','tax_on_delivery_fee','tax_type','tax_service_fee','tax_packaging'));
		$model->tax_enabled = isset($data['tax_enabled'])?$data['tax_enabled']['meta_value']:false;				
		$model->tax_on_delivery_fee = isset($data['tax_on_delivery_fee'])?$data['tax_on_delivery_fee']['meta_value']:false;
		$model->tax_type = isset($data['tax_type'])?$data['tax_type']['meta_value']:'';
		$model->tax_service_fee = isset($data['tax_service_fee'])?$data['tax_service_fee']['meta_value']:false;				
		$model->tax_packaging = isset($data['tax_packaging'])?$data['tax_packaging']['meta_value']:false;				
		$model->tax_for_delivery = CommonUtility::getDataToDropDown("{{merchant_meta}}",'meta_value','meta_value',
		"where merchant_id=".q($merchant_id)." and meta_name='tax_for_delivery' ");		

		$table_col = array(
		  'tax_uuid'=>array(
		    'label'=>t("ID"),
		    'width'=>'10%'
		  ),		  
		  'tax_name'=>array(
		    'label'=>t("Name"),
		    'width'=>'20%'
		  ),
		  'tax_rate'=>array(
		    'label'=>t("Rate"),
		    'width'=>'15%'
		  ),
		  'active'=>array(
		    'label'=>t("Status"),
		    'width'=>'15%'
		  ),
		  'date_created'=>array(
		    'label'=>t("Actions"),
		    'width'=>'15%'
		  ),
		);
		$columns = array(
		  array('data'=>'tax_uuid','visible'=>false),		  
		  array('data'=>'tax_name'),
		  array('data'=>'tax_rate'),
		  array('data'=>'active'),
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_tax_edit normal btn btn-light tool_tips"><i class="zmdi zmdi-border-color"></i></a>			    
			    <a class="ref_tax_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>			    
			 </div>
		     '
		  ),
		);					
		
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$merchant,
			'template_name'=>"tax_settings",
			'widget'=>'WidgetMerchantSettings',		
			'avatar'=>$avatar,			
			'params'=>array(  
			   'model'=>$model,			   
			   'table_col'=>$table_col,
		       'columns'=>$columns,
		       'order_col'=>1,
               'sortby'=>'desc',  
		       'tax_type_list'=>CommonUtility::taxType(),
		       'tax_in_price_list'=>CommonUtility::taxPriceList(),
		       'mutilple_tax_list'=>CommonUtility::getDataToDropDown("{{tax}}",'tax_id','tax_name',"WHERE tax_type='multiple'"),
			   'links'=>array(	
			     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
	             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
			   ),				   
			 ),
			 'menu'=>$menu
		));
	}
	
    public function actionsocial_settings()
	{		
		$this->pageTitle = t("Social Settings");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
									
			$model=new AR_option;
		    $model->scenario = 'social_settings';
			
		    $options = array(
			 'facebook_page','twitter_page','google_page','instagram_page',
			 'merchant_fb_flag','merchant_fb_app_id','merchant_fb_app_secret','merchant_google_login_enabled','merchant_google_client_id','merchant_google_client_secret'
			);
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
				
		    if(isset($_POST['AR_option'])){
				$model->attributes=$_POST['AR_option'];				

				if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					$this->render('//tpl/error',array(  
						 'error'=>array(
						   'message'=>t("Modification not available in demo")
						 )
					   ));	
				   return false;
			   }			

				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
			
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"social_settings",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu
			));
			
		} else $this->render("error");
	}			
	
    public function actionnotification_settings()
	{		
		$this->pageTitle = t("Notification Settings");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$model=new AR_option;
		    $model->scenario = 'social_settings';
			
		    $options = array(
			 'merchant_enabled_alert','merchant_email_alert','merchant_mobile_alert'
			);
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
				
		    if(isset($_POST['AR_option'])){
				$model->attributes=$_POST['AR_option'];				
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"notification_settings",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}			
	
    public function actionorders_settings()
	{		
		$this->pageTitle = t("Orders Settings");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$model=new AR_option;		    
			
		    $options = array(
			 'merchant_order_critical_mins','merchant_order_reject_mins'
			);
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
				
		    if(isset($_POST['AR_option'])){
		    	if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
		    		 $this->render('//tpl/error',array(  
				          'error'=>array(
				            'message'=>t("Modification not available in demo")
				          )
				        ));	
				    return false;
		    	}
				$model->attributes=$_POST['AR_option'];				
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"order_settings",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}				

	public function actioncredit_card()
	{		
		$this->pageTitle=t("Manage Credit Card");
		$action_name='credit_card_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/credit_card_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = 'credit_card_list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/credit_card_create")
		));	
	}
	
	public function actioncredit_card_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Credit Card") : t("Update Credit Card");
		CommonUtility::setMenuActive('.merchant','.merchant_credit_card');			
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_merchant_cards::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}			
						
			$model->expiration = $model->expiration_month."/".$model->expiration_yr;
			
			try {
				$model->credit_card_number = CreditCardWrapper::decryptCard($model->encrypted_card);				
			} catch (Exception $e) {
				//
			}								
			
		} else {			
			$model=new AR_merchant_cards;			
		}

		if(isset($_POST['AR_merchant_cards'])){
			$model->attributes=$_POST['AR_merchant_cards'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/credit_card'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
							
		$this->render("credit_card_create",array(
		    'model'=>$model,	
		    'links'=>array(
	            t("All Credit Card")=>array(Yii::app()->controller->id.'/credit_card'),        
                $this->pageTitle,
		    ),	    		    
		));
	}	
	
	public function actioncredit_card_update()
	{
		$this->actioncredit_card_create(true);
	}
	
	public function actioncredit_card_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$model = AR_merchant_cards::model()->find("mt_id=:mt_id AND merchant_id=:merchant_id",array(
		  ':mt_id'=>$id,
		  ':merchant_id'=>$merchant_id
		));	
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/credit_card'));			
		} else $this->render("error");
	}
	
	public function actionall_order()
	{
		$this->pageTitle=t("All Orders");
		$action_name='order_list_new';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'list_app';
		} else $tpl = 'list_new';
		
		
		InlineCSTools::registerServicesCSS();
		
		$this->render("//order/$tpl",array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create_order")
		));	
	}
	
	public function actionorder_view()
	{
		$this->pageTitle = t("View Order");
		
		CommonUtility::setMenuActive('.merchant_orders','.merchant_all_order');			
		
		$id = Yii::app()->input->get('id');		
		require Yii::getPathOfAlias('frontend')."/models/AR_ordernew.php";
						
		$model = AR_ordernew::model()->cache(Yii::app()->params->cache, 
		CCacheData::dependency() )->find('order_uuid=:order_uuid', 
		array(':order_uuid'=>$id));
		
		if($model){
			$this->pageTitle = t("View Order #[order_id]",array(
			  '[order_id]'=>$model->order_id
			));
			$this->render("//order/view",array(
			  'links'=>array(
	            t("All Orders")=>array(Yii::app()->controller->id.'/all_order'),        
                $this->pageTitle,
		      ),	    	
			));	
		} else {
			$this->render("//tpl/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));		
		}
	}
	
	public function actionorder_edit()
	{
		CommonUtility::setMenuActive('.merchant_orders','.merchant_all_order');			
		
		$id = Yii::app()->input->get('id');		
		require Yii::getPathOfAlias('frontend')."/models/AR_ordernew.php";
						
		$model = AR_ordernew::model()->cache(Yii::app()->params->cache, 
		CCacheData::dependency() )->find('order_uuid=:order_uuid', 
		array(':order_uuid'=>$id));
		
		if($model){
			$this->render("//order/view",array(
			  
			));	
		} else {
			$this->render("//tpl/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));		
		}
	}
	
	public function actionarchive_order()
	{
		$this->pageTitle=t("All Orders");
		$action_name='order_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'list_app';
		} else $tpl = 'list';
		
		$this->render("//order/$tpl",array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create_order")
		));	
	}
	
	public function actiondelete()
	{
		$id = Yii::app()->input->get('id');		
		$model = AR_orders::model()->find('order_id_token=:order_id_token', array(':order_id_token'=>$id));
		if($model){				
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/all_order'));			
		} else $this->render("error");
	}

    public function actionorder_cancel_list()
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
		
		$this->render("//order/$tpl",array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create_order")
		));	
	}	
	
	public function actiontime_management()
	{
		$this->pageTitle=t("Order limit");
		$action_name='time_managment_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/time_mgt_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = 'time_managment_list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/time_management_create")
		));	
	}
	
	public function actiontime_management_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Time Management") : t("Update Time Management");
		CommonUtility::setMenuActive('.merchant','.merchant_time_management');			
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			
			$stmt="
			SELECT id,group_id,transaction_type,start_time,end_time,number_order_allowed,
			order_status,status,
			GROUP_CONCAT(days) as days
			 FROM
			{{order_time_management}}
			WHERE
			merchant_id=:merchant_id
			and group_id =:group_id
			GROUP BY group_id			
			";		
				
			$model = AR_order_time_mgt::model()->findBySql($stmt,array(
			  ':merchant_id'=>$merchant_id,
			  ':group_id'=>$id
			));		
						
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
			
			$day_selected = explode(",",$model->days);
			$model->days_selected = (array) $day_selected;	
			
			if(!empty($model->order_status)){
				if($order_status = json_decode($model->order_status,true)){
				   $model->order_status_selected = $order_status;
				}
			}
					
		} else {			
			$model=new AR_order_time_mgt;			
		}

		if(isset($_POST['AR_order_time_mgt'])){
			$model->attributes=$_POST['AR_order_time_mgt'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/time_management'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
							
		$this->render("time_management_create",array(
		    'model'=>$model,	
		    'services'=>AttributesTools::ListSelectServices(),
		    'days'=>AttributesTools::dayList(),
		    'order_status'=>AttributesTools::StatusList(),
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'links'=>array(
	            t("All Time")=>array(Yii::app()->controller->id.'/time_management'),        
                $this->pageTitle,
		    ),	    		    
		));
	}			
	
	public function actiontime_management_update()
	{
		$this->actiontime_management_create(true);
	}
	
	public function actiontime_mgt_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
						
		$model = AR_order_time_mgt::model()->deleteAll("merchant_id=:merchant_id AND group_id=:group_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':group_id'=>$id
		));
				
		Yii::app()->user->setFlash('success', t("Succesful") );					
		$this->redirect(array(Yii::app()->controller->id.'/time_management'));			
	}
	
	public function actioncoupon()
	{
		$this->pageTitle=t("Coupon list");
		$action_name='coupon_list';
		$delete_link = Yii::app()->CreateUrl("merchant/coupon_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//promo/coupon_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/coupon_create")
		));
	}
	
	public function actioncoupon_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Coupon") : t("Update Coupon");
		CommonUtility::setMenuActive('.promo',".merchant_coupon");
			
		$id='';	$days = AttributesTools::dayList();
		$selected_days = array(); $selected_merchant = array();
		$selected_customer = array();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
						
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_voucher::model()->findByPk( $id );						
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
						
			foreach ($days as $day=>$dayval) {
				if($model[$day]==1){
					$selected_days[]=$day;
				}
			}	
			
			$model->days_available = $selected_days;	
			$selected_merchant = !empty($model->joining_merchant) ? json_decode(stripslashes($model->joining_merchant)): '';			
			$model->apply_to_merchant = $selected_merchant; 
			$selected_merchant = MerchantAR::getSelected($selected_merchant);			
			
			$selected_customer = !empty($model->selected_customer) ? json_decode(stripslashes($model->selected_customer)): '';			
			$model->apply_to_customer = $selected_customer; 
			$selected_customer = CustomerAR::getSelected($selected_customer);			
			
		} else {			
			$model=new AR_voucher;							
		}			

		if(isset($_POST['AR_voucher'])){
			$model->attributes=$_POST['AR_voucher'];
			if($model->validate()){										
				foreach ($days as $day) {
					if(in_array($day,$model->days_available)){
						$model[$day]=1;
					} else $model[$day]=0;
				}				
				
				$model->voucher_owner = 'merchant';
				$model->merchant_id = $merchant_id;
				$model->selected_customer = !empty($model->apply_to_customer) ? json_encode($model->apply_to_customer): '';
								
				$model->amount = (float) $model->amount;			
				$model->min_order = (float) $model->min_order;
				$model->max_number_use = (integer) $model->max_number_use;
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/coupon'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {
				//dump($model);die();
			}
		}
		
		$model->amount = Price_Formatter::convertToRaw($model->amount,2,true);
		$model->min_order = Price_Formatter::convertToRaw($model->min_order,2,true);
		$model->max_number_use = Price_Formatter::convertToRaw($model->max_number_use,0);
		
		if($model->isNewRecord){
			$model->status = 'publish';
		}
						
		$this->render("coupon_create",array(
		    'model'=>$model,
		    'voucher_type'=>array(),		    
		    'coupon_options'=>array(),
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'voucher_type'=>AttributesTools::couponType(),
		    'coupon_options'=>AttributesTools::couponOoptions(),
		    'days'=>$days,		    
		    'selected_customer'=>$selected_customer,
		    'links'=>array(	
		      t("All Coupon")=>array(Yii::app()->controller->id.'/coupon'),		        
              $this->pageTitle
		    ),
		));
	}	
	
	public function actioncoupon_update()
	{
	    $this->actioncoupon_create(true);
	}
		
	public function actioncoupon_delete()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$model = AR_voucher::model()->find("merchant_id=:merchant_id AND merchant_id=:merchant_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':voucher_id'=>$id
		));		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/coupon'));			
		} else $this->render("error");
	}
	
	public function actionoffers()
	{
		$this->pageTitle=t("Offers list");
		$action_name='offer_list';
		$delete_link = Yii::app()->CreateUrl("merchant/offer_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//merchant/offer_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/offer_create")
		));
	}
	
	public function actionoffer_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Offers") : t("Update Offers");
		CommonUtility::setMenuActive('.promo',".merchant_offers");
	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');
			$model = AR_offers::model()->find("merchant_id=:merchant_id AND offers_id=:offers_id",array(
			  ':merchant_id'=>$merchant_id,
			  ':offers_id'=>$id
			));					
			if(!$model){				
				$this->render("//tpl/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));		
				Yii::app()->end();
			}	
			
			if($model->applicable_to){
				$model->applicable_selected = json_decode($model->applicable_to,true);
			}
			
		} else $model=new AR_offers;	
		
		if(isset($_POST['AR_offers'])){
			$model->attributes=$_POST['AR_offers'];
			if($model->validate()){
				
				$model->merchant_id = $merchant_id;				
				
				if($model->save()){
					if(!$update){						
					   $this->redirect(array(Yii::app()->controller->id.'/offers'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$model->status = $model->isNewRecord?'publish':$model->status;	
		$model->offer_percentage = Price_Formatter::convertToRaw($model->offer_percentage,0,true);
		$model->offer_price = Price_Formatter::convertToRaw($model->offer_price,2,true);
				
		$this->render("offers_create",array(
		    'model'=>$model,			    
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'services'=>(array)AttributesTools::ListSelectServices(),
		    'links'=>array(
	            t("All Offers")=>array(Yii::app()->controller->id.'/offers'),        
	            $this->pageTitle,
		    ),	    
		));			
	}
	
	public function actionoffer_update()
	{
		$this->actionoffer_create(true);
	}
	
	public function actionoffer_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
						
		$model = AR_offers::model()->deleteAll("merchant_id=:merchant_id AND offers_id=:offers_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':offers_id'=>$id
		));
				
		Yii::app()->user->setFlash('success', t("Succesful") );					
		$this->redirect(array(Yii::app()->controller->id.'/offers'));	
	}
	
	public function actionpayment_list()
	{		

		if(Yii::app()->merchant->merchant_type==2){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("This page is not available in your account.")
			 )
			));
			return ;
		}
			
		$this->pageTitle=t("Merchant Type");
		$action_name='payment_list';
		$delete_link = Yii::app()->CreateUrl("merchant/payment_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'payment_list_app';
		} else $tpl = 'payment_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/payment_create")
		));
	}
	
    public function actionpayment_create($update=false)
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$this->pageTitle = $update==false? t("Add Gateway") :  t("Update Gateway");
		CommonUtility::setMenuActive('.payment_gateway',".merchant_payment_list");
		
		$multi_language = CommonUtility::MultiLanguage();
		$attr_json = '';
		
		if($update){
			$id =  Yii::app()->input->get('id');	
			$model = AR_payment_gateway_merchant::model()->findByPk( $id );									
			$attr_json = !empty($model->attr_json)?json_decode($model->attr_json,true):'';						
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
			$model->scenario = "update";
		} else {
			$model=new AR_payment_gateway_merchant;	
			$model->scenario = "create";
		}
				
		if(isset($_POST['AR_payment_gateway_merchant'])){
			$model->attributes=$_POST['AR_payment_gateway_merchant'];			
			if($model->validate()){
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array('merchant/payment_update','id'=>$model->payment_uuid));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}	
						
		$this->render("payment_create",array(
		    'model'=>$model,		   		    		    
		    'attr_json'=>$attr_json,
		    'provider'=>AttributesTools::PaymentProviderByMerchant($merchant_id),
		    'status'=>(array)AttributesTools::StatusManagement('gateway'),
		));
	}		
	
	public function actionpayment_update()
	{
		$this->actionpayment_create(true);
	}
	
	public function actionpayment_delete()
	{
		$id = Yii::app()->input->get('id');		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
			
		$model = AR_payment_gateway_merchant::model()->find("payment_uuid=:payment_uuid AND 
		merchant_id=:merchant_id",array(
		  ':payment_uuid'=>$id,
		  ':merchant_id'=>$merchant_id
		));				
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('merchant/payment_list'));			
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
		
		$this->render('//notifications/notifications_all',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',		  
          'ajax_url'=>Yii::app()->createUrl("/apibackend"),
		));
	}

	public function actionbanner()
	{
				
		$this->pageTitle=t("Banner");		
				
		$table_col = array(		  
		  'banner_id'=>array(
		    'label'=>t("ID"),
		    'width'=>'15%'
		  ),		  
		  'photo'=>array(
		    'label'=>t("Banner"),
		    'width'=>'15%'
		  ),
		  'status'=>array(
		    'label'=>t("Status"),
		    'width'=>'20%'
		  ),
		  'title'=>array(
		    'label'=>t("Title"),
		    'width'=>'20%'
		  ),		  
		  'banner_type'=>array(
		    'label'=>t("Type"),
		    'width'=>'20%'
		  ),		  
		  'banner_uuid'=>array(
		    'label'=>t("Actions"),
		    'width'=>'20%'
		  ),
		);
		$columns = array(
			array('data'=>'banner_id', 'visible'=>false),
			array('data'=>'photo'),
			array('data'=>'status'),
			array('data'=>'title'),			
			array('data'=>'banner_type'),			
			array('data'=>null,'orderable'=>false,
			   'defaultContent'=>'
			   <div class="btn-group btn-group-actions" role="group">
				  <a class="ref_edit normal btn btn-light tool_tips"><i class="zmdi zmdi-border-color"></i></a>
				  <a class="ref_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>
			   </div>
			   '
			),	  
		);		
		
		$this->render('//vendor/banner_list',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',		  
          'ajax_url'=>Yii::app()->createUrl("/apibackend"),
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/banner_create")
		));
	}

	public function actionbanner_create($update=false)
	{
		$this->pageTitle=t("Create Banner");
		CommonUtility::setMenuActive('.merchant','.merchant_banner');
		$upload_path = CMedia::merchantFolder();
		$selected_item = array();
		
		$id = Yii::app()->input->get('id');			
		$model = new AR_banner;
		if($update){
			$model = AR_banner::model()->find("banner_uuid=:banner_uuid",array(
			 ':banner_uuid'=>$id
			));
			if(!$model){
				$this->render('//tpl/error',array(
				 'error'=>array(
				   'message'=>t(Helper_not_found)
				 )
				));
				return ;
			}

			$selected_item = CommonUtility::getDataToDropDown("{{item}}",'item_id','item_name',
			"WHERE item_id=".q($model->meta_value2)."");			
		}
		
		if(isset($_POST['AR_banner'])){
			$model->attributes=$_POST['AR_banner'];			

			$model->owner="merchant";
			$model->meta_value1=Yii::app()->merchant->merchant_id;				

			if(isset($_POST['photo'])){
				if(!empty($_POST['photo'])){
					$model->photo = $_POST['photo'];
					$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
				} else $model->photo = '';
			} else $model->photo = '';
			
			if($model->validate()){										
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id."/banner"));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString( $model->getErrors() ));
		}
		
		$this->render('//vendor/banner_create',array(		  
		  'model'=>$model,
		  'status'=>(array)AttributesTools::StatusManagement('post'),
		  'banner_type'=>AttributesTools::BannerType(),
		  'upload_path'=>$upload_path,
		  'items'=>$selected_item,
		  'links'=>array(
			    t("Banner")=>array('merchant/banner'),        
			    $this->pageTitle,
			)
		));
	}
	
	public function actionbanner_update()
	{
		$this->actionbanner_create(true);
	}	

	public function actionbanner_delete()
	{
		$id = Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
				
		$model = AR_banner::model()->find('meta_value1=:meta_value1 AND banner_uuid=:banner_uuid', 
		array(':meta_value1'=>$merchant_id, ':banner_uuid'=>$id ));
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/banner'));			
		} else $this->render("error");
	}

	public function actionpages_list()
	{
		$this->pageTitle=t("Pages list");		
				
		$table_col = array(		  
		  'banner_id'=>array(
		    'label'=>t("#"),
		    'width'=>'10%'
		  ),		  
		  'photo'=>array(
		    'label'=>t("Title"),
		    'width'=>'40%'
		  ),		
		  'banner_uuid'=>array(
		    'label'=>t("Actions"),
		    'width'=>'15%'
		  ),
		);
		$columns = array(
			array('data'=>'page_id'),
			array('data'=>'title'),			
			array('data'=>null,'orderable'=>false,
			   'defaultContent'=>'
			   <div class="btn-group btn-group-actions" role="group">
				  <a class="ref_edit normal btn btn-light tool_tips"><i class="zmdi zmdi-border-color"></i></a>
				  <a class="ref_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>
			   </div>
			   '
			),	  
		);		
		
		$this->render('//vendor/pages_list',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',		  
          'ajax_url'=>Yii::app()->createUrl("/apibackend"),
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/pages_create")
		));
	}

	public function actionpages_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Page") : t("Update Page");
		CommonUtility::setMenuActive('.merchant','.merchant_pages_list');
		
		$id='';
		$multi_language = CommonUtility::MultiLanguage();
		$upload_path = CMedia::merchantFolder();
		$merchant_id = intval(Yii::app()->merchant->merchant_id);		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_pages::model()->find("owner=:owner AND merchant_id=:merchant_id",[
				'owner'=>"merchant",
				'merchant_id'=>$merchant_id
			]);
					
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}				
		} else {			
			$model=new AR_pages;							
		}

		$model->multi_language = $multi_language;
		
		if(isset($_POST['AR_pages'])){
			$model->attributes=$_POST['AR_pages'];
			if($model->validate()){
				$model->slug = CommonUtility::SeoURL($model->slug);
				$model->owner = "merchant";
				$model->merchant_id=Yii::app()->merchant->merchant_id;
								
				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->meta_image = $_POST['photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->meta_image = '';
				} else $model->meta_image = '';
												
				if($model->save()){
					if(!$update){
					   $this->redirect(array('merchant/pages_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {
				//dump($model);die();
			}
		}
		
		$data  = array();
		if($update){
			$translation = AttributesTools::pagesTranslation($id);
			$data['title_translation'] = isset($translation['title'])?$translation['title']:'';
			$data['long_content_translation'] = isset($translation['long_content'])?$translation['long_content']:'';
		}		
		
		$fields[]=array(
		  'name'=>'title_translation',
		  'placeholder'=>"Enter [lang] title here"
		);
		$fields[]=array(
		  'name'=>'long_content_translation',
		  'placeholder'=>"Enter [lang] content here",
		  'type'=>"textarea",
		  'class'=>"summernote"
		);
					
		$this->render("//attributes/pages_create",array(
		    'model'=>$model,
		    'upload_path'=>$upload_path,
		    'links'=>array(
	            t("All Pages")=>array('merchant/pages_list'),        
                $this->pageTitle,
		    ),
		    'status_list'=>(array)AttributesTools::StatusManagement('post'),
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,		    
		));
	}	

	public function actionpage_update()
	{
	    $this->actionpages_create(true);
	}

	public function actionpages_delete()
	{
		$merchant_id = intval(Yii::app()->merchant->merchant_id);
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_pages::model()->find("owner=:owner AND merchant_id=:merchant_id AND page_id=:page_id ",[
			'owner'=>"merchant",
			'merchant_id'=>$merchant_id,
			'page_id'=>$id
		]);
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('merchant/pages_list'));			
		} else $this->render("error");
	}

	public function actionrecaptcha_settings()
	{
		$this->pageTitle = t("Recaptcha Settings");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
									
			$model=new AR_option;
		    $model->scenario = 'recaptcha_settings';
			
		    $options = ['merchant_captcha_enabled','merchant_captcha_site_key','merchant_captcha_secret','merchant_captcha_lang'];
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
				
		    if(isset($_POST['AR_option'])){
				$model->attributes=$_POST['AR_option'];	
				
				if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					$this->render('//tpl/error',array(  
						 'error'=>array(
						   'message'=>t("Modification not available in demo")
						 )
					   ));	
				   return false;
			   }			
			   
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
			
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"//vendor/recaptcha_settings",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu
			));
			
		} else $this->render("error");
	}

	public function actionpages_menu()
	{
		$this->pageTitle=t("Theme menu");
		CommonUtility::setMenuActive('.sales_channel',".theme_changer");
		$this->render("//theme/theme-menu",array(
		   'ajax_url'=>Yii::app()->createUrl("/apibackend"),		   
		   'links'=>array(
				'links'=>array(
				    t("Menu")=>array('merchant/pages_menu'),        				    
				),
				'homeLink'=>false,
				'separator'=>'<span class="separator">
				<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
			)
		));		
	}

    public function actionmap_keys()
	{		
		$this->pageTitle = t("Map API Keys");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$model=new AR_option;		    
			
		    $options = array(
			 'merchant_map_provider','merchant_google_geo_api_key','merchant_google_maps_api_key','merchant_mapbox_access_token'
			);
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
						$model[$name] = CommonUtility::mask(date("Ymjhs"));
					} else $model[$name]=$val;					
				}			
			}
				
		    if(isset($_POST['AR_option'])){
		    	if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
		    		 $this->render('//tpl/error',array(  
				          'error'=>array(
				            'message'=>t("Modification not available in demo")
				          )
				        ));	
				    return false;
		    	}
				$model->attributes=$_POST['AR_option'];				
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"map_keys",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,	
				   'map_provider'=>AttributesTools::mapsProvider(),		   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}			
	
    public function actionlogin_sigup()
	{		
		$this->pageTitle = t("Login & Signup");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$model=new AR_option;		    
			
		    $options = array(
			   'merchant_signup_enabled_verification','merchant_signup_resend_counter','merchant_signup_enabled_terms',
			   'merchant_signup_terms'
			);
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
				
		    if(isset($_POST['AR_option'])){
		    	if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
		    		 $this->render('//tpl/error',array(  
				          'error'=>array(
				            'message'=>t("Modification not available in demo")
				          )
				        ));	
				    return false;
		    	}
				$model->attributes=$_POST['AR_option'];				
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"login_signup",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}					

    public function actionphone_settings()
	{		
		$this->pageTitle = t("Phone Settings");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$model=new AR_option;		    
			
		    $options = array(
			   'merchant_mobilephone_settings_country','merchant_mobilephone_settings_default_country'
			);
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
				
		    if(isset($_POST['AR_option'])){
		    	if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
		    		 $this->render('//tpl/error',array(  
				          'error'=>array(
				            'message'=>t("Modification not available in demo")
				          )
				        ));	
				    return false;
		    	}
				$model->attributes=$_POST['AR_option'];				
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"phone_settings",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,	
				   'country_list'=>AttributesTools::CountryList(),		   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}						

    public function actionsearch_settings()
	{		
		$this->pageTitle = t("Search Mode");
		CommonUtility::setMenuActive('.merchant','.merchant_settings');		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if($merchant){
						
			$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
						
			$model=new AR_option;		    
			
		    $options = array(
			   'merchant_set_default_country'
			);
					
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
				
		    if(isset($_POST['AR_option'])){
		    	if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
		    		 $this->render('//tpl/error',array(  
				          'error'=>array(
				            'message'=>t("Modification not available in demo")
				          )
				        ));	
				    return false;
		    	}
				$model->attributes=$_POST['AR_option'];				
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;					
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} 
			}
						
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantSettings;
	           $menu->init();    
			}
		
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"search_settings",
				'widget'=>'WidgetMerchantSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,	
				   'country_list'=>AttributesTools::CountryList(),		   
				   'links'=>array(	
				     t("Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),				   
				 ),
				 'menu'=>$menu		
			));
			
		} else $this->render("error");
	}							

}
/*end class*/