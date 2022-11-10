<?php
/*
@parameters
$fields[]=array(
  'name'=>'dish_name_trans'
);
Array
(
    [0] => Array
        (
            [name] => cuisine_name_trans
        )

)

$data 
$data['dish_name_trans'] = 
Array
(
    [ar] => ??????
    [en] => 
    [jp] => ???
)

$language =
Array
(
    [ar] => ar
    [en] => en
    [jp] => jp
)
*/
class WidgetTemplates extends CWidget 
{
	public $title;
	public $form;
	public $model;
	public $language;
	public $field;
	public $data;	
	public $target;
	
	public function run() {
		$this->render('templates_form');
	}
	
}
/*end class*/