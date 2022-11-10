<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>
 
  <?php if(isset($link)):?>
  <?php if(!empty($link)):?> 
    <div class="d-flex flex-row justify-content-end">
	  <div class="p-2">  
	  <a type="button" class="btn btn-black btn-circle" 
	  href="<?php echo $link?>">
	    <i class="zmdi zmdi-plus"></i>
	  </a>  
	  </div>
	  <div class="p-2"><h5><?php echo t("Add new")?></h5></div>
	</div> <!--flex-->     
  <?php endif;?>
 <?php endif;?>	 
</nav>  
    


<div id="vue-report-sales-summary" class="card">
<div class="card-body">


<div class="mb-3 mt-3">

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
actions="supplierList"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:settings="{
    filter : '<?php echo true;?>',           
    ordering :'<?php echo true;?>',       
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>',      
    filter_date_disabled :'<?php echo true;?>',    
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