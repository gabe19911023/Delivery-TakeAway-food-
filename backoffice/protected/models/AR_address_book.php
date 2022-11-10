<?php
class AR_address_book extends CActiveRecord
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
		return '{{address_book}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'street'=>t("Street"),
		    'city'=>t("City"),
		    'state'=>t("State"),
		    'zipcode'=>t("Zip code"),
		    'location_name'=>t("Apartment suite, unit number, or company names"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('street,city,state,zipcode,country_code,location_nickname', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('street,city,state,zipcode,location_name,country_code', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('as_default','safe'),
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();					
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
	}

	protected function afterDelete()
	{
		parent::afterDelete();			
	}
		
}
/*end class*/
