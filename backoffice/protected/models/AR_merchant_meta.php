<?php
class AR_merchant_meta extends CActiveRecord
{	
	   
	public $role_access;
	public $running_balance;	
	public $tax_enabled, $tax_on_delivery_fee,$tax_type,
	$tax_service_fee, $tax_packaging, $tax_for_delivery,
	$zone,$website_url, $api_url,$payment_api_url
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
		return '{{merchant_meta}}';
	}
	
	public function primaryKey()
	{
	    return 'meta_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'tax_name'=>t("Tax name"),
		    'tax_default'=>t("Default tax"),	
			'website_url'=>'',
			'api_url'=>t("API URL"),
			'payment_api_url'=>t("Payment API URL")
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,meta_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('meta_value,meta_value1,meta_value2,meta_value3', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('meta_value1,meta_value2,meta_value3','safe'),	

		  array('website_url','url', 'defaultScheme'=>'http'),

		  array('website_url', 
		  'required','message'=> t( Helper_field_required ), 'on'=>'create_api_access' ),
		 
		);
	}
		
	protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 			
		$this->date_modified = CommonUtility::dateNow();		
		return true;
	}

	protected function afterSave()
	{
		parent::afterSave();
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
		
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		
		switch ($this->scenario) {
			case "cancel_subscription":
				$merchant = CMerchants::get($this->merchant_id);
				$merchant->package_id = 0;
				$merchant->status = 'expired';
				$merchant->save();
				
				$criteria=new CDbCriteria();
				$criteria->addInCondition('meta_name', array(
				  'stripe','payment_method_stripe','subscription_payment_method'
				));
				AR_merchant_meta::model()->deleteAll($criteria);
				break;					
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
		
	}	
				
	public static function saveMeta($merchant_id=0,$meta_name='', $meta_value='', $meta_value1='',$scenario='')
	{		
		$model=AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name",array(
		  ':merchant_id'=>intval($merchant_id),
		  ':meta_name'=>$meta_name
		));
		if($model){			
			$model->scenario = $scenario;
			$model->meta_value=$meta_value;
			if(!empty($meta_value1)){
				$model->meta_value1=$meta_value1;
			}			
			$model->save();
		} else {			
			$model = new AR_merchant_meta;
			$model->scenario = $scenario;
			$model->merchant_id = intval($merchant_id);
			$model->meta_name = $meta_name;
			$model->meta_value = $meta_value;
			if(!empty($meta_value1)){
				$model->meta_value1=$meta_value1;
			}
			$model->save();
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
		
		return true;
	}
	
	public static function getValue($merchant_id=0, $meta_name='')
    {
    	$dependency = CCacheData::dependency();
     	$model = AR_merchant_meta::model()->cache( Yii::app()->params->cache,$dependency)->find("merchant_id=:merchant_id AND meta_name=:meta_name",array(
     	 ':merchant_id'=>intval($merchant_id),
		 ':meta_name'=>trim($meta_name),		 
		));			
		if($model){
			return array(
			  'meta_value'=>$model->meta_value,
			  'meta_value1'=>$model->meta_value1,
			  'meta_value2'=>$model->meta_value2,
			  'meta_value3'=>$model->meta_value3,
			);
		}
		return false;
     }
     
     public static function getMeta($merchant_id=0, $meta_name=array())
     {
     	  $data = array();
     	  $criteria=new CDbCriteria();
     	  $criteria->condition="merchant_id=:merchant_id";
     	  $criteria->params = array(':merchant_id'=>intval($merchant_id));
     	  $criteria->addInCondition('meta_name', (array) $meta_name );     	  
     	  
     	  $dependency = CCacheData::dependency();
     	  $model = AR_merchant_meta::model()->cache( Yii::app()->params->cache , $dependency  )->findAll($criteria);  
     	  if($model){
     	  	  foreach ($model as $item) {     	  	  	 
     	  	  	 $data[$item->meta_name] = array(
     	  	  	   'meta_value'=>$item->meta_value,
     	  	  	   'meta_value1'=>$item->meta_value1,
     	  	  	   'meta_value2'=>$item->meta_value2,
			       'meta_value3'=>$item->meta_value3,
     	  	  	 );
     	  	  }
     	  }
     	  return $data;
     }
     	
}
/*end class*/
