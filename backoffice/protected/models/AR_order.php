<?php
class AR_order extends CActiveRecord
{	

	public $first_name, $last_name, $avatar, $path, $order_items,
	$total_items
	;
	
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
		return '{{order}}';
	}
	
	public function primaryKey()
	{
	    return 'order_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'order_id'=>t("order_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('order_id,', 
		  'required','message'=> t( Helper_field_required ) ),		  		  		  
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