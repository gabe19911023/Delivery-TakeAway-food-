<?php
class AR_availability extends CActiveRecord
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
		return '{{availability}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'merchant_id'=>t("merchant_id"),		    		  
		);
	}
	
	public function rules()
	{
		return array(
		  array('merchant_id,meta_name,meta_value', 
		  'required','message'=> t( Helper_field_required ) ),		
		  array('day_of_week,start_time,end_time,date_created,date_modified,ip_address','safe')  		  		                   		 		 		
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
	
	public static function saveMeta($merchant_id=0,$meta_name='', $meta_value='', $day_of_week=0, $status='', $start_time='', $end_time='')
	{		
		$model=AR_availability::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name 
		AND meta_value=:meta_value AND day_of_week=:day_of_week ",array(
		  ':merchant_id'=>intval($merchant_id),
		  ':meta_name'=>$meta_name,
		  ':meta_value'=>$meta_value,
		  ':day_of_week'=>$day_of_week
		));
		if(!$model){
			$model = new AR_availability;
			$model->meta_name = $meta_name;
			$model->merchant_id = intval($merchant_id);
		}			
		
		$model->meta_value=$meta_value;
		$model->day_of_week = intval($day_of_week);
		$model->status = intval($status);
		$model->start_time = !empty($start_time)?Date_Formatter::TimeTo24($start_time):null;
		$model->end_time = !empty($end_time)?Date_Formatter::TimeTo24($end_time):null;
		$model->save();
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
		
		return true;
	}
		
	public static function getValue($merchant_id=0, $meta_name='', $meta_value='' )
	{
		$day = array(); $start = array(); $end = array();
		$model = AR_availability::model()->findAll("merchant_id=:merchant_id AND meta_name=:meta_name 
        AND meta_value=:meta_value",array(
		  ':merchant_id'=>intval($merchant_id),
		  ':meta_name'=>$meta_name,
		  ':meta_value'=>$meta_value
        ));
        if($model){        	
        	foreach ($model as $item) {        		
        		if($item->status==1){
        			$day[] = $item->day_of_week;
        		}
        		$start[$item->day_of_week] = $item->start_time;
        		$end[$item->day_of_week] = $item->end_time;
        	}        	
        }        
        $data  = array(
    	  'day'=>$day,
    	  'start'=>$start,
    	  'end'=>$end
    	);
    	return $data;
	}
	
}
/*end class*/
