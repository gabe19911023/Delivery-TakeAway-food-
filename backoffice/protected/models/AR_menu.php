<?php
class AR_menu extends CActiveRecord
{	

	public $child_menu ;
	
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
		return '{{menu}}';
	}
	
	public function primaryKey()
	{
	    return 'menu_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'menu_type'=>t("menu_type"),		    
		);
	}
	
	public function rules()
	{
		//link,action_name
		return array(
		  array('menu_type,menu_name,status,visible', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('menu_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		 
		  
		  array('meta_value1','safe'),
		  
		  array('link','url', 'defaultScheme'=>'http', 'on'=>"custom_link")
		  		  
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
		
		if($this->scenario=="theme_menu"){
			AR_admin_meta::saveMeta(PPages::menuActiveKey(),$this->menu_id);
			
			if(is_array($this->child_menu) && count($this->child_menu)>=1){
				foreach ($this->child_menu as $index => $items) {					
					$model = MMenu::get($items['menu_id'],PPages::menuType());
					if($model){
						$model->menu_name = $items['menu_name'];
						$model->sequence = intval($index);
						$model->save();
					}
				}
			}
		}

		if($this->scenario=="theme_menu_merchant"){
			AR_merchant_meta::saveMeta($this->meta_value1,PPages::menuActiveKey(),$this->menu_id,$this->meta_value1);
			
			if(is_array($this->child_menu) && count($this->child_menu)>=1){
				foreach ($this->child_menu as $index => $items) {					
					$model = MMenu::get($items['menu_id'],PPages::menuMerchantType());
					if($model){						
						$model->menu_name = $items['menu_name'];
						$model->meta_value1 = $this->meta_value1;
						$model->sequence = intval($index);
						$model->save();
					}
				}
			}
		}
		
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		
		if($this->scenario=="theme_menu"){
			
			AR_menu::model()->deleteAll('menu_type=:menu_type AND parent_id=:parent_id',array(
			   ':menu_type'=> PPages::menuType(),
			   ':parent_id'=>$this->menu_id,
			));
			
			$model= AR_admin_meta::model()->find("meta_name=:meta_name",array(
			  ':meta_name'=>PPages::menuActiveKey()
			));
			if($model){
				$model->delete();
			}
		} elseif ($this->scenario=="theme_menu_merchant" ){
			AR_menu::model()->deleteAll('menu_type=:menu_type AND parent_id=:parent_id AND meta_value1=:meta_value1 ',array(
				':menu_type'=> PPages::menuMerchantType(),
				':parent_id'=>$this->menu_id,
				':meta_value1'=>$this->meta_value1
			 ));			 
			 $model= AR_merchant_meta::model()->find("meta_name=:meta_name AND meta_value1=:meta_value1",array(
			   ':meta_name'=>PPages::menuActiveKey(),
			   ':meta_value1'=>$this->meta_value1
			 ));
			 if($model){
				 $model->delete();
			 }
		}
		
		
		CCacheData::add();
	}
		
}
/*end class*/