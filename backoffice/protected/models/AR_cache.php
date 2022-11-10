<?php
class AR_cache extends CActiveRecord
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
		return '{{cache}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'date_modified'=>t("Date modified"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('date_modified', 
		  'required','message'=> t( Helper_field_required ) ),		  		 		  
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
