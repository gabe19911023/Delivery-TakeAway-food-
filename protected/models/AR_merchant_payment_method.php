<?php
class AR_merchant_payment_method extends CActiveRecord
{	

	public $method_meta;
	
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
		return '{{merchant_payment_method}}';
	}
	
	public function primaryKey()
	{
	    return 'payment_method_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'merchant_id'=>t("merchant_id")
		);
	}
	
	public function rules()
	{
		return array(

		     array('merchant_id,payment_code,as_default', 
            'required','message'=> t(Helper_field_required) ),   
            
            array('attr1,attr2,payment_uuid','safe'),
            
            array('payment_code', 'findRecords', 'on'=>"insert" ),
		
		);
	}
	
	public function findRecords($attribute,$params)
	{						
		$model = AR_merchant_payment_method::model()->find("merchant_id=:merchant_id AND payment_code=:payment_code AND attr5=:attr5",array(
		  ':merchant_id'=>intval($this->merchant_id),
		  ':payment_code'=>$this->payment_code,
		  'attr5'=>$this->attr5
		));
		if($model){
			$this->addError('payment_code', t("Payment already exist") );
		}
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				$this->date_created = CommonUtility::dateNow();		
				$this->payment_uuid = CommonUtility::generateUIID();			
			} else {
				$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
			
			Yii::app()->db->createCommand("UPDATE {{merchant_payment_method}}
		    SET as_default = 0
		    WHERE 
		    merchant_id=".q(intval($this->merchant_id))."
		    ")->query();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();			
	}

	protected function afterDelete()
	{
		parent::afterDelete();	
		
		switch ($this->payment_code) {			
			default:	
			  if(Yii::app()->getModule($this->payment_code)){
			     Yii::app()->getModule($this->payment_code)->deletePaymentMerchant($this);
			  }
			break;
			
		}			
	}
		
}
/*end class*/