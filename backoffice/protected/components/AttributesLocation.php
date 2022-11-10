<?php
class AttributesLocation
{
	
	public static function StateList($country_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{location_states}}",'state_id','name',
		"WHERE country_id=".q($country_id)." ","ORDER BY name ASC");
		return $list;
	}
	
	public static function CityList($country_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{location_cities}} a",'city_id','name',
		"WHERE state_id IN (
		  select state_id from {{location_states}}
		  where country_id = ".q($country_id)."
		)
		","ORDER BY name ASC");
		return $list;
	}
}
/*END CLASS*/