<?php
class AR_plans_invoice extends CActiveRecord
{	
	   		
	public $payment_code;
	public $title;
	public $total;
	
	public $restaurant_name, $logo , $path;
	
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
		return '{{plans_invoice}}';
	}
	
	public function primaryKey()
	{
	    return 'invoice_number';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'invoice_type'=>t("invoice_type"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('invoice_type,amount,status,invoice_ref_number,merchant_id,created', 
		  'required','message'=> CommonUtility::t( Helper_field_required ) ),
		  
		  array('invoice_uuid,due_date,date_created,ip_address','safe'),
		  //array('title,description,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),

		  array('invoice_ref_number','unique'),
		            
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();					
				$this->invoice_uuid = CommonUtility::createUUID("{{plans_invoice}}",'invoice_uuid');
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
		
		if($this->scenario=='invoice_paid'){
			/*SAVE PAYMENT METHOD*/
			AR_merchant_meta::saveMeta($this->merchant_id,'subscription_payment_method',$this->payment_code);			
			/*$merchant = Cmerchants::get($this->merchant_id);
			if($merchant){
				$merchant->status = 'active';
				$merchant->package_id = intval($this->package_id);
				$merchant->save();
			}*/
		}
						
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
