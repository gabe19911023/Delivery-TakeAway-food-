<?php
class AR_subscriber extends CActiveRecord
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
		return '{{subscriber}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		 'client_id'=>"client_id",
		);
	}
	
	public function rules()
	{
		 return array(
            array('email_address,merchant_id,subcsribe_type', 
            'required','message'=> t(Helper_field_required) ),   

            array('email_address','email' , 'message'=>t("Email address is not valid")),

            array('email_address','ext.UniqueAttributesValidator','with'=>'subcsribe_type',
            'message'=>t("Email already exist")
            ),
                        
            array('client_id,date_created,ip_address','safe'),
                        
         );
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){	
                $this->date_created = CommonUtility::dateNow();			
			} 
            $this->ip_address = CommonUtility::userIp();
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
												
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
						
		CCacheData::add();
	}
		
}
/*end class*/
