

<div class="container">
 
<div class="login-container m-auto pt-5 pb-5">
  
  
  <DIV id="vue-reset-password" v-cloakx >       
       
          
     <form @submit.prevent="resetPassword">

      <h5 class="text-center mb-4">Reset Password</h5>
      
      <template v-if="steps==1"> 
      <p><?php echo t("Please enter a new password for your account, [first_name]",array(
        '[first_name]'=>isset($first_name)?$first_name:''
      ))?></p>
     
       <div class="form-label-group">    
         <input class="form-control form-control-text" placeholder="" 
           id="password" type="password"  v-model="password"  >   
         <label for="password" class="required">Enter new password</label> 
       </div> 
       
       <div class="form-label-group">    
         <input class="form-control form-control-text" placeholder="" 
           id="cpassword" type="password"  v-model="cpassword"  >   
         <label for="cpassword" class="required">Confirm new password</label> 
       </div> 
       
        <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>        
	   	    
       <button class="btn btn-green w-100"  :class="{ loading: loading }" :disabled="!checkForm" >
	      <span v-if="loading==false">Submit</span>
	      <div v-cloak v-if="loading==true" class="m-auto" data-loader="circle-side"></div>
	   </button>
	   
	   </template>
	   
	   <template v-else-if="steps==2"> 
	   
	    <div  v-cloak v-if="success" class="alert alert-success" role="alert">
		    <p class="m-0">{{success}}</p>	    
		 </div>
		 
		<div class="mt-3 text-center">
	     <span>You can continue to login <a  href="<?php echo Yii::app()->createUrl("/account/login")?>" class="btn btn-white p-0 font14">click here</a></span>
	   </div>
	   
	   </template>
	   
       
     </form>       
     
     
  </DIV>     
  
  
  </div> <!--center-->

</div> <!--login container-->

</div> <!--containter-->