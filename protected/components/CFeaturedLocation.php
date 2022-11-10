<?php
class CFeaturedLocation
{
	
	public static function Details($featured_name='')
	{		
		$model = AR_featured_location::model()->find('featured_name=:featured_name AND status=:status', 
		array(
		  ':featured_name'=>$featured_name,
		  ':status'=>'publish'
		)); 		
		if($model){
			return array(  
			  'location_name'=>Yii::app()->input->stripClean($model->location_name),
			  'latitude'=>$model->latitude,
			  'longitude'=>$model->longitude,
			);
		}
		throw new Exception( 'no results' );
	}
	
	public static function Listing($featured_name='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT 
		a.merchant_id,
		a.restaurant_name,
		a.restaurant_slug,
		a.logo,
		a.path,
		
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
		
		FROM {{merchant}} a		

		WHERE a.status='active'  AND a.is_ready ='2' 
		AND a.merchant_id IN (
		  select merchant_id from {{merchant_meta}}
		  where merchant_id = a.merchant_id 
		  and meta_name='featured'
		  and meta_value=".q($featured_name)."
		)	
		ORDER BY a.date_created ASC
		LIMIT 0,50			
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
			    
				$val2['restaurant_name'] = Yii::app()->input->xssClean($val2['restaurant_name']);
				$val2['cuisine_name'] = (array)$cuisine_list;
				$val2['ratings'] = $ratings;
				$val2['merchant_url']= Yii::app()->createAbsoluteUrl($val2['restaurant_slug']);				
				$val2['url_logo']= CMedia::getImage($val2['logo'],$val2['path'],Yii::app()->params->size_image,
				CommonUtility::getPlaceholderPhoto('merchant')
				);
				
				$data[] = $val2;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function services($featured_name='')
	{
		$data = array();
		$stmt="
		SELECT a.meta_value as service_name,
		a.merchant_id
		
		FROM {{merchant_meta}} a
		WHERE 
		a.merchant_id IN (
		  select merchant_id from {{merchant_meta}}
		  where merchant_id = a.merchant_id 
		  and meta_name='featured'
		  and meta_value=".q($featured_name)."
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
	
	public static function estimation($featured_name=0)
	{
	    $data = array();
		$stmt="
		SELECT merchant_id,service_code,charge_type,
		estimation
		FROM {{shipping_rate}} a
		WHERE merchant_id  IN (
		    select merchant_id from {{merchant_meta}}
		    where merchant_id = a.merchant_id 
		    and meta_name='featured'
		    and meta_value=".q($featured_name)."
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
	
}
/*end class*/