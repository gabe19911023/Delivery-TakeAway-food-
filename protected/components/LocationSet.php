<?php
class LocationSet
{
	public static function verifyLocalID()
	{		
		if ( !$local_id = CommonUtility::getCookie( Yii::app()->params->local_id ) ){
			return true;
		}
		return false;
	}
	
}
/*end class*/