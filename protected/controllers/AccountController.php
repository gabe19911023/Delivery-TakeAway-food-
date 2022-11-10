<?php
class AccountController extends SiteCommon
{
  
	public function beforeAction($action)
	{				
		if(isset($_GET)){			
			$_GET = Yii::app()->input->xssClean($_GET);			
		}
		return true;
	}
	
	public function actionIndex()
	{
	   if(Yii::app()->user->isGuest){
	   	  $this->redirect(Yii::app()->getBaseUrl(true));		
	   } else $this->redirect(Yii::app()->createUrl("/account/profile"));		
	}
	
	public function actionlogin()
	{				
		$redirect_to = isset($_GET['redirect'])?$_GET['redirect']:'';		
		if(!Yii::app()->user->isGuest){			
			$this->redirect(!empty($redirect_to)?$redirect_to:Yii::app()->getBaseUrl(true)  );		
		}else{
			$cart_uuid = CommonUtility::getCookie("cart_uuid_local");
			if(!empty($cart_uuid) && !empty($redirect_to)){
				$model = AR_clientsignup::model()->find('cart_uuid=:cart_uuid', array(':cart_uuid'=>$cart_uuid)); 
				if(!$model){		    	
			    	$model = new AR_clientsignup;		
			    	$model->cart_uuid = $cart_uuid;	
			    	$model->scenario = 'guest';
			    	$model->status='active';
					$model->merchant_id = 0;					
			    	$model->save();
			    }
			    if(!empty($model)){
			    	$user=AR_customer_login::model()->find('cart_uuid=?',array($cart_uuid));	
			    	$_identity=new CustomerIdentity($user->username,$user->password,$user->merchant_id);
					$_identity->guestAuthenticate($user->client_id);
			    	$duration=3600*24*30;
			    	Yii::app()->user->login($_identity,$duration);
					$place_id = CommonUtility::getCookie(Yii::app()->params->local_id);			
					$address_uuid = CCheckout::saveDeliveryAddress($place_id , Yii::app()->user->id);
					$this->redirect(!empty($redirect_to)?$redirect_to:Yii::app()->getBaseUrl(true)  );
			    }	
			}
		}

		$options = OptionsTools::find(array('signup_enabled_capcha','signup_enabled_capcha','captcha_site_key',
		 'fb_flag','google_login_enabled','fb_app_id','google_client_id','signup_enabled_verification'
		));
		$capcha = isset($options['signup_enabled_capcha'])?$options['signup_enabled_capcha']:'';
        $capcha = $capcha==1?true:false;                
        $captcha_site_key = isset($options['captcha_site_key'])?$options['captcha_site_key']:'';	

        $fb_enabled = isset($options['fb_flag'])?$options['fb_flag']:'';
        $google_enabled = isset($options['google_login_enabled'])?$options['google_login_enabled']:'';
        $fb_app_id = isset($options['fb_app_id'])?$options['fb_app_id']:'';
        $google_client_id = isset($options['google_client_id'])?$options['google_client_id']:''; 
        
        $enabled_verification = isset($options['signup_enabled_verification'])?$options['signup_enabled_verification']:''; 
        $enabled_verification = $enabled_verification==1?true:false;
        
		ScriptUtility::registerScript(array(
			  "var redirect_to='".CJavaScript::quote($redirect_to)."';",					  
			),'redirect_to');	
			        
		$this->render('login',array(
		   'redirect_to'=>$redirect_to,
		   'capcha'=>$capcha,
		   'captcha_site_key'=>$captcha_site_key,
		   'fb_enabled'=>$fb_enabled,
		   'google_enabled'=>$google_enabled,
		   'fb_app_id'=>$fb_app_id,
		   'google_client_id'=>$google_client_id,	
		   'enabled_verification'=>$enabled_verification,
		));
	}	
	
	public function actionlogout()
	{		
		Yii::app()->user->logout(false);		
		$this->redirect(Yii::app()->user->loginUrl);		
	}
		
	public function actionforgot_pass()
	{		
		$redirect_to = isset($_GET['redirect'])?$_GET['redirect']:'';
		$this->render('forgot_pass',array(
		 'redirect_to'=>$redirect_to
		));
	}	
	
	public function actionreset_password()
	{				
		$client_uuid = isset($_GET['token'])?$_GET['token']:'';		
		$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		array(':client_uuid'=>$client_uuid)); 				
		if($model){
			if($model->status=="active" && $model->reset_password_request==1){
				ScriptUtility::registerScript(array(
					  "var _client_id='".CJavaScript::quote($client_uuid)."';",			  
					),'reset_password');
					
				$this->render('reset_password',array(
				  'first_name'=>Yii::app()->input->xssClean($model->first_name)
				));
			} else $this->render("//store/404-page");
		} else $this->render("//store/404-page");
	}
	
	public function actionsignup()
	{		
		
		$redirect_to = isset($_GET['redirect'])?$_GET['redirect']:'';		
		$next_url = Yii::app()->createAbsoluteUrl("/account/login");
				
		$options = OptionsTools::find(array('signup_type','signup_enabled_capcha','signup_enabled_terms',
		'signup_terms','signup_resend_counter','captcha_site_key','fb_app_id','google_client_id',
		'mobilephone_settings_country','mobilephone_settings_default_country'
		));
				
        $signup_type = isset($options['signup_type'])?$options['signup_type']:'';
        $capcha = isset($options['signup_enabled_capcha'])?$options['signup_enabled_capcha']:'';
        $capcha = $capcha==1?true:false;                
        $captcha_site_key = isset($options['captcha_site_key'])?$options['captcha_site_key']:'';
                
        $enabled_terms = isset($options['signup_enabled_terms'])?$options['signup_enabled_terms']:'';
        $signup_terms = isset($options['signup_terms'])?$options['signup_terms']:'';
        $resend_counter = isset($options['signup_resend_counter'])?intval($options['signup_resend_counter']):40;
        if($resend_counter<=0){
        	$resend_counter = 40;
        }
        
        $phone_default_country = isset($options['mobilephone_settings_default_country'])?$options['mobilephone_settings_default_country']:'us';
        $phone_country_list = isset($options['mobilephone_settings_country'])?$options['mobilephone_settings_country']:'';
        $phone_country_list = !empty($phone_country_list)?json_decode($phone_country_list,true):array();        
                
		ScriptUtility::registerScript(array(
			  "var redirect_to='".CJavaScript::quote($redirect_to)."';",
			  "var next_url='".CJavaScript::quote($next_url)."';",				
			  "var _capcha='".CJavaScript::quote($capcha)."';",	
			  "var _resend_counter='".CJavaScript::quote($resend_counter)."';",				  
			),'redirect_to');
									
		$tpl_use = $signup_type=="mobile_phone"?'signup-less':'signup';		
		
		$this->render($tpl_use,array(
		  'redirect_to'=>$redirect_to,			  
		  'capcha'=>$capcha,
		  'captcha_site_key'=>$captcha_site_key,
		  'enabled_terms'=>$enabled_terms,
		  'signup_terms'=>$signup_terms,	
		  'phone_country_list'=>$phone_country_list,
		  'phone_default_country'=>$phone_default_country
		));
	}	
	
	public function actionverify()
	{
		$redirect_to = Yii::app()->input->get('redirect_to');
		$uuid = Yii::app()->input->get('uuid');
				
		$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		array(':client_uuid'=>$uuid)); 
		
		if($model){					
			if($model->account_verified==11 && $model->status=="active"){
				$this->render("//store/404-page");
			} else {				
				
				$options = OptionsTools::find(array('signup_resend_counter'));
				$resend_counter = isset($options['signup_resend_counter'])?intval($options['signup_resend_counter']):40;				
				
				ScriptUtility::registerScript(array(
				  "var _uuid='".CJavaScript::quote($uuid)."';",		
				  "var _redirect_to='".CJavaScript::quote($redirect_to)."';",
				  "var _steps='".CJavaScript::quote(1)."';",	
				  "var _resend_counter='".CJavaScript::quote($resend_counter)."';",		
				),'verify');	
				
				$this->render('account-verify',array(
				  'redirect_to'=>$redirect_to,
				  'uuid'=>$uuid,
				  'email_address'=>$model->email_address,			
				));
			}
		} else $this->render("//store/404-page");
	}
	
	public function actionverification()
	{
		$redirect_to = isset($_GET['redirect_to'])?$_GET['redirect_to']:'';
		$uuid = isset($_GET['uuid'])?$_GET['uuid']:'';
				
		$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		array(':client_uuid'=>$uuid)); 
		
		if($model){
			
			$options = OptionsTools::find(array('signup_resend_counter','mobilephone_settings_country','mobilephone_settings_default_country'));
		    $resend_counter = isset($options['signup_resend_counter'])?intval($options['signup_resend_counter']):40;
			$phone_default_country = isset($options['mobilephone_settings_default_country'])?$options['mobilephone_settings_default_country']:'us';
	        $phone_country_list = isset($options['mobilephone_settings_country'])?$options['mobilephone_settings_country']:'';
	        $phone_country_list = !empty($phone_country_list)?json_decode($phone_country_list,true):array();        
	        
			ScriptUtility::registerScript(array(
			  "var _uuid='".CJavaScript::quote($uuid)."';",		
			  "var _redirect_to='".CJavaScript::quote($redirect_to)."';",
			  "var _steps='".CJavaScript::quote(1)."';",	
			  "var _resend_counter='".CJavaScript::quote($resend_counter)."';",		
			),'verification');	
			
			$this->render('account-verification',array(
			  'redirect_to'=>$redirect_to,
			  'uuid'=>$uuid,
			  'email_address'=>$model->email_address,	
			  'phone_default_country'=>$phone_default_country,
			  'phone_country_list'=>$phone_country_list
			));
		} else $this->render("//store/404-page");
	}
	
	public function actioncomplete_registration()
	{
		$redirect_to = isset($_GET['redirect_to'])?$_GET['redirect_to']:'';
		$uuid = isset($_GET['uuid'])?$_GET['uuid']:'';
				
		$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		array(':client_uuid'=>$uuid)); 
		
		if($model){
			
			ScriptUtility::registerScript(array(
			  "var _uuid='".CJavaScript::quote($uuid)."';",		
			  "var _redirect_to='".CJavaScript::quote($redirect_to)."';",
			  "var _steps='".CJavaScript::quote(2)."';",		
			),'complete_registration');	
			
			$options = OptionsTools::find(array('mobilephone_settings_country','mobilephone_settings_default_country'));						
			$phone_default_country = isset($options['mobilephone_settings_default_country'])?$options['mobilephone_settings_default_country']:'us';
	        $phone_country_list = isset($options['mobilephone_settings_country'])?$options['mobilephone_settings_country']:'';
	        $phone_country_list = !empty($phone_country_list)?json_decode($phone_country_list,true):array();        
	        		
			$this->render('account-verification',array(
			  'redirect_to'=>$redirect_to,
			  'uuid'=>$uuid,
			  'email_address'=>$model->email_address,
			  'phone_country_list'=>$phone_country_list,
		      'phone_default_country'=>$phone_default_country
			));
		} else $this->render("//store/404-page");
	}
	
	public function actioncheckout()
	{
		$this->layout = 'checkout_layout';	
				
		$maps = CMaps::config();		
		$provider = isset($maps['provider'])?$maps['provider']:'';
		$key = isset($maps['key'])?$maps['key']:'';			
		ScriptUtility::registerJS(array(
		  '//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key='.$key
		),CClientScript::POS_HEAD);		
				
		$payments = array(); $payments_credentials = array(); $merchant_id = 0; $merchant_uuid='';
			
		try {
			$cart_uuid = CommonUtility::getCookie("cart_uuid_local");			
			$merchant_id = CCart::getMerchantId($cart_uuid);
			$merchants = CMerchantListingV1::getMerchant( $merchant_id );			
			$merchant_uuid = isset($merchants['merchant_uuid'])?$merchants['merchant_uuid']:'';
			if($payments = CPayments::PaymentList($merchant_id)){	
				$payments_credentials = CPayments::getPaymentCredentials($merchant_id,'',$merchants->merchant_type);
				CComponentsManager::RegisterBundle($payments);
			}
		} catch (Exception $e) {
		    //
		}	
					
		$payload = array(
		   'items','merchant_info','service_fee',
		   'delivery_fee','packaging','tax','tips','checkout','discount','distance',
		   'summary','total','items_count'
	    );						
	    	   	    	  
		ScriptUtility::registerScript(array(
		  "var is_checkout='".CJavaScript::quote(1)."';",
		  "var payload='".CJavaScript::quote(json_encode($payload))."';",			  
		  "var merchant_id='".CJavaScript::quote($merchant_id)."';",
		  "var merchant_uuid='".CJavaScript::quote($merchant_uuid)."';",		
		),'is_checkout');

		$options = OptionsTools::find(array('mobilephone_settings_default_country','mobilephone_settings_country'));
		$phone_default_country = isset($options['mobilephone_settings_default_country'])?$options['mobilephone_settings_default_country']:'us';
        $phone_country_list = isset($options['mobilephone_settings_country'])?$options['mobilephone_settings_country']:'';
        $phone_country_list = !empty($phone_country_list)?json_decode($phone_country_list,true):array();        
		
		$shortcode = CCheckout::getPhoneCodeByUserID(Yii::app()->user->id);
		if($shortcode){
			$phone_default_country = $shortcode;
		}
		if($merchant_id == 0){
			$url = Yii::app()->user->hasState('rest_slug_name') ? Yii::app()->getUrlManager()->getBaseUrl().'/'.Yii::app()->user->getState('rest_slug_name') : Yii::app()->homeUrl;
			$this->redirect($url);
		}
		$client = AR_client::model()->find('client_id=:client_id', 
		   array(':client_id'=>Yii::app()->user->id));  
		$this->render("checkout",array(
		  'payments'=>$payments,
		  'payments_credentials'=>$payments_credentials,
		  'merchant_id'=>$merchant_id,
		  'phone_country_list'=>$phone_country_list,
		  'phone_default_country'=>$phone_default_country,
		  'client'	  => $client
		));
	}
	
	public function actionprofile()
	{		
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		
		$message='';
		$model = AR_client::model()->findByPk( Yii::app()->user->id );	
		if($model){
		   $message = t("We sent a code to {{email_address}}.",array(
             '{{email_address}}'=> CommonUtility::maskEmail($model->email_address)
           ));           		    			          
		}
		
		ScriptUtility::registerScript(array(			   
		   "var _message='".CJavaScript::quote($message)."';",
		),'manage_account');		           
	
		$avatar = Yii::app()->user->avatar;
		
		$options = OptionsTools::find(array('mobilephone_settings_country','mobilephone_settings_default_country'));
		
		$phone_default_country = isset($options['mobilephone_settings_default_country'])?$options['mobilephone_settings_default_country']:'us';
        $phone_country_list = isset($options['mobilephone_settings_country'])?$options['mobilephone_settings_country']:'';
        $phone_country_list = !empty($phone_country_list)?json_decode($phone_country_list,true):array();        

		$shortcode = CCheckout::getPhoneCodeByUserID(Yii::app()->user->id);
		if($shortcode){
			$phone_default_country = $shortcode;
		}
		
		$this->render('my-profile',array(			  
		  'avatar'=>$avatar,
		  'phone_country_list'=>$phone_country_list,
		  'phone_default_country'=>$phone_default_country,
		  'model'=>$model,
		  'menu'=>WidgetUserProfile::CustomMenu()
		));
	}
	
	public function actionchange_password()
	{
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$avatar = Yii::app()->user->avatar;
		
		$model = AR_client::model()->findByPk( Yii::app()->user->id );	
		
		$phone_default_country = isset($options['mobilephone_settings_default_country'])?$options['mobilephone_settings_default_country']:'us';
        $phone_country_list = isset($options['mobilephone_settings_country'])?$options['mobilephone_settings_country']:'';
        $phone_country_list = !empty($phone_country_list)?json_decode($phone_country_list,true):array();        

		$this->render('change-password',array(			  
			'avatar'=>$avatar,
			'phone_country_list'=>$phone_country_list,
			'phone_default_country'=>$phone_default_country,
			'model'=>$model,
			'menu'=>WidgetUserProfile::CustomMenu()
		));		
	}
	
	public function actionmanage_account()
	{			
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$model = AR_client::model()->findByPk( Yii::app()->user->id );		
		if($model){			
			
			$message = t("We sent a code to {{email_address}}.",array(
			             '{{email_address}}'=> CommonUtility::maskEmail($model->email_address)
			           ));
		    
			ScriptUtility::registerScript(array(
			   "var _phone_prefix='".CJavaScript::quote($model->phone_prefix)."';",
			   "var _contact_phone='".CJavaScript::quote($model->contact_phone)."';",
			   "var _message='".CJavaScript::quote($message)."';",
			),'manage_account');	
		
			
			$avatar = Yii::app()->user->avatar;
			$this->render('manage-account',array(			  
			  'avatar'=>$avatar,
			  'model'=>$model,
			  'menu'=>WidgetUserProfile::CustomMenu()	  
			));			
		} else $this->redirect(Yii::app()->createUrl("/account/login"));
	}
	
	public function actionnotifications()
	{
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$model = AR_client::model()->findByPk( Yii::app()->user->id );		
		
		if($model){				

			$settings = AR_admin_meta::getMeta(array('webpush_provider','pusher_instance_id','webpush_app_enabled'));			
		    $webpush_provider = isset($settings['webpush_provider'])?$settings['webpush_provider']['meta_value']:'';
		    $pusher_instance_id = isset($settings['pusher_instance_id'])?$settings['pusher_instance_id']['meta_value']:'';
		    $webpush_app_enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';
		    $webpush_app_enabled = $webpush_app_enabled==1?true:false;
		    		    
		    $iterest_list = array();
		    try {
		       $iterest_list = CNotificationData::interestListing('communication_client');
		    } catch (Exception $e) {
		       //echo $e->getMessage();
		    }
		    		    
			$avatar = Yii::app()->user->avatar;
			$this->render('account-notifications',array(			  
			   'avatar'=>$avatar,
			   'model'=>$model,			   
			   'iterest_list'=>(array)$iterest_list,
			   'pusher_instance_id'=>$pusher_instance_id,
			   'webpush_provider'=>$webpush_provider,	
			   'webpush_app_enabled'=>$webpush_app_enabled,
			   'menu'=>WidgetUserProfile::CustomMenu()
			));			
		}
	}
	
    public function actionorders()
	{
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('my-orders');
	}	
	
	public function actionfavourites()
	{
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('my-favourites');
	}
	
	public function actionaddresses()
	{
		AssetsFrontBundle::includeMaps();
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('my-addresses');
	}
	
	public function actionbooking()
	{
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('my-booking');
	}
	
	public function actionpayments()
	{
		try {
			$payments = CPayments::DefaultPaymentList();			
			CComponentsManager::RegisterBundle($payments);
		} catch (Exception $e) {
		    //echo $e->getMessage();
		}	
		
		try {
			$payments_credentials = CPayments::getPaymentCredentials(0,'',2);
		} catch (Exception $e) {
		    //echo $e->getMessage();
		}	
						
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('my-payments',array(
		  'payments'=>$payments,
		  'payments_credentials'=>$payments_credentials,
		));
	}
	
	public function actionpoints()
	{
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('my-points');
	}
	
	public function actionvouchers()
	{
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('my-vouchers');
	}
	
	public function actioncuisine()
	{
		$this->render('cuisine');
	}
	
	public function actionnotificationslist()
	{		
		
		AssetsFrontBundle::includeMaps();
		$this->addBodyClasses("column2-layout");
		$this->layout = 'column2';
		$this->render('notifications_list');
	}
		
}
/*end class*/