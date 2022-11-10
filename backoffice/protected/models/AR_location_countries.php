<?php
class AR_location_countries extends CActiveRecord
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
		return '{{location_countries}}';
	}
	
	public function primaryKey()
	{
	    return 'country_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'country_id'=>t("country_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('country_id,shortcode,country_name,phonecode,', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('shortcode,country_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  		  		  
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
