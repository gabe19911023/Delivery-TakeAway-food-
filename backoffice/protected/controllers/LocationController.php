<?php
class LocationController extends CommonController
{
	public $layout='backend';
		
	public function beforeAction($action)
	{							
		$ajaxurl = Yii::app()->createUrl("/backend");
		$is_mobile = Yii::app()->params['isMobile'];
		
		ScriptUtility::registerScript(array(
		  "var ajaxurl='$ajaxurl';",
		  "var is_mobile='$is_mobile';"
		),'admin_script');			
		return true;
	}
	
	public function actioncountry_list()
	{
		$this->pageTitle = t("Country List");
		$action_name='country_list';
		$delete_link = Yii::app()->CreateUrl("location/delete_country");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		$this->render("country_list",array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/country_create")
		));	
	}
	
	public function actioncountry_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Country") : t("Update Country");
		CommonUtility::setMenuActive('.attributes',".location_country_list");
		
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_country::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}				
		} else {			
			$model=new AR_country;				
		}

		if(isset($_POST['AR_country'])){
			$model->attributes=$_POST['AR_country'];
			if($model->validate()){
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/country_list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_save));
			}
		}
							
		$this->render("country_create",array(
		    'model'=>$model,			    
		    'links'=>array(
	            t("All Country")=>array(Yii::app()->controller->id.'/country_list'),        
                $this->pageTitle,
		    ),			    
		));		
	}
	
	public function actioncountry_update()
	{
		$this->actioncountry_create(true);
	}
	
	public function actiondelete_country()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_country::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/country_list'));			
		} else $this->render("error");
	}
	
	public function actionstate_list()
	{
		$this->pageTitle = t("State List");
		CommonUtility::setMenuActive('.attributes',".location_country_list");
		
		$country_id = (integer) Yii::app()->input->get('country_id');
		$model = AR_country::model()->findByPk( $country_id );
		if(!$model){
			$this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));				
		}		
		
		$action_name='state_list';
		$delete_link = Yii::app()->CreateUrl("location/state_delete/country_id/".$country_id);
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		/*$this->pageTitle = t("State List - [country_name]",array(
		 '[country_name]'=>$model->country_name
		));*/
						
		$this->render("/admin/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"state_list",
				'widget'=>'WidgetLocations',		
				'avatar'=>"",				
				'params'=>array(  
				   'model'=>$model,					   
				   'links'=>array(
				      t("All Country")=>array(Yii::app()->controller->id.'/country_list'),     
				      $model->country_name,   
                      $this->pageTitle,
				   ),
				   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/state_create",array(
				   'country_id'=>$country_id
				   )),
				 )
		   ));
		
	}	
	
	public function actionstate_delete()
	{		
		$id = (integer) Yii::app()->input->get('id');			
		$country_id = (integer) Yii::app()->input->get('country_id');			
		$model = AR_state::model()->findByPk( $id );		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/state_list','country_id'=>$country_id ));	
		} else $this->render("error");
	}
	
	public function actionstate_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add State") : t("Update State");
		CommonUtility::setMenuActive('.attributes',".location_country_list");
		CommonUtility::setSubMenuActive('.location-menu','.location-state');		
		
		$country_id = (integer) Yii::app()->input->get('country_id');	
		$country = AR_country::model()->findByPk( $country_id );
		if(!$country){
			$this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));		
		}
		
		$id='';
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_state::model()->findByPk( $id );				
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));			
			}				
		} else {			
			$model=new AR_state;				
		}
					
		if(isset($_POST['AR_state'])){
			$model->attributes=$_POST['AR_state'];
			if($model->validate()){
				
				$model->country_id = $country_id;
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/state_list','country_id'=>$country_id));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_save));
			}
		}
				
		$this->render("/admin/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"state_create",
				'widget'=>'WidgetLocations',		
				'avatar'=>"",				
				'params'=>array(  
				   'model'=>$model,				   
				   'links'=>array(
				     t("All Country")=>array(Yii::app()->controller->id.'/country_list'),        
	                 t("All State")=>array(Yii::app()->controller->id.'/state_list','country_id'=>$country_id),        
                     $this->pageTitle,
				   )
				 )
		 ));				
	}
	
	public function actionstate_update()
	{
		$this->actionstate_create(true);
	}
	
	public function actioncity_list()
	{
		$this->pageTitle = t("City List");
		CommonUtility::setMenuActive('.attributes',".location_country_list");
		
		$country_id = (integer) Yii::app()->input->get('country_id');
		$model = AR_country::model()->findByPk( $country_id );
		if(!$model){
			$this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));				
		}		
		
		$action_name='city_list';
		$delete_link = Yii::app()->CreateUrl("location/city_delete/country_id/".$country_id);
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
					
		$this->render("/admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"city_list",
			'widget'=>'WidgetLocations',		
			'avatar'=>"",				
			'params'=>array(  
			   'model'=>$model,				   
			   'links'=>array(
			      t("All Country")=>array(Yii::app()->controller->id.'/country_list'),        
			      $model->country_name,
                  $this->pageTitle,
			   ),
			   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/city_create",array(
			   'country_id'=>$country_id
			   )),
			 )
	   ));
	}
	
	public function actioncity_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add City") : t("Update City");
		CommonUtility::setMenuActive('.attributes',".location_country_list");
		CommonUtility::setSubMenuActive('.location-menu','.location-city');
		
		$country_id = (integer) Yii::app()->input->get('country_id');	
		$state_id = (integer) Yii::app()->input->get('state_id');	
		$country = AR_country::model()->findByPk( $country_id );
		if(!$country){
			$this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));		
			Yii::app()->end();			
		}
				
		$id='';
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_city::model()->findByPk( $id );				
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));			
			}				
		} else {			
			$model=new AR_city;				
		}
		
		if(isset($_POST['AR_city'])){
			$model->attributes=$_POST['AR_city'];
			if($model->validate()){
							
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/city_list','country_id'=>$country_id));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_save));
			}
		}
		
		$this->render("/admin/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"city_create",
				'widget'=>'WidgetLocations',		
				'avatar'=>"",				
				'params'=>array(  
				   'model'=>$model,					   
				   'links'=>array(
				      t("All Country")=>array(Yii::app()->controller->id.'/country_list'),        				      
				      t("All City")=>array(Yii::app()->controller->id.'/city_list','country_id'=>$country_id), 				      
                      $this->pageTitle,
				   ),				
				   'state_list'=>AttributesLocation::StateList($country_id)
				 )
		   ));
		
	}
	
	public function actioncity_update()
	{
		$this->actioncity_create(true);
	}
	
	public function actioncity_delete()
	{		
		$id = (integer) Yii::app()->input->get('id');			
		$country_id = (integer) Yii::app()->input->get('country_id');			
		$model = AR_city::model()->findByPk( $id );			
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/city_list','country_id'=>$country_id ));	
		} else $this->render("error");
	}
	
	public function actionarea_list()
	{
		$this->pageTitle = t("Area List");
		CommonUtility::setMenuActive('.attributes',".location_country_list");
		
		$country_id = (integer) Yii::app()->input->get('country_id');
		$model = AR_country::model()->findByPk( $country_id );
		if(!$model){
			$this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));				
		}		
		
		$action_name='area_list';
		$delete_link = Yii::app()->CreateUrl("location/area_delete/country_id/".$country_id);
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
					
		$this->render("/admin/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>"area_list",
			'widget'=>'WidgetLocations',		
			'avatar'=>"",				
			'params'=>array(  
			   'model'=>$model,				   
			   'links'=>array(
			      t("All Country")=>array(Yii::app()->controller->id.'/country_list'),        
			      $model->country_name,
                  $this->pageTitle,
			   ),
			   'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/area_create",array(
			   'country_id'=>$country_id
			   )),
			 )
	   ));
	}	
	
	public function actionarea_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Area") : t("Update Area");
		CommonUtility::setMenuActive('.attributes',".location_country_list");
		CommonUtility::setSubMenuActive('.location-menu','.location-area');
		
		$country_id = (integer) Yii::app()->input->get('country_id');	
		$state_id = (integer) Yii::app()->input->get('state_id');	
		$country = AR_country::model()->findByPk( $country_id );
		if(!$country){
			$this->render("/admin/error",array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));		
			Yii::app()->end();			
		}
				
		$id='';
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_area::model()->findByPk( $id );				
			if(!$model){				
				$this->render("/admin/error",array(
				 'error'=>array(
				   'message'=>t(HELPER_RECORD_NOT_FOUND)
				 )
				));			
			}				
		} else {			
			$model=new AR_area;				
		}
		
		if(isset($_POST['AR_area'])){
			$model->attributes=$_POST['AR_area'];
			if($model->validate()){
							
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/area_list','country_id'=>$country_id));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_save));
			}
		}
		
		$this->render("/admin/submenu_tpl",array(
			    'model'=>$model,
				'template_name'=>"area_create",
				'widget'=>'WidgetLocations',		
				'avatar'=>"",				
				'params'=>array(  
				   'model'=>$model,					   
				   'links'=>array(
				      t("All Country")=>array(Yii::app()->controller->id.'/country_list'),        				      
				      t("All Area")=>array(Yii::app()->controller->id.'/area_list','country_id'=>$country_id), 				      
                      $this->pageTitle,
				   ),				
				   'city_list'=>AttributesLocation::CityList($country_id)
				 )
		   ));
	}
	
	public function actionarea_update()
	{
		$this->actionarea_create(true);
	}
		
	public function actionarea_delete()
	{		
		$id = (integer) Yii::app()->input->get('id');			
		$country_id = (integer) Yii::app()->input->get('country_id');			
		$model = AR_area::model()->findByPk( $id );					
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/area_list','country_id'=>$country_id ));	
		} else $this->render("error");
	}
	
}
/*END CLASS*/
