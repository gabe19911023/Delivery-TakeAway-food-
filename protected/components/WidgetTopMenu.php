<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetTopMenu extends CMenu
{		 
	 public function init()
	 {		 		 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>t("Home"),
	 	    'url'=>array("/"),
	 	    'itemOptions'=>array(
	 	      'class'=>"d-inline"
	 	    )
	 	  );	 	  
	 	  $menu[]=array(
	 	    'label'=>t("Restaurants"),
	 	    'url'=>array("/store/restaurants"),
	 	    'itemOptions'=>array(
	 	      'class'=>"d-inline"
	 	    )
	 	  );	 	  
	 	    $menu[]=array(
	 	    'label'=>t("Menu"),
	 	    'url'=>array("/store/menu"),
	 	    'itemOptions'=>array(
	 	      'class'=>"d-inline"
	 	    )
	 	  );	
	 	  $menu[]=array(
	 	    'label'=>t("Hot deals"),
	 	    'url'=>array("/store/offers"),
	 	    'itemOptions'=>array(
	 	      'class'=>"d-inline"
	 	    )
	 	  );	 	  
	 	  
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'top-menu list-unstyled'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'top-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/