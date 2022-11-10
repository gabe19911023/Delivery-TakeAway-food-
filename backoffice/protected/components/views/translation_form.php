<!--TRANSLATION-->
<div id="translation-container" class="card mt-3" >
  <div class="card-body">  
  <a class="btn" data-toggle="collapse" href="#translation" role="button">
    <div class="d-flex flex-row">
        <div class="p-2"><?php echo t("Item translations")?></div>
        <div class="p-2"><i class="zmdi zmdi-plus"></i></div>
    </div>
  </a>
  
<div class="collapse" id="translation">
  <div class="card card-body p-4">
 
<?php 
if(is_array($this->data) && count($this->data)>=1){	
	foreach ($this->data as $key=>$data) {		
		$this->model[$key] = $data;
	}
}
?>  

<?php foreach ($this->language as $lang=>$lval):?>

<h6 class="mb-4"><?php echo t("{{lang}} translation",array('{{lang}}'=>t($lval)))?></h6>

<?php foreach ($this->field as $field): 
$field_name=isset($field['name'])?$field['name']:'';
$default_placeholder = t("Enter {{lang}} name here",array('{{lang}}'=>t($lval)));
if(isset($field['placeholder'])){
	$default_placeholder  = !empty($field['placeholder'])? t($field['placeholder'],array('[lang]'=>t($lval))) :$default_placeholder;
}
$field_type = isset($field['type'])?$field['type']:'';
$field_class = isset($field['class'])?$field['class']:'';
?>
<div class="form-label-group">    
   
   <?php if($field_type=="textarea"):?>
      <p class="m-0 mb-1"><?php echo $default_placeholder?></p>
      <?php echo $this->form->textArea($this->model,$field_name."[$lang]",array(
	     'class'=>"form-control form-control-text $field_class",     
	     'placeholder'=>""
	   )); ?>             
   <?php else :?>
	   <?php echo $this->form->textField($this->model,$field_name."[$lang]",array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>""
	   )); ?>   
	   <?php echo $this->form->label($this->model,$field_name."[$lang]",array(
	    'label'=>$default_placeholder
	   )); ?>   
   <?php endif;?>

</div>
<?php endforeach;?>
<?php endforeach;?>

  </div> <!--card body-->
</div>
<!--collapse-->

  </div> <!--body-->
</div> <!--card-->
<!--TRANSLATION-->
  