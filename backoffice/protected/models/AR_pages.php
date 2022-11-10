<?php
class AR_pages extends CActiveRecord
{	
	   			
	public $multi_language, $title_translation, $long_content_translation,$image;
	
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
		return '{{pages}}';
	}
	
	public function primaryKey()
	{
	    return 'page_id';	 
	}
		
	public function attributeLabels()
	{
		return array(		    
		  'meta_image'=>t("Meta Image"),
		  'title'=>t("Title"),
		  'meta_title'=>t("Meta Title"),
		  'meta_description'=>t("Meta Description"),
		  'meta_keywords'=>t("Meta Keywords"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('slug,title,long_content,short_content', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('slug,title,long_content,status, short_content,meta_title,meta_description,meta_keywords,
		  title_translation,long_content_translation
		  ', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  		  
		  array('slug','unique','message'=>t(Helper_field_unique)),
		  
		  array('title_translation,long_content_translation','safe'),
		  
		  array('title,short_content,meta_title','length','max'=>255)
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			
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
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();			
				
		$name = array(); $description = array();
		/*if($this->multi_language){			
		
			$name  = $this->title_translation;
		    if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			   $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->title;
		    }			
		    
		    $description  = $this->long_content_translation;
		    if(isset($description[KMRS_DEFAULT_LANGUAGE])){
			   $description[KMRS_DEFAULT_LANGUAGE] = !empty($description[KMRS_DEFAULT_LANGUAGE])?$description[KMRS_DEFAULT_LANGUAGE]:$this->long_content;
		    }			
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->title;
			$description[KMRS_DEFAULT_LANGUAGE] = $this->long_content;
		}*/
		
		$name = $this->title_translation;
		$description = $this->long_content_translation;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->title;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->long_content;
		
		
		Item_translation::insertTranslation( 
		(integer) $this->page_id ,
		'page_id',
		'title',
		'long_content',
		array(	                  
		  'title'=>$name,
		  'long_content'=>$description
		),"{{pages_translation}}");
		
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
		
		Item_translation::deleteTranslation($this->page_id,'page_id','pages_translation');
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
