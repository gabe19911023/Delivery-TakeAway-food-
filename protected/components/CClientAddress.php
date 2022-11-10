<?php
class CClientAddress
{
	
	public static function delete($client_id='', $address_uuid='')
	{
		$model = AR_client_address::model()->find('address_uuid=:address_uuid AND client_id=:client_id', 
		array(':address_uuid'=>$address_uuid,'client_id'=>$client_id)); 
		if($model){
			$model->delete();
			return true;
		}
		throw new Exception( 'Address not found' );
	}
	
	public static function find($client_id='', $address_uuid='')
	{
		$model = AR_client_address::model()->find('address_uuid=:address_uuid AND client_id=:client_id', 
		array(':address_uuid'=>$address_uuid,'client_id'=>$client_id)); 
		if($model){			
			return array(
			   'address_uuid'=>$model->address_uuid,
			   'address' => array(
			     'address1'=>$model->address1,
			     'address2'=>$model->address2,
			     'country'=>$model->country,
			     'country_code'=>$model->country_code,
			     'postal_code'=>$model->postal_code,
			     'formatted_address'=>$model->formatted_address,
			   ),
			   'latitude'=>$model->latitude,
			   'longitude'=>$model->longitude,
			   'place_id'=>$model->place_id,
			   'reference'=>$model->place_id,
			   'attributes'=>array(
			     'location_name'=>$model->location_name,
			     'delivery_options'=>$model->delivery_options,
			     'delivery_instructions'=>$model->delivery_instructions,
			     'address_label'=>$model->address_label,
			   )
			);
		}
		throw new Exception( 'Address not found' );
	}
	
	public static function getAddress($place_id='',$client_id='')
	{
		$model = AR_client_address::model()->find('place_id=:place_id AND client_id=:client_id', 
		array(':place_id'=>$place_id,'client_id'=>$client_id)); 
		if($model){
			return array(
			   'address_uuid'=>$model->address_uuid,
			   'address' => array(
			     'address1'=>$model->address1,
			     'address2'=>$model->address2,
			     'country'=>$model->country,
			     'country_code'=>$model->country_code,
			     'postal_code'=>$model->postal_code,
			     'formatted_address'=>$model->formatted_address,
			   ),
			   'latitude'=>$model->latitude,
			   'longitude'=>$model->longitude,
			   'place_id'=>$model->place_id,
			   'reference'=>$model->place_id,
			   'attributes'=>array(
			     'location_name'=>$model->location_name,
			     'delivery_options'=>$model->delivery_options,
			     'delivery_instructions'=>$model->delivery_instructions,
			     'address_label'=>$model->address_label,
			   )
			);
		} else throw new Exception( 'no results' );
	}
		
	public static function getAddresses($client_id='')
	{
		$data = array();
		$model = AR_client_address::model()->findAll('client_id=:client_id order by address_id DESC', 
		array('client_id'=>$client_id)); 
		if($model){
			foreach ($model as $val) {
				$data[]=array(
				  'address_uuid'=>$val->address_uuid,
				  'address'=>array(
				    'address1'=>$val->address1,
				    'address2'=>$val->address2,
				    'country'=>$val->country,
				    'country_code'=>$val->country_code,
				    'postal_code'=>$val->postal_code,
				    'formatted_address'=>$val->formatted_address,
				  ),
				  'latitude'=>$val->latitude,
				  'longitude'=>$val->longitude,
				  'place_id'=>$val->place_id,
				  'reference'=>$val->place_id,
				  'attributes'=>array(
				    'location_name'=>$val->location_name,
				    'delivery_options'=>$val->delivery_options,
				    'delivery_instructions'=>$val->delivery_instructions,
				    'address_label'=>$val->address_label,
				  )
				);
			}
			return $data;
		}
		return false;
	}
	
}
/*end class*/