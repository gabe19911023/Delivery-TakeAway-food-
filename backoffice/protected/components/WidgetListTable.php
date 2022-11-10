<?php
class WidgetListTable extends CWidget 
{
	public $id,$data,$logo,$title,$description,
	$sub_string,$status_title,$status,$logo_type,$badge_type,$view_url;	
	
	public function run() {		
		$this->render('list_table');
	}
	
}
/*end class*/