<?php
class TThemeManager
{
	
	public static function getSiteTheme($path='', $url='')
	{
		$themes = array(); 		
		if(file_exists($path)){
			if($folder = scandir($path)){
				foreach ($folder as $item) {					
					if ($item != '..' && $item != '.' && is_dir($path . "/" . $item)){						
						$themes[] = array(
						  'theme_name'=>$item,
						  'screenshot'=>$url."/$item/assets/images/screenshot.png"
						);
					}
				}
			}
			return $themes;
		}
		throw new Exception( 'No themes installed' );
	}
	
}
/*end class*/