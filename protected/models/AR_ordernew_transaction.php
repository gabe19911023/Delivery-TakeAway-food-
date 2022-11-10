<?php
class AR_ordernew_transaction extends CActiveRecord
{	

	public $order_uuid;
	public $total;
	public $used_card;
	public $photo, $path;
	
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
		return '{{ordernew_transaction}}';
	}
	
	public function primaryKey()
	{
	    return 'transaction_id';
	}
		
	public function attributeLabels()
	{
		return array(
		 'transaction_id'=>"transaction_id",
		);
	}
	
	public function rules()
	{
		 return array(
            array('order_id,merchant_id,client_id,payment_code,trans_amount,currency_code,status', 
            'required','message'=> t(Helper_field_required) ),   
                        
            array('date_created,date_modified,ip_address,transaction_uuid,transaction_type,transaction_name','safe'),
                        
         );
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				$this->date_created = CommonUtility::dateNow();
				$this->transaction_uuid = CommonUtility::createUUID('{{ordernew_transaction}}','transaction_uuid');
			} else {				
				$this->date_modified = CommonUtility::dateNow();
			}			
			$this->ip_address = CommonUtility::userIp();
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		if($this->scenario=="partial_refund"){
			try {			    
			    CEarnings::partialRefund($this->order_uuid);
			} catch (Exception $e) {
			    //$e->getMessage();
			}	
		} elseif ( $this->scenario=="refund" ){
			try {			    
			    CEarnings::fullRefund($this->order_uuid);
			} catch (Exception $e) {
			    //$e->getMessage();
			}	
		}
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/