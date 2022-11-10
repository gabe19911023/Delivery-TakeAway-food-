<?php
class AR_smsbroadcast extends CActiveRecord
{	

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
		return '{{sms_broadcast}}';
	}
	
	public function primaryKey()
	{
	    return 'broadcast_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'sms_alert_message'=>t("SMS Message"),
		  'list_mobile_number'=>t("List of mobile number")
		);
	}
	
	public function rules()
	{
		return array(
		
		  array('send_to,sms_alert_message', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('merchant_id,send_to,list_mobile_number,sms_alert_message,status', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  
		  array('sms_alert_message', 'length', 'min'=>1, 'max'=>255,
          'tooShort'=>t(Helper_password_toshort) ,
          'tooLong'=>t(Helper_toolong)
          ),
          
          array('list_mobile_number','safe'),
          
          array('list_mobile_number','list_mobile_number')
		  
		);
	}

	
	public function list_mobile_number($attribute,$params)
	{
		if($this->send_to==3){
			if(empty($this->list_mobile_number)){
				$this->addError('list_mobile_number', t(Helper_field_required));
			}
		}		
	}
	
    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		$this->send_to = (integer) $this->send_to;
		
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();					
		} else {
			$this->date_modified = CommonUtility::dateNow();											
		}
		$this->ip_address = CommonUtility::userIp();	
		
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
