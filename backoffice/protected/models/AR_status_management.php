<?php
class AR_status_management extends CActiveRecord
{	
	public $multi_language,$title_translation;
	   			
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
		return '{{status_management}}';
	}
	
	public function primaryKey()
	{
	    return 'status_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'group_name'=>t("Group Name"),
		    'status'=>t("Status Key"),
		    'title'=>t("Title"),
		    'color_hex'=>t("Color Hex"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('group_name,status,title', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('group_name,status,title', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('color_hex,font_color_hex,title_translation','safe'),	  
		  
		  array('status', 'filter', 'filter'=>'trim'),
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			
			if(DEMO_MODE){				
			    return false;
			}

			$this->status = str_replace(" ","",$this->status);
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
				
		$name = array();
		/*if($this->multi_language){			
			$name  = $this->title_translation;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			   $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->title;
		    }	
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->title;
		}*/
		
		$name = $this->title_translation;				
		$name[KMRS_DEFAULT_LANGUAGE] = $this->title;		
		
		Item_translation::insertTranslation( 
		(integer) $this->status_id ,
		'status_id',
		'title',	
		'',				
		array(	                  
		  'title'=>$name,			  
		),"{{status_management_translation}}");
		
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
		Item_translation::deleteTranslation($this->status_id,'status_id','status_management_translation');
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
