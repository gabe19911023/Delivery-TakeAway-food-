<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetItemMenu extends CMenu
{		 
	 public function init()
	 {		 		 	  
	 	  	 	  
	 	  $id = (integer) Yii::app()->input->get('item_id');	
	 	  
	 	  $tax_menu_settings = Yii::app()->params['tax_menu_settings'];	 	  
	 	  	 	  		
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-info-outline"></i>'.t("Details"),
	 	    'url'=>array("/food/item_update",'item_id'=>$id)
	 	  );	 	 

	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-money-box"></i>'.t("Price"),
	 	    'url'=>array("/food/item_price",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_price"
	 	    )
	 	  );	 	  	 	 	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-plus-circle-o-duplicate"></i>'.t("Addon"),
	 	    'url'=>array("/food/item_addon",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_addon"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-plus-circle-o"></i>'.t("Attributes"),
	 	    'url'=>array("/food/item_attributes",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_attributes"
	 	    )
	 	  );	 	  
	 	  	 	  
	 	  if($tax_menu_settings['tax_enabled']==true && $tax_menu_settings['tax_type']=="multiple"){
		 	  $menu[]=array(
		 	    'label'=>'<i class="zmdi zmdi-balance"></i>'.t("Tax"),
		 	    'url'=>array("/food/item_tax",'item_id'=>$id),
		 	    'itemOptions'=>array(
		 	      'class'=>"item_tax"
		 	    )
		 	  );	 	  
	 	  }

	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-assignment-check"></i>'.t("Availability"),
	 	    'url'=>array("/food/item_availability",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_availability"
	 	    )
	 	  );	 	  	 	 	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-storage"></i>'.t("Inventory"),
	 	    'url'=>array("/food/item_inventory",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_inventory"
	 	    )
	 	  );	 	  	 	 	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-drink"></i>'.t("Sales Promotion"),
	 	    'url'=>array("/food/item_promos",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_promos"
	 	    )
	 	  );	 	  	 	 	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-collection-image-o"></i>'.t("Gallery"),
	 	    'url'=>array("/food/item_gallery",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_gallery"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-google-earth"></i>'.t("SEO"),
	 	    'url'=>array("/food/item_seo",'item_id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"item_seo"
	 	    )
	 	  );	 	
	 	  
	 	  	 	
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'item-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'item-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/