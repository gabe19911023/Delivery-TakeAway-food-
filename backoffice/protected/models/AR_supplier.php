<?php
class AR_supplier extends CActiveRecord
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
		return '{{inventory_supplier}}';
	}
	
	public function primaryKey()
	{
	    return 'supplier_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'supplier_name'=>t("Supplier Name"),		    
		    'contact_name'=>t("Contact Name"),
		    'email'=>t("Email address"),	
		    'phone_number'=>t("Phone Number"),
		    'address_1'=>t("Address 1"),
		    'address_2'=>t("Address 2"),
		    'city'=>t("City"),
		    'postal_code'=>t("Postal/zip code"),
		    'country_code'=>t("Country"),
		    'region'=>"Region",
		    'notes'=>t("Notes")
		);
	}
	
	public function rules()
	{
		return array(
		  array('supplier_name,contact_name,email', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('supplier_name,contact_name,email,phone_number,address_1,address_2,city,
		  postal_code,country_code,region,notes', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('phone_number,address_1,address_2,city,
		  postal_code,country_code,region,notes','safe'),		
		  
		  array('supplier_name','unique','message'=>t(Helper_field_unique)),
		  
		  array('email', 'email', 'message'=> CommonUtility::t(Helper_field_email) ),       
		  		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if($this->isNewRecord){
			$this->updated_at = CommonUtility::dateNow();					
		} else {
			$this->created_at = CommonUtility::dateNow();											
		}
		$this->ip_address = CommonUtility::userIp();	
		
		return true;
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
