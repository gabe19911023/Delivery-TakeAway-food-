<?php
class InlineCSTools
{
	public static function registerStatusCSS()
	{
		$criteria=new CDbCriteria;
		$criteria->select='group_name,status,color_hex,font_color_hex'; 
		$model=AR_status_management::model()->findAll($criteria);		
		if($model){
			$css_inline = '';
			foreach ($model as $val) {								
$css_inline.=".$val->group_name.$val->status{background:$val->color_hex;color:$val->font_color_hex;}";
			}				
			Yii::app()->clientScript->registerCss('status_colors',$css_inline);
		}		
	}
	
	public static function registerOrder_StatusCSS()
	{
		$criteria=new CDbCriteria;
		$criteria->select='description,background_color_hex,font_color_hex'; 
		$model=AR_status::model()->findAll($criteria);		
		if($model){
			$css_inline = '';			
			foreach ($model as $val) {		
			$val->description = str_replace(" ","_",$val->description);
$css_inline.=".order_status.$val->description{background:$val->background_color_hex;color:$val->font_color_hex;}";
			}					
			Yii::app()->clientScript->registerCss('order_status_colors',$css_inline);
		}		
	}
	
	public static function registerServicesCSS()
	{
		$criteria=new CDbCriteria;
		$criteria->select='service_code,color_hex,font_color_hex'; 
		$model=AR_services::model()->findAll($criteria);		
		if($model){
			$css_inline = '';			
			foreach ($model as $val) {				
$css_inline.=".services.$val->service_code{background:$val->color_hex;color:$val->font_color_hex;}";
			}					
			Yii::app()->clientScript->registerCss('services_colors',$css_inline);
		}		
	}
	
	public static function registerStoreHours()
	{
		$data['open']=array(
		  'bg'=>"#ffd966",
		  'font'=>"#212529",
		);		
		$data['close']=array(
		  'bg'=>"#ea9895",
		  'font'=>"#fff",
		);
		
		$css_inline = '';	
		foreach ($data as $key=>$val) {			
			$css_inline.=".store_hours_$key{background:$val[bg];color:$val[font];}";
		}
		Yii::app()->clientScript->registerCss('store_hours',$css_inline);
	}
	
	public static function registerDeliveryTimes()
	{
		$data['open']=array(
		  'bg'=>"#ffd966",
		  'font'=>"#212529",
		);		
		$data['close']=array(
		  'bg'=>"#ea9895",
		  'font'=>"#fff",
		);
		
		$css_inline = '';	
		foreach ($data as $key=>$val) {			
			$css_inline.=".delivery_times_$key{background:$val[bg];color:$val[font];}";
		}
		Yii::app()->clientScript->registerCss('delivery_times',$css_inline);
	}
	
}
/*end class*/