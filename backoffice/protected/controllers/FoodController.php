<?php
class FoodController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		
		$meta = AR_merchant_meta::getMeta(Yii::app()->merchant->merchant_id,array('tax_enabled','tax_type'));		
		$tax_enabled = isset($meta['tax_enabled'])?$meta['tax_enabled']['meta_value']:false;
		$tax_type = isset($meta['tax_type'])?$meta['tax_type']['meta_value']:'';
		Yii::app()->params['tax_menu_settings'] = array(
		  'tax_enabled'=>$tax_enabled,
		  'tax_type'=>$tax_type,
		);		
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
	
	public function actioncategory()
	{		
		$this->pageTitle=t("Category List");
		$action_name='category_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/category_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//tpl/list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/category_create")
		));	
	}	
	
   public function actioncategory_create($update=false)
   {
		$this->pageTitle = $update==false? t("Add Category") : t("Update Category");
		CommonUtility::setMenuActive('.food','.food_category');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		$upload_path = CMedia::merchantFolder();
		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');							
			$model = AR_category::model()->find('merchant_id=:merchant_id AND cat_id=:cat_id', 
		    array(':merchant_id'=>$merchant_id, ':cat_id'=>$id ));				
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));		
				Yii::app()->end();
			}															
			$model->category_name = CHtml::decode($model->category_name);
		} else $model=new AR_category;
		
		$model->multi_language = $multi_language;

		if(isset($_POST['AR_category'])){						
			$model->attributes=$_POST['AR_category'];			
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
								
				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->photo = $_POST['photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->photo = '';
				} else $model->photo = '';

				if(isset($_POST['icon'])){
					if(!empty($_POST['icon'])){
						$model->icon = $_POST['icon'];
						$model->icon_path = isset($_POST['icon_path'])?$_POST['icon_path']:$upload_path;
					} else $model->icon = '';
				} else $model->icon = '';
								
				if($model->save()){
					if(!$update){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/category_update', 'id'=>$model->cat_id ));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data  = array();
		if($update && !isset($_POST['AR_category'])){
			$translation = AttributesTools::GetFromTranslation($id,'{{category}}',
			'{{category_translation}}',
			'cat_id',
			array('cat_id','category_name','category_description'),
			array( 
			  'category_name'=>'category_translation',
			  'category_description'=>'category_description_translation'
			)
			);		
								
			$data['category_translation'] = isset($translation['category_name'])?$translation['category_name']:'';
			$data['category_description_translation'] = isset($translation['category_description'])?$translation['category_description']:'';
			
			$find = AR_category_relationship_dish::model()->findAll(
			    'cat_id=:cat_id',
			    array(':cat_id'=> intval($model->cat_id) )
			);
			if($find){
				$dish_selected = array();
				foreach ($find as $items) {					
					$dish_selected[]=$items->dish_id;
				}
				$model->dish_selected = $dish_selected;							
			}			
		}
				
			
		$fields[]=array(
		  'name'=>'category_translation',
		  'placeholder'=>"Enter [lang] Name here",
		  'type'=>"text"
		);
		$fields[]=array(
		  'name'=>'category_description_translation',
		  'placeholder'=>"Enter [lang] description here",
		  'type'=>"textarea"
		);

		$model->status = $model->isNewRecord?'publish':$model->status;	
					
		$params_model = array(
		    'model'=>$model,	
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'ctr'=>Yii::app()->controller->id."/category_remove_image",
		    'dish'=>AttributesTools::Dish(),
		    'upload_path'=>$upload_path,
		    'links'=>array(
	            t("All Category")=>array(Yii::app()->controller->id.'/category'),        
                $this->pageTitle,
		    ),	    	
		);
		
		if($update){
			$this->render("//admin/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"category_create",
				'widget'=>'WidgetCategoryMenu',		
				'avatar'=>'',
				'params'=>$params_model			
			));
		} else {
			$this->render("category_create",$params_model);
		}
	}		
	
	public function actioncategory_update()
	{
		$this->actioncategory_create(true);
	}
	
	public function actioncategory_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
				
		$model = AR_category::model()->find('merchant_id=:merchant_id AND cat_id=:cat_id', 
		array(':merchant_id'=>$merchant_id, ':cat_id'=>$id ));
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/category'));			
		} else $this->render("error");
	}
	
	public function actioncategory_remove_image()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$page = Yii::app()->input->get('page');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_category::model()->find("merchant_id=:merchant_id AND cat_id=:cat_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':cat_id'=>$id
		));		
		if($model){
			$model->scenario="remove_image";
			$model->photo = '';		
			$model->save();
		}
		$this->redirect(array($page,'id'=>$id));			
	}
	
    public function actioncategory_availabilityOLD()
	{
		$this->pageTitle = t("Update Category");
		CommonUtility::setMenuActive('.food','.food_category');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		
		
		$id = (integer) Yii::app()->input->get('id');	
		$model = AR_category_availability::model()->findByPk( $id );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}												
				
		if(isset($_POST['AR_category_availability'])){
			$model->attributes=$_POST['AR_category_availability'];
			if($model->validate()){	
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
				
		$params_model = array(
		    'model'=>$model,
		    'days'=>AttributesTools::dayList(),
		    'links'=>array(
	            t("All Category")=>array(Yii::app()->controller->id.'/category'),        
                $this->pageTitle,
		    ),	    	
		);
		
		$this->render("//admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"//merchant/category_availability",
			'widget'=>'WidgetCategoryMenu',		
			'avatar'=>'',
			'params'=>$params_model			
		));
	}			
	
	public function actioncategory_availability()
	{
		$this->pageTitle = t("Availability");
		CommonUtility::setMenuActive('.food','.food_category');			
				
		$cat_id = intval(Yii::app()->input->get('id'));
		$model = AR_category::model()->find("merchant_id=:merchant_id AND cat_id=:cat_id",array(
		  ':merchant_id'=>Yii::app()->merchant->merchant_id,
		  ':cat_id'=>$cat_id
		));		
			
		if(!$model){				
			$this->render("//tpl/error",array('error'=>array('message'=>t("Category not found"))));
			Yii::app()->end();
		}		
		
		if(isset($_POST['AR_category'])){
			$model->attributes=$_POST['AR_category'];
			$model->scenario = 'availability';		
			if(isset($_POST['AR_category']['available_day'])){
				$model->available_day = $_POST['AR_category']['available_day'];
				$model->available_time_start = $_POST['AR_category']['available_time_start'];
				$model->available_time_end = $_POST['AR_category']['available_time_end'];
			}
			if($model->validate()){	
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data = AR_availability::getValue($model->merchant_id,'category',$model->cat_id);
				
		$params_model = array(
		    'model'=>$model,
		    'days'=>AttributesTools::dayWeekList(),
		    'data'=>(array)$data,
		    'links'=>array(
	            t("All Category")=>array(Yii::app()->controller->id.'/category'),        	            
	            CHtml::decode($model->category_name) =>array("/food/category_update",'id'=>$model->cat_id),        
                $this->pageTitle,
		    ),	    	
		);
		$this->render("//admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"//food/category_availability",
			'widget'=>'WidgetCategoryMenu',		
			'avatar'=>'',
			'params'=>$params_model			
		));
	}
	
	public function actionaddoncategory()
	{		
		$this->pageTitle=t("Addon Category List");
		$action_name='addoncategory_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/addoncategory_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//tpl/list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/addoncategory_create")
		));	
	}	
	
   public function actionaddoncategory_create($update=false)
   {
		$this->pageTitle = $update==false? t("Add Addon Category") : t("Update Addon Category");
		CommonUtility::setMenuActive('.food','.food_addoncategory');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		$upload_path = CMedia::merchantFolder();
		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');
						
			$model = AR_subcategory::model()->find('merchant_id=:merchant_id AND subcat_id=:subcat_id', 
		    array(':merchant_id'=>$merchant_id, ':subcat_id'=>$id ));			
		    
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));	
				Yii::app()->end();
			}												
		} else $model=new AR_subcategory;
		
		$model->multi_language = $multi_language;

		if(isset($_POST['AR_subcategory'])){
			$model->attributes=$_POST['AR_subcategory'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				
				
				if(isset($_POST['featured_image'])){
					if(!empty($_POST['featured_image'])){
						$model->featured_image = $_POST['featured_image'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->featured_image = '';
				} else $model->featured_image = '';		
								
				if($model->save()){
					if(!$update){						
					   $this->redirect(array(Yii::app()->controller->id.'/addoncategory'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data  = array();
		if($update && !isset($_POST['AR_subcategory'])){
			$translation = AttributesTools::GetFromTranslation($id,'{{subcategory}}',
			'{{subcategory_translation}}',
			'subcat_id',
			array('subcat_id','subcategory_name','subcategory_description'),
			array( 
			  'subcategory_name'=>'subcategory_translation',
			  'subcategory_description'=>'subcategory_description_translation'
			)
			);						
			$data['subcategory_translation'] = isset($translation['subcategory_name'])?$translation['subcategory_name']:'';			
			$data['subcategory_description_translation'] = isset($translation['subcategory_description'])?$translation['subcategory_description']:'';			
		}
			
		$fields[]=array(
		  'name'=>'subcategory_translation',
		  'placeholder'=>"Enter [lang] Name here"
		);
		$fields[]=array(
		  'name'=>'subcategory_description_translation',
		  'placeholder'=>"Enter [lang] description here",
		  'type'=>"textarea"
		);

		$model->status = $model->isNewRecord?'publish':$model->status;	
				
		$params_model = array(
		    'model'=>$model,	
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,
		    'ctr'=>Yii::app()->controller->id."/addoncategory_remove_image",
		    'status'=>(array)AttributesTools::StatusManagement('post'),		    
		    'upload_path'=>$upload_path,
		    'links'=>array(
	            t("All Addon Category")=>array(Yii::app()->controller->id.'/addoncategory'),        
                $this->pageTitle,
		    ),	    	
		);
		
		$this->render("addoncategory_create",$params_model);
	}		
	
	public function actionaddoncategory_update()
	{
		$this->actionaddoncategory_create(true);
	}
	
	public function actionaddoncategory_remove_image()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$page = Yii::app()->input->get('page');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_subcategory::model()->find("merchant_id=:merchant_id AND subcat_id=:subcat_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':subcat_id'=>$id
		));		
		if($model){
			$model->scenario="remove_image";
			$model->featured_image = '';		
			$model->save();
		}
		$this->redirect(array($page,'id'=>$id));	
	}
	
	public function actionaddoncategory_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
				
		$model = AR_subcategory::model()->find('merchant_id=:merchant_id AND subcat_id=:subcat_id', 
		array(':merchant_id'=>$merchant_id, ':subcat_id'=>$id ));
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/addoncategory'));			
		} else $this->render("error");
	}
	
	public function actionaddonitem()
	{		
		$this->pageTitle=t("Addon Item List");
		$action_name='addonitem_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/addonitem_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = 'addonitem_list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/addonitem_create")
		));	
	}	
	
	public function actionaddonitem_create($update=false)
    {
		$this->pageTitle = $update==false? t("Add Addon Item") : t("Update Addon Item");
		CommonUtility::setMenuActive('.food','.food_addonitem');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		$upload_path = CMedia::merchantFolder();
		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');				
			$model = AR_subcategory_item::model()->find('merchant_id=:merchant_id AND sub_item_id=:sub_item_id', 
		    array(':merchant_id'=>$merchant_id, ':sub_item_id'=>$id ));		
		    
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));		
				Yii::app()->end();
			}					
		} else $model=new AR_subcategory_item;
		
		$model->multi_language = $multi_language;		

		if(isset($_POST['AR_subcategory_item'])){
			$model->attributes=$_POST['AR_subcategory_item'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				
				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->photo = $_POST['photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->photo = '';
				} else $model->photo = '';
								
				if($model->save()){
					if(!$update){						
					   $this->redirect(array(Yii::app()->controller->id.'/addonitem'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data  = array();
		if($update && !isset($_POST['AR_subcategory_item'])){
			$translation = AttributesTools::GetFromTranslation($id,'{{subcategory_item}}',
			'{{subcategory_item_translation}}',
			'sub_item_id',
			array('sub_item_id','sub_item_name','item_description'),
			array( 
			  'sub_item_name'=>'sub_item_name_translation',
			  'item_description'=>'item_description_translation'
			)
			);						
			$data['sub_item_name_translation'] = isset($translation['sub_item_name'])?$translation['sub_item_name']:'';			
			$data['item_description_translation'] = isset($translation['item_description'])?$translation['item_description']:'';			
			
			$find = AR_subcategory_item_relationships::model()->findAll(
			    'sub_item_id=:sub_item_id',
			    array(':sub_item_id'=> intval($model->sub_item_id) )
			);
			if($find){
				$selected = array();
				foreach ($find as $items) {					
					$selected[]=$items->subcat_id;
				}
				$model->category_selected = $selected;
			}		
			
		}
			
		$fields[]=array(
		  'name'=>'sub_item_name_translation',
		  'placeholder'=>"Enter [lang] Name here"
		);
		$fields[]=array(
		  'name'=>'item_description_translation',
		  'placeholder'=>"Enter [lang] description here",
		  'type'=>"textarea"
		);

		$model->status = $model->isNewRecord?'publish':$model->status;	
				
		$params_model = array(
		    'model'=>$model,	
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,
		    'ctr'=>Yii::app()->controller->id."/addonitem_remove_image",
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'addon_category'=>AttributesTools::Subcategory( $merchant_id ),
		    'upload_path'=>$upload_path,
		    'links'=>array(
	            t("All Addon Item")=>array(Yii::app()->controller->id.'/addonitem'),        
                $this->pageTitle,
		    ),	    	
		);
		
		$this->render("addonitem_create",$params_model);
	}	
	
	public function actionaddonitem_update()
	{
		$this->actionaddonitem_create(true);
	}

	public function actionaddonitem_delete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$model = AR_subcategory_item::model()->find('merchant_id=:merchant_id AND sub_item_id=:sub_item_id', 
		array(':merchant_id'=>$merchant_id, ':sub_item_id'=>$id ));

		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/addonitem'));			
		} else $this->render("error");
	}
	
    public function actionaddonitem_remove_image()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$page = Yii::app()->input->get('page');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_subcategory_item::model()->find("merchant_id=:merchant_id AND sub_item_id=:sub_item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':sub_item_id'=>$id
		));				
		if($model){
			$model->scenario="remove_image";
			$model->photo = '';		
			$model->save();						
		}
		$this->redirect(array($page,'id'=>$id));	
	}
	
	public function actionitemsOLD()
	{		
		$this->pageTitle=t("Item List");
		$action_name='item_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/item_delete");
		
		$model = AR_item::model()->findAll("merchant_id=:merchant_id AND slug=:slug",[
			':merchant_id'=>Yii::app()->merchant->id,
			':slug'=>''
		]);
		if($model){
			foreach ($model as $items) {				
				$model2=AR_item::model()->findByPk($items->item_id);				
				$model2->slug = CommonUtility::createSlug(CommonUtility::toSeoURL($model2->item_name),'{{item}}');				
				$model2->scenario = 'update_slug';
				$model2->save();				
			}
		}				
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		$tpl = 'item_list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/item_create")
		));	
   }	

   public function actionitems()
   {
		$this->pageTitle=t("Item list");

		$table_col = array(
			'item_id'=>array(
				'label'=>t("#"),
				'width'=>'10%'
			),
			'available'=>array(
				'label'=>t("Available"),
				'width'=>'15%'
			),
			'item_name'=>array(
				'label'=>t("Name"),
				'width'=>'30%'
			),
			'category_group'=>array(
				'label'=>t("Category"),
				'width'=>'15%'
			),
			'price'=>array(
				'label'=>t("Price"),
				'width'=>'15%'
			),
			'action'=>array(
				'label'=>t("Actions"),
				'width'=>'15%'
			)	
		);
		$columns = array(
		array('data'=>'item_id'),
		array('data'=>'available'),
		array('data'=>'item_name'),
		array('data'=>'category_group','orderable'=>false),
		array('data'=>'price','orderable'=>false),		
		array('data'=>null,'orderable'=>false,
			'defaultContent'=>'
			<div class="btn-group btn-group-actions" role="group">
				<a class="ref_edit normal btn btn-light tool_tips"><i class="zmdi zmdi-border-color"></i></a>
				<a class="ref_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>
			</div>
			'
		),	  
		);				
				
		$this->render('//food/item_list_new',array(
		'table_col'=>$table_col,
		'columns'=>$columns,
		'order_col'=>0,
		'sortby'=>'desc',
		'transaction_type'=>array(),		  
		'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/item_create")
		));
   }

		
   public function actionitem_create($update=false)
   {
		$this->pageTitle = $update==false? t("Add Item") : t("Update Item");
		CommonUtility::setMenuActive('.food','.food_items');			
		
		$multi_language = CommonUtility::MultiLanguage();
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		$upload_path = CMedia::merchantFolder();
				
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		if($update){				
			
			$model = AR_item::model()->find('merchant_id=:merchant_id AND item_id=:item_id', 
		    array(':merchant_id'=>$merchant_id, ':item_id'=>$item_id ));	
		    
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));		
				Yii::app()->end();
			}										
			$model->scenario = 'update';									
		} else {
			$model=new AR_item;
			$model->scenario = 'create';
		}
		
		$model->multi_language = $multi_language;		

		if(isset($_POST['AR_item'])){
			$model->attributes=$_POST['AR_item'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				
				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->photo = $_POST['photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->photo = '';
				} else $model->photo = '';
										
				if($model->save()){
					if(!$update){						
					   Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/item_update', 'item_id'=>$model->item_id ));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$data  = array();
		if($update && !isset($_POST['AR_item'])){
			$translation = AttributesTools::GetFromTranslation($item_id,'{{item}}',
			'{{item_translation}}',
			'item_id',
			array('item_id','item_name','item_description'),
			array( 
			  'item_name'=>'item_name_translation',
			  'item_description'=>'item_description_translation'
			)
			);			
				
			$data['item_name_translation'] = isset($translation['item_name'])?$translation['item_name']:'';			
			$data['item_description_translation'] = isset($translation['item_description'])?$translation['item_description']:'';		
			
			$meta = AR_item_meta::model()->findAll("merchant_id=:merchant_id AND item_id=:item_id 
			AND meta_name=:meta_name ",array(
			  ':merchant_id'=>$merchant_id,
			  ':item_id'=>$item_id,
			  ':meta_name'=>"item_featured"
			));		
			$item_featured = array();
			if($meta){
				foreach ($meta as $meta_val) {					
					$item_featured[]=$meta_val->meta_id;
				}
				$model->item_featured = $item_featured;		
			}	
			
			$find = AR_item_relationship_category::model()->findAll(
			    'item_id=:item_id',
			    array(':item_id'=> intval($model->item_id) )
			);
			if($find){
				$selected = array();
				foreach ($find as $items) {					
					$selected[]=$items->cat_id;
				}
				$model->category_selected = $selected;
			}		
				
		}
		
		$fields[]=array(
		  'name'=>'item_name_translation',
		  'placeholder'=>"Enter [lang] Name here"
		);
		$fields[]=array(
		  'name'=>'item_description_translation',
		  'placeholder'=>"Enter [lang] description here",
		  'type'=>"textarea"
		);

		$model->status = $model->isNewRecord?'publish':$model->status;	
				
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
		
				
		if($update){
			$links = array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	            $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    );
		} else {
			$links = array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),                
	            $this->pageTitle,
		    );
		}
				
		$params_model = array(
		    'model'=>$model,	
		    'multi_language'=>$multi_language,
		    'language'=>AttributesTools::getLanguage(),
		    'fields'=>$fields,
		    'data'=>$data,		    
		    'ctr'=>Yii::app()->controller->id."/item_remove_image",
		    'status'=>(array)AttributesTools::StatusManagement('post'),		
		    'category'=>(array)AttributesTools::Category( $merchant_id ),
		    'units'=> (array) AttributesTools::Size( $merchant_id ),
		    'discount_type'=> AttributesTools::CommissionType(),
		    'links'=>$links,
		    'item_featured'=>AttributesTools::ItemFeatured(),
		    'upload_path'=>$upload_path,
		);
		
		if($update){
			$menu = new WidgetItemMenu;
            $menu->init();    
			$this->render("//tpl/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"//food/item_create",
				'widget'=>'WidgetItemMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu
			));
		} else $this->render("item_create",$params_model);		
	}		
	
	public function actionitem_update()
	{
		$this->actionitem_create(true);
	}
	
	public function actionitem_delete()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_item::model()->find('merchant_id=:merchant_id AND item_id=:item_id', 
		array(':item_id'=>$id, ':merchant_id'=>$merchant_id ));
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/items'));			
		} else $this->render("//tpl/error",[
			'error'=>[
				'message'=>t("Record not found")
			]
		]);
	}
		
	public function actionitem_remove_image()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$page = Yii::app()->input->get('page');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$model = AR_item::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$id
		));		
		if($model){			
			$model->scenario="remove_image";
			$model->photo = '';					
			$model->save();
		}
		$this->redirect(array(Yii::app()->controller->id.'/item_update','item_id'=>$id));			
	}
	
	public function actionitem_price()
	{
		
		$this->pageTitle=t("Item Price");
		CommonUtility::setMenuActive('.food','.food_items');		
		
		$item_id = (integer) Yii::app()->input->get('item_id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;

		$action_name='itemprice_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/itemprice_delete",array('item_id'=>$item_id));
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_custom_link='$delete_link';",
		),'action_name');
		
		$model = AR_item::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
		
		if(!$model){				
			$this->render("//tpl/error");				
			Yii::app()->end();
		}				
		
		$params_model = array(		
		    'model'=>$model,
		    'item_id'=>$item_id,
		    'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/itemprice_create",array('item_id'=>$item_id)),
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),   
	             $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),             
                $this->pageTitle,
		    ),	    	
		);	
		
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list_item';
			$menu = new WidgetItemMenu;
            $menu->init();    
		} else $tpl = '//food/itemprice_list';

		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>$tpl,
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu			
		));
	}
	
	public function actionitemprice_create($update=false)
	{		
		$this->pageTitle = $update==false? t("Add Price") : t("Update Price");
		CommonUtility::setMenuActive('.food','.food_items');
		CommonUtility::setSubMenuActive(".item-menu",'.item_price');
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		$item = AR_item::model()->findByPk( $item_id );		
		if(!$item){				
			$this->render("//tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}													
				
		if($update){			
			$id = (integer) Yii::app()->input->get('id');	
						
			$model = AR_item_size::model()->find('merchant_id=:merchant_id AND item_size_id=:item_size_id', 
		    array(':merchant_id'=>$merchant_id, ':item_size_id'=>$id ));			
		    			
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
				));						
				Yii::app()->end();
			}												
		} else $model=new AR_item_size;
		
		$model->scenario = 'add_price';
		
		if(isset($_POST['AR_item_size'])){
			$model->attributes=$_POST['AR_item_size'];
			if($model->validate()){		
				
				$model->merchant_id = (integer) $merchant_id;
				$model->item_id = (integer) $item_id;
				$model->price = (float) $model->price;
				$model->cost_price = (float) $model->cost_price;
				$model->discount = (float) $model->discount;
											
				if($model->save()){
					if(!$update){						
					   Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/item_price', 'item_id'=>$model->item_id ));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
				
		$model->price = !empty($model->price)? Price_Formatter::formatNumberNoSymbol( $model->price ):'';
		$model->cost_price = !empty($model->cost_price)? Price_Formatter::formatNumberNoSymbol( $model->cost_price ):'';
		$model->discount = !empty($model->discount)? Price_Formatter::formatNumberNoSymbol( $model->discount ):'';
		
		$model->price = $model->price>0?$model->price:'';
		$model->cost_price = $model->cost_price>0?$model->cost_price:'';
		$model->discount = $model->discount>0?$model->discount:'';

		$params_model = array(		
		    'model'=>$model,		    
		    'units'=> (array) AttributesTools::Size( $merchant_id ),
		    'discount_type'=> AttributesTools::CommissionType(),
		    'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/itemprice_create",array('item_id'=>$item_id)),
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	            $item->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$item->item_id),             
                $this->pageTitle,
		    ),	    	
		    'sub_link'=>array(
		        t("All Item")=>array(Yii::app()->controller->id.'/item_price','item_id'=>$item->item_id),  
                $this->pageTitle,
		    )
		);	
		
		
		$avatar = CMedia::getImage($item->photo,$item->path,Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('item'));
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
		   $menu = new WidgetItemMenu;
           $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//food/itemprice_create",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu		
		));		
	}
	
	public function actionitemprice_update()
	{
		$this->actionitemprice_create(true);
	}
	
	public function actionitemprice_delete()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$item_id = (integer) Yii::app()->input->get('item_id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$model = AR_item_size::model()->find('merchant_id=:merchant_id AND item_size_id=:item_size_id', 
		array(':item_size_id'=>$id, ':merchant_id'=>$merchant_id ));
				
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/item_price','item_id'=>$item_id ));			
		} else $this->render("error");
	}
	
	public function actionitem_inventory()
	{
	    $this->pageTitle = t("Item inventory");
		CommonUtility::setMenuActive('.food','.food_items');		
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
				
		$model = AR_item::model()->find('merchant_id=:merchant_id AND item_id=:item_id', 
		    array(':merchant_id'=>$merchant_id, ':item_id'=>$item_id ));		
		    
		if(!$model){				
			$this->render("//tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}													
		
		$model->scenario = 'item_inventory';
		
		if(isset($_POST['AR_item'])){
			$model->attributes=$_POST['AR_item'];
			if($model->validate()){																	
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {				
				Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
			}
		}
		
		$params_model = array(		
		    'model'=>$model,		
		    'supplier'=>AttributesTools::Supplier($merchant_id),
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),       
	             $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),         
                $this->pageTitle,
		    ),	    	
		);	
		
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
				
        $menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//food/item_inventory",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu
		));		
	}
	

	public function actionitem_attributes()
	{
	    $this->pageTitle = t("Item attributes");
		CommonUtility::setMenuActive('.food','.food_items');		
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		
		$model = AR_item_attributes::model()->find('merchant_id=:merchant_id AND item_id=:item_id', 
		array(':merchant_id'=>$merchant_id, ':item_id'=>$item_id ));			
		    
		if(!$model){				
			$this->render("/tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}		

		$model->scenario = "item_attributes";	
		
		if(isset($_POST['AR_item_attributes'])){
			$model->attributes=$_POST['AR_item_attributes'];
			if($model->validate()){																	
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {				
				Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
			}
		}
			
		
		if(!isset($_POST['AR_item_attributes'])){
			/*COOKING REF*/
			$find = AR_item_meta::model()->findAll(
			    'item_id=:item_id AND merchant_id=:merchant_id AND meta_name=:meta_name',
			    array(  
			       ':item_id'=> intval($model->item_id),
			       ':merchant_id'=> intval($merchant_id),
			       ':meta_name'=>"cooking_ref"
			    )
			);
			if($find){
				$selected = array();
				foreach ($find as $items) {					
					$selected[]=$items->meta_id;
				}
				$model->cooking_selected = $selected;
			}		
			
			/*INGREDIENTS*/
			$find = AR_item_meta::model()->findAll(
			    'item_id=:item_id AND merchant_id=:merchant_id AND meta_name=:meta_name',
			    array(  
			       ':item_id'=> intval($model->item_id),
			       ':merchant_id'=> intval($merchant_id),
			       ':meta_name'=>"ingredients"
			    )
			);
			if($find){
				$selected = array();
				foreach ($find as $items) {					
					$selected[]=$items->meta_id;
				}
				$model->ingredients_selected = $selected;
			}
			
			/*DISH*/
			$find = AR_item_meta::model()->findAll(
			    'item_id=:item_id AND merchant_id=:merchant_id AND meta_name=:meta_name',
			    array(  
			       ':item_id'=> intval($model->item_id),
			       ':merchant_id'=> intval($merchant_id),
			       ':meta_name'=>"dish"
			    )
			);
			if($find){
				$selected = array();
				foreach ($find as $items) {					
					$selected[]=$items->meta_id;
				}
				$model->dish_selected = $selected;
			}
			
			/*DELIVERY VEHICLE*/
			$find = AR_item_meta::model()->findAll(
			    'item_id=:item_id AND merchant_id=:merchant_id AND meta_name=:meta_name',
			    array(  
			       ':item_id'=> intval($model->item_id),
			       ':merchant_id'=> intval($merchant_id),
			       ':meta_name'=>"delivery_options"
			    )
			);
			if($find){
				$selected = array();
				foreach ($find as $items) {					
					$selected[]=$items->meta_id;
				}
				$model->delivery_options_selected = $selected;
			}
		}
		
		
		$model->points_earned = !empty($model->points_earned)? Price_Formatter::formatNumberNoSymbol( $model->points_earned ):'';
		$model->packaging_fee = !empty($model->packaging_fee)? Price_Formatter::formatNumberNoSymbol( $model->packaging_fee ):'';
		
		$model->points_earned = $model->points_earned>0?$model->points_earned:'';
		$model->packaging_fee = $model->packaging_fee>0?$model->packaging_fee:'';
		
		$params_model = array(		
		    'model'=>$model,		   
		    'cooking_ref'=>(array)AttributesTools::Cooking($merchant_id),
		    'ingredients'=>(array)AttributesTools::Ingredients($merchant_id),
		    'dish'=>AttributesTools::Dish(),
		    'transport'=>AttributesTools::transportType(),
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'), 
	             $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    ),	    	
		);	
		
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));		
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//food/item_attributes",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu			
		));		
	}	
	
	public function actionitem_availability()
	{
		$this->pageTitle = t("availability");
		CommonUtility::setMenuActive('.food','.food_items');		
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		
		$model = AR_item_attributes::model()->find('merchant_id=:merchant_id AND item_id=:item_id', 
		array(':merchant_id'=>$merchant_id, ':item_id'=>$item_id ));			
		    
		if(!$model){				
			$this->render("/tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}		
				
		$model->scenario = 'availability';		
		
		if(isset($_POST['AR_item_attributes'])){
			$model->attributes=$_POST['AR_item_attributes'];					
			if(isset($_POST['AR_item_attributes']['available_day'])){
				$model->available_day = $_POST['AR_item_attributes']['available_day'];
				$model->available_time_start = $_POST['AR_item_attributes']['available_time_start'];
				$model->available_time_end = $_POST['AR_item_attributes']['available_time_end'];
			}
			if($model->validate()){	
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
				
		$data = AR_availability::getValue($model->merchant_id,'item',$model->item_id);
				
		$params_model = array(		
		    'model'=>$model,		
		    'days'=>AttributesTools::dayWeekList(),   		    
		    'data'=>$data,
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'), 
	             $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    ),	    	
		);	
		
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));		
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//food/item_availability",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu			
		));		
	}
	
	public function actionitem_tax()
	{
		$this->pageTitle = t("Item Tax");
		CommonUtility::setMenuActive('.food','.food_items');		
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		
		$model = AR_item_attributes::model()->find('merchant_id=:merchant_id AND item_id=:item_id', 
		array(':merchant_id'=>$merchant_id, ':item_id'=>$item_id ));			
		    
		if(!$model){				
			$this->render("/tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}		
				
		
		$models = new AR_item_meta;		
		$models->scenario = 'tax';		
						
		if(isset($_POST['AR_item_meta'])){				
			$tax  = isset($_POST['AR_item_meta'])?$_POST['AR_item_meta']['merchant_tax']:'';	
						
			AR_item_meta::model()->deleteAll('merchant_id=:merchant_id AND item_id=:item_id AND meta_name=:meta_name',array(
			   ':merchant_id'=>intval($merchant_id),
			   ':item_id'=>intval($item_id),
			   ':meta_name'=>'tax'
			));
					
			if(is_array($tax) && count($tax)>=1){
				foreach ($tax as $val) {
					$models = new AR_item_meta;
					$models->merchant_id = $merchant_id;
					$models->item_id = $item_id;
					$models->meta_name = 'tax';
					$models->meta_id = intval($val);
					$models->save();					
				}				
			}
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
			$this->refresh();
		} elseif ( isset($_POST['yt0'])){
			AR_item_meta::model()->deleteAll('merchant_id=:merchant_id AND item_id=:item_id AND meta_name=:meta_name',array(
			   ':merchant_id'=>intval($merchant_id),
			   ':item_id'=>intval($item_id),
			   ':meta_name'=>'tax'
			));
		}
				
		
		$tax_menu_settings = Yii::app()->params['tax_menu_settings'];		
		$tax_type = isset($tax_menu_settings['tax_type'])?$tax_menu_settings['tax_type']:'';
							
		if($tax_menu_settings['tax_enabled']==false || $tax_menu_settings['tax_type']!="multiple"){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("This page is not available.")
			 )
			));
			return ;
		}
				
		$tax_list = CTax::taxList($merchant_id,$tax_type);
						
		$data = CommonUtility::getDataToDropDown("{{item_meta}}",'id','meta_id',
		"WHERE merchant_id=".q($merchant_id)." AND item_id=".q($item_id)." AND meta_name='tax' ");
		$models->merchant_tax = $data;
		
		$params_model = array(		
		    'model'=>$models,	
		    'tax_type'=>$tax_type,		
		    'tax_list'=>$tax_list,
		    'data'=>$data,
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'), 
	             $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    ),	    	
		);	
		
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));		
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
				
							
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//food/item_tax",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu			
		));		
	}
	
	public function actionitem_gallery()
	{
	    $this->pageTitle = t("Item gallery");
		CommonUtility::setMenuActive('.food','.food_items');		
					
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		$upload_path = CMedia::merchantFolder();
		
		$upload_ajaxurl = Yii::app()->createUrl("/upload");
		$upload_params = array(
		  Yii::app()->request->csrfTokenName=>Yii::app()->request->csrfToken,
		  'item_id'=>$item_id
		);		
		$upload_params = json_encode($upload_params);
		
		ScriptUtility::registerScript(array(
		  "var upload_ajaxurl='$upload_ajaxurl';",		  
		  "var upload_params='$upload_params';",		  
		),'upload_ajaxurl');
				
				
		$model = AR_item_attributes::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
		if(!$model){				
			$this->render("/tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}						
		
		if(isset($_POST['yt0'])){	
									
			if(DEMO_MODE && in_array($merchant_id,DEMO_MERCHANT)){		
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
		
			
			AR_item_meta::model()->deleteAll('merchant_id=:merchant_id 
			AND item_id=:item_id
			AND meta_name=:meta_name', 
			 array(
			    ':merchant_id' => $merchant_id,
			    ':item_id'=>$item_id,
			    ':meta_name'=>"item_gallery",
			));
			
			if(isset($_POST['item_gallery'])){	
				$params = array();				
				foreach ($_POST['item_gallery'] as $key=> $items) {
					$params[]=array(
					  'merchant_id'=>$merchant_id,
					  'item_id'=>$item_id,
					  'meta_name'=>"item_gallery",
					  'meta_id'=>$items,
					  'meta_value'=>$_POST['path'][$key]
					);				
				}		
				$builder=Yii::app()->db->schema->commandBuilder;
				$command=$builder->createMultipleInsertCommand('{{item_meta}}',$params);
				$command->execute();	
			}
			
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
			$this->refresh();		
		}
		
		$item_gallery = array();
		$meta = AR_item_meta::model()->findAll("merchant_id=:merchant_id AND item_id=:item_id 
		AND meta_name=:meta_name ",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id,
		  ':meta_name'=>"item_gallery"
		));
		if($meta){
			foreach ($meta as $item) {				
				$item_gallery[] = $item->meta_id;
			}			
		}
				
		$params_model = array(		
		    'model'=>$model,	
		    'item_gallery'=>$item_gallery,	    		    		   
		    'upload_path'=>$upload_path,
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	             $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    ),	    	
		);	
		
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
		
        $menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"item_gallery",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu			
		));		
	}		
	
	public function actionitem_gallery_remove()
	{		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		$id = (integer) Yii::app()->input->get('id');	
		
		$model = AR_item_meta::model()->find('merchant_id=:merchant_id AND id=:id', 
		array(':merchant_id'=>$merchant_id, ':id'=>$id ));
		
		$model->scenario = "item_gallery";
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/item_gallery','item_id'=>$item_id));			
		} else $this->render("error");
	}
	
	public function actionitem_seo()
	{
	    $this->pageTitle = t("Item SEO");
		CommonUtility::setMenuActive('.food','.food_items');		
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		$upload_path = CMedia::merchantFolder();
				
		$model = AR_item_seo::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));						
		if(!$model){				
			$this->render("/tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}													
		
		if(isset($_POST['AR_item_seo'])){
			$model->attributes=$_POST['AR_item_seo'];
			
			/*$model->image=CUploadedFile::getInstance($model,'image');
				if($model->image){						
					$model->meta_image = CommonUtility::uploadNewFilename($model->image->name);					
					$path = CommonUtility::uploadDestination('')."/".$model->meta_image;								
					$model->image->saveAs( $path );
				}	*/
			
			if(isset($_POST['meta_image'])){
				if(!empty($_POST['meta_image'])){
					$model->meta_image = $_POST['meta_image'];
					$model->meta_image_path = isset($_POST['path'])?$_POST['path']:$upload_path;
				} else $model->meta_image = '';
			} else $model->meta_image = '';
			
			if($model->validate()){	
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		
		$params_model = array(		
		    'model'=>$model,	
		    'upload_path'=>$upload_path,	    		    		   
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	            $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    ),	    	
		);	
				
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
			
        $menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"item_seo",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu		
		));		
	}			
	
	public function actionitem_remove_seoimage()
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');					
		$model = AR_item_seo::model()->find('merchant_id=:merchant_id AND item_id=:item_id', 
		array(':merchant_id'=>$merchant_id, ':item_id'=>$item_id ));
		
		if($model){					
			$model->meta_image = '';
			$model->save();			
			$this->redirect(array(Yii::app()->controller->id.'/item_seo','item_id'=>$item_id));			
		} else $this->render("error");
	}
	
	public function actionitem_addon()
	{
		$this->pageTitle = t("Item addon");
		CommonUtility::setMenuActive('.food','.food_items');		
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		$action_name='itemaddon_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/itemaddon_delete",array('item_id'=>$item_id));
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_custom_link='$delete_link';",
		),'action_name');
		
		$model = AR_item::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
		
		if(!$model){				
			$this->render("//tpl/error");				
			Yii::app()->end();
		}						
		
		$params_model = array(		
		    'model'=>$model,	
		    'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/itemaddon_create",array('item_id'=>$model->item_id) ),	    		    		   
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	            $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    ),	
		);	
				
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list_item';
			$menu = new WidgetItemMenu;
            $menu->init();    
		} else $tpl = '//food/itemaddon_list';
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>$tpl,
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu
		));		
	}
	
	public function actionitemaddon_create($update=false)
	{
		$this->pageTitle = t("Item addon");
		CommonUtility::setMenuActive('.food','.food_items');
		CommonUtility::setSubMenuActive(".item-menu",'.item_addon');	
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		$item = AR_item::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
		
		if(!$item){				
			$this->render("/tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}				
		
		if($update){			
			$id = (integer) Yii::app()->input->get('id');				
			$model = AR_item_addon::model()->find('merchant_id=:merchant_id AND item_id=:item_id AND id=:id', 
		    array(':merchant_id'=>$merchant_id, ':item_id'=>$item_id, ':id'=>$id ));
		    
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
				));						
				Yii::app()->end();
			}			

			if($model->multi_option=="two_flavor"){
				$model->multi_option_value_selection = $model->multi_option_value;
			} elseif ( $model->multi_option=="custom" ){
				$model->multi_option_value_text = $model->multi_option_value;
			}
					
		} else $model = new AR_item_addon;
		
		$model->merchantid = $merchant_id;
		$model->itemid = $item_id;
		
		if(isset($_POST['AR_item_addon'])){
			$model->attributes=$_POST['AR_item_addon'];
			
			if($model->validate()){		
				
				$model->merchant_id = (integer) $merchant_id;
				$model->item_id = (integer) $item_id;				
														
				if($model->save()){
					if(!$update){						
					   Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/item_addon', 'item_id'=>$model->item_id ));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
						
		$params_model = array(		
		    'model'=>$model,		    
		    'addon_caregory_list'=>AttributesTools::Subcategory( $merchant_id ),
		    'multi_option'=>AttributesTools::MultiOption(),
		    'two_flavor_properties'=>AttributesTools::TwoFlavor(),
		    'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/itemprice_create",array('item_id'=>$item_id)),
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	            $item->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$item->item_id),        
                $this->pageTitle,
		    ),	   
		     'sub_link'=>array(
		        t("All Addon")=>array(Yii::app()->controller->id.'/item_addon','item_id'=>$item->item_id),  
                $this->pageTitle,
		    ),
		    'size_list'=>AttributesTools::ItemSize($merchant_id,$item->item_id)
		);	
		
					
		$avatar = CMedia::getImage($item->photo,$item->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"itemaddon_create",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu
		));		
		
	}
	
	public function actionitemaddon_update()
	{
		$this->actionitemaddon_create(true);
	}
	
	public function actionitemaddon_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
				
		$model = AR_item_addon::model()->find('merchant_id=:merchant_id AND id=:id', 
		array(':merchant_id'=>$merchant_id, ':id'=>$id ));
		
		$model->merchantid = $merchant_id;
		$model->itemid = $item_id;
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/item_addon','item_id'=>$item_id));			
		} else $this->render("error");
	}
	
	public function actionitem_promos()
	{
		$this->pageTitle = t("Sales Promotion");
		CommonUtility::setMenuActive('.food','.food_items');		
				
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		
		$action_name='item_promo';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/itempromo_delete",array('item_id'=>$item_id));
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_custom_link='$delete_link';",
		),'action_name');
		
		$model = AR_item::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
		
		if(!$model){				
			$this->render("//tpl/error");				
			Yii::app()->end();
		}						
		
		$params_model = array(		
		    'model'=>$model,	
		    'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/itempromo_create",array('item_id'=>$model->item_id) ),	    		    		   
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	            $model->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$model->item_id),        
                $this->pageTitle,
		    ),	
		);	
				
		$avatar = CMedia::getImage($model->photo,$model->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list_item';
			$menu = new WidgetItemMenu;
            $menu->init();    
		} else $tpl = '//food/itempromo_list';
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>$tpl,
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu			
		));		
	}		
	
	public function actionitempromo_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Item Promo") : t("Update Item Promo");
		CommonUtility::setMenuActive('.food','.food_items');
		CommonUtility::setSubMenuActive(".item-menu",'.item_promos');	
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$item_id = (integer) Yii::app()->input->get('item_id');	
		$selected_item = array();
		
		$item = AR_item::model()->find("merchant_id=:merchant_id AND item_id=:item_id",array(
		  ':merchant_id'=>$merchant_id,
		  ':item_id'=>$item_id
		));		
		
		if(!$item){				
			$this->render("/tpl/error",array(
			 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
			));						
			Yii::app()->end();
		}			
		
		if($update){			
			$id = (integer) Yii::app()->input->get('id');				
			$model = AR_item_promo::model()->find('merchant_id=:merchant_id AND promo_id=:promo_id', 
		    array(':merchant_id'=>$merchant_id, ':promo_id'=>$id ));
		    
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array( 'message'=>t(HELPER_RECORD_NOT_FOUND))
				));						
				Yii::app()->end();
			}					
			
			$selected_item = CommonUtility::getDataToDropDown("{{item}}",'item_id','item_name',
			"WHERE item_id=".q($model->item_id_promo)."");			
			
		} else $model = new AR_item_promo;
		
				
		if(isset($_POST['AR_item_promo'])){
			$model->attributes=$_POST['AR_item_promo'];
			
			if($model->validate()){		
			
					
				$model->merchant_id = (integer) $merchant_id;
				$model->item_id = (integer) $item_id;				
														
				if($model->save()){
					if(!$update){						
					   Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/item_promos', 'item_id'=>$model->item_id ));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$params_model = array(		
		    'model'=>$model,		    		    
		    'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/itempromo_create",array('item_id'=>$item_id)),
		    'links'=>array(
	            t("All Item")=>array(Yii::app()->controller->id.'/items'),        
	            $item->item_name=>array(Yii::app()->controller->id.'/item_update','item_id'=>$item->item_id),        
                $this->pageTitle,
		    ),	   
		     'sub_link'=>array(
		        t("All Promo")=>array(Yii::app()->controller->id.'/item_promos','item_id'=>$item->item_id),   
		        $this->pageTitle             
		    ),	
		    'promo_type'=>AttributesTools::ItemPromoType(),
		    'items'=>$selected_item
		);	
				
		$avatar = CMedia::getImage($item->photo,$item->path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('item'));
						
        $menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetItemMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"itempromo_create",
			'widget'=>'WidgetItemMenu',		
			'avatar'=>$avatar,
			'params'=>$params_model,
			'menu'=>$menu	
		));		
		
	}
	
	public function actionitempromo_update()
	{
	    $this->actionitempromo_create(true);		
	}
	
	public function actionitempromo_delete()
	{
	    $id = (integer) Yii::app()->input->get('id');	
	    $item_id = (integer) Yii::app()->input->get('item_id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
				
		$model = AR_item_promo::model()->find('merchant_id=:merchant_id AND promo_id=:promo_id', 
		array(':merchant_id'=>$merchant_id, ':promo_id'=>$id ));
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/item_promos','item_id'=>$item_id));			
		} else $this->render("error");
	}
	
}
/*end class*/