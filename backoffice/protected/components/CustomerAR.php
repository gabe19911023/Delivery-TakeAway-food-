<?php
/*
*/
class CustomerAR extends CComponent
{
	public static function tableName()
	{
		return '{{client}}';
	}
	
	public static function search($search='')
	{
		$where ="WHERE status='active'";
		if (!empty($search)){
			$where = " WHERE (
			  first_name LIKE ".q("%$search%")."
			  OR
			  last_name LIKE ".q("%$search%")."
			)";
		}
		
		$stmt="
		SELECT client_id as id,
		concat(first_name,' ',last_name) as text
		FROM ".self::tableName()."
		$where
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$val['text'] = stripslashes(Yii::app()->input->stripClean($val['text']));
				$data[]=$val;
			}			
			return $data;
		}		
		throw new Exception('no results');		
	}
	
	public static function getSelected($ids=array())
	{		
		$data = array();
		if(is_array($ids) && count($ids)>=1){
			$stmt="
			SELECT client_id as id, 
	    	concat(first_name,' ',last_name) as text
	    	FROM {{client}}
	    	WHERE client_id IN (".implode($ids,",").")
			";			
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				foreach ($res as $val) {					
					$data[$val['id']] = stripslashes($val['text']);
				}
			}
		}		
		return $data;
	}
	
	public static function RecentRegistered($limit=5)
	{
		$stmt = "
		SELECT 
		a.client_id,
		concat(a.first_name,' ',a.last_name) as customer_name,
		a.status,a.email_address,a.avatar,
		
		IFNULL(concat(b.street,' ',b.city,' ',b.state,' ',b.zipcode,' ',b.country_code) 
		,'".t('Address not available')."')as customer_address,
		
		IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='customer'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title	
				
		FROM ".self::tableName()." a	
		LEFT JOIN {{address_book}} b
		ON
		a.client_id = b.client_id
		
		ORDER BY a.client_id DESC
		LIMIT 0,$limit
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			return $res;
		}				
		return false;	
	}
	
}
/*end class*/