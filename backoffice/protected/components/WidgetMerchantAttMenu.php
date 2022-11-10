<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetMerchantAttMenu extends CMenu
{		 	 
	 public $merchant_type,$main_account;
	 
	 public function init()
	 {		 		 	 	 	
	 		 		 	 	 	  	 	 	 	
	 	$menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-store"></i>'.t("Merchant information"),
	 	    'url'=>array("/merchant/edit")
	 	  );
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-account-circle"></i>'.t("Login information"),
	 	    'url'=>array("/merchant/login"),	
	 	    'visible'=>$this->main_account==1?true:false 	    
	 	  );
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-pin"></i>'.t("Address"),
	 	    'url'=>array("/merchant/address")
	 	  );
	 	  
	 	  /*$menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-cutlery"></i>'.t("Merchant Type"),
	 	    'url'=>array("/merchant/membership")
	 	  );*/
	 	  
	 	  /*$menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-star-outline"></i>'.t("Featured"),
	 	    'url'=>array("/merchant/featured")
	 	  );*/
	 	  	 	  
	 	  if($this->merchant_type==1):
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-tv-list"></i>'.t("Payment history"),
	 	    'url'=>array("/merchant/payment_history")
	 	  );			 	  	 	  
	 	  endif;
	 	  
	 	  $menu[]=array(	 	    
	 	    'label'=>'<i class="zmdi zmdi-facebook"></i>'.t("Social Media"),
	 	    'url'=>array("/merchant/social_media")
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