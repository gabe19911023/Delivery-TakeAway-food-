<?php
class AR_favorites extends CActiveRecord
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
		return '{{favorites}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'client_id'=>t("client id"),
		    'merchant_id'=>t("merchant id"),			
		);
	}
	
	public function rules()
	{
		 return array(
            array('client_id,merchant_id', 
            'required','message'=> t(Helper_field_required) ),                                                  
         );
	}
	
	public function scopes() {
	    return array(
	        'orderid' => array('order' => 'id DESC'),
	    );
	}

	public function Check($attribute,$params)
	{
	   $stmt="
		SELECT id FROM {{favorites}}
		WHERE client_id=".q( (integer) $this->client_id)."
		AND merchant_id = ".q( (integer) $this->merchant_id)."		
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			$this->addError('client_id',"Already saved this store");
		} 
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