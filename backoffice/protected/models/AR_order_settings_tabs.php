<?php
class AR_order_settings_tabs extends CActiveRecord
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
		return '{{order_settings_tabs}}';
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
		   array('group_name,stats_id', 'required','message'=> t( Helper_field_required ) ),
		   array('date_modified,ip_address','safe'),
		   array('stats_id','unique','message'=>t(Helper_field_unique))
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				$this->uuid = CommonUtility::createUUID("{{order_settings_tabs}}",'uuid');
			}
			$this->date_modified = CommonUtility::dateNow();
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
