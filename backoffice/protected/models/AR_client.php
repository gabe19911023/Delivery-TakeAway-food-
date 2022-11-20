<?php
class AR_client extends CActiveRecord
{	

	public $npassword;
	public $cpassword;
	public $old_password;
	public $image;
	public $total;
	public $shortcode;
	
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
		return '{{client}}';
	}
	
	public function primaryKey()
	{
	    return 'client_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'first_name'=>t("First Name"),
		    'last_name'=>t("Last Name"),
		    'email_address'=>t("Email address"),
		    'contact_phone'=>t("Contact Phone"),
		    'npassword'=>t("Password"),
		    'cpassword'=>t("Confirm Password"),
		    'image'=>t("Profile Photo")
		);
	}
	
	public function rules()
	{
		return array(
		  array('first_name,last_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('first_name,last_name,email_address,contact_phone', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  
		  array('phone_prefix,contact_phone,password,cpassword,npassword,image','safe'),
		  
		//   array('email_address', 'email', 'message'=> CommonUtility::t(Helper_field_email) ),
		  
		//   array('contact_phone','length', 'min'=>8, 'max'=>15,
        //        'tooShort'=>t("{attribute} number is too short (minimum is 8 characters).")               
        //      ),
             
          //array('email_address,contact_phone','unique','message'=>t(Helper_field_unique)),
		//   array('email_address,contact_phone','ext.UniqueAttributesValidator','with'=>'merchant_id',
		//    'message'=>t(Helper_field_unique)
		//   ),            
          
          array('npassword, cpassword', 'length', 'min'=>4, 'max'=>40,
              'tooShort'=>t("{attribute} number is too short (minimum is 4 characters).")  
            ),
             
          //array('npassword', 'validatepassword'),  
          array('npassword', 'compare', 'compareAttribute'=>'cpassword',
          'message'=> t(Helper_password_compare) ),
          
          
           array('image', 'file', 'types'=>Helper_imageType, 'safe' => false,
			  'maxSize'=>Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => true
			),      
			
		   array('old_password,npassword,cpassword', 'required', 'on'=>'update_password'), 
		   
		   array('old_password', 'findPasswords', 'on'=>"update_password" ),
          
		);
	}
	
	public function validatepassword($attribute,$params)
	{		
		if(!empty($this->npassword)){
			if($this->npassword!=$this->cpassword){
				$this->addError('cpassword',t(Helper_password_compare));
			}
		}		
	}

	public function findPasswords($attribute, $params)
	{				
		if(!empty($this->old_password)){					   
		   if(!empty($this->npassword) && !empty($this->cpassword) ){		   	
			   $user = AR_client::model()->findByPk($this->client_id);			   			   
			   if ($user->password != md5($this->old_password)){
			   		$this->addError('old_password', t("Old password does not match with current password") );	
			   }
		   } else {
		   	    $this->addError('npassword', CommonUtility::t( Helper_field_required ) );	
		   	    $this->addError('cpassword', CommonUtility::t(  Helper_field_required ) );	
		   }
		}		
	}
	
	
    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();		
				$this->password = md5($this->password);
			} else {
				$this->date_modified = CommonUtility::dateNow();
			}
			$this->ip_address = CommonUtility::userIp();	
			
			if(empty($this->client_uuid)){
				$this->client_uuid = CommonUtility::createUUID("{{client}}",'client_uuid');
			}
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
				
		if($this->image){
			$params = array(
			  'title'=>$this->image->name,
			  'filename'=>$this->avatar,
			  'path'=>CommonUtility::uploadPath(false),
			  'size'=>$this->image->size,
			  'media_type'=>$this->image->type,
			  'date_created'=>CommonUtility::dateNow(),
			  'ip_address'=>CommonUtility::userIp()
			);
			Yii::app()->db->createCommand()->insert("{{media_files}}",$params);
		}		

		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$verification_type = 'email';		
		$get_params = array( 
			'client_uuid'=> $this->client_uuid,
			'key'=>$cron_key,
			'verification_type'=>$verification_type
		 );		
		switch ($this->scenario) {
			case "resend_otp":				
				CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/resend_otp?".http_build_query($get_params) );
				break;
			default:
			   break;
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
				
		$media = AR_media::model()->find( "filename=:filename" ,array(
		 ':filename'=>$this->avatar
		));
		if($media){
		   $media->delete(); 
		}
		
	}
		
}
/*end class*/
