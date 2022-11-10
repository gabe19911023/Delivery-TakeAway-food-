<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetUser extends CMenu
{		 
	 public static $ctr=array();
	 
	 public function init()
	 {		 		 	  
	 	  $id = Yii::app()->input->get('id');	
	 	  
	 	  $menu = array();
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-account-circle"></i>'.t("Basic Details"),
	 	    'url'=>array( isset(self::$ctr[0])?self::$ctr[0]:'','id'=>$id)
	 	  );	 	  	 	 	 	
	 	  
	 	  $menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-lock-outline"></i>'.t("Change Password"),
	 	    'url'=>array( isset(self::$ctr[1])?self::$ctr[1]:'','id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"customer-address"
	 	    )
	 	  );	 	  	 	 	 	
	 	  
	 	  /*$menu[]=array(
	 	    'label'=>'<i class="zmdi zmdi-pin-drop"></i>'.t("Zone"),
	 	    'url'=>array( isset(self::$ctr[2])?self::$ctr[2]:'','id'=>$id),
	 	    'itemOptions'=>array(
	 	      'class'=>"customer-zone"
	 	    )
	 	  );*/
	 	  	 	
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