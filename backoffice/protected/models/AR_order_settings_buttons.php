<?php
class AR_order_settings_buttons extends CActiveRecord
{	

	public $status;
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
		return '{{order_settings_buttons}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'group_name'=>t("group_name"),
		    'stats_id'=>t("stats_id"),
		);
	}
	
	public function rules()
	{
		return array(
		   array('group_name', 
		   'required','message'=> t( Helper_field_required ) ),
		   
		   array('button_name', 
		   'required','message'=> t("Button name is required") ),
		   
		   array('stats_id', 
		   'numerical', 'min'=>1, 'message'=> t("Order status is required"),
		    'tooSmall'=>t("Order status is required")
		    ),
		   
		   array('date_modified,ip_address,do_actions','safe'),		   
		   
		   array('stats_id','validateStatus')
		);
	}

	public function validateStatus($attribute, $params)
	{		
		if($this->isNewRecord){
			$stmt="
			SELECT id FROM {{order_settings_buttons}}
			WHERE group_name=".q($this->group_name)."
			AND stats_id=".q(intval($this->stats_id))."
			AND order_type=".q($this->order_type)."
			";
			if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
				$this->addError('stats_id',t("Status already exist"));
			}
		} else {
			$stmt="
			SELECT id FROM {{order_settings_buttons}}
			WHERE group_name=".q($this->group_name)."
			AND stats_id=".q(intval($this->stats_id))."
			AND order_type=".q($this->order_type)."
			AND id!=".q($this->id)."
			";
			if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
				$this->addError('stats_id',t("Status already exist"));
			}
		}
	}
	
    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				$this->uuid = CommonUtility::createUUID("{{order_settings_buttons}}",'uuid');
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
		parent::afterSave();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/