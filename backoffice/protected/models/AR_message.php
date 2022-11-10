<?php
class AR_message extends CActiveRecord
{	

	public $csv;
	
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
		return '{{message}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'id'=>t("id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('id,language', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('language,translation', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  		  		  
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