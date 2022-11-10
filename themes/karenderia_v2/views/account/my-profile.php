
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
	  <div class="card-body p-1 p-lg-3" id="vue-update-profile" v-cloak>
	
	  <form 
       @submit.prevent="checkForm" 
       method="POST" >
	  
	   <h5 class="mb-2 mb-lg-4 d-none d-lg-block"><?php echo t("Basic Details")?></h5>
	    
	   <div class="row">
	     <div class="col-lg-6">	     

	      <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="first_name" id="first_name" type="text"  >   
           <label for="first_name" class="required"><?php echo t("First name")?></label> 
          </div>    
	     
	     </div> <!--col-->
	     <div class="col-lg-6">	     
	     
	     <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="last_name" id="last_name" type="text"  >   
           <label for="last_name" class="required"><?php echo t("Last name")?></label> 
          </div>    
	     
	     </div> <!--col-->
	   </div> <!--row-->
	   
	    <div class="row">
	     <div class="col-lg-6">	     
	     <div class="form-label-group">    
           <input  class="form-control form-control-text" placeholder=""
        v-model="email_address" id="email_address" type="text" >              
           <label for="email_address"><?php echo t("Email address")?></label> 
          </div>   	     
	     </div> <!--col-->
	     <div class="col-lg-6">	     
	     
	      <!--COMPONENTS-->
        <component-phone
	    default_country="<?php echo CJavaScript::quote($phone_default_country);?>"    
	    :only_countries='<?php echo json_encode($phone_country_list)?>'	
	    v-model:mobile_number="mobile_number"
	    v-model:mobile_prefix="mobile_prefix"
	    >
	    </component-phone>
	    <!--END COMPONENTS-->	 
	    
	    
	    <component-change-phoneverify
         ref="cphoneverify"
         @after-submit="saveProfile"
          :label="{
		    steps: '<?php echo t("2-Step Verification")?>',
		    for_security: '<?php echo CJavaScript::quote(t("For your security, we want to make sure it's really you."))?>', 
		    enter_digit: '<?php echo CJavaScript::quote(t("Enter 6-digit code"))?>',  			    
		    resend_code: '<?php echo CJavaScript::quote(t("Resend Code"))?>',
		    resend_code_in: '<?php echo CJavaScript::quote(t("Resend Code in"))?>',
		    code: '<?php echo CJavaScript::quote(t("Code"))?>',
		    submit: '<?php echo CJavaScript::quote(t("Submit"))?>',			    
		 }"
         >   
        </component-change-phoneverify>
	    
	     </div> <!--col-->
	   </div> <!--row-->
	  	   	
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