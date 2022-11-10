<?php
class AR_role_access extends CActiveRecord
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
		return '{{role_access}}';
	}
	
	public function primaryKey()
	{
	    return 'role_access_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'role_access_id'=>t("role_access_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('role_id,action_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('action_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  		  
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
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
