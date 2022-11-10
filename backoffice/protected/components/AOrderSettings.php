<?php 
class AOrderSettings
{
	public static function getStatus($meta_name = array())
	{		
		$not_in_status = array();
		$criteria=new CDbCriteria();
		$criteria->select = "meta_value";
		$criteria->addInCondition('meta_name', $meta_name );
		
		//$models = AR_admin_meta::model()->findAll($criteria);		
		$dependency = CCacheData::dependency();		
		$models = AR_admin_meta::model()->cache(Yii::app()->params->cache, $dependency)->findAll($criteria); 
		if($models){
			foreach ($models as $items) {
				array_push($not_in_status,$items->meta_value);
			}
		}
		return $not_in_status;
	}
	
	public static function getGroup($status='')
	{
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "group_name";
		$criteria->condition = "stats_id IN  (
		  select stats_id from {{order_status}}
		  where description=:status
		)";
		$criteria->params = array(
		  ':status'=>$status
		);
		$models = AR_order_settings_tabs::model()->find($criteria);	
		if($models){
			return $models->group_name;
		}
		throw new Exception( 'no group buttons' );
	}
	
	public static function getPrintSettings()
	{
		$criteria=new CDbCriteria();
		$criteria->addInCondition('meta_name',array(
		  'receipt_logo','receipt_thank_you','receipt_footer'
		));
		$model=AR_admin_meta::model()->findAll($criteria);
		if($model){
			$data = array();
			foreach ($model as $item) {
				if($item->meta_name=="receipt_logo"){
					$item->meta_value = CMedia::getImage($item->meta_value,$item->meta_value1,
					Yii::app()->params->size_image ,
					CommonUtility::getPlaceholderPhoto('logo') );
				}
				$data[$item->meta_name] = $item->meta_value;
			}
			return $data;
		}
		return false;
	}
}
/*end class*/