<?php
class AR_client_cc extends CActiveRecord
{		
	public $expiration;
	public $payment_code;
	public $credit_card_number_raw;
	public $merchant_id;
	   		
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
		return '{{client_cc}}';
	}
	
	public function primaryKey()
	{
	    return 'cc_id';	 
	}
					
	/**
	 * Declares the validation rules.	 
	 */
	public function rules()
	{
		
		 Yii::import('ext.validators.ECCValidator');
		  
		 return array(
            array('client_id,card_name,credit_card_number,cvv,expiration', 
            'required','message'=> t(Helper_field_required) ),   

            array('billing_address',
            'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),                           
                        
            array('billing_address,payment_code,credit_card_number_raw','safe'),
            
            array('credit_card_number', 'findRecords' , 'on'=>'add'),
            array('credit_card_number', 'findRecords' , 'on'=>'update'),
            
            array('credit_card_number','ext.validators.ECCValidator',
             'message'=>t("Card Number is not a valid Credit Card number.")
            ),
            
            array('expiration', 'validateExpiration'),
            array('card_name', 'validateName'),
                        
         );
	}
		
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{		
		return array(
		    'card_name'=>t("Card Name"),
		    'credit_card_number'=>t("Card Number"),			
		);
	}	
	
	public function findRecords($attribute,$params)
	{				
		$mask_card = CommonUtility::mask($this->credit_card_number);
		$and = "";
		if($this->scenario=="update"){
			$and = " AND cc_id <> ".q($this->cc_id)." ";
		}
		$stmt="
		SELECT cc_id FROM {{client_cc}}
		WHERE credit_card_number=".q($mask_card)."
		AND client_id = ".q( (integer) $this->client_id)."
		$and
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			$this->addError('credit_card_number',"Card number already exist");
		} 
	}
	
	public function validateExpiration($attribute,$params)
	{
		$cc = new ECCValidator();
		
		if(!empty($this->expiration)){
			$expiratio = explode("/",$this->expiration);
			if(is_array($expiratio) && count($expiratio)>=1){
				$this->expiration_month = $expiratio[0];
				$this->expiration_yr = $expiratio[1];
			}
		}		
							
		if(!$cc->validateDate($this->expiration_month,  substr(date("Y"),0,2) .$this->expiration_yr )){
			$this->addError('expiration',"Expiration is not valid.");
		}
	}
	
	public function validateName($attribute,$params)
	{
		$cc = new ECCValidator();
		if(!$cc->validateName($this->card_name)){
			$this->addError('card_name',"Card Name is not valid.");
		}
	}
		
	protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
				
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();					
		} else {
			$this->date_modified = CommonUtility::dateNow();											
		}
		$this->ip_address = CommonUtility::userIp();		
				
		if(!empty($this->expiration)){
			$expiratio = explode("/",$this->expiration);
			if(is_array($expiratio) && count($expiratio)>=1){
				$this->expiration_month = $expiratio[0];
				$this->expiration_yr = $expiratio[1];
			}
		}			
		
		try {
			$this->encrypted_card = CreditCardWrapper::encryptCard($this->credit_card_number);
		} catch (Exception $e) {
			//
		}		
		
		$this->credit_card_number_raw = $this->credit_card_number;
		$this->credit_card_number = CommonUtility::mask($this->credit_card_number);
		
		$this->card_uuid = CommonUtility::generateUIID();		
				        		
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		$cc = new ECCValidator();
		$card_type = $cc->getCCType($this->credit_card_number_raw);		
				
		if($this->scenario=="add"){
			$model = new AR_client_payment_method;
		} else {
			$model = AR_client_payment_method::model()->find('client_id=:client_id AND reference_id=:reference_id', 
		    array(
		      ':client_id'=>Yii::app()->user->id,
		      ':reference_id'=>$this->cc_id
		    )); 
		    if(!$model){
		    	return false;
		    }
		}
		$model->client_id = $this->client_id;
		$model->payment_code = $this->payment_code;
		$model->as_default = (integer)1;
		$model->attr1 = $card_type;
		$model->attr2 = $this->credit_card_number;
		$model->reference_id = intval($this->cc_id);
		$model->merchant_id = intval($this->merchant_id);
		$model->save();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/