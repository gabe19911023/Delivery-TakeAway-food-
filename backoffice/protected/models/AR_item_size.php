<?php
class AR_item_size extends CActiveRecord
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
		return '{{item_relationship_size}}';
	}
	
	public function primaryKey()
	{
	    return 'item_size_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'price'=>t("Price"),
		    'cost_price'=>t("Cost Price"), 
		    'discount'=>t("Discount"), 
		    'discount_start'=>t("Discount Start"), 
		    'discount_end'=>t("Discount End"), 
		    'sku'=>t("SKU"), 
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,item_id', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('price', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>'add_price' ),
		  
		  array('price', 'numerical', 'integerOnly' => false,
		  'min'=>0,
		  'tooSmall'=>t("You must enter at least greater than 0"),
		  'message'=>t(Helper_field_numeric)),
		  
		  array('merchant_id,item_token,item_id,size_id,price,cost_price,sku,available,low_stock
		  ', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('size_id,price,cost_price,sku,discount_start,discount_end,sequence,discount_type','safe'),
		  
		  array('cost_price,discount', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('sku','unique','message'=>t(Helper_field_unique),
			'on'=>'add_price'
			),
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if(DEMO_MODE && !$this->isNewRecord && in_array($this->merchant_id,DEMO_MERCHANT)){				
		    return false;
		}
		
		if($this->isNewRecord){
			$this->created_at = CommonUtility::dateNow();					
			$this->item_token = CommonUtility::generateToken("{{item_relationship_size}}",'item_token', CommonUtility::generateAplhaCode(20) );
		} else {
			$this->updated_at = CommonUtility::dateNow();											
		}		
		
		if(empty($this->discount_start)){
			$this->discount_start = null;
		}
		if(empty($this->discount_end)){
			$this->discount_end = null;
		}
				
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

		
	protected function beforeDelete()
	{				
	    if(DEMO_MODE && in_array($this->merchant_id,DEMO_MERCHANT)){				
	        return false;
	    }
	    return true;
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		
		Yii::app()->db->createCommand("DELETE FROM {{item_relationship_subcategory}}
		WHERE merchant_id = ".q((integer)$this->merchant_id)."  AND item_id=".q((integer)$this->item_id)."
		AND item_size_id = ".q((integer)$this->item_size_id)."
		 ")->query();  
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
