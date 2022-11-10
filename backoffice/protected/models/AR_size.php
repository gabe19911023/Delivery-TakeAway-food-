<?php
class AR_size extends CActiveRecord
{	

	public $size_name_translation;
	public $multi_language;
	
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
		return '{{size}}';
	}
	
	public function primaryKey()
	{
	    return 'size_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'size_name'=>t("Size Name"),		    
		    'status'=>t("Status"),	
		);
	}
	
	public function rules()
	{
		return array(
		  array('size_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('size_name,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('size_name_translation,date_created,date_modified,ip_address','safe'),
		 		  
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if(DEMO_MODE && !$this->isNewRecord && in_array($this->merchant_id,DEMO_MERCHANT)){				
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
				
		/*if($this->multi_language){			
			$name = $this->size_name_translation;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			    $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->size_name;
		    }	
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->size_name;
		}*/
		
		$name = array(); $description = array();
				
		$name = $this->size_name_translation;				
		$name[KMRS_DEFAULT_LANGUAGE] = $this->size_name;		
		
		Item_translation::insertTranslation( 
		(integer) $this->size_id ,
		'size_id',
		'size_name',
		'',
		array(	                  
		  'size_name'=>$name,	  
		),"{{size_translation}}");
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
	
	
   protected function beforeDelete()
	{				
	    if(DEMO_MODE && in_array($this->merchant_id,DEMO_MERCHANT)){
		    return false;
		}
	    return true;
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		Item_translation::deleteTranslation($this->size_id,'size_id','size_translation');		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
