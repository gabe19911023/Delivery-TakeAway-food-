<?php
class CComponentsManager
{
	
	public static function RegisterBundle($data = array(), $prefix='' )
	{
		$components_bundle = ''; $components_name_js=''; $components_name=''; $pascalName='';
		
		$baseUrl = Yii::app()->baseUrl; 
		
		$cs = Yii::app()->getClientScript();
				
		$path=Yii::getPathOfAlias('modules_dir');				
		
		if(is_array($data) && count($data)>=1){
			
			$cs->registerScriptFile($baseUrl."/assets/js/components-var.js"
			,CClientScript::POS_HEAD); 
						
			foreach ($data as $val) {				
				$payment_code = trim($val['payment_code']);
				$components_name_js = "components-".$prefix.$val['payment_code'].".js";				
				$components_name = "components-".$prefix.$val['payment_code'];
				
				if(!empty($prefix)){
					 $pascalPrefix = str_replace("-","",$prefix);
					 $pascalName = "components".$pascalPrefix.ucwords($val['payment_code']);			
				} else $pascalName = "components".ucwords($val['payment_code']);							
								
				if( file_exists($path."/$payment_code/assets/js/$components_name_js") ){					
					
					$components_bundle.="\n";
					$components_bundle.="'$components_name':$pascalName,";
					
					$cs->registerScriptFile($baseUrl."/protected/modules/$payment_code/assets/js/$components_name_js"
			        ,CClientScript::POS_HEAD); 	
				} 
			} /*end for*/
						
			if(!empty($components_bundle)){
				$components_bundle = substr($components_bundle,0,-1);			
				$components_bundle.="\n";
				ScriptUtility::registerScript(array(			  
				  "const components_bundle = {".$components_bundle."};",
				),'components-bundle');
			}
			
		} //endif
	}
	
	public static function renderComponents($data=array(),$credentials=array(),$widget='' , $prefix='')
	{		
		$path=Yii::getPathOfAlias('modules_dir');	
		
		if(is_array($data) && count($data)>=1){
			foreach ($data as $val) {				
				$components_name = $val['payment_code'];				
				if(Yii::app()->hasModule($components_name)){					   
				   Yii::import($components_name.'.components');
				   $render_components_name = $components_name."Components";					   
				   if(!empty($prefix)){
				   	   $render_components_name = $prefix.$render_components_name;
				   }
				   $components_path = $path."/$components_name/components/$render_components_name.php";						   	  
				   if(file_exists($components_path)){						   	   				   	   
					   $widget->widget($components_name.'.components.'.$render_components_name,array(					   
					     'data'=>$val,
					     'credentials'=>isset($credentials[$components_name])?$credentials[$components_name]:''
					   ));
				   }
				}
			}
		}
	}
	
}
/*end class*/