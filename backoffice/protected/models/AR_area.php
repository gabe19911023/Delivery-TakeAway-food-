<?php
class AR_area extends CActiveRecord
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
		return '{{location_area}}';
	}
	
	public function primaryKey()
	{
	    return 'area_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'name'=>t("Area Name"),		    
			'sequence'=>t("Sequence"),	
		);
	}
	
	public function rules()
	{
		return array(
		  array('name,city_id', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
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
