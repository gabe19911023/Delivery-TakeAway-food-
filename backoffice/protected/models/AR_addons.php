<?php
class AR_addons extends CActiveRecord
{	

    public $file;
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
		return '{{addons}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'addon_name	'=>t("Addon name"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('addon_name,uuid,version', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>'register' ),

		  array("uuid",'unique','message'=>t("Addon aready registered")),
		  		  
		  array('addon_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('image,path,date_created,date_modified,ip_address','safe'),

		  array('file','file','types'=>'zip','safe'=>false,'on'=>"upload"),
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();					
			} else {
				$this->date_modified = CommonUtility::dateNow();											
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
