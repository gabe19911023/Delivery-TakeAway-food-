<?php
class AR_item_relationship_category extends CActiveRecord
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
		return '{{item_relationship_category}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'item_id'=>t("item_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,item_id,cat_id', 		  
		  'required','message'=> t( Helper_field_required ),		  
		  ),
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
