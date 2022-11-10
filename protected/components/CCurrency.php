<?php
class CCurrency
{
	public static function defaultCurrency()
	{
		$model = AR_currency::model()->find('as_default=:as_default', 
		array(':as_default'=>1)); 		
		if($model){
			return array(
			  'currency_code'=>$model->currency_code,
			  'exchange_rate'=>$model->exchange_rate,
			  'exchange_rate_fee'=>$model->exchange_rate_fee
			);
		}
		return false;	
	}
	
}
/*end class*/