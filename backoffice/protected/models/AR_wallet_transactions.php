<?php
class AR_wallet_transactions extends CActiveRecord
{		   				
	
	public $meta_name;
	public $meta_value;
	public $meta_array;
	
	public $merchant_id, $restaurant_name,$logo,$path;
	public $total_earning;	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return static the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{wallet_transactions}}';
	}
	
	public function primaryKey()
	{
	    return 'transaction_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'transaction_id'=>t("Transaction ID"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('card_id,transaction_date,transaction_description,transaction_type,transaction_amount', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('transaction_uuid,ip_address','safe'),
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->transaction_uuid = CommonUtility::createUUID("{{wallet_cards}}",'card_uuid');
				$this->transaction_date = CommonUtility::dateNow();					
			} 
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();			

		if(!empty($this->meta_name) && !empty($this->meta_value) ){
			$model = new AR_wallet_transactions_meta;
			$model->transaction_id = $this->transaction_id;
			$model->meta_name = $this->meta_name;
			$model->meta_value = $this->meta_value;
			$model->save();
		}	
		
		if(is_array($this->meta_array) && count($this->meta_array)>=1){
			$model = new AR_wallet_transactions_meta;
			foreach ($this->meta_array as $item) {
				$model->meta_name = isset($item['meta_name'])?$item['meta_name']:'';
				$model->meta_value = isset($item['meta_value'])?$item['meta_value']:'';
				$model->save();
			}
		}
		
		
		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$get_params = array( 
		   'transaction_uuid'=> $this->transaction_uuid,
		   'key'=>$cron_key,
		);	
						
		switch ($this->scenario) {
			case "payout":
				CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/after_request_payout?".http_build_query($get_params) );
				break;
			case "payout_paid":		
				CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afterpayout_paid?".http_build_query($get_params) );
				break;					
				
			case "payout_cancel":				
				CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afterpayout_cancel?".http_build_query($get_params) );
				break;			
			case "cashin":				
			     try {
				     $card_id = CWallet::getCardID( Yii::app()->params->account_type['admin']); 
				     $params = array(					  		 
				      'transaction_description'=>"Cash in",			      
				      'transaction_type'=>"credit",
				      'transaction_amount'=>floatval($this->transaction_amount),
				      'status'=>'paid',		
				      'meta_name'=>"transaction_reference",
				      'meta_value'=>$this->meta_value,
				    );
			        $resp = CWallet::inserTransactions($card_id,$params);  
		        } catch (Exception $e) {
		        	
		        }
			
			    break;
		}
	}

	protected function afterDelete()
	{
		parent::afterDelete();			
	}
		
}
/*end class*/
