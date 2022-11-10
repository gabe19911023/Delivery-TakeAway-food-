<?php
class ScriptUtility
{
	
	public static function registerJS($data=array(),$position=CClientScript::POS_END)
	{
		$cs = Yii::app()->getClientScript();
		if(is_array($data) && count($data)>=1){
			foreach ($data as $link) {				
				//Yii::app()->clientScript->registerScriptFile($link,CClientScript::POS_END);
				Yii::app()->clientScript->registerScriptFile($link,$position);
			}
		}		
	}
	
	public static function registerCSS($data=array())
	{		
		$cs = Yii::app()->getClientScript();		
		if(is_array($data) && count($data)>=1){
			foreach ($data as $link) {
				$cs->registerCssFile($link);
			}
		}		
	}	
	
    public static function registerScript($script=array(), $script_name='reg_script', $pos_head=CClientScript::POS_HEAD)
	{
		$reg_script='';
		if(is_array($script) && count($script)>=1){		
			foreach ($script as $val) {
				$reg_script.="$val\n";
			}
			$cs = Yii::app()->getClientScript(); 
			$cs->registerScript(
			  $script_name,
			  "$reg_script",
			  $pos_head
			);		
		}
	}	
	
}
/*end class*/