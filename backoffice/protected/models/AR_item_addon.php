<?php
class AR_item_addon extends CActiveRecord
{	

	public $multi_option_value_text, $multi_option_value_selection, $merchantid, $itemid;
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
		return '{{item_relationship_subcategory}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'merchant_id'=>t("Merchant ID"),
		  'multi_option_value_text'=>t("Enter how many can select"),
		  'item_size_id'=>t("Select Item Price")
		);
	}
	
	public function rules()
	{
		return array(
		
		  array('merchant_id,item_id,subcat_id,item_size_id', 
		  'required','message'=> t( Helper_field_required ), 'on'=> "insert,update" ),		  		 

		  array('subcat_id','unique_addon','message'=>t(Helper_field_unique)), 
		  
		  array('multi_option,multi_option_value,require_addon,multi_option_value_selection,pre_selected', 'safe'),
		  
		  array('multi_option_value_text', 'numerical', 'integerOnly' => true,	'min'=>1,	  
		  'message'=>t(Helper_field_numeric)),
		  
		);
	}
	
	public function unique_addon($attribute,$params)
	{
		$and = '';
		if(!$this->isNewRecord){
			$and = " AND id <> ".q((integer)$this->id)." ";
		}		
		$stmt = "
		SELECT id FROM {{item_relationship_subcategory}}
		WHERE merchant_id = ". q($this->merchantid)."
		AND item_id = ".q($this->itemid)."
		AND subcat_id = ".q($this->subcat_id)."
		AND item_size_id = ".q($this->item_size_id)."
		$and
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			$this->addError('subcat_id', t("This addon is already been added") );	    
		}						
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
						
		if(DEMO_MODE && !$this->isNewRecord && in_array($this->merchant_id,DEMO_MERCHANT)){				
		    return false;
		}
				
		switch ($this->scenario) {
			case "insert":
			case "update":
				if($this->multi_option=="two_flavor"){
					$this->multi_option_value = $this->multi_option_value_selection;
		        } else if($this->multi_option=="custom"){
		        	$this->multi_option_value = (integer) $this->multi_option_value_text;
		        } else {
		        	$this->multi_option_value = '';
		        }
				break;
		
			default:
				break;
		}
				
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		$params = array();
		
		switch ($this->scenario) {
			case "insert":
			case "update":		

			    /*DELETE RELATIONSHIP*/
				Yii::app()->db->createCommand("DELETE FROM {{item_relationship_subcategory_item}}
				WHERE merchant_id = ".q((integer)$this->merchantid)."  AND item_id=".q((integer)$this->itemid)."
				AND subcat_id = ".q((integer)$this->subcat_id)."
				 ")->query();  
					
				$stmt="
				SELECT * FROM {{subcategory_item_relationships}}
				WHERE subcat_id = ".q($this->subcat_id)."
				";				
				if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
					foreach ($res as $val) {						
						$params[]=array(
						  'merchant_id'=>(integer)$this->merchantid,
						  'item_id'=>(integer)$this->itemid,
						  'subcat_id'=>(integer)$val['subcat_id'],
						  'sub_item_id'=>(integer)$val['sub_item_id'],
						);
					}		
					$builder=Yii::app()->db->schema->commandBuilder;
				    $command=$builder->createMultipleInsertCommand('{{item_relationship_subcategory_item}}',$params);
				    $command->execute();			
				}				
				break;
				
			default:
				break;
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
		
		/*DELETE RELATIONSHIP*/
		Yii::app()->db->createCommand("DELETE FROM {{item_relationship_subcategory_item}}
		WHERE merchant_id = ".q((integer)$this->merchantid)."  AND item_id=".q((integer)$this->itemid)."
		AND subcat_id = ".q((integer)$this->subcat_id)."
		 ")->query();  	

		/*ADD CACHE REFERENCE*/
		CCacheData::add();	
	}
		
}
/*end class*/
