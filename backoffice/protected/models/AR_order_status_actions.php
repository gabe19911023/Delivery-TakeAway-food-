<?php
class AR_order_status_actions extends CActiveRecord
{	

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
		return '{{order_status_actions}}';
	}
	
	public function primaryKey()
	{
	    return 'action_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'stats_id'=>t("Status"),
		  'action_type'=>t("Action"),
		  'action_value'=>t("Value"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('stats_id,action_type,action_value', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('date_created,date_modified,ip_address','safe'),
		  
		  array('stats_id','ext.UniqueAttributesValidator','with'=>'action_type,action_value',
		   'message'=>t("Actions already exist")
		  ),
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			
			if(DEMO_MODE){				
			    return false;
			}

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
