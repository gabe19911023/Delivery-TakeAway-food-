<?php 
class Price_Formatter
{
	public static $currency_code='';
	
	public static $number_format=array(
	  'decimals'=>2, 
	  'decimal_separator'=>'.', 
	  'thousand_separator'=>'',
	  'position'=>"left",
	  'spacer'=>"",
	  'currency_symbol'=>"",
	  'show_symbol'=>true,
	  'currency_code'=>""
	);
	
	public static function init($currency='')
	{
		Price_Formatter::$currency_code = $currency;
		$res  = array();
		
		$stmt="
		SELECT currency_code,currency_symbol,currency_position,
		IF(number_decimal IS NULL or number_decimal = '',  0 , number_decimal )  as number_decimal,
		IF(decimal_separator IS NULL or decimal_separator = '',  '.' , decimal_separator )  as decimal_separator
		,thousand_separator
		
		FROM {{currency}} a
		WHERE currency_code = ".q($currency)."
		LIMIT 0,1
		";		
	    if(!$res = Yii::app()->db->createCommand($stmt)->queryRow()){
	    	$stmt="
			SELECT id,currency_code,currency_symbol,currency_position,
			IF(number_decimal IS NULL or number_decimal = '',  0 , number_decimal )  as number_decimal,
			IF(decimal_separator IS NULL or decimal_separator = '',  '.' , decimal_separator )  as decimal_separator
			,thousand_separator
			
			FROM {{currency}} a
			WHERE as_default = 1
			LIMIT 0,1
			";	    	
			$res = Yii::app()->db->createCommand($stmt)->queryRow();
	    }
	    
	    if($res){			
		    $spacer = ""; $currency_position = $res['currency_position'];
			switch ($res['currency_position']) {
				case "left_space":				
				    $spacer = " ";
				    $currency_position = "left";
				break;
				
				case "right_space":	
				    $spacer = " ";
				    $currency_position = "right";
					break;
			
				default:
					//
					break;
			}
												
			Price_Formatter::$number_format = array(
			   'decimals'=>$res['number_decimal'], 
			   'decimal_separator'=>$res['decimal_separator'], 
			   'thousand_separator'=>$res['thousand_separator'], 
			   'position'=>$currency_position,
			   'spacer'=>$spacer,
			   'currency_symbol'=>$res['currency_symbol'],
			   'currency_code'=>$res['currency_code']
			);			
		}
	}
	
	public static function getSymbol($currency_code='')
	{
		$stmt="
		SELECT currency_symbol 
		FROM {{currency}}
		WHERE 
		currency_code = ".q($currency_code)."
		LIMIT 0,1
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			return $res['currency_symbol'];
		}
		return '$';
	}
	
	public static function formatNumber($value=0)
	{				
				
		$formatted_number = number_format( (float) $value ,
		   !empty(Price_Formatter::$number_format['decimals'])?Price_Formatter::$number_format['decimals']:0,
		   Price_Formatter::$number_format['decimal_separator'],
		   Price_Formatter::$number_format['thousand_separator']
		);
		
		if(Price_Formatter::$number_format['position']=="left" || self::$number_format['position']=="left_space"){
			return Price_Formatter::$number_format['currency_symbol'].Price_Formatter::$number_format['spacer'].$formatted_number;
		} else {
			return $formatted_number.Price_Formatter::$number_format['spacer'].Price_Formatter::$number_format['currency_symbol'];
		}
	}
	
	public static function formatNumberNoSymbol($value=0)
	{								
		$formatted_number = number_format( (float) $value ,
		   !empty(Price_Formatter::$number_format['decimals'])?Price_Formatter::$number_format['decimals']:0,
		   Price_Formatter::$number_format['decimal_separator'],
		   Price_Formatter::$number_format['thousand_separator']
		);
		
		return $formatted_number;
	}
	
	public static function convertToRaw($price, $decimal=2,$return_empty=false,$thousand_separator='')
	{
		/*if (is_numeric($price)){		
			if($price>0 && $return_empty==false){	
		       return number_format($price,$decimal,'.','');
			} elseif ( $price>0){
				return number_format($price,$decimal,'.','');
			}
	    }
	    return $return_empty==false?0:''; */
		return number_format(  floatval($price) , $decimal , '.',$thousand_separator );
	}
	
	public static function getSpacer($currency_position='')
	{
		$spacer = "";
		switch ($currency_position) {
			case "left_space":				
			    $spacer = " ";			    
			break;
			
			case "right_space":	
			    $spacer = " ";			    
				break;
		
			default:
				//
				break;
		}
		return $spacer;	
	}
	
}
/*end class*/