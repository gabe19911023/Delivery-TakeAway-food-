<?php
class CCuisine
{
	public static function getList($lang=KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.cuisine_id,a.slug, b.cuisine_name, a.featured_image, a.path
		FROM {{cuisine}} a		
		LEFT JOIN {{cuisine_translation}} b
		ON
		a.cuisine_id = b.cuisine_id
		WHERE b.language=".q($lang)."
		AND b.cuisine_name IS NOT NULL AND TRIM(b.cuisine_name) <> ''
		ORDER BY a.cuisine_id ASC
		";				
		$depency = CCacheData::dependency();		
		if($res = Yii::app()->db->cache(Yii::app()->params->cache, $depency )->createCommand($stmt)->queryAll() ){						
			$data = array();
			foreach ($res as $val) {
				
				$val['featured_image'] =  CMedia::getImage($val['featured_image'],$val['path'],
				Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('item'));
				
				$val['url'] = Yii::app()->createAbsoluteUrl("cuisine/".$val['slug']);
				
				$data[]=$val;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
}
/*end class*/