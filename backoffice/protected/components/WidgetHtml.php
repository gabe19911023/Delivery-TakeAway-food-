<?php
class WidgetHtml extends CWidget 
{
	public $id;
	public $value;
	public $class_name;
	
	public function run() {
		$this->render('html_form');
	}
	
}
/*end class*/