<?php
class AR_merchant_earnings extends CActiveRecord
{	
	
	public $meta_name;
	public $meta_value;
	
	public $logo;
	public $path;
	public $restaurant_name;
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
		return '{{merchant_earnings}}';
	}
	
	public function primaryKey()
	{
	    return 'transaction_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'transaction_description'=>t("Description"),
		    'transaction_type'=>t("Transaction Type"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,transaction_date,transaction_description,transaction_type,transaction_amount,running_balance,ip_address',
		   'required','message'=> CommonUtility::t( Helper_field_required ) ),
		   
		  array('transaction_description', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),    
		  
		  array('transaction_uuid,status','safe'),   
		  
		  array('transaction_amount', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('transaction_type','validateTransaction')
		    
		);
	}

	public function validateTransaction($attribute, $params)
	{		
		switch ($this->scenario) {
			case "payout":
				
				$options = AdminTools::getPayoutSettings();
				$payout_request_enabled = isset($options['enabled'])?$options['enabled']:false;
				$payout_minimum_amount = isset($options['minimum_amount'])?$options['minimum_amount']:false;
				
				if($payout_request_enabled){						
					if($payout_minimum_amount>0){						
						if($this->transaction_amount<$payout_minimum_amount){						
							$this->addError('transaction_type', t("Payout minimum amount is {{minimum_amount}}",array(
							 '{{minimum_amount}}'=>Price_Formatter::formatNumber($payout_minimum_amount)
							)) );	
						}
					}					
				} else $this->addError('transaction_type', t("Payout is not available") );				
				break;					
		}		
	}
	
    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->transaction_uuid = CommonUtility::createUUID("{{merchant_earnings}}",'transaction_uuid');
				$this->transaction_date = CommonUtility::dateNow();					
			} 
			$this->ip_address = CommonUtility::userIp();				
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		$model = new AR_merchant_earnings_meta;
		$model->transaction_id = $this->transaction_id;
		$model->meta_name = $this->meta_name;
		$model->meta_value = $this->meta_value;
		$model->save();
		
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
		}
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/