<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$params['links'],
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
        
      <div class="d-none d-md-block">
        <?php if(!empty($avatar)):?>
        <div class="preview-image mb-2">
        <div class="col-lg-7">
          <img src="<?php echo $avatar?>" class="img-fluid mb-2 rounded-circle img-120">
        </div>     
        </div>
        <?php else :?>
        <div class="mt-3"></div>
        <?php endif;?>

        <div class="attributes-menu-wrap">
	        <?php $this->widget('application.components.'.$widget,array());?>
	      </div>	
      </div>

      <div class="d-block d-md-none text-right">
      
      <div class="dropdown btn-group dropleft">
		      <button class="btn btn-sm dropdown-togglex dropleft" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		       <i class="zmdi zmdi-more-vert"></i>
		     </button>
         <div class="dropdown-menu dropdown-menu-mobile" aria-labelledby="dropdownMenuButton">
            <?php $this->widget('application.components.'.$widget,array());?>
         </div>         
       </div> 

      </div>
      <!-- mobile menu -->
    
    </div> <!--col-->
    <div class="col-md-9">
    
     <?php echo $this->renderPartial($template_name, $params); ?>  
    
    </div>
  </div> <!--row-->  
  
  </div> <!--card-body-->

</div> <!--card-->