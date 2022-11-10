<?php
class AR_wallet_cards extends CActiveRecord
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
		return '{{wallet_cards}}';
	}
	
	public function primaryKey()
	{
	    return 'card_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'card_uuid'=>t("card_uuid"),		    
		    'account_type'=>t("account_type"),		    
		    'account_id'=>t("account_id"),			    
		);
	}
	
	public function rules()
	{
		return array(
		  array('account_type,account_id', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('card_uuid,date_modified,ip_address','safe'),
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->card_uuid = CommonUtility::createUUID("{{wallet_cards}}",'card_uuid');
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
