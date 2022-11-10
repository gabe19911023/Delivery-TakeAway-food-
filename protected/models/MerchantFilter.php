<?php
class MerchantFilter extends CFormModel
{
	public $services;
	public $sort;
	public $cuisine;
	
	public function attributeLabels()
	{
		return array(
		  'services'=>t("All")
		);
	}
	
}
/*end class*/