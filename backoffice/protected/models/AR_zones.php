<?php
class AR_zones extends CActiveRecord
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
		return '{{zones}}';
	}
	
	public function primaryKey()
	{
	    return 'zone_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'zone_id'=>t("Zone id"),
		    'zone_name'=>t("Name"),
		    'description'=>t("Description"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('zone_name', 
		  'required','message'=> t("Zone name is required") ),
		  array('zone_name,description', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('zone_uuid,zone_name','safe'),
		  array('zone_name','unique','message'=>t("Zone name already exist")),
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
			
			if(empty($this->zone_uuid)){
				$this->zone_uuid = CommonUtility::createUUID("{{zones}}",'zone_uuid');
			}
			
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
