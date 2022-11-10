<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetMerchantInfoMenu extends CMenu
{		 
	 public $merchant_id;
	 	
	 public function init()
	 {		 		 	 	 	
	 	  $menu = array();	
		   
		  $addon_single = CommonUtility::getAddonStatus('DXpn3kxHj8oVc64YvsHDTm2n6srn87gmcA2ZqXhgxI3dZ0cvYHh6UE8YXZQW/Xr2Mzf7svb3dPWaqg==');
	 	  	 	 
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-store"></i>'.t("Merchant information"),
	 	    'url'=>array("/".Yii::app()->controller->id."/edit",'id'=>$this->merchant_id)
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-account-circle"></i>'.t("Login information"),
	 	    'url'=>array("/".Yii::app()->controller->id."/login",'id'=>$this->merchant_id)
	 	  );
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-pin"></i>'.t("Address"),
	 	    'url'=>array("/".Yii::app()->controller->id."/address",'id'=>$this->merchant_id)
	 	  );
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-pin-drop"></i>'.t("Zone"),
	 	    'url'=>array("/".Yii::app()->controller->id."/zone",'id'=>$this->merchant_id)
	 	  );
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-cutlery"></i>'.t("Merchant Type"),
	 	    'url'=>array("/".Yii::app()->controller->id."/membership",'id'=>$this->merchant_id)
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-star-outline"></i>'.t("Featured"),
	 	    'url'=>array("/".Yii::app()->controller->id."/featured",'id'=>$this->merchant_id)
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-tv-list"></i>'.t("Payment history"),
	 	    'url'=>array("/".Yii::app()->controller->id."/payment_history",'id'=>$this->merchant_id)
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-money"></i>'.t("Payment settings"),
	 	    'url'=>array("/".Yii::app()->controller->id."/payment_settings",'id'=>$this->merchant_id)
	 	  );
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-store"></i>'.t("Others"),
	 	    'url'=>array("/".Yii::app()->controller->id."/others",'id'=>$this->merchant_id)
	 	  );
	 	  /*$menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-lock-open"></i>'.t("Access"),
	 	    'url'=>array("/".Yii::app()->controller->id."/access",'id'=>$this->merchant_id)
	 	  );*/

		  if($addon_single):		  
		  $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-code-setting"></i>'.t("API Access"),
			'url'=>array("/".Yii::app()->controller->id."/api_access",'id'=>$this->merchant_id)
		  );

		  $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-search"></i>'.t("Search Mode"),
			'url'=>array("/".Yii::app()->controller->id."/search_mode",'id'=>$this->merchant_id)
		  );

		  $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-account-o"></i>'.t("Login & Signup"),
			'url'=>array("/".Yii::app()->controller->id."/login_sigup",'id'=>$this->merchant_id)
		  );

		  $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-phone"></i>'.t("Phone Settings"),
			'url'=>array("/".Yii::app()->controller->id."/phone_settings",'id'=>$this->merchant_id)
		  );

		  $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-facebook"></i>'.t("Social Settings"),
			'url'=>array("/".Yii::app()->controller->id."/social_settings",'id'=>$this->merchant_id)
		  );

		  $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-google"></i>'.t("Google Recaptcha"),
			'url'=>array("/".Yii::app()->controller->id."/recaptcha_settings",'id'=>$this->merchant_id)
		  );

		  $menu[]=array(	 	    
			'label'=>'<i class="zmdi zmdi-map"></i>'.t("Map API Keys"),
			'url'=>array("/".Yii::app()->controller->id."/map_keys",'id'=>$this->merchant_id)
		  );
		  endif;
	 	  
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