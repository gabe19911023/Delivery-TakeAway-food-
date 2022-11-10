<?php
class AR_ordernew_additional_charge extends CActiveRecord
{	

	public $order_uuid;
	
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
		return '{{ordernew_additional_charge}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		
	}
	
	public function rules()
	{
		return array(
		  array('order_id,item_row,charge_name,additional_charge','required'),
		  array('order_id,additional_charge', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),	  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
			
		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$get_params = array( 
		   'order_uuid'=> $this->order_uuid,
		   'key'=>$cron_key,
		);			
		CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/updatesummary?".http_build_query($get_params) );
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/