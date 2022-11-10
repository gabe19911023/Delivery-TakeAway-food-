<?php
require 'php-jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class VendorController extends CommonController
{
	
	public function beforeAction($action)
	{									
		InlineCSTools::registerStatusCSS();	
		return true;
	}
		
	public function actionIndex()
	{
		$this->redirect(array(Yii::app()->controller->id.'/list'));
	}
	
	public function actionList()
	{		
		$this->pageTitle=t("All Merchant");
		$action_name='merchant_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete");

		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');		
			
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'merchant_list_app';
		} else $tpl = 'merchant_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create")
		));
	}
	
	private function setMenuActive($class_name='.vendor_list')
	{
		ScriptUtility::registerScript(array(
		  '$(".siderbar-menu li.merchant").addClass("active")',		 
		  '$(".siderbar-menu li.merchant ul li'.$class_name.'").addClass("active")',		 
		),'menu_active',CClientScript::POS_END);
		
		$this->pageTitle = t("Add Merchant");		
	}
	
	public function actionCreate()
	{
		$this->setMenuActive(".vendor_list");
		$model=new AR_merchant;
		$model->scenario='information';
		$upload_path = CMedia::adminFolder();		
				
		if(isset($_POST['AR_merchant'])){
		    $model->attributes=$_POST['AR_merchant'];			    	
		    if($model->validate()){						    				    	
		    			    	
		    	if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->logo = $_POST['photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->logo = '';
				} else $model->logo = '';
				
				if(isset($_POST['header_image'])){
					if(!empty($_POST['header_image'])){
						$model->header_image = $_POST['header_image'];
						$model->path2 = isset($_POST['path2'])?$_POST['path2']:$upload_path;
					} else $model->header_image = '';
				} else $model->header_image = '';
				
				if(isset($_POST['footer_image'])){
					if(!empty($_POST['footer_image'])){
						$model->footer_image = $_POST['footer_image'];
						$model->path2 = isset($_POST['path2'])?$_POST['path2']:$upload_path;
					} else $model->footer_image = '';
				} else $model->footer_image = '';
			    
		    	if($model->save()){					    				    		
					Yii::app()->user->setFlash('success', t(Helper_success) );					
					$this->redirect(array('vendor/edit','id'=>$model->merchant_id));
					Yii::app()->end();
				} else {					
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}				
			} else Yii::app()->user->setFlash('error',CommonUtility::t(HELPER_CORRECT_FORM));
		}
		
		$cuisine = CommonUtility::getDataToDropDown("{{cuisine}}",'cuisine_id','cuisine_name',"
		WHERE status = 'publish'","ORDER BY cuisine_name ASC");
		
		$tags = CommonUtility::getDataToDropDown("{{tags}}",'tag_id','tag_name',"","ORDER BY tag_name ASC");

		$this->render("merchant_create",array(
		  'model'=>$model,
		  'status'=>(array)AttributesTools::StatusManagement('customer'),
		  'cuisine'=>(array)AttributesTools::ListSelectCuisine(),
		  'tags'=>(array)AttributesTools::ListSelectTags(),
		  'services'=>(array)AttributesTools::ListSelectServices(),
		  'unit'=>AttributesTools::unit(),
		  'featured'=>AttributesTools::MerchantFeatured(),
		  'upload_path'=>$upload_path,
		  'links'=>array(
			    'links'=>array(
			        t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
			        $model->isNewRecord?t("Add new"):t("Edit Merchant"),
			    ),
			    'homeLink'=>false,
			    'separator'=>'<span class="separator">
			    <i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
			)
	    ));
	}
	
	public function actionEdit()
	{
		$this->setMenuActive('.vendor_list');
		$this->pageTitle = t("Edit Merchant - information");	
		
		$id = (integer) Yii::app()->input->get('id');	
		$upload_path = CMedia::adminFolder();
				
		if($id>0){
										
			$model = AR_merchant::model()->findByPk( $id );					
			
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}
			
			$model->scenario='information';
			
			if(isset($_POST['AR_merchant'])){
			    $model->attributes=$_POST['AR_merchant'];			
			    if($model->validate()){			    	
			    			    			    	    
		    	    if(isset($_POST['photo'])){
						if(!empty($_POST['photo'])){
							$model->logo = $_POST['photo'];
							$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
						} else $model->logo = '';
					} else $model->logo = '';
					
					if(isset($_POST['header_image'])){
						if(!empty($_POST['header_image'])){
							$model->header_image = $_POST['header_image'];
							$model->path2 = isset($_POST['path2'])?$_POST['path2']:$upload_path;
						} else $model->header_image = '';
					} else $model->header_image = '';
					
					if(isset($_POST['footer_image'])){
						if(!empty($_POST['footer_image'])){
							$model->footer_image = $_POST['footer_image'];
							$model->path2 = isset($_POST['path2'])?$_POST['path2']:$upload_path;
						} else $model->footer_image = '';
					} else $model->footer_image = '';
			    	
					if($model->save()){																					
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					} else {
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				} else Yii::app()->user->setFlash('error',CommonUtility::t(HELPER_CORRECT_FORM));
			}
			
			
			if(!isset($_POST['AR_merchant'])){
												
				/*if(isset($model->cuisine)){				
				   $model->cuisine2 = json_decode($model->cuisine,true);
				}*/				
				$find = AR_cuisine_merchant::model()->findAll(
				    'merchant_id=:merchant_id',
				    array(':merchant_id'=> intval($model->merchant_id) )
				);
				if($find){
					$selected = array();
					foreach ($find as $items) {					
						$selected[]=$items->cuisine_id;
					}
					$model->cuisine2 = $selected;
				}		
			
				if($services = MerchantTools::getMerchantMeta($model->merchant_id,'services')){
					$model->service2=$services;
				}											
				
				if($services = MerchantTools::getMerchantMeta($model->merchant_id,'featured')){
					$model->featured=$services;
				}											
				
				if($tags = MerchantTools::getMerchantOptions($model->merchant_id,'tags')){					
					$model->tags=$tags;
				}											
			}
			
			$model->delivery_distance_covered = Price_Formatter::convertToRaw($model->delivery_distance_covered,0);
			
			$model->restaurant_name = stripslashes($model->restaurant_name);
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$params_model = array(		
				'model'=>$model,	
				'status'=>(array)AttributesTools::StatusManagement('customer'),	    
				'cuisine'=>(array)AttributesTools::ListSelectCuisine(),
				'services'=>(array)AttributesTools::ListSelectServices(),
				'tags'=>(array)AttributesTools::ListSelectTags(),
				'unit'=>AttributesTools::unit(),	
				'featured'=>AttributesTools::MerchantFeatured(),
				'ctr'=>'/vendor',
				'upload_path'=>$upload_path,
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
			       $model->isNewRecord?t("Add new"):t("Edit Merchant"),
			       isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    		
				'show_status'=>true
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/merchant_info",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				)
			));		
			
		} else {
			$this->render("error");
		}
	}
	
	public function actiondelete_logo()
	{		
		$id = (integer) Yii::app()->input->get('id');			
		$page = Yii::app()->input->get('page');			
		$model = AR_merchant::model()->findByPk( $id );				
		if($model){		
			$model->logo='';
			$model->save();					
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array($page."/id/$id"));
		} else $this->render("error");
	}
	
    public function actionlogin()
    {
    	$this->setMenuActive();		
		$this->pageTitle = t("Edit Merchant - login");
		
		$merchant_id = intval(Yii::app()->input->get('id'));		
    	$merchant = AR_merchant::model()->findByPk( $merchant_id );	
    	if($merchant){
	    	$avatar = CMedia::getImage($merchant->logo,$merchant->path,'@thumbnail',
	        CommonUtility::getPlaceholderPhoto('merchant_logo'));
	        
	        $model = AR_merchant_user::model()->find("merchant_id=:merchant_id AND main_account=:main_account",array(
			  ':merchant_id'=>$merchant_id,
			  ':main_account'=>1
			));	
			
			if(!$model){
	            $model = new AR_merchant_user;
	            $model->scenario = "register";
			} 
					
			if(isset($_POST['AR_merchant_user'])){
				$model->attributes=$_POST['AR_merchant_user'];
				if($model->validate()){	
					$model->status = 'active';
					$model->main_account = 1;
					$model->merchant_id = intval($merchant->merchant_id);
					
					if($model->scenario=="register"){
						$model->password = trim($model->new_password);
					} else {
						if( !empty($model->new_password) && !empty($model->repeat_password) ){
							$model->password = md5(trim($model->new_password));
						}
					}
					
					if($model->save()){
					   Yii::app()->user->setFlash('success', t(Helper_success) );		
			       	   $this->refresh();			
					} else Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				} else Yii::app()->user->setFlash('error',CommonUtility::t(HELPER_CORRECT_FORM));
			}

			if($model){
				$model->password = '';
			}
				
        	$params_model = array(		
				'model'=>$model,	
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
				   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
				   isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''
				),	    		   
			);	
				
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $merchant_id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"merchant_login",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$merchant_id
				)
			));		
	        
    	} else $this->render("error");	
    }
	
	public function actionmembership()
	{		
		$this->setMenuActive();		
		$this->pageTitle = t("Edit Merchant - Merchant type");
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			//$model->scenario='membership';

			if(isset($_POST['AR_merchant'])){
		       $model->attributes=$_POST['AR_merchant'];				       
			    if($model->validate()){						    				    	
			    	$model->percent_commision = (float)$model->percent_commision;			    	
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				} else Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString($model->getErrors(),"<br/>") );
			}		
			
			$model->percent_commision = number_format( (float) $model->percent_commision,2);
			
            $avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));

			$params_model = array(		
				'model'=>$model,	
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
				   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    
				'merchant_type'=>AttributesTools::ListMerchantType(),
			    'package'=>AttributesTools::ListPlans(),
			    'commision_type'=>AttributesTools::CommissionType(),
			    'invoice_terms'=>AttributesTools::InvoiceTerms()		   
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/membership",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				)
			));		
			
		} else $this->render("error");
	}
	
	public function actionfeatured()
	{
		$this->setMenuActive();		
		$this->pageTitle = t("Edit Merchant - Featured");
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model->scenario='featured';

			if(isset($_POST['AR_merchant'])){
		       $model->attributes=$_POST['AR_merchant'];				       
			    if($model->validate()){						    				    	
			    				    	
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				}
			}		
							
            $avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$params_model = array(		
				'model'=>$model,	
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
				   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    		   
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/featured",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				)
			));		
			
		} else $this->render("error");
	}
	
	public function actionaddress()
	{
		$this->setMenuActive();		
		$this->pageTitle = t("Edit Merchant - Address");
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model->scenario='address';

			if(isset($_POST['AR_merchant'])){
		       $model->attributes=$_POST['AR_merchant'];				       
			    if($model->validate()){						    				    				    				    
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
					} else Yii::app()->user->setFlash('error',CommonUtility::t(HELPER_CORRECT_FORM));
			}		

			//$country_list = require_once 'CountryCode.php';
						
            $avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$params_model = array(		
				'model'=>$model,	
				'unit'=>AttributesTools::unit(),	
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
				   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    		   
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"address",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				)
			));		
			
		} else $this->render("error");
	}
	
	public function actionzone()
	{
		$this->setMenuActive();		
		$this->pageTitle = t("Edit Merchant - Zone");
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		$meta_name = 'zone';
		
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
		    
		    $models = new AR_merchant_meta;
		   
		    if(isset($_POST['AR_merchant_meta'])){
		    			    	
		    	AR_merchant_meta::model()->deleteAll('merchant_id=:merchant_id AND meta_name=:meta_name',array(
				 ':merchant_id'=> $id,
				 ':meta_name'=>$meta_name
				));
				
		    	$post = Yii::app()->input->xssClean($_POST); 
		    	$zone = isset($post['AR_merchant_meta']['zone'])?$post['AR_merchant_meta']['zone']:'';
		    	if(is_array($zone) && count($zone)>=1){
		    		foreach ($zone as $zone_id) {
		    			$meta = new AR_merchant_meta;
		    			$meta->merchant_id = intval($id);
		    			$meta->meta_name = $meta_name;
		    			$meta->meta_value = intval($zone_id);
		    			$meta->save();
		    		}		    		
		    	}	
		    	Yii::app()->user->setFlash('success', t(Helper_success) );
				$this->refresh();							    	
		    } else if ( isset($_POST['yt0']) ) {
		    	AR_merchant_meta::model()->deleteAll('merchant_id=:merchant_id AND meta_name=:meta_name',array(
				 ':merchant_id'=> $id,
				 ':meta_name'=>$meta_name
				));		   		    	
				Yii::app()->user->setFlash('success', t(Helper_success) );
				$this->refresh();							    	
		    }
		    
		    $zone_data = CommonUtility::getDataToDropDown("{{merchant_meta}}",'meta_value','meta_value',"where merchant_id=".q($id)." AND meta_name='zone' " );		    
		    $models->zone = (array)$zone_data;
		    
			$params_model = array(		
				'model'=>$models,				
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
				   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    		   
				'zone_list'=>CommonUtility::getDataToDropDown("{{zones}}",'zone_id','zone_name','where merchant_id=0',"Order by zone_name asc"),				
			);	
								
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"zone",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),				
			));		
			
		} else $this->render("error");
	}
	
	public function actionpayment_history()
	{
		$this->setMenuActive();		
		$this->pageTitle = t("Payment history");		
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$action_name='payment_history';
			$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete");
			
			ScriptUtility::registerScript(array(
			  "var action_name='$action_name';",
			  "var delete_link='$delete_link';",
			),'action_name');
									
            $avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			/*if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			   $tpl = '//tpl/lazy_list';
			} else $tpl = "//vendor/payment_history";*/
			
			$table_col = array(		  			 
			 'created'=>array(
			    'label'=>t("Created"),
			    'width'=>'20%'
			  ),			 
			  'payment_code'=>array(
			    'label'=>t("Payment"),
			    'width'=>'10%'
			  ),		  
			  'invoice_ref_number'=>array(
			    'label'=>t("Invoice #"),
			    'width'=>'20%'
			  ),		  
			  'package_id'=>array(
			    'label'=>t("Plan"),
			    'width'=>'20%'
			  ),		  
			);
			$columns = array(		  			  
			  array('data'=>'created'),			  
			  array('data'=>'payment_code'),		  
			  array('data'=>'invoice_ref_number'),		  
			  array('data'=>'package_id','orderable'=>false),		  
			);		
			
			$params_model = array(		
				'model'=>$model,	
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
				   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    		  
				'table_col'=>$table_col,
			    'columns'=>$columns,
			    'order_col'=>1,
	            'sortby'=>'desc', 
	            'merchant_id'=>$id,
	            'ajax_url'=>Yii::app()->createUrl("/api")
			);							
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>'payment_history',
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>array(),
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),				
			));		
					
		} else $this->render("error");
	}
	
	public function actionpayment_settings()
	{
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Payment Settings");
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			if(isset($_POST['AR_merchant'])){
		        $model->attributes=$_POST['AR_merchant'];		        
			    if($model->validate()){						    				    				    	
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				}
			}		
			
			if($payment_gateway = MerchantTools::getMerchantMeta($model->merchant_id,'payment_gateway')){			
				$model->payment_gateway=$payment_gateway;
			}		
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));

			$params_model = array(		
				'model'=>$model,	
				'links'=>array(
				   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
				   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
				   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
				),	    		   
				'provider'=>AttributesTools::PaymentProvider()
			);	
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/payment_settings",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,
				'params'=>$params_model,
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				)
			));		
		} else $this->render("error");
	}
	
	
	public function actionothers()
	{
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Others");
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model->scenario='others';

			if(isset($_POST['AR_merchant'])){				
		       $model->attributes=$_POST['AR_merchant'];	
		       		       
		        //MerchantTools::savedOptions($model->merchant_id,$_POST['AR_merchant']);		       
		       
			    if($model->validate()){						    				    				    				    
			    	if($model->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				}
			}		
										
            $avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
					
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/others",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'model'=>$model,	
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}
	
	public function actionaccess()
	{
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Access");
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){					
									
            $avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
			
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/access",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'model'=>$model,	
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}
	
	public function actiondelete()
	{
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/list'));			
		} else $this->render("error");
	}
	
	public function actioncsvlist()
	{		
		$this->pageTitle=t("All CSV Upload");
		$action_name='csv_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete_csv");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'csv_list_app';
		} else $tpl = 'csv_list';
		
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/csv_upload")
		));		
	}
	
	public function actioncsv_upload()
	{
		$this->setMenuActive(".vendor_csvlist");
		$this->pageTitle = t("Upload");
		
		
		$model=new AR_csv;
		if(isset($_POST['AR_csv'])){
			$model->attributes=$_POST['AR_csv'];
			
			if($model->validate()){		
				$model->image=CUploadedFile::getInstance($model,'image');
				$model->filename =  $model->image->name;
				$extension = substr($model->image->name,-3,3);		
				$path = CommonUtility::uploadDestination('csv')."/".$model->filename;
				$model->file_path = $path;						
				$model->image->saveAs( $path );
				if($model->save()){										
					$this->redirect(array(Yii::app()->controller->id.'/csvlist'));		
				} else {
					Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
				}
			}
		}
		
		$this->render("csv_upload",array(		  
		  'model'=>$model,
		  'links'=>array(
			    'links'=>array(
			        t("All CSV Upload")=>array(Yii::app()->controller->id.'/csvlist'),        
			        $this->pageTitle,
			    ),
			    'homeLink'=>false,
			    'separator'=>'<span class="separator">
			    <i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
			)
	    ));
	}
	
	public function actioncsv_view()
	{				
		$this->setMenuActive(".vendor_csvlist");
		$this->pageTitle=t("CSV details");
				
		$action_name='csv_list_details';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete_csv_details");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		$id = (integer) Yii::app()->input->get('id');
		$model = AR_csv::model()->findByPk( $id );
		
		if($model){
			
			if(Yii::app()->params['isMobile']==TRUE){
				$tpl = 'csv_list_details_app';
			} else $tpl = 'csv_list_details';
				
			$this->render( $tpl ,array(
			  'id'=>$id,
			  'back_link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/csvlist"),
			  'links'=>array(
				    'links'=>array(
				        t("All CSV Upload")=>array(Yii::app()->controller->id.'/csvlist'),        
				        t("View"),
				        t("#[id]",array('[id]'=>$id))
				    ),
				    'homeLink'=>false,
				    'separator'=>'<span class="separator">
				    <i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
				)			  
			));		
		} else $this->render("error");
	}
	
	public function actiondelete_csv()
	{
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_csv::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/csvlist'));			
		} else $this->render("error");
	}
	
	public function actionSponsored()
	{
		$this->pageTitle=t("All Sponsored");
		$action_name='sponsored_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete_sponsored");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = 'sponsored_list_app';
		} else $tpl = 'sponsored_list';
		
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create_sponsored")
		));		
	}
	
	public function actioncreate_sponsored()
	{		
		$this->setMenuActive(".vendor_sponsored");
		$this->pageTitle=t("Add sponsored");
		
		$model=new AR_sponsored;		
		if(isset($_POST['AR_sponsored'])){
			$model->attributes=$_POST['AR_sponsored'];
			if($model->validate()){				
				$id = (integer) $model->merchant_id;		
				$model2 = AR_sponsored::model()->findByPk( $id );					
				$model2->is_sponsored = 2;					
				$model2->sponsored_expiration = $model->sponsored_expiration; 				
				if($model2->save()){
					Yii::app()->user->setFlash('success',t(Helper_success));
					$this->redirect(array(Yii::app()->controller->id.'/sponsored'));					
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
				
		$this->render("sponsored_add",array(
		   'model'=>$model,
		   'merchant_list'=>AttributesTools::MerchantList(),
		   'selected_merchant'=>array(),
		   'links'=>array(
			    'links'=>array(
			        t("All Sponsored")=>array(Yii::app()->controller->id.'/sponsored'),        
			        $this->pageTitle,
			    ),
			    'homeLink'=>false,
			    'separator'=>'<span class="separator">
			    <i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
			)
		));	
	}
	
	public function actionedit_sponsored()
	{
		$this->setMenuActive(".vendor_sponsored");
		$this->pageTitle=t("Add sponsored");
		$selected_merchant = array();
		
		$id = (integer) Yii::app()->input->get('id');				
		$model = AR_sponsored::model()->findByPk( $id );				
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}	
		
		if($model->is_sponsored==2){
			$selected_merchant[$model->merchant_id] = stripslashes($model->restaurant_name);
		}
				
		if(isset($_POST['AR_sponsored'])){
			$model->attributes=$_POST['AR_sponsored'];			
			if($model->validate()){				
				if($model->save()){
					Yii::app()->user->setFlash('success',t(Helper_update));
					$this->refresh();					
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
				
		$this->render("sponsored_add",array(
		   'model'=>$model,
		   'merchant_list'=>AttributesTools::MerchantList(),
		   'selected_merchant'=>$selected_merchant,
		   'links'=>array(
			    'links'=>array(
			        t("All Sponsored")=>array(Yii::app()->controller->id.'/sponsored'),        
			        $this->pageTitle,
			    ),
			    'homeLink'=>false,
			    'separator'=>'<span class="separator">
			    <i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
			)
		));	
	}
	
	public function actiondelete_sponsored()
	{
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_sponsored::model()->findByPk( $id );
		if($model){
			$model->is_sponsored = 1;
			$model->save();
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/sponsored'));			
		} else $this->render("error");
	}

	public function actionapi_access()
	{

		try {
			ItemIdentity::addonIdentity('Single app');
		} catch (Exception $e) {
			$this->render('//tpl/error',[
				'error'=>[
					'message'=>$e->getMessage()
				]
			]);
			Yii::app()->end();
		}
				
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - API Access");
		$jwt_token = AttributesTools::JwtTokenID();
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$model2 = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name",[
				':merchant_id'=>intval($id),
				':meta_name'=>$jwt_token
			]);
			if(!$model2){
				$model2 = new  AR_merchant_meta;
			}			

			$model2->scenario = 'create_api_access';
			
			if(isset($_POST['AR_merchant_meta'])){				
		        $model2->attributes=$_POST['AR_merchant_meta'];			       		       		        
				$model2->merchant_id = $id;
				$model2->meta_name = $jwt_token;				
			    if($model2->validate()){		
					$payload = [
						'iss'=>Yii::app()->request->getServerName(),
						'sub'=>$id,
						'aud'=>$model2->website_url,
						'iat'=>time(),						
					];					
					$jwt = JWT::encode($payload, CRON_KEY, 'HS256');					
					$model2->meta_value = $jwt;					
			    	if($model2->save()){						    					    	
						Yii::app()->user->setFlash('success', t(Helper_success) );
						$this->refresh();						
					} else {					
						Yii::app()->user->setFlash('error',CommonUtility::t(Helper_failed_update));
					}				
				} else Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString($model2->getErrors(),"<br/>") );
			}		
										
            $avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
					
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/api_access",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,				
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				   'id'=>$id,
				  'model'=>$model2,	
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}

	public function actiondelete_apikeys()
	{
		$jwt_token = AttributesTools::JwtTokenID();
		$id = (integer) Yii::app()->input->get('id');
		
		if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
			$this->render('//tpl/error',array(  
				 'error'=>array(
				   'message'=>t("Modification not available in demo")
				 )
			   ));	
		   return false;
	   }
	   
		$model = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name",[
			':merchant_id'=>intval($id),
			':meta_name'=>$jwt_token
		]);
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/api_access','id'=>$id ));			
		} else $this->render("error");
	}

	public function actionsearch_mode()
	{
				
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Search Mode");
		$jwt_token = AttributesTools::JwtTokenID();
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));

			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
													
			$model2 = new AR_option;

			$options = array(
				'merchant_set_default_country'
			 );
					 
			 if($data = OptionsTools::find($options,$id)){
				 foreach ($data as $name=>$val) {
					 $model2[$name]=$val;
				 }			
			 }
				 
			 if(isset($_POST['AR_option'])){
				 if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					  $this->render('//tpl/error',array(  
						   'error'=>array(
							 'message'=>t("Modification not available in demo")
						   )
						 ));	
					 return false;
				 }
				 $model2->attributes=$_POST['AR_option'];				
				 if($model2->validate()){				
					 OptionsTools::$merchant_id = $id;					
					 if(OptionsTools::save($options, $model2, $id)){
						 Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						 $this->refresh();
					 } else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				 } 
			 }
           
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//merchant/search_settings",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,				
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'id'=>$id,
				  'model'=>$model2,	
				  'country_list'=>AttributesTools::CountryList(),		   
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}	

	public function actionlogin_sigup()
	{				
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Login & Signup");
		$jwt_token = AttributesTools::JwtTokenID();
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
								
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
													
			$model2 = new AR_option;

			$options = array(
				'merchant_signup_enabled_verification','merchant_signup_resend_counter','merchant_signup_enabled_terms',
				'merchant_signup_terms'
			 );
					 
			 if($data = OptionsTools::find($options,$id)){
				 foreach ($data as $name=>$val) {
					 $model2[$name]=$val;
				 }			
			 }
				 
			 if(isset($_POST['AR_option'])){
				 if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					  $this->render('//tpl/error',array(  
						   'error'=>array(
							 'message'=>t("Modification not available in demo")
						   )
						 ));	
					 return false;
				 }
				 $model2->attributes=$_POST['AR_option'];				
				 if($model2->validate()){				
					 OptionsTools::$merchant_id = $id;					
					 if(OptionsTools::save($options, $model2, $id)){
						 Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						 $this->refresh();
					 } else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				 } 
			 }
           
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//merchant/login_signup",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,				
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'id'=>$id,
				  'model'=>$model2,					  
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}		

	public function actionphone_settings()
	{				
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Phone Settings");
		$jwt_token = AttributesTools::JwtTokenID();
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
								
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
													
			$model2 = new AR_option;

			$options = array(
				'merchant_mobilephone_settings_country','merchant_mobilephone_settings_default_country'
			 );
					 
			 if($data = OptionsTools::find($options,$id)){
				 foreach ($data as $name=>$val) {
					 $model2[$name]=$val;
				 }			
			 }
				 
			 if(isset($_POST['AR_option'])){
				 if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					  $this->render('//tpl/error',array(  
						   'error'=>array(
							 'message'=>t("Modification not available in demo")
						   )
						 ));	
					 return false;
				 }
				 $model2->attributes=$_POST['AR_option'];				
				 if($model2->validate()){				
					 OptionsTools::$merchant_id = $id;					
					 if(OptionsTools::save($options, $model2, $id)){
						 Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						 $this->refresh();
					 } else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				 } 
			 }
           
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//merchant/phone_settings",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,				
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'id'=>$id,
				  'model'=>$model2,	
				  'country_list'=>AttributesTools::CountryList(),		   
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}			

	public function actionsocial_settings()
	{				
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Social Settings");
		$jwt_token = AttributesTools::JwtTokenID();
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
								
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
													
			$model2 = new AR_option;

			$options = array(
			 'facebook_page','twitter_page','google_page','instagram_page',
			 'merchant_fb_flag','merchant_fb_app_id','merchant_fb_app_secret','merchant_google_login_enabled','merchant_google_client_id','merchant_google_client_secret'
			);
					 
			 if($data = OptionsTools::find($options,$id)){
				 foreach ($data as $name=>$val) {
					 $model2[$name]=$val;
				 }			
			 }
				 
			 if(isset($_POST['AR_option'])){
				 if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					  $this->render('//tpl/error',array(  
						   'error'=>array(
							 'message'=>t("Modification not available in demo")
						   )
						 ));	
					 return false;
				 }
				 $model2->attributes=$_POST['AR_option'];				
				 if($model2->validate()){				
					 OptionsTools::$merchant_id = $id;					
					 if(OptionsTools::save($options, $model2, $id)){
						 Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						 $this->refresh();
					 } else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				 } 
			 }
           
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//merchant/social_settings",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,				
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'id'=>$id,
				  'model'=>$model2,					  
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}			

	public function actionrecaptcha_settings()
	{				
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Recaptcha Settings");
		$jwt_token = AttributesTools::JwtTokenID();
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
								
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
													
			$model2 = new AR_option;

			$options = ['merchant_captcha_enabled','merchant_captcha_site_key','merchant_captcha_secret','merchant_captcha_lang'];
					 
			 if($data = OptionsTools::find($options,$id)){
				 foreach ($data as $name=>$val) {
					 $model2[$name]=$val;
				 }			
			 }
				 
			 if(isset($_POST['AR_option'])){
				 if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					  $this->render('//tpl/error',array(  
						   'error'=>array(
							 'message'=>t("Modification not available in demo")
						   )
						 ));	
					 return false;
				 }
				 $model2->attributes=$_POST['AR_option'];				
				 if($model2->validate()){				
					 OptionsTools::$merchant_id = $id;					
					 if(OptionsTools::save($options, $model2, $id)){
						 Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						 $this->refresh();
					 } else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				 } 
			 }
           
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//vendor/recaptcha_settings",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,				
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'id'=>$id,
				  'model'=>$model2,					  
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}			

	public function actionmap_keys()
	{				
		$this->setMenuActive();		
		$this->pageTitle = t("Merchant - Map API Keys");
		$jwt_token = AttributesTools::JwtTokenID();
		
		$id = (integer) Yii::app()->input->get('id');		
		$model = AR_merchant::model()->findByPk( $id );
		if($model){
			
			$avatar = CMedia::getImage($model->logo,$model->path,'@thumbnail',
		    CommonUtility::getPlaceholderPhoto('merchant_logo'));
								
			$menu = array();
			if(Yii::app()->params['isMobile']==TRUE){
			   $menu = new WidgetMerchantInfoMenu;		   			   
			   $menu->merchant_id = $id;
			   $menu->init();    
			}
													
			$model2 = new AR_option;

			$options = array(
			  'merchant_map_provider','merchant_google_geo_api_key','merchant_google_maps_api_key','merchant_mapbox_access_token'
			);
					 
			 if($data = OptionsTools::find($options,$id)){
				 foreach ($data as $name=>$val) {
					if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
						$model2[$name] = CommonUtility::mask(date("Ymjhs"));
					} else $model2[$name]=$val;					 
				 }			
			 }
				 
			 if(isset($_POST['AR_option'])){
				 if(DEMO_MODE && in_array($id,DEMO_MERCHANT)){
					  $this->render('//tpl/error',array(  
						   'error'=>array(
							 'message'=>t("Modification not available in demo")
						   )
						 ));	
					 return false;
				 }
				 $model2->attributes=$_POST['AR_option'];				
				 if($model2->validate()){				
					 OptionsTools::$merchant_id = $id;					
					 if(OptionsTools::save($options, $model2, $id)){
						 Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						 $this->refresh();
					 } else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				 } 
			 }
           
						
			$this->render("//tpl/submenu_tpl",array(		    
				'template_name'=>"//merchant/map_keys",
				'widget'=>'WidgetMerchantInfoMenu',		
				'avatar'=>$avatar,				
				'menu'=>$menu,				
				'params_widget'=>array(			   
				   'merchant_id'=>$id
				),
				'params'=>array(
				  'id'=>$id,
				  'model'=>$model2,	
				  'map_provider'=>AttributesTools::mapsProvider(),		   				  
					'links'=>array(
					   t("All Merchant")=>array(Yii::app()->controller->id.'/list'),        
					   $model->isNewRecord?t("Add new"):t("Edit Merchant"),
					   isset($model->restaurant_name)?stripslashes(ucwords($model->restaurant_name)):''
					),	    		   
				)
			));						
		} else $this->render("error");
	}		
	
	public function actionautologin()
	{
		$merchant_uuid = Yii::app()->input->get('merchant_uuid');  
		$model = AR_merchant::model()->find("merchant_uuid=:merchant_uuid",[
			':merchant_uuid'=>trim($merchant_uuid)
		]);
		if($model){						
			$user = AR_merchant_user::model()->find("merchant_id=:merchant_id AND main_account=:main_account",[
				':merchant_id'=>$model->merchant_id,
				':main_account'=>1
			]);
			if($user){				
				Yii::app()->merchant->id = $model->merchant_id;
				Yii::app()->merchant->setState('merchant_id', $model->merchant_id);
				Yii::app()->merchant->setState('merchant_uuid', $model->merchant_uuid);
				Yii::app()->merchant->setState('status', $model->status);
				Yii::app()->merchant->setState('merchant_type', $model->merchant_type);                				
				Yii::app()->merchant->setState('restaurant_slug', $model->restaurant_slug);     

				Yii::app()->merchant->setState('first_name', $user->first_name);     
				Yii::app()->merchant->setState('last_name', $user->last_name); 
				Yii::app()->merchant->setState('email_address', $user->contact_email); 
				Yii::app()->merchant->setState('contact_number', $user->contact_number); 
				Yii::app()->merchant->setState('avatar', $user->profile_photo); 
				Yii::app()->merchant->setState('login_type', 'merchant'); 
				Yii::app()->merchant->setState('main_account', $user->main_account); 				
				Yii::app()->merchant->setState('role_id', $user->role); 
				Yii::app()->merchant->setState('logintoken', $user->session_token); 

				$this->redirect(Yii::app()->createUrl('/merchant/dashboard'));

			} else $this->render("//tpl/error",[
				'error'=>[
					'message'=>t("Merchant has no user please create one first")
				]
			]);
		} else $this->render("error");
	}		
	
}
/*end class*/