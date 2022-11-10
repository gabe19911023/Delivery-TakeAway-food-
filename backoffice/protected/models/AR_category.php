<?php
class AR_category extends CActiveRecord
{	

	public $category_translation,$category_description_translation,
	$multi_language,$image,$dish_selected, 
	$available_day, $available_time_start, $available_time_end
	;
	
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
		return '{{category}}';
	}
	
	public function primaryKey()
	{
	    return 'cat_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'category_name'=>t("Name"),		    
		    'category_description'=>t("Description"),		    
		    'status'=>t("Status"),	
		    'image'=>t("Featured Image"),	
		    'available_time_start[1]'=>t("From"),
		    'available_time_start[2]'=>t("From"),
		    'available_time_start[3]'=>t("From"),
		    'available_time_start[4]'=>t("From"),
		    'available_time_start[5]'=>t("From"),
		    'available_time_start[6]'=>t("From"),
		    'available_time_start[7]'=>t("From"),
		    'available_time_end[1]'=>t("To"),
		    'available_time_end[2]'=>t("To"),
		    'available_time_end[3]'=>t("To"),
		    'available_time_end[4]'=>t("To"),
		    'available_time_end[5]'=>t("To"),
		    'available_time_end[6]'=>t("To"),
		    'available_time_end[7]'=>t("To"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('category_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('category_name,category_description,
		  status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('photo,path,available_at_specific,sequence','safe'),
		  
		  array('category_name','length', 'min'=>0, 'max'=>255,
               'tooShort'=>t("category name is is too short"),
               'tooLong'=>t("category name is is too long"),                           
             ),

		  array('category_translation,category_description_translation','safe'),

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

		/*if($this->scenario=="remove_image" || $this->scenario=="availability"){
			return true;
		}*/
		
		if($this->scenario=="remove_image"){
			return true;
		} elseif ($this->scenario=="availability"){
			$days = AttributesTools::dayWeekList();					
			foreach ($days as $key=>$item) {				
				$day_of_week = isset($this->available_day[$key])?$key:0;		
				$status = isset($this->available_day[$key])? $this->available_day[$key] :0;						
				$start = isset($this->available_time_start[$key])? $this->available_time_start[$key] :null;				
				$end = isset($this->available_time_end[$key])? $this->available_time_end[$key] :null;						
				AR_availability::saveMeta($this->merchant_id,'category',$this->cat_id,$day_of_week,$status,$start,$end);
			}						
			return true;
		}
		
		$name = array(); $description = array();
				
		$name = $this->category_translation;
		$description = $this->category_description_translation;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->category_name;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->category_description;
		
		/*DISH RELATIONSHIP*/
		Yii::app()->db->createCommand("DELETE FROM {{category_relationship_dish}}
		WHERE cat_id=".q($this->cat_id)."
		 ")->query();
		
		if(!empty($this->dish_selected)){
			foreach ($this->dish_selected as $dish_id) {
				$params_dish[]=array('cat_id'=>(integer)$this->cat_id,'dish_id'=>(integer)$dish_id);
			}
			$builder=Yii::app()->db->schema->commandBuilder;
		    $command=$builder->createMultipleInsertCommand('{{category_relationship_dish}}',$params_dish);
		    $command->execute();			
		}
		
		
		Item_translation::insertTranslation( 
		(integer) $this->cat_id ,
		'cat_id',
		'category_name',
		'category_description',
		array(	                  
		  'category_name'=>$name,	  
		  'category_description'=>$description,
		),"{{category_translation}}");
		
		
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
		Item_translation::deleteTranslation($this->cat_id,'cat_id','category_translation');		
				
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
