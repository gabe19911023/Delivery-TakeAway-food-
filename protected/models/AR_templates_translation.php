<?php
class AR_templates_translation extends CActiveRecord
{	

	public $enabled_email, $enabled_sms, $enabled_push;
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
		return '{{templates_translation}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'template_type'=>"template_type",
		);
	}
	
	public function rules()
	{
		 return array(
            array('template_id,template_type,language,title,content', 
            'required','message'=> t(Helper_field_required) ),            
         );
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
			} else {				
			}
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
