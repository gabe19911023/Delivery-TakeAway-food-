

<div class="container">
 
<div class="login-container m-auto pt-5 pb-5">

  <h5 class="text-center mb-4"><?php echo t("Register user")?></h5>
  
  <div class="forms-center">
  
       <DIV id="vue-merchant-user"> 
       
       <form 
       @submit.prevent="onRegister" 
       method="POST" >
              
       <div class="row p-0">
         <div class="col pr-0">
         
          <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="first_name" id="first_name" type="text" maxlength="50"   >   
           <label for="first_name" class="required"><?php echo t("First name")?></label> 
          </div>    
         
         </div> <!--col-->
         <div class="col">
         
          <div class="form-label-group">    
           <input class="form-control form-control-text" placeholder="" v-model="last_name" id="last_name" type="text" maxlength="50"  >   
           <label for="last_name" class="required"><?php echo t("Last name")?></label> 
         </div>   
         
         </div> <!--col-->
       </div> <!--row-->
       
       <div class="form-label-group">    
         <input class="form-control form-control-text" placeholder="" v-model="contact_email" id="contact_email" type="email" maxlength="255" >   
         <label for="contact_email" class="required"><?php echo t("Email address")?></label> 
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
       
	    <div class="form-label-group">    
         <input class="form-control form-control-text" placeholder="" v-model="username" id="username" type="text" maxlength="50" >   
         <label for="username" class="required"><?php echo t("Username")?></label> 
       </div>   
       
        <div class="form-label-group change_field_password">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  :type="password_type" id="password" v-model="password"  maxlength="32" >
		   <label for="password" class="required"><?php echo t("Password")?></label>      
		   <a href="javascript:;" @click="showPassword" >
		      <i v-if="show_password==false" class="zmdi zmdi-eye"></i>
		      <i v-cloak v-if="show_password==true" class="zmdi zmdi-eye-off"></i>
		   </a>
		</div> 
		
		<!--<div class="form-label-group">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  type="password" id="cpassword" v-model="cpassword"  >
		   <label for="cpassword" class="required">Confirm Password</label>      		   
		</div> -->
		
		 <div class="form-label-group change_field_password">    
		   <input class="form-control form-control-text" autocomplete="new-password" 
           placeholder="Password"  :type="password_type" id="cpassword" v-model="cpassword" maxlength="32"  >
		   <label for="cpassword" class="required"><?php echo t("Confirm Password")?></label>      
		   <a href="javascript:;" @click="showPassword" >
		      <i v-if="show_password==false" class="zmdi zmdi-eye"></i>
		      <i v-cloak v-if="show_password==true" class="zmdi zmdi-eye-off"></i>
		   </a>
		</div> 
       	
		
        <?php if(!empty($terms)):?>
	    <p class="m-0 mt-3 mb-3"><?php echo $terms;?></p>
	    <?php endif;?>
	   
	   <div  v-cloak v-if="error.length>0" class="alert alert-warning" role="alert">
	    <p v-cloak v-for="err in error" class="m-1">{{err}}</p>	    
	   </div>
	   
	   <div  v-cloak v-if="success" class="alert alert-success" role="alert">
	    <p class="m-0">{{success}}</p>	    
	   </div>
	   	   
	   <button class="btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!DataValid"  >
          <span class="label"><?php echo t("Signup")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
       </button>
	   	   
	  </form> 
	   
	  
	   </DIV> <!--vue-register-->
	   	  
  
  </div> <!--center-->

</div> <!--login container-->

</div> <!--containter-->