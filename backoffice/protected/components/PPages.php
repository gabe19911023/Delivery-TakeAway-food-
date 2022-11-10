<?php
class PPages
{
	public static function menuType()
	{
		return 'website';
	}

	public static function menuMerchantType()
	{
		return 'website_merchant';
	}
	
	public static function menuActiveKey()
	{
		return 'theme_menu_active';
	}
	
	public static function all($lang='')
	{
		$data = CommonUtility::getDataToDropDown("{{pages_translation}}",'page_id','title',
    	"where language=".q($lang)." 
    	and title IS NOT NULL AND TRIM(title) <> ''
    	and page_id IN (
    	  select page_id from {{pages}}
    	  where page_type='page'
    	  and status='publish'
		  and owner='admin'
    	)
    	"
    	);
    	return $data;
	}

	public static function merchantPages($lang='',$merchant_id=0)
	{
		$data = CommonUtility::getDataToDropDown("{{pages_translation}}",'page_id','title',
    	"where language=".q($lang)." 
    	and title IS NOT NULL AND TRIM(title) <> ''
    	and page_id IN (
    	  select page_id from {{pages}}
    	  where page_type='page'
    	  and status='publish'
		  and owner='merchant'
		  and merchant_id=".q(intval($merchant_id))."
    	)
    	"
    	);
    	return $data;
	}
	
	public static function get($page_id=0)
	{
		$model = AR_pages::model()->findByPk( intval($page_id) );
		if($model){
			return $model;
		}
		throw new Exception( 'page not found' );
	}
	
	public static function pageDetailsSlug($slug='', $lang='')
	{
		$criteria=new CDbCriteria();
		$criteria->alias ="a";
		$criteria->select="a.title, a.long_content, a.meta_title ,a.meta_description,a.meta_keywords,
		b.meta_image,b.path
		";
		$criteria->join='LEFT JOIN {{pages}} b on a.page_id = b.page_id ';
		$criteria->condition = "a.language=:language AND b.slug=:slug AND a.title IS NOT NULL AND TRIM(a.title) <> ''";
		$criteria->params = array(
		  ':language'=>$lang,
		  ':slug'=>$slug
		);		
		$dependency = CCacheData::dependency();
		$model = AR_pages_translation::model()->cache(Yii::app()->params->cache, $dependency)->find($criteria);
		if($model){
			return $model;
		}
		throw new Exception( 'page not found' );
	}	
	
}
/*end class*/