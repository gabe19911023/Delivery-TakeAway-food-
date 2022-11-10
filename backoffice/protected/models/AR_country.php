<?php
class AR_country extends CActiveRecord
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
		    'shortcode'=>t("Code"),
		    'country_name'=>t("Name"),
		    'phonecode'=>t("Phone Code"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('shortcode,country_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('shortcode,country_name,phonecode', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  
		  array('shortcode', 'length', 'min'=>2, 'max'=>2,
              'tooShort'=>t(Helper_password_toshort) ,
              ),
		  		  
          array('phonecode', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),

		    
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		return true;
	}
	
	protected function afterSave()
	{
		if(!parent::afterSave()){
			return false;
		}
	}

	protected function afterDelete()
	{
		if(!parent::afterDelete()){
			return false;
		}
	}
		
}
/*end class*/
