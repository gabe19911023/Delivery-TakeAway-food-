<?php
class WidgetUserNav extends CWidget 
{
	
	public function run() {		
		$controller_id = Yii::app()->controller->action->id;
		$cart_preview = true;
		if($controller_id=="menu" || $controller_id=="checkout" ){
			$cart_preview = false;
			if($controller_id=="menu" && Yii::app()->params['isMobile']){
				$cart_preview = true;
			}
		}
		
		if(Yii::app()->user->isGuest){
		   $this->render('user-nav-guest',array(
		     'cart_preview'=>$cart_preview
		   ));		
		} else $this->render('user-nav',array(
		  'cart_preview'=>$cart_preview
		));		
	}
	
}
/*end class*/