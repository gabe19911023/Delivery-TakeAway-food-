<?php
class AR_client_address extends CActiveRecord
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
		return '{{client_address}}';
	}
	
	public function primaryKey()
	{
	    return 'address_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'address_uuid'=>t("address_uuid"),
		  'place_id'=>t("place_id"),
		  'location_name'=>t("Aparment, suite or floor"),
		  'formatted_address'=>t("Address"),
		  'place_id'=>t("Places ID"),
		  'latitude'=>t("Latitude"),
		  'longitude'=>t("Longitude"),
		);
	}
	
	public function rules()
	{
		return array(
		  
		  array('client_id,address_uuid,place_id,address1,address2,postal_code,country,country_code,formatted_address,delivery_instructions', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  		  
		  array('client_id,formatted_address,place_id,latitude,longitude',
		  'required'),

		  //array('place_id','unique','message'=> t("Address place id already exist") ),
		  array('place_id','ext.UniqueAttributesValidator','with'=>'client_id',
		   'message'=>t("Address place id already exist")
		  ),
		  
		  array('address2,postal_code,address_label,address1,country,
		  country_code,delivery_instructions,delivery_options','safe'),

		  array('client_id', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),	  
		  
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
			
			if(empty($this->address_uuid)){
				$this->address_uuid = CommonUtility::createUUID("{{client_address}}",'address_uuid');
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
