<?php
class AR_item_promo extends CActiveRecord
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
		return '{{item_promo}}';
	}
	
	public function primaryKey()
	{
	    return 'promo_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'item_id'=>t("Item Name"),
		  'buy_qty'=>t("Buy (qty)"),
		  'get_qty'=>t("Get (qty"),
		  'discount_start'=>t("Discount Start"),
		  'discount_end'=>t("Discount End"),
		);
	}
	
	public function rules()
	{
	   return array(
		  array('merchant_id,item_id,promo_type,item_id_promo', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('merchant_id,item_id,promo_type', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  
		  array('buy_qty,get_qty', 'numerical', 'integerOnly' => true,
		  'min'=>1,
		  'tooSmall'=>t("You must enter at least greater than 1"),
		  'message'=>t(Helper_field_numeric)),
		  		  
		  array('item_id_promo', 'numerical', 'integerOnly' => true,
		  'min'=>1,
		  'tooSmall'=>t("You must select items"),
		  'message'=>t(Helper_field_numeric)),
		  
		  array('discount_start,discount_end','safe'),
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
				
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();					
		} else {
			$this->date_modified = CommonUtility::dateNow();											
		}
		$this->ip_address = CommonUtility::userIp();	
		
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

	protected function afterDelete()
	{
		parent::afterDelete();		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
		
	}
		
}
/*end class*/
