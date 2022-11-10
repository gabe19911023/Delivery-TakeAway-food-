<?php
class SupplierController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
			
		return true;
	}
		
	public function actionIndex()
	{	
		$this->redirect(array(Yii::app()->controller->id.'/list'));		
	}	
	
	public function actionlist()
	{	   
		$this->pageTitle=t("Supplier List");
		
		$table_col = array(		  
		  'supplier_id'=>array(
		    'label'=>t(""),
		    'width'=>'8%'
		  ),
		  'supplier_name'=>array(
		    'label'=>t("Name"),
		    'width'=>'15%'
		  ),
		  'contact_name'=>array(
		    'label'=>t("Contacts"),
		    'width'=>'15%'
		  ),		  
		  'created_at'=>array(
		    'label'=>t("Actions"),
		    'width'=>'10%'
		  ),		  
		);
		$columns = array(		  
		  array('data'=>'supplier_id','visible'=>false),
		  array('data'=>'supplier_name'),
		  array('data'=>'contact_name'),
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_edit normal btn btn-light tool_tips"><i class="zmdi zmdi-border-color"></i></a>
			    <a class="ref_delete normal btn btn-light tool_tips"><i class="zmdi zmdi-delete"></i></a>
			 </div>
		     '
		  ),	 
		);			
		
		$this->render('supplier_list',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>0,
          'sortby'=>'desc',
		  'transaction_type'=>array(),	
		  'link'=>Yii::app()->createUrl('supplier/create'),
		));
		
	}
	
	public function actioncreate($update=false)
	{
		$this->pageTitle = $update==false? t("Add Supplier") : t("Update Supplier");		
		CommonUtility::setMenuActive('.inventory_management','.supplier_list');			
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;

		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_supplier::model()->find("merchant_id=:merchant_id AND supplier_id=:supplier_id",array(
			  ':merchant_id'=>$merchant_id,
			  ':supplier_id'=>$id
			));
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}												
		} else $model=new AR_supplier;
							
		if(isset($_POST['AR_supplier'])){
			$model->attributes=$_POST['AR_supplier'];
			if($model->validate()){	
				$model->merchant_id = $merchant_id;
				if($model->save()){
					if(!$update){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
						
		$this->render("create_supplier",array(
		    'model'=>$model,		
		    'country_list'=>AttributesTools::CountryList(),
		    'links'=>array(
	            t("Supplier List")=>array(Yii::app()->controller->id.'/list'),        
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
		$id = intval(Yii::app()->input->get('id'));	
		$merchant_id = intval(Yii::app()->merchant->merchant_id);		
				
		$model = AR_supplier::model()->find('merchant_id=:merchant_id AND supplier_id=:supplier_id', 
		array(':merchant_id'=>$merchant_id, ':supplier_id'=>$id ));
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/list'));			
		} else $this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t(HELPER_RECORD_NOT_FOUND)
			 )
			));
	}
	
}
/*end class*/