

<div class="container">
 
<div class="login-container m-auto pt-5 pb-5">

  <h4 class="text-center mb-5">Sign Up</h4>
  
  <div class="forms-center">
  
     <DIV id="vue-account-information"> 
     
        
     <template v-if="steps==1">
     
      <form 
       @submit.prevent="verifyCode" 
       method="POST" >
             	   
       <p class="m-1"><?php echo t("Enter the code sent to [email]",array('[email]'=> CommonUtility::maskEmail($email_address) ))?></p>
              
       <div class="form-label-group">    
           <input  ref="code" class="form-control form-control-text" placeholder=""
        v-model="verification_code" id="code"  v-maska="'######'" type="text" >   
            <label for="code"><?php echo t("Code")?></label>            
       </div>   
       
       <template v-if="counter===0">          
       <div class="mt-1 mb-3">           
        <a href="javascript:;" @click="resendCode" :disabled="is_loading" >
          <p class="m-0"><u>Resend Code</u></p>
        </a>
       </div>
       </template>
       <template v-else>          
         <p><u>Resend Code in {{counter}}</u></p>
       </template>
       
	   <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>        
	   
	  <div  v-cloak v-if="success" class="alert alert-success" role="alert">
	    <p class="m-0">{{success}}</p>	    
	   </div>
	    
	   <button class="btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!CodeValid"  >
          <span class="label">Next</span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
       </button>
	   	   
	  </form> 
	  
     </template>        
     
     
     <template v-else-if="steps==2">
     
      <form 
       @submit.prevent="completeSignup" 
       method="POST" >
	  
	  <p class="m-1">Fill your information</p>
	  
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
         <input class="form-control form-control-text" 
       placeholder="" v-model="email_address" id="email_address" type="email" disabled >   
         <label for="email_address" class="required">Email address</label> 
       </div>   
       
        <p class="m-1">Enter your phone number</p>
        <!--COMPONENTS-->        		
        <component-phone
	    default_country="<?php echo CJavaScript::quote($phone_default_country);?>"    
	    :only_countries='<?php echo json_encode($phone_country_list)?>'	
	    v-model:mobile_number="mobile_number"
	    v-model:mobile_prefix="mobile_prefix"
	    >
	    </component-phone>
	    <!--END COMPONENTS-->	   
       
       <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		  <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>     
       
	    <p class="m-0 mt-3 mb-3">
	    By clicking "Submit," you agree to <a href="" class="text-green">Karenderia General Terms and Conditions</a>
	     and acknowledge you have read the <a href="" class="text-green">Privacy Policy</a>.
	    </p>
	   
       <button class="btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!CompleteFormValid"  >
          <span class="label">Submit</span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
       </button>
	  
	  </form> 
     
     </template> 
	 
	   
	 </DIV> <!--vue-account-verification-->	   
  
  </div> <!--center-->

</div> <!--login container-->

</div> <!--containter-->