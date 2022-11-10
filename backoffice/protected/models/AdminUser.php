<?php
class AdminUser extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
		
	public function tableName()
	{
		return '{{admin_user}}';
	}
	
	public function validatePassword($password)
	{		
		if($this->password == trim(md5($password)) ){
			return true;
		}
		return false;
	}
	
	public function primaryKey()
	{
	    return 'admin_id';	 
	}

}
/*end class*/