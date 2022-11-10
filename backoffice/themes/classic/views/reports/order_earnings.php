<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>


<div id="vue-tables" class="card">
<div class="card-body">
  

 <div class="mt-3">

  <components-reports-earnings
    ref="summary_earnings"
    ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
    :label="{    
	    total_count : '<?php echo CJavaScript::quote(t("Count"));?>',    
	    admin_earning : '<?php echo CJavaScript::quote(t("Admin earned"));?>',    
	    merchant_earning : '<?php echo CJavaScript::quote(t("Merchant earned"));?>',    
	    total_sell : '<?php echo CJavaScript::quote(t("Total sell"));?>',    	    
	}"            
 />
 </components-reports-earnings>    
 
 <components-datatable
  ref="datatable"
  ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
  actions="ReportsOrderEarnings"
  :table_col='<?php echo json_encode($table_col)?>'
  :columns='<?php echo json_encode($columns)?>'  
  :date_filter='<?php echo true;?>'
  :filter="<?php echo false; ?>"
  :settings="{
      auto_load : '<?php echo true;?>',
      filter : '<?php echo true;?>',   
      ordering :'<?php echo true;?>',  
      order_col :'<?php echo intval($order_col);?>',   
      load_filter :'<?php echo false;?>',  
      sortby :'<?php echo $sortby;?>',     
      placeholder : '<?php echo t("Start date -- End date")?>',  
      separator : '<?php echo t("to")?>',
      all_transaction : '<?php echo t("All status")?>'
    }"  
  page_limit = "<?php echo Yii::app()->params->list_limit?>"  
  @after-selectdate="afterSelectdate"
  >
  </components-datatable>
  
  
  </div>
  

</div> <!--card body-->
</div> <!--card-->

<?php //$this->renderPartial("/finance/filter_order");?>