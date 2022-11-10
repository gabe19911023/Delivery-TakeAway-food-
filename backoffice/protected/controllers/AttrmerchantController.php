<?php
class AttrmerchantController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
			
		return true;
	}
		
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else	    	    
	        	$this->render('error', array(
	        	 'error'=>$error
	        	));
	    }
	}
	
	public function actionsize_list()
	{		
		$this->pageTitle=t("Size List");
		$action_name='size_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/size_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		$tpl = '//tpl/list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/size_create")
		));	
	}
	
    public function actionsize_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Size") : t("Update Size");
		CommonUtility::setMenuActive('.attributes','.attrmerchant_size_list');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			
			$model = AR_size::model()->find('merchant_id=:merchant_id AND size_id=:size_id', 
		    array(':merchant_id'=>$merchant_id, ':size_id'=>$id ));			
		    			
			if(!$model){				
				$this->render("//tpl/error",array(
				 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
				));			
				Yii::app()->end();
			}												
		} else $model=new AR_size;
		
		$model->multi_language = $multi_language;

		if(isset($_POST['AR_size'])){
			$model->attributes=$_POST['AR_size'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/size_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data  = array();
		if($update && !isset($_POST['AR_size'])){
			$translation = AttributesTools::GetFromTranslation($id,'{{size}}',
			'{{size_translation}}',
			'size_id',
			array('size_id','size_name'),
			array('size_name'=>'size_name_translation')
			);					
			$data['size_name_translation'] = isset($translation['size_name'])?$translation['size_name']:'';			
		}
						
		$fields[]=array(
		  'name'=>'size_name_translation'
		);				

		$model->status = $model->isNewRecord?'publish':$model->status;	
		
		$this->render("//merchant/size_create",array(
		    'model'=>$model,	
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'links'=>array(
	            t("All Size")=>array(Yii::app()->controller->id.'/size_list'),        
                $this->pageTitle,
		    ),	    		    
		));
	}	
	
	public function actionsize_update()
	{
		$this->actionsize_create(true);
	}
		
	public function actionsize_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$model = AR_size::model()->find("size_id=:size_id AND merchant_id=:merchant_id",array(
		  ':size_id'=>$id,
		  ':merchant_id'=>$merchant_id
		));		
						
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/size_list'));			
		} else $this->render("error");
	}
	
	public function actioningredients_list()
	{		
		$this->pageTitle=t("Ingredients List");
		$action_name='ingredients_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/ingredients_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//tpl/list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/ingredients_create")
		));	
	}
	
    public function actioningredients_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Ingredients") : t("Update Ingredients");
		CommonUtility::setMenuActive('.attributes','.attrmerchant_ingredients_list');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			
			$model = AR_ingredients::model()->find('merchant_id=:merchant_id AND ingredients_id=:ingredients_id', 
		    array(':merchant_id'=>$merchant_id, ':ingredients_id'=>$id ));			
			if(!$model){				
				$this->render("//tpl/error",array(
				 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
				));										
				Yii::app()->end();
			}												
		} else $model=new AR_ingredients;
		
		$model->multi_language = $multi_language;

		if(isset($_POST['AR_ingredients'])){
			$model->attributes=$_POST['AR_ingredients'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/ingredients_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data  = array();
		if($update && !isset($_POST['AR_ingredients'])){
			$translation = AttributesTools::GetFromTranslation($id,'{{ingredients}}',
			'{{ingredients_translation}}',
			'ingredients_id',
			array('ingredients_id','ingredients_name'),
			array('ingredients_name'=>'ingredients_translation')
			);					
			$data['ingredients_translation'] = isset($translation['ingredients_name'])?$translation['ingredients_name']:'';			
		}
						
		$fields[]=array(
		  'name'=>'ingredients_translation'
		);				
							
		$model->status = $model->isNewRecord?'publish':$model->status;
		
		$this->render("//merchant/ingredients_create",array(
		    'model'=>$model,	
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'links'=>array(
	            t("All Ingredients")=>array(Yii::app()->controller->id.'/ingredients_list'),        
                $this->pageTitle,
		    ),	    		    
		));
	}	
	
	public function actioningredients_update()
	{
		$this->actioningredients_create(true);
	}
	
	public function actioningredients_delete()
	{
		$id = (integer) Yii::app()->input->get('id');
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
							
		$model = AR_ingredients::model()->find("ingredients_id=:ingredients_id AND merchant_id=:merchant_id",array(
		  ':ingredients_id'=>$id,
		  ':merchant_id'=>$merchant_id
		));		
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/ingredients_list'));			
		} else $this->render("error");
	}
		
	public function actioncookingref_list()
	{		
		$this->pageTitle=t("Cooking Reference List");
		$action_name='cookingref_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/cookingref_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');

		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//tpl/list';	
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/cookingref_create")
		));	
	}
	
    public function actioncookingref_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Cooking Reference") : t("Update Cooking Reference");
		CommonUtility::setMenuActive('.attributes','.attrmerchant_cookingref_list');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');				
			$model = AR_cookingref::model()->find('merchant_id=:merchant_id AND cook_id=:cook_id', 
		    array(':merchant_id'=>$merchant_id, ':cook_id'=>$id ));			
			if(!$model){				
				$this->render("//tpl/error",array(
				 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
				));	
				Yii::app()->end();
			}												
		} else $model=new AR_cookingref;
		
		$model->multi_language = $multi_language;

		if(isset($_POST['AR_cookingref'])){
			$model->attributes=$_POST['AR_cookingref'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/cookingref_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data  = array();
		if($update && !isset($_POST['AR_cookingref'])){
			$translation = AttributesTools::GetFromTranslation($id,'{{cooking_ref}}',
			'{{cooking_ref_translation}}',
			'cook_id',
			array('cook_id','cooking_name'),
			array('cooking_name'=>'cooking_translation')
			);					
			$data['cooking_translation'] = isset($translation['cooking_name'])?$translation['cooking_name']:'';			
		}
						
		$fields[]=array(
		  'name'=>'cooking_translation'
		);				
		
		
		$model->status = $model->isNewRecord?'publish':$model->status;
									
		$this->render("//merchant//cookingref_create",array(
		    'model'=>$model,	
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'links'=>array(
	            t("All Cooking Reference")=>array(Yii::app()->controller->id.'/cookingref_list'),        
                $this->pageTitle,
		    ),	    		    
		));
	}	
	
	public function actioncookingref_update()
	{
		$this->actioncookingref_create(true);
	}
	
	public function actioncookingref_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;				
		$model = AR_cookingref::model()->find("cook_id=:cook_id AND merchant_id=:merchant_id",array(
		  ':cook_id'=>$id,
		  ':merchant_id'=>$merchant_id
		));		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/cookingref_list'));			
		} else $this->render("error");
	}
		
}
/*end class*/