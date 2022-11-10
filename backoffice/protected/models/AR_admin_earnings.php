<?php
class AR_admin_earnings extends CActiveRecord
{	
	
	public $meta_name;
	public $meta_value;
	
	public $meta_array;
	
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
		return '{{admin_earnings}}';
	}
	
	public function primaryKey()
	{
	    return 'transaction_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'transaction_description'=>t("Description"),
		    'transaction_type'=>t("Transaction Type"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('transaction_date, transaction_description,transaction_type,transaction_amount,running_balance,ip_address',
		   'required','message'=> CommonUtility::t( Helper_field_required ) ),
		   
		  array('transaction_description', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),   
		  
		  array('transaction_uuid','safe'),
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->transaction_uuid = CommonUtility::createUUID("{{admin_earnings}}",'transaction_uuid');
				$this->transaction_date = CommonUtility::dateNow();					
			} 
			$this->ip_address = CommonUtility::userIp();				
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();		
		
		if(!empty($this->meta_name) && !empty($this->meta_value) ){
			$model = new AR_admin_earnings_meta;
			$model->transaction_id = $this->transaction_id;
			$model->meta_name = $this->meta_name;
			$model->meta_value = $this->meta_value;
			$model->save();
		}
		
		if(is_array($this->meta_array) && count($this->meta_array)>=1){
			$model = new AR_admin_earnings_meta;
			foreach ($this->meta_array as $item) {
				$model->meta_name = isset($item['meta_name'])?$item['meta_name']:'';
				$model->meta_value = isset($item['meta_value'])?$item['meta_value']:'';
				$model->save();
			}
		}
		
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/