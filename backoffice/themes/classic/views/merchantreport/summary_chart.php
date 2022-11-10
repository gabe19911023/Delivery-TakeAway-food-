<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>


<div id="vue-report-sales-summary" class="card">
<div class="card-body">

<div class="mb-3">
  
<div class="row">
<div class="col"></div>
<div class="col">

  <div class="d-none d-md-block">    
  <ul class="nav nav-pills justify-content-end">			  
	  <li class="nav-item">
    <a href="<?php echo Yii::app()->createUrl("/merchantreport/summary")?>" class="nav-link py-1 px-3">
      <?php echo t("Sales summary")?>
    </a>	    
	  </li>			  
	  <li class="nav-item">
	    <a class="nav-link py-1 px-3 active">
      <?php echo t("Sales chart")?></a>	    
	  </li>			  
  </ul>
  </div>

  <div class="d-block d-md-none text-right">
     <div class="dropdown btn-group dropleft">
		      <button class="btn btn-sm dropdown-togglex dropleft" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		       <i class="zmdi zmdi-more-vert"></i>
		     </button>
         <div class="dropdown-menu dropdown-menu-mobile" aria-labelledby="dropdownMenuButton">
             <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("/merchantreport/summary")?>">
               <?php echo t("Sales Summary")?>
              </a>
             <a class="dropdown-item active" >
              <?php echo t("Sales chart")?>
             </a>
         </div>         
       </div> 
  </div>
 
</div>
</div>  
<!--row-->	


<div class="position-relative m-5">
<components-report-sales-summary-chart
ref="report_summary_chart"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
:label="{    
    sold : '<?php echo t("Sold")?>'
  }"  
>
</components-report-sales-summary-chart>
</div>

</div> <!--mb-3-->

</div> <!--body-->
</div> <!--card-->