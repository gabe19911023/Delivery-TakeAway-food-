<?php
class ServicesController extends Commonmerchant
{
		
	public function beforeAction($action)
	{						
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();			
		return true;
	}

	public function actionIndex()
	{
		$this->redirect(array(Yii::app()->controller->id.'/delivery_settings'));	
	}
	
	public function actiondelivery_settings()
	{
		$this->pageTitle = t("Settings");
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$model=new AR_option;
		$model->scenario = 'delivery_settings';
				 
		 $options = array('merchant_delivery_charges_type',
		 'merchant_opt_contact_delivery','free_delivery_on_first_order');
		 
		 if($data = OptionsTools::find($options,$merchant_id)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		 }
		 
		 if(isset($model->merchant_delivery_charges_type)){		 	
		 	WidgetDeliveryMenu::$charge_type = $model->merchant_delivery_charges_type;
		 }
		 
		 $merchant_type = MerchantAR::getMerchantType();
		 if($merchant_type==1 || $merchant_type==3){
			 $service_id = AR_services::getID('delivery');		 
			 $fee = AR_services_fee::model()->find('merchant_id=:merchant_id AND service_id=:service_id', 
						array(':merchant_id'=>$merchant_id, ':service_id'=>$service_id ));
	         if($fee){         	
	         	$model->merchant_service_fee = Price_Formatter::convertToRaw($fee->service_fee,2,true);
	         }
		 }
		 	
		 if(isset($_POST['AR_option'])){
		 						 	
			if(DEMO_MODE && in_array($merchant_id,DEMO_MERCHANT)){
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
		 	
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				OptionsTools::$merchant_id = $merchant_id;														
				if(OptionsTools::save($options, $model, $merchant_id)){										
								
					if($merchant_type==1 || $merchant_type==3){		
						if(!$fee){
							$fee = new AR_services_fee;			
						} 					
						$fee->service_id = intval($service_id);
						$fee->merchant_id = intval($merchant_id);
						$fee->service_fee = floatval($model->merchant_service_fee);
						$fee->save();	
					}
					
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$params_model = array(
			'model'=>$model,	
			'charge_type'=>AttributesTools::DeliveryChargeType(),
			'merchant_type'=>$merchant_type,
			'links'=>array(
				t("Delivery")=>array(Yii::app()->controller->id.'/delivery_settings'),
				t("Settings")				
			),	    	
		);		
		
		
        $menu = array();
		if(Yii::app()->params['isMobile']==TRUE){			
			$menu = new WidgetDeliveryMenu;
            $menu->init();    
		} 
				
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'//merchant/delivery_settings',
			'widget'=>'WidgetDeliveryMenu',		
			'avatar'=>'',
			'params'=>$params_model,
			'menu'=>$menu		
		));
			
	}
	public function actionfixed_charge()
	{
		$this->pageTitle=t("Fixed charge");
		CommonUtility::setMenuActive('.services_settings','.services_delivery_settings');
		CommonUtility::setSubMenuActive(".services-menu",'.fixed_charge');	
		
		$merchant_id = intval(Yii::app()->merchant->merchant_id);
		$options = array('merchant_delivery_charges_type');
		$options_data = OptionsTools::find($options,$merchant_id);
		WidgetDeliveryMenu::$charge_type = isset($options_data['merchant_delivery_charges_type'])?$options_data['merchant_delivery_charges_type']:'standard';		         
		
		$service_code = 'delivery';
		$charge_type = 'fixed';
		$shipping_type = 'standard';
		
		$update = true;
		$model = AR_shipping_rate::model()->find('merchant_id=:merchant_id AND charge_type=:charge_type
		AND shipping_type=:shipping_type AND service_code=:service_code
		', 
		  array(':merchant_id'=>$merchant_id, 
		    ':charge_type'=>$charge_type,
		    ':shipping_type'=>$shipping_type,
		    ':service_code'=>$service_code,
		   ));
		  
		if(!$model){
			$update = false;
			$model = new AR_shipping_rate;
		} 
		
		if(isset($_POST['AR_shipping_rate'])){
			$model->scenario = 'fixed';
			$model->attributes=$_POST['AR_shipping_rate'];
			if($model->validate()){
				$model->merchant_id = $merchant_id;				
				$model->charge_type = $charge_type ;
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {
				//dump($model->getErrors());die();
				Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
			}
		}
		
		$model->distance_price = $model->distance_price>0? Price_Formatter::convertToRaw($model->distance_price,2) :'';
		$model->minimum_order = $model->minimum_order>0? Price_Formatter::convertToRaw($model->minimum_order,2) :'';
		$model->maximum_order = $model->maximum_order>0? Price_Formatter::convertToRaw($model->maximum_order,2) :'';
		
		$params_model = array(
			'model'=>$model,				
			'links'=>array(
				t("Delivery Settings")=>array(Yii::app()->controller->id.'/delivery_settings'),
				t("Settings")				
			),	    	
		);		
		
		
        $menu = array();
		if(Yii::app()->params['isMobile']==TRUE){			
			$menu = new WidgetDeliveryMenu;
            $menu->init();    
		} 
				
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'//merchant/fixed_charge',
			'widget'=>'WidgetDeliveryMenu',		
			'avatar'=>'',
			'params'=>$params_model,
			'menu'=>$menu		
		));
	}
	
	public function actioncharges_table()
	{
		$this->pageTitle=t("Delivery Charges Rates");
		CommonUtility::setMenuActive('.services_settings','.services_delivery_settings');
		CommonUtility::setSubMenuActive(".services-menu",'.services_charges_table');	
		
		$action_name='charges_table';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/chargestable_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		$params_model = array(		    
			'charge_type'=>AttributesTools::DeliveryChargeType(),
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/chargestable_create"),
			'links'=>array(
				t("Delivery Settings")=>array(Yii::app()->controller->id.'/delivery_settings'),	
				t("Delivery Charges Rates")
			),	    	
		);	
		
		$merchant_id = intval(Yii::app()->merchant->merchant_id);
		$options = array('merchant_delivery_charges_type');
		$options_data = OptionsTools::find($options,$merchant_id);
		WidgetDeliveryMenu::$charge_type = isset($options_data['merchant_delivery_charges_type'])?$options_data['merchant_delivery_charges_type']:'standard';		
				     
		$menu = array();  
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
			$menu = new WidgetDeliveryMenu;
            $menu->init();    
		} else $tpl = '//merchant/charges_table';
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>$tpl,
			'widget'=>'WidgetDeliveryMenu',		
			'avatar'=>'',		
			'params'=>$params_model,
			'menu'=>$menu			
		));
	}
	
	public function actionchargestable_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Charges Rates") : t("Update Charges Rates");
		CommonUtility::setMenuActive('.services_settings','.services_delivery_settings');	
		CommonUtility::setSubMenuActive(".services-menu",'.services_charges_table');		
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';	
		
		$merchant_id = intval(Yii::app()->merchant->merchant_id);
		$options = array('merchant_delivery_charges_type');
		$options_data = OptionsTools::find($options,$merchant_id);
		WidgetDeliveryMenu::$charge_type = isset($options_data['merchant_delivery_charges_type'])?$options_data['merchant_delivery_charges_type']:'standard';		         
		
		$units = AttributesTools::unit();
		$merchant = AR_merchant::model()->findByPk( $merchant_id );
		if($merchant){			
			if(!empty($merchant->distance_unit)){
				$new_units[$merchant->distance_unit] = $units[$merchant->distance_unit];			
				$units = $new_units;
			}
		}
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_shipping_rate::model()->find('merchant_id=:merchant_id AND id=:id', 
		     array(':merchant_id'=>$merchant_id, ':id'=>$id ));
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}												
		} else $model=new AR_shipping_rate;
		
		if(isset($_POST['AR_shipping_rate'])){
			
			$model->scenario = 'dynamic';
			
			$model->attributes=$_POST['AR_shipping_rate'];
			if($model->validate()){
				$model->merchant_id = $merchant_id;				
				if($model->save()){
					if(!$update){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/charges_table'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$model->distance_from = $model->distance_from>0? Price_Formatter::convertToRaw($model->distance_from,0) :0;
		$model->distance_to = $model->distance_to>0? Price_Formatter::convertToRaw($model->distance_to,0) :0;
		$model->distance_price = $model->distance_price>0? Price_Formatter::convertToRaw($model->distance_price,2) :'';
		$model->minimum_order = $model->minimum_order>0? Price_Formatter::convertToRaw($model->minimum_order,2) :'';
		$model->maximum_order = $model->maximum_order>0? Price_Formatter::convertToRaw($model->maximum_order,2) :'';
		
		$params_model = array(
		    'model'=>$model,			    
			'avatar'=>'',		    
		    'links'=>array(
	            t("All Rates")=>array(Yii::app()->controller->id.'/charges_table'),        
                $this->pageTitle,
		    ),	   
		    'shipping'=>AttributesTools::ShippingType(),
		    'units'=>$units
		);
		
		
        $menu = array();
		if(Yii::app()->params['isMobile']==TRUE){
			$menu = new WidgetDeliveryMenu;
            $menu->init();    
		}
		
		$this->render("//tpl/submenu_tpl",array(		    
			'template_name'=>"//merchant/chargestable_create",
			'widget'=>'WidgetDeliveryMenu',		
			'avatar'=>'',		
			'params'=>$params_model,
			'menu'=>$menu	
		));
	}
	
	public function actionchargestable_update()
	{
	    $this->actionchargestable_create(true);
	}
	
	public function actionchargestable_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
				
		$model = AR_shipping_rate::model()->find('merchant_id=:merchant_id AND id=:id', 
		array(':merchant_id'=>$merchant_id, ':id'=>$id ));
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/charges_table'));			
		} else $this->render("error");
	}
	
	public function actionsettings_pickup()
	{
		$this->pageTitle = t("Settings");
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$service_code = 'pickup';
				
		$model = AR_shipping_rate::model()->find('merchant_id=:merchant_id AND service_code=:service_code
		', 
		  array(':merchant_id'=>$merchant_id, 		    
		    ':service_code'=>$service_code,
		   ));
		  
		if(!$model){		
			$model = new AR_shipping_rate;
		} 
		
		$merchant_type = MerchantAR::getMerchantType();
		if($merchant_type==1 || $merchant_type==3){
			 $service_id = AR_services::getID('pickup');		 
			 $fee = AR_services_fee::model()->find('merchant_id=:merchant_id AND service_id=:service_id', 
						array(':merchant_id'=>$merchant_id, ':service_id'=>$service_id ));
	         if($fee){         	
	         	$model->merchant_service_fee = Price_Formatter::convertToRaw($fee->service_fee,2,true);
	         }
		}
		
		
		if(isset($_POST['AR_shipping_rate'])){
			$model->scenario = 'fixed';
			$model->attributes=$_POST['AR_shipping_rate'];
			if($model->validate()){
				$model->merchant_id = $merchant_id;				
				$model->service_code = $service_code;
				$model->charge_type = 'fixed';				
				if($model->save()){
					
					if($merchant_type==1 || $merchant_type==3){		
						if(!$fee){
							$fee = new AR_services_fee;			
						} 					
						$fee->service_id = intval($service_id);
						$fee->merchant_id = intval($merchant_id);
						$fee->service_fee = floatval($model->merchant_service_fee);
						$fee->save();	
					}
					
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {				
				Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
			}
		}

		$model->distance_price = $model->distance_price>0? Price_Formatter::convertToRaw($model->distance_price,2) :'';
		$model->minimum_order = $model->minimum_order>0? Price_Formatter::convertToRaw($model->minimum_order,2) :'';
		$model->maximum_order = $model->maximum_order>0? Price_Formatter::convertToRaw($model->maximum_order,2) :'';	
		
		/*$this->render("//merchant/settings_pickup",array(
		    'model'=>$model,	
		    'merchant_type'=>$merchant_type,
		    'links'=>array(
		        t("Services Settings"),
				t("Pickup")
			),	    	
		));*/
		
		$params_model = array(
		    'model'=>$model,			    
			'merchant_type'=>$merchant_type,
		    'links'=>array(
	            t("Pickup")=>array(Yii::app()->controller->id.'/charges_table'),        
                $this->pageTitle,
		    ),	   		    
		);
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){			
			$menu = new WidgetPickupMenu;
            $menu->init();    
		} 
					
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'//merchant/settings_pickup',
			'widget'=>'WidgetPickupMenu',		
			'avatar'=>'',
			'params'=>$params_model,
			'menu'=>$menu		
		));
		
	}
	
	public function actionpickup_instructions()
	{
		$this->pageTitle = t("Instructions");
		CommonUtility::setMenuActive('.services_settings','.services_settings_pickup');		
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
		$meta_name = 'customer_pickup_instructions';
		
		$model = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name",array(
		  ':merchant_id'=>intval($merchant_id),
		  ':meta_name'=>$meta_name
		));
		
		if(!$model){
			$model = new AR_merchant_meta;
		}		
		
		if(isset($_POST['AR_merchant_meta'])){
									
			if(DEMO_MODE && in_array($merchant_id,DEMO_MERCHANT)){				
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_merchant_meta'];
			$model->merchant_id = $merchant_id;
			$model->meta_name = $meta_name;
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
		    'label'=>array(
		      'title'=>t("Customer Pickup Instructions"),
		      'sub'=>t("Describe how a customer will pickup their order when they arrive to your store. Instructions will be displayed on a customer's order status page.")
		    ),    
		    'links'=>array(
	            t("Pickup")=>array(Yii::app()->controller->id.'/settings_pickup'),        
                $this->pageTitle,
		    ),	   		    
		);
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){			
			$menu = new WidgetPickupMenu;
            $menu->init();    
		} 
					
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'//merchant/settings_instructions',
			'widget'=>'WidgetPickupMenu',		
			'avatar'=>'',
			'params'=>$params_model,
			'menu'=>$menu		
		));
		
	}
	
	public function actionsettings_dinein()
	{
		$this->pageTitle = t("Settings");
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$service_code = 'dinein';
		
		$model = AR_shipping_rate::model()->find('merchant_id=:merchant_id AND service_code=:service_code
		', 
		  array(':merchant_id'=>$merchant_id, 		    
		    ':service_code'=>$service_code,
		   ));
		  
		if(!$model){		
			$model = new AR_shipping_rate;
		} 
		
		$merchant_type = MerchantAR::getMerchantType();
		 if($merchant_type==1 || $merchant_type==3){
			 $service_id = AR_services::getID('dinein');		 
			 $fee = AR_services_fee::model()->find('merchant_id=:merchant_id AND service_id=:service_id', 
						array(':merchant_id'=>$merchant_id, ':service_id'=>$service_id ));
	         if($fee){         	
	         	$model->merchant_service_fee = Price_Formatter::convertToRaw($fee->service_fee,2,true);
	         }
		 }
		
		if(isset($_POST['AR_shipping_rate'])){
			$model->scenario = 'fixed';
			$model->attributes=$_POST['AR_shipping_rate'];
			if($model->validate()){
				$model->merchant_id = $merchant_id;				
				$model->service_code = $service_code;
				$model->charge_type = 'fixed';				
				if($model->save()){
					
					if($merchant_type==1 || $merchant_type==3){		
						if(!$fee){
							$fee = new AR_services_fee;			
						} 					
						$fee->service_id = intval($service_id);
						$fee->merchant_id = intval($merchant_id);
						$fee->service_fee = floatval($model->merchant_service_fee);
						$fee->save();	
					}
					
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {				
				Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
			}
		}

		$model->distance_price = $model->distance_price>0? Price_Formatter::convertToRaw($model->distance_price,2) :'';
		$model->minimum_order = $model->minimum_order>0? Price_Formatter::convertToRaw($model->minimum_order,2) :'';
		$model->maximum_order = $model->maximum_order>0? Price_Formatter::convertToRaw($model->maximum_order,2) :'';	
				
		/*$this->render("//merchant/settings_dinein",array(
		    'model'=>$model,	
		    'merchant_type'=>$merchant_type,
		    'links'=>array(
		        t("Services Settings"),
				t("Dinein")
			),	    	
		));*/
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){			
			$menu = new WidgetDineinMenu;
            $menu->init();    
		} 
				
		$params_model = array(
		    'model'=>$model,			    
		    'merchant_type'=>$merchant_type,
		    'links'=>array(
	            t("Dinein")=>array(Yii::app()->controller->id.'/settings_dinein'),        
                $this->pageTitle,
		    ),	   		    
		);		
				
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'//merchant/settings_dinein',
			'widget'=>'WidgetDineinMenu',		
			'avatar'=>'',
			'params'=>$params_model,
			'menu'=>$menu		
		));
		
	}
	
	public function actiondinein_instructions()
	{
		$this->pageTitle = t("Instructions");
		CommonUtility::setMenuActive('.services_settings','.services_settings_dinein');		
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
		$meta_name = 'customer_dinein_instructions';
		
		$model = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name",array(
		  ':merchant_id'=>intval($merchant_id),
		  ':meta_name'=>$meta_name
		));
		
		if(!$model){
			$model = new AR_merchant_meta;
		}		
		
		if(isset($_POST['AR_merchant_meta'])){
									
			if(DEMO_MODE && in_array($merchant_id,DEMO_MERCHANT)){				
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
			
			$model->attributes=$_POST['AR_merchant_meta'];
			$model->merchant_id = $merchant_id;
			$model->meta_name = $meta_name;
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
		    'label'=>array(
		      'title'=>t("Customer Dinein Instructions"),
		      'sub'=>t("Describe how customer will dinein in your restaurant. Instructions will be displayed on a customer's order status page")
		    ),
		    'links'=>array(
	            t("Dinein")=>array(Yii::app()->controller->id.'/dinein_instructions'),        
                $this->pageTitle,
		    ),	   		    
		);
		
		$menu = array();
		if(Yii::app()->params['isMobile']==TRUE){			
			$menu = new WidgetDineinMenu;
            $menu->init();    
		} 
					
		$this->render("//tpl/submenu_tpl",array(
		    'model'=>$model,
			'template_name'=>'//merchant/settings_instructions',
			'widget'=>'WidgetDineinMenu',		
			'avatar'=>'',
			'params'=>$params_model,
			'menu'=>$menu		
		));
		
	}	
	
	
}
/*end class*/