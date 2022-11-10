<div class="container mt-5 mb-5">
 
<div class="row">
  <div class="col-md-8 border-right">
     
   <div class="text-center mb-4">
      <h3><?php echo Yii::app()->input->xssClean($model->title)?></h3>  
   </div>
   
   <div class="text-left">
     <?php echo Yii::app()->input->xssClean($model->long_content)?>
  </div>
  
  
  </div> <!--col-->
  <div class="col-md-4"> 
  
  </div> <!--col-->
</div>
<!--row--> 
</div> <!--container-->


<div class="container-fluid m-0 p-0 full-width">
 <?php $this->renderPartial("//store/join-us")?>
</div>
  

<div id="vue-carousel" class="container">

   <!--COMPONENTS FEATURED LOCATION-->
  <component-carousel
  title="<?php echo t("Try something new in")?>"
  featured_name="best_seller"
  :settings="{
      theme: '<?php echo CJavaScript::quote('rounded')?>',       
      items: '<?php echo CJavaScript::quote(5)?>', 
      lazyLoad: '<?php echo CJavaScript::quote(true)?>', 
      loop: '<?php echo CJavaScript::quote(true)?>', 
      margin: '<?php echo CJavaScript::quote(15)?>', 
      nav: '<?php echo CJavaScript::quote(false)?>', 
      dots: '<?php echo CJavaScript::quote(false)?>', 
      stagePadding: '<?php echo CJavaScript::quote(10)?>', 
      free_delivery: '<?php echo CJavaScript::quote( t("Free delivery") )?>', 
  }"
  :responsive='<?php echo json_encode($responsive);?>'
  />
  </component-carousel>  
  <!--COMPONENTS FEATURED LOCATION-->

</div> <!--vue-carousel-->  