<?php
class CWallet
{
	public static function getCardID($account_type='', $account_id=0)
	{
		$model = AR_wallet_cards::model()->find("account_type=:account_type AND account_id=:account_id",array(
		  ':account_type'=>$account_type,
		  ':account_id'=>intval($account_id)
		));
		if($model){
			return $model->card_id;
		}
		throw new Exception( 'card not found' );
	}
	
	public static function getCardUUID($account_type='', $account_id=0)
	{
		$model = AR_wallet_cards::model()->find("account_type=:account_type AND account_id=:account_id",array(
		  ':account_type'=>$account_type,
		  ':account_id'=>intval($account_id)
		));
		if($model){
			return $model->card_uuid;
		}
		throw new Exception( 'card uuid not found' );
	}
	
	public static function getAccountID($card_id=0)
	{
		$model = AR_wallet_cards::model()->find("card_id=:card_id",array(
		  ':card_id'=>intval($card_id),		  
		));
		if($model){
			return $model->account_id;
		}
		throw new Exception( 'card uuid not found' );
	}
	
	public static function createCard($account_type='', $account_id=0)
	{		
		try {
			$card_id = self::getCardID($account_type,$account_id);
			return $card_id;
		} catch (Exception $e) {
			$model = new AR_wallet_cards;
			$model->account_type = trim($account_type);
			$model->account_id = intval($account_id);
			if($model->save()){
				return $model->card_id;
			}						
			throw new Exception( CommonUtility::parseModelErrorToString($model->getErrors()) );
		}		
	}
	
	public static function inserTransactions($card_id=0,$data=array())
	{
		$transaction_description = isset($data['transaction_description'])?$data['transaction_description']:'';
		$transaction_description_parameters = isset($data['transaction_description_parameters'])?$data['transaction_description_parameters']:'';
		$transaction_type = isset($data['transaction_type'])?$data['transaction_type']:'';
		$transaction_amount = isset($data['transaction_amount'])?$data['transaction_amount']:0;
		$status = isset($data['status'])?$data['status']:'';
		
		$meta_name = isset($data['meta_name'])?$data['meta_name']:'';
		$meta_value = isset($data['meta_value'])?$data['meta_value']:'';

		$last_balance = self::getBalance($card_id);					
		//if($transaction_type=="credit"){
		if($transaction_type=="credit" || $transaction_type=="cashin"){
			$running_balance = floatval($last_balance) + floatval($transaction_amount);
		} else $running_balance = floatval($last_balance) - floatval($transaction_amount);
		
		$earnings = new AR_wallet_transactions;		
		$earnings->scenario = $transaction_type;	
		$earnings->card_id = $card_id; 	
		$earnings->transaction_date = CommonUtility::dateNow();
		$earnings->transaction_description = $transaction_description;
		$earnings->transaction_description_parameters = json_encode($transaction_description_parameters);
		$earnings->transaction_type = $transaction_type;
		$earnings->transaction_amount = floatval($transaction_amount);
		$earnings->running_balance = floatval($running_balance);
		$earnings->status = $status;
		$earnings->ip_address = CommonUtility::userIp();
		$earnings->meta_name = $meta_name;
		$earnings->meta_value = $meta_value;	
		if($earnings->save()){			
			return $earnings->transaction_id;
		} else throw new Exception( CommonUtility::parseModelErrorToString( $earnings->getErrors()) );				
	}
	
	public static function getBalance($card_id=0)
	{
		$running_balance = 0;
		$criteria = new CDbCriteria;		
		$criteria->addCondition("card_id=:card_id");
		$criteria->params=array(':card_id'=>intval($card_id));
		$criteria->select = "transaction_id,running_balance";		
		$criteria->order = "transaction_id DESC";			
		$model=AR_wallet_transactions::model()->find($criteria);		
		if($model){			
			$running_balance = $model->running_balance;
		}
		return floatval($running_balance);
	}
}
/*end class*/