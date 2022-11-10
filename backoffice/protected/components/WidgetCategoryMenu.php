<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetCategoryMenu extends CMenu
{		 
	 public function init()
	 {		 		 	  
	 	  	 	  
	 	  $id = (integer) Yii::app()->input->get('id');			
	 	
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-info-outline"></i>'.t("Details"),
	 	    'url'=>array("/food/category_update",'id'=>$id)
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-time-countdown"></i>'.t("Availability"),
	 	    'url'=>array("/food/category_availability",'id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"category_availability"
	 	    )
	 	  );	 	  	 	 	 	
	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'category-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'category-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/