<?php
class ACustomer{
	
	public static function get($client_id=0)
	{
		$model = AR_client::model()->find("client_id=:client_id",array(
		  ':client_id'=>intval($client_id)
		));
		if($model){
			return $model;
		}
		throw new Exception( 'customer id not found' );
	}
	
	public static function getAddresses($client_id='')
	{
		$model = AR_client_address::model()->findAll("client_id=:client_id",array(
		 ':client_id'=>intval($client_id)
		));
		if($model){
			$data = array();
			foreach ($model as $items) {
				$data[]=array(
				  'address'=>$items->formatted_address,
				  'latitude'=>$items->latitude,
				  'longitude'=>$items->longitude,
				  'direction'=>"https://www.google.com/maps/dir/?api=1&destination=$items->latitude,$items->longitude"
				);
			}
			return $data;
		}
		throw new Exception( 'customer address not found' );
	}
	
	public static function isBlockFromOrdering($client_id=0, $merchant_id=0)
	{
		$meta_name = 'block_customer';
		$model = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND 
			meta_name=:meta_name AND meta_value=:meta_value",array(
			 ':merchant_id'=>intval($merchant_id),
			 ':meta_name'=>$meta_name,
			 ':meta_value'=>$client_id
			));
		if($model){
			return true;
		}
		return false;
	}
		
	public static function getOrdersTotal($client_id = 0, $merchant_id=0, $status=array(), $not_in_status=array() )
	{				
		$criteria=new CDbCriteria();
	    $criteria->select="order_id,order_uuid,total,status";
	    
	    if($merchant_id>0){
		    $criteria->condition = "merchant_id=:merchant_id AND client_id=:client_id ";		    
			$criteria->params  = array(
			  ':merchant_id'=>intval($merchant_id),
			  ':client_id'=>intval($client_id)
			);
	    } else {	    	
	    	$criteria->condition = "client_id=:client_id ";		    
			$criteria->params  = array(			  
			  ':client_id'=>intval($client_id)
			);
	    }	    
		if(is_array($status) && count($status)>=1){		
			$criteria->addInCondition('status', (array) $status );
		}
		if(is_array($not_in_status) && count($not_in_status)>=1){			
			$criteria->addNotInCondition('status', (array) $not_in_status );
		}		
		$count = AR_ordernew::model()->count($criteria); 
		if($count){
			return $count;
		}
		return 0;
	}
	
	public static function getOrderSummary($client_id = 0, $merchant_id=0, $status=array())
	{
		$criteria=new CDbCriteria();
		$criteria->select="sum(total) as total";
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id AND client_id=:client_id ";		    
			$criteria->params  = array(
			  ':merchant_id'=>intval($merchant_id),
			  ':client_id'=>intval($client_id)
			);
		} else {
			$criteria->condition = "client_id=:client_id ";		    
			$criteria->params  = array(			  
			  ':client_id'=>intval($client_id)
			);
		}
		$criteria->addInCondition('status', (array) $status );
		$model = AR_ordernew::model()->find($criteria); 
		if($model){
			return $model->total;
		}
		return 0;
	}
	
	public static function getOrderRefundSummary($client_id = 0, $merchant_id=0, $status=array())
	{
		$criteria=new CDbCriteria();
		$criteria->select="sum(trans_amount) as total";
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id AND client_id=:client_id AND status=:status";		    
			$criteria->params  = array(
			  ':merchant_id'=>intval($merchant_id),
			  ':client_id'=>intval($client_id),
			  ':status'=>"paid"
			);
		} else {
			$criteria->condition = "client_id=:client_id AND status=:status";		    
			$criteria->params  = array(			  
			  ':client_id'=>intval($client_id),
			  ':status'=>"paid"
			);
		}
		$criteria->addInCondition('transaction_name', (array) $status );
		$model = AR_ordernew_transaction::model()->find($criteria); 
		if($model){
			return $model->total;
		}
		return 0;
	}
	
}
/*end class*/