

<div class="container">
 
<div class="login-container m-auto pt-5 pb-5">

  <h5 class="text-center mb-4">Register</h5>
  
  <div class="forms-center">
  
       <DIV id="vue-register"> 
       
       <form 
       @submit.prevent="onRegister" 
       method="POST" >
       
       <div class="row p-0">
         <div class="col pr-0">
         
          <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="firstname" id="firstname" type="text"  >   
           <label for="firstname" class="required">First name</label> 
          </div>    
         
         </div> <!--col-->
         <div class="col">
         
          <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="lastname" id="lastname" type="text" >   
           <label for="lastname" class="required">Last name</label> 
         </div>   
         
         </div> <!--col-->
       </div> <!--row-->
       
       <div class="form-label-group">    
         <input class="form-control form-control-text" placeholder="" v-model="email_address" id="email_address" type="email" maxlength="50" >   
         <label for="email_address" class="required">Email address</label> 
       </div>   
       
        <!--COMPONENTS-->                
        <component-phone
	    default_country="<?php echo CJavaScript::quote($phone_default_country);?>"    
	    :only_countries='<?php echo json_encode($phone_country_list)?>'	
	    v-model:mobile_number="mobile_number"
	    v-model:mobile_prefix="mobile_prefix"
	    >
	    </component-phone>
	    <!--END COMPONENTS-->
       
       
        <div class="form-label-group change_field_password">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  :type="password_type" id="password" v-model="password"  >
		   <label for="password" class="required">Password</label>      
		   <a href="javascript:;" @click="showPassword" >
		      <i v-if="show_password==false" class="zmdi zmdi-eye"></i>
		      <i v-cloak v-if="show_password==true" class="zmdi zmdi-eye-off"></i>
		   </a>
		</div> 
		
		<div class="form-label-group">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  type="password" id="cpassword" v-model="cpassword"  >
		   <label for="cpassword" class="required">Confirm Password</label>      		   
		</div> 
       	
	   <!--COMPONENTS--> 
       <vue-recaptcha v-if="show_recaptcha" 
         sitekey="<?php echo $captcha_site_key;?>"
		 size="normal" 
		 theme="light"
		 :tabindex="0"
		 is_enabled="<?php echo CJavaScript::quote($capcha)?>"
		 @verify="recaptchaVerified"
		 @expire="recaptchaExpired"
		 @fail="recaptchaFailed"
		 ref="vueRecaptcha">
       </vue-recaptcha>		
       <!--END COMPONENTS-->
		
        <?php if($enabled_terms):?>
	    <p class="m-0 mt-3 mb-3"><?php echo $signup_terms;?></p>
	    <?php endif;?>
	   
	   <div  v-cloak v-if="error.length>0" class="alert alert-warning" role="alert">
	    <p v-cloak v-for="err in error" class="m-1">{{err}}</p>	    
	   </div>
	   
	   <div  v-cloak v-if="success" class="alert alert-success" role="alert">
	    <p class="m-0">{{success}}</p>	    
	   </div>
	   
	   <button class="btn btn-green w-100" :class="{ loading: loading }" :disabled="ready==false" >
	      <span v-if="loading==false">Sign Up</span>
	      <div v-cloak v-if="loading==true" class="m-auto" data-loader="circle-side"></div>
	   </button>
	   	   
	  </form> 
	   
	   <div class="mt-3 text-center">
	     <p class="m-0">Have an account?</p>
	     <a  href="<?php echo Yii::app()->createUrl("/account/login",array(
	      'redirect'=>$redirect_to
	     ))?>" class="btn btn-white p-0 font14">Sign in</a>
	   </div>
	   
	   </DIV> <!--vue-register-->
	   	  
  
  </div> <!--center-->

</div> <!--login container-->

</div> <!--containter-->