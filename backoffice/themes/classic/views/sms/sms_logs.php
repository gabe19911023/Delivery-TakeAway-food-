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
actions="smslogs"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:date_filter='<?php echo true;?>'
:settings="{
    auto_load : '<?php echo true;?>',
    filter : '<?php echo true;?>',   
    ordering :'<?php echo true;?>',  
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>',     
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All transactions")?>'
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
@view="view"
>
</components-datatable>

<components-view-data
ref="view_data"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
method="getSMS"
:label="{    
    title : '<?php echo CJavaScript::quote(t("View SMS"))?>',      
    close : '<?php echo CJavaScript::quote(t("Close"))?>',  
  }"  
/>
</components-view-data>

</div> <!--card body-->
</div> <!--card-->