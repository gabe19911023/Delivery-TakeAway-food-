<?php
class AR_cart_attributes extends CActiveRecord
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
		return '{{cart_attributes}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'cart_uuid'=>t("cart_uuid"),
		  'meta_name'=>t("meta_name"),
		  'meta_id'=>t("meta_id"),
		);
	}
	
	public function rules()
	{
		return array(
		  		  
		  array('cart_row,cart_uuid,meta_name,meta_id', 
		  'required','message'=> t( "Required" ) ),		  		  
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			$this->last_update = CommonUtility::dateNow();			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
