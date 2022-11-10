<?php
class AR_payment_gateway_merchant extends CActiveRecord
{	

	public $image;
	
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
		return '{{payment_gateway_merchant}}';
	}
	
	public function primaryKey()
	{
	    return 'payment_uuid';	 
	}
		
	public function attributeLabels()
	{
		return array(		    
		  'payment_name'=>t("Payment name"),
		  'payment_code'=>t("Payment code"),		  
		  'status'=>t("Status"),
		  'sequence'=>t("Sequence"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('payment_id,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('payment_id,merchant_id,payment_id,status', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  		  		  
		  array('sequence,attr1,attr2,attr3,attr4,attr_json,is_live','safe'),
		  
		  array('payment_code','length','max'=>255),		  
		  
		   array('payment_id', 'ext.UniqueAttributesValidator', 'with'=>'merchant_id' , 
		   'message'=>t("Payment gateway already added.") ),		 
		  
		);
	}
		
    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();		
				$this->payment_uuid = CommonUtility::createUUID("{{payment_gateway_merchant}}",'payment_uuid');
								
				$model = AR_payment_gateway::model()->find('payment_id=:payment_id', 
		        array(':payment_id'=>$this->payment_id)); 				        
		        if($model){
		        	$this->payment_code = $model->payment_code;
		        	$this->attr_json = $model->attr_json;
		        }
				
			} else {
				$this->date_modified = CommonUtility::dateNow();	
				if(empty($this->payment_uuid)){
					$this->payment_uuid = CommonUtility::createUUID("{{payment_gateway_merchant}}",'payment_uuid');
				}
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