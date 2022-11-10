<?php
class AR_item_attributes extends CActiveRecord
{	

	 public $cooking_selected, $ingredients_selected, $dish_selected, $delivery_options_selected,
	 $gallery_photo_selected,
	 $available_day, $available_time_start, $available_time_end
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
		return '{{item}}';
	}
	
	public function primaryKey()
	{
	    return 'item_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'cooking_selected'=>t("Please select"),
		  'available_time_start[1]'=>t("From"),
		  'available_time_start[2]'=>t("From"),
		  'available_time_start[3]'=>t("From"),
		  'available_time_start[4]'=>t("From"),
		  'available_time_start[5]'=>t("From"),
		  'available_time_start[6]'=>t("From"),
		  'available_time_start[7]'=>t("From"),
		  'available_time_end[1]'=>t("To"),
		  'available_time_end[2]'=>t("To"),
		  'available_time_end[3]'=>t("To"),
		  'available_time_end[4]'=>t("To"),
		  'available_time_end[5]'=>t("To"),
		  'available_time_end[6]'=>t("To"),
		  'available_time_end[7]'=>t("To"),
		  'points_earned'=>t("Points earned"),
		  'packaging_fee'=>t("Packaging fee"),
		);
	}
	
	public function rules()
	{
		return array(
		  
		  array('cooking_selected', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('cooking_selected,ingredients_selected,dish_selected,cooking_ref,
		  ingredients,dish,delivery_options_selected,delivery_options,non_taxable,points_earned,
		  points_disabled,packaging_fee,packaging_incremental,gallery_photo,cooking_ref_required,
		  available_at_specific,not_for_sale,available
		  ','safe'),
		  
		  array('points_earned,packaging_fee', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  		  		 
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();					
		} else {
			$this->date_modified = CommonUtility::dateNow();											
		}
		$this->ip_address = CommonUtility::userIp();	
		
		if($this->scenario == "item_attributes"){			    			
			$this->points_earned = (integer) $this->points_earned;
			$this->packaging_fee = (float) $this->packaging_fee;
	    }
		
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		$merchant_id = intval($this->merchant_id);
				
		if($this->scenario == "item_attributes"){		
			
		    /*DELETE META*/
			Yii::app()->db->createCommand("DELETE FROM {{item_meta}}
			WHERE item_id=".q($this->item_id)."
			AND merchant_id = ".q($merchant_id)."
			AND meta_name IN ('cooking_ref','ingredients','dish','delivery_options')
			 ")->query();
			
			$params = array();		
			if(!empty($this->cooking_selected)){
				foreach ($this->cooking_selected as $id) {
					$params[]=array(
					  'merchant_id'=>(integer)$merchant_id,
					  'item_id'=>(integer)$this->item_id,
					  'meta_name'=>'cooking_ref',
					  'meta_id'=>$id
					);
				}			
			}
			
			/*ITEM INGREDIENTS*/				
			if(!empty($this->ingredients_selected)){
				foreach ($this->ingredients_selected as $id) {
					$params[]=array(
					  'merchant_id'=>(integer)$merchant_id,
					  'item_id'=>(integer)$this->item_id,
					  'meta_name'=>'ingredients',
					  'meta_id'=>$id
					);
				}			
			}
			
			/*ITEM DISH*/				
			if(!empty($this->dish_selected)){
				foreach ($this->dish_selected as $id) {
					$params[]=array(
					  'merchant_id'=>(integer)$merchant_id,
					  'item_id'=>(integer)$this->item_id,
					  'meta_name'=>'dish',
					  'meta_id'=>$id
					);
				}			
			}
			
			/*ITEM DELIVERY OPTIONS*/				
			if(!empty($this->delivery_options_selected)){
				foreach ($this->delivery_options_selected as $id) {
					$params[]=array(
					  'merchant_id'=>(integer)$merchant_id,
					  'item_id'=>(integer)$this->item_id,
					  'meta_name'=>'delivery_options',
					  'meta_id'=>$id
					);				
				}			
			}
			
			if(is_array($params) && count($params)>=1){
				$builder=Yii::app()->db->schema->commandBuilder;
			    $command=$builder->createMultipleInsertCommand('{{item_meta}}',$params);
			    $command->execute();			
			}
			
		} elseif ( $this->scenario=="availability"){
			$days = AttributesTools::dayWeekList();					
			foreach ($days as $key=>$item) {				
				$day_of_week = isset($this->available_day[$key])?$key:0;		
				$status = isset($this->available_day[$key])? $this->available_day[$key] :0;						
				$start = isset($this->available_time_start[$key])? $this->available_time_start[$key] :null;				
				$end = isset($this->available_time_end[$key])? $this->available_time_end[$key] :null;						
				if($day_of_week>0){
				   AR_availability::saveMeta($this->merchant_id,'item',$this->item_id,$day_of_week,$status,$start,$end);
				}
			}						
			return true;
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
