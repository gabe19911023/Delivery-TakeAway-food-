<?php
class AR_city extends CActiveRecord
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
		return '{{location_cities}}';
	}
	
	public function primaryKey()
	{
	    return 'city_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'name'=>t("City Name"),		    
		    'postal_code'=>t("Postal Code/Zip code"),	
			'sequence'=>t("Sequence"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('name,state_id', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('name,postal_code', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('postal_code','safe'),
		  
		  array('sequence', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		return true;
	}
	
	protected function afterSave()
	{
		if(!parent::afterSave()){
			return false;
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		if(!parent::afterDelete()){
			return false;
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
