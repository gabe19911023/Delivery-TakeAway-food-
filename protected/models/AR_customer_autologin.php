<?php
class AR_customer_autologin extends CActiveRecord
{	
	   	
	public $username;
	public $password;
	public $rememberMe;
	public $email_address;
	private $_identity;
	
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
	
	public function authenticate($attribute,$params)
	{		
		$this->_identity=new CustomerIdentityAuto($this->username,$this->password,$this->merchant_id);
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
			$this->_identity=new CustomerIdentityAuto($this->username,$this->password,$this->merchant_id);
			$this->_identity->authenticate();
		}
			
		if($this->_identity->errorCode===CustomerIdentityAuto::ERROR_NONE)
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
		if($this->password == trim($password) ){
			return true;
		}
		return false;
	}
		
}
/*end class*/