<?php
class AR_item_slug extends CActiveRecord
{	

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
		    'item_id'=>t("item_id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('item_id,merchant_id', 
		  'required','message'=> t( Helper_field_required ) ),		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			
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
		} else return true;
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
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
