<?php
class CPayments
{
	public static function paidStatus()
	{
		return 'paid';
	}
	
	public static function umpaidStatus()
	{
		return 'unpaid';
	}
	
	public static function getMerchantForCredentials($order_uuid='')
	{
		$stmt="
		SELECT a.merchant_id,a.payment_code,a.total,a.order_id,
		a.client_id,
		b.merchant_type, b.restaurant_name
		FROM {{ordernew}} a
		LEFT JOIN {{merchant}} b
		ON
		a.merchant_id = b.merchant_id
		WHERE 
		a.order_uuid = ".q($order_uuid)."
		";
		if ($res = CCacheData::queryRow($stmt)){
			return $res;
		}
		throw new Exception( 'no results' );
	}
	
	public static function PaymentList($merchant_id='' , $with_key=false)
	{
		$merchant = CMerchantListingV1::getMerchant($merchant_id);
		
		$data = array();
		$stmt="
		SELECT a.payment_name,a.payment_code,a.logo_type,a.logo_class,a.logo_image,a.path
		FROM {{payment_gateway}} a	
		WHERE a.payment_code IN (
		  select meta_value from {{merchant_meta}}
		  where meta_name='payment_gateway'
		  and meta_value = a.payment_code
		  and merchant_id = ".q($merchant_id)."
		)	
		AND a.status='active'
		ORDER BY a.sequence ASC
		";		
		if($merchant->merchant_type==1){
		   $stmt="
		   SELECT a.payment_name,a.payment_code,a.logo_type,a.logo_class,a.logo_image,a.path
			FROM {{payment_gateway}} a	
			WHERE a.payment_code IN (
			  select payment_code from {{payment_gateway_merchant}}
			  where merchant_id=".q($merchant_id)."
			  and status='active'
			  and payment_code in (
			      select meta_value from {{merchant_meta}}
				  where meta_name='payment_gateway'
				  and meta_value = a.payment_code
				  and merchant_id = ".q($merchant_id)."
			  )
			)	
			AND a.status='active'
			ORDER BY a.sequence ASC
		   ";
		}		
		if( $res = CCacheData::queryAll($stmt)){		 
		   foreach ($res as $val) {		   	  
		   	  $logo_image = '';
		   	  if(!empty($val['logo_image'])){
		   	    $logo_image = CMedia::getImage($val['logo_image'],$val['path'],Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('item'));
		   	  }
			
		   	  if($with_key)	{
		   	  	$data[$val['payment_code']] = array(
			   	    'payment_name'=>$val['payment_name'],
			   	    'payment_code'=>$val['payment_code'],
			   	    'logo_type'=>$val['logo_type'],
			   	    'logo_class'=>$val['logo_class'],
			   	    'logo_image'=>$logo_image,
			   	  );
		   	  } else {
			   	  $data[] = array(
			   	    'payment_name'=>$val['payment_name'],
			   	    'payment_code'=>$val['payment_code'],
			   	    'logo_type'=>$val['logo_type'],
			   	    'logo_class'=>$val['logo_class'],
			   	    'logo_image'=>$logo_image,
			   	  );
		   	  }
		   }
		   return $data;
		} 
		throw new Exception( 'no available payment method' );
	}
	
	public static function getPaymentCredentials($merchant_id='', $payment_code='',$merchant_type='')
	{
		$and = !empty($payment_code)?" AND payment_code =".q($payment_code)." ":'';
						
		if($merchant_type==1){			
			$stmt="
			SELECT merchant_id,payment_code,is_live,attr1,attr2,attr3,attr4
			FROM {{payment_gateway_merchant}} a
			WHERE merchant_id = ".q($merchant_id)."					
			$and
			";			
			//AND attr_json!=''
		} else {			
			$stmt="
			SELECT payment_code,is_live,attr1,attr2,attr3,attr4
			FROM {{payment_gateway}} a
			WHERE attr_json!=''
			$and
			";
		}		
		if( $res = CCacheData::queryAll($stmt)){
			$data = array();
			foreach ($res as $val) {				
				$data[$val['payment_code']] = array(
				  'is_live'=>intval($val['is_live']),
				  'attr1'=>trim($val['attr1']),
				  'attr2'=>trim($val['attr2']),
				  'attr3'=>trim($val['attr3']),
				  'attr4'=>trim($val['attr4']),
				  'merchant_id'=>isset($val['merchant_id'])?$val['merchant_id']:0,
				  'merchant_type'=>$merchant_type,
				);
			}			
			return $data;
		}
		throw new Exception( 'no results payment credentials' );
	}	

	public static function getPaymentCredentialsPublic($merchant_id='', $payment_code='',$merchant_type='')
	{
		$and = !empty($payment_code)?" AND payment_code =".q($payment_code)." ":'';
								
		if($merchant_type==1){			
			$stmt="
			SELECT merchant_id,payment_code,is_live,attr1,attr2,attr3,attr4
			FROM {{payment_gateway_merchant}} a
			WHERE merchant_id = ".q($merchant_id)."		
			AND status='active'			
			$and
			";			
			//AND attr_json!=''
		} else {			
			$stmt="
			SELECT payment_code,is_live,attr1,attr2,attr3,attr4
			FROM {{payment_gateway}} a
			WHERE attr_json!=''
			AND status='active'
			$and
			";
		}		
		if( $res = CCacheData::queryAll($stmt)){			
			$data = array(); $keys = '';
			foreach ($res as $val) {				
				switch ($val['payment_code']) {
					case 'paypal':					
					case "razorpay":
					case "mercadopago":
					case "bank":
						$keys = trim($val['attr1']);
						break;

					default:
						$keys = trim($val['attr2']);
						break;
				}
				$data[$val['payment_code']] = array(
				  'is_live'=>intval($val['is_live']),
				  //'attr1'=>trim($val['attr1']),
				   'attr2'=>$keys,
				//   'attr3'=>trim($val['attr3']),
				//   'attr4'=>trim($val['attr4']),
				  'merchant_id'=>isset($val['merchant_id'])?$val['merchant_id']:0,
				  'merchant_type'=>$merchant_type,
				);
			}			
			return $data;
		}
		throw new Exception( 'no results payment credentials' );
	}	
	
	public static function DefaultPaymentList()
	{
		$data = array();
		$stmt="
		SELECT a.payment_name,a.payment_code,a.logo_type,a.logo_class,a.logo_image,a.path
		FROM {{payment_gateway}} a			
		WHERE a.status='active'
		ORDER BY a.sequence ASC
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
		   foreach ($res as $val) {		   	  
		   	  $logo_image = CMedia::getImage($val['logo_image'],$val['path'],Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('item'));
		   	  $data[] = array(
		   	    'payment_name'=>$val['payment_name'],
		   	    'payment_code'=>$val['payment_code'],
		   	    'logo_type'=>$val['logo_type'],
		   	    'logo_class'=>$val['logo_class'],
		   	    'logo_image'=>$logo_image,
		   	  );
		   }
		   return $data;
		} 
		throw new Exception( 'no available payment method' );
	}
		
	public static function SavedPaymentList($client_id="" , $merchant_type='', $merchant_id='')
	{		
		$and = "";
		if($merchant_type==1){
			$and = "AND a.merchant_id =".q($merchant_id)." ";
		} else $and = "AND a.merchant_id = 0 ";
		
		$data = array();
		$stmt="
		SELECT a.payment_uuid,a.payment_code,a.as_default,a.reference_id,
		a.attr1,a.attr2,
		b.payment_name, b.logo_type, b.logo_class, b.logo_image, b.path
		
		FROM {{client_payment_method}} a
		LEFT JOIN {{payment_gateway}} b
		ON
		a.payment_code = b.payment_code
		
		WHERE a.client_id=".q($client_id)."		
		AND b.status = 'active'				
		$and
		ORDER BY payment_method_id DESC		
		";			
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {
				$logo_image = '';
				if(!empty($val['logo_image'])){
					$logo_image = CMedia::getImage($val['logo_image'],$val['path'],
					Yii::app()->params->size_image_thumbnail,
				    CommonUtility::getPlaceholderPhoto('item'));
				}
				$val['logo_image']=$logo_image;
				$data[]=$val;
			}
			return $data;
		}
		throw new Exception( 'no available saved payment' );
	}
	
	public static function MerchantSavedPaymentList($merchant_id)
	{		
	
		$data = array();
		$stmt="
		SELECT a.payment_uuid,a.payment_code,a.as_default,
		a.attr1,a.attr2,
		b.payment_name, b.logo_type, b.logo_class, b.logo_image, b.path
		
		FROM {{merchant_payment_method}} a
		LEFT JOIN {{payment_gateway}} b
		ON
		a.payment_code = b.payment_code
		
		WHERE a.merchant_id=".q($merchant_id)."		
		AND b.status = 'active'			
		ORDER BY payment_method_id DESC		
		";					
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {
				$logo_image = '';
				if(!empty($val['logo_image'])){
					$logo_image = CMedia::getImage($val['logo_image'],$val['path'],
					Yii::app()->params->size_image_thumbnail,
				    CommonUtility::getPlaceholderPhoto('item'));
				}
				$val['logo_image']=$logo_image;
				$data[]=$val;
			}
			return $data;
		}
		throw new Exception( 'no available saved payment' );
	}
	
	public static function delete($client_id='',$payment_uuid='')
	{
		
		$model = AR_client_payment_method::model()->find('client_id=:client_id AND payment_uuid=:payment_uuid', 
	        array(
	         ':client_id'=>$client_id,
	         ':payment_uuid'=>$payment_uuid,
	        )); 		
		        
	    if($model){    			
			if($model->delete()){
				return true;
			}
	    } else throw new Exception( 'record not found.' );
		throw new Exception( 'cannot delete records please try again.' );
	}
	
	public static function defaultPayment($client_id='')
	{
		$model = AR_client_payment_method::model()->find('client_id=:client_id AND as_default=:as_default', 
	        array(
	         ':client_id'=>intval($client_id),
	         ':as_default'=>1,
	        ));
	    if($model){
	    	return array(
	    	  'payment_uuid'=>$model->payment_uuid,
	    	  'payment_code'=>$model->payment_code,
	    	  'reference_id'=>$model->reference_id
	    	);
	    }
	    return false;
	}
	
	public static function getPaymentMethod($payment_uuid='', $client_id='')
	{
		$model = AR_client_payment_method::model()->find('client_id=:client_id AND payment_uuid=:payment_uuid', 
	        array(
	         ':client_id'=>intval($client_id),
	         ':payment_uuid'=>$payment_uuid,
	        ));
	    if($model){
	    	return array(
	    	  'payment_uuid'=>$model->payment_uuid,
	    	  'payment_code'=>$model->payment_code,
	    	  'reference_id'=>$model->reference_id
	    	);
	    }
	    return false;
	}
	
	public static function getPaymentMethodMeta($payment_uuid='', $client_id='')
	{
		$stmt="
		SELECT meta_name,meta_value
		FROM {{payment_method_meta}}
		WHERE payment_method_id IN (
		   select payment_method_id
		   from {{client_payment_method}}
		   where payment_uuid = ".q($payment_uuid)."
		   and
		   client_id = ".q( intval($client_id) )."
		)
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$data[$val['meta_name']] = $val['meta_value'];
			}
			return $data;
		}
		throw new Exception( 'No payment method meta found' );
	}

	public static function getPaymentTypeOnline($is_online=1)
	{
		$model = AR_payment_gateway::model()->findAll("is_online=:is_online",array(
		  'is_online'=>intval($is_online)
		));
		if($model){
			$data = array();
			foreach ($model as $items) {
			   	$data[$items->payment_code] = array(
			   	  'payment_code'=>$items->payment_code,
			   	  'payment_name'=>$items->payment_name,
			   	);
			}
			return $data;
		}
		return false;
	}
	
	public static function getPaymentList($is_online=1,$prefix='',$reference='')
	{
		$data = array();
		$stmt="
		SELECT a.payment_name,a.payment_code,a.logo_type,a.logo_class,a.logo_image,a.path
		FROM {{payment_gateway}} a			
		WHERE a.status='active'
		AND a.is_online = ".q(intval($is_online))."
		ORDER BY a.sequence ASC
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
		   foreach ($res as $val) {		   	  
		   	  $logo_image = CMedia::getImage($val['logo_image'],$val['path'],Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('item'));
		   	  $data[] = array(
		   	    'payment_name'=>$val['payment_name'],
		   	    'payment_code'=>$val['payment_code'],
		   	    'logo_type'=>$val['logo_type'],
		   	    'logo_class'=>$val['logo_class'],
		   	    'logo_image'=>$logo_image,
		   	    'prefix'=>$prefix,
		   	    'reference'=>$reference
		   	  );
		   }
		   return $data;
		} 
		throw new Exception( 'no available payment method' );
	}
	
	public static function getMerchantPayment($merchant_id=0, $payment_uuid='')
	{
		$model = AR_merchant_payment_method::model()->find("merchant_id=:merchant_id AND payment_uuid=:payment_uuid",array(
		  ':merchant_id'=>intval($merchant_id),
		  ':payment_uuid'=>$payment_uuid
		));
		if($model){
			return $model;
		}
		throw new Exception( 'payment not found' );
	}
	
}
/*end class*/