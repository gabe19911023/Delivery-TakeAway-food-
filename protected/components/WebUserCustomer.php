<?php
class WebUserCustomer extends CWebUser
{
	const LOGIN_TOKEN="logintoken";
	
	protected function beforeLogin($id,$states,$fromCookie)
	{			
		//The cookie isn't here, we refuse the login
		if(!isset($states[self::LOGIN_TOKEN])){
			return false;
		}
								
		$cookieLogintoken = $states[self::LOGIN_TOKEN];	
				
		$user = AR_customer_login::model()->findbyPk($id);	
		if ($user == null) {
			return false;
		}
						
		if($cookieLogintoken==$user->token) {		
			return true;
		}		
		
		return false;		
	}	
}