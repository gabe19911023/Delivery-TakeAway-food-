<?php
class AR_payment_method_meta extends CActiveRecord
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
		return '{{payment_method_meta}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
	     return array(
	       'payment_method_id'=>"payment_method_id"
	     );
	}
	
	public function rules()
	{
		return array(

		     array('payment_method_id,meta_name,meta_value', 
            'required','message'=> t(Helper_field_required) ),                          
		
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