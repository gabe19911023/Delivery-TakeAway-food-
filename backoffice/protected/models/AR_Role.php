<?php
class AR_Role extends CActiveRecord
{	
	   
	public $role_access;
	
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
		return '{{role}}';
	}
	
	public function primaryKey()
	{
	    return 'role_id';	 
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		    'role_name'=>t("Name"),
		    'role_access'=>t("Permission"),
		);
	}
	
	/**
	 * Declares the validation rules.	 
	 */
	public function rules()
	{
		 return array(
            array('role_name', 
            'required','message'=> CommonUtility::t(Helper_field_required) ),
            array('role_name','unique','message'=>t(Helper_field_unique)),
            array('role_access', 'validateAccess' ),         
            array('role_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),            
         );
	}
				
    public function validateAccess($attribute, $params)
	{
		$found  = false;
		if(is_array($this->role_access) && count($this->role_access)>=1){
			foreach ($this->role_access as $val) {
				if($val>0){
					$found  = true;
				}
			}
		}	
		if($found==false){
			$this->addError('role_access', t("Please select one of the access") );	
		}
	}
	
	public function getRoleAccess()
	{
		$data = array();
		$stmt = "SELECT role_access_id,action_name FROM {{role_access}} WHERE role_id=".q($this->role_id)." ";
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
				$data[$val['role_access_id']]=$val['action_name'];
			}			
		}		
		return $data;
	}
	
	protected function beforeSave()
	{		
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				$this->date_created = CommonUtility::dateNow();
				$this->ip_address = CommonUtility::userIp();					
			} else {
				$this->date_modified = CommonUtility::dateNow();
				$this->ip_address = CommonUtility::userIp();				
			}
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();		
		if(is_array($this->role_access) && count($this->role_access)>=1){
			Yii::app()->db->createCommand("DELETE FROM {{role_access}}
			WHERE role_id=".q($this->role_id)."
			")->query();
			foreach ($this->role_access as $role_access_key=>$role_access) {
				if($role_access>0){
					Yii::app()->db->createCommand()->insert("{{role_access}}",array( 
					  'role_id'=>$this->role_id,
					  'action_name'=>$role_access_key
					));
				}
			}
		}
	}
			
	protected function afterDelete()
	{
		parent::afterDelete();
				
		Yii::app()->db->createCommand("DELETE FROM {{role_access}}
			WHERE role_id=".q($this->role_id)."
			")->query();		
		
	}
		
}
/*end class*/
