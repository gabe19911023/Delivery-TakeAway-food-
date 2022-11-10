<?php
class AR_sms_broadcast_details extends CActiveRecord
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
		return '{{sms_broadcast_details}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'contact_phone'=>t("contact_phone"),
		  'sms_message'=>t("sms_message"),
		);
	}
	
	public function rules()
	{
		return array(
		
		  array('contact_phone,sms_message', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('contact_phone,sms_message', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  		            
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
			$this->date_executed = CommonUtility::dateNow();											
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