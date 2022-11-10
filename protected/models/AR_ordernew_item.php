<?php
class AR_ordernew_item extends CActiveRecord
{	

	public $order_uuid;
	public $refund_item_details;
	public $total_sold, $item_name,$category_name, $photo, $path,
	$date_sold, $color_hex , $restaurant_name
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
		return '{{ordernew_item}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		 'order_id'=>"order_id",
		);
	}
	
	public function rules()
	{
		 return array(
            array('order_id,item_row,cat_id,item_token,item_size_id,qty,price', 
            'required','message'=> t(Helper_field_required) ),   
                        
            array('special_instructions,discount,discount_type','safe'),
                        
         );
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
			} else {				
			}			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
										
		switch ($this->scenario) {
			case "update_cart":				    			    
				break;				
				
			case "update_item_qty":							  			  
			  /*$criteria = new CDbCriteria;
			  $criteria->condition="order_id=:order_id";
			  $criteria->params = array(':order_id'=>$this->order_id);
			  $criteria->addInCondition('multi_option', array('custom','one') );
			  AR_ordernew_addons::model()->updateAll(array(
			   'qty'=>$this->qty
			  ),$criteria);*/	
			  $stmt="
			  UPDATE {{ordernew_addons}}
			  SET qty=".q(intval($this->qty)).",
			  addons_total = (price*".q(intval($this->qty)).")
			  WHERE order_id=".q($this->order_id)."
			  AND multi_option IN ('custom','one')
			  ";					  
			  Yii::app()->db->createCommand($stmt)->query();  
			break;	
		}
		
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
				
		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$get_params = array( 
		   'order_uuid'=> $this->order_uuid,
		   'key'=>$cron_key,
		);						
						
		switch ($this->scenario) {
			case "out_stock":			
			case "refund":
			case "remove":
				AR_ordernew_addons::model()->deleteAll("order_id=:order_id AND item_row=:item_row",array(
				  ':order_id'=>$this->order_id,
				  ':item_row'=>$this->item_row,
				));				
				
				AR_ordernew_additional_charge::model()->deleteAll("order_id=:order_id AND item_row=:item_row",array(
				  ':order_id'=>$this->order_id,
				  ':item_row'=>$this->item_row,
				));				
				
				AR_ordernew_attributes::model()->deleteAll("order_id=:order_id AND item_row=:item_row",array(
				  ':order_id'=>$this->order_id,
				  ':item_row'=>$this->item_row,
				));				
				
				CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/updatesummary?".http_build_query($get_params) );
				break;				
		}		
		
		CCacheData::add();
	}
		
}
/*end class*/
