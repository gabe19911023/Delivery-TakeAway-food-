<?php
class AR_cuisine_merchant extends CActiveRecord
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
		return '{{cuisine_merchant}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'merchant_id'=>t("merchant_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,cuisine_id', 		  
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
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
