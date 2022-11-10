<?php
class AR_category_availability extends CActiveRecord
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
		return '{{category}}';
	}
	
	public function primaryKey()
	{
	    return 'cat_id';	 
	}
		
	public function attributeLabels()
	{
		$time = t("Time");
		return array(
		    'monday_start'=>$time,
		    'monday_end'=>$time,
		    'tuesday_start'=>$time,
		    'tuesday_end'=>$time,
		    'wednesday_start'=>$time,
		    'wednesday_end'=>$time,
		    'thursday_start'=>$time,
		    'thursday_end'=>$time,
		    'friday_start'=>$time,
		    'friday_end'=>$time,
		    'saturday_start'=>$time,
		    'saturday_end'=>$time,
		    'sunday_start'=>$time,
		    'sunday_end'=>$time,
		);
	}
	
	public function rules()
	{
		return array(
		  
		  array('monday,tuesday,wednesday,thursday,friday,saturday,sunday,
		  monday_start,monday_end,tuesday_start,tuesday_end,wednesday_start,wednesday_end,
		  thursday_start,thursday_end,friday_start,friday_end,saturday_start,saturday_end,sunday_start,sunday_end
		  ', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('monday,tuesday,wednesday,thursday,friday,saturday,sunday,
		  monday_start,monday_end,tuesday_start,tuesday_end,wednesday_start,wednesday_end,
		  thursday_start,thursday_end,friday_start,friday_end,saturday_start,saturday_end,sunday_start,sunday_end','safe'),
		  		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		$days = AttributesTools::dayList();
		foreach ($days as $day=>$day_name) {
			$this[ $day."_start" ] = !empty($this[ $day."_start" ])? Date_Formatter::TimeTo24($this[$day."_start"]):'';
			$this[ $day."_end" ] = !empty($this[ $day."_end" ])? Date_Formatter::TimeTo24($this[$day."_end"]):'';
		}
		
		return true;
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
