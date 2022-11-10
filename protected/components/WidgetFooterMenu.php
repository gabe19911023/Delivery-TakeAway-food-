<?php
class WidgetFooterMenu extends CWidget 
{	
	public function run() {		
		MMenu::buildMenu(0,false,PPages::menuType());		
		$this->render('footer-menu',array(
		  'menu'=>MMenu::$items
		));
	}
	
}
/*end class*/