<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>array(
    t("Site configuration")=>array('admin/site_information')    
),
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>

<div class="card">

  <div class="card-body">
    
  <div class="row">
    <div class="col-md-3">
       <div class="d-none d-md-block attributes-menu-wrap">
          <?php $this->widget('application.components.WidgetSiteSetup',array());?>
       </div>
       <div class="d-block d-md-none text-right">
         
       <div class="dropdown btn-group dropleft">
		      <button class="btn btn-sm dropdown-togglex dropleft" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		       <i class="zmdi zmdi-more-vert"></i>
		     </button>
         <div class="dropdown-menu dropdown-menu-mobile" aria-labelledby="dropdownMenuButton">
           <?php $this->widget('application.components.WidgetSiteSetup',array());?>
         </div>         
       </div> 

       </div>
    </div> <!--col-->
    <div class="col-md-9">    
     <?php echo $this->renderPartial($template_name, $params); ?>      
    </div>
  </div> <!--row-->  
  
  </div> <!--card-body-->

</div> <!--card-->