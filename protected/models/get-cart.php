<?php

$merchant_id = CCart::getMerchantId($cart_uuid);
$transaction_type = CCart::cartTransaction($cart_uuid,Yii::app()->params->local_transtype,$merchant_id);						
$options_data = OptionsTools::find(array('merchant_delivery_charges_type','merchant_tax',
'merchant_default_tip'),$merchant_id);		

/*GET TAX*/
$tax_settings = array(); $tax_delivery = array();
try {
	$tax_settings = CTax::getSettings($merchant_id);		
	$tax_enabled = true;
	CCart::addTaxCondition($tax_settings['tax']);
	CCart::setTaxType($tax_settings['tax_type']);
			
	if($tax_settings['tax_type']=="multiple"){
		$tax_delivery = CTax::taxForDelivery($merchant_id,$tax_settings['tax_type']);		
	} else $tax_delivery = $tax_settings['tax'];
	
} catch (Exception $e) {	
	$tax_enabled = false;	
}

$charge_type = isset($options_data['merchant_delivery_charges_type'])?$options_data['merchant_delivery_charges_type']:'';

if(in_array('merchant_info',(array)$payload)){				
	$merchant_info = CCart::getMerchant($merchant_id,Yii::app()->language);		
	$unit = isset($merchant_info['distance_unit'])?$merchant_info['distance_unit']:$unit;
	$distance_covered = isset($merchant_info['delivery_distance_covered'])?floatval($merchant_info['delivery_distance_covered']):0;
	$merchant_lat = isset($merchant_info['latitude'])?$merchant_info['latitude']:'';
	$merchant_lng = isset($merchant_info['lontitude'])?$merchant_info['lontitude']:'';
			
	$merchant_info['restaurant_slug']=Yii::app()->createAbsoluteUrl($merchant_info['restaurant_slug']);	
	$merchant_info['logo']=CMedia::getImage($merchant_info['logo'],
	$merchant_info['path'],Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('merchant'));
}		

if(in_array('distance_local',(array)$payload) && $transaction_type=="delivery" ){				
	if($distance_resp = CCart::getLocalDistance($local_id,$unit,$merchant_lat,$merchant_lng)){										
		$distance = floatval($distance_resp['distance']);
		$address_component = $distance_resp['address_component'];
		if($distance_covered>0 && $distance>0){
			if($distance>$distance_covered){
				$out_of_range = true;
				$error[] = t("You're out of range");
				$error[] = t("This restaurant cannot deliver to your location.");
			}
		}					
	}
}		
			
if(in_array('distance',(array)$payload) && $transaction_type=="delivery" ){
	if( $distance_resp = CCart::getDistance( Yii::app()->user->id,$local_id,$unit,$merchant_lat,$merchant_lng)){
		$distance = floatval($distance_resp['distance']);
		$address_component = $distance_resp['address_component'];					
		if($distance_covered>0 && $distance>0){
			if($distance>$distance_covered){
				$out_of_range = true;
				$error[] = t("You're out of range");
				$error[] = t("This restaurant cannot deliver to your location.");
			}
		}	
	}
}
			
$max_min_estimation = CCart::getMaxMinEstimationOrder($merchant_id,$transaction_type,$charge_type,$distance,$unit);	
//dump($max_min_estimation); die();
if($max_min_estimation){	
	 //if( in_array('delivery_fee',(array)$payload) && $charge_type=="fixed" && $transaction_type=="delivery"  ){
	 if( in_array('delivery_fee',(array)$payload) && $transaction_type=="delivery"  ){
	 	 $delivery_fee = (float)$max_min_estimation['distance_price'];	
	 }				 
	 $minimum_order = floatval($max_min_estimation['minimum_order']);
  	 $maximum_order = floatval($max_min_estimation['maximum_order']);	
  	 CCart::savedAttributes($cart_uuid,'estimation', $max_min_estimation['estimation'] );	          	 	          
}

/*GET CART*/
CCart::getContent($cart_uuid,Yii::app()->language);	

/*SERVICE FEE*/
if(in_array('service_fee',(array)$payload)){
	if($service_fee = CCheckout::getServiceFee($merchant_id,$transaction_type)){				
		CCart::addCondition(array(
		  'name'=>t("Service fee"),
		  'type'=>"service_fee",
		  'target'=>"total",
		  'value'=>$service_fee,
		  'taxable'=>isset($tax_settings['tax_service_fee'])?$tax_settings['tax_service_fee']:false,
		  'tax'=>$tax_delivery,
		  //'tax'=>isset($tax_settings['tax'])?$tax_settings['tax']:'',
		));
	}		
}				

/*DELIVERY FEE*/
if(in_array('delivery_fee',(array)$payload)){
	if($delivery_fee>0 && $transaction_type=="delivery"){
		CCart::addCondition(array(
		  'name'=>t("Delivery Fee"),
		  'type'=>"delivery_fee",
		  'target'=>"total",
		  'value'=>$delivery_fee,
		  'taxable'=>isset($tax_settings['tax_delivery_fee'])?$tax_settings['tax_delivery_fee']:false,
		  'tax'=>$tax_delivery,
		  //'tax'=>isset($tax_settings['tax'])?$tax_settings['tax']:'',
		));
	}
}

/*PACKAGING*/
if(in_array('packaging',(array)$payload)){
	if( $packaging_fee = CCart::getPackagingFee()){
		CCart::addCondition(array(
		  'name'=>t("Packaging fee"),
		  'type'=>"packaging_fee",
		  'target'=>"total",
		  'value'=>$packaging_fee,
		  'taxable'=>isset($tax_settings['tax_packaging'])?$tax_settings['tax_packaging']:false,
		  'tax'=>$tax_delivery,
		  //'tax'=>isset($tax_settings['tax'])?$tax_settings['tax']:'',
		));
	}
}

/*TAX*/
if(in_array('tax',(array)$payload) && $tax_enabled==true){	
	foreach ($tax_settings['tax'] as $tax_item) {
		$tax_rate = floatval($tax_item['tax_rate']);
		$tax_name = $tax_item['tax_name'];
		$tax_label = $tax_item['tax_in_price']==false?'{{tax_name}} {{tax}}%' : '{{tax_name}} ({{tax}}% included)';
		CCart::addCondition(array(
		  'name'=>t($tax_label,array(
			 '{{tax_name}}'=>t($tax_name),
			 '{{tax}}'=>$tax_rate
		  )),
		  'type'=>"tax",
		  'target'=>"total",
		  'taxable'=>false,
		  'value'=>"$tax_rate%",
		  'tax_id'=>$tax_item['tax_id']
		));
	}	
}

/*TIP*/
if(in_array('tips',(array)$payload)){
	if($transaction_type=="delivery"){
		$default_tip = isset($options_data['merchant_default_tip'])?$options_data['merchant_default_tip']:0;
		if ( $tips = CCart::getTips($cart_uuid,$merchant_id,$default_tip)){								
			CCart::addCondition(array(
			  'name'=>t("Courier tip"),
			  'type'=>"tip",
			  'target'=>"total",
			  'value'=>floatval($tips)
			));
			CCart::savedAttributes($cart_uuid,'tips',$tips);	
		}			
	} else CCart::deleteAttributes($cart_uuid,'tips');
}

$subtotal = CCart::getSubTotal();
$sub_total = floatval($subtotal['sub_total']);

/*CHECK IF MAX AND MIN IS SATISFY*/
if($minimum_order>0){
	if($minimum_order>$sub_total){
		$error[] = t("minimum order is {{minimum_order}}",array(
		 '{{minimum_order}}'=>Price_Formatter::formatNumber($minimum_order)
		));
	}
}
if($maximum_order>0){
	if($sub_total>$maximum_order){
		$error[] = t("maximum order is {{maximum_order}}",array(
		 '{{maximum_order}}'=>Price_Formatter::formatNumber($maximum_order)
		));
	}
}

/*PROMO AND DISCOUNT*/						
if(in_array('discount',(array)$payload)){
   $now = date("Y-m-d");
   if($cart_condition = CCart::cartCondition($cart_uuid)){
   	  foreach ($cart_condition as $condition) {
   	  	  if ( $meta_value = json_decode($condition['meta_value'],true) ){
   	  	  	  //dump($meta_value);
   	  	  	  $name = t($meta_value['name']);
			  if( $isjson = json_decode($meta_value['name'],true) ){							
					$name = t($isjson['label'],$isjson['params']);
			  }	
			  
			  if($meta_value['type']=="voucher"){							
				try {
					$promo_details = CPromos::applyVoucher($merchant_id, $meta_value['id'] , Yii::app()->user->id , $now , $sub_total);
					//dump($promo_details);
					$meta_value['value'] = -$promo_details['less_amount'];
				} catch (Exception $e) {								
					break;
				}
			  } elseif ( $meta_value['type']=="offers" ){
				try {
				    $promo_details = CPromos::applyOffers($merchant_id, $meta_value['id'], $now , $sub_total , $transaction_type);
				} catch (Exception $e) {								
					break;
				}
			  }			
			  						  
			  CCart::addCondition(array(
			   'name'=>$name,
			   'type'=>$meta_value['type'],
			   'target'=>$meta_value['target'],
			   'value'=>$meta_value['value'],
			   'voucher_owner'=>isset($meta_value['voucher_owner'])?$meta_value['voucher_owner']:''
			  ));
			  				  
   	  	  }
   	  }
   }
}


/*SAVE IF THERE IS ERROR*/
if(is_array($error) && count($error)>=1){
	CCart::savedAttributes($cart_uuid,'error', json_encode($error) );
} else CCart::deleteAttributes($cart_uuid,'error');

//$total = CCart::getTotal();	

$data  = array();
if(in_array('merchant_info',(array)$payload)){
   $data['merchant']=$merchant_info;
}
if(in_array('items',(array)$payload)){
   $data['items']=CCart::getItems();   
}
if(in_array('summary',(array)$payload)){
   $summary = CCart::getSummary();
   $data['summary']=$summary;   
   //dump($summary);
}

if(in_array('subtotal',(array)$payload)){
	$data['subtotal']=array(
	  'value'=>Price_Formatter::formatNumber($sub_total),
	  'raw'=>$sub_total
	);
}
if(in_array('total',(array)$payload)){
   $total = CCart::getTotal();	   
   //dump($total);die();
   $data['total']=array(
     'value'=>Price_Formatter::formatNumber($total),
     'raw'=>Price_Formatter::convertToRaw($total),     
   );
}

/*CHECKOUT DATA*/
$checkout_data = array();
if(in_array('checkout',(array)$payload)){
	$checkout_data = array(
	  'transaction_type'=>$transaction_type,
	  'data'=>CCheckout::getTransactionData($cart_uuid,$transaction_type)
	);
	if(!Yii::app()->user->isGuest){		
		CCart::savedAttributes($cart_uuid,'contact_number', Yii::app()->user->contact_number );
		CCart::savedAttributes($cart_uuid,'contact_email', Yii::app()->user->email_address );
		CCart::savedAttributes($cart_uuid,'customer_name', Yii::app()->user->first_name." ".Yii::app()->user->last_name );
		CCart::savedAttributes($cart_uuid,'first_name', Yii::app()->user->first_name);
		CCart::savedAttributes($cart_uuid,'last_name', Yii::app()->user->last_name);
	}	
}

/*GET CHECKOUT LINK*/
$go_checkout = array();
if(in_array('go_checkout',(array)$payload)){
	if(Yii::app()->user->isGuest){
		$go_checkout = array(
		  'link'=>Yii::app()->createAbsoluteUrl("account/login?redirect=". Yii::app()->createAbsoluteUrl("/account/checkout") )
		);
	} else {
		$go_checkout = array(
		  'link'=>Yii::app()->createAbsoluteUrl("account/checkout")
		);
	}
}

/*GET ITEM COUNT*/
$items_count = 0;
if(in_array('items_count',(array)$payload)){
	$items_count = CCart::itemCount($cart_uuid);
}

