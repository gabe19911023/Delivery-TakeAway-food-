<?php
class AR_cookingref extends CActiveRecord
{	

	public $cooking_translation;
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
		return '{{cooking_ref}}';
	}
	
	public function primaryKey()
	{
	    return 'cook_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'cooking_name'=>t("Name"),		    
		    'status'=>t("Status"),	
		);
	}
	
	public function rules()
	{
		return array(
		  array('cooking_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('cooking_name,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('cooking_translation,date_created,date_modified,ip_address','safe'),
		 		  
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		/*if(is_array($this->cooking_translation) && count($this->cooking_translation)){
			$this->cooking_name_trans = json_encode($this->cooking_translation);				
		} else {
			$this->cooking_name_trans='';
		}*/
		
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
			$name = $this->cooking_translation;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			    $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->cooking_name;
		    }	
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->cooking_name;
		}*/
		
		$name = array(); $description = array();
				
		$name = $this->cooking_translation;		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->cooking_name;
		
		
		Item_translation::insertTranslation( 
		(integer) $this->cook_id ,
		'cook_id',
		'cooking_name',
		'',
		array(	                  
		  'cooking_name'=>$name,	  
		),"{{cooking_ref_translation}}");
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		Item_translation::deleteTranslation($this->cook_id,'cook_id','cooking_ref_translation');		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
