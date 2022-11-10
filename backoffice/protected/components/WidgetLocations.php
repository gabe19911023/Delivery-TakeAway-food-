<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetLocations extends CMenu
{		 
	 public function init()
	 {		 		 	  
	 	  $country_id = Yii::app()->input->get('country_id');	
	 	  	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-collection-item-1"></i>'.t("State List"),
	 	    'url'=>array("/location/state_list",'country_id'=>$country_id),
	 	    'itemOptions'=>array(
	 	      'class'=>"location-state"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-collection-item-2"></i>'.t("City List"),
	 	    'url'=>array("/location/city_list",'country_id'=>$country_id),
	 	    'itemOptions'=>array(
	 	      'class'=>"location-city"
	 	    )
	 	  );
	 	  	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-collection-item-3"></i>'.t("District/Area List"),
	 	    'url'=>array("/location/area_list",'country_id'=>$country_id),
	 	    'itemOptions'=>array(
	 	      'class'=>"location-area"
	 	    )
	 	  );	 	  	 	 	 	
	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'location-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'location-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/