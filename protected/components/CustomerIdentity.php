<?php
class CustomerIdentity extends CUserIdentity
{
	const LOGIN_TOKEN="logintoken";	
	private $_id;
	public $merchant_id;
	
	//Must need to add
	public function __construct($username, $password, $merchant_id)
	{
		$this->username = $username;
		$this->password = $password;
		$this->merchant_id = $merchant_id;
	}

	public function authenticate()
	{										
		if(!CommonUtility::checkEmail($this->username)){
			$user=AR_customer_login::model()->find('LOWER(contact_phone)=? AND merchant_id=?',array(strtolower($this->username), intval($this->merchant_id) ));				
		} else {
			$user=AR_customer_login::model()->find('LOWER(email_address)=? AND merchant_id=?',array(strtolower($this->username), intval($this->merchant_id) ));						
		}
		
		if($user===null)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if(!$user->validatePassword($this->password))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		elseif ($user->status!="active"){			
			$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		} else {			
			$this->_id=$user->client_id;
			$this->username=$user->username;			
			$this->setState('client_uuid', $user->client_uuid);
			$this->setState('first_name', $user->first_name);
			$this->setState('last_name', $user->last_name);
			$this->setState('email_address', $user->email_address);
			$this->setState('contact_number', $user->contact_phone);
						
			$this->setState('avatar', CMedia::getImage($user->avatar,$user->path,Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('customer'))
			);
			$this->errorCode=self::ERROR_NONE;
			
			if(empty($user->token)){
				$user->token = CommonUtility::generateToken("{{client}}",'token');	
			}			
			$user->last_login = CommonUtility::dateNow();
			$user->ip_address = CommonUtility::userIp();
			
			$this->setState(self::LOGIN_TOKEN, $user->token);
						
			$user->save();			
			
		}
		return $this->errorCode==self::ERROR_NONE;
	}

	public function guestAuthenticate($client_id)
	{										
		$user=AR_customer_login::model()->find('client_id=?',array($client_id));
			
		$this->_id=$user->client_id;
		$this->username=$user->username;			
		$this->setState('client_uuid', $user->client_uuid);
		$this->setState('first_name', $user->first_name);
		$this->setState('last_name', $user->last_name);
		$this->setState('email_address', $user->email_address);
		$this->setState('contact_number', $user->contact_phone);
					
		$this->setState('avatar', CMedia::getImage($user->avatar,$user->path,Yii::app()->params->size_image_thumbnail,
			CommonUtility::getPlaceholderPhoto('customer'))
		);
		$this->errorCode=self::ERROR_NONE;
		
		if(empty($user->token)){
			$user->token = CommonUtility::generateToken("{{client}}",'token');	
		}			
		$user->last_login = CommonUtility::dateNow();
		$user->ip_address = CommonUtility::userIp();
		
		$this->setState(self::LOGIN_TOKEN, $user->token);
					
		$user->save();
		return $this->errorCode==self::ERROR_NONE;
	}
	
	/**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->_id;
	}	
	
}
/*end class*/