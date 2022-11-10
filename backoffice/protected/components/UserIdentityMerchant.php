<?php
class UserIdentityMerchant extends CUserIdentity
{
	const LOGIN_TOKEN="logintoken";
	const LOGIN_TYPE="merchant";
	private $_id;
	
	public function authenticate()
	{				
		$user=AR_merchant_login::model()->find('LOWER(username)=?',array(strtolower($this->username)));					
		if($user===null)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if(!$user->validatePassword($this->password))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		elseif ($user->status!="active"){			
			$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		} else {			
			$merchant = CMerchants::get($user->merchant_id);					
			if( $merchant->status=='active' || $merchant->status=='expired') {				
				$this->_id=$user->merchant_user_id;
				$this->username=$user->username;
				$this->setState('merchant_id', $user->merchant_id);
				$this->setState('merchant_uuid', $merchant->merchant_uuid );
				$this->setState('status', $merchant->status );
				$this->setState('merchant_type', $merchant->merchant_type );
				$this->setState('restaurant_slug', $merchant->restaurant_slug);
				
				$this->setState('first_name', $user->first_name);
				$this->setState('last_name', $user->last_name);
				$this->setState('email_address', $user->contact_email);
				$this->setState('contact_number', $user->contact_number);
				$this->setState('avatar', $user->profile_photo);
				$this->setState('login_type', self::LOGIN_TYPE );
				$this->setState('main_account', $user->main_account);				
				$this->errorCode=self::ERROR_NONE;
							
				$user->session_token = CommonUtility::createUUID("{{merchant_user}}",'session_token');						
				$user->last_login = CommonUtility::dateNow();
				$user->ip_address = CommonUtility::userIp();
				
				$this->setState(self::LOGIN_TOKEN, $user->session_token);							
				$user->save();			
				
			} else if ($merchant->status=='blocked') {
				$this->errorMessage = t('Your account is blocked');
			} else if ($merchant->status=='suspended') {
				$this->errorMessage = t('Your account is suspended');
			} else if ($merchant->status=='pending') {
				$this->errorMessage = t('Your account is not activated');
			} else {
				$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
			}
			
		}
		return $this->errorCode==self::ERROR_NONE;
	}
	
	/**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->_id;
	}
	
	public static function verifyAccess()
	{				
		/*ALLOW ACCESS IF MAIN ACCOUNT OF ADMIN*/
		$main_account = Yii::app()->merchant->getState("main_account");	
		
		if(Yii::app()->merchant->merchant_type==1 && Yii::app()->merchant->status=="expired"){
			//return false;
		}
						
		if($main_account==1){			
			return true;
		}
		
		$controller = Yii::app()->controller->id;
        $action = Yii::app()->controller->action->id;
        $actions = "$controller.$action";				
                
        $criteria=new CDbCriteria();
        $criteria->alias="a";              
        $criteria->condition = "a.action_name=:action_name AND b.merchant_user_id=:merchant_user_id";
        $criteria->join='LEFT JOIN {{merchant_user}} b on a.role_id = b.role ';
        $criteria->params = array(
          ':action_name'=>$actions,
          ':merchant_user_id'=>intval(Yii::app()->merchant->id)
        );               
        
        $dependency = CCacheData::dependency();
        if($model = AR_role_access::model()->cache(Yii::app()->params->cache, $dependency)->find($criteria)){        	
        	return true;
        } 
        return false;
	}
	
	public static function trialCheck()
	{
		return false;
	}
	
	public static function verifyLogin()
	{
		if(Yii::app()->merchant->isGuest){
			return false;
		}
		return true;
	}
	
}
/*end class*/