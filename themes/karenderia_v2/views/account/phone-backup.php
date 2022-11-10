<DIV id="vue-contactphone" v-cloak  > 	
				
			<el-skeleton :loading="is_loading" animated>
			<template #template>
			<div class="border rounded p-1 mb-2">
				<el-skeleton :rows="1" />
			</div>
			</template>		   
			<template #default>
				<a @click="showChangePhone()" 
				href="javascript:;" class="d-block chevron-section promo-section d-flex align-items-center rounded mb-2">
				<div class="flexcol mr-2"><i class="zmdi zmdi-phone"></i></div>
				<div class="flexcol" > 
					<span class="bold"><?= !empty($contact_number) ? $contact_number : 'Required Fields: First name, Last name & Phone number' ?></span>
				</div>
				</a>
			</template>
			</el-skeleton>
			  
             <!--COMPONETS CHANGE PHONE-->			 
             <component-change-phone 
             ref="cphone"
             @set-phone="loadVerification"
			 is_mobile="<?php echo Yii::app()->params['isMobile'];?>"
			 default_country="<?php echo CJavaScript::quote($phone_default_country);?>"    
	         :only_countries='<?php echo json_encode($phone_country_list)?>'	
             :label="{
			    edit_phone: '<?php echo t("Edit phone number")?>',
			    country: '<?php echo CJavaScript::quote(t("Country"))?>', 
			    mobile_number: '<?php echo CJavaScript::quote(t("Mobile number"))?>',  
			    enter_ten_digit: '<?php echo CJavaScript::quote(t("enter a 10 digit phone number"))?>',
			    continue: '<?php echo CJavaScript::quote(t("Continue"))?>',
			    cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>',
			 }"
             >                                    
             </component-change-phone>
             <!--END COMPONETS CHANGE PHONE-->
             
             <component-change-phoneverify
             ref="cphoneverify"
             @after-submit="ChangePhone"
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
                        
           </DIV>  