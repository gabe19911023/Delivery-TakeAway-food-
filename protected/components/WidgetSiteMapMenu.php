<?php
Yii::import('zii.widgets.CMenu', true);

class WidgetSiteMapMenu extends CMenu
{		 
	 public $items;
	 
	 public function init()
	 {		 			 	  	 	  
	 	  $this->encodeLabel = false;
	 	  $this->activeCssClass = "active";
	 	  $this->activateParents = true;
	 	  $this->htmlOptions = array(
	 	    'class'=>'parent list-unstyled'
	 	  ); 
	 	  $this->submenuHtmlOptions = array(
	 	    'class'=>'sub-child'
	 	  ); 
	 	  
	 	  parent::init();
	 }
	 
}
/*end class*/