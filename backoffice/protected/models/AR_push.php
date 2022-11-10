<?php
class AR_push extends CActiveRecord
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
		return '{{push}}';
	}
	
	public function primaryKey()
	{
	    return 'push_uuid';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'push_type'=>t("push_type"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('push_type,provider,channel_device_id,title,body', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('title,body', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('date_created,ip_address,response','safe'),
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->push_uuid = CommonUtility::createUUID('{{push}}','push_uuid');
				$this->date_created = CommonUtility::dateNow();					
			} 
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{		
		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$get_params = array( 
		   'push_uuid'=> $this->push_uuid,
		   'key'=>$cron_key,
		);			
				
		if($this->scenario=="insert"){
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/sendwebpush?".http_build_query($get_params) );
		}

		parent::afterSave();
	}

	protected function afterDelete()
	{
		parent::afterDelete();			
	}
		
}
/*end class*/
