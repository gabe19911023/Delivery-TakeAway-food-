<?php 
class MapSdk
{
	public static $api_key;
	public static $map_provider;
	public static $map_parameters;
	public static $http_code;
	
	public static $map_api = array(
	  'google'=>array(
	    'place'=>'https://maps.googleapis.com/maps/api/place/autocomplete/json',
	    'place_detail'=>'https://maps.googleapis.com/maps/api/place/details/json',
	    'reverse_geocoding'=>'https://maps.googleapis.com/maps/api/geocode/json',
	    'distance'=>"https://maps.googleapis.com/maps/api/distancematrix/json"
	  ),
	  'mapbox'=>array(
	    'place'=>'https://api.mapbox.com/geocoding/v5/mapbox.places',
	    'place_detail'=>'https://api.mapbox.com/geocoding/v5/mapbox.places',
	    'reverse_geocoding'=>'https://api.mapbox.com/geocoding/v5/mapbox.places'
	  )
	);
	
	public static function setKeys($keys=array())
	{		
		if(array_key_exists(self::$map_provider,(array)$keys)){
			self::$api_key=$keys[self::$map_provider];
		} else  throw new Exception('Invalid api keys');
	}
	
	public static function setMapParameters($parameters=array())
	{
		self::$map_parameters = $parameters;
	}
	
	private static function getMapParameters($separator='')
	{
		$components='';
		if(is_array(self::$map_parameters) && count(self::$map_parameters)>=1){
			foreach (self::$map_parameters as $key=>$val) {
				if($separator=="="){
					$components.= "&".$key.$separator.$val;
				} else $components.= $key.$separator.$val;				
			}			
		}
		return $components;
	}
	
	public static function getData($api_url='')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $result = curl_exec($ch);        
        if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}
		self::$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		curl_close($ch);
		
		return $result;
	}
		
	/**
	 * find place api
	 *
	 * @param $query  = address or location name
	 * @return array
	 * //'types'=>'geocode', //possible values geocode,address,establishment
	 */
	public static function findPlace($query='')
	{		
				
		$resp = array(); $data=array(); $components = '';		
		
		switch (self::$map_provider) {
			case "google.maps":			
			    $components = self::getMapParameters(":");
			    
			    //echo $components;
			    
			    $api_url = self::$map_api['google']['place']."?".http_build_query(array(
				  'key'=>self::$api_key,
				  'input'=>trim($query),			  
				  'components'=>$components 
				));
				
				//dump(urldecode($api_url));die();
			
				$result = self::getData($api_url);						
				if (is_string($result) && strlen($result) > 0){
					if ($resp = json_decode($result,true)){				
						$data = MapSdk::parseGoogleResponse($resp,'place');
					}
				}				
			    
				break;
		
			case "mapbox":	
			    $api_url = self::$map_api['mapbox']['place']."/".urlencode($query).".json?".http_build_query(array(			  
				  'access_token'=>self::$api_key			  
				));
				$api_url.= self::getMapParameters("=");
				
				$result = self::getData($api_url);												
				if (is_string($result) && strlen($result) > 0){
					if ($resp = json_decode($result,true)){				
						$data = MapSdk::parseMapboxResponse($resp,'place');
					}
				}				
			    break;
			    
			default:
				throw new Exception ( 'undefined map provider' ); 
				break;
		}
				
		return $data;
	}

	public static function findPlace2($query='')
	{		
				
		$resp = array(); $data=array(); $components = '';		
		
		switch (self::$map_provider) {
			case "google.maps":			
			    $components = self::getMapParameters(":");
			    
			    //echo $components;
			    
			    $api_url = 'https://maps.googleapis.com/maps/api/geocode/json'."?".http_build_query(array(
				  'key'=>self::$api_key,
				  'address'=>trim($query),			  
				  'components'=>$components 
				));
				
				// dump(urldecode($api_url));die();
			
				$result = self::getData($api_url);						
				if (is_string($result) && strlen($result) > 0){
					if ($resp = json_decode($result,true)){				
						$data = MapSdk::parseGoogleResponse($resp,'place2');
					}
				}				
			    
				break;
		
			case "mapbox":	
			    $api_url = self::$map_api['mapbox']['place']."/".urlencode($query).".json?".http_build_query(array(			  
				  'access_token'=>self::$api_key			  
				));
				$api_url.= self::getMapParameters("=");
				
				$result = self::getData($api_url);												
				if (is_string($result) && strlen($result) > 0){
					if ($resp = json_decode($result,true)){				
						$data = MapSdk::parseMapboxResponse($resp,'place');
					}
				}				
			    break;
			    
			default:
				throw new Exception ( 'undefined map provider' ); 
				break;
		}
				
		return $data;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $place_id
	 * @return unknown
	 */
	public static function placeDetails($place_id='')
	{
		if(empty($place_id)){
			throw new Exception("invalid place id parameters");
		}
		
		$resp = array(); $data=array();		
		
		switch (self::$map_provider) {
			case "google.maps":
												
				$api_url = self::$map_api['google']['place_detail']."?".http_build_query(array(
				  'key'=>self::$api_key,
				  'placeid'=>trim($place_id)
				));
								
				$result = self::getData($api_url);		
				
				if (is_string($result) && strlen($result) > 0){
					if ($resp = json_decode($result,true)){				
						$data = MapSdk::parseGoogleResponse($resp,'place_detail');
					}
				}								
				break;
		
			case "mapbox":	
			    			    
			    $api_url = self::$map_api['mapbox']['place_detail']."/$place_id.json?".http_build_query(array(
				  'access_token'=>self::$api_key				  
				));
				
				$api_url.= self::getMapParameters("=");
												
				$result = self::getData($api_url);
				
				if (is_string($result) && strlen($result) > 0){
					if ($resp = json_decode($result,true)){							
						$data = self::parseMapboxResponse($resp,'place_detail');
					}
				}					
			    break;
			    
			default:
				throw new Exception ( 'undefined map provider' );
				break;
		}			
		
		if(is_array($data) && count($data)>=1){
		   return $data;
		} else throw new Exception ( 'no results' );
	}
	
	public static function parseGoogleResponse($resp=array(),$parse_method='place')
	{
		$data = array();
		$status = isset($resp['status']) ? trim($resp['status']) :'';
		switch ($status) {
			case "OK":
				if($parse_method==="place"){
					if(isset($resp['predictions'])){
						foreach ($resp['predictions'] as $val) {	
							$place_type = '';
							foreach ($val['types'] as $types){
								$place_type.="$types,";
							}							
							$place_type = !empty($place_type)?substr($place_type,0,-1):'';							
							$data[]=array(
							  'id'=>isset($val['place_id'])?$val['place_id']:'',
							  'provider'=>self::$map_provider,
							  'addressLine1'=>$val['structured_formatting']['main_text'],							  
							  'addressLine2'=>isset($val['structured_formatting']['secondary_text'])?$val['structured_formatting']['secondary_text']:'',
							  'place_type'=>$place_type,
							  'description'=>isset($val['description'])?$val['description']:''
							);
						}		
					}				
				}elseif($parse_method==="place2"){
					if(isset($resp['results'])){
						foreach ($resp['results'] as $val) {	
							$place_type = '';
							foreach ($val['types'] as $types){
								$place_type.="$types,";
							}							
							$place_type = !empty($place_type)?substr($place_type,0,-1):'';
							$address = array(
							  'id'=>isset($val['place_id'])?$val['place_id']:'',
							  'provider'=>self::$map_provider,

							  'place_type'=>$place_type,
							  'description'=>isset($val['formatted_address'])?$val['formatted_address']:'',
							  'street_number'	=>	'',
							  'street_name'	=>	'',
							  'city'	=>	'',
							  'state'	=>	'',
							  'postal_code'	=>	'',
							  'country'	=>	''
							);
				            foreach ($val['address_components'] as $component) {
				                switch ($component['types'][0]) {
				                    case 'street_number':
				                        $address['street_number'] = $component['long_name'];
				                        break;
				                    case 'route':
				                        $address['street_name'] = $component['long_name'];
				                        break;
				                    case 'locality':
				                        $address['city'] = $component['long_name'];
				                        break;
				                    case 'administrative_area_level_1':
				                        $address['state'] = $component['long_name'];
				                        break;
				                    case 'postal_code':
				                        $address['zip'] = $component['long_name'];
				                        break;
				                    case 'country':
				                        $address['country'] = $component['long_name'];
				                        break;
				                }
				            }					
							$data[]=$address;
						}		
					}				
				} elseif ( $parse_method==="place_detail"){		
																		
					$address_components = $resp['result']['address_components'];
										
					$address1 = array('street_address','neighborhood','premise','street_number');
					$address2 = array('locality','route','administrative_area_level_1','administrative_area_level_2');
					$country  =  array('country');
					$postal_code = array('postal_code');
					$street_number = array('street_number');
					$city = array('locality');
					$state = array('administrative_area_level_1');
					
					$address_out['address1']='';
					$address_out['address2']='';
					$address_out['country']='';
					$address_out['country_code']='';
					$address_out['postal_code']='';
					$address_out['street_number']='';
					$address_out['city']='';
					$address_out['state']='';
					$address_out['formatted_address'] = $resp['result']['formatted_address'];
					$address_out['address'] = $resp['result']['formatted_address'];
					
					$latitude = $resp['result']['geometry']['location']['lat'];
					$longitude = $resp['result']['geometry']['location']['lng'];
					$place_id = $resp['result']['place_id'];
					$reference = isset($resp['result']['reference'])?$resp['result']['reference']:'';
					
					$name = isset($resp['result']['name'])?$resp['result']['name']:'';
					
					if(is_array($address_components) && count($address_components)>=1){
						foreach ($address_components as $val) {									
							foreach ($val['types'] as $types) {														
								if(in_array($types,$address1)){		
									if(!empty($address_out['address1'])){
										$address_out['address1'].= ", ".$val['long_name'];
									} else $address_out['address1'].= $val['long_name'];									
								}
								if(in_array($types,$address2)){
									if(!empty($address_out['address2'])){
										$address_out['address2'].= ", ".$val['long_name'];
									} else $address_out['address2'].= $val['long_name'];									
								}
								if(in_array($types,$country)){
									$address_out['country'].= $val['long_name'];
									$address_out['country_code'].= $val['short_name'];
								}
								if(in_array($types,$postal_code)){
									$address_out['postal_code'].= $val['long_name'];
								}
								if(in_array($types,$street_number)){
									$address_out['street_number'].= $val['long_name'];
								}
								if(in_array($types,$city)){
									$address_out['city'].= $val['long_name'];
								}
								if(in_array($types,$state)){
									$address_out['state'].= $val['long_name'];
								}
							}															
						}
												
						if(!empty($name)){
						   $address_out['address1'] = $name;
						}
					}
					
					$place_type = '';
					if(isset($resp['result']['types'])){
						foreach ($resp['result']['types'] as $types){
							$place_type.="$types,";
						}
						$place_type = !empty($place_type)?substr($place_type,0,-1):'';
					}					
										
					$data = array(
					  'address'=>$address_out,
					  'latitude'=>$latitude,
					  'longitude'=>$longitude,
					  'place_id'=>$place_id,
					  'reference'=>$reference,
					  'place_type'=>$place_type
					);								
				} elseif ( $parse_method==="distance"){						
					$elements = $resp['rows'][0]['elements'][0];
					$elements_status = $resp['rows'][0]['elements'][0]['status'];									
					if($elements_status=="ZERO_RESULTS"){
					   throw new Exception($elements_status);
					}
					$distance_raw = $elements['distance']['text'];
					$duration_raw = $elements['duration']['text'];
					
					$distance = (float)$distance_raw;
					$unit = trim(str_replace($distance,"",$distance_raw));					
					
					$duration = (float)$duration_raw;
					$duration_unit = trim(str_replace($duration,"",$duration_raw));					
																			
					$data = array(
					  'distance'=>$distance,
					  'unit'=>$unit,
					  'pretty_unit'=>MapSdk::prettyUnit($unit),
					  'duration'=>$duration,
					  'duration_unit'=>$duration_unit
					);
				}
				break;
			
			case "ZERO_RESULTS":	
			    //throw new Exception("zero search found");
			    break;							
		
			case "OVER_QUERY_LIMIT":	
			    throw new Exception("over query limit");
			    break;
			        
			case "REQUEST_DENIED":  
			    throw new Exception( isset($resp['error_message'])?$resp['error_message']:"request denied" );  
			    break;
			        
			case "INVALID_REQUEST":						     
			    throw new Exception("input parameter is missing");
			    break;					    					   			
			    
			case "UNKNOWN_ERROR":
				throw new Exception("unknow error");
				break;
				
			case "NOT_FOUND":
				throw new Exception("place id not found");
				break;	
				
			default:
				throw new Exception("undefined error");
				break;
		}
		return $data;
	}
	
	public static function parseMapboxResponse($resp=array(),$parse_method='place')
	{		
		$data = array();
		
		if($parse_method=="place"){
			if(is_array($resp) && count($resp)>=1){								
				if(isset($resp['features'])){
					foreach ($resp['features'] as $val) {					
						$data[]=array(
						  'id'=>$val['id'],
						  'provider'=>self::$map_provider,
						  'addressLine1'=>$val['text'],
						  'addressLine2'=>$val['place_name'],
						  'latitude'=>$val['center'][1],
						  'longitude'=>$val['center'][0],
						  'place_type'=>$val['place_type'][0]
						);
					}
				} else {
					$error = self::$http_code." ";
					$error.= isset($resp['message'])?$resp['message']:'';
					throw new Exception( $error );
				}
			}
		} elseif ($parse_method=="place_detail"){
			
			
			$address1 = array('poi','address','neighborhood');
			$address2 = array('locality','place','district','region');
			$country  =  array('country');
			$postal_code = array('postcode');
			
			$address_out['address1']='';
			$address_out['address2']='';
			$address_out['country']='';
			$address_out['postal_code']='';
			$address_out['formatted_address'] = '';
									
			if(is_array($resp) && count($resp)>=1){
				if(isset($resp['features'])){					
					foreach ($resp['features'] as $val) {		
																				
						$address_out['formatted_address'] =isset($val['place_name'])?$val['place_name']:'';
						$address_out['address1'] =  isset($val['text'])?$val['text']:'';
						
						if(isset($val['context'])){
							if(is_array($val['context']) && count($val['context'])>=1){
								foreach ($val['context'] as $context) {									
									$id = isset($context['id'])? substr($context['id'],0, strpos($context['id'],".") ) :'';
									$text=isset($context['text'])?$context['text']:'';									
									
									if (is_string($address_out['address1']) && strlen($address_out['address1']) <= 0){
									if(in_array($id,$address1)){		
										if(!empty($address_out['address1'])){
											$address_out['address1'].= ", ".$text;
										} else $address_out['address1'].= $text;
									}
									}
									
									if(in_array($id,$address2)){
										if(!empty($address_out['address2'])){
											$address_out['address2'].= ", ".$text;
										} else $address_out['address2'].= $text;
									}
									if(in_array($id,$country)){
										$address_out['country'].= $text;
									}
									if(in_array($id,$postal_code)){
										$address_out['postal_code'].= $text;
									}
									
								}
							}
														
						}
						
						$place_type = '';
						if(isset($val['place_type'])){
							foreach ($val['place_type'] as $types){
								$place_type.="$types,";
							}
							$place_type = !empty($place_type)?substr($place_type,0,-1):'';
						}					
						
						$data=array(
						  'address'=>$address_out,
						  'place_id'=>$val['id'],
						  'reference'=>$val['id'],						  						  
						  'latitude'=>$val['center'][1],
						  'longitude'=>$val['center'][0],
						  'place_type'=>$place_type
						);
					}
				} else {
					$error = self::$http_code." ";
				    $error.= isset($resp['message'])?$resp['message']:'';
				    throw new Exception( $error );
				}
			} 
		}
		return $data;
	}
	
	public static function prettyUnit($unit='')
	{
		switch ($unit) {
			case "M":		
			case "mi":	
			    return t("miles");
				break;
				
			case "K":			
			case "km":	
			    return t("km");
				break;	
				
			case "m":			
			    return t("m");
				break;		
				
			case "ft":			
			    return t("ft");
				break;			
		
			default:
				return $unit;
				break;
		}
	}
	
	public static function reverseGeocoding($lat='', $lng='')
	{
		if(empty($lat)){
			throw new Exception("invalid latitude parameters");
		}
		if(empty($lng)){
			throw new Exception("invalid longitude parameters");
		}

		$data = array();
		
		switch (self::$map_provider) {
			case "google.maps":	
			   $api_url = self::$map_api['google']['reverse_geocoding']."?".http_build_query(array(
			      'latlng'=>"$lat,$lng",
			      'location_type'=>"ROOFTOP",
				  'key'=>self::$api_key,			  
			   ));					   
			   $result = self::getData($api_url);
			   if (is_string($result) && strlen($result) > 0){
				   if ($resp = json_decode($result,true)){					   	   
					   $new_resp = array(
					    'error_message'=>isset($resp['error_message'])?$resp['error_message']:'',
					    'result'=>isset($resp['results'][0])?$resp['results'][0]:array(),
					    'status'=>isset($resp['status'])?$resp['status']:''
					   );					   
					   $data = MapSdk::parseGoogleResponse($new_resp,'place_detail');					   
				   }
			   }	
			break;
			
			case "mapbox":
			   $api_url = self::$map_api['mapbox']['reverse_geocoding']."/$lng,$lat".".json?".http_build_query(array(
			      'access_token'=>self::$api_key	
			   ));
			   $api_url.= self::getMapParameters("=");
			   $result = self::getData($api_url);
			   if (is_string($result) && strlen($result) > 0){
				   if ($resp = json_decode($result,true)){				   	   
					   $data = MapSdk::parseMapboxResponse($resp,'place_detail');					   
				   }
			   }	
			break;
		}
				
		return $data;
	}

	
	/*
	how to use
	
	MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];		   
    MapSdk::setKeys(array(
     'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
     'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
    ));
    		    		    		    		    		  
    MapSdk::setMapParameters(array(
      'from_lat'=>$merchant_lat,
      'from_lng'=>$merchant_lng,
      'to_lat'=>$customer_lat,
      'to_lng'=>$customer_lng,
      'place_id'=>$place_id,
      'unit'=>$unit,
      'mode'=>'driving'
    ));*/
		    
	public static function distance()
	{
		$data=array(); $parameters = self::$map_parameters;		
		
		if(is_array($parameters) && count($parameters)>=1){
						
			switch (MapSdk::$map_provider) {
				case "google.maps":					
					$params = array(
					  'origins'=>$parameters['from_lat'].",".$parameters['from_lng'],
					  'units'=>$parameters['unit']=="mi"?"imperial":"metric",
					  'key'=>MapSdk::$api_key,
					);
					if(isset($parameters['place_id'])){						
						$params['destinations']="place_id:".$parameters['place_id'];
					} else $params['destinations']=$parameters['to_lat'].",".$parameters['to_lng'];
					
					$api_url = MapSdk::$map_api['google']['distance']."?".http_build_query($params);					
										
					$result = self::getData($api_url);											
					if (is_string($result) && strlen($result) > 0){
						if ($resp = json_decode($result,true)){								
							$data = MapSdk::parseGoogleResponse($resp,'distance');
						}
					}				
				
					break;
			
				case "mapbox":
					break;
						
				default:
					break;
			}
		} 
		
		if(is_array($data) && count($data)>=1){
		   return $data;
		} else throw new Exception ( 'no results' );
	}
	
}
/*end class*/