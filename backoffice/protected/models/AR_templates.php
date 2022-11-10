<?php
class AR_templates extends CActiveRecord
{	
	   				
	public $multi_language,$email_title_translation,$email_content_translation,$email_template_type,
	$sms_title_translation,$sms_content_translation,$sms_template_type,
	$push_title_translation,$push_content_translation,$push_template_type
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
		return '{{templates}}';
	}
	
	public function primaryKey()
	{
	    return 'template_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'template_key'=>t("Template Key"),
		    'template_name'=>t("Template name"),
		    'enabled_email'=>t("Enabed Email"),
		    'enabled_sms'=>t("Enabed SMS"),
		    'enabled_push'=>t("Enabed Push"),
		    'tags'=>t("Tags")
		);
	}
	
	public function rules()
	{
		return array(
		  array('template_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  /*array('template_name,email_title_translation,email_content_translation,email_template_type', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), */
		  
		  array('enabled_email,enabled_sms,enabled_push,tags,email_title_translation,
		  email_content_translation,email_template_type,sms_title_translation,sms_content_translation,sms_template_type,
		  push_title_translation,push_content_translation,push_template_type',
		  'safe'),
		  
		  array('template_key','unique','message'=>t(Helper_field_unique)),
		  
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
				//$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();			
						
		if($this->multi_language){			
									
			/*EMAIL*/			
			$this->insertTranslations( 
			(integer) $this->template_id ,
			'template_id',
			array(
			  'template_type',
			  'title',
			  'content'
			),
			array(	    
			  'template_type'=>$this->email_template_type,            
			  'title'=>$this->email_title_translation,
			  'content'=>$this->email_content_translation
			),"{{templates_translation}}",'template_type', 'email' );			
						
			/*SMS*/		
			$this->insertTranslations( 
			(integer) $this->template_id ,
			'template_id',
			array(
			  'template_type',
			  'title',
			  'content'
			),
			array(	    
			  'template_type'=>$this->sms_template_type,            
			  'title'=>$this->sms_title_translation,
			  'content'=>$this->sms_content_translation
			),"{{templates_translation}}",'template_type','sms');				
			
			/*PUSH*/		
			$this->insertTranslations( 
			(integer) $this->template_id ,
			'template_id',
			array(
			  'template_type',
			  'title',
			  'content'
			),
			array(	    
			  'template_type'=>$this->push_template_type,            
			  'title'=>$this->push_title_translation,
			  'content'=>$this->push_content_translation
			),"{{templates_translation}}",'template_type','push');		
			
		}
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
		Item_translation::deleteTranslation($this->template_id,'template_id','templates_translation');
		
		Yii::app()->db->createCommand("
		DELETE FROM {{templates_translation}}
		WHERE template_id = ".q($this->template_id)."
		")->query();
	}
	
	public function getData($template_id,$template_type='email')
	{
		$data=array();
		$stmt="
		SELECT 
		a.template_id,
		b.template_type,
		IFNULL(b.language,'default') as language,		
		IFNULL(b.title,'') as  title_trans,
		IFNULL(b.content,'') as  content_trans
		FROM {{templates}} a		
		LEFT JOIN {{templates_translation}} b
		ON
		a.template_id = b.template_id
		
		WHERE a.template_id = ".q($template_id)."
		AND b.template_type=".q($template_type)."
		";
		//dump($stmt);
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {	
			   $data['title'][$val['language']] = $val['title_trans'];
			   $data['content'][$val['language']] = $val['content_trans'];
			}
			return $data;	
		}
		return false;
	}
	
	public function getFields($type='')
	{
		$fields_email = array();
		$fields_email[]=array(
		  'name'=>$type.'_template_type',
		  'placeholder'=>"Enter {{lang}} Type here",
		  'class'=>'',
		  'label'=>true,
		  'type'=>'hidden',
		  'value'=>$type
		);		
		if($type=="email"):
			$fields_email[]=array(
			  'name'=>$type.'_title_translation',
			  'placeholder'=>"Enter {{lang}} Subject here",
			  'class'=>'',
			  'label'=>true,
			  'type'=>'text'
			);
		endif;
		if($type=="push"):
			$fields_email[]=array(
			  'name'=>$type.'_title_translation',
			  'placeholder'=>"Enter {{lang}} Title here",
			  'class'=>'',
			  'label'=>true,
			  'type'=>'text'
			);
		endif;
		$fields_email[]=array(
		  'name'=>$type.'_content_translation',
		  'placeholder'=>"Enter {{lang}} Content here",
		  'class'=>$type=="email"?"summernote":'',
		  'label'=>false,
		  'type'=>'text_area'
		);
		return $fields_email;
	}
	
	public function insertTranslations($id='',$primary_key='', $column=array(), 
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
				
				$multiple_insert = array();
							
				Yii::app()->db->createCommand("DELETE FROM $table
				WHERE $primary_key =".q( (integer) $id)." $and
				")->query();
												
				foreach ($data[$column[0]] as $lang=>$val) {
					$params = array(
					  'language'=>$lang,
					  $primary_key=>(integer)$id,					  			 
					);
					foreach ($column as $colval) {
						$correct_val = isset($data[$colval][$lang])?$data[$colval][$lang]:'' ;
						$correct_val = str_replace("&amp;","&",$correct_val);
						$params[$colval] =  $correct_val ;
					}			
					$multiple_insert[] = $params;					
				}		
				
				$builder=Yii::app()->db->schema->commandBuilder;
		        $command=$builder->createMultipleInsertCommand($table,$multiple_insert);
		        $command->execute();
				return true;
			}
		//}
		return false;
	}	
		
}
/*end class*/
