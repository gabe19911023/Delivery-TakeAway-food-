<?php
class AR_featured_location extends CActiveRecord
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
		return '{{featured_location}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'featured_name'=>t("Featured name"),
		    'location_name'=>t("Location name"),
		    'latitude'=>t("Latitude"),
		    'longitude'=>t("Longitude"),
		    'status'=>t("Status"),	
		);
	}
	
	public function rules()
	{
		return array(
		  array('featured_name,location_name,latitude,longitude,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('featured_name,location_name,latitude,longitude,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('featured_name','unique','message'=>t(Helper_field_unique)),
		  		  		 		  		 
		);
	}

     protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
				
		if(DEMO_MODE){				
		    return false;
		}
		
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
		parent::afterSave();
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
	
    protected function beforeDelete()
	{				
	    if(DEMO_MODE){				
		    return false;
		}
	    return true;
	}
	
	protected function afterDelete()
	{
		parent::afterDelete();		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
