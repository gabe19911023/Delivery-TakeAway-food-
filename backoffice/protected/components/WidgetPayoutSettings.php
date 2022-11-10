<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetPayoutSettings extends CMenu
{		 
	 public static $ctr = array();
	 
	 public function init()
	 {		 		 	  
	 	  	 	  	 	  
	 	  $menu = array();	 	   	 	 		 	  	 		 	  
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-settings"></i>'.t("Settings"),
	 	    'url'=>array("/withdrawals/settings")
	 	  );	 	  	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-square-o"></i>'.t("Template"),
	 	    'url'=>array('withdrawals/settings_template'),
	 	    'itemOptions'=>array(
	 	      'class'=>"settings-template"
	 	    )
	 	  );	 	  	 	 	 		 	  	 	 
	 	  	 	
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