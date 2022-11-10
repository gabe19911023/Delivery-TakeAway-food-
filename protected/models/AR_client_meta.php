<?php
class AR_client_meta extends CActiveRecord
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
		return '{{client_meta}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'client_id'=>t("Client ID"),		    
		);
	}
	
	public function rules()
	{
		return array(

		  array('client_id', 
		  'required','message'=> t( Helper_field_required ) ,'on'=>'registration_phone' ),
		  
		  array('meta1,meta2,meta3,meta4,date_created,ip_address','safe')
		  		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			$this->date_created = CommonUtility::dateNow();	
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