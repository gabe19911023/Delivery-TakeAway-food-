<?php
class OptionsTools
{
	public static $merchant_id=0;
	
	public static function save($option=array(),$data , $merchant_id=0)
	{
		self::delete($option);
		$params = array();
		foreach ($option as $option_name) {			
			$params[]=array(
			  'merchant_id'=>$merchant_id,
			  'option_name'=>$option_name,
			  'option_value'=>$data[$option_name],			  
			);			
		}				
		$builder=Yii::app()->db->schema->commandBuilder;
		$command=$builder->createMultipleInsertCommand("{{option}}",$params);
		$command->execute();
		
		CCacheData::add();
		
		return true;
	}
	
	public static function delete($option=array())
	{
		$in = '';
		if(is_array($option) && count($option)>=1){
			foreach ($option as $option_name) {
				$in.=q($option_name).",";
			}
			$in = substr($in,0,-1);
			$stmt="
			DELETE FROM {{option}}
			WHERE merchant_id=".q(self::$merchant_id)."
			AND option_name IN ($in)
			";			
			Yii::app()->db->createCommand($stmt)->query();
			
			CCacheData::add();
		}		
	}
	
	public static function find($options_name=array(),$merchant_id=0)
	{
		$que=''; $data=array();
		if(is_array($options_name) && count($options_name)>=1){
			foreach ($options_name as $key=>$val) {
				$que.=q($val).",";
			}
			$que = substr($que,0,-1);
		}
		$stmt="
		SELECT option_name,option_value
		FROM {{option}}		
		WHERE option_name IN ($que)
		AND merchant_id=".q($merchant_id)."
		";				
		$dependency = CCacheData::dependency();
		if($resp = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll()){				
			foreach ($resp as $val) {				
				$data[$val['option_name']]=$val['option_value'];
			}
			return $data;
		}
		return false;
	}
	
}
/*end class*/