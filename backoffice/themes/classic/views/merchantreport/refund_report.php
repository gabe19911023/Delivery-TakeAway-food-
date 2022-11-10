<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>


<div id="vue-tables" class="card">
<div class="card-body">

<div class="mb-3">

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
actions="refundReport"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:transaction_type_list='<?php echo json_encode($transaction_type)?>'
:settings="{
    filter : '<?php echo false;?>',       
    filter_date_disabled : '<?php echo false;?>',   
    ordering :'<?php echo true;?>',        
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>', 
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All Payment status")?>',
    searching : '<?php echo t("Searching...")?>',
    no_results : '<?php echo t("No results")?>'
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
>
</components-datatable>

</div> <!--mb-3-->

</div> <!--body-->
</div> <!--card-->