<?php
class CMerchantSignup
{
	public static function membershipProgram($lang = KMRS_DEFAULT_LANGUAGE , $filter=array() )
	{		
		/*$stmt="
		SELECT a.type_id, b.type_name, a.description,
		a.commision_type, a.commission, a.based_on
		FROM {{merchant_type}} a		
		LEFT JOIN {{merchant_type_translation}} b
		ON
		a.type_id = b.type_id
		WHERE b.language=".q($lang)."
		AND a.status = 'publish'
		ORDER BY a.type_id ASC
		";				
		dump($stmt);
		$dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{cache}}');
		if($res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll() ){		
			return $res;
		}
		throw new Exception( 'no results' );*/
		
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select="a.type_id, b.type_name, a.description, a.commision_type, a.commission, a.based_on";
		$criteria->join='LEFT JOIN {{merchant_type_translation}} b on  a.type_id=b.type_id ';
		$criteria->condition = "b.language=:language AND a.status=:status ";
		$criteria->params = array(
		  ':language'=>$lang,
		  ':status'=>'publish'
		);
		
		if(is_array($filter) && count($filter)>=1){
			$criteria->addInCondition('a.type_id', (array) $filter );
		}
		
		$model=AR_merchant_type::model()->findAll($criteria);
		if($model){
			$data = array();
			foreach ($model as $items) {
				$data[] = array(
				  'type_id'=>$items->type_id,
				  'type_name'=>$items->type_name,
				  'description'=>Yii::app()->input->xssClean($items->description),
				  'commision_type'=>$items->commision_type,
				  'commission'=>$items->commission,
				  'based_on'=>$items->based_on,
				);
			}
			return $data;
		}
		throw new Exception( 'no memberhisp program' );
	}
	
	public static function get($type_id=0)
	{
		$model = AR_merchant_type::model()->find('type_id=:type_id', 
		array(':type_id'=> intval($type_id) )); 	
		if($model){
			return $model;
		}
		return false;
	}
	
}
/*end class*/