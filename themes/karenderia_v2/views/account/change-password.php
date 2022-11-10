
<?php 
$this->renderPartial('//account/my-profile-header',array(
	'avatar'=>$avatar,
	'model'=>$model,
	'menu'=>$menu
));
?>

<div class="card">
  <div class="card-body p-0 p-lg-3">
  
  <div class="row">
    <div class="col-md-4 d-none d-lg-block">
    
    <div class="preview-image mb-2">
     <div class="col-lg-7">
      
      <?php 
      $this->renderPartial('//account/my-profile-photo',array(
        'avatar'=>$avatar,			
      ));
      ?>
      
     </div>     
    </div>
     
    <div class="attributes-menu-wrap">
    <?php $this->widget('application.components.WidgetUserProfile',array());?>
    </div>
    
    </div> <!--col-->

    <div class="col-lg-8 col-md-12">    
    
	<div class="card">
	  <div class="card-body p-1 p-lg-3" id="vue-update-password" v-cloak>
	    
    <form 
       @submit.prevent="updatePassword" 
       method="POST" >
	  
	   
	   <div class="form-label-group">    
        <input class="form-control form-control-text" placeholder="" v-model="old_password" id="old_password" type="text"  >   
        <label for="old_password" class="required"><?php echo t("Old password")?></label> 
       </div>    
	   
       <div class="form-label-group">    
        <input class="form-control form-control-text" placeholder="" v-model="new_password" id="new_password" type="text"  >   
        <label for="new_password" class="required"><?php echo t("New password")?></label> 
       </div>   
       
       <div class="form-label-group">    
        <input class="form-control form-control-text" placeholder="" v-model="confirm_password" 
       id="confirm_password" type="text"  >   
        <label for="confirm_password" class="required"><?php echo t("Confirm Password")?></label> 
       </div>   
       
	   <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>   
	   
	   <div  v-cloak v-if="success" class="alert alert-success" role="alert">
	    <p class="m-0">{{success}}</p>	    
	   </div>
	   
	   <button class="mt-3 btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!DataValid"  >
          <span class="label"><?php echo t("Submit")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
       </button>
        
	   </form>
	
	  </div> <!--body-->
	</div> <!--card-->
    
    </div> <!--col-->
  </div> <!--row-->
  
  </div> <!--card-body-->
</div> <!--card-->



<DIV id="vue-bootbox">
<component-bootbox
ref="bootbox"
@callback="Callback"
size='small'
:label="{
  confirm: '<?php echo CJavaScript::quote(t("Confirm account deletion"))?>',
  are_you_sure: '<?php echo CJavaScript::quote(t("Are you sure you want to delete your account and customer data from {{site_title}}?{{new_line}} This action is permanent and cannot be undone.",array(
      '{{site_title}}'=> Yii::app()->params['settings']['website_title'],
      '{{new_line}}'=>"<br/><br/>"
   )))?>',
  yes: '<?php echo CJavaScript::quote(t("Delete Account"))?>',
  cancel: '<?php echo CJavaScript::quote(t("Don't Delete"))?>',  
  ok: '<?php echo CJavaScript::quote(t("Okay"))?>',  
}"
>
</component-bootbox>
</DIV>