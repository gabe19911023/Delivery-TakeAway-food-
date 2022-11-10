<?php
class WebUser extends CWebUser
{
	const LOGIN_TOKEN="logintoken";
	
	protected function beforeLogin($id,$states,$fromCookie)
	{			
		//The cookie isn't here, we refuse the login
		if(!isset($states[self::LOGIN_TOKEN])){
			return false;
		}
		
		$cookieLogintoken = $states[self::LOGIN_TOKEN];		
					
		$role_id=0;
		$user = AdminUser::model()->findbyPk($id);		
		if($user->main_account!=1){
			$role_id = $user->role;
		}												
		Yii::app()->user->setState("role_id",intval($role_id));

		$resp = ItemIdentity::instantiateIdentity();
		if(!$resp){
			return false;
		}
					
		if($cookieLogintoken==$user->session_token) {		
			return true;
		}							
		return false;		
	}	
}