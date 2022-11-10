<?php
class AR_gpdr_request extends CActiveRecord
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
		return '{{gpdr_request}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'request_type'=>"request_type"
		);
	}
	
	public function rules()
	{
		 return array(
            array('client_id,first_name,last_name,email_address,status', 
            'required','message'=> t(Helper_field_required) ),      

            array('client_id,first_name,last_name,email_address,status', 
		    'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
                       
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