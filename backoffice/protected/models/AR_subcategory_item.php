<?php
class AR_subcategory_item extends CActiveRecord
{	

	public $sub_item_name_translation,$item_description_translation,
	$multi_language,$image,$category_selected;	
	
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
		return '{{subcategory_item}}';
	}
	
	public function primaryKey()
	{
	    return 'sub_item_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'sub_item_name'=>t("AddOn Item"),		    
		    'item_description'=>t("Description"),
		    'status'=>t("Status"),	
		    'image'=>t("Featured Image"),		
		    'price'=>t("Price"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('sub_item_name,status,category_selected', 		  
		  'required','message'=> t( Helper_field_required ),
		  'on'=>"insert,update"
		  ),
		  
		  array('sub_item_name,item_description,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('category,price,photo,sub_item_name_translation,item_description_translation','safe'),
		  
		  array('price', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('image', 'file', 'types'=> Helper_imageType, 'safe' => false,
			  'maxSize'=> Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => false,'on'=>'new','message'=>t(Helper_file_allowEmpty)
			),      
		  		  
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
		
		if($this->scenario=="remove_image"){
			return true;
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
		
		if($this->scenario=="remove_image"){
			return true;
		}
				
		$name = $this->sub_item_name_translation;
		$description = $this->item_description_translation;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->sub_item_name;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->item_description;
						
		if($this->multi_language){	
			Item_translation::insertTranslation( 
			(integer) $this->sub_item_id ,
			'sub_item_id',
			'sub_item_name',
			'item_description',
			array(	                  
			  'sub_item_name'=>(array)$name,	  
			  'item_description'=>(array)$description,
			),"{{subcategory_item_translation}}");
		}
		
		/*MEDIA*/
		if($this->image){
			$media = new AR_media;
			$media->merchant_id = (integer) $this->merchant_id;
			$media->title = $this->image->name;
			$media->filename = $this->photo;
			$media->path = CommonUtility::uploadPath(false);
			$media->size = $this->image->size;
			$media->media_type = $this->image->type;
			$media->date_created = CommonUtility::dateNow();
			$media->ip_address = CommonUtility::userIp();
			$media->save();
		}
		
		/*ITEM RELATIONSHIP*/
		Yii::app()->db->createCommand("DELETE FROM {{subcategory_item_relationships}}
		WHERE sub_item_id=".q($this->sub_item_id)."
		 ")->query();
		
		if(!empty($this->category_selected)){
			foreach ($this->category_selected as $subcat_id) {
				$params_dish[]=array('sub_item_id'=>(integer)$this->sub_item_id,'subcat_id'=>(integer)$subcat_id);
			}
			$builder=Yii::app()->db->schema->commandBuilder;
		    $command=$builder->createMultipleInsertCommand('{{subcategory_item_relationships}}',$params_dish);
		    $command->execute();			
		}
		
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
		Item_translation::deleteTranslation($this->sub_item_id,'sub_item_id','subcategory_item_translation');		
				
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
