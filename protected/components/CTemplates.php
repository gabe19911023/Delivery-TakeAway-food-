<?php
class CTemplates
{	
	public static function get( $id='', $template_type=array(), $lang=KMRS_DEFAULT_LANGUAGE)
	{				
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select = "a.template_id,a.template_type,a.language,a.title,a.content, b.enabled_email,b.enabled_sms,b.enabled_push";
		$criteria->join='LEFT JOIN {{templates}} b on  a.template_id = b.template_id ';
		$criteria->condition = "a.template_id=:template_id AND a.language=:language";
		$criteria->params = array(		  
		  ':template_id'=>$id,  
		  ':language'=>$lang
		);
		$criteria->addInCondition('template_type', (array) $template_type );	
		$model=AR_templates_translation::model()->findAll($criteria);	
		if($model){
			$data = array();
			foreach ($model as $item) {				
				$data[]=array(
				  'template_type'=>$item->template_type,
				  'enabled_email'=>$item->enabled_email,
				  'enabled_sms'=>$item->enabled_sms,
				  'enabled_push'=>$item->enabled_push,
				  'title'=>$item->title,
				  'content'=>$item->content,
				);
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getMany($ids=array() , $template_type=array(), $lang=KMRS_DEFAULT_LANGUAGE )
	{
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select = "a.template_id,a.template_type,a.language,a.title,a.content, b.enabled_email,b.enabled_sms,b.enabled_push";
		$criteria->join='LEFT JOIN {{templates}} b on  a.template_id = b.template_id ';
		$criteria->condition = "a.language=:language";
		$criteria->params = array(		  
		  ':language'=>$lang
		);
		$criteria->addInCondition('a.template_id', (array) $ids );
		$criteria->addInCondition('a.template_type', (array) $template_type );		
		
		$model=AR_templates_translation::model()->findAll($criteria);
		if($model){
			$data = array();
			foreach ($model as $item) {				
				$data[$item->template_id][]=array(
				  'template_type'=>$item->template_type,
				  'enabled_email'=>$item->enabled_email,
				  'enabled_sms'=>$item->enabled_sms,
				  'enabled_push'=>$item->enabled_push,
				  'title'=>$item->title,
				  'content'=>$item->content,
				);
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
		
}
/*end class*/