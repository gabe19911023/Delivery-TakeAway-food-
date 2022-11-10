<?php
class AR_shipping_rate extends CActiveRecord
{	

	public $merchant_service_fee;
	
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
		return '{{shipping_rate}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'shipping_type'=>t("Shipping Type"),
		  'distance_from'=>t("From"),
		  'distance_to'=>t("To"),
		  'shipping_units'=>t("Unit"),
		  'distance_price'=>t("Price"),
		  'minimum_order'=>t("Minimum Order"),
		  'maximum_order'=>t("Maximum Order"),
		  'estimation'=>t("Delivery estimation")
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,shipping_type,distance_from,distance_to,shipping_units,distance_price,estimation', 
		  'required','message'=> t( Helper_field_required ),'on'=>'dynamic' ),
		  
		  array('merchant_id,shipping_type,distance_from,distance_to,shipping_units,distance_price', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('minimum_order,maximum_order,','safe'),
		  
		  array('distance_to,distance_price,minimum_order,maximum_order', 'numerical', 'integerOnly' => false,		  
		  'min'=>1,
		  'tooSmall'=>t("Minimum value is 1"),
		  'message'=>t(Helper_field_numeric),
		   'on'=>'dynamic'
		  ),
		  
		  array('distance_from', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric),
		   'on'=>'dynamic'
		  ),
		  
		  array('shipping_type','check','on'=>'dynamic'),
		  
		  array('merchant_id,distance_price,estimation', 
		  'required','message'=> t( Helper_field_required ),'on'=>'fixed' ),

		  array('merchant_service_fee', 'numerical', 'integerOnly' => false,		  
		  // 'min'=>1,
		  // 'tooSmall'=>t("Minimum value is 1"),
		  'message'=>t(Helper_field_numeric),
		   'on'=>'fixed'
		  ),
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if(DEMO_MODE && in_array($this->merchant_id,DEMO_MERCHANT)){		
		    return false;
		}
				
		$this->distance_from = (float) $this->distance_from;
		$this->distance_to = (float) $this->distance_to;
		$this->distance_price = (float) $this->distance_price;
		$this->minimum_order = (float) $this->minimum_order;
		$this->maximum_order = (float) $this->maximum_order;
		
		$this->last_update = CommonUtility::dateNow();
		
		return true;
	}
	
	public function check()
	{
		if($this->isNewRecord){
			$stmt = "
			SELECT * FROM {{shipping_rate}}
			WHERE shipping_type = ".q($this->shipping_type)."
			AND distance_from<=".q($this->distance_from)." AND distance_to>=".q($this->distance_to)."
			AND shipping_units = ".q($this->shipping_units)."
			";
		} else{
			$stmt = "
			SELECT * FROM {{shipping_rate}}
			WHERE shipping_type = ".q($this->shipping_type)."
			AND distance_from<=".q($this->distance_from)." AND distance_to>=".q($this->distance_to)."
			AND shipping_units = ".q($this->shipping_units)."
			AND id <> ".q($this->id)."
			";
		}		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			//dump($res);
			$this->addError('shipping_type', t("This range already exist") );	
			return false;
		} 
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
	}
	
    protected function beforeDelete()
	{				
	    if(DEMO_MODE){				
		    return false;
		}
	    return true;
	}
	
	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
