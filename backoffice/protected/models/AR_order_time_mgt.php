<?php
class AR_order_time_mgt extends CActiveRecord
{	

	public $days_selected,$order_status_selected;
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
		return '{{order_time_management}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'start_time'=>t("Start Time"),		    
		    'end_time'=>t("End Time"),	
		    'number_order_allowed'=>t("Number Order Allowed"),	
		);
	}
	
	public function rules()
	{
		return array(
		  array('group_id,transaction_type,days_selected,start_time,end_time,number_order_allowed,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('transaction_type,days_selected,start_time,end_time,number_order_allowed
		  ', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('number_order_allowed', 'numerical', 'integerOnly' => true,		  
		  'min'=>1,'max'=>100,
		  'tooSmall'=>t("Minimum value is 1"),
		  'message'=>t(Helper_field_numeric)),
		  
		  array('order_status,order_status_selected','safe')
		  		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
				
		$this->start_time = !empty($this->start_time)? Date_Formatter::TimeTo24($this->start_time):'';
		$this->end_time = !empty($this->end_time)? Date_Formatter::TimeTo24($this->end_time):'';
		$this->days = $this->days_selected[0];
		
		$group_id=1;
		$max = Yii::app()->db->createCommand()->select('max(id) as max')->from('{{order_time_management}}')->queryScalar();		
		$group_id = ($max + 1);		
		
		if($this->isNewRecord){
			$this->group_id = $group_id;
		} else {
			//
		}				
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();		
		
		$merchant_id = (integer) $this->merchant_id;		
		
		 AR_order_time_mgt::model()->deleteAll("merchant_id=:merchant_id AND group_id=:group_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':group_id'=>$this->group_id
		));

		$params = array();	
		foreach ($this->days_selected as $key=> $day) {			
			$params[] = array(
			  'group_id'=>$this->group_id,
			  'merchant_id'=> $merchant_id,
			  'transaction_type'=>$this->transaction_type,
			  'days'=>$day,
			  'start_time'=>$this->start_time,
			  'end_time'=>$this->end_time,
			  'number_order_allowed'=>(integer) $this->number_order_allowed,
			  'order_status'=> is_array($this->order_status_selected)? json_encode($this->order_status_selected) : '',
			  'status'=>$this->status,
			);
		}		
		$builder=Yii::app()->db->schema->commandBuilder;
		$command=$builder->createMultipleInsertCommand('{{order_time_management}}',$params);
		$command->execute();		
	}

	protected function afterDelete()
	{
		if(!parent::afterDelete()){
			return false;
		}
	}
		
}
/*end class*/
