<?php
class AR_notifications extends CActiveRecord
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
		return '{{notifications}}';
	}
	
	public function primaryKey()
	{
	    return 'notification_uuid';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'notication_channel'=>t("notication_channel"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('notication_channel,notification_event,notification_type,message', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('message', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('date_created,ip_address','safe'),
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->notification_uuid = CommonUtility::createUUID('{{notifications}}','notification_uuid');
				$this->date_created = CommonUtility::dateNow();					
			} 
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();		

		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$get_params = array( 
		   'notification_uuid'=> $this->notification_uuid,
		   'key'=>$cron_key,
		);			

		if($this->scenario=="insert"){
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/sendnotifications?".http_build_query($get_params) );
		}
				
			
	}

	protected function afterDelete()
	{
		parent::afterDelete();			
	}
		
}
/*end class*/
