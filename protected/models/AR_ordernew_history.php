<?php
class AR_ordernew_history extends CActiveRecord
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
		return '{{ordernew_history}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		 'created_at'=>"created_at",
		);
	}
	
	public function rules()
	{
		 return array(
            array('order_id,status', 
            'required','message'=> t(Helper_field_required) ),                          
            array('remarks,ramarks_trans','safe'),
         );
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->created_at = CommonUtility::dateNow();							
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