

<div class="container">
 
<div class="login-container m-auto pt-5 pb-5">

  <h4 class="text-center mb-5"><?php echo t("Let's get started")?></h4>
  
  <div class="forms-center">
  
       <DIV id="vue-register-less" v-cloak> 
       
       
       <template v-if="steps===1">
       <form 
       @submit.prevent="registerPhone" 
       method="POST" >
       
       <input ref="redirect" type="hidden" value="<?php echo $redirect_to?>">
             
        <p class="m-1"><?php echo t("Enter your phone number")?></p>
        
        <!--COMPONENTS-->        
        <component-phone
	    default_country="<?php echo CJavaScript::quote($phone_default_country);?>"    
	    :only_countries='<?php echo json_encode($phone_country_list)?>'	
	    v-model:mobile_number="mobile_number"
	    v-model:mobile_prefix="mobile_prefix"
	    >
	    </component-phone>
	    <!--END COMPONENTS-->	   
	    
	    
	     <!--COMPONENTS--> 	     
       <vue-recaptcha v-if="show_recaptcha" 
         sitekey="<?php echo CJavaScript::quote($captcha_site_key);?>"
		 size="normal" 
		 theme="light"
		 is_enabled="<?php echo CJavaScript::quote($capcha)?>"
		 :tabindex="0"
		 @verify="recaptchaVerified"
		 @expire="recaptchaExpired"
		 @fail="recaptchaFailed"
		 ref="vueRecaptcha">
       </vue-recaptcha>		
       <!--END COMPONENTS-->
	    

	   <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>        
	    
	   <button class="btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!DataValid"  >
          <span class="label"><?php echo t("Next")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
       </button>
	   	   
	  </form> 
	  
	    <div class="mt-3 text-center">
	     <span><?php echo t("Have an account?")?> <a  href="<?php echo Yii::app()->createUrl("/account/login",array(
	      'redirect'=>$redirect_to
	     ))?>" class="btn btn-white p-0 font14"><?php echo t("Sign in")?></a></span>
	   </div>
	  
	  </template>
	  
	  <template v-else-if="steps===2">
	   <form 
       @submit.prevent="verifyPhoneCode" 
       method="POST" >
             	   	   
       <p class="m-1"><?php echo t("Enter the code sent to")?> +{{mobile_prefix}}{{mobile_number}}</p>
              
       <div class="form-label-group">    
           <input  ref="code" class="form-control form-control-text" placeholder="0000"
        v-model="verification_code" v-maska="'######'" type="text" >               
       </div>   
       
       <template v-if="counter===0">          
       <div class="mt-1 mb-3">           
        <a href="javascript:;" @click="resendCode" :disabled="is_loading" >
          <p class="m-0"><u><?php echo t("Resend Code")?></u></p>
        </a>
       </div>
       </template>
       <template v-else>          
         <p><u><?php echo t("Resend Code in")?> {{counter}}</u></p>
       </template>
       
	   <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>        
	   
	   <div  v-cloak v-if="success" class="alert alert-success" role="alert">
	    <p class="m-0">{{success}}</p>	    
	   </div>
	    
	   <button class="btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!CodeValid"  >
          <span class="label"><?php echo t("Next")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
       </button>
	   	   
	  </form> 
	  </template>
	  
	  
	  <template v-else-if="steps===3">
	  
	  <form 
       @submit.prevent="completeSignup" 
       method="POST" >
	  
	  <p class="m-1"><?php echo t("Fill your information")?></p>
	  
	  <div class="row p-0">
         <div class="col pr-0">
         
          <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="firstname" id="firstname" type="text"  >   
           <label for="firstname" class="required"><?php echo t("First name")?></label> 
          </div>    
         
         </div> <!--col-->
         <div class="col">
         
          <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="lastname" id="lastname" type="text" >   
           <label for="lastname" class="required"><?php echo t("Last name")?></label> 
         </div>   
         
         </div> <!--col-->
       </div> <!--row-->
       
       <div class="form-label-group">    
         <input class="form-control form-control-text" placeholder="" v-model="email_address" id="email_address" type="email" maxlength="50" >   
         <label for="email_address" class="required"><?php echo t("Email address")?></label> 
       </div>   
       
       <div class="form-label-group change_field_password">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  :type="password_type" id="password" v-model="password"  >
		   <label for="password" class="required"><?php echo t("Password")?></label>      
		   <a href="javascript:;" @click="showPassword" >
		      <i v-if="show_password==false" class="zmdi zmdi-eye"></i>
		      <i v-cloak v-if="show_password==true" class="zmdi zmdi-eye-off"></i>
		   </a>
		</div> 
		
		<div class="form-label-group">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  type="password" id="cpassword" v-model="cpassword"  >
		   <label for="cpassword" class="required"><?php echo t("Confirm Password")?></label>      		   
		</div> 
       
        <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>     
       
	   <?php if($enabled_terms==1):?>	   
	    <p class="m-0 mt-3 mb-3">	    
	    <?php 
	    $this->beginWidget('CHtmlPurifier');
	    echo $signup_terms;
	    $this->endWidget();
	    ?>
	    </p>	    
	    <?php endif;?>
	   
       <button class="btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!CompleteFormValid"  >
          <span class="label"><?php echo t("Submit")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
       </button>
	  
	  </form> 
	  </template>
	   
	 </DIV> <!--vue-register-->	   
  
  </div> <!--center-->

</div> <!--login container-->

</div> <!--containter-->