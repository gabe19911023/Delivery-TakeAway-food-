<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetOrderSettings extends CMenu
{		 
	 public static $ctr = array();
	 
	 public function init()
	 {		 		 	  
	 	  	 	  	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-shopping-basket"></i>'.t("Order Status"),
	 	    'url'=>array('order/settings')
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-tab"></i>'.t("Order Tabs"),
	 	    'url'=>array('order/settings_tabs'),
	 	    'itemOptions'=>array(
	 	      'class'=>"settings-tabs"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-square-o"></i>'.t("Order Buttons"),
	 	    'url'=>array('order/settings_buttons'),
	 	    'itemOptions'=>array(
	 	      'class'=>"settings-buttons"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-car"></i>'.t("Order Tracking"),
	 	    'url'=>array('order/settings_tracking'),
	 	    'itemOptions'=>array(
	 	      'class'=>"settings-tracking"
	 	    )
	 	  );	 	  	 	 	 		 	  	 	 
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-square-o"></i>'.t("Template"),
	 	    'url'=>array('order/settings_template'),
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