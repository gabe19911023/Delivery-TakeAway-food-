<div id="vue-schedule-order">

<?php if($show):?>
<div class="container-fluid" v-cloak v-if="store_close" >
    <div class="container">
       <div class="d-flex justify-content-center">
              
       <div class="w-100 text-center mt-3 mb-3">   
	      <h4><?php echo t("Store is close")?></h4>	      
	      <p class="m-0"><?php echo t("This store is close right now, but you can schedulean order later.")?></p>
	      <a href="javascript:;" @click="show" class="font-weight-bold"><?php echo t("Schedule Order")?></a>
	   </div>
       
       </div>
    </div>
</div>
<?php endif;?>

<component-select-time
ref="select_time" 
:label="{
title:'<?php echo CJavaScript::quote(t("Time for delivery"))?>', 
save: '<?php echo CJavaScript::quote(t("Save"))?>',	    	    
}"
@after-save="afterSaveTransOptions"
@after-close="afterCloseAddress"
>
</component-select-time>

</div>
<!--vue-schedule-order-->