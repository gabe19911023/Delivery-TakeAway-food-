<?php
class ClocationCountry
{
	
	public static function listing($filter=array())
	{
		$where="";
		$only_countries = isset($filter['only_countries'])?$filter['only_countries']:'';		
		if($country = CommonUtility::arrayToQueryParameters($only_countries)){
			$where = "WHERE shortcode IN ($country) ";
		}
	    $stmt="
	    SELECT shortcode,country_name,phonecode
	    FROM {{location_countries}}
	    $where
	    ORDER BY shortcode ASC
	    ";
	    
	    $dependency = new CDbCacheDependency('SELECT MAX(country_id) FROM {{location_countries}}');
	    if($res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll() ){
	    	$data = array();
	    	foreach ($res as $val) {
	    		$val['flag']= Yii::app()->createAbsoluteUrl("themes/".Yii::app()->theme->name."/assets/flag/".strtolower($val['shortcode']).".svg");
	    		$data[]=$val;
	    	}
	    	return $data;
	    }
	    throw new Exception( 'no results' );
	}
	
	public static function get($shortcode='')
	{
		 $dependency = new CDbCacheDependency('SELECT MAX(country_id) FROM {{location_countries}}');
	     $model = AR_country::model()->cache(Yii::app()->params->cache, $dependency)->find('shortcode=:shortcode', 
		 array(':shortcode'=>$shortcode)); 	
		 if($model){
		 	return array(
		 	  'shortcode'=>$model->shortcode,
		 	  'country_name'=>$model->country_name,
		 	  'phonecode'=>$model->phonecode,
		 	  'flag'=>Yii::app()->createAbsoluteUrl("themes/".Yii::app()->theme->name."/assets/flag/".strtolower($model->shortcode).".svg"),
		 	);
		 }
		 return false;
	}
	
}
/*end class*/