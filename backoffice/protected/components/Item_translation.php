<?php
class Item_translation
{		
	
	/*
	@parameters
	$data = 
	Array
	(
	    [ar] => ar
	    [en] => 
	    [jp] => jp
	)
	
	Array
	(
	    [ar] => 
	    [en] => 
	    [jp] => 
	)
	
	Item_translation::insertTranslation( 
	(integer) $cat_id ,
	'cat_id',
	'category_name',
	'category_description',
	array(	                  
	  'category_name'=>isset($this->data['category_name_trans'])?$this->data['category_name_trans']:'',
	  'category_description'=>isset($this->data['category_description_trans'])?$this->data['category_description_trans']:'',
	),"{{category_translation}}");
	
	*/	
	public static function insertTranslation($id='',$primary_key='', $column1 = '', $column2='', 
	$data=array(), $table ='')
	{
		$params = array();
		
		if(!Yii::app()->db->schema->getTable($table)){
			return false;
		}
				
		
		//if(CommonUtility::MultiLanguage()){
			if(is_array($data) && count($data)>=1){
				
				Yii::app()->db->createCommand("DELETE FROM $table
				WHERE $primary_key =".q( (integer) $id)."
				")->query();
															
				foreach ($data[$column1] as $lang=>$val) {	
					if(!empty($column2)){
						$params = array(
						  $primary_key=>(integer)$id,
						  $column1=>$val,
						  'language'=>$lang,
						  $column2=>$data[$column2][$lang]
						);
					} else {									
						$params = array(
						  $primary_key=>(integer)$id,
						  $column1=>$val,
						  'language'=>$lang
						);
					}
					$params = Yii::app()->input->xssClean($params);
					Yii::app()->db->createCommand()->insert($table,$params);
				}								
				return true;
			}
		//}
		return false;
	}
	
	public static function deleteTranslation($id='', $primary='', $table='')
	{
		$trans_table = "{{".$table."}}";
		if(Yii::app()->db->schema->getTable($trans_table)){
			$stmt = "
			DELETE FROM $trans_table
			WHERE $primary=".q($id)."
			";	
			try {			
			    Yii::app()->db->createCommand($stmt)->query();
			} catch (Exception $e) {
			    // $e->getMessage()
			}

		}
	}
	
	public static function insertTranslations($id='',$primary_key='', $column=array(), 
	$data=array(), $table ='',$key='',$key_value='')
	{		
		$params = array(); $and='';
		if(!empty($key) && !empty($key_value)){
			$and = "AND $key=".q($key_value)." ";
		}
		
		if(!Yii::app()->db->schema->getTable($table)){
			return false;
		}
		
		//if(CommonUtility::MultiLanguage()){
			if(is_array($data) && count($data)>=1){
				
							
				Yii::app()->db->createCommand("DELETE FROM $table
				WHERE $primary_key =".q( (integer) $id)." $and
				")->query();
												
				foreach ($data[$column[0]] as $lang=>$val) {
					$params = array(
					  'language'=>$lang,
					  $primary_key=>(integer)$id,					  			 
					);
					foreach ($column as $colval) {
						$params[$colval] = $data[$colval][$lang];
					}										
					$params = Yii::app()->input->xssClean($params);
					Yii::app()->db->createCommand()->insert($table,$params);
				}				
				return true;
			}
		//}
		return false;
	}	
	
}
/*end class*/