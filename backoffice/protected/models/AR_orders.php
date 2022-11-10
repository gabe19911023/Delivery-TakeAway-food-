<?php
class AR_orders extends CActiveRecord
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
		return '{{order}}';
	}
	
	public function primaryKey()
	{
	    return 'order_id';	 
	}
		
	public function attributeLabels()
	{
		//
	}
	
	public function rules()
	{
		//
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
		
	protected function afterDelete()
	{
	    parent::afterDelete();	    	
	    Yii::app()->db->createCommand("DELETE FROM 
		    {{order_delivery_address}} WHERE order_id=".q($this->order_id)." ")->query();   
	    
	    Yii::app()->db->createCommand("DELETE FROM 
		    {{order_details}} WHERE order_id=".q($this->order_id)." ")->query();   
	    
	    Yii::app()->db->createCommand("DELETE FROM 
		    {{order_history}} WHERE order_id=".q($this->order_id)." ")->query();   
	}		
		
}
/*end class*/
