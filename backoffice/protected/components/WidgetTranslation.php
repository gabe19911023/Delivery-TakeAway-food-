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
class WidgetTranslation extends CWidget 
{
	public $form;
	public $model;
	public $language;
	public $field;
	public $data;
	
	public function run() {
		$this->render('translation_form');
	}
	
}
/*end class*/