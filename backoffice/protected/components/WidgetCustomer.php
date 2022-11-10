<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetCustomer extends CMenu
{		 
	 public function init()
	 {		 		 	  
	 	  $id = (integer) Yii::app()->input->get('id');	
	 	  	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-account-circle"></i>'.t("Basic Details"),
	 	    'url'=>array("/buyer/customer_update",'id'=>$id)
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-pin"></i>'.t("Address"),
	 	    'url'=>array("/buyer/address",'id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"customer-address"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-shopping-basket"></i>'.t("Order history"),
	 	    'url'=>array("/buyer/order_history",'id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"customer-order-history"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  /*$menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-coffee"></i>'.t("Booking history"),
	 	    'url'=>array("/buyer/booking_history",'id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"customer-booking-history"
	 	    )
	 	  );*/	 	  	 	 	 	
	 	  
	 	  $this->items = $menu;	 	  
	 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'customer-menu'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'customer-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/