<?php
class AR_voucher extends CActiveRecord
{	
	   				
	public $days_available,$apply_to_merchant,$apply_to_customer;
	
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
		return '{{voucher_new}}';
	}
	
	public function primaryKey()
	{
	    return 'voucher_id';	 
	}
		
	public function attributeLabels()
	{
		return array(		    
		  'voucher_name'=>t("Coupon name"),
		  'max_number_use'=>t("Define max number of use"),
		  'voucher_name'=>t("Coupon name"),		  
		  'amount'=>t("Amount"),		 
		  'min_order'=>t("Min Order"),		
		  'expiration'=>t("Expiration"),				   		  
		);
	}
	
	public function rules()
	{
		return array(		  
		   array('voucher_name,voucher_type,amount,days_available,status,expiration', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('voucher_name,voucher_type,apply_to_merchant', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  
		  array('apply_to_merchant,min_order,apply_to_customer,used_once,max_number_use','safe'),
		  
		  array('expiration', 'date', 'format'=>'yyyy-M-d'),
		  
		  array('amount', 'numerical', 'integerOnly' => false,
		    'min'=>1,
		    'tooSmall'=>t(Helper_field_numeric_tooSmall),
		    'message'=>t(Helper_field_numeric)),
		    
		  array('amount,min_order', 'numerical', 'integerOnly' => false,		    
		    'message'=>t(Helper_field_numeric)),
		    
		  array('max_number_use', 'numerical', 'integerOnly' => true,
		    'message'=>t(Helper_field_numeric)),  
		    
		  array('voucher_name','unique','message'=>t(Helper_field_unique))
		    
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();					
			} else {
				$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
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
