<div class="custom-control custom-switch custom-switch-md">  
  <?php
  echo CHtml::checkBox($id,
  $check
  ,array(
    'id'=>$id,
    'class'=>"custom-control-input checkbox_child ".$class,
    'value'=>$value
  ));
  ?>   
  <label class="custom-control-label" for="<?php echo $id?>">
   <?php echo t($label)?>
  </label>
</div>    