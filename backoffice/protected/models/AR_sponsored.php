<?php
class AR_sponsored extends CActiveRecord
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
		return '{{merchant}}';
	}
	
	public function primaryKey()
	{
	    return 'merchant_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'merchant_id'=>t("Merchant"),
		    'sponsored_expiration'=>t("Expiration Date"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id, sponsored_expiration', 'required','message'=> CommonUtility::t( Helper_field_required ) ),
		  array('merchant_id, sponsored_expiration', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),         
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
		
		
}
/*end class*/
