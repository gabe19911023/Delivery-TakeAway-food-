<?php
class AR_tags extends CActiveRecord
{	
	   			
	public $tag_name_translation;
	public $description_translation;
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
		return '{{tags}}';
	}
	
	public function primaryKey()
	{
	    return 'tag_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'tag_name'=>t("Tag Name"),
		    'description'=>t("Description"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('tag_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('tag_name,description,tag_name_translation,description_translation', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('description,tag_name_translation,description_translation','safe')		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			
			/*if(is_array($this->tag_name_translation) && count($this->tag_name_translation)){
				$this->tag_name_trans = json_encode($this->tag_name_translation);
				$this->description_trans = json_encode($this->description_translation);
			} else {
				$this->tag_name_trans='';$this->description_trans='';
			}*/
			
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();					
			} else {
				//$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();			
				
		/*$name = array(); $description = array();
		if($this->multi_language){			
			
			$name  = $this->tag_name_translation;
		    if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			   $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->tag_name;
		    }	
		    
		    $description  = $this->description_translation;
		    if(isset($description[KMRS_DEFAULT_LANGUAGE])){
			   $description[KMRS_DEFAULT_LANGUAGE] = !empty($description[KMRS_DEFAULT_LANGUAGE])?$description[KMRS_DEFAULT_LANGUAGE]:$this->description;
		    }	
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->tag_name;
			$description[KMRS_DEFAULT_LANGUAGE] = $this->description;
		}*/
		
		$name = $this->tag_name_translation;
		$description = $this->description_translation;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->tag_name;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->description;
		
		
		Item_translation::insertTranslation( 
		(integer) $this->tag_id ,
		'tag_id',
		'tag_name',
		'description',
		array(	                  
		  'tag_name'=>$name,
		  'description'=>$description
		),"{{tags_translation}}");
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		Item_translation::deleteTranslation($this->tag_id,'tag_id','tags_translation');
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
