<?php
class CCacheData
{
	public static function dependency()
	{
		$dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{cache}}');
		return $dependency;
	}
	
	public static function queryAll($stmt="", $table='merchant', $fields='date_modified')
	{
		if(Yii::app()->params->db_cache_enabled){		  		  	 
		  $res = Yii::app()->db->cache(Yii::app()->params->cache, self::dependency() )->createCommand($stmt)->queryAll();
		} else $res = Yii::app()->db->createCommand($stmt)->queryAll();		
		return $res;		
	}
	
	public static function queryRow($stmt="", $table='merchant', $fields='date_modified')
	{
		if(Yii::app()->params->db_cache_enabled){		  
		  $res = Yii::app()->db->cache(Yii::app()->params->cache,self::dependency())->createCommand($stmt)->queryRow();		  
		} else $res = Yii::app()->db->createCommand($stmt)->queryRow();		
		return $res;		
	}
	
	public static function add()
	{
		$model = AR_cache::model()->find( "id=:id" ,array(
		 ':id'=>1
		));
		if($model){
			$model->date_modified = CommonUtility::dateNow();
			$model->save();	
		} else {
			$cache = new AR_cache;
			$cache->date_modified = CommonUtility::dateNow();
			$cache->save();		
		}
	}
	
}
/*end class*/