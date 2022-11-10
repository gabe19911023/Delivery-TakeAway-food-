<?php
class WidgetSiteLogo extends CWidget 
{
	public $class_name;
	
	public function run() {		
		$option = OptionsTools::find(['website_logo','mobilelogo']);
		$website_logo = isset($option['website_logo'])?$option['website_logo']:'';
		$image_url = CMedia::getImage($website_logo, CMedia::adminFolder());		
		$this->render('site-logo',[
			'class_name'=>$this->class_name,
			'website_logo'=>$website_logo,
			'image_url'=>$image_url
		]);
	}
	
}
/*end class*/