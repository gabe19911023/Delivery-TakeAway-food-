<?php
class AR_email_provider extends CActiveRecord
{	

	public $email_provider_id='';
	
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
		return '{{email_provider}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'provider_id'=>t("Provider ID"),
		    'provider_name'=>t("Provider name"),
		    'sender'=>t("Sender email"),
		    'sender_name'=>t("Sender name"),
		    'api_key'=>t("API KEY"),
		    'secret_key'=>t("SECRET KEY"),
		    'smtp_host'=>t("SMTP host"),
		    'smtp_port'=>t("SMTP port"),
		    'smtp_username'=>t("Username"),
		    'smtp_password'=>t("Password"),
		    'smtp_secure'=>t("Secure Connection"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('sender,provider_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('provider_id', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>"create" ),
		  
		  array('sender,provider_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),	
		  
		  array('sender','email','message'=> t(Helper_field_email) ),

		  array('api_key,as_default,secret_key,smtp_host,smtp_port,smtp_username,smtp_password,smtp_secure,sender_name',
		  'safe')   
		  
		);
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
		if($this->as_default==1){			
			self::updateDefault($this->id);
		}								
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
	
	public function updateDefault($id='')
	{
		 $stmt="
		 UPDATE {{email_provider}}			
		 SET as_default=0
		 WHERE id NOT IN (".q($id).")
		 ";					 
		 Yii::app()->db->createCommand($stmt)->query();
	}
		
}
/*end class*/
