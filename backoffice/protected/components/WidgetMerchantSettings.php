<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetMerchantSettings extends CMenu
{		 	
	 public function init()
	 {		 		 	  
	 	  	 	  
	 	  $menu = array();
		  $addon_single = CommonUtility::getAddonStatus('DXpn3kxHj8oVc64YvsHDTm2n6srn87gmcA2ZqXhgxI3dZ0cvYHh6UE8YXZQW/Xr2Mzf7svb3dPWaqg==');


	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-settings"></i>'.t("Basic Settings"),
	 	    'url'=>array("/merchant/settings")
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-time"></i>'.t("Take Away Times"),
	 	    'url'=>array("/merchant/store_hours"),
	 	    'itemOptions'=>array(
	 	      'class'=>"store-hours"
	 	    )
	 	  );		  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-time"></i>'.t("Delivery Times"),
	 	    'url'=>array("/merchant/delivery_times"),
	 	    'itemOptions'=>array(
	 	      'class'=>"delivery-times"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-balance"></i>'.t("Taxes"),
	 	    'url'=>array("/merchant/taxes"),
	 	    'itemOptions'=>array(
	 	      'class'=>"taxes"
	 	    )
	 	  );

		  if($addon_single):
		   $menu[]=array(
			'label'=>'<i class="zmdi zmdi-search"></i>'.t("Search Mode"),
			'url'=>array("/merchant/search_settings"),
			'itemOptions'=>array(
			  'class'=>"search-settings"
			)
		  );	 	  	 	 	 	
 

		 $menu[]=array(
			'label'=>'<i class="zmdi zmdi-account-o"></i>'.t("Login & Signup"),
			'url'=>array("/merchant/login_sigup"),
			'itemOptions'=>array(
			  'class'=>"login-sigup"
			)
		  );	 	  	 	 	 	

		  $menu[]=array(
			'label'=>'<i class="zmdi zmdi-phone"></i>'.t("Phone Settings"),
			'url'=>array("/merchant/phone_settings"),
			'itemOptions'=>array(
			  'class'=>"phone-settings"
			)
		  );	 	  	 	 
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-facebook"></i>'.t("Social Settings"),
	 	    'url'=>array("/merchant/social_settings"),
	 	    'itemOptions'=>array(
	 	      'class'=>"social-settings"
	 	    )
	 	  );	 	  
		   
		 $menu[]=array(
			'label'=>'<i class="zmdi zmdi-google"></i>'.t("Google Recaptcha"),
			'url'=>array("/merchant/recaptcha_settings"),
			'itemOptions'=>array(
			  'class'=>"recaptcha-settings"
			)
		  );	

		  $menu[]=array(
			'label'=>'<i class="zmdi zmdi-map"></i>'.t("Map API Keys"),
			'url'=>array("/merchant/map_keys"),
			'itemOptions'=>array(
			  'class'=>"map-keys"
			)
		  );	 	  	 	 	 	
		  endif;
		  	 			   
	 	  	 	  
		  $menu[]=array(
			'label'=>'<i class="zmdi zmdi-notifications-active"></i>'.t("Notification Settings"),
			'url'=>array("/merchant/notification_settings"),
			'itemOptions'=>array(
			  'class'=>"notification-settings"
			)
		  );	 	  	 	 	 	
		  

	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-settings-square"></i>'.t("Orders Settings"),
	 	    'url'=>array("/merchant/orders_settings"),
	 	    'itemOptions'=>array(
	 	      'class'=>"orders-settings"
	 	    )
	 	  );	 	  	 	 	 	
		
	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'merchant-settings'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'user-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/