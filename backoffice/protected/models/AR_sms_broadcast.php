<?php
class AR_sms_broadcast extends CActiveRecord
{	

	public $multi_language, $title_translation, $description_translation;
	
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
		return '{{sms_broadcast_details}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'contact_phone'=>t("Mobile Number"),
		    'sms_message'=>t("Message"),
		    'gateway'=>t("Provider")
		);
	}
	
	public function rules()
	{
		return array(
		  array('contact_phone,sms_message,gateway', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('contact_phone,sms_message', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  

		  array('sms_message', 'length', 'min'=>1, 'max'=>1600,
              'tooShort'=>t("Message is too short") ,
              'tooLong'=>t("Message is too long") ,
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
			//
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
