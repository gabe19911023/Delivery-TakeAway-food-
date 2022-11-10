<?php
class AR_newsletter extends CActiveRecord
{	
	   			
	public $tag_name_trans;
	public $description_trans;
	public $multi_language;
	
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
		return '{{newsletter}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'email_address'=>t("Email Address"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('email_address', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('email_address', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();					
			} else {
				//$this->date_modified = CommonUtility::dateNow();											
			}
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
