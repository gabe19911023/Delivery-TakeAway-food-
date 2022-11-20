<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>


<div id="vue-order-history" class="card">
<div class="card-body">

<div class="mb-3">
  
  
  <div class="row">
   <div class="col">
	  <div class="bg-light p-3 mb-3 rounded">   
	   <div class="d-flex">
        <p class="m-0 mr-2 text-muted text-truncate"><?php echo t("TAKE AWAY")?></p><h5 ref="summary_orders" class="m-0">0</h5>
       </div>  	  
	  </div><!-- bg-light-->
	</div> <!--col-->
	
	<div class="col">
	  <div class="bg-light p-3 mb-3 rounded">   
	   <div class="d-flex">
        <p class="m-0 mr-2 text-muted text-truncate"><?php echo t("DELIVERIES")?></p><h5 ref="summary_cancel" class="m-0">0</h5>
       </div>  	  
	  </div><!-- bg-light-->
	</div> <!--col-->
	
	<!-- <div class="col">
	  <div class="bg-light p-3 mb-3 rounded">   
	   <div class="d-flex">
        <p class="m-0 mr-2 text-muted text-truncate"><?php echo t("Total refund")?></p><h5 ref="total_refund" class="m-0">0</h5>
       </div>  	  
	  </div>
	</div>  -->
	
	<div class="col">
	  <div class="bg-light p-3 mb-3 rounded">   
	   <div class="d-flex">
        <p class="m-0 mr-2 text-muted text-truncate"><?php echo t("Total Orders")?></p><h5 ref="summary_total" class="m-0">0</h5>
       </div>  	  
	  </div><!-- bg-light-->
	</div> <!--col-->
	
  </div> <!--row-->



<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
actions="orderHistory"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:filter="<?php echo true; ?>"
:settings="{
    filter : '<?php echo false;?>',   
    ordering :'<?php echo true;?>',    
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>', 
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All transactions")?>',
    searching : '<?php echo t("Searching...")?>',
    no_results : '<?php echo t("No results")?>',
    filters : '<?php echo t("Filters")?>',
    delete_confirmation : '<?php echo t("Delete Confirmation")?>',
    are_your_sure : '<?php echo t("Are you sure you want to permanently delete the selected item?")?>',
    cancel : '<?php echo t("Cancel")?>',
    delete : '<?php echo t("Delete")?>',
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
>
</components-datatable>

</div> <!--mb-3-->

</div> <!--body-->
</div> <!--card-->

<?php $this->renderPartial("/orders/template-filter");?> 