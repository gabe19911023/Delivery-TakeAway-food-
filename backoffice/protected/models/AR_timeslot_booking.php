<?php
class AR_timeslot_booking extends CActiveRecord
{	

	public $mtid;
	
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
		return '{{timeslot_booking}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'days'=>t("Days"),		    
		    'start_time'=>t("Start Time"),	
		    'end_time'=>t("End Time"),	
		    'number_order_allowed'=>t("Number Of Allowed")
		);
	}
	
	public function rules()
	{
		return array(
		  array('days,start_time,end_time,number_order_allowed', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('days,start_time,end_time,number_order_allowed', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  		 
		  array('number_order_allowed', 'numerical', 'integerOnly' => true,		  
		  'min'=>1,'max'=>100,
		  'tooSmall'=>t("Minimum value is 1"),
		  'message'=>t(Helper_field_numeric)),
		  
		  //array('days','unique', 'message'=>t(Helper_field_unique) ),
		  
		  array('days','days_unique'),
		  
		);
	}
	
	public function days_unique($attribute,$params)
	{
		$and = '';
		if(!$this->isNewRecord){
			$and = " AND id <> ".q((integer)$this->id)." ";
		}		
		$stmt = "
		SELECT id FROM {{timeslot_booking}}
		WHERE merchant_id = ". q($this->mtid)."
		AND days = ".q($this->days)."		
		$and
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			$this->addError('days', t("days value already exist") );	    
		}					
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		$this->start_time = !empty($this->start_time)? Date_Formatter::TimeTo24($this->start_time):'';
		$this->end_time = !empty($this->end_time)? Date_Formatter::TimeTo24($this->end_time):'';
		
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();					
		} else {
			$this->date_modified = CommonUtility::dateNow();											
		}
		$this->ip_address = CommonUtility::userIp();
		
		return true;
	}
	
	protected function afterSave()
	{
		if(!parent::afterSave()){
			return false;
		}
	}

	protected function afterDelete()
	{
		if(!parent::afterDelete()){
			return false;
		}
	}
		
}
/*end class*/
