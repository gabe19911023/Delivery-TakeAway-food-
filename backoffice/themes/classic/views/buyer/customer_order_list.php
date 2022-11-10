<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>


<div id="vue-tables" class="card">
<div class="card-body">

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
actions="customerOrderList"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:date_filter='<?php echo true;?>'
ref_id='<?php echo $client_id;?>'
:filter="<?php echo true; ?>"
:settings="{
    auto_load : '<?php echo true;?>',
    filter : '<?php echo false;?>',   
    ordering :'<?php echo true;?>',  
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>',     
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All transactions")?>',
    load_filter : '<?php echo true;?>',
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
>
</components-datatable>
</div>
</div>

<?php $this->renderPartial("/finance/filter_order");?>