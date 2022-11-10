<?php
class AR_ordernew extends CActiveRecord
{	

	public $items;
	public $meta;
	public $address_component;
	public $cart_uuid;
	public $total_items;
	
	public $remarks;
	public $ramarks_trans;
	public $change_by;
	public $customer_name;
	
	public $restaurant_name,$logo,$path;
	public $tax_use, $tax_for_delivery;
	public $total_sold , $first_name, $last_name,$month, $monthly_sales , $min_diff,
	$ratings
	;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return static the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{ordernew}}';
	}
	
	public function primaryKey()
	{
	    return 'order_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'order_id'=>t("Order ID"),
		  'order_uuid'=>t("Order UUID"),		  
		);
	}
	
	public function rules()
	{
		 return array(
            array('order_uuid,merchant_id,client_id,status,payment_status,service_code,payment_code,
            sub_total,total,whento_deliver,delivery_date', 
            'required','message'=> t(Helper_field_required) ),   
            
            array('order_uuid','unique','message'=>t(Helper_field_unique)),
            
            array('total_discount,points,service_fee,delivery_fee,packaging_fee,tax,courier_tip,
            promo_code,promo_total,delivery_time,delivery_time_end,cash_change,commission_type,
            commission_based,commission,merchant_earning,use_currency,base_currency,
            exchange_rate,is_critical,date_created,date_modified,ip_address','safe'),
            
            // array('formatted_address','required','on'=>'delivery' ,'message'=>t("Delivery address is required") ),
                        
         );
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();							
			} else {
				$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{										
		if ( is_array($this->items) && count($this->items)>=1 ){
			foreach ($this->items as $items) {				
				$line_item = new AR_ordernew_item;
				$line_item->order_id = intval($this->order_id);				
				$line_item->item_row = $items['cart_row'];
				$line_item->cat_id = intval($items['cat_id']);
				$line_item->item_id = intval($items['item_id']);
				$line_item->item_token = $items['item_token'];
				$line_item->item_size_id = intval($items['price']['item_size_id']);
				$line_item->qty = intval($items['qty']);
				$line_item->special_instructions = $items['special_instructions'];
				$line_item->if_sold_out = $items['if_sold_out'];
				$line_item->price = floatval($items['price']['price']);
				$line_item->discount = floatval($items['price']['discount']);
				$line_item->discount_type = $items['price']['discount_type'];
				$line_item->tax_use = isset($items['tax']) ? json_encode($items['tax']) : '';
				if($line_item->save()){
					
					/*ADDONS*/
					if(isset($items['addons']) && count($items['addons'])>=1){
						foreach ($items['addons'] as $addons) {
							$subcat_id = $addons['subcat_id'];
							foreach ($addons['addon_items'] as $addon_items) {								
								$addon = new AR_ordernew_addons;
		                        $addon->order_id = intval($this->order_id);
		                        $addon->item_row = $items['cart_row'];
		                        $addon->subcat_id = intval($subcat_id);
		                        $addon->sub_item_id = intval($addon_items['sub_item_id']);
		                        $addon->qty = floatval($addon_items['qty']);
		                        $addon->price = floatval($addon_items['price']);
		                        $addon->addons_total = floatval($addon_items['addons_total']);
		                        $addon->multi_option = $addon_items['multiple'];
		                        $addon->save();
							}
						}
					}
					/*END ADDONS*/
					
					/*ATTRIBUTES*/
					if(isset($items['attributes_raw']) && count($items['attributes_raw'])>=1){
						if(isset($items['attributes_raw']['cooking_ref']) && count($items['attributes_raw']['cooking_ref'])>=1){
							foreach ($items['attributes_raw']['cooking_ref'] as $cooking_id=>$cooking_ref) {
								$attributes = new AR_ordernew_attributes;
								$attributes->order_id = intval($this->order_id);;
								$attributes->item_row = $items['cart_row'];
								$attributes->meta_name = 'cooking_ref';
								$attributes->meta_value = $cooking_id;
								$attributes->save();
							}
						}
						
						if(isset($items['attributes_raw']['ingredients']) && count($items['attributes_raw']['ingredients'])>=1){
							foreach ($items['attributes_raw']['ingredients'] as $ingredients_id=>$ingredients) {
								$attributes = new AR_ordernew_attributes;
								$attributes->order_id = intval($this->order_id);;
								$attributes->item_row = $items['cart_row'];
								$attributes->meta_name = 'ingredients';
								$attributes->meta_value = $ingredients_id;
								$attributes->save();
							}
						}						
					}
					/*END ATTRIBUTES*/																				
				} else {
					//dump($line_item->getErrors());					
				}
			} /*end foreach*/
			
		} /*end item*/
		
		/*META*/
		if(is_array($this->meta) && count($this->meta)>=1){
			foreach ($this->meta as $meta_key=>$meta_value) {					
				$meta = new AR_ordernew_meta;
				$meta->order_id = intval($this->order_id);
				$meta->meta_name = $meta_key;
				$meta->meta_value = $meta_value;
				$meta->save();
			}
		}
		
		/*ADDRESS COMPONENTS*/
		if(is_array($this->address_component) && count($this->address_component)>=1){
			foreach ($this->address_component as $meta_key=>$meta_value) {					
				$meta = new AR_ordernew_meta;
				$meta->order_id = intval($this->order_id);
				$meta->meta_name = $meta_key;
				$meta->meta_value = $meta_value;
				$meta->save();
			}
		}
		
		if(is_array($this->tax_use) && count($this->tax_use)>=1){
			$meta = new AR_ordernew_meta;
			$meta->order_id = intval($this->order_id);
			$meta->meta_name = 'tax_use';
			$meta->meta_value = json_encode($this->tax_use);
			$meta->save();
		}
		
		if(is_array($this->tax_for_delivery) && count($this->tax_for_delivery)>=1){
			$meta = new AR_ordernew_meta;
			$meta->order_id = intval($this->order_id);
			$meta->meta_name = 'tax_for_delivery';
			$meta->meta_value = json_encode($this->tax_for_delivery);
			$meta->save();
		}
						
		
		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$get_params = array( 
		   'order_uuid'=> $this->order_uuid,
		   'key'=>$cron_key,
		);								
		
		if ( $this->scenario=="new_order"){
			
			$args = array();
			try {
				$customer = ACustomer::get($this->client_id);
				$args = array(
				  '{{customer_name}}'=> $customer->first_name." ".$customer->last_name
				);
			} catch (Exception $e) {
				//
			}			
			
			$history = new AR_ordernew_history;
			$history->order_id = $this->order_id;
			$history->status = $this->status;
			$history->remarks = "Order placed by {{customer_name}}";			
			$history->ramarks_trans = json_encode($args);
			$history->save();
			
			/*CLEAR CART*/
			CCart::clear($this->cart_uuid);						
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afterpurchase?".http_build_query($get_params) );
			
		} elseif ($this->scenario=="change_status"){
						
			$this->insertHistory();						
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afterupdatestatus?".http_build_query($get_params) );			
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/trackorder?".http_build_query($get_params) );	
			
		} elseif ($this->scenario=="cancel_order" || $this->scenario=="reject_order" ){
			
			$this->insertHistory();						
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afterordercancel?".http_build_query($get_params) );	
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/trackorder?".http_build_query($get_params) );			
			
		} elseif ($this->scenario=="delay_order"){
			
			$this->insertHistory();						
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afterdelayorder?".http_build_query($get_params) );
			
		} elseif ($this->scenario=="adjustment"){
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/afteradjustment?".http_build_query($get_params) );
			
		} elseif ($this->scenario=="customer_cancel_partial_refund"){
			$this->insertHistory();	
		} elseif ($this->scenario=="pos_entry"){
			$this->insertHistory();	
		}
		
		parent::afterSave();
	}

	protected function afterDelete()
	{
		parent::afterDelete();
		
		if($this->scenario == "reset_cart"){			
			AR_ordernew_item::model()->deleteAll('order_id=:order_id',array(
			   ':order_id'=>$this->order_id,			   
			));
			
			AR_ordernew_additional_charge::model()->deleteAll('order_id=:order_id',array(
			   ':order_id'=>$this->order_id,			   
			));
			
			AR_ordernew_addons::model()->deleteAll('order_id=:order_id',array(
			   ':order_id'=>$this->order_id,			   
			));
			
			AR_ordernew_attributes::model()->deleteAll('order_id=:order_id',array(
			   ':order_id'=>$this->order_id,			   
			));
			
			AR_ordernew_meta::model()->deleteAll('order_id=:order_id',array(
			   ':order_id'=>$this->order_id,			   
			));
			
		}
	}
	
	public function insertHistory()
	{
		$history = new AR_ordernew_history;
		$history->order_id = $this->order_id;
		$history->status = $this->status;
		$history->remarks = $this->remarks;
		$history->ramarks_trans = $this->ramarks_trans;
		$history->change_by = $this->change_by;
		$history->save();
	}
		
}
/*end class*/
