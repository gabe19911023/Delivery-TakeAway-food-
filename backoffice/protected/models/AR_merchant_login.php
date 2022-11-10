<?php
class AR_merchant_login extends CActiveRecord
{	
	   	
	public $username;
	public $password;
	public $rememberMe;
	public $email_address;
	private $_identity;
	public $verifyCode;
	public $captcha_enabled;
	
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
		return '{{merchant_user}}';
	}
	
	public function primaryKey()
	{
	    return 'merchant_user_id';	 
	}
		
			
	/**
	 * Declares the validation rules.	 
	 */
	public function rules()
	{
		 /*return array(
            array('username,password', 
            'required','message'=> t(Helper_field_required) , 'on'=>'login' ),   

            array('username,password',
            'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),                           
            
            array('password', 'authenticate' , 'on'=>'login'),
            
            array('session_token,last_login,ip_address','safe'),
            
            array('email_address', 
            'required','message'=> t(Helper_field_required) , 'on'=>'forgot_password' ),   
         );*/
		 $rules = array(
		    array('username,password', 
            'required','message'=> t(Helper_field_required) , 'on'=>'login' ),   

            array('username,password',
            'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),                           
            
            array('password', 'authenticate' , 'on'=>'login'),
            
            array('session_token,last_login,ip_address','safe'),
            
            array('email_address', 
            'required','message'=> t(Helper_field_required) , 'on'=>'forgot_password' ),   
            
            array('email_address','email','on'=>'forgot_password'),
            array('email_address','findEmailAddress','on'=>'forgot_password'),
            
		 );
		 if($this->captcha_enabled){
		 	$rules[] = array('verifyCode', 'required','message'=> t("Capcha is required"));
		 	$rules[] = array('verifyCode', 'ext.yiiReCaptcha.ReCaptchaValidator');
		 }		 
		 return $rules;
	}
		
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{		
		return array(
		    'username'=>t("Username"),
		    'password'=>t("Password"),
			'rememberMe'=>t("Remember me"),
		);
	}
	
	public function findEmailAddress($attribute, $params)
	{		
		if(!empty($this->email_address)){
			$user = AR_merchant_user::model()->find('contact_email=:contact_email', array(':contact_email'=>$this->email_address));			
			if(!$user){
				$this->addError('email_address', t("Email address not found") );	
			}
		}
	}

	
	public function authenticate($attribute,$params)
	{		
		$this->_identity=new UserIdentityMerchant($this->username,$this->password);
		if(!$this->_identity->authenticate()){					
			if(!empty($this->_identity->errorMessage)){
				$this->addError('password',$this->_identity->errorMessage);
			} else $this->addError('password',t('Incorrect username or password.'));			
		}
	}
	
	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentityMerchant($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentityMerchant::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->merchant->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
	
	public function validatePassword($password)
	{		
		if($this->password == trim(md5($password)) ){
			return true;
		}
		return false;
	}
		
	protected function afterSave()
	{
		parent::afterSave();
		if($this->scenario=="send_forgot_password"){
			Yii::import('ext.runactions.components.ERunActions');	
			$cron_key = CommonUtility::getCronKey();		
			$get_params = array( 
			   'user_uuid'=> $this->user_uuid,
			   'key'=>$cron_key,
			);						
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/merchantpassword?".http_build_query($get_params) );					
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

}
/*end class*/
