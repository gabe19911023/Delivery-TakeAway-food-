<?php
class CMerchantListingV1
{
	public static function getMerchant($merchant_id='')
	{
		$dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{merchant}}');
		$model = AR_merchant::model()->cache(Yii::app()->params->cache, $dependency)->find('merchant_id=:merchant_id', 
		array(':merchant_id'=>$merchant_id)); 
		if($model){
			return $model;
		}
		throw new Exception( 'merchant not found' );
	}
	
	public static function getMerchantInfo($slug_name='',$lang='')
	{
		$stmt = "
		SELECT a.merchant_id, a.merchant_uuid,
		a.restaurant_name,a.description,a.logo,a.path,	
		a.header_image,a.footer_image,a.facebook,a.instagram,a.ticktok,a.path2,	
		a.address,
		a.restaurant_slug, a.latitude,a.lontitude,a.short_description,
		b.review_count,
		b.ratings,
		
		IFNULL((
		 select GROUP_CONCAT(DISTINCT cuisine_name SEPARATOR ';')
		 from {{view_cuisine}}
		 where language=".q($lang)."
		 and cuisine_id in (
		    select cuisine_id from {{cuisine_merchant}}
		    where merchant_id  = a.merchant_id
		 )		 
		),'') as cuisine_name
		
		FROM {{merchant}} a
		LEFT JOIN {{view_ratings}} b
		ON
		a.merchant_id = b.merchant_id
		
		WHERE restaurant_slug=".q($slug_name)."
		AND a.status='active'  AND a.is_ready ='2' 
		LIMIT 0,1
		";		
		if($res = CCacheData::queryRow($stmt) ){			
			$val2 = $res;						
			$cuisine_list = array(); $cuisine = '';
			$cuisine_name = explode(";",$res['cuisine_name']);	
			if(is_array($cuisine_name) && count($cuisine_name)>=1){
				foreach ($cuisine_name as $name) {
					$cuisine.= "&#8226; $name ";
				}								
			}
			unset($val2['cuisine_name']);
			$val2['restaurant_name'] = Yii::app()->input->xssClean($res['restaurant_name']);				
			$val2['merchant_address'] = Yii::app()->input->xssClean($res['address']);				
			$val2['url']= Yii::app()->createAbsoluteUrl($val2['restaurant_slug']);
			$val2['cuisine'] = (array)$cuisine_name;
			$val2['cuisine2'] = $cuisine;			
			$val2['url_logo'] = CMedia::getImage($res['logo'],$res['path'],"@2x",
				CommonUtility::getPlaceholderPhoto('item'));
			$val2['url_header'] = CMedia::getImage($res['header_image'],$res['path2'],"",
				CommonUtility::getPlaceholderPhoto('logo'));
			$val2['footer_image'] = CMedia::getImage($res['footer_image'],$res['path2'],"",
				'');	
			$val2['has_header']	 = !empty($res['header_image'])?true:false;
			$val2['latitude'] = $res['latitude'];
			$val2['lontitude'] = $res['lontitude'];
			$val2['facebook'] = $res['facebook'];
			$val2['instagram'] = $res['instagram'];
			$val2['ticktok'] = $res['ticktok'];
			$val2['delivery_estimation']='';
			return $val2;
		}
		throw new Exception( 'no results' );
	}

	public static function getGallery($merchant_id='')
	{		
		$criteria=new CDbCriteria;
		$criteria->condition = "merchant_id=:merchant_id AND meta_name=:meta_name";		    
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id),
		  ':meta_name'=>'merchant_gallery'
		);
		$criteria->order='meta_id ASC';
		$model = AR_merchant_meta::model()->findAll($criteria); 
		if($model){
			$data = array();
			foreach ($model as $val) {
				$data[] = array(
				  'thumbnail' =>CMedia::getImage($val['meta_value'],$val['meta_value1'],
				    Yii::app()->params->size_image_thumbnail,CommonUtility::getPlaceholderPhoto('gallery')),
				  'image_url' =>CMedia::getImage($val['meta_value'],$val['meta_value1'],
				    Yii::app()->params->size_image_medium,CommonUtility::getPlaceholderPhoto('gallery'))  
				);
			}
			return $data;
		}
		return false;
	}

	public static function openingHours($merchant_id='')
	{
		$stmt = "
		SELECT day,status,start_time,end_time,
		start_time_pm,end_time_pm,custom_text
		FROM {{opening_hours}}
		WHERE merchant_id=".q($merchant_id)."			
		ORDER BY day_of_week ASC
		";				
		if($res = CCacheData::queryAll($stmt) ){	
			$data = [];
			foreach ($res as $item) {				
				$item['start_time'] = Date_Formatter::Time($item['start_time']);
				$item['end_time'] = Date_Formatter::Time($item['end_time']);
				$item['start_time_pm'] = Date_Formatter::Time($item['start_time_pm']);
				$item['end_time_pm'] = Date_Formatter::Time($item['end_time_pm']);
				$data[]	= $item;
			}
			return $data;
		}
		return false;
	}
	
	public static function staticMapLocation($maps_credentials=array(),
	   $lat='', $lng='',$size='500x300',$icon='',$zoom=13,$scale=2,$format='png8')
	{
		$link = '';		
		if($maps_credentials){
			$api_keys = $maps_credentials['api_keys'];
			if($maps_credentials['map_provider']=="google.maps"){
				$link = "https://maps.googleapis.com/maps/api/staticmap";
				$link.= "?".http_build_query(array(
				  'center'=>"$lat,$lng",
				  'size'=>$size,
				  'zoom'=>$zoom,
				  'scale'=>$scale,
				  'format'=>$format,
				  'markers'=>"icon:$icon|$lat,$lng",
				  'key'=>$api_keys,				  
				));
			} else if ( $maps_credentials['map_provider']=="mapbox"  ) {
				
			}			
			return $link;
		}
		return false;
	}
	
    public static function mapDirection($maps_credentials=array(),$lat='', $lng='')
	{
		$link = '';
		if($maps_credentials){
			if($maps_credentials['map_provider']=="google.maps"){
				$link = "https://www.google.com/maps/dir/?api=1&destination=$lat,$lng";
			} else if ( $maps_credentials['map_provider']=="mapbox"  ) {
				
			}
			return $link;
		}
		return false;
	}

	public static function openHours($merchant_id='', $interval="20 mins")
	{
		$today = date('Y-m-d'); $order_by_days = ''; $daylist = array();
		$yesterday = date('Y-m-d', strtotime($today. " -1 days"));	
		$tomorrow = date('Y-m-d', strtotime($today. " +1 days"));		
		$current_time = date("Hi");
		$time_now = date("H:i",strtotime("+".intval($interval)." minutes"));
		$day_of_week = date("N");		

		for($i=1; $i<=7; $i++){			
			$days = date('l', strtotime($yesterday. " +$i days"));			
			$days = strtolower($days);	
			$order_by_days.=q($days).",";	
			$daylist[$days]= date('Y-m-d', strtotime($yesterday. " +$i days"));	 
		}
				
		$order_by_days = substr($order_by_days,0,-1);
		$stmt="
		SELECT day,start_time,end_time
		FROM {{delivery_times}}
		WHERE merchant_id=".q($merchant_id)."
		AND status='open'			
		ORDER BY FIELD(day, $order_by_days);	
		";					
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			$data = array(); $times = array();
			foreach ($res as $val) {	
								
				$start_time = date("Hi",strtotime($val['start_time']));		
				$item_start_time = $val['start_time'];
				
				$date = isset($daylist[$val['day']])?$daylist[$val['day']]:'';				
				$name = Date_Formatter::date($date,"EEE, MMM dd");
							
				if($today==$date){
					$name = t("Today").", $name";
					if($current_time>$start_time){								
						$item_start_time = self::blockMinutesRound($time_now, intval($interval) ); 						
					}
				} elseif ($tomorrow==$date){
					$name = t("Tomorrow").", $name";
					$item_start_time = date("H:i",strtotime($item_start_time." +".intval($interval)." minutes"));
				} else {
					$item_start_time = date("H:i",strtotime($item_start_time." +".intval($interval)." minutes"));
				}

				$end_time = $val['end_time'];						
				$end_time = date("H:i",strtotime($end_time." -".intval($interval)." minutes"));
				
				$time = self::createTimeRange($item_start_time,$end_time,$interval);				
				$times[$date][]=$time;
								
				if(is_array($time) && count($time)>=1){
				$data[$date] = array(
				  'name'=>$name,
				  'value'=>$date,				  
				  'data'=>$val,				  
				);
				}
			} //endfor				
			
			$_times = array();
			if(is_array($times) && count($times)>=1){
				foreach ($times as $key=>$item) {				
					$merge = array();
					for ($x = 0; $x <= count($item)-1; $x++) {				
						$merge += $item[$x];
					}
					$_times[$key] = $merge;
				}			
			}
			
			return array(
			  'dates'=>$data,
			  'time_ranges'=>$_times
			);
		}
		return false;
	}

	public static function pickupOpenHours($merchant_id='', $interval="20 mins")
	{
		$today = date('Y-m-d'); $order_by_days = ''; $daylist = array();
		$yesterday = date('Y-m-d', strtotime($today. " -1 days"));	
		$tomorrow = date('Y-m-d', strtotime($today. " +1 days"));		
		$current_time = date("Hi");
		$time_now = date("H:i",strtotime("+".intval($interval)." minutes"));
		$day_of_week = date("N");		

		for($i=1; $i<=7; $i++){			
			$days = date('l', strtotime($yesterday. " +$i days"));			
			$days = strtolower($days);	
			$order_by_days.=q($days).",";	
			$daylist[$days]= date('Y-m-d', strtotime($yesterday. " +$i days"));	 
		}
				
		$order_by_days = substr($order_by_days,0,-1);
		$stmt="
		SELECT day,start_time,end_time
		FROM {{opening_hours}}
		WHERE merchant_id=".q($merchant_id)."
		AND status='open'			
		ORDER BY FIELD(day, $order_by_days);	
		";					
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			$data = array(); $times = array();
			foreach ($res as $val) {	
								
				$start_time = date("Hi",strtotime($val['start_time']));		
				$item_start_time = $val['start_time'];
				
				$date = isset($daylist[$val['day']])?$daylist[$val['day']]:'';				
				$name = Date_Formatter::date($date,"EEE, MMM dd");
							
				if($today==$date){
					$name = t("Today").", $name";
					if($current_time>$start_time){								
						$item_start_time = self::blockMinutesRound($time_now, intval($interval) ); 						
					}
				} elseif ($tomorrow==$date){
					$name = t("Tomorrow").", $name";
					$item_start_time = date("H:i",strtotime($item_start_time." +".intval($interval)." minutes"));
				} else {
					$item_start_time = date("H:i",strtotime($item_start_time." +".intval($interval)." minutes"));
				}

				$end_time = $val['end_time'];						
				$end_time = date("H:i",strtotime($end_time." -".intval($interval)." minutes"));
				
				$time = self::createTimeRange($item_start_time,$end_time,$interval);				
				$times[$date][]=$time;
								
				if(is_array($time) && count($time)>=1){
				$data[$date] = array(
				  'name'=>$name,
				  'value'=>$date,				  
				  'data'=>$val,				  
				);
				}
			} //endfor				
			
			$_times = array();
			if(is_array($times) && count($times)>=1){
				foreach ($times as $key=>$item) {				
					$merge = array();
					for ($x = 0; $x <= count($item)-1; $x++) {				
						$merge += $item[$x];
					}
					$_times[$key] = $merge;
				}			
			}
			
			return array(
			  'dates'=>$data,
			  'time_ranges'=>$_times
			);
		}
		return false;
	}
	
	 public static function createTimeRange($start, $end, $interval = '30 mins', $format = '24') {
	    $startTime = strtotime($start); 
	    $startEnd = strtotime($start); 
	    $endTime   = strtotime($end);
	    $returnTimeFormat = ($format == '12')?'g:i:s A':'G:i:s';	    
	
	    $current   = time(); 
	    $addTime   = strtotime('+'.$interval, $current); 
	    $diff      = $addTime - $current;
	
	    $times = array(); 	    
	    while ($startTime < $endTime) { 	 
	    	$start_time =  date("H:i", $startTime);   		    		    		    	
	    	$startEnd  += $diff; 
	    	$start_end =  date("H:i", $startEnd);  
	        	        
	        $pretty_time = Date_Formatter::Time($startTime,"hh:mm a") . " - " . Date_Formatter::Time($startEnd,"hh:mm a"); 
	        
	        $times[] = array(
	          'start_time'=>date($returnTimeFormat, $startTime),
	          'end_time'=>date($returnTimeFormat, $startEnd),
	          'pretty_time'=>$pretty_time
	        );
	        $startTime += $diff; 
	    } 
	    
	    $start_time =  date("H:i", $startTime);  	       
	    return $times; 
	}    
	
	public static function blockMinutesRound($hour, $minutes = '5', $format = "H:i") {
	   $seconds = strtotime($hour);
	   $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
	   return date($format, $rounded);
	}

	//https://ourcodeworld.com/articles/read/756/how-to-round-up-down-to-nearest-10-or-5-minutes-of-datetime-in-php
	public static function roundToNearestMinuteInterval(\DateTime $dateTime, $minuteInterval = 10)
	{
	    return $dateTime->setTime(
	        $dateTime->format('H'),
	        round($dateTime->format('i') / $minuteInterval) * $minuteInterval,
	        0
	    );
	}	
	
	public static function getDistanceExp($filter=array())
	{
	    $distance_exp=3959;
		if ($filter['unit']=="km"){
			$distance_exp=6371;
		}
		return $distance_exp;			
	}
	
	public static function preFilter($filter=array())
	{				
		$and = '';		
		if(isset($filter['filters'])){
			foreach ($filter['filters'] as $filter_by=>$val) {
				switch ($filter_by) {
					
					case "transaction_type":
						    $and.="\n\n";
							$and.= "
							AND a.merchant_id IN (
							 select merchant_id from {{merchant_meta}}
							 where merchant_id = a.merchant_id
							 and meta_name='services' 
							 and meta_value=".q($val)."
							)
							";
						break;
						
					case "sortby":
						if($val=="sort_most_popular"){
							$and.="\n\n";
							$and.= "
							AND a.merchant_id IN (
							 select merchant_id from {{merchant_meta}}
							 where merchant_id = a.merchant_id
							 and meta_name='featured' 
							 and meta_value='popular'
							)
							";
						} elseif ($val=="sort_rating"){
							$and.="\n\n";
							$and.= "
							AND a.merchant_id IN (
							  select merchant_id from {{review}}
							  where merchant_id = a.merchant_id
							  and status = 'publish'
							)
							";
						} elseif ( $val=="sort_promo"){	
							$date_now = isset($filter['date_now'])?$filter['date_now']:'';
							$and.="\n\n";
							$and.= "
							AND a.merchant_id IN (
							  select merchant_id from {{offers}}
							  where merchant_id = a.merchant_id
							  and status = 'publish'
							  and ".q($date_now)." >= valid_from and ".q($date_now)." <= valid_to
							)
							";
						} elseif ($val=="sort_free_delivery"){
							$and.="\n\n";
							$and.="
							AND a.merchant_id IN (
							  select merchant_id from {{option}}
							  where merchant_id = a.merchant_id
							  and option_name='free_delivery_on_first_order'
							  and option_value=1
							)
							";
						}
						break;
				
					case "price_range":				
					     if(!empty($val)){
							 $based_price = str_pad(9, intval($val) ,9);	
							 $and.="\n\n";
							 $and.=" AND a.merchant_id IN (
							  select merchant_id from {{item_relationship_size}}
							  where price <=".q($based_price)."
							  and available = 1
							 )
						    ";
					     }
						break;
					
					case "cuisine":		
					    if(is_array($val) && count($val)>=1){
					    	$in = '';
					    	foreach ($val as $cuisine_id) {
					    		$in.=q(intval($cuisine_id)).",";
					    	}
					    	$in = substr($in,0,-1);
					    	if(!empty($in)){
								$and.="\n\n";
								$and.=" AND a.merchant_id IN (
								 select merchant_id from {{cuisine_merchant}}
								 where merchant_id = a.merchant_id				 
								 and cuisine_id IN ($in)
							   )";		 
						   }
					    }
					    break;
							
					case "max_delivery_fee":    
					    $max_delivery_fee = floatval($val);
					    if($max_delivery_fee>0){
					    	$and.="\n\n";
					    	$and.="
					    	AND a.merchant_id IN (
					    	  select merchant_id
					    	  from {{shipping_rate}}
					    	  where distance_price between 1 and ".q($max_delivery_fee)."
					    	  and service_code='delivery'
					    	  and charge_type  = (
					    	    select option_value  
					    	    from {{option}}	
					    	    where merchant_id = a.merchant_id
					    	    and option_name='merchant_delivery_charges_type'					    	    
					    	  )
					    	)
					    	";
					    }
					    break;
					    
					case "rating":    
					    $rating = intval($val);					    
					    if($rating>0){
					    	$and.="\n\n";
							$and.= "
							AND a.merchant_id IN (
							  select merchant_id from {{view_ratings}}
							  where merchant_id = a.merchant_id
							  and ratings>=".q($rating)."
							)
							";							
					    }					    
					    break;
					    
					default:
						break;
				}
			}
		}		
		return $and;
	}
	
	public static function getLocalID($local_id='')
	{		
		if(!empty($local_id)){
			$dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{map_places}}');
			$model = AR_map_places::model()->cache( Yii::app()->params->cache , $dependency)->find("reference_id=:reference_id",array(
			  ':reference_id'=>$local_id		  
			));	
			/*$model = AR_map_places::model()->find("reference_id=:reference_id",array(
			  ':reference_id'=>$local_id		  
			));	*/
			if($model){
				return $model;
			}
		} else throw new Exception( 'Place id is empty' );
		throw new Exception( 'Place id not found' );
	}
	
	public static function preSearch($filter=array())
	{
		if(!is_array($filter) && count($filter)<=0){
			throw new Exception( 'Invalid filter' );
		}
		if(empty($filter['lat']) || empty($filter['lng']) ){
			throw new Exception( 'Invalid coordinates' );
		}
		if(empty($filter['unit'])){
			throw new Exception( 'Invalid distance unit' );
		}
		
		$distance_exp = self::getDistanceExp($filter);
		$and = self::preFilter($filter);
					
		$stmt="
		SELECT count(*) as total		
		FROM {{merchant}} a 					
		WHERE a.status='active' AND a.is_ready ='2' 		
		AND a.delivery_distance_covered > (
		  ( $distance_exp * acos( cos( radians($filter[lat]) ) * cos( radians( latitude ) ) 
		   * cos( radians( lontitude ) - radians($filter[lng]) ) 
		  + sin( radians($filter[lat]) ) * sin( radians( latitude ) ) ) ) 
		)
		$and
		";								
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){			
			return $res['total'];
		}
		throw new Exception( 'no results' );
	}

	/*
	@parameters
	
	'filters':{
			   	  'cuisine': this.cuisine,
			   	  'sortby' : this.sortby,
			   	  'price_range': this.price_range,
			   	  'max_delivery_fee': this.max_delivery_fee,
			   },	
			   
	$filter = array(
		    'lat'=>$local_info->latitude,
		    'lng'=>$local_info->longitude,
		    'unit'=>Yii::app()->params['settings']['home_search_unit_type'],
		    'limit'=>intval(Yii::app()->params->list_limit),
		    'today_now'=>strtolower(date("l")),
		    'time_now'=>date("H:i"),
		    'date_now'=>date("Y-m-d"),
		    'filters'=>$filters,
		  );
	*/
	public static function Search($filter=array(), $lang = KMRS_DEFAULT_LANGUAGE )
	{
		if(!is_array($filter) && count($filter)<=0){
			throw new Exception( 'Invalid filter' );
		}
		if(empty($filter['lat']) || empty($filter['lng']) ){
			throw new Exception( 'Invalid coordinates' );
		}
		if(empty($filter['unit'])){
			throw new Exception( 'Invalid distance unit' );
		}
		if(empty($filter['limit'])){
			throw new Exception( 'Invalid limit' );
		}
		
		$distance_exp = self::getDistanceExp($filter);
		$and = self::preFilter($filter);
		
		$query_distance = ",
		( $distance_exp * acos( cos( radians($filter[lat]) ) * cos( radians( latitude ) ) 
		* cos( radians( lontitude ) - radians($filter[lng]) ) 
		+ sin( radians($filter[lat]) ) * sin( radians( latitude ) ) ) ) 
		AS distance
		";
		
		$stmt="
		SELECT 
		a.merchant_id,
		a.restaurant_name,
		a.restaurant_slug,
		a.logo,
		a.delivery_distance_covered,
		a.status,a.is_ready,
		a.close_store,
		a.disabled_ordering,
		a.pause_ordering,		
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
		) as free_delivery,
		
		(
		select COUNT(DISTINCT(merchant_id))
		from {{favorites}}
		where merchant_id = a.merchant_id
		and client_id=".q($filter['client_id'])."
		and fav_type='restaurant'
		) as saved_store,
		
		(
		select GROUP_CONCAT(day_of_week,';',start_time,';',end_time order by day_of_week asc)
		from {{opening_hours}}
		where merchant_id = a.merchant_id
		and day_of_week>=".q(intval($filter['day_of_week']))."
		and CAST(".q($filter['time_now'])." AS TIME) < CAST(end_time AS TIME)
		and status='open'		
		) as next_opening
		
		$query_distance	
		
		,(
			select count(*) from
			{{opening_hours}}
			where
			merchant_id = a.merchant_id
			and
			day=".q($filter['today_now'])."
			and
			status = 'open'
			and 
			
			(
			CAST(".q($filter['time_now'])." AS TIME)
			BETWEEN CAST(start_time AS TIME) and CAST(end_time AS TIME)
			
			or
			
			CAST(".q($filter['time_now'])." AS TIME)
			BETWEEN CAST(start_time_pm AS TIME) and CAST(end_time_pm AS TIME)
			
			)
			
		) as merchant_open_status
		
		FROM {{merchant}} a		
		HAVING distance < a.delivery_distance_covered
		AND a.status='active'  AND a.is_ready ='2'		
		$and
		ORDER BY close_store,disabled_ordering,pause_ordering ASC, merchant_open_status+0 DESC, is_sponsored DESC, distance ASC		
		LIMIT $filter[offset],$filter[limit]
		";				
		if($res = CCacheData::queryAll($stmt)){						
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
				
				/*next_opening*/	
				$next_opening = '';
				if(!empty($val['next_opening'])){
					$next_open = explode(",",$val['next_opening']);							
					if(is_array($next_open) && count($next_open)>=1){
						$next_open = isset($next_open[0])?$next_open[0]:'';						
						$next_open = explode(";",$next_open);	
																		
						$next_open_date = self::getDayWeek($filter['date_now'],$next_open[0]);
						$next_open_date ="$next_open_date $next_open[1]";
											
						$next_opening = t("Opens [day] at [time]",array(
						 '[day]'=>Date_Formatter::date($next_open_date,"E"),
						 '[time]'=>Date_Formatter::Time($next_open_date,"h:mm a")
						));
					}
				}
				
			    
				$val2['restaurant_name'] = Yii::app()->input->xssClean($val2['restaurant_name']);
				$val2['cuisine_name'] = (array)$cuisine_list;
				$val2['ratings'] = $ratings;
				$val2['merchant_url']= Yii::app()->createAbsoluteUrl($val2['restaurant_slug']);				
				$val2['url_logo']= CMedia::getImage($val2['logo'],$val2['path'],Yii::app()->params->size_image_medium,
				CommonUtility::getPlaceholderPhoto('item'));
				$val2['next_opening'] = $next_opening;				
				$data[] = $val2;
			}
			return $data;
		} else throw new Exception( 'no results' );		
	}
	
	public static function getDayWeek($date='',$day=0)
	{
		$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday');
		if(isset($days[$day])){
		   return date('Y-m-d', strtotime($days[$day], strtotime($date)));
		}
	}
	
    public static function services($filter='')
	{
		$distance_exp = self::getDistanceExp($filter);
		
		$data = array();
		$stmt="
		SELECT a.meta_value as service_name,
		a.merchant_id
		
		FROM {{merchant_meta}} a
		WHERE 
		a.merchant_id IN (
		    SELECT merchant_id
			FROM {{merchant}} a 					
			WHERE a.status='active' AND a.is_ready ='2' 		
			AND a.delivery_distance_covered > (
			  ( $distance_exp * acos( cos( radians($filter[lat]) ) * cos( radians( latitude ) ) 
			   * cos( radians( lontitude ) - radians($filter[lng]) ) 
			  + sin( radians($filter[lat]) ) * sin( radians( latitude ) ) ) ) 
			)
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
	
    public static function estimation($filter=array())
	{
		$distance_exp = self::getDistanceExp($filter);
		
	    $data = array();
		$stmt="
		SELECT merchant_id,service_code,charge_type,
		estimation,shipping_type
		FROM {{shipping_rate}} a
		WHERE
		shipping_type='standard'
		AND merchant_id  IN (
		    SELECT merchant_id
			FROM {{merchant}} a 					
			WHERE a.status='active' AND a.is_ready ='2' 		
			AND a.delivery_distance_covered > (
			  ( $distance_exp * acos( cos( radians($filter[lat]) ) * cos( radians( latitude ) ) 
			   * cos( radians( lontitude ) - radians($filter[lng]) ) 
			  + sin( radians($filter[lat]) ) * sin( radians( latitude ) ) ) ) 
			)
		)
		";						
		$dependency = CCacheData::dependency();	
		if($res = Yii::app()->db->cache(Yii::app()->params->cache,$dependency)->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {
				$data[$val['merchant_id']][$val['service_code']][$val['charge_type']] = array(
				  'service_code'=>$val['service_code'],
				  'charge_type'=>$val['charge_type'],
				  'estimation'=>$val['estimation'],
				  'shipping_type'=>$val['shipping_type']
				);
			}
			return $data;
		}
		return false;
	}		
	
	public static function estimationMerchant($filter=array())
	{
		$distance_exp = self::getDistanceExp($filter);
		
	    $data = array();
		$stmt="
		SELECT merchant_id,service_code,charge_type,
		estimation,shipping_type
		FROM {{shipping_rate}} a
		WHERE
		shipping_type=".q($filter['shipping_type'])."
		AND merchant_id  IN (
		    SELECT merchant_id
			FROM {{merchant}} a 					
			WHERE a.status='active' AND a.is_ready ='2' 	
			AND merchant_id = ".intval($filter['merchant_id'])."	
			AND a.delivery_distance_covered > (
			  ( $distance_exp * acos( cos( radians($filter[lat]) ) * cos( radians( latitude ) ) 
			   * cos( radians( lontitude ) - radians($filter[lng]) ) 
			  + sin( radians($filter[lat]) ) * sin( radians( latitude ) ) ) ) 
			)
		)
		";						
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){				
			foreach ($res as $val) {
				$data[$val['service_code']][$val['charge_type']] = array(
				  'service_code'=>$val['service_code'],
				  'charge_type'=>$val['charge_type'],
				  'estimation'=>$val['estimation'],
				  'shipping_type'=>$val['shipping_type']
				);
			}
			return $data;
		}
		return false;
	}		
	
	/*
	$filter = array(			
	  'search'=>$q,
	  'lat'=>$local_info->latitude,
	  'lng'=>$local_info->longitude,
	  'unit'=>Yii::app()->params['settings']['home_search_unit_type']  
	);
	*/
	public static function searchSuggestion($filter=array(), $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$query_distance='';	$where = ''; $and= '';	 	
		$distance_exp = self::getDistanceExp($filter);
		$query = isset($filter['search'])?$filter['search']:'';
		$page = isset($filter['page'])?intval($filter['page']):0;
		$limit = isset($filter['limit'])?intval($filter['limit']):10;
		
		$unit = isset($filter['unit'])?$filter['unit']:'mi';
		$lat = isset($filter['lat'])?$filter['lat']:'';
		$lng = isset($filter['lng'])?$filter['lng']:'';
		
		if(!empty($lat) && !empty($lng)){
			$query_distance = "
			( $distance_exp * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
			* cos( radians( lontitude ) - radians($lng) ) 
			+ sin( radians($lat) ) * sin( radians( latitude ) ) ) ) 
			AS distance,
			";
			$where='HAVING distance < a.delivery_distance_covered';
			//$and = "OR cuisine_name LIKE ".q("%$query%")." ";
		} else $where = "WHERE 1";
		
		if(empty($query)){
			throw new Exception( 'no results' );
		}
		
		$stmt = "
		SELECT a.restaurant_slug as slug,a.restaurant_name as title,
		a.logo,a.path,a.delivery_distance_covered,a.status,a.is_ready,
		
		$query_distance
		
		IFNULL((
		 select GROUP_CONCAT(cuisine_name,';',color_hex,';',font_color_hex)
		 from {{view_cuisine}}
		 where language=".q($lang)."
		 and cuisine_id in (
		    select cuisine_id from {{cuisine_merchant}}
		    where merchant_id  = a.merchant_id
		 )		 		 
		),'') as cuisine_name
		
		FROM {{merchant}} a
		$where
		AND restaurant_name LIKE ".q("%$query%")."
		AND a.status='active'  AND a.is_ready ='2' 		
		$and
		ORDER BY a.restaurant_name ASC
		LIMIT $page,$limit
		";								
		if( $res = CCacheData::queryAll($stmt)){
			$data = array();
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
				
				$val2['title'] = Yii::app()->input->xssClean($val2['title']);
				$val2['cuisine_name'] = (array)$cuisine_list;
				$val2['url']= Yii::app()->createAbsoluteUrl($val2['slug']);				
				$val2['url_logo'] = CMedia::getImage($val2['logo'],$val2['path'],"@2x",
				CommonUtility::getPlaceholderPhoto('merchant'));
				$data[] = $val2;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}

	public static function searchSuggestionFood($filter=array(), $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$query_distance='';	$where = ''; $and= '';	 	
		$distance_exp = self::getDistanceExp($filter);
		$query = isset($filter['search'])?$filter['search']:'';
		$page = isset($filter['page'])?intval($filter['page']):0;
		$limit = isset($filter['limit'])?intval($filter['limit']):10;
		
		$unit = isset($filter['unit'])?$filter['unit']:'mi';
		$lat = isset($filter['lat'])?$filter['lat']:'';
		$lng = isset($filter['lng'])?$filter['lng']:'';
		
		if(!empty($lat) && !empty($lng)){
			$query_distance = "
			( $distance_exp * acos( cos( radians($lat) ) * cos( radians( latitude ) ) 
			* cos( radians( lontitude ) - radians($lng) ) 
			+ sin( radians($lat) ) * sin( radians( latitude ) ) ) ) 
			AS distance
			";
			$where='HAVING distance < a.delivery_distance_covered';			
		} else $where = "WHERE 1";
		
		if(empty($query)){
			throw new Exception( 'no results' );
		}

		$stmt = "
		SELECT a.item_name as title, b.slug, b.photo, b.path,
		b.merchant_id,

		IFNULL((
			select GROUP_CONCAT(cuisine_name,';',color_hex,';',font_color_hex)
			from {{view_cuisine}}
			where language=".q($lang)."
			and cuisine_id in (
			   select cuisine_id from {{cuisine_merchant}}
			   where merchant_id  = b.merchant_id
			)		 		 
		),'') as cuisine_name

		FROM {{item_translation}} a
		LEFT JOIN {{item}} b
		ON 
		a.item_id = b.item_id

		WHERE a.item_name LIKE ".q("%$query%")."
		AND  a.language=".q($lang)."
		AND b.merchant_id IN (
			select merchant_id
			from {{merchant}}
			where delivery_distance_covered > (
				select 
			    $query_distance from {{merchant}}
				where merchant_id = b.merchant_id
				AND status='active'  AND is_ready ='2' 		
			)
		)
		";						
		
		if( $res = CCacheData::queryAll($stmt)){
			$data = array();
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
								
				$val2['title'] = Yii::app()->input->xssClean($val2['title']);
				$val2['cuisine_name'] = (array)$cuisine_list;
				$val2['url']= Yii::app()->createAbsoluteUrl($val2['slug']);				
				$val2['url_logo'] = CMedia::getImage($val2['photo'],$val2['path'],"@2x",
				CommonUtility::getPlaceholderPhoto('item'));
				$data[] = $val2;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function checkStoreOpen($merchant_id=0, $date_now='', $time_now='')
	{
		$day_of_week = strtolower(date("N",strtotime($date_now)));
		$today_now = strtolower(date("l",strtotime($date_now)));
		
		$stmt="
		SELECT a.merchant_id,
		
		(
		select GROUP_CONCAT(day_of_week,';',start_time,';',end_time order by day_of_week asc)
		from {{opening_hours}}
		where merchant_id = a.merchant_id
		and day_of_week>=".q(intval($day_of_week))."
		and status='open'		
		) as next_opening,
		
		(
			select count(*) from
			{{opening_hours}}
			where
			merchant_id = a.merchant_id
			and
			day=".q($today_now)."
			and
			status = 'open'
			and 
			
			(
			CAST(".q($time_now)." AS TIME)
			BETWEEN CAST(start_time AS TIME) and CAST(end_time AS TIME)
			
			or
			
			CAST(".q($time_now)." AS TIME)
			BETWEEN CAST(start_time_pm AS TIME) and CAST(end_time_pm AS TIME)
			
			)
			
		) as merchant_open_status
		
		FROM {{merchant}} a
		WHERE merchant_id = ".q($merchant_id)."
		";									
		if($res=Yii::app()->db->createCommand($stmt)->queryRow()){
			/*next_opening*/	
			$next_opening = '';
			if(!empty($res['next_opening'])){
				$next_open = explode(",",$res['next_opening']);							
				if(is_array($next_open) && count($next_open)>=1){
					$next_open = isset($next_open[0])?$next_open[0]:'';						
					$next_open = explode(";",$next_open);	
																	
					$next_open_date = self::getDayWeek($date_now,$next_open[0]);
					$next_open_date ="$next_open_date $next_open[1]";
										
					$next_opening = t("Opens [day] at [time]",array(
					 '[day]'=>Date_Formatter::date($next_open_date,"E"),
					 '[time]'=>Date_Formatter::Time($next_open_date,"h:mm a")
					));
				}
			}

			$res['next_opening'] = $next_opening;
			return $res;
		}
		throw new Exception( 'no results' );
	}
	
	public static function checkCurrentTime($datetime_now='', $datetime_to='')
	{		
		$diff = CommonUtility::dateDifference($datetime_to,$datetime_now);				
		if(is_array($diff) && count($diff)>=1){
			if($diff['days']>0){
			   throw new Exception( "Selected delivery time is already past" );	
			}			
			if($diff['hours']>0){
			   throw new Exception( "Selected delivery time is already past" );	
			}			
			if($diff['minutes']>1){
			   throw new Exception( "Selected delivery time is already past" );	
			}			
		}
		return true;
	}
	
	public static function storeAvailable($merchant_uuid='')
	{
		$merchant = CMerchants::getByUUID($merchant_uuid);
		$message = t("Currently unavailable");
		if($merchant->close_store>0){
             throw new Exception( $message );	
         } elseif ( $merchant->pause_ordering>0){
             $meta = AR_merchant_meta::getValue($merchant->merchant_id,'pause_reason');
             if($meta){			 		                  
                  throw new Exception( !empty($meta['meta_value'])?$meta['meta_value']:$message );	
             } else throw new Exception( $message );
         } 
         return true;
	}
	
	public static function storeAvailableByID($merchant_id='')
	{
		$merchant = CMerchants::get($merchant_id);
		$message = t("Currently unavailable");
		if($merchant->close_store>0){
             throw new Exception( $message );	
        } elseif ( $merchant->pause_ordering>0){
             $meta = AR_merchant_meta::getValue($merchant->merchant_id,'pause_reason');
             if($meta){			 		                  
                  throw new Exception( !empty($meta['meta_value'])?$meta['meta_value']:$message );	
             } else throw new Exception( $message );
        } 
        return true;
	}
	
}
/*end class*/