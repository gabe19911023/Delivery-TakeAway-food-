<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetStatus extends CMenu
{		 
	 
	 public function init()
	 {		 		 	  
	 	  	 	  	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-shopping-basket"></i>'.t("Details"),
	 	    'url'=>array('attributes/status_update','id'=>Yii::app()->input->get("id"))
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-arrow-right-top"></i>'.t("Actions"),
	 	    'url'=>array('attributes/status_actions','id'=>Yii::app()->input->get("id")),
	 	    'itemOptions'=>array(
	 	      'class'=>"status-actions"
	 	    )
	 	  );	 	  	 	 	 	
	 	  	 		 	  	 	 	 		 	  	 	 	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'status-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'status-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/