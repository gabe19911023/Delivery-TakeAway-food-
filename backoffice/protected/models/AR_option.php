<?php
class AR_option extends CActiveRecord
{	
	   		
	public $image,$image2,$website_title,$website_logo,$mobilelogo;
		
	public $map_provider,$google_geo_api_key,$google_maps_api_key,$mapbox_access_token;	
	
	public $captcha_site_key,$captcha_secret,$captcha_lang,$captcha_customer_signup,
	$captcha_merchant_signup,$captcha_customer_login,$captcha_merchant_login,$captcha_admin_login,
	$captcha_order,$captcha_driver_signup,$captcha_contact_form;
	
	public $admin_printing_receipt_width,$admin_printing_receipt_size,$website_enabled_rcpt,$website_receipt_logo;
	
	public $signup_verification,$signup_verification_type,$blocked_email_add,
	$blocked_mobile,$website_terms_customer,$website_terms_customer_url;
	
	public $website_review_type,$review_baseon_status,$earn_points_review_status,$publish_review_status,
	$website_reviews_actual_purchase,$merchant_can_edit_reviews;
	
	public $website_merchant_mutiple_login,$website_admin_mutiple_login;
	
	public $website_timezone_new,$website_date_format_new,$website_time_format_new,$website_time_picker_interval;
	
	public $disabled_website_ordering,$website_hide_foodprice,$website_disbaled_auto_cart,
	$website_disabled_cart_validation,$enabled_merchant_check_closing_time,$disabled_order_confirm_page,
	$restrict_order_by_status,$enabled_map_selection_delivery,$admin_service_fee,$admin_service_fee_applytax;
	
	public $admin_country_set,$website_address,$website_contact_phone,$website_contact_email;
	
	public $admin_currency_set,$admin_currency_position,$admin_decimal_place,$admin_thousand_separator,$admin_decimal_separator;
	
	public $admin_menu_allowed_merchant,$admin_menu_lazyload,$mobile2_hide_empty_category,$admin_activated_menu,$enabled_food_search_menu;
	
	public $merchant_enabled_registration,$merchant_sigup_status,$merchant_default_country,
	$merchant_specific_country,$merchant_email_verification,$pre_configure_size;
	
	public $home_search_mode,$enabled_advance_search,$enabled_share_location,$google_default_country,$admin_zipcode_searchtype,$location_default_country;
	
	public $merchant_tbl_book_enabled,$booking_cancel_days,$booking_cancel_hours;
	
	public $website_enabled_guest_checkout,$enabled_cc_management,$enabled_featured_merchant,$enabled_subscription;
	
	public $cancel_order_enabled,$cancel_order_days_applied,$cancel_order_hours,$cancel_order_status_accepted,$website_review_approved_status;
	
	public $noti_new_signup_email,$noti_new_signup_sms,$noti_receipt_email,$noti_receipt_sms,$noti_booked_admin_email,$order_idle_admin_email,
	$order_cancel_admin_email,$order_cancel_admin_sms,$order_idle_admin_minutes,
	$merchant_near_expiration_day,$admin_enabled_order_notification,$admin_enabled_order_notification_sounds;
	
	public $enabled_multiple_translation_new,$enabled_language_admin,$enabled_language_merchant,$enabled_language_front;
	
	public $fb_flag,$fb_app_id,$fb_app_secret,$google_login_enabled,$google_client_id,$google_client_secret,$google_client_redirect_url,
	$enabled_contact_form,$contact_email_receiver,$contact_field,$contact_content,$admin_header_codes,
	$enabled_fb_pixel,$fb_pixel_id,$enabled_google_analytics,$google_analytics_tracking_id;
	
	/*MERCHANT SETTINGS*/
	public $food_option_not_available,$enabled_private_menu,$merchant_two_flavor_option,$merchant_tax_number,
	$merchant_extenal,$merchant_enabled_voucher,$merchant_required_delivery_time,$merchant_packaging_wise,
	$merchant_packaging_charge,$merchant_packaging_increment, $merchant_tax, $merchant_apply_tax,$merchant_delivery_charges,
	$merchant_no_tax_delivery_charges, $merchant_opt_contact_delivery,$merchant_delivery_estimation,$merchant_delivery_miles,$merchant_distance_type,
	$merchant_enabled_tip,$merchant_default_tip,$merchant_close_store,$merchant_show_time,$merchant_disabled_ordering
	;
	
	public $tracking_estimation_delivery1,$tracking_estimation_delivery2,$tracking_estimation_pickup1,$tracking_estimation_pickup2,
	$tracking_estimation_dinein1,$tracking_estimation_dinein2;
	
	/*BOOKING SETTINGS*/
	public $enabled_merchant_table_booking,$accept_booking_sameday,$fully_booked_msg,$enabled_merchant_booking_alert,
	$merchant_booking_receiver,$merchant_delivery_estimation_inminutes, $merchant_delivery_estimation_min1, $merchant_delivery_estimation_min2;
	
	public $merchant_delivery_charges_type,$merchant_maximum_order,$merchant_minimum_order,
	$merchant_delivery_fee_priority , $merchant_delivery_fee_no_rush,
	$merchant_minimum_order_pickup,$merchant_maximum_order_pickup,$merchant_minimum_order_dinein,$merchant_maximum_order_dinein,
	$sms_notify_number,$facebook_page,$twitter_page,$google_page,$merchant_enabled_alert,$merchant_email_alert,$merchant_mobile_alert,
	$order_verification,$order_sms_code_waiting,$free_delivery_above_price,$merchant_pickup_estimation,$free_delivery_on_first_order,
	$merchant_service_fee,$merchant_service_fee_applytax
	;
	
	public $admin_enabled_alert, $admin_email_alert, $admin_mobile_alert;
	
	public $signup_type, $signup_enabled_verification, $signup_enabled_terms,$signup_terms,$signup_enabled_capcha,
	$signup_welcome_tpl,$signup_verification_tpl,$signup_resetpass_tpl, $signup_resend_counter,$signupnew_tpl,
	$enabled_website_ordering , $merchant_reg_verification, $merchant_reg_admin_approval,
	$search_enabled_select_from_map ,$search_default_country, $location_searchtype, $review_template_id,
	$review_send_after,$review_template_enabled , $review_image_resize_width
	;
	
	public $realtime_provider, $pusher_app_id, $pusher_key, $pusher_secret, $pusher_cluster,
	$merchant_enabled_registration_capcha, $registration_membeship, $registration_commission,
	$registration_confirm_account_tpl, $registration_welcome_tpl, $registration_program,
	$registration_terms_condition , $merchant_registration_new_tpl, $merchant_registration_welcome_tpl,
	$merchant_plan_expired_tpl,$merchant_plan_near_expired_tpl, $merchant_order_critical_mins, $merchant_order_reject_mins,
	$mobilephone_settings_country,$mobilephone_settings_default_country ,$capcha_admin_login_enabled, $capcha_merchant_login_enabled,
	$enabled_language_bar, $default_language, $enabled_language_bar_front,
	$backend_forgot_password_tpl, $allow_return_home, $image_resizing
	;

	public $merchant_fb_flag,$merchant_fb_app_id,$merchant_fb_app_secret,$merchant_google_login_enabled,
	$merchant_google_client_id,$merchant_google_client_secret;

	public $merchant_captcha_enabled,$merchant_captcha_site_key,$merchant_captcha_secret,$merchant_captcha_lang;

	public $merchant_map_provider,$merchant_google_geo_api_key,$merchant_google_maps_api_key,$merchant_mapbox_access_token;

	public $merchant_signup_enabled_verification,$merchant_signup_resend_counter,$merchant_signup_enabled_terms,$merchant_signup_terms,
	$merchant_mobilephone_settings_country,$merchant_mobilephone_settings_default_country, $merchant_set_default_country,$instagram_page;

	public $website_jwt_token,$runactions_method;
		
	/**
	 * Returns the static model of the specified AR class.
	 * @return static the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{option}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(		    
		    'website_logo'=>t("Desktop Website Logo"),
		    'mobilelogo'=>t("Mobile Website Logo"),
		    'google_geo_api_key'=>t("Geocoding API Key"),
		    'google_maps_api_key'=>t("Google Maps JavaScript API"),
		    'admin_printing_receipt_width'=>t("Receipt Width"),
		    'admin_printing_receipt_size'=>t("Font size"),
		    'website_receipt_logo'=>t("Receipt Logo"),
		    'website_terms_customer_url'=>t("Terms and conditions Link"),
		    'website_time_picker_interval'=>t("Time interval"),
		    'admin_service_fee'=>t("Service fee"),
		    'admin_decimal_place'=>t("Decimals"),
		    'admin_decimal_separator'=>t("Decimals"),
		    'admin_thousand_separator'=>t("Thousand Separator"),
		    'booking_cancel_days'=>t("after how many days after booking"),
		    'booking_cancel_hours'=>t("after how many hours"),
		    'cancel_order_days_applied'=>t("after how many days of purchase"),
		    'cancel_order_hours'=>t("after how many hours"),
		    'noti_new_signup_email'=>t("Merchant new signup email"),
		    'noti_new_signup_sms'=>t("Merchant new signup SMS"),
		    'noti_receipt_email'=>t("Receipt Email send to admin"),
		    'noti_receipt_sms'=>t("Receipt SMS send to admin"),
		    'noti_booked_admin_email'=>t("New Table Booking email"),
		    'order_idle_admin_email'=>t("Order IDLE email"),
		    'order_cancel_admin_email'=>t("Order Cancel Request email"),
		    'order_cancel_admin_sms'=>t("Order Cancel Request sms"),
		    'order_idle_admin_minutes'=>t("Order IDLE minutes"),
		    'merchant_near_expiration_day'=>t("Number of days before expiration"),
		    'fb_app_id'=>t("App ID"),
		    'fb_app_secret'=>t("App Secret"),
		    'google_client_id'=>t("Client ID"),
		    'google_client_secret'=>t("Client Secret"),
		    'google_client_redirect_url'=>t("Redirect URL"),
		    'contact_email_receiver'=>t("Receiver Email Address"),
		    'fb_pixel_id'=>t("Facebook Pixel ID"),
		    'google_analytics_tracking_id'=>t("Tracking ID"),
		    'merchant_tax_number'=>t("Tax number"),
		    'merchant_extenal'=>t("Website address"),
		    'merchant_packaging_charge'=>t("Packaging Fee"),
		    'merchant_tax'=>t("Tax"),
		    'merchant_delivery_charges'=>t("Standard Delivery Fee"),	
		    'merchant_delivery_fee_priority'=>t("Priority Delivery Fee"),	
		    'merchant_delivery_fee_no_rush'=>t("No Rush Delivery Fee"),	
		    'merchant_delivery_estimation'=>t("Delivery Estimation"),
		    'merchant_delivery_miles'=>t("Delivery Distance Covered"),
		    'tracking_estimation_delivery1'=>t("From"),
		    'tracking_estimation_delivery2'=>t("To"),
		    'merchant_delivery_estimation_inminutes'=>t("Delivery Estimation Minutes"),
		    'merchant_minimum_order'=>t("Minimum purchase amount"),
		    'merchant_maximum_order'=>t("Maximum purchase amount"),
		    
		    'merchant_minimum_order_pickup'=>t("Minimum purchase amount"),
		    'merchant_maximum_order_pickup'=>t("Maximum purchase amount"),
		    
		    'merchant_minimum_order_dinein'=>t("Minimum purchase amount"),
		    'merchant_maximum_order_dinein'=>t("Maximum purchase amount"),
		    'facebook_page'=>t("Facebook Page"),
		    'twitter_page'=>t("Twitter Page"),
		    'google_page'=>t("Google Page"),
		    'merchant_enabled_alert'=>t("Enabled Notifications"),
		    'merchant_email_alert'=>t("Email address"),
		    'merchant_enabled_alert'=>t("Mobile Number"),
		    'merchant_booking_receiver'=>t("Email address"),
		    'order_sms_code_waiting'=>t("Request code in minutes default is 5mins"),
		    'free_delivery_above_price'=>t("Free Delivery if Sub-Total Greater Or Equal To"),
		    'merchant_pickup_estimation'=>t("Standard estimate time for pickup"),
		    'merchant_service_fee'=>t("Service Fee"),
		    'merchant_delivery_estimation_min1'=> t('Minutes'),
		    'merchant_delivery_estimation_min2'=> t('Minutes'),
		    
		    'admin_email_alert'=> t('Email address'),
		    'admin_mobile_alert'=> t('Mobile number'),
		    'signup_resend_counter'=> t('Resend code interval'),
		    'review_send_after'=>t("Send after how many days"),
		    'review_image_resize_width'=>t("Resize image width"),
		    
		    'pusher_app_id'=>t("App ID"),
		    'pusher_key'=>t("Key"),
		    'pusher_secret'=>t("Secret"),
		    'pusher_cluster'=>t("Cluster"),
		    
		    'merchant_order_critical_mins'=>t("Critical minutes"),
		    'merchant_order_reject_mins'=>t("Reject order minutes"),
		    'mapbox_access_token'=>t("Mapbox Access Token"),
		    'captcha_site_key'=>t("Captcha Site Key"),
		    'captcha_secret'=>t("Captcha Secret"),
		    'captcha_lang'=>t("Captcha Lang"),
		    'merchant_mobile_alert'=>t("Mobile number"),

			'merchant_fb_app_id'=>t("App ID"),
		    'merchant_fb_app_secret'=>t("App Secret"),
		    'merchant_google_client_id'=>t("Client ID"),
		    'merchant_google_client_secret'=>t("Client Secret"),

			'merchant_captcha_site_key'=>t("Captcha Site Key"),
		    'merchant_captcha_secret'=>t("Captcha Secret"),
		    'merchant_captcha_lang'=>t("Captcha Lang"),

			'merchant_google_geo_api_key'=>t("Geocoding API Key"),
		    'merchant_google_maps_api_key'=>t("Google Maps JavaScript API"),
			'merchant_mapbox_access_token'=>t("Mapbox Access Token"),
			'merchant_signup_resend_counter'=> t('Resend code interval'),		
			'instagram_page'=>t("Instagram Page"),
			'website_jwt_token'=>''
		    
		);
	}
		
	public function rules()
	{
		return array(
		  array('website_title,', 
		  'required','on'=>"site_config", 'message'=> t( Helper_field_required ) ),
		  
		  array('website_title', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  
		  array('image,image2,website_logo,mobilelogo,admin_menu_allowed_merchant,admin_menu_lazyload,mobile2_hide_empty_category,
		  admin_activated_menu,enabled_food_search_menu,
		  merchant_enabled_registration,merchant_sigup_status,merchant_default_country,merchant_specific_country,
		  merchant_email_verification,pre_configure_size,home_search_mode,enabled_advance_search,enabled_share_location,
		  google_default_country,admin_zipcode_searchtype,location_default_country,merchant_tbl_book_enabled,
		  booking_cancel_days,booking_cancel_hours,website_enabled_guest_checkout,enabled_cc_management,enabled_featured_merchant,
		  enabled_subscription,cancel_order_enabled,cancel_order_days_applied,cancel_order_hours,cancel_order_status_accepted,website_review_approved_status
		  ',
		  'safe'),
		  
		  array('map_provider,google_geo_api_key,google_maps_api_key,mapbox_access_token',
		  'safe'),
		  
		  array('captcha_site_key,captcha_secret,captcha_lang,captcha_customer_signup,captcha_merchant_signup,captcha_customer_login,
		  captcha_merchant_login,captcha_admin_login,captcha_order,captcha_driver_signup,captcha_contact_form',
		  'safe'),
		  		  
		  array('admin_printing_receipt_width,admin_printing_receipt_size,website_enabled_rcpt,
		  website_receipt_logo','safe'),
		  
		  array('signup_verification,signup_verification_type,blocked_email_add,blocked_mobile,
		  website_terms_customer,website_terms_customer_url','safe'),		
		  
		  //array('website_terms_customer_url', 'url','message'=>t(Helper_field_url)),
		  
		  array('website_review_type,review_baseon_status,earn_points_review_status,publish_review_status,
		  website_reviews_actual_purchase,merchant_can_edit_reviews',
		  'safe'),
		  
		  array('website_admin_mutiple_login,website_merchant_mutiple_login','safe'),
		  array('website_timezone_new,website_date_format_new,website_time_format_new,website_time_picker_interval','safe'),
		  		  
		  array('website_time_picker_interval,signup_resend_counter,review_send_after,merchant_order_critical_mins,merchant_order_reject_mins', 
		  'numerical', 'integerOnly' => true,
		  'min'=>2,
		  'max'=>59,
		  'tooSmall'=>t("You must enter at least greater than 1"),
		  'tooBig'=>t("You must enter below 59"),
		  'message'=>t(Helper_field_numeric)),
		  
		  array('review_image_resize_width', 'numerical', 'integerOnly' => true,
		  'min'=>100,
		  'max'=>1000,
		  'tooSmall'=>t("You must enter at least greater than 100"),
		  'tooBig'=>t("You must enter below 1000"),
		  'message'=>t(Helper_field_numeric)),
		  
		  array('disabled_website_ordering,website_hide_foodprice,website_disbaled_auto_cart,
		  website_disabled_cart_validation,enabled_merchant_check_closing_time,
		  disabled_order_confirm_page,restrict_order_by_status,enabled_map_selection_delivery,
		  admin_service_fee,admin_service_fee_applytax','safe'),
		  
		  array('admin_service_fee,booking_cancel_days,cancel_order_days_applied,
		  merchant_near_expiration_day,order_idle_admin_minutes,order_sms_code_waiting,free_delivery_above_price
		  ', 'numerical', 'integerOnly' => false,
		  'min'=>1,		  
		  'tooSmall'=>t("You must enter at least greater than 1"),		  
		  'message'=>t(Helper_field_numeric)),
		  
		  
		  array('admin_printing_receipt_width,admin_printing_receipt_size,admin_decimal_place', 'numerical', 'integerOnly' => true,
		  'min'=>1,		  
		  'tooSmall'=>t("You must enter at least greater than 1"),		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('admin_country_set,website_address,website_contact_phone,website_contact_email','safe'),
		  
		  array('website_contact_email','email','message'=>t(Helper_field_email)),
		  
		  array('admin_currency_set,admin_currency_position,admin_decimal_place,admin_thousand_separator,admin_decimal_separator','safe'),
		  
		  array('admin_decimal_separator,admin_thousand_separator','length' , 'min'=>1,'max'=>1,
		  'tooLong'=>t("this fields is too long (maximum is 1 characters).")
		  ),
		  
		  //array('booking_cancel_hours','type'=>'time','timeFormat'=>'hh:mm' ),
		  array('booking_cancel_hours,cancel_order_hours', 'type', 'type'=>'time', 'timeFormat'=>'hh:mm',
		   'message'=>t(Helper_field_time)
		  ),
		  
		  array('noti_new_signup_email,noti_new_signup_sms,noti_receipt_email,noti_receipt_sms,noti_booked_admin_email,
		  order_idle_admin_email,order_cancel_admin_email,order_cancel_admin_sms,order_idle_admin_minutes,
		  merchant_near_expiration_day,admin_enabled_order_notification,admin_enabled_order_notification_sounds',
		  'safe'),
		  
		  array('enabled_multiple_translation_new,enabled_language_admin,enabled_language_merchant,enabled_language_front',
		  'safe'),
		  
		  array('fb_flag,fb_app_id,fb_app_secret,google_login_enabled,google_client_id,
		  google_client_secret,google_client_redirect_url,enabled_contact_form,contact_email_receiver,
		  contact_field,contact_content,admin_header_codes,enabled_fb_pixel,fb_pixel_id,enabled_google_analytics,
		  google_analytics_tracking_id',
		  'safe'),

		  array('contact_email_receiver', 'email', 'message'=> CommonUtility::t(Helper_field_email) ),		 
		  
		  array('image,image2', 'file', 'types'=>Helper_imageType, 'safe' => false,
			  'maxSize'=>Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => true
		  ),      
			
		  array('food_option_not_available,enabled_private_menu,merchant_two_flavor_option,merchant_tax_number,
		  merchant_extenal,merchant_enabled_voucher,merchant_required_delivery_time,merchant_packaging_wise,
		  merchant_packaging_charge,merchant_packaging_increment,merchant_tax,merchant_apply_tax,merchant_delivery_charges,
		  merchant_no_tax_delivery_charges,merchant_opt_contact_delivery,merchant_delivery_estimation,merchant_delivery_miles,
		  merchant_distance_type,merchant_enabled_tip,merchant_default_tip,merchant_close_store,merchant_show_time,merchant_disabled_ordering,
		  tracking_estimation_delivery1,tracking_estimation_delivery2,merchant_delivery_estimation_inminutes,
		  merchant_minimum_order,merchant_maximum_order,merchant_delivery_fee_priority,merchant_delivery_fee_no_rush,
		  merchant_minimum_order_pickup,merchant_maximum_order_pickup,merchant_minimum_order_dinein,merchant_maximum_order_dinein,
		  sms_notify_number,facebook_page,twitter_page,google_page,merchant_enabled_alert,merchant_email_alert,merchant_mobile_alert,
		  order_verification,order_sms_code_waiting,merchant_pickup_estimation,free_delivery_on_first_order,merchant_service_fee,
		  merchant_service_fee_applytax',
		  'safe'),
			
		  array('tracking_estimation_delivery1,tracking_estimation_delivery2', 'numerical', 'integerOnly' => true,
		  'min'=>1,
		  'max'=>60,
		  'tooSmall'=>t("You must enter at least greater than 1"),
		  'tooBig'=>t("You must enter below 60"),
		  'message'=>t(Helper_field_numeric),
		  'on'=>'tracking_estimation'
		  ),

		  array('tracking_estimation_delivery1,tracking_estimation_delivery2', 
		  'required','on'=>"tracking_estimation", 'message'=> t( Helper_field_required ) ),	  
		  
		  array('merchant_booking_receiver','email','message'=>t(Helper_field_email),
		   'on'=>'booking_settings'
		  ),
		  
		  array('enabled_merchant_table_booking,accept_booking_sameday,enabled_merchant_booking_alert,
		  fully_booked_msg,merchant_booking_receiver','safe','on'=>'booking_settings'),
		  
		   array('merchant_delivery_estimation_inminutes,merchant_delivery_estimation_min1,merchant_delivery_estimation_min2', 'numerical', 'integerOnly' => true,		  
		  'min'=>1,'max'=>300,
		  'tooSmall'=>t("Minimum value is 1"),
		  'message'=>t(Helper_field_numeric)),
		  
		  array('merchant_delivery_charges_type', 
		  'required','on'=>"delivery_settings", 'message'=> t( Helper_field_required ) ),
		  
		   array('merchant_delivery_charges,merchant_maximum_order,merchant_minimum_order,
		   merchant_delivery_fee_priority,merchant_delivery_fee_no_rush,merchant_minimum_order_pickup,merchant_maximum_order_pickup,
		   merchant_minimum_order_dinein,merchant_maximum_order_dinein
		   ', 'numerical', 'integerOnly' => false,		   
		  'min'=>1,		  
		  'tooSmall'=>t("You must enter at least greater than 1"),		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('website_terms_customer_url,facebook_page,twitter_page,google_page,merchant_extenal','url',
		   'defaultScheme'=>'http',
		   'message'=>t(HELPER_NOT_VALID_URL)
		  ),
		 	
		  array('merchant_service_fee','numerical','integerOnly'=>false),	 
		  
		  array('admin_enabled_alert,admin_email_alert,admin_mobile_alert,signup_type,
		  signup_enabled_verification,signup_enabled_terms,signup_terms,signup_enabled_capcha,
		  signup_welcome_tpl,signup_verification_tpl,signup_resetpass_tpl,enabled_website_ordering,signupnew_tpl,
		  merchant_reg_verification,merchant_reg_admin_approval, search_enabled_select_from_map,
		  search_default_country,location_searchtype,review_template_id,review_send_after,review_template_enabled,
		  review_image_resize_width
		  ', 'safe' ),
		  
		  array('pusher_app_id,pusher_key,pusher_secret,pusher_cluster,realtime_provider,
		  merchant_enabled_registration_capcha ,registration_membeship , registration_commission,
		  registration_confirm_account_tpl,registration_welcome_tpl,registration_program,registration_terms_condition,
		  merchant_registration_new_tpl,merchant_registration_welcome_tpl,merchant_plan_expired_tpl,
		  merchant_plan_near_expired_tpl,merchant_order_critical_mins,merchant_order_reject_mins,
		  mobilephone_settings_country,mobilephone_settings_default_country,
		  capcha_admin_login_enabled,capcha_merchant_login_enabled,enabled_language_bar,default_language,enabled_language_bar_front,
		  backend_forgot_password_tpl,allow_return_home,image_resizing,
		  merchant_fb_flag,merchant_fb_app_id,merchant_fb_app_secret,merchant_google_login_enabled,merchant_google_client_id,
		  merchant_google_client_secret,
		  merchant_captcha_enabled,merchant_captcha_site_key,merchant_captcha_secret,merchant_captcha_lang,
		  merchant_map_provider,merchant_google_geo_api_key,merchant_google_maps_api_key,merchant_mapbox_access_token,
		  merchant_signup_enabled_verification,merchant_signup_resend_counter,merchant_signup_enabled_terms,merchant_signup_terms,
		  merchant_mobilephone_settings_country,merchant_mobilephone_settings_default_country,merchant_set_default_country,instagram_page,
		  runactions_method
		  ','safe'),
		  
		//   array('website_jwt_token','url', 'defaultScheme'=>'http'),

		//   array('website_jwt_token', 
		//   'required','message'=> t( Helper_field_required ) ),
		 
		  
		);
	}
	
	protected function beforeSave()
	{
		if(parent::beforeSave()){
			$this->last_update = CommonUtility::dateNow();
		} 
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();				
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();	
	}
	
}
/*end class*/
