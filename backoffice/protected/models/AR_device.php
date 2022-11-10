<?php
class AR_device extends CActiveRecord
{		   				

	/*public $user_type;	
	public $user_id;*/	
	public $interest;
	
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
		return '{{device}}';
	}
	
	public function primaryKey()
	{
	    return 'device_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'platform'=>t("platform"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('user_type,user_id,platform', 
		  'required','message'=> t( '{{attribute}} is required' ) ),
		  		  		  
		  array('device_token,device_uiid,date_created,ip_address','safe'),
		  
		  //array('device_token','unique' ,'message'=>Helper_field_unique ),
		  
		  array('device_token','checkNull')
		  
		);
	}
	
	public function checkNull()
	{
		if($this->device_token=='null'){
			$this->addError('device_token',t("device id cannot be null"));
		}
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				$this->date_created = CommonUtility::dateNow();					
			} else  $this->date_modified = CommonUtility::dateNow();	
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();		
		
		if($this->scenario=="update_device_token"){
			return false;
		}
		
		AR_device_meta::model()->deleteAll('device_id=:device_id AND meta_name=:meta_name',array(
		   ':device_id'=>$this->device_id,
		   ':meta_name'=>'interest'
		));
				
		if(is_array($this->interest) && count($this->interest)>=1){
			foreach ($this->interest as $item) {				
				$model = new AR_device_meta;
				$model->device_id = $this->device_id;
				$model->meta_name = "interest";
				$model->meta_value = $item;
				$model->save();
			}
		}		
	}

	protected function afterDelete()
	{
		parent::afterDelete();				
		AR_device_meta::model()->deleteAll('device_id=:device_id',array(
		 ':device_id'=>$this->device_id
		));		
	}
		
}
/*end class*/