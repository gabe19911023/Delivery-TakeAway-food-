<?php
class AR_ordernew_attributes extends CActiveRecord
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
		return '{{ordernew_attributes}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		 'order_id'=>"order_id",
		);
	}
	
	public function rules()
	{
		 return array(
            array('order_id,item_row,meta_name,meta_value', 
            'required','message'=> t(Helper_field_required) ),                          
         );
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
			} else {				
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