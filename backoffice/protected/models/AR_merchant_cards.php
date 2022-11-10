<?php
class AR_merchant_cards extends CActiveRecord
{	

	public $expiration;
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
		return '{{merchant_cc}}';
	}
	
	public function primaryKey()
	{
	    return 'mt_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'card_name'=>t("Card name"),		    
		    'credit_card_number'=>t("Credit Card Number"),	
		    'expiration'=>t("Expiration"),
		    'cvv'=>t("CVV"),
		    'billing_address'=>t("Billing Address"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('card_name,credit_card_number,cvv,expiration', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('card_name,credit_card_number', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('credit_card_number','unique','message'=>t(Helper_field_unique)),
		  
		  array('encrypted_card,billing_address,merchant_id','safe'),
		  
		  array('cvv', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('credit_card_number', 'length', 'min'=>19, 'max'=>19,
              'tooShort'=>t(Helper_password_toshort) ,
              ),
              
          array('expiration', 'length', 'min'=>5, 'max'=>5,
              'tooShort'=>t(Helper_password_toshort) ,
              ),    
              
          array('cvv', 'length', 'min'=>3, 'max'=>3,
              'tooShort'=>t(Helper_password_toshort) ,
              ),        
		  
		);
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
		
		$this->credit_card_number = CommonUtility::maskCardnumber($this->credit_card_number);
		
		return true;
	}
	
	protected function afterSave()
	{
		if(!parent::afterSave()){
			return false;
		}
	}

	protected function afterDelete()
	{
		if(!parent::afterDelete()){
			return false;
		}
	}
		
}
/*end class*/
