<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>


<div id="vue-report-sales-summary" class="card">
<div class="card-body">

<div class="mb-3">

<div class="row align-items-center">
<div class="col "></div>
<div class="col ">

  <div class="d-none d-md-block">    
  <ul class="nav nav-pills justify-content-end">			  
	  <li class="nav-item">
	    <a class="nav-link py-1 px-3 active"><?php echo t("Sales Summary")?></a>	    
	  </li>			  
	  <li class="nav-item">
	    <a href="<?php echo Yii::app()->createUrl("/merchantreport/summary_chart")?>" class="nav-link py-1 px-3">
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
             <a class="dropdown-item" ><?php echo t("Sales Summary")?></a>
             <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("/merchantreport/summary_chart")?>" >
              <?php echo t("Sales chart")?>
             </a>
         </div>         
       </div> 
  </div>

</div>
</div>  
<!--row-->	

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
actions="reportSalesSummary"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:transaction_type_list='<?php echo json_encode($transaction_type)?>'
:settings="{
    filter : '<?php echo false;?>',       
    filter_date_disabled : '<?php echo true;?>',   
    ordering :'<?php echo false;?>',        
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>', 
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All Items")?>',
    searching : '<?php echo t("Searching...")?>',
    no_results : '<?php echo t("No results")?>'
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
>
</components-datatable>

</div> <!--mb-3-->

</div> <!--body-->
</div> <!--card-->