<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetMenu extends CMenu
{	
	 public $menu_type;
	 
	 public function init()
	 {		 	   	  	 	
	 	  if($this->menu_type=="admin"){		 	  
		 	  $role_id = Yii::app()->user->role_id;		
		 	  AdminMenu::buildMenu(0,false,$role_id); 	  		 	  
		 	  $this->items = AdminMenu::$items;
	 	  } else {	 	  	  
	 	  	  $role_id = Yii::app()->merchant->role_id;	 		 	  	  
	 	  	  AdminMenu::buildMenu(0,false,$role_id,'merchant'); 	  		 	  
		 	  $this->items = AdminMenu::$items;  	  
	 	  }
	 	  	 	  	 	 
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'sidebar-nav'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'sidebar-nav-sub-menu'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/