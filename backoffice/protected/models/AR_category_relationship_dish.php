<?php
class AR_category_relationship_dish extends CActiveRecord
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
		return '{{category_relationship_dish}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		
	}
	
	public function rules()
	{
		return array(
		 array('cat_id,dish_id', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('cat_id,dish_id', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),
		);
	}

    protected function beforeSave()
	{		
		parent::beforeSave();
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