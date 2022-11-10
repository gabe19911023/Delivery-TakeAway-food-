<?php
class AR_booking extends CActiveRecord
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
		return '{{bookingtable}}';
	}
	
	public function primaryKey()
	{
	    return 'booking_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'number_guest'=>t("Number Of Guests"),		    
		    'date_booking'=>t("Date Of Booking"),
		    'booking_time'=>t("Time"),
		    'booking_name'=>t("Name"),
		    'email'=>t("Email Address"),
		    'mobile'=>t("Mobile Number"),
		    'booking_notes'=>t("Customer Instructions"),
		    'status'=>t("Status")
		);
	}
	
	public function rules()
	{
		return array(
		  array('booking_name,date_booking,booking_time,email,mobile,status,number_guest', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('booking_name,date_booking,booking_time,email,mobile,booking_notes', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('booking_notes','safe'),
		  
		  array('email', 'email', 'message'=> CommonUtility::t(Helper_field_email) ),	  
		  
		  array('mobile','length', 'min'=>8, 'max'=>15,
               'tooShort'=>t("{attribute} number is too short (minimum is 8 characters).")               
             ),
             
         array('number_guest', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),
		      
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
