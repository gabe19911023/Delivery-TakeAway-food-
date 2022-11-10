<?php
class CTax {
	
	public static function getSettings($merchant_id=0)
	{
		$merchant_meta = AR_merchant_meta::getMeta($merchant_id,array('tax_enabled','tax_on_delivery_fee','tax_type','tax_service_fee','tax_packaging'));
		$tax_enabled = isset($merchant_meta['tax_enabled'])?$merchant_meta['tax_enabled']['meta_value']:false;				
		$tax_enabled = $tax_enabled==1?true:false;
		$tax_on_delivery_fee = isset($merchant_meta['tax_on_delivery_fee'])?$merchant_meta['tax_on_delivery_fee']['meta_value']:false;
		$tax_type = isset($merchant_meta['tax_type'])?$merchant_meta['tax_type']['meta_value']:'';
		
		$tax_service_fee = isset($merchant_meta['tax_service_fee'])?$merchant_meta['tax_service_fee']['meta_value']:false;
		$tax_service_fee = $tax_service_fee==1?true:false;
		
		$tax_packaging = isset($merchant_meta['tax_packaging'])?$merchant_meta['tax_packaging']['meta_value']:false;
		$tax_packaging = $tax_packaging==1?true:false;
		
		if($tax_enabled){			
			$tax = self::getTax($merchant_id,$tax_type);
			$data = array(
			  'tax_type'=>$tax_type,
			  'tax_delivery_fee'=>$tax_on_delivery_fee==1?true:false,
			  'tax_service_fee'=>$tax_service_fee,
			  'tax_packaging'=>$tax_packaging,			  
			  'tax'=>$tax
			);
			return $data;
		}
		throw new Exception( 'tax not available' );
	}
	
	public static function getTax($merchant_id=0,$tax_type='')
	{		
		$dependency = CCacheData::dependency();
		
		switch ($tax_type) {
			case "standard":				
				$model = AR_tax::model()->cache( Yii::app()->params->cache , $dependency )->findAll("merchant_id=:merchant_id AND tax_type=:tax_type AND default_tax=:default_tax AND active=:active ",array(
				  ':merchant_id'=>intval($merchant_id),
				  ':tax_type'=>$tax_type,
				  ':default_tax'=>1,
				  ':active'=>1
				));
				if($model){
					$data = array();
					foreach ($model as $item) {
						$data[$item->tax_id]=array(
						  'tax_id'=>$item->tax_id,						  
						  'tax_name'=>Yii::app()->input->xssClean($item->tax_name),						  
						  'tax_in_price'=>$item->tax_in_price==1?true:false,
						  'tax_rate'=>$item->tax_rate,
						  'tax_rate_type'=>$item->tax_rate_type,
						);
					}
					return $data;
				}				
				break;
		
			case "multiple":												
				$criteria=new CDbCriteria;
				$criteria->order='tax_id ASC';
				$criteria->addCondition("merchant_id=:merchant_id AND tax_type=:tax_type AND active=:active");
				$criteria->params = array(
				  ':merchant_id'=>intval($merchant_id),
				  ':tax_type'=>$tax_type,				  
				  ':active'=>1
				);					  
			  
				$model = AR_tax::model()->cache( Yii::app()->params->cache , $dependency )->findAll($criteria);
				if($model){
					$data = array();
					foreach ($model as $item) {
						$data[$item->tax_id]=array(
						  'tax_id'=>$item->tax_id,						  
						  'tax_name'=>Yii::app()->input->xssClean($item->tax_name),						  
						  'tax_in_price'=>$item->tax_in_price==1?true:false,
						  'tax_rate'=>$item->tax_rate,
						  'tax_rate_type'=>$item->tax_rate_type,
						);
					}
					return $data;
				}				
			    break;
			    
			default:
				break;
		}
		throw new Exception( 'tax not available' );
	}
	
	public static function taxList($merchant_id=0,$tax_type='')
	{
		$model =  AR_tax::model()->findAll("merchant_id=:merchant_id AND tax_type=:tax_type ",array(
		  ':merchant_id'=>intval($merchant_id),
		  ':tax_type'=>$tax_type
		));
		if($model){
			foreach ($model as $item) {
				$prefix = $item->tax_rate_type == 'percent' ?'%' :'';
				$data[$item->tax_id] = t("{{tax_name}} ({{tax_rate}})",array(
				  '{{tax_name}}'=>$item->tax_name,
				  '{{tax_rate}}'=>Price_Formatter::formatNumberNoSymbol($item->tax_rate)."$prefix",
				));
			}
			return $data;
		}
		return false;
	}
	
	public static function taxForDelivery($merchant_id=0, $tax_type='')
	{
		$data = array();
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select="a.*";
		$criteria->condition = "merchant_id=:merchant_id AND tax_type=:tax_type 
		AND a.tax_id IN (
		  select meta_value from {{merchant_meta}}
		  where merchant_id=a.merchant_id and meta_name='tax_for_delivery'
		)
		";		    
		$criteria->params  = array(			  
		  ':merchant_id'=>intval($merchant_id),
		  ':tax_type'=>$tax_type
		);
		$model = AR_tax::model()->findAll($criteria); 
		if($model){
			foreach ($model as $item) {
				$data[$item->tax_id]=array(
				  'tax_id'=>$item->tax_id,						  
				  'tax_name'=>Yii::app()->input->xssClean($item->tax_name),						  
				  'tax_in_price'=>$item->tax_in_price==1?true:false,
				  'tax_rate'=>$item->tax_rate,
				  'tax_rate_type'=>$item->tax_rate_type,
				);
			}
			return $data;
		}
		return false;
	}
	
	public static function getItemTaxUse($merchant_id = 0, $item_id=0 )
	{
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select="a.*";
		$criteria->condition = "a.merchant_id=:merchant_id AND 
		tax_id IN (
		  select meta_id from {{item_meta}} 
		  where merchant_id=a.merchant_id
		  and item_id=".q(intval($item_id))."
		  and meta_name='tax'
		)
		";		    
		$criteria->params  = array(			  
		  ':merchant_id'=>intval($merchant_id),		  
		);		
		$model = AR_tax::model()->findAll($criteria);
		if($model){
			foreach ($model as $item) {
				$data[$item->tax_id]=array(
				  'tax_id'=>$item->tax_id,						  
				  'tax_name'=>Yii::app()->input->xssClean($item->tax_name),						  
				  'tax_in_price'=>$item->tax_in_price==1?true:false,
				  'tax_rate'=>$item->tax_rate,
				  'tax_rate_type'=>$item->tax_rate_type,
				);
			}
			return $data;
		}
		return false;
	}
		
}
/*end class*/