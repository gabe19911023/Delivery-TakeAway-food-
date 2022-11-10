<?php
class MMenu extends CComponent
{
	public static $items = array();
	
	public static function get($menu_id=0, $menu_type='')
	{
		$model = AR_menu::model()->find("menu_id=:menu_id AND menu_type=:menu_type",array(
		   ':menu_id'=>intval($menu_id),
		   ':menu_type'=>$menu_type
		 ));
		 if($model){
		 	return $model;
		 }
		 throw new Exception( 'menu not found' );
	}
	
	public static function getMenu($parent_id=0, $menu_type='')
	{
		$criteria=new CDbCriteria();
		$criteria->condition = "parent_id=:parent_id AND menu_type=:menu_type";
		$criteria->params = array(':parent_id'=>intval($parent_id),':menu_type'=>$menu_type);
		$criteria->order="sequence ASC";
		
		$model = AR_menu::model()->findAll($criteria); 				
		if($model){
			$data = array();
			foreach ($model as $items) {
				//$data[$items->menu_id] = array(
				$data[] = array(
				  'menu_id'=>$items->menu_id,
				  'menu_name'=>$items->menu_name,
				  'link'=>$items->link,
				  'sequence'=>$items->sequence,
				);
			}
			return $data;
		}
		throw new Exception( 'no found menu' );
	}
	
	 public static function buildMenu($parent_id=0,$with_visible=false,$menu_type='admin',$meta_value=0)
	 {	 	 
	 	  $criteria=new CDbCriteria();
		  $condition=''; $params=array();
	 	  
	 	  $criteria->alias="a";
		  $condition = "a.menu_type=:menu_type AND a.status=:status AND a.parent_id=:parent_id";			
		  $params = array(
			':menu_type'=>$menu_type,
			':status'=>1,
			':parent_id'=>intval($parent_id),
		  );              
		  if($meta_value>0){
			 $condition.=" AND meta_value1=:meta_value1";
			 $params[':meta_value1']=intval($meta_value);
		  }

		  $criteria->condition=$condition;
		  $criteria->params = $params;		  
	 	  
	 	  if($with_visible){
	 	  	 $criteria->addInCondition('a.visible', array(0,1) );
	 	  } else $criteria->addInCondition('a.visible', array(1) );
	 	  
	 	  $criteria->order="sequence ASC";
		  	 	  
	 	  $dependency = CCacheData::dependency();	 	  
	 	  if($model = AR_menu::model()->cache(Yii::app()->params->cache, $dependency)->findAll($criteria)){
	 	  	 foreach ($model as $items) {
	 	  	     $class_li = str_replace(".","_",$items->action_name);		 	  	     
	 	  	     
	 	  	     if(!empty($items->link)){
	 	  	     	$items->link = t($items->link,array(
	 	  	     	 '{{site_url}}'=>CMedia::homeUrl()
	 	  	     	));
	 	  	     }
	 	  	     
	 	  	     if($items->parent_id>0){
	 	 			self::$items[ $items->parent_id ]['items'][] = array(
	 	 			  'label'=>$items->menu_name,
	 	 		      'url'=>!empty($items->link)?$items->link:'javascript:;',
	 	 		      'action_name'=>$items->action_name,
	 	 		      'itemOptions'=>array('class'=>"position-relative ".$class_li,'ref'=>$class_li)
	 	 			);
	 	 		} else self::$items[$items->menu_id] = array(
	 	 		   'label'=>$items->menu_name,
	 	 		   'url'=>!empty($items->link)?$items->link:'javascript:;',
	 	 		   'action_name'=>$items->action_name,
	 	 		   'itemOptions'=>array('class'=>$class_li)
	 	 		);	 	 	
	 	 			 	  	
	 	 		$sub=self::buildMenu($items->menu_id,$with_visible,$menu_type);     
	 	  	 }
	 	  }	 	  
	 }
	
}
/*end class*/