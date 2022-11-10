
<DIV id="vue-pos">

<div class="text-right mb-3 d-block d-lg-none">
<div class="hamburger hamburger--3dx ssm-toggle-nav">
  <div class="hamburger-box">
    <div class="hamburger-inner"></div>
  </div>
</div> 
</div>

<div class="row">
  <div class="col-md-8">
      
  <components-menu
  ref="menu"    
  ajax_url="<?php echo $ajax_url?>"
  @show-item="showItemDetails"
  
  image_placeholder="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"
  merchant_id="<?php echo $merchant_id?>"    
  :label="{
    previous:'<?php echo CJavaScript::quote(t("Previous"))?>', 
    next:'<?php echo CJavaScript::quote(t("Next"))?>',     
  }"    
  :responsive='<?php echo json_encode($responsive);?>'
  >
 </components-menu>
 
 
  <components-item-details
  ref="item"    
  ajax_url="<?php echo $ajax_url?>"
  @go-back="showMerchantMenu"
  @close-menu="hideMerchantMenu"
  @refresh-order="refreshOrderInformation"  
  
  image_placeholder="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"
  merchant_id="<?php echo $merchant_id?>"  
  :order_type="order_type"
  :order_uuid="order_uuid"
  >
  </components-item-details>
  
  </div>
  <!--left col-->
  
  <!--CART SECTION-->
  <div class="col-md-4 position-relative">
  
   <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
    <div>
      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
    </div>
  </div>   
  
  <components-order-pos
  ref="pos_details"    
  ajax_url="<?php echo $ajax_url?>"
  :order_uuid="order_uuid"
  :label="{
    clear_items:'<?php echo CJavaScript::quote(t("Clear all items"))?>', 
    are_you_sure:'<?php echo CJavaScript::quote(t("are you sure?"))?>', 
    cancel:'<?php echo CJavaScript::quote(t("Cancel"))?>', 
    confirm:'<?php echo CJavaScript::quote(t("Confirm"))?>', 
    searching:'<?php echo CJavaScript::quote(t("Searching..."))?>', 
    no_results:'<?php echo CJavaScript::quote(t("No results"))?>', 
    walkin_customer:'<?php echo CJavaScript::quote(t("Walk-in Customer"))?>',     
  }"  
  @refresh-order="refreshOrderInformation" 
  @after-reset="afterReset" 
  @after-createorder="afterCreateorder"
  >
  </components-order-pos>
  
  </div>
  <!--right col-->
  
</div> <!--row-->


<components-order-print
  ref="print"      
  :order_uuid="order_uuid_print"
  mode="popup"
  :line="75"
  ajax_url="<?php echo $ajax_url?>"  
  >
</components-order-print>

</DIV>
<!--vue-->

<?php $this->renderPartial("/pos/template_menu_pos");?>
<?php $this->renderPartial("/orders/template_item");?>
<?php $this->renderPartial("/pos/order-details-pos");?>
<?php $this->renderPartial("/orders/template_print");?>