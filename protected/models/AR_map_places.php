<?php
class AR_map_places extends CActiveRecord
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
		return '{{map_places}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'reference_type'=>t("reference type")
		);
	}
	
	public function rules()
	{
		return array(
		  
		  array('reference_type,reference_id,latitude,longitude,
		  address1,address2,country,postal_code,formatted_address', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  
		  array('reference_type,reference_id,latitude,longitude,
		  address1,address2,country,postal_code,formatted_address','safe')
		  		  
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
