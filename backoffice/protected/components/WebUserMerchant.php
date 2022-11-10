<?php
class WebUserMerchant extends CWebUser
{
	const LOGIN_TOKEN="logintoken";
	
	protected function beforeLogin($id,$states,$fromCookie)
	{			
		//The cookie isn't here, we refuse the login
		if(!isset($states[self::LOGIN_TOKEN])){
			return false;
		}
					
		$role_id=0;
		$user = AR_merchant_login::model()->findbyPk($id);				
		if($user->main_account!=1){
			$role_id = $user->role;
		}			
		$cookieLogintoken = $states[self::LOGIN_TOKEN];	
		
		Yii::app()->merchant->setState("role_id",intval($role_id));
		
		/*AdminMenu::buildMenu(0,false,$role_id,'merchant');				
		Yii::app()->merchant->setState("menu",AdminMenu::$items);*/
		
		if($cookieLogintoken==$user->session_token) {		
			return true;
		}		
		
		return false;		
	}	
}