<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetSiteSetup extends CMenu
{		 
	 public function init()
	 {		 		 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-home"></i>'.t("Site information"),
	 	    'url'=>array("/admin/site_information")
	 	  );	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-map"></i>'.t("Map API Keys"),
	 	    'url'=>array("/admin/map_keys")
	 	  );	 	
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-google"></i>'.t("Google Recaptcha"),
	 	    'url'=>array("/admin/recaptcha")
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-search"></i>'.t("Search Mode"),
	 	    'url'=>array("/admin/search_settings")
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-account-o"></i>'.t("Login & Signup"),
	 	    'url'=>array("/admin/login_sigup")
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-phone"></i>'.t("Phone Settings"),
	 	    'url'=>array("/admin/phone_settings")
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-facebook"></i>'.t("Social Login"),
	 	    'url'=>array("/admin/social_settings")
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-print"></i>'.t("Printing Settings"),
	 	    'url'=>array("/admin/printing")
	 	  );	 	  	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-star-half"></i>'.t("Reviews"),
	 	    'url'=>array("/admin/reviews")
	 	  );
	 	  /*$menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-lock-outline"></i>'.t("Security"),
	 	    'url'=>array("/admin/security")
	 	  );*/
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-calendar-check"></i>'.t("Timezone"),
	 	    'url'=>array("/admin/timezone")
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-shopping-basket"></i>'.t("Ordering"),
	 	    'url'=>array("/admin/ordering")
	 	  );	 	  
	 	  /*$menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-menu"></i>'.t("Menu Options"),
	 	    'url'=>array("/admin/menu_options")
	 	  );*/
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-sign-in"></i>'.t("Merchant Registration"),
	 	    'url'=>array("/admin/merchant_registration")
	 	  );	 	  
	 	  /*$menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-account-add"></i>'.t("Booking"),
	 	    'url'=>array("/admin/booking_settings")
	 	  );*/
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-notifications-add"></i>'.t("Notifications"),
	 	    'url'=>array("/admin/notifications")
	 	  );
	 	  /*$menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-flag"></i>'.t("Languages"),
	 	    'url'=>array("/admin/language_settings")
	 	  );*/
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-phone-in-talk"></i>'.t("Contact Settings"),
	 	    'url'=>array("/admin/contact_settings")
	 	  );
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-chart"></i>'.t("Analytics"),
	 	    'url'=>array("/admin/analytics_settings")
	 	  );

		   $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-code-setting"></i>'.t("API Access"),
			'url'=>array("/admin/api_access")
		  );
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-more"></i>'.t("Others"),
	 	    'url'=>array("/admin/site_others")
	 	  );
	 	 
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'attributes-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'attributes-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/