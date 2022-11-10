<?php
class WidgetOwlCarousel extends CWidget 
{
	public $owl_data=array();
	public $data=array();
	public $template;
	public $title, $owl_prev, $owl_next;
	
	public function run() {		
		$this->render('owl-carousel');
	}
	
}
/*end class*/