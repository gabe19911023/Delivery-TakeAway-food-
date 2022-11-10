<?php
class PlansController extends CommonController
{
	public $layout='backend';
	
	
	public function beforeAction($action)
	{					
		InlineCSTools::registerStatusCSS();					
		return true;
	}
		
	public function actionList()
	{		
		$this->pageTitle=t("Plan list");
		$action_name='plan_list';
		$delete_link = Yii::app()->CreateUrl("plans/plan_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		$tpl = 'plans_list';
				
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl("plans/create")
		));	
	}	
			
	public function actioncreate($update=false)
	{
		$this->pageTitle = $update==true? t("Update") : t("Add");
		CommonUtility::setMenuActive('.membership',".plans_create");
		
		$id = (integer) Yii::app()->input->get('id');		
		$multi_language = CommonUtility::MultiLanguage();
				
		if($update){
			$model = AR_plans::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
		} else {
			$model=new AR_plans;	
			$model->status = "publish";
		}
		
		$model->multi_language = $multi_language;	
		
		if(isset($_POST['AR_plans'])){
			$model->attributes=$_POST['AR_plans'];
			if($model->validate()){
				
				$model->price = floatval($model->price);	
				$model->promo_price = floatval($model->promo_price);					
				$model->sequence = intval($model->sequence);	
								
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/update','id'=>$model->package_id));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				
			} else Yii::app()->user->setFlash('error',CommonUtility::t(HELPER_CORRECT_FORM));
		}		
		
		$model->price = Price_Formatter::convertToRaw($model->price,2,true);
		$model->promo_price = Price_Formatter::convertToRaw($model->promo_price,2,true);
		
		$data  = array();
		
		if($update && !isset($_POST['AR_plans'])){
			$translation = AttributesTools::GetFromTranslation($id,'{{plans}}',
			'{{plans_translation}}',
			'package_id',
			array('package_id','title','description'),
			array('title'=>'title_trans','description'=>"description_trans")
			);					
			$data['title_trans'] = isset($translation['title'])?$translation['title']:'';			
			$data['description_trans'] = isset($translation['description'])?$translation['description']:'';			
		}		
		
		$fields = array();
		$fields[]=array(
		  'name'=>'title_trans',
		  'placeholder'=>"Enter [lang] title here"
		);
		$fields[]=array(
		  'name'=>'description_trans',
		  'placeholder'=>"Enter [lang] description here",
		  'type'=>"textarea",
		  'class'=>"summernote"
		);
		
		if($update){
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//plans/plans_create",
				'widget'=>'WidgetPlans',					
				'params'=>array(  		
				    'model'=>$model,	
				    'update'=>$update,     				   
				    'status'=>(array)AttributesTools::StatusManagement('post'),
				    'package_period_list'=>AttributesTools::PlanPeriod(),				    
				    'multi_language'=>$multi_language,
				    'language'=>AttributesTools::getLanguage(),		    
				    'fields'=>$fields,
				    'data'=>$data,
				   'links'=>array(
				     t("All Plans")=>array('plans/list'),        
	                 $this->pageTitle,
	                 Yii::app()->input->xssClean($model->title)
				   )
				 )
			));		
		} else {
			$this->render("plans_create",array(
			    'model'=>$model,
			    'update'=>$update,
			    'status'=>(array)AttributesTools::StatusManagement('post'),
			    'package_period_list'=>AttributesTools::PlanPeriod(),				    
			    'multi_language'=>$multi_language,
			    'language'=>AttributesTools::getLanguage(),		    
			    'fields'=>$fields,
			    'data'=>$data,
			    'links'=>array(
				    t("All Plans")=>array('plans/list'),        
	                $this->pageTitle,
				)
			));
		}
	}
	
	public function actionupdate()
	{
	   $this->actioncreate(true);
	}
	
	public function actionplan_delete()
	{
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_plans::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('plans/list'));			
		} else $this->render("error");
	}
	
	public function actionfeatures()
	{
		$this->pageTitle = t("Features");
		CommonUtility::setMenuActive('.membership',".plans_create");
		
		$id = Yii::app()->input->get('id');
		$model = AR_plans::model()->findByPk( intval($id) );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		$table_col = array(
		  'meta_id'=>array(
		    'label'=>t("ID"),
		    'width'=>'15%',		    
		  ),
		  'meta_value'=>array(
		    'label'=>t("Description"),
		    'width'=>'40%'
		  ),		  
		  'date_modified'=>array(
		    'label'=>t("Actions"),
		    'width'=>'10%'
		  ),		  
		);
		$columns = array(
		  array('data'=>'meta_id','visible'=>false),
		  array('data'=>'meta_value'),		  
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_edit normal btn btn-light tool_tips"><i class="zmdi zmdi-border-color"></i></a>
			    <a class="ref_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>
			 </div>
		     '
		  ),   
		  array('data'=>null,'visible'=>false),
		);				
				
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//plans/features_list",
			'widget'=>'WidgetPlans',					
			'params'=>array(  						    
			   'table_col'=>$table_col,
			   'columns'=>$columns,
		       'order_col'=>1,
               'sortby'=>'desc',
               'ref_id'=>intval($id),
			   'links'=>array(
			     t("All Plans")=>array('plans/list'),        
                 $this->pageTitle,
                 Yii::app()->input->xssClean($model->title)
			   ),
			   'link'=>Yii::app()->createUrl('plans/feature_create',array('id'=>$id))
			 )
		));	
	}
	
	public function actionfeature_update()
	{
		$this->actionfeature_create(true);
	}
	
	public function actionfeature_create($update=false)
	{
		$this->pageTitle = t("Features");
		CommonUtility::setMenuActive('.membership',".plans_create");
		CommonUtility::setSubMenuActive('.features-menu','.features');
		
		$id = Yii::app()->input->get('id');
		$find = AR_plans::model()->findByPk( intval($id) );				
		if(!$find){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		$model = new AR_admin_meta;		
		if($update){
			$meta_id = Yii::app()->input->get('meta_id');
			$model = AR_admin_meta::model()->findByPk( intval($meta_id) );	
			if(!$model){
				 $this->render('//tpl/error',array(  
				  'error'=>array(
				    'message'=>t("Record not found")
				  )
				));
				Yii::app()->end();
			}
		}
		
		if(DEMO_MODE){			
			$this->render('//tpl/error',array(  
				  'error'=>array(
				    'message'=>t("Modification not available in demo")
				  )
				));	
		    return false;
		}
		
		if(isset($_POST['AR_admin_meta'])){
			$model->attributes=$_POST['AR_admin_meta'];
			$model->meta_name = 'plan_features';
			$model->meta_value1 = intval($id);
			if($model->validate()){							
				if($model->save()){
					if(!$update){
					   $this->redirect(array('plans/features','id'=>$id));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {
				dump($model->getErrors());
				Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString($model->getErrors()) );
			}
		}	
		
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//plans/features_create",
			'widget'=>'WidgetPlans',					
			'params'=>array(  		
			   'model'=>$model,
			   'links'=>array(
			     t("All Plans")=>array('plans/list'),        
                 $this->pageTitle,
                 Yii::app()->input->xssClean($find->title)
			   ),
			   'link'=>Yii::app()->createUrl('plans/feature_create')
			 )
		));	
	}
	
	public function actionfeature_delete()
	{
				
		if(DEMO_MODE){			
		    $this->render('//tpl/error',array(  
		          'error'=>array(
		            'message'=>t("Modification not available in demo")
		          )
		        ));	
		    return false;
		}

		$id = intval(Yii::app()->input->get('id'));	
		$meta_id = intval(Yii::app()->input->get('meta_id'));	
		$model = AR_admin_meta::model()->findByPk( $meta_id );
		if($model){	
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('plans/features','id'=>$id));			
		} else $this->render("error");
	}
	
	public function actionpayment_list()
	{
	 	$this->pageTitle = t("Features");
		CommonUtility::setMenuActive('.membership',".plans_create");
		
		$id = Yii::app()->input->get('id');
		$model = AR_plans::model()->findByPk( intval($id) );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		$payment_list = AttributesTools::PaymentPlansProvider(); 
		$payment_code_list = array();
		if(is_array($payment_list) && count($payment_list)>=1){
			foreach ($payment_list as $item) {
				$payment_code_list[]="plan_price_".$item['payment_code'];
			}
		}
		
		$model = new AR_admin_meta;		
		if(isset($_POST['AR_admin_meta'])){
									
			if(DEMO_MODE){			
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$payment_plan = $_POST['AR_admin_meta']['payment_plan_id'];				
			if(is_array($payment_list) && count($payment_list)>=1){
				foreach ($payment_list as $item) {					
					$payment_code = $item['payment_code'];
					$meta_name = "plan_price_$payment_code";
					$value = isset($payment_plan[$payment_code])?$payment_plan[$payment_code]:'';						
					AR_admin_meta::saveMetaWithID($meta_name,$value,$id);
				}
				Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
				$this->refresh();
			}						
		}
				
		$new_data = array();
		$data = AR_admin_meta::getMetaWithID($payment_code_list,$id);
		if(is_array($data) && count($data)>=1){
			foreach ($data as $key=>$val) {
				$new_data[$key]=$val['meta_value'];
			}
		}			
				
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//plans/payment_create",
			'widget'=>'WidgetPlans',					
			'params'=>array(  	
			    'payment_list'=>$payment_list,
			    'model'=>$model,
			    'new_data'=>$new_data,
			    'links'=>array(			    
			     t("All Plans")=>array('plans/list'),        
                 $this->pageTitle,
                 t("Payment ID")
			   ),			   
			 )
		));	
	}
	
}
/*end class*/