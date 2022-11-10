<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;
	public $verifyCode;
	public $captcha_enabled;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{		 		 		 
		 $rules = array(
		    array('username, password', 'required','message'=> t(Helper_field_required) ),
            array('password', 'authenticate'),
            array('username,password',
            'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),     
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

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 * @param string $attribute the name of the attribute to be validated.
	 * @param array $params additional parameters passed with rule when being executed.
	 */
	public function authenticate($attribute,$params)
	{		
		$this->_identity=new AdminUserIdentity($this->username,$this->password);
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
			$this->_identity=new AdminUserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===AdminUserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
