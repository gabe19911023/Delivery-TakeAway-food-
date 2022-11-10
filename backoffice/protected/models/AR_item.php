<?php
class AR_item extends CActiveRecord
{	

	public $item_name_translation,$item_description_translation,
	$multi_language,$image,$category_selected,$item_price, $item_unit, $item_featured,
	$prices, $group_category,
	$discount_type,$discount_start,$discount_end,$discount_valid
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
		return '{{item}}';
	}
	
	public function primaryKey()
	{
	    return 'item_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'item_name'=>t("Item Name"),		    
		    'item_description'=>t("Description"),
		    'status'=>t("Status"),	
		    'image'=>t("Featured Image"),
		    'sku'=>t("SKU"),
			'item_price'=>t("Item price"),
			'slug'=>t("Slug")
		);
	}
	
	public function rules()
	{
		return array(
		  array('item_name,status,category_selected', 
		  'required','message'=> t( Helper_field_required ), 'on'=> "create,update" ),
		  
		  array('item_name,status,photo', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('item_description,item_name_translation,price,
		  item_description_translation,category_selected,photo,item_token,item_price,item_unit,track_stock,
		  supplier_id,item_short_description,sku,item_featured,available,color_hex
		  ','safe'),
		  
		  array('item_price', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('image', 'file', 'types'=> Helper_imageType, 'safe' => false,
			  'maxSize'=> Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => false,'on'=>'new','message'=>t(Helper_file_allowEmpty)
			),      
			
		 array('item_short_description', 'length', 'max'=>255,
              'tooShort'=>t(Helper_password_toshort) ,
              ),
		  		  
         array('sku','unique','message'=>t(Helper_field_unique)),

		 array('slug','unique','message'=>t(Helper_field_unique)),
              
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
				
		$allowed_scenario = array('create','update');
		
		/*if($this->scenario=="item_inventory"){
		   $this->not_available = $this->not_available>0?$this->not_available:2;
		}*/
		
		/*
		if($this->scenario=="remove_image" || $this->scenario=="item_inventory"){
			return true;
		}*/

		if(!in_array($this->scenario,$allowed_scenario)){
			return true;
		}		
		
		/*if(is_array($this->item_name_translation) && count($this->item_name_translation)){
			$this->item_name_trans = json_encode($this->item_name_translation);				
		} else $this->item_name_trans='';
		
		if(is_array($this->item_description_translation) && count($this->item_description_translation)){
			$this->item_description_trans = json_encode($this->item_description_translation);				
		} else $this->item_description_trans='';
		
		if(is_array($this->category_selected) && count($this->category_selected)){
			$this->category = json_encode($this->category_selected);				
		} else $this->category='';*/
		
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();	
			$this->item_token = CommonUtility::generateToken("{{item}}",'item_token', CommonUtility::generateAplhaCode(20) );
		} else {
			$this->date_modified = CommonUtility::dateNow();			
			if(empty($this->item_token)){
			$this->item_token = CommonUtility::generateToken("{{item}}",'item_token', CommonUtility::generateAplhaCode(20) );
			}
		}

		if(empty($this->slug)){
			$this->slug = $this->createSlug(CommonUtility::toSeoURL($this->item_name));
		}

		$this->ip_address = CommonUtility::userIp();	
						
		return true;
	}

	private function createSlug($slug='')
	{
		$stmt="SELECT count(*) as total FROM {{item}}
		WHERE slug=".q($slug)."
		";					
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){	
			if($res['total']>0){
				$new_slug = $slug.$res['total'];					
				return self::createSlug($new_slug);
			}
		}
		return $slug;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
				
		/*if($this->scenario=="remove_image" || $this->scenario=="item_inventory"){
			return true;
		}*/
		$allowed_scenario = array('create','update');
		if(!in_array($this->scenario,$allowed_scenario)){
			return true;
		}		
				
		$merchant_id = (integer) $this->merchant_id;
				
		$name = $this->item_name_translation;
		$description = $this->item_description_translation;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->item_name;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->item_description;
		
						
		if($this->multi_language){	
			Item_translation::insertTranslation( 
			(integer) $this->item_id ,
			'item_id',
			'item_name',
			'item_description',
			array(	                  
			  'item_name'=>(array)$name,	  
			  'item_description'=>(array)$description,
			),"{{item_translation}}");
		}
		
		/*MEDIA*/
		if($this->image){
			$media = new AR_media;
			$media->merchant_id = (integer) $merchant_id;
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
		Yii::app()->db->createCommand("DELETE FROM {{item_relationship_category}}
		WHERE item_id=".q($this->item_id)."
		 ")->query();
				
		if(!empty($this->category_selected)){
			foreach ($this->category_selected as $cat_id) {
				$params_category[]=array(
				  'merchant_id'=>(integer)$merchant_id,
				  'item_id'=>(integer)$this->item_id,
				  'cat_id'=>(integer)$cat_id
				);
			}
			$builder=Yii::app()->db->schema->commandBuilder;
		    $command=$builder->createMultipleInsertCommand('{{item_relationship_category}}',$params_category);
		    $command->execute();			
		}
		
		
		/*INSERT SIZE*/
		if($this->isNewRecord){
			$item_size = new AR_item_size;
			$item_size->merchant_id = (integer)$merchant_id;			
			$item_size->item_id = (integer)$this->item_id;
			$item_size->price = (float)$this->item_price;		
			if(!empty($this->item_unit)){
				$item_size->size_id = (integer)$this->item_unit;
			} 	
			$item_size->save();			
		}
		
		/*DELETE META*/
		Yii::app()->db->createCommand("DELETE FROM {{item_meta}}
		WHERE item_id=".q($this->item_id)."
		AND merchant_id = ".q($merchant_id)."
		AND meta_name IN ('item_featured')
		 ")->query();
					
		$item_featured = array();	
		if($this->item_featured){						
			foreach ($this->item_featured as $id) {
				$params[]=array(
				  'merchant_id'=>(integer)$merchant_id,
				  'item_id'=>(integer)$this->item_id,
				  'meta_name'=>'item_featured',
				  'meta_id'=>$id
				);
			}		
			$builder=Yii::app()->db->schema->commandBuilder;
			$command=$builder->createMultipleInsertCommand('{{item_meta}}',$params);
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
		Item_translation::deleteTranslation($this->item_id,'item_id','item_translation');		
				
		/*DELETE SIZE*/		
		$item_size = AR_item_size::model()->deleteAll("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=> (integer) $this->merchant_id ,
		  ':item_id'=> (integer) $this->item_id
		));
		
		/*DELETE META*/
		Yii::app()->db->createCommand("DELETE FROM {{item_meta}}
		WHERE item_id=".q($this->item_id)."
		AND merchant_id = ".q($this->merchant_id)."		
		 ")->query();
		
		/*ITEM RELATIONSHIP*/
		Yii::app()->db->createCommand("DELETE FROM {{item_relationship_category}}
		WHERE item_id=".q($this->item_id)."
		 ")->query();
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
