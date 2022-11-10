<?php
class AR_cart_addons extends CActiveRecord
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
		return '{{cart_addons}}';
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
		  		  
		  array('cart_row,cart_uuid,subcat_id,sub_item_id,qty', 
		  'required','message'=> t( "Required" ) ),
		  		  		 
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				//
			} else {
				//
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
