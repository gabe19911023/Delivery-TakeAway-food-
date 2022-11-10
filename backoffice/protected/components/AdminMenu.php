<?php
class AdminMenu
{	 	 	 
	 public static $items = array();
	 
	 public static function buildMenu($parent_id=0,$with_visible=false,$role_id=0,$menu_type='admin')
	 {	 	 
	 	  $criteria=new CDbCriteria();
	 	  $sub_query = '';
	 	  if($role_id>0){
	 	  	  $sub_query="
	 	  	  AND a.action_name IN (
		 	 	  select action_name from {{role_access}}
		 	 	  where role_id=".q($role_id)."
		 	   )
	 	  	  ";
	 	  }
	 	  
	 	  $criteria->alias="a";
	 	  $criteria->condition="a.menu_type=:menu_type AND a.status=:status AND a.parent_id=:parent_id $sub_query";
	 	  $criteria->params = array(
	 	    ':menu_type'=>$menu_type,
	 	    ':status'=>1,
	 	    ':parent_id'=>intval($parent_id),
	 	  );
	 	  
	 	  if($with_visible){
	 	  	 $criteria->addInCondition('a.visible', array(0,1) );
	 	  } else $criteria->addInCondition('a.visible', array(1) );
	 	  
	 	  $criteria->order="sequence ASC";
	 	  	 	  
	 	  $dependency = CCacheData::dependency();	 	  
	 	  if($model = AR_menu::model()->cache(Yii::app()->params->cache, $dependency)->findAll($criteria)){
	 	  	 foreach ($model as $items) {
	 	  	     $class_li = str_replace(".","_",$items->action_name);		 	  	     
	 	  	     if($items->parent_id>0){
	 	 			self::$items[ $items->parent_id ]['items'][] = array(
	 	 			  'label'=>t($items->menu_name),
	 	 		      'url'=>!empty($items->link)?array($items->link):'javascript:;',
	 	 		      'action_name'=>$items->action_name,
	 	 		      'itemOptions'=>array('class'=>"position-relative ".$class_li,'ref'=>$class_li)
	 	 			);
	 	 		} else self::$items[$items->menu_id] = array(
	 	 		   'label'=>t($items->menu_name),
	 	 		   'url'=>!empty($items->link)?array($items->link):'javascript:;',
	 	 		   'action_name'=>$items->action_name,
	 	 		   'itemOptions'=>array('class'=>$class_li)
	 	 		);	 	 	
	 	 			 	  	
	 	 		$sub=self::buildMenu($items->menu_id,$with_visible,$role_id,$menu_type);     
	 	  	 }
	 	  }	 	  
	 }
	 
}
/*end class*/