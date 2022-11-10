<?php
class CEarnings
{	
	public static function creditCommission($order_uuid='')
	{
		$order = COrders::get($order_uuid);	
		if($order){
			$params = array(					  		 
			  'transaction_description'=>"Commission on order #{{order_id}}",
			  'transaction_description_parameters'=>array('{{order_id}}'=>$order->order_id),					  
			  'transaction_type'=>"credit",
			  'transaction_amount'=>$order->commission,
			  'status'=>'paid',
			  'meta_name'=>"order",
			  'meta_value'=>$order->order_id
			);						
			
			$card_id = CWallet::getCardID( Yii::app()->params->account_type['admin'], 0);
									
			$criteria = new CDbCriteria;
			$criteria->alias = 'a';		
			$criteria->join='LEFT JOIN {{wallet_transactions}} b ON a.transaction_id = b.transaction_id';
			$criteria->addCondition('meta_name=:meta_name AND meta_value=:meta_value 
			AND b.transaction_type=:transaction_type AND card_id=:card_id ');
			$criteria->params = array( 
			  ':meta_name' => 'order', 
			  ':meta_value'=> $order->order_id,
			  ':transaction_type'=> 'credit',
			  ':card_id'=>$card_id
			);
			$models=AR_wallet_transactions_meta::model()->find($criteria);			
			if($models){
				throw new Exception( 'Transaction already exist' );
			}
			
			$resp = self::saveTransactions($order,$params,$card_id);
			return $resp;
		}
		throw new Exception( 'Order not found' );
	}
	
	public static function saveTransactions($order=null ,$params=array() , $card_id = 0)
	{		
		$all_online = CPayments::getPaymentTypeOnline();		
		$merchant = CMerchants::get($order->merchant_id);
		if($merchant && $order){
			if($order->payment_status=='paid' && $merchant->merchant_type==2 ){				
				if(array_key_exists($order->payment_code,(array)$all_online)){					
					$resp = CWallet::inserTransactions($card_id,$params);
					return $resp;
				} else throw new Exception( 'Payment is not online' );
			} else throw new Exception( 'Either merchant type or payment status not valid' );
		} 
		throw new Exception( 'Merchant not found' );
	}
	
	public static function autoApproval()
	{
		/*$models = AR_admin_meta::model()->find("meta_name=:meta_name",array(
		  'meta_name'=>'earning_auto_approval',		  
		));
		if($models){
			if($models->meta_value==1){
				return true;
			}
		}
		return false;*/
		return true;
	}
	
	public static function getDeliveredStatus()
	{
		$status = array();
		$model_status = AR_admin_meta::getMeta(array('status_delivered','status_completed'));
		if($model_status){
			foreach ($model_status as $item) {								
				$status[] = CommonUtility::cleanString($item['meta_value']);
			}
		}
		return $status;
	}
	
	public static function creditMerchant($order_uuid='')
	{
		$order = COrders::get($order_uuid);	
		if($order){
						
			$delivered_status = self::getDeliveredStatus();			
			if(!in_array( CommonUtility::cleanString($order->status) ,$delivered_status)){				
				throw new Exception( 'order status is not valid' );
			}			
						
			$transaction_amount = $order->merchant_earning;
						
			$meta = AR_ordernew_meta::model()->find("order_id=:order_id AND meta_name=:meta_name AND meta_value=:meta_value",array(
			  ':order_id'=>$order->order_id,
			  ':meta_name'=>'order_revision',
			  ':meta_value'=>'less_account'
			));			
			if($meta){
				$transaction_amount = $order->merchant_earning_original;
			}
			
			$params = array(					  		 
			  'transaction_description'=>"Sales on order #{{order_id}}",
			  'transaction_description_parameters'=>array('{{order_id}}'=>$order->order_id),					  
			  'transaction_type'=>"credit",
			  'transaction_amount'=>$transaction_amount,
			  'status'=>'paid',
			  'meta_name'=>"order",
			  'meta_value'=>$order->order_id
			);					
						
			$card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'], $order->merchant_id );
									
			$criteria = new CDbCriteria;
			$criteria->alias = 'a';		
			$criteria->join='LEFT JOIN {{wallet_transactions}} b ON a.transaction_id = b.transaction_id';
			$criteria->addCondition('meta_name=:meta_name AND meta_value=:meta_value 
			AND b.transaction_type=:transaction_type AND card_id=:card_id ');
			$criteria->params = array( 
			  ':meta_name' => 'order', 
			  ':meta_value'=> $order->order_id,
			  ':transaction_type'=> 'credit',
			  ':card_id'=>$card_id
			);
			$models=AR_wallet_transactions_meta::model()->find($criteria);			
			if($models){
				throw new Exception( 'Transaction already exist' );
			}			
			$resp = self::saveTransactions($order,$params,$card_id);
			return $resp;
		}
		throw new Exception( 'Order not found' );
	}
	
	public static function fullRefund($order_uuid='')
	{
	    $order = COrders::get($order_uuid);	    
	    if($order){	    	
	    	$params = array(					  		 
			   'transaction_description'=>"Refund commission order #{{order_id}}",
			   'transaction_description_parameters'=>array('{{order_id}}'=>$order->order_id),					  
			   'transaction_type'=>"debit",
			   //'transaction_amount'=>$order->commission,
			   'transaction_amount'=>$order->commission_original,
			   'status'=>'paid',
			   'meta_name'=>"order",			   
			   'meta_value'=>$order->order_id
			);
			try {
			   $card_id = CWallet::getCardID( Yii::app()->params->account_type['admin'], 0);			   
			   $resp = CWallet::inserTransactions($card_id,$params);
			   return $resp;
			} catch (Exception $e) {		       
		       throw new Exception($e->getMessage());
	        }
	    }
	    throw new Exception( 'Order not found' );
	}
	
	public static function partialRefund($order_uuid='')
	{
	    $order = COrders::get($order_uuid);
	    if($order){	    	
	    	$params = array(					  		 
			   'transaction_description'=>"Adjustment commission order #{{order_id}}",
			   'transaction_description_parameters'=>array('{{order_id}}'=>$order->order_id),					  
			   'transaction_type'=>"debit",
			   'transaction_amount'=>($order->adjustment_commission * -1),
			   'status'=>'paid',
			   'meta_name'=>"order",			   
			   'meta_value'=>$order->order_id
			);
	    	try {
			   $card_id = CWallet::getCardID( Yii::app()->params->account_type['admin'], 0);
			   $resp = CWallet::inserTransactions($card_id,$params);	    	
			   return $resp;
			} catch (Exception $e) {		       
		       throw new Exception($e->getMessage());
	        }
	    }
	    throw new Exception( 'Order not found' );
	}
	
	public static function requestPayout($card_id=0, $amount=0 , $account='' , $to_account=array() , $status='unpaid')
	{
		$balance = CWallet::getBalance($card_id);
				
		if($amount<=0){
			throw new Exception( 'Amount must be greater than 0' );
		}
		
		$payout_settings = AdminTools::getPayoutSettings();
		$minimum_amount = isset($payout_settings['minimum_amount'])?floatval($payout_settings['minimum_amount']):0;
		
		if($minimum_amount>0){
			if($minimum_amount>$amount){
				throw new Exception( t("Payout minimum amount is {{minimum_amount}}",
				array('{{minimum_amount}}'=>Price_Formatter::formatNumber($minimum_amount))) );
			}
		}
		
		if($amount<=$balance){
		   	$params = array(			  
			  'transaction_description'=>"Payout to {{account}}",
			  'transaction_description_parameters'=>array('{{account}}'=>$account),			  
			  'transaction_type'=>"payout",
			  'transaction_amount'=>floatval($amount),		
			  'status'=>$status
			);			
			$resp = CWallet::inserTransactions($card_id,$params);
			return $resp;		
		} else throw new Exception( t("The amount may not be greater than [balance].",array('[balance]'=>$balance)) );		
	}
	
	public static function getTotalMerchantBalance()
	{		
		$running_balance = 0;
		$criteria = new CDbCriteria;
		$criteria->select = "sum(meta_value) as running_balance";
		$criteria->addCondition('meta_name=:meta_name');	
		$criteria->params = array(':meta_name' => 'running_balance');			
		$model=AR_merchant_meta::model()->find($criteria);			
		if($model){			
			$running_balance = $model->running_balance;
		}
		return floatval($running_balance);
	}
	
}
/*end class*/