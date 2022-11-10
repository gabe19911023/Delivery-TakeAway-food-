<?php
class AR_ordernew_addons extends CActiveRecord
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
		return '{{ordernew_addons}}';
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
            array('order_id,item_row,subcat_id,sub_item_id,qty', 
            'required','message'=> t(Helper_field_required) ),   
                        
            array('multi_option','safe'),
                        
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