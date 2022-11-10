<?php
class CPromos
{	
    public static function promo($merchant_id='', $date_now='')
    {
    	$today = strtolower(date("l",strtotime($date_now)));  	
    	$mtid='"'.$merchant_id.'"'; 
    	
    	$stmt="
    	SELECT 
    	'voucher' as promo_type,
    	a.voucher_id as promo_id,
    	a.merchant_id,
    	a.joining_merchant,
    	a.voucher_name,
    	a.voucher_type, 
    	a.amount, 
    	a.expiration, 
    	a.status, 
    	a.used_once, 
    	a.min_order,
    	a.expiration     	
    	
    	FROM {{voucher_new}} a
    	WHERE a.expiration >= ".q($date_now)."
    	AND status in ('publish','published')
    	AND ".$today."=1
    	AND ( merchant_id =".q($merchant_id)." OR joining_merchant LIKE ".q("%$mtid%")." )
    	
    	UNION ALL
    	
    	SELECT 
    	'offers' as promo_type,
    	offers_id as promo_id,
    	merchant_id,    	
    	applicable_to,
    	offer_percentage,
    	offer_price,
    	status,
    	'',
    	valid_from,
    	valid_to,
    	'',
    	''
    	
    	FROM {{offers}}
    	WHERE merchant_id =".q($merchant_id)."
    	AND status in ('publish','published')
    	AND ".q($date_now)." >= valid_from and ".q($date_now)." <= valid_to
    	";    	    	    	    	
		if( $res = Yii::app()->db->createCommand($stmt)->queryAll() ){
			$data = array();
			foreach ($res as $val) {				
				if($val['promo_type']=="voucher"){
					$name=''; $min_spend=''; $use_until='';
					
					$pretty_expiration = Date_Formatter::date( $val['expiration'] );
					$pretty_amount = Price_Formatter::formatNumber( $val['amount'] );
					$pretty_min_order = Price_Formatter::formatNumber( $val['min_order'] );
					
					$use_until = t("Use until {{date}}",array(
					  '{{date}}'=>$pretty_expiration
					));
					
					if($val['voucher_type']=="percentage"){
						$name = t("({{coupon_name}}) {{amount}}% off",array(
						 '{{amount}}'=>Price_Formatter::convertToRaw($val['amount'],0),
						 '{{coupon_name}}'=>$val['voucher_name'],
						));
					} else {
						$name = t("({{coupon_name}}) {{amount}} off",array(
						 '{{amount}}'=>$pretty_amount,
						 '{{coupon_name}}'=>$val['voucher_name'],
						));
					}
					
					if($val['min_order']>0){
						$min_spend = t("Min. spend {{amount}}",array(
						  '{{amount}}'=>$pretty_min_order
						));
					}
										
					$data[] = array(
					  'promo_type'=>$val['promo_type'],
					  'promo_id'=>$val['promo_id'],
					  'title'=>$name,		
					  'sub_title'=>$min_spend,
					  'valid_to'=>$use_until,					  
					);
					
				} elseif ( $val['promo_type']=="offers" ){
					$transaction_type = json_decode($val['joining_merchant'],true);						
					$name = t("{{amount}}% off over {{order_over}} on {{transaction}}",array(
					 '{{amount}}'=>Price_Formatter::convertToRaw($val['voucher_name'],0),
					 '{{order_over}}'=>Price_Formatter::formatNumber($val['voucher_type']),
					 '{{transaction}}'=>CommonUtility::arrayToString($transaction_type)
					));
					$valid_to = t("valid {{from}} to {{to}}",array(
					 '{{from}}'=> Date_Formatter::date($val['status']),
					 '{{to}}'=> Date_Formatter::date($val['used_once']),
					));
					$data[] = array(
					  'promo_type'=>$val['promo_type'],
					  'promo_id'=>$val['promo_id'],
					  'title'=>$name,		
					  'sub_title'=>'',
					  'valid_to'=>$valid_to,					  
					);
				}
			}			
			return $data;
		}
		return false;
    }

    public static function applyVoucher($merchant_id='',$voucher_id='',$client_id='',$date='', $sub_total=0 )   
    {    	
    	$days = date("l",strtotime($date));
		$stmt="
		SELECT a.voucher_id,a.voucher_owner,a.merchant_id,a.joining_merchant,a.voucher_name,
		a.voucher_type,a.amount,a.min_order,
		a.used_once,a.max_number_use,a.selected_customer,
		
		(
		  select count(*) from {{ordernew}}
		  where promo_code = a.voucher_name
		  and
		  client_id=".q($client_id)."
		) as customer_use_count,
		
		(
		  select count(*) from {{ordernew}}
		  where promo_code = a.voucher_name
		) as all_use_count,
		
	    (
	      select count(*) from {{ordernew}}
	      where client_id=".q($client_id)."
	      and status not in ('initial_order','cancel','cancelled')
	    ) as first_order_count
		
		FROM {{voucher_new}} a
		WHERE voucher_id = ".q($voucher_id)."
		AND expiration >= ".q($date)."
		AND ".$days."=1			    
		AND status in ('publish','published')
		";		
				
	    if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
	    	
	    	$voucher_options = (integer)$res['used_once'];
			$max_number_use = (integer)$res['max_number_use'];
			$voucher_type = $res['voucher_type'];
			$min_order = floatval($res['min_order']);
			$less_amount = floatval($res['amount']);
			
			if($res['voucher_owner']=="admin"){
				$joining_merchant = !empty($res['joining_merchant'])?json_decode($res['joining_merchant'],true):'';
				if(is_array($joining_merchant) && count($joining_merchant)>=1){
					if(!in_array($merchant_id,(array)$joining_merchant)){						
						throw new Exception( "Voucher code not applicable to this merchant" );						
					}						
				}					
			} else if ($res['voucher_owner']=="merchant"){										
				if ($res['merchant_id']!=$merchant_id){
					throw new Exception( "Voucher code not applicable to this merchant" );
				}					
			} else {
				throw new Exception( "Voucher code not found" );
			}				
			
			if ($min_order>0){
				if ($sub_total<$min_order){											
					throw new Exception( t("Minimum order for this voucher is [min_order]",array(
						  '[min_order]'=>Price_Formatter::formatNumber($res['min_order'])
						))
					);
				} 
			}
			
			if($voucher_type=="percentage"){
				$less_amount = $sub_total *($less_amount/100);				
			}
			
			$total = floatval($sub_total) - floatval($less_amount);
			if($total<=0){									
			   throw new Exception( "Discount cannot be applied due to total less than zero after discount" );
			}
			
			switch ($voucher_options) {
				case 2:
					if($res['all_use_count']>0){
						throw new Exception( "This voucher code has already been used" );
					}
					break;
					
				case 3:
					if($res['customer_use_count']>0){
						throw new Exception( "Sorry but you have already use this voucher code" );
					}
					break;	
					
			    case 4:
			    	if($res['first_order_count']>0){
						throw new Exception( "This voucher can be use only in your first order" );
					}
			    	break;
			    	
			    case 5:				       
			        if($res['customer_use_count']>=$max_number_use){
			        	
			        	$error_msg='';
			        	if($res['customer_use_count']<=1){
			        		$error_msg = "You already used this voucher [count] time and cannot be use again";
			        	} else $error_msg = "You already used this voucher [count] times and cannot be use again";
			        	
						throw new Exception( 
						   Yii::t("default",$error_msg,array( 
						    '[count]'=>$max_number_use
						   ))
						);
					}
			    	break;
			    	
			    case 6:	
			      if($res['customer_use_count']>0){
						throw new Exception( "Sorry but you have already use this voucher code" );
				  }
				  
			      $selected_customer = !empty($res['selected_customer'])?json_decode($res['selected_customer'],true):false;
			      if(is_array($selected_customer) && count($selected_customer)>=1){			      	
			      	if(!in_array($client_id,(array)$selected_customer)){
			      		throw new Exception( "This voucher cannot be use in your account" );
			      	}
			      } else throw new Exception( "Voucher code not found" );
			      
			      break;
			    	
				default:
					break;
			}
						
			//return $less_amount;
			return array(
			  'promo_type'=>"voucher",
			  'less_amount'=>$less_amount,
			  'voucher_id'=>$res['voucher_id'],
			  'voucher_name'=>$res['voucher_name'],
			);
	    }
	    throw new Exception( "Voucher code not found" );
    }
    
    public static function applyOffers($merchant_id='',$offer_id='',$date='', $sub_total=0, $transaction_type='')
    {
    	$stmt="
    	SELECT * FROM {{offers}}    	
    	WHERE offers_id = ".q($offer_id)."
    	AND merchant_id =".q($merchant_id)."
    	AND status in ('publish','published')
    	AND ".q($date)." >= valid_from and ".q($date)." <= valid_to
    	";       	
    	if($res = Yii::app()->db->createCommand($stmt)->queryRow()){    	   
    	   $less = floatval($res['offer_percentage']);
    	   $min_order = floatval($res['offer_price']);		   
		   $transaction = json_decode($res['applicable_to'],true);					
		   
    	   if($min_order>0){
    	   	  if ($min_order>$sub_total){
    	   	  	  throw new Exception( t("Minimum order is [min_order]",array('[min_order]'=> Price_Formatter::formatNumber($min_order) )) );
    	   	  }
    	   }
    	   if(!in_array($transaction_type,(array)$transaction)){
    	   	   throw new Exception( t("this offer is not valid for [transaction_type]",array('[transaction_type]'=>t($transaction_type))) );
    	   }
    	   //return $less;
    	   return array(
			  'promo_type'=>"offers",
			  'less_amount'=>$less,	
			  'offers_id'=>$offer_id
			);
    	}
    	throw new Exception( "Ofers not valid" );
    }
    
    public static function findVoucherByID($voucher_id='')
    {
    	
    }
    
}
/*end class*/