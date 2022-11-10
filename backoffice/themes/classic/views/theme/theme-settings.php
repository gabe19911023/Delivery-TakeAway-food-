<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs',$links);
?>
</nav>

<DIV class="card">
 <div class="card-body">
 
 
 <div class="row">
   <div class="col-md-3">

     <a href="<?php echo Yii::app()->createUrl('/theme/menu')?>" class="menu-boxes">
     <div class="d-flex align-items-center">
       <div class="mr-2">
         <div class="bg-light rounded p-3"><i class="zmdi zmdi-menu"></i></div>
       </div>
       <div>
         <h6 class="m-0"><?php echo t("Menu")?></h6>
         <p class="text-muted font11 m-0"><?php echo t("Organize your menu")?></p>
       </div>
     </div>  
     </a>
   
   </div>
 </div>
 <!--row-->
 
  
</div>
  
 </div>
</DIV>