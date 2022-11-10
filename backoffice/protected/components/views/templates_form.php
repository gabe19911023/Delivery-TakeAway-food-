<!--TRANSLATION-->
<div class="card mt-3">
  <div class="card-body">  
  <a class="btn" data-toggle="collapse" href="#<?php echo $this->target?>" role="button">
    <div class="d-flex flex-row">
        <div class="p-2"><?php echo $this->title?></div>
        <div class="p-2"><i class="zmdi zmdi-plus"></i></div>
    </div>
  </a>
  
<div class="collapse" id="<?php echo $this->target?>">
  <div class="card card-body">
 
<?php 
if(is_array($this->data) && count($this->data)>=1){	
	foreach ($this->data as $key=>$data) {		
		$this->model[$key] = $data;
	}
}
$x=0;
?>  

<?php foreach ($this->language as $lang=>$lval):?>

<DIV class="mb-3"> 
<h6 class="mb-4"><?php echo t("{{lang}} Template",array('{{lang}}'=>t($lval)))?></h6>

<?php foreach ($this->field as $field): 
$field_name=isset($field['name'])?$field['name']:'';
$default_placeholder = t("Enter {{lang}} name here",array('{{lang}}'=>t($lval)));
if(isset($field['placeholder'])){
	$default_placeholder  = !empty($field['placeholder'])? t($field['placeholder'],array('{{lang}}'=>t($lval))) :$default_placeholder;
}
$class = isset($field['class'])?$field['class']:'';
$type = isset($field['type'])?$field['type']:'text';
$value = isset($field['value'])?$field['value']:'';
?>

<?php if($type=="text"):?>
<div class="form-label-group">    
  <?php echo $this->form->textField($this->model,$field_name."[$lang]",array(
     'class'=>"form-control form-control-text ".$class,
     'placeholder'=>""     
   )); ?>   
   <?php 
   if($field['label']==true):
   echo $this->form->label($this->model,$field_name."[$lang]",array(
    'label'=>$default_placeholder
   ));
   endif;
    ?>   
</div>
<?php elseif ($type=="hidden"):?>

<?php echo $this->form->hiddenField($this->model,$field_name."[$lang]",array(
     'class'=>"form-control form-control-text ".$class,
     'value'=>$value
   )); ?>   

<?php else:?>
<div class="form-label-group">    
  <?php echo $this->form->textArea($this->model,$field_name."[$lang]",array(
     'class'=>"form-control form-control-text ".$class,
     'placeholder'=>""
   )); ?>   
   <?php 
   if($field['label']==true):
   echo $this->form->label($this->model,$field_name."[$lang]",array(
    'label'=>$default_placeholder
   ));
   endif;
    ?>   
</div>
<?php endif;?>

<?php endforeach;?>
</DIV>
<?php endforeach;?>

  </div> <!--card body-->
</div>
<!--collapse-->

  </div> <!--body-->
</div> <!--card-->
<!--TRANSLATION-->
  