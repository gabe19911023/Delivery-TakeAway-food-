<?php class CCommission
{
	
	public static function getCommissionValue($merchant_type='',$commision_type='',$merchant_commission=0,$sub_total=0,$total=0)
	{
		$data = array(); $commission = 0; $merchant_earning = 0;
		$merchant_commission_raw = $merchant_commission;
						
		if($merchant_type==2 || $merchant_type==3){
			$model = AR_merchant_type::model()->find('type_id=:type_id', 
			array(':type_id'=>$merchant_type)); 		
			if($model){
				
				if($model->based_on=="subtotal"){
				   $total_based = $sub_total;	
				} else $total_based = $total;		
				
				//dump("total_based=>$total_based");
				
				if($commision_type=="fixed"){
					$commission = $merchant_commission;
					$merchant_earning = floatval($total_based) - floatval($commission);
				} else {
					$merchant_commission = floatval($merchant_commission)/100;
					$commission = floatval($total_based) * $merchant_commission;
					$merchant_earning = floatval($total_based) - floatval($commission);
				}
						
				return array(
				  'commission_value'=>$merchant_commission_raw,
				  'commission_based'=>$model->based_on,
				  'commission'=>floatval($commission),
				  'merchant_earning'=>floatval($merchant_earning)
				);
			}
		}
		return false;
	}
	
}
/*end class*/