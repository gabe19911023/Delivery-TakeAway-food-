<?php
class WidgetLogo extends CWidget 
{
	public $class_name, $link;
	
	public function run() {		
		$option = OptionsTools::find(['website_logo','mobilelogo']);
		$website_logo = isset($option['website_logo'])?$option['website_logo']:'';
		$folder = CMedia::adminFolder();
		if(isset(Yii::app()->merchant->merchant_id) ){
			$id = Yii::app()->merchant->merchant_id;
			$model = AR_merchant::model()->findByPk( $id );
			if(!empty($model)){
				$website_logo = $model->logo;
				$folder = $model->path;//CMedia::merchantFolder();
			}
		}
		$image_url = CMedia::getImage($website_logo, $folder);
		$this->render('backend-logo',[
			'class_name'=>$this->class_name,
			'website_logo'=>$website_logo,
			'image_url'=>$image_url,
            'link'=>$this->link
		]);
	}
	
}
/*end class*/