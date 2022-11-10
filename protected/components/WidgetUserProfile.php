<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetUserProfile extends CMenu
{		 	 
	 public function init()
	 {		 		 	  
	 	  	 	  	 	  
	 	  $settings = AR_admin_meta::getMeta(array('webpush_app_enabled'));		
	 	  $webpush_app_enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';
		  $webpush_app_enabled = $webpush_app_enabled==1?true:false;	
	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-account-circle"></i>'.t("Basic Details"),
	 	    'url'=>array('account/profile')
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-lock-outline"></i>'.t("Change Password"),
	 	    'url'=>array('account/change_password'),
	 	    'itemOptions'=>array(
	 	      'class'=>"user-change-password"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	 
	 	  if($webpush_app_enabled){
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-notifications-none"></i>'.t("Notifications"),
	 	    'url'=>array('account/notifications'),
	 	    'itemOptions'=>array(
	 	      'class'=>"manage-notifications"
	 	    )
	 	  );	
	 	  }
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-account-box-o"></i>'.t("Manage Account"),
	 	    'url'=>array('account/manage_account'),
	 	    'itemOptions'=>array(
	 	      'class'=>"manage-account"
	 	    )
	 	  );	 
	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'sub-menu list-unstyled'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'sub-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }

	 public static function CustomMenu()
	 {
		   $menu = array();

		  $settings = AR_admin_meta::getMeta(array('webpush_app_enabled'));		
	 	  $webpush_app_enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';
		  $webpush_app_enabled = $webpush_app_enabled==1?true:false;	
	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>t("Basic Details"),
	 	    'url'=>Yii::app()->createAbsoluteUrl('account/profile')
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>t("Change Password"),
	 	    'url'=>Yii::app()->createAbsoluteUrl('account/change_password'),	 	    
	 	  );	 	  	 	 	 	
	 	  
	 	 
	 	  if($webpush_app_enabled){
	 	  $menu[]=array(
	 	    'label'=>t("Notifications"),
	 	    'url'=>Yii::app()->createAbsoluteUrl('account/notifications'),	 	    
	 	  );	
	 	  }
	 	  
	 	  $menu[]=array(
	 	    'label'=>t("Manage Account"),
	 	    'url'=>Yii::app()->createAbsoluteUrl('account/manage_account'),	 	    
	 	  );	 
		  
		  return $menu;
	 }
	 
}
/*end class*/