<?php
class CMerchantEarnings
{
	public static function autoApproval()
	{
		$models = AR_admin_meta::model()->find("meta_name=:meta_name",array(
		  'meta_name'=>'earning_auto_approval',		  
		));
		if($models){
			if($models->meta_value==1){
				return true;
			}
		}
		return false;
	}
	
	public static function creditOrder($order_uuid='')
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
		}
		throw new Exception( 'Order not found' );
	}
	
	public static function saveTransactions($order=null ,$params=array())
	{		
		$all_online = CPayments::getPaymentTypeOnline();		
		$merchant = CMerchants::get($order->merchant_id);
		if($merchant && $order){
			if($order->payment_status=='paid' && $merchant->merchant_type==2 ){				
				if(array_key_exists($order->payment_code,(array)$all_online)){
					$card_id = CWallet::getCardID( Yii::app()->params->account_type['merchant'] );										
					$resp = CWallet::inserTransactions($card_id,$params);
					return $resp;
				} else throw new Exception( 'Payment is not online' );
			} else throw new Exception( 'Either merchant type or payment status not valid' );
		} 
		throw new Exception( 'Merchant not found' );
	}
}
/*end class*/