<?php
class AR_ingredients extends CActiveRecord
{	

	public $ingredients_translation;
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
		return '{{ingredients}}';
	}
	
	public function primaryKey()
	{
	    return 'ingredients_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'ingredients_name'=>t("Ingredients Name"),		    
		    'status'=>t("Status"),	
		);
	}
	
	public function rules()
	{
		return array(
		  array('ingredients_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('ingredients_name,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('ingredients_translation,date_created,date_modified,ip_address','safe'),
		 		  
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		/*if(is_array($this->ingredients_translation) && count($this->ingredients_translation)){
			$this->ingredients_name_trans = json_encode($this->ingredients_translation);				
		} else {
			$this->ingredients_name_trans='';
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
			$name = $this->ingredients_translation;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			    $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->ingredients_name;
		    }	
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->ingredients_name;
		}*/
		
		$name = $this->ingredients_translation;
		$name[KMRS_DEFAULT_LANGUAGE] = $this->ingredients_name;	
		
		Item_translation::insertTranslation( 
		(integer) $this->ingredients_id ,
		'ingredients_id',
		'ingredients_name',
		'',
		array(	                  
		  'ingredients_name'=>$name,	  
		),"{{ingredients_translation}}");
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		Item_translation::deleteTranslation($this->ingredients_id,'ingredients_id','ingredients_translation');		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
