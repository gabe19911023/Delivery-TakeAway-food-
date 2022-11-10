<?php
class AR_customer_login extends CActiveRecord
{	
	   	
	public $username;
	public $password;
	public $rememberMe;
	public $email_address;
	private $_identity;
	public $capcha;
	public $recaptcha_response;	
	public $captcha_secret;
	
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
		
			
	/**
	 * Declares the validation rules.	 
	 */
	public function rules()
	{
		 return array(
            array('username,password', 
            'required','message'=> t(Helper_field_required) , 'on'=>'login' ),   

            array('username,password',
            'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),                           
            
            array('password', 'authenticate' , 'on'=>'login'),
            
            array('token,date_created,date_modified,last_login,ip_address,','safe'),

            array('recaptcha_response','validateCapcha'),	             
            
         );
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
	
	public function authenticate($attribute,$params)
	{		
		$this->_identity=new CustomerIdentity($this->username,$this->password,$this->merchant_id);
		if(!$this->_identity->authenticate())
			$this->addError('password','Incorrect username or password.');
	}
	
	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{			
			$this->_identity=new CustomerIdentity($this->username,$this->password,$this->merchant_id);
			$this->_identity->authenticate();
		}
			
		if($this->_identity->errorCode===CustomerIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		} else {			
			$fields_use = "Email address";
			if(!CommonUtility::checkEmail($this->username)){
				$fields_use = "Mobile phone number";
			} 			
			$message = t("Unable to login. Check your login information and try again.",array(
			 '[fields]'=>t($fields_use)
			));			
			$this->addError('password',$message);
			return false;
		}
	}
	
	public function validatePassword($password)
	{		
		if($this->password == trim(md5($password)) ){
			return true;
		}
		return false;
	}
		
}
/*end class*/