<?php
class AR_clientsignup extends CActiveRecord
{	
	
	public $cpassword;
	public $old_password;
	public $image;
	public $capcha;
	public $recaptcha_response;

	public $google_client_id;
	public $captcha_secret;
	public $merchant_id;
		
	//public $social_token;
	
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
		    'email_address'=>t("Email Address"),
		    'contact_phone'=>t("Contact Phone"),		    
		    'cpassword'=>t("Confirm Password"),
		    'image'=>t("Profile Photo")
		);
	}
	
	public function rules()
	{
		return array(
		  array('phone_prefix,contact_phone,mobile_verification_code,status', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>'registration_phone' ),
		  array('cart_uuid,status', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>'guest' ),
		  
		  array('phone_prefix,contact_phone,mobile_verification_code,status,
		  first_name,last_name,email_address', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  		 
		  array('phone_prefix,contact_phone,mobile_verification_code,status', 
		  'required','message'=> t( Helper_field_required ) , 'on'=>"complete_registration" ),
		  
		  //array('email_address,contact_phone','unique'),
		  array('email_address,contact_phone','ext.UniqueAttributesValidator','with'=>'merchant_id',
		   'message'=>t(Helper_field_unique)
		  ),            

		  array('contact_phone','length', 'min'=>8, 'max'=>15,
               'tooShort'=>t("{attribute} number is too short (minimum is 8 characters).")               
             ),
		  
		  array('email_address','email'),
		  		  
		  array('password', 'compare', 'compareAttribute'=>'cpassword',
              'message'=> t("Password and confirm password does not match") ,'on'=>'register' ),
              
          array('password', 'compare', 'compareAttribute'=>'cpassword',
              'message'=> t("Password and confirm password does not match") ,'on'=>'complete_registration' ),    
              
          array('password, cpassword', 'length', 'min'=>4, 'max'=>40,
              'tooShort'=>t("{attribute} is too short (minimum is 4 characters).")  
           ),    
            
          array('email_address,social_id,first_name,last_name,status', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>'registration_social' ),  
		  
		  array('password','safe','on'=>'registration_social' ),
		  
		  array('email_address,first_name,last_name,status,password', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>'register' ),  

		  array('recaptcha_response','validateCapcha'),	  
		  
		  array('contact_phone','validateBlockPhone'),
		  
		  array('social_token','required','on'=>"social_login,registration_social",'message'=>t("Social token is empty")),
		  
		  array('social_token','validateSocialToken','on'=>'social_login,registration_social'),
		  
		  array('email_address','validateBlockEmail')
		  
		);
	}
	
	public function validateCapcha()
	{		
		if($this->capcha==1 || $this->capcha==TRUE){
			if(!empty($this->recaptcha_response)){
				try {						
															
					if(empty($this->captcha_secret)){
						$options = OptionsTools::find(array('captcha_secret'));
					    $captcha_secret = isset($options['captcha_secret'])?$options['captcha_secret']:'';													
					} else $captcha_secret = $this->captcha_secret;

					$resp = CRecaptcha::verify($captcha_secret,$this->recaptcha_response);					
				} catch (Exception $e) {
					$err = CRecaptcha::getError();
					if($err == "timeout-or-duplicate"){
						$this->addError('recaptcha_response',  t("Captcha expired please re-validate captcha") );
					} else $this->addError('recaptcha_response', $err );					
				}
			} else $this->addError('recaptcha_response', t("Please validate captcha") );
		}				
	}
	
	public function validateBlockPhone()
	{
		if($this->scenario=="registration_phone"){
			$options = OptionsTools::find(array('blocked_mobile'));
			$blocked_mobile = isset($options['blocked_mobile'])?$options['blocked_mobile']:'';
			$blocked_mobile = explode(",",$blocked_mobile);
			if(!empty($this->contact_phone)){
			if(in_array($this->contact_phone, (array) $blocked_mobile)){				
				$this->addError('contact_phone', t("Your phone number is not allowed to register.") );
			}
			}
		}
	}
	
	public function validateBlockEmail()
	{
		$options = OptionsTools::find(array('blocked_email_add'));
		$blocked = isset($options['blocked_email_add'])?$options['blocked_email_add']:'';
		$blocked = explode(",",$blocked);
		if(!empty($this->email_address)){
		if(in_array($this->email_address, (array) $blocked)){				
			$this->addError('email_address', t("Your email address is not allowed to register.") );
		}
		}
	}
	
	public function validateSocialToken()
	{			
		if($this->social_strategy=="facebook"){
			try {
				CSocialLogin::validateAccessToken( $this->social_token );
			} catch (Exception $e) {
				$this->addError('social_token', t($e->getMessage()) );
			}
		} else if ($this->social_strategy=="google") {
			try {
				if(!empty($this->google_client_id)){
					//
				} else {
					$options = OptionsTools::find(array('google_client_id'));
					$google_client_id = isset($options['google_client_id'])?$options['google_client_id']:''; 
					CSocialLogin::validateIDToken( $this->social_token , $google_client_id );
				}
			} catch (Exception $e) {
				$this->addError('social_token', t($e->getMessage()) );
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
				if($this->scenario=="complete_registration"){
					$this->password = md5($this->password);
				}
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
		
		// $this->registration_type = intval($this->registration_type);
		// if($this->registration_type==2 && $this->merchant_id>0 ){			
		// 	$meta = AR_client_meta::model()->find("client_id=:client_id AND meta1=:meta1",[
		// 		':client_id'=>intval($this->client_id),
		// 		':meta1'=>'merchant'
		// 	]);
		// 	if(!$meta){
		// 		$meta = new AR_client_meta;
		// 	}			
		// 	$meta->client_id = intval($this->client_id);
		// 	$meta->meta1 = 'merchant';
		// 	$meta->meta2 = intval($this->merchant_id);
		// 	$meta->save();
		// }

		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		
		$verification_type = 'email';
		if($this->scenario=="registration_phone"){
			$verification_type = 'sms';
		}
		
		$get_params = array( 
		   'client_uuid'=> $this->client_uuid,
		   'key'=>$cron_key,
		   'verification_type'=>$verification_type
		);		
								
		switch ($this->scenario) {
			case 'guest':
			case 'registration_phone':
			case 'resend_otp':	
			case 'register':
			case "registration_social":		
				CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/aftercustomersignup?".http_build_query($get_params) );
				break;	
				
			case 'complete_registration':		
			case 'complete_standard_registration':		
			case 'complete_social_registration':
			    CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afterregistration?".http_build_query($get_params) );
			    break;	
			    
			case 'reset_password':    
			    CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/after_requestresetpassword?".http_build_query($get_params) );
			    break;
		}
			
	}

	protected function afterDelete()
	{
		parent::afterDelete();				
	}
		
}
/*end class*/
