<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetBookingSettings extends CMenu
{		 	
	 public function init()
	 {		 		 	  
	 	  	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-settings"></i>'.t("Settings"),
	 	    'url'=>array("/booking/settings")
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-time"></i>'.t("Time Slot"),
	 	    'url'=>array("/booking/time_slot"),
	 	    'itemOptions'=>array(
	 	      'class'=>"store-hours"
	 	    )
	 	  );	 	  	 	 	 	
	 	  	 	  
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'booking-settings'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'booking-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/