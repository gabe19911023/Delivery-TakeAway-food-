<?php
class AR_device_meta extends CActiveRecord
{		   				
	
	//public $interest = array();
	
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
		return '{{device_meta}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'device_id'=>t("device_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('device_id,meta_name,meta_value', 
		  'required','message'=> t( Helper_field_required ) ),		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				
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