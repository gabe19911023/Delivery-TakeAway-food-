<?php
class AR_payment_gateway extends CActiveRecord
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
		return '{{payment_gateway}}';
	}
	
	public function primaryKey()
	{
	    return 'payment_id';	 
	}
		
	public function attributeLabels()
	{
		return array(		    
		  'payment_name'=>t("Payment name"),
		  'payment_code'=>t("Payment code"),
		  'logo_type'=>t("Logo type"),
		  'logo_class'=>t("Logo class icon"),
		  'logo_image'=>t("Image"),
		  'status'=>t("Status"),
		  'sequence'=>t("Sequence"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('payment_name,payment_code,logo_type,status,is_online', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('payment_name,payment_code,logo_type,logo_image', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  		  
		  array('payment_code','unique','message'=>t(Helper_field_unique)),
		  
		  array('logo_class,logo_image,sequence,attr1,attr2,attr3,attr4,attr_json,is_live,is_payout,is_plan','safe'),
		  
		  array('payment_code,payment_name','length','max'=>255),
		  array('logo_class','length','max'=>100),
		  
		  array('payment_code','removeSpaces'),
		  
		);
	}

	public function removeSpaces($attribute, $params)
	{
		$this->payment_code = str_replace(" ","",$this->payment_code);
	}
	
    protected function beforeSave()
	{
		if(parent::beforeSave()){
						
			if(DEMO_MODE){				
			    return false;
			}
			
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

		
	protected function beforeDelete()
	{				
	    if(DEMO_MODE){				
		    return false;
		}
	    return true;
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
