<?php
class BookingController extends Commonmerchant
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
	
	public function actionlist()
	{
		$this->pageTitle=t("Booking List");
		$action_name='booking_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//merchant/booking_list';
		
		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create")
		));	
	}
	
    public function actioncreate($update=false)
	{
		$this->pageTitle = $update==false? t("Add Booking") : t("Update Credit Card");
		CommonUtility::setMenuActive('.booking','.booking_list');			
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id='';		
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_booking::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}									
		} else {			
			$model=new AR_booking;			
		}

		if(isset($_POST['AR_booking'])){
			$model->attributes=$_POST['AR_booking'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
							
		$this->render("//buyer/booking_create",array(
		    'model'=>$model,	
		    'hide_nav'=>false,
		    'status_list'=>(array)AttributesTools::StatusManagement('booking'),
		    'links2'=>array(
	            t("All Booking")=>array(Yii::app()->controller->id.'/list'),        
                $this->pageTitle,
		    ),	    		    
		));
	}	
	
	public function actionupdate()
	{
		$this->actioncreate(true);
	}
	
	public function actiondelete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$model = AR_booking::model()->find("booking_id=:booking_id AND merchant_id=:merchant_id",array(
		  ':booking_id'=>$id,
		  ':merchant_id'=>$merchant_id
		));		
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/list'));			
		} else $this->render("error");
	}
	
	public function actionsettings()
	{
		$this->pageTitle=t("Booking Settings");
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );
		
		if(!$merchant){
			$this->render("error");
			Yii::app()->end();
		}
		
		$avatar = CommonUtility::getPhoto($merchant->logo, CommonUtility::getPlaceholderPhoto('merchant_logo'));			
		
		$model=new AR_option;
		$model->scenario = "booking_settings";
		
		$options = array(
		   'enabled_merchant_table_booking','accept_booking_sameday','fully_booked_msg',
		   'enabled_merchant_booking_alert','merchant_booking_receiver'
		);
		
		 if(isset($_POST['AR_option'])){
				$model->attributes=$_POST['AR_option'];
				if($model->validate()){				
					OptionsTools::$merchant_id = $id;
					if(OptionsTools::save($options, $model, $id)){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
						$this->refresh();
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				}
			}
			
			if($data = OptionsTools::find($options,$id)){
				foreach ($data as $name=>$val) {
					$model[$name]=$val;
				}			
			}
		    
			$this->render("//admin/submenu_tpl",array(
			    'model'=>$merchant,
				'template_name'=>"//merchant/booking_settings",
				'widget'=>'WidgetBookingSettings',		
				'avatar'=>$avatar,
				'params'=>array(  
				   'model'=>$model,			   
				   'links'=>array(	
				     t("Booking Settings")=>array(Yii::app()->controller->id.'/settings'),		        
		             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
				   ),
				   //
				 )
			));
	}
	
	public function actiontime_slot()
	{
		$this->pageTitle=t("Time Slot");
		CommonUtility::setMenuActive('.booking',".booking_settings");
		
		$action_name='timeslot_booking';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/delete_timeslot");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		if(Yii::app()->params['isMobile']==TRUE){			
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//merchant/timeslot_booking';
		
		
		$id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $id );		
		$avatar = CommonUtility::getPhoto($merchant->logo, CommonUtility::getPlaceholderPhoto('merchant_logo'));			
		
		$this->render("//admin/submenu_tpl",array(
		    'model'=>$merchant,
			'template_name'=>$tpl,
			'widget'=>'WidgetBookingSettings',		
			'avatar'=>$avatar,
			'params'=>array(  			
			    'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/timeslot_create"),
			   'links'=>array(	
			     t("Booking Settings")=>array(Yii::app()->controller->id.'/settings'),		        
			     t("Time Slot")=>array(Yii::app()->controller->id.'/time_slot'),	
	             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
			   ),
			   //
			 )
		));
	}
	
	public function actiontimeslot_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Time Slot") : t("Update Time Slot");
		CommonUtility::setMenuActive('.booking',".booking_settings");
		CommonUtility::setSubMenuActive(".booking-settings",'.store-hours');
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$merchant = AR_merchant::model()->findByPk( $merchant_id );		
		$avatar = CommonUtility::getPhoto($merchant->logo, CommonUtility::getPlaceholderPhoto('merchant_logo'));			
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_timeslot_booking::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}											
		} else {			
			$model=new AR_timeslot_booking;		
			$model->mtid = 	$merchant_id;
		}
		
		if(isset($_POST['AR_timeslot_booking'])){
			$model->attributes=$_POST['AR_timeslot_booking'];
			if($model->validate()){		
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
					   $this->redirect(array(Yii::app()->controller->id.'/time_slot'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}
		
		$this->render("//admin/submenu_tpl",array(
		    'model'=>$merchant,
			'template_name'=>"//merchant/timeslot_create",
			'widget'=>'WidgetBookingSettings',		
			'avatar'=>$avatar,
			'params'=>array(  		
			   'model'=>$model,		
			   'days'=>AttributesTools::dayList(),
			   'links'=>array(	
			     t("Booking Settings")=>array(Yii::app()->controller->id.'/settings'),		        
			     t("Time Slot")=>array(Yii::app()->controller->id.'/time_slot'),	
	             isset($merchant->restaurant_name)?stripslashes(ucwords($merchant->restaurant_name)):''	            
			   ),
			   //
			 )
		));
	}
	
	public function actiontimeslot_update()
	{
		$this->actiontimeslot_create(true);
	}
	
	public function actiondelete_timeslot()
	{
		$id = (integer) Yii::app()->input->get('id');		
		$merchant_id = Yii::app()->merchant->merchant_id;			
		$model = AR_timeslot_booking::model()->find("id=:id AND merchant_id=:merchant_id",array(
		  ':id'=>$id,
		  ':merchant_id'=>$merchant_id
		));		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/time_slot'));			
		} else $this->render("error");
	}
	
}
/*end class*/