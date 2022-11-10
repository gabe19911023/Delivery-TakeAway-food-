<?php
class AR_services_fee extends CActiveRecord
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
		return '{{services_fee}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'service_id'=>t("service_id"),
		    'merchant_id'=>t("merchant_id"),
		    'service_fee'=>t("service_fee"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('service_id,merchant_id,service_fee', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('service_fee', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('date_modified','safe')
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			$this->date_modified = CommonUtility::dateNow();	
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
	}
		
}
/*end class*/
