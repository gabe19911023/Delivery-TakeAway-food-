<?php
class CNavs
{
	
	public static function widgetColOne()
	{
		$page_id = strtolower(Yii::app()->controller->action->id);		
		
		switch ($page_id) {
			case "restaurants":
				return 'widget-nav';
				break;
				
			case "menu":
				return 'widget-address';
				break;
			
		}
		return false;
	}
	
	public static function widgetColTwo()
	{
		$page_id = Yii::app()->controller->action->id;				
				
		switch ($page_id) {
			case "restaurants":
			case "menu":	
				return 'search-nav';
				break;					
		}
		return false;
	}
	
}
/*end class*/