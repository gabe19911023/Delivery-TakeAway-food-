<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetPlans extends CMenu
{		 
	 
	 public function init()
	 {		 		 	  
	 	  	 	  	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-shopping-basket"></i>'.t("Details"),
	 	    'url'=>array('plans/update','id'=>Yii::app()->input->get("id"))
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-folder-star-alt"></i>'.t("Features"),
	 	    'url'=>array('plans/features','id'=>Yii::app()->input->get("id")),
	 	    'itemOptions'=>array(
	 	      'class'=>"features"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi  zmdi-money-box"></i>'.t("Plans Payment ID"),
	 	    'url'=>array('plans/payment_list','id'=>Yii::app()->input->get("id")),
	 	    'itemOptions'=>array(
	 	      'class'=>"plans_payments"
	 	    )
	 	  );	 	  	 	 	 	
	 	  	 		 	  	 	 	 		 	  	 	 	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'features-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'features-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
	 
	
}
/*end class*/