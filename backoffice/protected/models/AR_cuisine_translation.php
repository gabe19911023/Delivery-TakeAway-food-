<?php
class AR_cuisine_translation extends CActiveRecord
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
		return '{{cuisine_translation}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'cuisine_id'=>t("cuisine_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('cuisine_id,cuisine_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('cuisine_name,language', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  		 		 
		  
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
