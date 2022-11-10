<?php
class AR_status_management_translation extends CActiveRecord
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
		return '{{status_management_translation}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'title'=>t("title"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('status_id,language,title', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('title', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  		  
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
