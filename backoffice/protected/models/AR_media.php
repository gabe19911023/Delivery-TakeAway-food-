<?php
class AR_media extends CActiveRecord
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
		return '{{media_files}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'title'=>t("Title"),
		    'filename'=>t("filename"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('title,filename', 
		  'required','message'=> t( Helper_field_required ) ),		  
		  
		  array('upload_uuid','safe')
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();			
		} 
		$this->ip_address = CommonUtility::userIp();			
		
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();					
	}

	protected function afterDelete()
	{
		parent::afterDelete();				
		if(!empty($this->filename)){
			CommonUtility::deletePhoto($this->filename,$this->path);			
		}		
	}
		
}
/*end class*/
