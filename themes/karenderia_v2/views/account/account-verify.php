

<div class="container">
 
<div class="login-container m-auto pt-5 pb-5">

  <h4 class="text-center mb-5">Sign Up</h4>
  
  <div class="forms-center">
  
     <DIV id="vue-account-verification"> 
             
     
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
    
	   
	 </DIV> <!--vue-account-verification-->	   
  
  </div> <!--center-->

</div> <!--login container-->

</div> <!--containter-->