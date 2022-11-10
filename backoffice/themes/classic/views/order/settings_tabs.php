<DIV id="vue-order-settings-tabs">


  <div class="card">
      <div class="card-body">         
         <components-order-tabs
         ref="tabs"
         ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
         :status_list='<?php echo json_encode($status)?>'
         group_name="new_order"
         :label="{
           title:'<?php echo CJavaScript::quote(t("New Orders"))?>',      
           text:'<?php echo CJavaScript::quote(t("select the status that will show on this tab."))?>',            
           save : '<?php echo CJavaScript::quote(t("Save"))?>',
         }"
         >
         </components-order-tabs>                  
      </div>
    </div> <!--card-->
    
    <hr/>
    
    <div class="card ">
      <div class="card-body">         
         <components-order-tabs
         ref="tabs"
         ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
         :status_list='<?php echo json_encode($status)?>'
         group_name="order_processing"
         :label="{
           title:'<?php echo CJavaScript::quote(t("Orders Processing"))?>',      
           text:'<?php echo CJavaScript::quote(t("select the status that will show on this tab."))?>',            
           save : '<?php echo CJavaScript::quote(t("Save"))?>',
         }"
         >
         </components-order-tabs>                  
      </div>
    </div> <!--card-->
    
     <hr/>
     
    <div class="card ">
      <div class="card-body">         
         <components-order-tabs
         ref="tabs"
         ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
         :status_list='<?php echo json_encode($status)?>'
         group_name="order_ready"
         :label="{
           title:'<?php echo CJavaScript::quote(t("Orders Ready"))?>',      
           text:'<?php echo CJavaScript::quote(t("select the status that will show on this tab."))?>',            
           save : '<?php echo CJavaScript::quote(t("Save"))?>',
         }"
         >
         </components-order-tabs>                  
      </div>
    </div> <!--card--> 
    
    <hr/>
    
     <div class="card">
      <div class="card-body">         
         <components-order-tabs
         ref="tabs"
         ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
         :status_list='<?php echo json_encode($status)?>'
         group_name="completed_today"
         :label="{
           title:'<?php echo CJavaScript::quote(t("Completed Today"))?>',      
           text:'<?php echo CJavaScript::quote(t("select the status that will show on this tab."))?>',            
           save : '<?php echo CJavaScript::quote(t("Save"))?>',
         }"
         >
         </components-order-tabs>                  
      </div>
    </div> <!--card--> 
    

</DIV>
<!--vue-->


<DIV id="vue-bootbox">
<component-bootbox
ref="bootbox"
@callback="Callback"
size='small'
:label="{
  confirm: '<?php echo CJavaScript::quote(t("Delete Confirmation"))?>',
  are_you_sure: '<?php echo CJavaScript::quote(t("Are you sure you want to continue?"))?>',
  yes: '<?php echo CJavaScript::quote(t("Yes"))?>',
  cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>',  
  ok: '<?php echo CJavaScript::quote(t("Okay"))?>',  
}"
>
</component-bootbox>
</DIV>