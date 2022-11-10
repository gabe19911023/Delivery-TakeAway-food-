<div id="vue-commission-statement" class="card">
<div class="card-body">

<div class="d-flex flex-row justify-content-end">
  <div class="p-2">  
  <a type="button" class="btn btn-black btn-circle" 
  href="<?php echo $link?>">
    <i class="zmdi zmdi-plus"></i>
  </a>  
  </div>
  <div class="p-2"><h5><?php echo t("Add new")?></h5></div>
</div> <!--flex-->     

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
actions="plans_features"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:ref_id="<?php echo $ref_id?>"
:date_filter='false'
:settings="{
    auto_load : '<?php echo true;?>',
    filter : '<?php echo false;?>',   
    ordering :'<?php echo false;?>',  
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>',     
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All transactions")?>'
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
>
</components-datatable>



</div> <!--card body-->
</div> <!--card-->