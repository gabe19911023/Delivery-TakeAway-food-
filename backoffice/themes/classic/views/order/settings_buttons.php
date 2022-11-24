<DIV id="vue-order-settings-tabs">

<div class="card">
      <div class="card-body">
         <components-order-buttons
         ref="buttons"
         group_name="new_order"
         :status_list='<?php echo json_encode($status)?>'
         :do_action_list='<?php echo json_encode($do_actions)?>'
         :order_type_list='<?php echo json_encode($order_type)?>'  
         ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
         :label="{
           title:'<?php echo CJavaScript::quote(t("New Orders"))?>',      
           text:'<?php echo CJavaScript::quote(t("define the buttons for this tab"))?>',            
           add : '<?php echo CJavaScript::quote(t("Add"))?>',
           save : '<?php echo CJavaScript::quote(t("Save"))?>',
           close : '<?php echo CJavaScript::quote(t("Close"))?>',      
           order_type : '<?php echo CJavaScript::quote(t("Order Type"))?>',     
           button_status : '<?php echo CJavaScript::quote(t("Status"))?>',
           button_name : '<?php echo CJavaScript::quote(t("Button Name"))?>',
           actions : '<?php echo CJavaScript::quote(t("Actions"))?>',
           class_name : '<?php echo CJavaScript::quote(t("Button CSS class name eg. btn-green, btn-black"))?>',
         }"
         />
      </div>
    </div>        
    <hr/>
    
    <div class="card">
      <div class="card-body">
         <components-order-buttons
         ref="buttons"
         group_name="order_processing"
         :status_list='<?php echo json_encode($status)?>'
         :do_action_list='<?php echo json_encode($do_actions)?>'
         ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
         :label="{
           title:'<?php echo CJavaScript::quote(t("Order Processing"))?>',      
           text:'<?php echo CJavaScript::quote(t("define the buttons for this tab"))?>',            
           add : '<?php echo CJavaScript::quote(t("Add"))?>',
           save : '<?php echo CJavaScript::quote(t("Save"))?>',
           close : '<?php echo CJavaScript::quote(t("Close"))?>',           
           button_status : '<?php echo CJavaScript::quote(t("Status"))?>',
           button_name : '<?php echo CJavaScript::quote(t("Button Name"))?>',
           actions : '<?php echo CJavaScript::quote(t("Actions"))?>',
           class_name : '<?php echo CJavaScript::quote(t("Button CSS class name eg. btn-green, btn-black"))?>',
         }"
         />
      </div>
    </div>        
    <hr/>
        
    <div class="card">
      <div class="card-body">
         <components-order-buttons
         ref="buttons"
         ajax_url="<?php echo Yii::app()->createUrl("/api")?>"  
         group_name="order_ready"
         :status_list='<?php echo json_encode($status)?>'
         :do_action_list='<?php echo json_encode($do_actions)?>'
         :order_type_list='<?php echo json_encode($order_type)?>'                 
         :label="{
           title:'<?php echo CJavaScript::quote(t("Orders Ready"))?>',      
           text:'<?php echo CJavaScript::quote(t("define the buttons for this tab"))?>',            
           add : '<?php echo CJavaScript::quote(t("Add"))?>',
           save : '<?php echo CJavaScript::quote(t("Save"))?>',
           close : '<?php echo CJavaScript::quote(t("Close"))?>',           
           order_type : '<?php echo CJavaScript::quote(t("Order Type"))?>',
           button_status : '<?php echo CJavaScript::quote(t("Status"))?>',
           button_name : '<?php echo CJavaScript::quote(t("Button Name"))?>',
           actions : '<?php echo CJavaScript::quote(t("Actions"))?>',
           class_name : '<?php echo CJavaScript::quote(t("Button CSS class name eg. btn-green, btn-black"))?>',
         }"
         />
      </div>
    </div>        
    <hr/>
    

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