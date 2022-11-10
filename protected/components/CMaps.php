<?php
class CMaps{
	
	public static function config()
	{		
		
		if(isset(Yii::app()->params['settings']['map_provider'])){
			MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];	
			MapSdk::setKeys(array(
		     'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
		     'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
		    ));
		    
			return array(
			  'provider'=>MapSdk::$map_provider,
			  'key'=>MapSdk::$api_key,
			  'zoom'=>15,		  
			  'icon'=>websiteDomain().Yii::app()->theme->baseUrl."/assets/images/marker2@2x.png",
			  'icon_merchant'=>websiteDomain().Yii::app()->theme->baseUrl."/assets/images/restaurant-icon1.png",
			  'icon_destination'=>websiteDomain().Yii::app()->theme->baseUrl."/assets/images/home-icon1.png",
			  'default_lat'=> '34.04703',
			  'default_lng'=> '-118.246860',
			);
		}
		return false;
	}
	
	public static function locationDetails($place_id='',$country_code='')
	{
		try {
			
			if(empty($place_id)){
				throw new Exception( 'Place ID is empty' );
			}
			
			if(!isset(Yii::app()->params['settings']['map_provider'])){
				throw new Exception( 'Map provider not set' );
			}
			
			$resp = array();
			MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];	
			 
			$reference_type = MapSdk::$map_provider;
		    $model = AR_map_places::model()->find('reference_type=:reference_type AND reference_id=:reference_id', 
		    array(':reference_type'=>$reference_type, ':reference_id'=>$place_id )); 
		    if($model){		    	
		       $resp = array(
		   	    'address'=>array(
		   	       'address1'=>$model->address1,
		   	       'address2'=>$model->address2,
		   	       'country'=>$model->country,
		   	       'country_code'=>$model->country_code,
		   	       'postal_code'=>$model->postal_code,
		   	       'street_number'=>$model->street_number,
		   	       'city'=>$model->city,
		   	       'state'=>$model->state,
		   	       'address'=>$model->address,
		   	       'formatted_address'=>$model->formatted_address,
		   	    ),
		   	    'latitude'=>$model->latitude,
		   	    'longitude'=>$model->longitude,
		   	    'place_id'=>$model->reference_id,
		   	    'reference'=>$model->reference_id,
		   	  );
		   	  		   	
		   	  $reference_id=$model->reference_id;
		    } else {
		    	$parameters = array();
		    	if(MapSdk::$map_provider=="mapbox"){
		    		 $place_id = isset($this->data['longitude'])?$this->data['longitude']:'';
					 $place_id.=",";
					 $place_id.=isset($this->data['latitude'])?$this->data['latitude']:'';						  
					 $parameters = array(
					    'country'=>$country_code,
					    'types'=>isset($this->data['place_type'])?$this->data['place_type']:'',
					    'limit'=>1,			    
					 );
		    	}
		    	
		    	MapSdk::setKeys(array(
			     'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
			     'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
			    ));
				   
			    MapSdk::setMapParameters($parameters);			   			   			  
				   
			    $resp = MapSdk::placeDetails($place_id);	
			   			   
			    $reference_id=isset($resp['reference'])?$resp['reference']:'';
	
			    /*SAVE PLACE DETAILS*/		   
			    $model = new AR_map_places;
			    $address_components = isset($resp['address'])?$resp['address']:'';			   
			   			   
			    $model->reference_type = $reference_type;
			    $model->reference_id = isset($resp['reference'])?$resp['reference']:'';
			    $model->latitude = isset($resp['latitude'])?$resp['latitude']:'';
			    $model->longitude = isset($resp['longitude'])?$resp['longitude']:'';
			    $model->address1 = isset($address_components['address1'])?$address_components['address1']:'';
			    $model->address2 = isset($address_components['address2'])?$address_components['address2']:'';
			    $model->country = isset($address_components['country'])?$address_components['country']:'';
			    $model->country_code = isset($address_components['country_code'])?$address_components['country_code']:'';
			    $model->postal_code = isset($address_components['postal_code'])?$address_components['postal_code']:'';
			    $model->formatted_address = isset($address_components['formatted_address'])?$address_components['formatted_address']:'';
			    $model->city = isset($address_components['city'])?$address_components['city']:'';
			    $model->state = isset($address_components['state'])?$address_components['state']:'';
			    $model->street_number = isset($address_components['street_number'])?$address_components['street_number']:'';
			    $model->formatted_address = isset($address_components['formatted_address'])?$address_components['formatted_address']:'';
			    $model->address = isset($address_components['formatted_address'])?$address_components['formatted_address']:'';
			    $model->save();
		    }
		    $resp['address']['parse_address'] = $model->street_number.', '.$model->address.', '.$model->city.', '.$model->postal_code;
	        return $resp;
	    } catch (Exception $e) {	    	
	    	throw new Exception( $e->getMessage() );
	    }
	}
	
	public static function getLocalDistance($unit='', $lat1='',$lon1='', $lat2='', $lon2='')
    {    	  
    	  if(!is_numeric($lat1)){
    	  	 return 0;
    	  }
    	  if(!is_numeric($lon1)){
    	  	 return false;
    	  }
    	  if(!is_numeric($lat2)){
    	  	 return 0;
    	  }
    	  if(!is_numeric($lon2)){
    	  	 return 0;
    	  }
    	  $theta = $lon1 - $lon2;
    	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    	 
    	  $dist = acos($dist);
		  $dist = rad2deg($dist);
		  $miles = $dist * 60 * 1.1515;
		  $unit = strtoupper($unit);
		  
		  $resp = 0;
		  		  
		  if ($unit == "km") {
		      $resp = ($miles * 1.609344);
		  } else if ($unit == "N") {
		      $resp = ($miles * 0.8684);
		  } else {
		      $resp = $miles;
		  }		  
		  
		  if($resp>0){
		  	 $resp = number_format($resp,1,".","");
		  }
		  
		  return $resp;
    }	
	
    public static function reverseGeocoding($lat='', $lng='')
    {
    	try {    		
    		MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];		   
			MapSdk::setKeys(array(
			 'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
			 'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
			));
    		$resp = MapSdk::reverseGeocoding($lat,$lng);
    		return $resp;
    	} catch (Exception $e) {
	    	//return t($e->getMessage());	
	    	throw new Exception( $e->getMessage() );
	    }
    }

	public static function locationDetailsNew($place_id='',$country_code='',$custom_data=array())
	{
		try {
			
			if(empty($place_id)){
				throw new Exception( 'Place ID is empty' );
			}
			
			if(!isset(MapSdk::$map_provider)){
				throw new Exception( 'Map provider not set' );
			}
			
			$resp = array();
			 
			$reference_type = MapSdk::$map_provider;
		    $model = AR_map_places::model()->find('reference_type=:reference_type AND reference_id=:reference_id', 
		    array(':reference_type'=>$reference_type, ':reference_id'=>$place_id )); 
		    if($model){				   
		       $resp = array(
		   	    'address'=>array(
		   	       'address1'=>$model->address1,
		   	       'address2'=>$model->address2,
		   	       'country'=>$model->country,
		   	       'country_code'=>$model->country_code,
		   	       'postal_code'=>$model->postal_code,
		   	       'formatted_address'=>$model->formatted_address,
		   	    ),
		   	    'latitude'=>$model->latitude,
		   	    'longitude'=>$model->longitude,
		   	    'place_id'=>$model->reference_id,
		   	    'reference'=>$model->reference_id,
		   	  );
		   	  		   	
		   	  $reference_id=$model->reference_id;
		    } else {
		    	$parameters = array();
		    	if(MapSdk::$map_provider=="mapbox"){
		    		 $place_id = isset($custom_data['longitude'])?$custom_data['longitude']:'';
					 $place_id.=",";
					 $place_id.=isset($custom_data['latitude'])?$custom_data['latitude']:'';						  
					 $parameters = array(
					    'country'=>$country_code,
					    'types'=>isset($custom_data['place_type'])?$custom_data['place_type']:'',
					    'limit'=>1,			    
					 );
		    	}
		    			    	   
			    MapSdk::setMapParameters($parameters);			   			   			  
				   
			    $resp = MapSdk::placeDetails($place_id);	
			   			   
			    $reference_id=isset($resp['reference'])?$resp['reference']:'';
	
			    /*SAVE PLACE DETAILS*/		   
			    $model = new AR_map_places;
			    $address_components = isset($resp['address'])?$resp['address']:'';			   
			   			   
			    $model->reference_type = $reference_type;
			    $model->reference_id = isset($resp['reference'])?$resp['reference']:'';
			    $model->latitude = isset($resp['latitude'])?$resp['latitude']:'';
			    $model->longitude = isset($resp['longitude'])?$resp['longitude']:'';
			    $model->address1 = isset($address_components['address1'])?$address_components['address1']:'';
			    $model->address2 = isset($address_components['address2'])?$address_components['address2']:'';
			    $model->country = isset($address_components['country'])?$address_components['country']:'';
			    $model->country_code = isset($address_components['country_code'])?$address_components['country_code']:'';
			    $model->postal_code = isset($address_components['postal_code'])?$address_components['postal_code']:'';
			    $model->formatted_address = isset($address_components['formatted_address'])?$address_components['formatted_address']:'';
			    $model->save();
		    }
		    
	        return $resp;
	    } catch (Exception $e) {	    	
	    	throw new Exception( $e->getMessage() );
	    }
	}	
    
}
/*end class*/