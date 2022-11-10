<?php
class AR_item_relationship_size extends CActiveRecord
{	

	public $item_name,$item_group , $photo ,$path
	;
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
		return '{{item_relationship_size}}';
	}
	
	public function primaryKey()
	{
	    return 'item_size_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'item_size_id'=>t("item_size_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,item_token,item_id,size_id,', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('price,cost_price,discount,discount_type,discount_start,discount_end,sequence,sku,
		  available,low_stock,created_at,updated_at
		  ','safe'),
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->created_at = CommonUtility::dateNow();					
			} else {
				$this->updated_at = CommonUtility::dateNow();											
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