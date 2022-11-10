<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetUserMenu extends CMenu
{		 
	 public static $ctr = array();
	 
	 public function init()
	 {		 		 	  
	 	  	 	  	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-account-circle"></i>'.t("Basic Details"),
	 	    'url'=>array( isset(self::$ctr[0])?self::$ctr[0]:'' )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-lock-outline"></i>'.t("Change Password"),
	 	    'url'=>array(isset(self::$ctr[1])?self::$ctr[1]:''),
	 	    'itemOptions'=>array(
	 	      'class'=>"customer-address"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  if(isset(self::$ctr[2])){
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-notifications-none"></i>'.t("Web Notifications"),
	 	    'url'=>array(isset(self::$ctr[2])?self::$ctr[2]:''),
	 	    'itemOptions'=>array(
	 	      'class'=>"settings-notifications"
	 	    )
	 	  );	 	  	 	 	 	
	 	  }
	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'user-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'user-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/