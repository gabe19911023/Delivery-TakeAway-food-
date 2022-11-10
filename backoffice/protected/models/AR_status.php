<?php
class AR_status extends CActiveRecord
{	
	   				
	public $multi_language,$description_translation;
	
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
		return '{{order_status}}';
	}
	
	public function primaryKey()
	{
	    return 'stats_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'description'=>t("Status")		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('description', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('description', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),	
		  array('font_color_hex,background_color_hex,description_translation','safe'),
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
		/*if($this->multi_language){			
			$name  = $this->description_translation;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			   $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->description;
		    }	 
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->description;
		}*/	
		
		$name = array(); $description = array();
				
		$name = $this->description_translation;				
		$name[KMRS_DEFAULT_LANGUAGE] = $this->description;
		
		Item_translation::insertTranslation( 
		(integer) $this->stats_id ,
		'stats_id',
		'description',	
		'',				
		array(	                  
		  'description'=>$name,			  
		),"{{order_status_translation}}");	
		
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
		Item_translation::deleteTranslation($this->stats_id,'stats_id','order_status_translation');
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
	public static function getTranslation($description='',$lang='')
	{
		
		$stmt="
		SELECT a.description,
		b.description as description_trans
		
		FROM {{order_status}} a
		LEFT JOIN {{order_status_translation}} b
		ON 
		a.stats_id = b.stats_id
		
		WHERE a.description = ".q($description)."
		AND b.language = ".q($lang)."
		";						
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){						
			return $res['description_trans'];
		}
		return $service_code;
	}
}
/*end class*/
