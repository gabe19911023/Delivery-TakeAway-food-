<?php
class AR_pages_translation extends CActiveRecord
{	

	public $meta_image, $path;
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
		return '{{pages_translation}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'page_id'=>t("page_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('page_id,language,title', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('title,long_content,meta_title,meta_description,meta_keywords', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  		  
		  array('long_content,meta_title,meta_description,meta_keywords','safe')
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
