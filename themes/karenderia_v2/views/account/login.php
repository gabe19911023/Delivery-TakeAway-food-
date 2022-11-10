

<div class="container">
 
<div class="login-container m-auto pt-5 pb-5 p-2">

  <h5 class="text-center mb-4"><?php echo t("Login")?></h5>
  
  <div class="forms-center">
  
       <DIV id="vue-login" v-cloak >       
       <form @submit.prevent="login">
                     
       <div class="form-label-group">    
         <input class="form-control form-control-text" placeholder="" 
           id="username" type="text" autocomplete="new-username"  v-model="username" maxlength="100" >   
         <label for="username" class="required"><?php echo t("Mobile number or email")?></label> 
       </div>   
       
        <div class="form-label-group change_field_password">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  :type="password_type" id="password" v-model="password" maxlength="100"  >
		   <label for="password" class="required"><?php echo t("Password")?> <span class="required">*</span></label>      
		   <a href="javascript:;" @click="showPassword" >
		      <i v-cloak v-if="show_password==false" class="zmdi zmdi-eye"></i>
		      <i v-cloak v-if="show_password==true" class="zmdi zmdi-eye-off"></i>
		   </a>
		</div> 
		
		  <!--COMPONENTS--> 
       <components-recapcha  
         sitekey="<?php echo $captcha_site_key;?>"
		 size="normal" 
		 theme="light"
		 :tabindex="0"
		 is_enabled="<?php echo CJavaScript::quote($capcha)?>"
		 @verify="recaptchaVerified"
		 @expire="recaptchaExpired"
		 @fail="recaptchaFailed"
		 ref="vueRecaptcha">
       </components-recapcha>		
       <!--END COMPONENTS-->
		
	   <div class="row m-0">
	      <div class="col-6 p-0"> 
	         <div class="custom-control custom-checkbox">
		      <input type="checkbox" id="rememberme" v-model="rememberme"  class="custom-control-input">
		      <label class="custom-control-label" for="rememberme"><?php echo t("Remember me")?></label>
		    </div>   
	      </div>
	      <div class="col-6 mb-4 mb-lg-3 d-flex justify-content-end"> 
	       <a href="<?php echo Yii::app()->createUrl("account/forgot_pass")?>" class="a-12"><u><?php echo t("Forgot password?")?></u></a>
	      </div>
	   </div><!-- row-->
	   
	   <div  v-cloak v-if="error.length>0" class="alert alert-warning" role="alert">
	    <p v-cloak v-for="err in error">{{err}}</p>	    
	   </div>
	   
	   <div  v-cloak v-if="success" class="alert alert-success" role="alert">
	    <p class="m-0">{{success}}</p>	    
	   </div>
	   	   
	   <button class="btn btn-green w-100"  :class="{ loading: loading }" :disabled="!formValid" >
	      <span v-if="loading==false"><?php echo t("Sign in")?></span>
	      <div v-cloak v-if="loading==true" class="m-auto" data-loader="circle-side"></div>
	   </button>
	   	   
	   
	   <div class="mt-3 text-center">
	     <p class="m-0"><?php echo t("Don't have an account?")?></p>
	     <a  href="<?php echo Yii::app()->createUrl("/account/signup",array(
	      'redirect'=>$redirect_to,
	      //'next_url'=>$next_url,
	     ))?>" class="btn btn-white p-0 font14"><?php echo t("Sign Up")?></a>
	   </div>
	   
	   </form>
	   
	   <div class="mt-4 text-center">
	     <component-facebook
	     app_id="<?php echo $fb_app_id;?>"	     
	     :show_button="<?php echo $fb_enabled==1?true:false;?>"	     
	     version="v12.0"
	     verification="<?php echo $enabled_verification?>"
	     redirect_to="<?php echo $redirect_to?>"
	     @social-registration="SocialRegister"
	     :errors="{		    
			 user_cancelled: '<?php echo CJavaScript::quote(t("User cancelled login or did not fully authorize."))?>', 		    
		  }"	    
		 :label="{		    
			 title: '<?php echo CJavaScript::quote(t("Login with Facebook"))?>', 		    
		 }"	    
	     >
	     </component-facebook>
	   </div>
	   	    
	    <div class="mt-4 text-center">
	     <component-google
	     client_id="<?php echo $google_client_id;?>"	     
	     :show_button="<?php echo $google_enabled==1?true:false;?>"	     
	     cookiepolicy="single_host_origin"
	     scope = "profile"
	     verification="<?php echo $enabled_verification?>"
	     redirect_to="<?php echo $redirect_to?>"
	     @social-registration="SocialRegister"
	     :errors="{		    
			 user_cancelled: '<?php echo CJavaScript::quote(t("User cancelled login or did not fully authorize."))?>', 		    
		  }"	    
		 :label="{		    
			 title: '<?php echo CJavaScript::quote(t("Login with Google"))?>', 		    
		 }"	    
	     >
	     </component-google>
	   </div>
	   
	   </DIV> <!--vue-login-->
	   	  
  
  </div> <!--center-->

</div> <!--login container-->

</div> <!--containter-->

<?php $this->renderPartial("//components/vue-bootbox")?>