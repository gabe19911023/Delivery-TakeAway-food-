<?php
class CTrackingOrder
{
	public static function getProgress($order_uuid='', $datetime = '' )
	{
		$order_status = ''; $order_status_details=''; $order_progress = 1;
		
		$tracking_stats = AR_admin_meta::getMeta(array(
		 'tracking_status_process','tracking_status_in_transit','tracking_status_delivered','tracking_status_delivery_failed',
		 'status_new_order','status_cancel_order','status_rejection','status_delivered','tracking_status_ready',
		 'tracking_status_completed','tracking_status_failed'
		));		
		
		$tracking_status_process = isset($tracking_stats['tracking_status_process'])?AttributesTools::cleanString($tracking_stats['tracking_status_process']['meta_value']):'';
		$tracking_status_ready = isset($tracking_stats['tracking_status_ready'])?AttributesTools::cleanString($tracking_stats['tracking_status_ready']['meta_value']):'';
		$tracking_status_in_transit = isset($tracking_stats['tracking_status_in_transit'])?AttributesTools::cleanString($tracking_stats['tracking_status_in_transit']['meta_value']):'';		
		$tracking_status_delivered = isset($tracking_stats['tracking_status_delivered'])?AttributesTools::cleanString($tracking_stats['tracking_status_delivered']['meta_value']):'';		
		
		$tracking_status_delivery_failed = isset($tracking_stats['tracking_status_delivery_failed'])?AttributesTools::cleanString($tracking_stats['tracking_status_delivery_failed']['meta_value']):'';		
		$tracking_status_completed = isset($tracking_stats['tracking_status_completed'])?AttributesTools::cleanString($tracking_stats['tracking_status_completed']['meta_value']):'';
		$tracking_status_failed = isset($tracking_stats['tracking_status_failed'])?AttributesTools::cleanString($tracking_stats['tracking_status_failed']['meta_value']):'';
		
		$status_new_order = isset($tracking_stats['status_new_order'])?AttributesTools::cleanString($tracking_stats['status_new_order']['meta_value']):'';
		$status_cancel_order = isset($tracking_stats['status_cancel_order'])?AttributesTools::cleanString($tracking_stats['status_cancel_order']['meta_value']):'';
		$status_rejection = isset($tracking_stats['status_rejection'])?AttributesTools::cleanString($tracking_stats['status_rejection']['meta_value']):'';		
		$status_delivered = isset($tracking_stats['status_delivered'])?AttributesTools::cleanString($tracking_stats['status_delivered']['meta_value']):'';
		
		$data = CNotifications::getOrder($order_uuid , array(
    	 'merchant_info','items','summary','order_info','customer','logo','total','meta'
    	));		
    	    	    
    	$order_info = isset($data['order_info'])?$data['order_info']:'';
    	$order_type = isset($order_info['order_type'])?$order_info['order_type']:'';    	
    	$status = isset($order_info['status'])?AttributesTools::cleanString($order_info['status']):'';    	
    	$whento_deliver = isset($order_info['whento_deliver'])?$order_info['whento_deliver']:'';
    	$delivery_date = isset($order_info['delivery_date'])?$order_info['delivery_date']:'';
    	$delivery_time = isset($order_info['delivery_time'])?$order_info['delivery_time']:'';    	
    	
    	$merchant = isset($data['merchant'])?$data['merchant']:'';
    	$customer = isset($data['customer'])?$data['customer']:'';
    	
    	$message_parameters = array();
		if(is_array($data['order_info']) && count($data['order_info'])>=1){
			foreach ($data['order_info'] as $data_key=>$data_value) {
				if($data_key=="service_code"){
					$data_key='order_type';
				}
				$message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
		if(is_array($data['merchant']) && count($data['merchant'])>=1){
			foreach ($data['merchant'] as $data_key=>$data_value) {				
				$message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
				
		if(is_array($data['meta']) && count($data['meta'])>=1){
			foreach ($data['meta'] as $data_key=>$data_value) {								
				$message_parameters["{{{$data_key}}}"]=$data_value;
			}
		}
		
		if($whento_deliver=="schedule"){
			$message_parameters['{{delivery_date_time}}'] = Date_Formatter::dateTime("$delivery_date $delivery_time");
		}
		
		//dump($status); dump($tracking_status_completed);
    	
    	if($status == $status_new_order){
    		$order_status = t("Confirming your order");
    		$order_status_details = t("We sent your order to {{restaurant_name}} for final confirmation.",$message_parameters);
    		$order_progress = 1;
    		if($whento_deliver=="schedule"){    			    			
    			$delivery_datetime = date("Y-m-d g:i:s a",strtotime("$delivery_date $delivery_time"));			    	    			
    			$diff = CommonUtility::dateDifference($delivery_datetime,$datetime);
    			if(!is_array($diff) && count((array)$diff)<=1){
    			   $order_status = t("Scheduled");
    		       $order_status_details = t("Your order is scheduled on {{delivery_date_time}}",$message_parameters);
    			} 		
    		}
    	} elseif ( $status == $tracking_status_process ){
    		$order_status = t("Preparing your order");
    		$order_status_details = t("{{restaurant_name}} is preparing your  order.",$message_parameters);
    		$order_progress = 2;
    	} elseif ( $status == $tracking_status_ready ){
    		if($order_type=="delivery"){
	    		$order_status = t("Your order is ready");
	    		$order_status_details = t("Your order is ready to pickup by driver.",$message_parameters);
	    		$order_progress = 2;	
    		} else if( $order_type == "pickup" ) {
    		   $order_status = t("Pickup your order");
    		   $order_status_details = t("Your order is ready. Time to go to {{restaurant_name}} to pickup your order.",$message_parameters);
    		   $order_progress = 3;
    		} else if( $order_type == "dinein" ) {
    		   $order_status = t("Your order is ready");
    		   $order_status_details = t("Your order is ready. Time to go to {{restaurant_name}} to eat your order.",$message_parameters);
    		   $order_progress = 3;
    		}
    	} elseif ( $status == $tracking_status_in_transit ){
    		$order_status = t("Heading to you");
    		$order_status_details = t("Your delivery guy is heading to you with your order.",$message_parameters);
    		$order_progress = 3;
    	} elseif ( $status == $tracking_status_delivered ){
    		$order_status = t("Order Complete");
    		$order_status_details = t("Your order is completed. Enjoy!",$message_parameters);
    		$order_progress = 4;
    	} elseif ( $status == $tracking_status_delivery_failed ){
    		$order_status = t("Delivery failed");
    		$order_status_details = t("Unfortunately, the restaurant is not able to complete the delivery.",$message_parameters);
    		$order_progress = 0;    		
    	} elseif ( $status == $status_cancel_order ){
    		$order_status = t("Order cancelled");
    		$order_status_details = t("Unfortunately, the restaurant is not able to complete this order due to the following reason: {{rejetion_reason}}",$message_parameters);
    		$order_progress = 0;    		
    	} elseif ( $status == $status_rejection ){
    		$order_status = t("Order rejected");
    		$order_status_details = t("Unfortunately, the restaurant is not able to complete this order due to the following reason: {{rejetion_reason}}",$message_parameters);
    		$order_progress = 0;    		
    	} elseif ( $status == $tracking_status_completed ){
    		$order_status = t("Order Complete");
    		$order_status_details = t("Your order is completed. Enjoy!",$message_parameters);
    		$order_progress = 3;
    	} elseif ( $status == $tracking_status_failed ){
    		$order_status = t("Your order failed to complete");
    		$order_status_details = t("Unfortunately, the restaurant is not able to complete your order.",$message_parameters);
    		$order_progress = 0;    		
    	} else {
    		$order_status = '';
    		$order_status_details = '';
    		$order_progress = -1;
    	}
    	    	
    	$data = array(
    	  'order_progress'=>$order_progress,
    	  'order_status'=>$order_status,
    	  'order_status_details'=>$order_status_details,
    	  'order_id'=>$order_info['order_id'],
    	  'customer'=>$customer,
    	);    	
    	return $data;   
	}
	
	public static function getInstructions($merchant_id=0, $order_type='')
	{
		$meta_name = '';
		if($order_type=="pickup"){
			$meta_name='customer_pickup_instructions';
		} elseif ( $order_type=="dinein"){
			$meta_name='customer_dinein_instructions';
		}		
		$model = AR_merchant_meta::getValue($merchant_id,$meta_name);
		if($model){
			return array(
			  'text'=>$model['meta_value']
			);
		}
		return false;
	}
	
}
/*end class*/