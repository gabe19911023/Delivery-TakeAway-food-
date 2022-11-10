<?php
class CSavedStore
{
	
	public static function getStoreReview($merchant_id=0, $client_id=0)
	{
		$model = AR_favorites::model()->find('merchant_id=:merchant_id AND client_id=:client_id', 
		array(
		  ':merchant_id'=>$merchant_id,
		  ':client_id'=>$client_id
		)); 		
		if($model){
			return array( 
			  'id'=>$model->id,
			  'merchant_id'=>$model->merchant_id,
			  'client_id'=>$model->client_id,
			);
		}
		throw new Exception( 'Record not found' );
	}
	
	
	public static function Listing($client=0, $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$data = array();
		$stmt="
		SELECT 
		a.merchant_id,
		b.restaurant_name,
		b.restaurant_slug,
		b.logo,
		b.path,
		
		IFNULL((
		 select GROUP_CONCAT(cuisine_name,';',color_hex,';',font_color_hex)
		 from {{view_cuisine}}
		 where language=".q($lang)."
		 and cuisine_id in (
		    select cuisine_id from {{cuisine_merchant}}
		    where merchant_id  = a.merchant_id
		 )		 		 
		),'') as cuisine_name,
				
		(
		select concat(review_count,';',ratings) as ratings from {{view_ratings}}
		where merchant_id = a.merchant_id
		) as ratings,
		
		(
		select option_value
		from {{option}}
		where option_name='merchant_delivery_charges_type'
		and merchant_id = a.merchant_id
		) as charge_type,
		
		(
		select option_value
		from {{option}}
		where option_name='free_delivery_on_first_order'
		and merchant_id = a.merchant_id
		) as free_delivery
		
		FROM {{favorites}} a
		LEFT JOIN {{merchant}} b
		ON
		a.merchant_id = b.merchant_id
		
		WHERE a.client_id = ".q( intval($client) )."
		AND a.fav_type = 'restaurant'
		AND a.merchant_id IN (
		  select merchant_id from {{merchant}}
		  where merchant_id = a.merchant_id
		)
		ORDER BY a.id DESC
		LIMIT 0,100
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {				
				$val2 = $val;	
				$cuisine_list = array();
				$cuisine_name = explode(",",$val['cuisine_name']);
				if(is_array($cuisine_name) && count($cuisine_name)>=1){
					foreach ($cuisine_name as $cuisine_val) {						
						$cuisine = explode(";",$cuisine_val);								
						$cuisine_list[]=array(
						  'cuisine_name'=>isset($cuisine[0])?Yii::app()->input->xssClean($cuisine[0]):'',
						  'bgcolor'=>isset($cuisine[1])?  !empty($cuisine[1])?$cuisine[1]:'#ffd966'  :'#ffd966',
						  'fncolor'=>isset($cuisine[2])? !empty($cuisine[2])?$cuisine[2]:'#ffd966' :'#000',
						);
					}
				}
				
				$ratings = array();
				if($rate = explode(";",$val['ratings'])){
				   $ratings = array(
				     'review_count'=>isset($rate[0])?intval($rate[0]):0,
				     'rating'=>isset($rate[1])?intval($rate[1]):0,
				   );
				}				
			    
				$val2['saved_store']=true;
				$val2['restaurant_name'] = Yii::app()->input->xssClean($val2['restaurant_name']);
				$val2['cuisine_name'] = (array)$cuisine_list;
				$val2['ratings'] = $ratings;
				$val2['merchant_url']= Yii::app()->createAbsoluteUrl($val2['restaurant_slug']);
				$val2['url_logo']= CMedia::getImage($val['logo'],$val['path'],Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('item'));
				
				$data[] = $val2;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function services($client_id=0)
	{
		$data = array();
		$stmt="
		SELECT a.meta_value as service_name,
		a.merchant_id
		
		FROM {{merchant_meta}} a
		WHERE 
		a.merchant_id IN (
		  select merchant_id 
		  from {{favorites}}
		  where merchant_id  = a.merchant_id
		  and client_id = ".q($client_id)."
		)
		AND meta_name ='services'
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {
				$data[$val['merchant_id']][] = $val['service_name'];
			}
			return $data;
		} 
		return false;
	}
	
	public static function estimation($client_id=0)
	{
	    $data = array();
		$stmt="
		SELECT merchant_id,service_code,charge_type,
		estimation
		FROM {{shipping_rate}} a
		WHERE merchant_id  IN (
		   select merchant_id 
		   from {{favorites}}
		   where merchant_id  = a.merchant_id
		   and client_id = ".q($client_id)."
		)
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {
				$data[$val['merchant_id']][$val['service_code']][$val['charge_type']] = array(
				  'service_code'=>$val['service_code'],
				  'charge_type'=>$val['charge_type'],
				  'estimation'=>$val['estimation'],
				);
			}
			return $data;
		}
		return false;
	}

	public static function getSaveItemsByMerchant($client_id=0,$merchant_id=0)
	{
		$model = AR_favorites::model()->findAll("client_id=:client_id AND merchant_id=:merchant_id
		AND fav_type=:fav_type",[
			':client_id'=>intval($client_id),
			':merchant_id'=>intval($merchant_id),
			':fav_type'=>"item"
		]);
		if($model){
			foreach ($model as $items) {				
				$data[]=[
					'cat_id'=>$items->cat_id,
					'item_id'=>$items->item_id,
					'save_item'=>true
				];
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}

	public static function getSaveItems($client_id=0,$merchant_id=0,$item_id=0)
	{
		$model = AR_favorites::model()->find("client_id=:client_id AND merchant_id=:merchant_id
		AND item_id=:item_id
		AND fav_type=:fav_type",[
			':client_id'=>intval($client_id),
			':merchant_id'=>intval($merchant_id),
			':item_id'=>intval($item_id),
			':fav_type'=>"item"
		]);
		if($model){			
			return $model;
		}
		throw new Exception( 'no results' );
	}
		
}
/*end class*/