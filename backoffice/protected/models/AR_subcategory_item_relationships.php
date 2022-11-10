<?php
class AR_subcategory_item_relationships extends CActiveRecord
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
		return '{{subcategory_item_relationships}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'subcat_id'=>t("subcat_id"),
		    'sub_item_id'=>t("sub_item_id"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('subcat_id,sub_item_id', 		  
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
