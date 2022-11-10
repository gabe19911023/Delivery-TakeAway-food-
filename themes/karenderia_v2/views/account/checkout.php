<div class="container-fluid page-grey">

<div class="container">
   <div class="row">
     <div class="col-lg-8 col-md-12 mb-4 mb-lg-3 p-0 p-lg-2">
        <div class="card">
                       
          <!--Delivery method and time-->
          <div class="card-body">
          
          <?php $this->renderPartial("//components/schedule-order",array(
            'show'=>false
          ))?>
          
          <div class="row mb-3" >    
			     <div class="col d-flex justify-content-start align-items-center" >
			         <span class="badge badge-dark rounded-pill">1</span>
			         <h5 class="m-0 ml-2 section-title"><?php echo t("Order type and time")?></h5>			         
			     </div>		     
 		   </div> <!--row-->  
 		   
 		   <!--vue-transaction-->
 		   <DIV id="vue-transaction" v-cloak  >
 		   
		   <el-skeleton :loading="is_loading" animated>
		   <template #template>
		       <div class="border rounded p-1 mb-2">
			     <el-skeleton :rows="1" />
			   </div>
		   </template>		   
		   <template #default>

			<!--transaction-section-->
			<a href="javascript:;" class="d-block chevron-section transaction-section d-flex align-items-center rounded mb-2"
			@click="show" > 		   
				<div class="flexcol mr-2"> 		       
					<i  v-if="display_transaction_type==='dinein'" class="fas fa-chair"></i>
					<i  v-else-if="display_transaction_type === 'delivery'" class="fas fa-biking"></i>
					<i  v-else-if="display_transaction_type === 'pickup'" class="fas fa-walking"></i>
				</div>
				<div class="flexcol">
				
				<span  class=" mr-1" v-if="transactions[display_transaction_type]" >
					{{ transactions[display_transaction_type].service_name }}
				</span>
							
				<p class="m-0 text-muted"  v-if="delivery_option[display_data.whento_deliver]" >
					{{ delivery_option[display_data.whento_deliver].name }}
					
					<span v-if="display_data.whento_deliver=='now'">
						<template v-if="display_data.estimation!=''">{{ display_data.estimation }}</template> 
					</span>
					
					<span v-if="display_data.whento_deliver=='schedule'">
					<!--{{ display_data.pretty_delivery_date }} -->
					{{ display_data.pretty_delivery_time }}
					</span>
					
				</p>
				
				<p  class="m-0 text-muted" v-if="display_transaction_type=='delivery'">
					<template v-if="display_data.delivery_distance">
					{{ display_data.delivery_distance }}
					</template>
				</p>
							
				<div  class="alert alert-warning m-0 p-0" v-if="checkout_error.length>0">
					<p class="m-0" v-for="error in checkout_error">
					{{ error }}
					</p>
				</div>
				
				</div>
			</a>
			<!--transaction-section-->

		   </template>
		   </el-skeleton>

 		   <?php $this->renderPartial("//account/checkout-transaction")?>	   
 		   
           		  
 		   </DIV>
 		   <!--vue-transaction-->
 		   
 		   <!--CHANGE PHONE showChangePhone-->
           <div id="vue-contactphone" data-v-app="">
			    <a href="javascript:;" class="d-block chevron-section promo-section d-flex align-items-center rounded mb-2 phone-selection">
			        <div class="flexcol mr-2"><i class="zmdi zmdi-phone"></i></div>
			        <div class="flexcol"><span class="bold" id="phone-text"><?= $client->first_name.' '.$client->last_name.' '.$client->contact_phone ?></span></div>
			    </a>
			    <div class="modal" id="changephoneModal" tabindex="-1" role="dialog" aria-labelledby="changephoneModal" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="display: none;">
			        <div class="modal-dialog" role="document">
			        	<form class="forms mt-2 mb-2 phone-form" action="/api/UpdatePhone" method="post">
							<div class="modal-content">
				                <div class="modal-body" style="overflow-y: inherit !important;">
				                    <!-- <a href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a> -->
				                    <h4 class="m-0 mb-3 mt-3">Add Required Details</h4>
			                        <div class="form-label-group">
			                        	<input class="form-control form-control-text" placeholder="" id="first_name" name="first_name" type="text" value="<?= $client->first_name ?>" required>
			                        	<label for="first_name" class="required">First name *</label>
			                        </div>
			                        <div class="form-label-group">
			                        	<input class="form-control form-control-text" placeholder="" id="last_name" name="last_name" type="text" value="<?= $client->last_name ?>" required>
			                        	<label for="last_name" class="required">Last name *</label>
			                        </div>
			                        <div class="inputs-with-dropdown d-flex align-items-center mb-3">
			                            <div class="dropdown">
			                                <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gb.svg" id="selected-country-img"></button>
			                                <div class="dropdown-menu" x-placement="bottom-start" x-out-of-boundaries="" style="display: none; position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 5px, 0px);">
			                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 39-country" data-code="+39" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/it.svg" data-name="Italy">
			                                    	<div class="mr-2">
			                                    		<img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/it.svg">
			                                    	</div>
			                                    	<div>Italy</div>
			                                    </a>
			                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 44-country" data-code="+44" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gb.svg" data-name="United Kingdom">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gb.svg"></div>
			                                        <div>United Kingdom</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 376-country" data-code="+376" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ad.svg" data-name="Andorra">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ad.svg"></div>
			                                        <div>Andorra</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 971-country" data-code="+971" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ae.svg" data-name="United Arab Emirates">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ae.svg"></div>
			                                        <div>United Arab Emirates</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 93-country" data-code="+93" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/af.svg" data-name="Afghanistan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/af.svg"></div>
			                                        <div>Afghanistan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-268-country" data-code="+1-268" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ag.svg" data-name="Antigua and Barbuda">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ag.svg"></div>
			                                        <div>Antigua and Barbuda</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-264-country" data-code="+1-264" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ai.svg" data-name="Anguilla">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ai.svg"></div>
			                                        <div>Anguilla</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 355-country" data-code="+355" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/al.svg" data-name="Albania">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/al.svg"></div>
			                                        <div>Albania</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 374-country" data-code="+374" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/am.svg" data-name="Armenia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/am.svg"></div>
			                                        <div>Armenia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 599-country" data-code="+599" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/an.svg" data-name="Netherlands Antilles">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/an.svg"></div>
			                                        <div>Netherlands Antilles</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 244-country" data-code="+244" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ao.svg" data-name="Angola">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ao.svg"></div>
			                                        <div>Angola</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 672-country" data-code="+672" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/aq.svg" data-name="Antarctica">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/aq.svg"></div>
			                                        <div>Antarctica</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 54-country" data-code="+54" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ar.svg" data-name="Argentina">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ar.svg"></div>
			                                        <div>Argentina</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-684-country" data-code="+1-684" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/as.svg" data-name="American Samoa">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/as.svg"></div>
			                                        <div>American Samoa</div>
			                                    </a>												
									            <a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 43-country" data-code="+43" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/at.svg" data-name="Austria">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/at.svg"></div>
			                                        <div>Austria</div>
			                                    </a>
											<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 61-country" data-code="+61" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/au.svg" data-name="Australia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/au.svg"></div>
			                                        <div>Australia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 297-country" data-code="+297" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/aw.svg" data-name="Aruba">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/aw.svg"></div>
			                                        <div>Aruba</div>
			                                    </a>															              
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 994-country" data-code="+994" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/az.svg" data-name="Azerbaijan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/az.svg"></div>
			                                        <div>Azerbaijan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 387-country" data-code="+387" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ba.svg" data-name="Bosnia and Herzegovina">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ba.svg"></div>
			                                        <div>Bosnia and Herzegovina</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-246-country" data-code="+1-246" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bb.svg" data-name="Barbados">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bb.svg"></div>
			                                        <div>Barbados</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 880-country" data-code="+880" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bd.svg" data-name="Bangladesh">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bd.svg"></div>
			                                        <div>Bangladesh</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 32-country" data-code="+32" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/be.svg" data-name="Belgium">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/be.svg"></div>
			                                        <div>Belgium</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 226-country" data-code="+226" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bf.svg" data-name="Burkina Faso">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bf.svg"></div>
			                                        <div>Burkina Faso</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 359-country" data-code="+359" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bg.svg" data-name="Bulgaria">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bg.svg"></div>
			                                        <div>Bulgaria</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 973-country" data-code="+973" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bh.svg" data-name="Bahrain">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bh.svg"></div>
			                                        <div>Bahrain</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 257-country" data-code="+257" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bi.svg" data-name="Burundi">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bi.svg"></div>
			                                        <div>Burundi</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 229-country" data-code="+229" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bj.svg" data-name="Benin">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bj.svg"></div>
			                                        <div>Benin</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 590-country" data-code="+590" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bl.svg" data-name="Saint Barthelemy">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bl.svg"></div>
			                                        <div>Saint Barthelemy</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-441-country" data-code="+1-441" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bm.svg" data-name="Bermuda">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bm.svg"></div>
			                                        <div>Bermuda</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 673-country" data-code="+673" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bn.svg" data-name="Brunei">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bn.svg"></div>
			                                        <div>Brunei</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 591-country" data-code="+591" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bo.svg" data-name="Bolivia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bo.svg"></div>
			                                        <div>Bolivia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 55-country" data-code="+55" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/br.svg" data-name="Brazil">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/br.svg"></div>
			                                        <div>Brazil</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-242-country" data-code="+1-242" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bs.svg" data-name="Bahamas">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bs.svg"></div>
			                                        <div>Bahamas</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 975-country" data-code="+975" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bt.svg" data-name="Bhutan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bt.svg"></div>
			                                        <div>Bhutan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 267-country" data-code="+267" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bw.svg" data-name="Botswana">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bw.svg"></div>
			                                        <div>Botswana</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 375-country" data-code="+375" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/by.svg" data-name="Belarus">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/by.svg"></div>
			                                        <div>Belarus</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 501-country" data-code="+501" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bz.svg" data-name="Belize">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/bz.svg"></div>
			                                        <div>Belize</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-country" data-code="+1" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ca.svg" data-name="Canada">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ca.svg"></div>
			                                        <div>Canada</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 61-country" data-code="+61" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cc.svg" data-name="Cocos Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cc.svg"></div>
			                                        <div>Cocos Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 243-country" data-code="+243" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cd.svg" data-name="Democratic Republic of the Congo">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cd.svg"></div>
			                                        <div>Democratic Republic of the Congo</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 236-country" data-code="+236" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cf.svg" data-name="Central African Republic">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cf.svg"></div>
			                                        <div>Central African Republic</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 242-country" data-code="+242" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cg.svg" data-name="Republic of the Congo">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cg.svg"></div>
			                                        <div>Republic of the Congo</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 41-country" data-code="+41" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ch.svg" data-name="Switzerland">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ch.svg"></div>
			                                        <div>Switzerland</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 225-country" data-code="+225" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ci.svg" data-name="Ivory Coast">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ci.svg"></div>
			                                        <div>Ivory Coast</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 682-country" data-code="+682" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ck.svg" data-name="Cook Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ck.svg"></div>
			                                        <div>Cook Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 56-country" data-code="+56" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cl.svg" data-name="Chile">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cl.svg"></div>
			                                        <div>Chile</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 237-country" data-code="+237" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cm.svg" data-name="Cameroon">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cm.svg"></div>
			                                        <div>Cameroon</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 86-country" data-code="+86" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cn.svg" data-name="China">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cn.svg"></div>
			                                        <div>China</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 57-country" data-code="+57" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/co.svg" data-name="Colombia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/co.svg"></div>
			                                        <div>Colombia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 506-country" data-code="+506" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cr.svg" data-name="Costa Rica">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cr.svg"></div>
			                                        <div>Costa Rica</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 53-country" data-code="+53" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cu.svg" data-name="Cuba">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cu.svg"></div>
			                                        <div>Cuba</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 238-country" data-code="+238" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cv.svg" data-name="Cape Verde">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cv.svg"></div>
			                                        <div>Cape Verde</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 599-country" data-code="+599" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cw.svg" data-name="Curacao">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cw.svg"></div>
			                                        <div>Curacao</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 61-country" data-code="+61" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cx.svg" data-name="Christmas Island">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cx.svg"></div>
			                                        <div>Christmas Island</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 357-country" data-code="+357" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cy.svg" data-name="Cyprus">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cy.svg"></div>
			                                        <div>Cyprus</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 420-country" data-code="+420" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cz.svg" data-name="Czech Republic">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/cz.svg"></div>
			                                        <div>Czech Republic</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 49-country" data-code="+49" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/de.svg" data-name="Germany">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/de.svg"></div>
			                                        <div>Germany</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 253-country" data-code="+253" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dj.svg" data-name="Djibouti">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dj.svg"></div>
			                                        <div>Djibouti</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 45-country" data-code="+45" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dk.svg" data-name="Denmark">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dk.svg"></div>
			                                        <div>Denmark</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-767-country" data-code="+1-767" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dm.svg" data-name="Dominica">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dm.svg"></div>
			                                        <div>Dominica</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-809, 1-829, 1-849-country" data-code="+1-809, 1-829, 1-849" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/do.svg" data-name="Dominican Republic">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/do.svg"></div>
			                                        <div>Dominican Republic</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 213-country" data-code="+213" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dz.svg" data-name="Algeria">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/dz.svg"></div>
			                                        <div>Algeria</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 593-country" data-code="+593" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ec.svg" data-name="Ecuador">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ec.svg"></div>
			                                        <div>Ecuador</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 372-country" data-code="+372" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ee.svg" data-name="Estonia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ee.svg"></div>
			                                        <div>Estonia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 20-country" data-code="+20" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/eg.svg" data-name="Egypt">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/eg.svg"></div>
			                                        <div>Egypt</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 212-country" data-code="+212" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/eh.svg" data-name="Western Sahara">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/eh.svg"></div>
			                                        <div>Western Sahara</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 291-country" data-code="+291" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/er.svg" data-name="Eritrea">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/er.svg"></div>
			                                        <div>Eritrea</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 34-country" data-code="+34" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/es.svg" data-name="Spain">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/es.svg"></div>
			                                        <div>Spain</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 251-country" data-code="+251" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/et.svg" data-name="Ethiopia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/et.svg"></div>
			                                        <div>Ethiopia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 358-country" data-code="+358" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fi.svg" data-name="Finland">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fi.svg"></div>
			                                        <div>Finland</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 679-country" data-code="+679" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fj.svg" data-name="Fiji">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fj.svg"></div>
			                                        <div>Fiji</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 500-country" data-code="+500" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fk.svg" data-name="Falkland Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fk.svg"></div>
			                                        <div>Falkland Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 691-country" data-code="+691" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fm.svg" data-name="Micronesia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fm.svg"></div>
			                                        <div>Micronesia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 298-country" data-code="+298" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fo.svg" data-name="Faroe Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fo.svg"></div>
			                                        <div>Faroe Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 33-country" data-code="+33" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fr.svg" data-name="France">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/fr.svg"></div>
			                                        <div>France</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 241-country" data-code="+241" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ga.svg" data-name="Gabon">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ga.svg"></div>
			                                        <div>Gabon</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-473-country" data-code="+1-473" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gd.svg" data-name="Grenada">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gd.svg"></div>
			                                        <div>Grenada</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 995-country" data-code="+995" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ge.svg" data-name="Georgia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ge.svg"></div>
			                                        <div>Georgia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 44-1481-country" data-code="+44-1481" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gg.svg" data-name="Guernsey">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gg.svg"></div>
			                                        <div>Guernsey</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 233-country" data-code="+233" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gh.svg" data-name="Ghana">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gh.svg"></div>
			                                        <div>Ghana</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 350-country" data-code="+350" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gi.svg" data-name="Gibraltar">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gi.svg"></div>
			                                        <div>Gibraltar</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 299-country" data-code="+299" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gl.svg" data-name="Greenland">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gl.svg"></div>
			                                        <div>Greenland</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 220-country" data-code="+220" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gm.svg" data-name="Gambia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gm.svg"></div>
			                                        <div>Gambia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 224-country" data-code="+224" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gn.svg" data-name="Guinea">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gn.svg"></div>
			                                        <div>Guinea</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 240-country" data-code="+240" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gq.svg" data-name="Equatorial Guinea">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gq.svg"></div>
			                                        <div>Equatorial Guinea</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 291-country" data-code="+291" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gr.svg" data-name="Eritrea">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gr.svg"></div>
			                                        <div>Eritrea</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 502-country" data-code="+502" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gt.svg" data-name="Guatemala">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gt.svg"></div>
			                                        <div>Guatemala</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-671-country" data-code="+1-671" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gu.svg" data-name="Guam">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gu.svg"></div>
			                                        <div>Guam</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 245-country" data-code="+245" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gw.svg" data-name="Guinea-Bissau">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gw.svg"></div>
			                                        <div>Guinea-Bissau</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 592-country" data-code="+592" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gy.svg" data-name="Guyana">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/gy.svg"></div>
			                                        <div>Guyana</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 852-country" data-code="+852" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hk.svg" data-name="Hong Kong">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hk.svg"></div>
			                                        <div>Hong Kong</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 504-country" data-code="+504" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hn.svg" data-name="Honduras">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hn.svg"></div>
			                                        <div>Honduras</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 385-country" data-code="+385" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hr.svg" data-name="Croatia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hr.svg"></div>
			                                        <div>Croatia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 509-country" data-code="+509" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ht.svg" data-name="Haiti">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ht.svg"></div>
			                                        <div>Haiti</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 36-country" data-code="+36" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hu.svg" data-name="Hungary">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/hu.svg"></div>
			                                        <div>Hungary</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 62-country" data-code="+62" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/id.svg" data-name="Indonesia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/id.svg"></div>
			                                        <div>Indonesia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 353-country" data-code="+353" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ie.svg" data-name="Ireland">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ie.svg"></div>
			                                        <div>Ireland</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 972-country" data-code="+972" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/il.svg" data-name="Israel">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/il.svg"></div>
			                                        <div>Israel</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 44-1624-country" data-code="+44-1624" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/im.svg" data-name="Isle of Man">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/im.svg"></div>
			                                        <div>Isle of Man</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 91-country" data-code="+91" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/in.svg" data-name="India">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/in.svg"></div>
			                                        <div>India</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 246-country" data-code="+246" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/io.svg" data-name="British Indian Ocean Territory">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/io.svg"></div>
			                                        <div>British Indian Ocean Territory</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 964-country" data-code="+964" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/iq.svg" data-name="Iraq">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/iq.svg"></div>
			                                        <div>Iraq</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 98-country" data-code="+98" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ir.svg" data-name="Iran">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ir.svg"></div>
			                                        <div>Iran</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 354-country" data-code="+354" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/is.svg" data-name="Iceland">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/is.svg"></div>
			                                        <div>Iceland</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 44-1534-country" data-code="+44-1534" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/je.svg" data-name="Jersey">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/je.svg"></div>
			                                        <div>Jersey</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-876-country" data-code="+1-876" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/jm.svg" data-name="Jamaica">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/jm.svg"></div>
			                                        <div>Jamaica</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 962-country" data-code="+962" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/jo.svg" data-name="Jordan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/jo.svg"></div>
			                                        <div>Jordan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 81-country" data-code="+81" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/jp.svg" data-name="Japan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/jp.svg"></div>
			                                        <div>Japan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 254-country" data-code="+254" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ke.svg" data-name="Kenya">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ke.svg"></div>
			                                        <div>Kenya</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 996-country" data-code="+996" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kg.svg" data-name="Kyrgyzstan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kg.svg"></div>
			                                        <div>Kyrgyzstan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 855-country" data-code="+855" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kh.svg" data-name="Cambodia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kh.svg"></div>
			                                        <div>Cambodia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 686-country" data-code="+686" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ki.svg" data-name="Kiribati">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ki.svg"></div>
			                                        <div>Kiribati</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 269-country" data-code="+269" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/km.svg" data-name="Comoros">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/km.svg"></div>
			                                        <div>Comoros</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-869-country" data-code="+1-869" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kn.svg" data-name="Saint Kitts and Nevis">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kn.svg"></div>
			                                        <div>Saint Kitts and Nevis</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 850-country" data-code="+850" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kp.svg" data-name="North Korea">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kp.svg"></div>
			                                        <div>North Korea</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 82-country" data-code="+82" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kr.svg" data-name="South Korea">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kr.svg"></div>
			                                        <div>South Korea</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 965-country" data-code="+965" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kw.svg" data-name="Kuwait">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kw.svg"></div>
			                                        <div>Kuwait</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-345-country" data-code="+1-345" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ky.svg" data-name="Cayman Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ky.svg"></div>
			                                        <div>Cayman Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 7-country" data-code="+7" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kz.svg" data-name="Kazakhstan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/kz.svg"></div>
			                                        <div>Kazakhstan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 856-country" data-code="+856" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/la.svg" data-name="Laos">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/la.svg"></div>
			                                        <div>Laos</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 961-country" data-code="+961" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lb.svg" data-name="Lebanon">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lb.svg"></div>
			                                        <div>Lebanon</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-758-country" data-code="+1-758" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lc.svg" data-name="Saint Lucia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lc.svg"></div>
			                                        <div>Saint Lucia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 423-country" data-code="+423" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/li.svg" data-name="Liechtenstein">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/li.svg"></div>
			                                        <div>Liechtenstein</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 94-country" data-code="+94" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lk.svg" data-name="Sri Lanka">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lk.svg"></div>
			                                        <div>Sri Lanka</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 231-country" data-code="+231" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lr.svg" data-name="Liberia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lr.svg"></div>
			                                        <div>Liberia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 266-country" data-code="+266" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ls.svg" data-name="Lesotho">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ls.svg"></div>
			                                        <div>Lesotho</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 370-country" data-code="+370" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lt.svg" data-name="Lithuania">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lt.svg"></div>
			                                        <div>Lithuania</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 352-country" data-code="+352" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lu.svg" data-name="Luxembourg">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lu.svg"></div>
			                                        <div>Luxembourg</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 371-country" data-code="+371" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lv.svg" data-name="Latvia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/lv.svg"></div>
			                                        <div>Latvia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 218-country" data-code="+218" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ly.svg" data-name="Libya">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ly.svg"></div>
			                                        <div>Libya</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 212-country" data-code="+212" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ma.svg" data-name="Morocco">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ma.svg"></div>
			                                        <div>Morocco</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 377-country" data-code="+377" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mc.svg" data-name="Monaco">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mc.svg"></div>
			                                        <div>Monaco</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 373-country" data-code="+373" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/md.svg" data-name="Moldova">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/md.svg"></div>
			                                        <div>Moldova</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 382-country" data-code="+382" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/me.svg" data-name="Montenegro">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/me.svg"></div>
			                                        <div>Montenegro</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 590-country" data-code="+590" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mf.svg" data-name="Saint Martin">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mf.svg"></div>
			                                        <div>Saint Martin</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 261-country" data-code="+261" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mg.svg" data-name="Madagascar">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mg.svg"></div>
			                                        <div>Madagascar</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 692-country" data-code="+692" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mh.svg" data-name="Marshall Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mh.svg"></div>
			                                        <div>Marshall Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 389-country" data-code="+389" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mk.svg" data-name="Macedonia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mk.svg"></div>
			                                        <div>Macedonia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 223-country" data-code="+223" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ml.svg" data-name="Mali">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ml.svg"></div>
			                                        <div>Mali</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 95-country" data-code="+95" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mm.svg" data-name="Myanmar">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mm.svg"></div>
			                                        <div>Myanmar</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 976-country" data-code="+976" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mn.svg" data-name="Mongolia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mn.svg"></div>
			                                        <div>Mongolia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 853-country" data-code="+853" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mo.svg" data-name="Macau">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mo.svg"></div>
			                                        <div>Macau</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-670-country" data-code="+1-670" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mp.svg" data-name="Northern Mariana Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mp.svg"></div>
			                                        <div>Northern Mariana Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 222-country" data-code="+222" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mr.svg" data-name="Mauritania">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mr.svg"></div>
			                                        <div>Mauritania</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-664-country" data-code="+1-664" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ms.svg" data-name="Montserrat">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ms.svg"></div>
			                                        <div>Montserrat</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 356-country" data-code="+356" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mt.svg" data-name="Malta">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mt.svg"></div>
			                                        <div>Malta</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 230-country" data-code="+230" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mu.svg" data-name="Mauritius">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mu.svg"></div>
			                                        <div>Mauritius</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 960-country" data-code="+960" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mv.svg" data-name="Maldives">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mv.svg"></div>
			                                        <div>Maldives</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 265-country" data-code="+265" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mw.svg" data-name="Malawi">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mw.svg"></div>
			                                        <div>Malawi</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 52-country" data-code="+52" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mx.svg" data-name="Mexico">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mx.svg"></div>
			                                        <div>Mexico</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 60-country" data-code="+60" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/my.svg" data-name="Malaysia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/my.svg"></div>
			                                        <div>Malaysia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 258-country" data-code="+258" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mz.svg" data-name="Mozambique">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/mz.svg"></div>
			                                        <div>Mozambique</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 264-country" data-code="+264" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/na.svg" data-name="Namibia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/na.svg"></div>
			                                        <div>Namibia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 687-country" data-code="+687" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nc.svg" data-name="New Caledonia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nc.svg"></div>
			                                        <div>New Caledonia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 227-country" data-code="+227" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ne.svg" data-name="Niger">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ne.svg"></div>
			                                        <div>Niger</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 234-country" data-code="+234" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ng.svg" data-name="Nigeria">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ng.svg"></div>
			                                        <div>Nigeria</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 505-country" data-code="+505" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ni.svg" data-name="Nicaragua">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ni.svg"></div>
			                                        <div>Nicaragua</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 31-country" data-code="+31" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nl.svg" data-name="Netherlands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nl.svg"></div>
			                                        <div>Netherlands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 47-country" data-code="+47" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/no.svg" data-name="Norway">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/no.svg"></div>
			                                        <div>Norway</div>
													<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 977-country" data-code="+977" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/np.svg" data-name="Nepal">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/np.svg"></div>
			                                        <div>Nepal</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 674-country" data-code="+674" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nr.svg" data-name="Nauru">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nr.svg"></div>
			                                        <div>Nauru</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 683-country" data-code="+683" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nu.svg" data-name="Niue">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nu.svg"></div>
			                                        <div>Niue</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 64-country" data-code="+64" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nz.svg" data-name="New Zealand">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/nz.svg"></div>
			                                        <div>New Zealand</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 968-country" data-code="+968" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/om.svg" data-name="Oman">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/om.svg"></div>
			                                        <div>Oman</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 507-country" data-code="+507" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pa.svg" data-name="Panama">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pa.svg"></div>
			                                        <div>Panama</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 51-country" data-code="+51" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pe.svg" data-name="Peru">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pe.svg"></div>
			                                        <div>Peru</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 689-country" data-code="+689" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pf.svg" data-name="French Polynesia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pf.svg"></div>
			                                        <div>French Polynesia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 675-country" data-code="+675" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pg.svg" data-name="Papua New Guinea">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pg.svg"></div>
			                                        <div>Papua New Guinea</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 63-country" data-code="+63" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ph.svg" data-name="Philippines">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ph.svg"></div>
			                                        <div>Philippines</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 92-country" data-code="+92" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pk.svg" data-name="Pakistan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pk.svg"></div>
			                                        <div>Pakistan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 48-country" data-code="+48" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pl.svg" data-name="Poland">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pl.svg"></div>
			                                        <div>Poland</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 508-country" data-code="+508" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pm.svg" data-name="Saint Pierre and Miquelon">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pm.svg"></div>
			                                        <div>Saint Pierre and Miquelon</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 64-country" data-code="+64" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pn.svg" data-name="Pitcairn">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pn.svg"></div>
			                                        <div>Pitcairn</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-787, 1-939-country" data-code="+1-787, 1-939" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pr.svg" data-name="Puerto Rico">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pr.svg"></div>
			                                        <div>Puerto Rico</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 970-country" data-code="+970" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ps.svg" data-name="Palestine">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ps.svg"></div>
			                                        <div>Palestine</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 351-country" data-code="+351" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pt.svg" data-name="Portugal">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pt.svg"></div>
			                                        <div>Portugal</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 680-country" data-code="+680" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pw.svg" data-name="Palau">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/pw.svg"></div>
			                                        <div>Palau</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 595-country" data-code="+595" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/py.svg" data-name="Paraguay">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/py.svg"></div>
			                                        <div>Paraguay</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 974-country" data-code="+974" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/qa.svg" data-name="Qatar">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/qa.svg"></div>
			                                        <div>Qatar</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 262-country" data-code="+262" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/re.svg" data-name="Reunion">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/re.svg"></div>
			                                        <div>Reunion</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 40-country" data-code="+40" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ro.svg" data-name="Romania">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ro.svg"></div>
			                                        <div>Romania</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 381-country" data-code="+381" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/rs.svg" data-name="Serbia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/rs.svg"></div>
			                                        <div>Serbia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 7-country" data-code="+7" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ru.svg" data-name="Russia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ru.svg"></div>
			                                        <div>Russia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 250-country" data-code="+250" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/rw.svg" data-name="Rwanda">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/rw.svg"></div>
			                                        <div>Rwanda</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 966-country" data-code="+966" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sa.svg" data-name="Saudi Arabia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sa.svg"></div>
			                                        <div>Saudi Arabia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 677-country" data-code="+677" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sb.svg" data-name="Solomon Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sb.svg"></div>
			                                        <div>Solomon Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 248-country" data-code="+248" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sc.svg" data-name="Seychelles">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sc.svg"></div>
			                                        <div>Seychelles</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 249-country" data-code="+249" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sd.svg" data-name="Sudan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sd.svg"></div>
			                                        <div>Sudan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 46-country" data-code="+46" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/se.svg" data-name="Sweden">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/se.svg"></div>
			                                        <div>Sweden</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 65-country" data-code="+65" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sg.svg" data-name="Singapore">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sg.svg"></div>
			                                        <div>Singapore</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 290-country" data-code="+290" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sh.svg" data-name="Saint Helena">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sh.svg"></div>
			                                        <div>Saint Helena</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 386-country" data-code="+386" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/si .svg" data-name="Slovenia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/si.svg"></div>
			                                        <div>Slovenia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 47-country" data-code="+47" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sj .svg" data-name="Svalbard and Jan Mayen">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sj.svg"></div>
			                                        <div>Svalbard and Jan Mayen</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 232-country" data-code="+232" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sl .svg" data-name="Sierra Leone">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sl.svg"></div>
			                                        <div>Sierra Leone</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 378-country" data-code="+378" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sm .svg" data-name="San Marino">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sm.svg"></div>
			                                        <div>San Marino</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 221-country" data-code="+221" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sn .svg" data-name="Senegal">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sn.svg"></div>
			                                        <div>Senegal</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 252-country" data-code="+252" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/so .svg" data-name="Somalia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/so.svg"></div>
			                                        <div>Somalia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 597-country" data-code="+597" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sr .svg" data-name="Suriname">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sr.svg"></div>
			                                        <div>Suriname</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 211-country" data-code="+211" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ss .svg" data-name="South Sudan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ss.svg"></div>
			                                        <div>South Sudan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 239-country" data-code="+239" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/st .svg" data-name="Sao Tome and Principe">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/st.svg"></div>
			                                        <div>Sao Tome and Principe</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 503-country" data-code="+503" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sv .svg" data-name="El Salvador">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sv.svg"></div>
			                                        <div>El Salvador</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-721-country" data-code="+1-721" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sx .svg" data-name="Sint Maarten">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sx.svg"></div>
			                                        <div>Sint Maarten</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 963-country" data-code="+963" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sy .svg" data-name="Syria">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sy.svg"></div>
			                                        <div>Syria</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 268-country" data-code="+268" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sz .svg" data-name="Swaziland">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/sz.svg"></div>
			                                        <div>Swaziland</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-649-country" data-code="+1-649" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tc .svg" data-name="Turks and Caicos Islands">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tc.svg"></div>
			                                        <div>Turks and Caicos Islands</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 235-country" data-code="+235" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/td .svg" data-name="Chad">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/td.svg"></div>
			                                        <div>Chad</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 228-country" data-code="+228" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tg .svg" data-name="Togo">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tg.svg"></div>
			                                        <div>Togo</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 66-country" data-code="+66" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/th .svg" data-name="Thailand">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/th.svg"></div>
			                                        <div>Thailand</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 992-country" data-code="+992" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tj .svg" data-name="Tajikistan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tj.svg"></div>
			                                        <div>Tajikistan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 690-country" data-code="+690" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tk .svg" data-name="Tokelau">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tk.svg"></div>
			                                        <div>Tokelau</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 670-country" data-code="+670" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tl .svg" data-name="East Timor">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tl.svg"></div>
			                                        <div>East Timor</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 993-country" data-code="+993" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tm .svg" data-name="Turkmenistan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tm.svg"></div>
			                                        <div>Turkmenistan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 216-country" data-code="+216" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tn .svg" data-name="Tunisia">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tn.svg"></div>
			                                        <div>Tunisia</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 676-country" data-code="+676" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/to .svg" data-name="Tonga">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/to.svg"></div>
			                                        <div>Tonga</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 90-country" data-code="+90" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tr .svg" data-name="Turkey">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tr.svg"></div>
			                                        <div>Turkey</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-868-country" data-code="+1-868" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tt .svg" data-name="Trinidad and Tobago">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tt.svg"></div>
			                                        <div>Trinidad and Tobago</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 688-country" data-code="+688" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tv .svg" data-name="Tuvalu">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tv.svg"></div>
			                                        <div>Tuvalu</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 886-country" data-code="+886" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tw .svg" data-name="Taiwan">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tw.svg"></div>
			                                        <div>Taiwan</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 255-country" data-code="+255" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tz .svg" data-name="Tanzania">
			                                        <div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/tz.svg"></div>
			                                        <div>Tanzania</div>
			                                    </a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 380-country" data-code="+380" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ua.svg" data-name="Ukraine">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ua.svg"></div>
													<div>Ukraine</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 256-country" data-code="+256" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ug.svg" data-name="Uganda">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ug.svg"></div>
													<div>Uganda</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 581-country" data-code="+581" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/um.svg" data-name="United States Minor Outlying Islands">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/um.svg"></div>
													<div>U. S. Minor Outlying Islands</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-country" data-code="+1" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/us.svg" data-name="United States">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/us.svg"></div>
													<div>United States</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 598-country" data-code="+598" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/uy.svg" data-name="Uruguay">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/uy.svg"></div>
													<div>Uruguay</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 998-country" data-code="+998" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/uz.svg" data-name="Uzbekistan">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/uz.svg"></div>
													<div>Uzbekistan</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 379-country" data-code="+379" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/va.svg" data-name="Vatican">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/va.svg"></div>
													<div>Vatican</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-784-country" data-code="+1-784" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vc.svg" data-name="Saint Vincent and the Grenadines">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vc.svg"></div>
													<div>Saint Vincent and the Grenadines</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 58-country" data-code="+58" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ve.svg" data-name="Venezuela">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ve.svg"></div>
													<div>Venezuela</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-284-country" data-code="+1-284" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vg.svg" data-name="British Virgin Islands">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vg.svg"></div>
													<div>British Virgin Islands</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 1-340-country" data-code="+1-340" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vi.svg" data-name="U.S. Virgin Islands">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vi.svg"></div>
													<div>U.S. Virgin Islands</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 84-country" data-code="+84" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vn.svg" data-name="Vietnam">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vn.svg"></div>
													<div>Vietnam</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 678-country" data-code="+678" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vu.svg" data-name="Vanuatu">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/vu.svg"></div>
													<div>Vanuatu</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 681-country" data-code="+681" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/wf.svg" data-name="Wallis and Futuna">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/wf.svg"></div>
													<div>Wallis and Futuna</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 685-country" data-code="+685" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ws.svg" data-name="Samoa">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ws.svg"></div>
													<div>Samoa</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 383-country" data-code="+383" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/xk.svg" data-name="Kosovo">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/xk.svg"></div>
													<div>Kosovo</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 967-country" data-code="+967" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ye.svg" data-name="Yemen">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/ye.svg"></div>
													<div>Yemen</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 262-country" data-code="+262" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/yt.svg" data-name="Mayotte">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/yt.svg"></div>
													<div>Mayotte</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 27-country" data-code="+27" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/za.svg" data-name="South Africa">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/za.svg"></div>
													<div>South Africa</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 260-country" data-code="+260" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/zm.svg" data-name="Zambia">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/zm.svg"></div>
													<div>Zambia</div>
												</a>
												<a href="javascript:;" class="dropdown-item d-flex align-items-center country-selection 263-country" data-code="+263" data-image="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/zw.svg" data-name="Zimbabwe">
													<div class="mr-2"><img src="https://geamenu.co.uk/themes/karenderia_v2/assets/flag/zw.svg"></div>
													<div>Zimbabwe</div>
												</a>
			                                </div>
			                            </div>
			                            <div class="mr-0 ml-1 phone-code"><?= !empty($client->phone_prefix) ? $client->phone_prefix : '+44'  ?></div>
											<input type="hidden" id="phone-code-input" value="" name="mobile_number">
											<input type="hidden" id="mobile_prefix-input" value="<?= $client->phone_prefix ?>" name="mobile_prefix">
			                            <input type="text" name="phone_number" id="phone_number" value="<?= str_replace($client->phone_prefix, '', $client->contact_phone) ?>" data-mask="###################" data-mask-raw-value="" data-mask-inited="true" required>
			                        </div>
									<div class="form-label-group">
										<div id="recaptcha-container"></div>

										<div class="alert alert-warning mb-2 phone-error-container" role="alert" style="display:none;"><p class="m-0 phone-error"></p></div>
									</div>
				                </div>
				                <div class="modal-footer justify-content-start">
				                    <!-- <div class="border flex-fill"><button class="btn btn-black w-100">Cancel</button></div> -->
				                    <div class="border flex-fill">
				                        <button class="btn btn-green w-100 basic-info_submt" type="submit">
				                            <span class="label">Continue</span>
				                            <div class="m-auto circle-loader" data-loader="circle-side"></div>
				                        </button>
				                    </div>
				                </div>
				            </div>
			        	</form>
			        </div>
			    </div>
			    <div class="modal" id="verifyCodeModal" tabindex="-1" role="dialog" aria-labelledby="verifyCodeModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			        <div class="modal-dialog" role="document">
			            <div class="modal-content">
			                <form class="forms">
			                    <div class="modal-body">
			                        <!-- <a href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a> -->
			                        <div class="text-center">
			                            <h4 class="m-0 mb-3 mt-3">2-Step Verification</h4>
			                            <h6>For your security, we want to make sure it's really you.</h6>
			                        </div>
			                        <p class="bold">Enter 6-digit code</p>
			                        <div class="form-label-group">
										<input class="form-control form-control-text" placeholder="" type="text" maxlength="6" id="pin-code" data-mask="######" data-mask-raw-value="" data-mask-inited="true">
										<label for="code" class="required">Code</label>
									</div>
			                        <p class="bold"></p>
									<div class="alert alert-warning mb-2 pin-error" role="alert" style="display:none;"><p class="m-0">Invalid 6 digit code</p></div>
			                        <div class="alert alert-success mb-2 pin-success" role="alert" style="display:none;"><p class="m-0">Pin code validated successfully</p></div>
			                        <!-- <p><u>Resend Code in 20</u></p> -->
			                        <!---->
			                    </div>
			                    <div class="modal-footer justify-content-start">
			                        <button class="btn btn-green w-100" type="button" id="verify-btn">
			                            <span class="label">Submit</span>
			                            <div class="m-auto circle-loader" data-loader="circle-side"></div>
			                        </button>
			                    </div>
			                </form>
			            </div>
			        </div>
			    </div>
			</div>
			<script src="//code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
			<script src="//www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
			<script type="text/javascript">
			// Initialize Firebase
				var phone = "<?= $client->contact_phone ?>";
				const firebaseConfig = {
					apiKey: "AIzaSyDuPb4hOs-D4Ntb39dPigGpUAoJkCJk9rI",
					authDomain: "geamenu.firebaseapp.com",
					projectId: "geamenu",
					storageBucket: "geamenu.appspot.com",
					messagingSenderId: "45639342478",
					appId: "1:45639342478:web:85334d5eb138a4d0e4acdf",
					measurementId: "G-XD3JY160N9"
				};
				firebase.initializeApp(firebaseConfig);
				window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier(
				    "recaptcha-container",
				    {
				      size: "invisible",
				      callback: function(response) {
				        submitPhoneNumberAuth();
				      }
				    }
				  );
				$(document).ready(function(e){
					if(phone == '')
						$('#changephoneModal').modal('show');
				});
			</script>
           <!--CHANGE PHONE-->
 		   
 		   <!--promo-section--> 	
 		   <DIV v-cloak id="vue-promo">  
			 
			<el-skeleton :loading="is_loading" animated>
			<template #template>
			<div class="border rounded p-1 mb-2">
				<el-skeleton :rows="1" />
			</div>
			</template>		   
			<template #default>

			<template v-if="data.length>0">
			  <a @click="show" href="javascript:;" class="d-block chevron-section promo-section d-flex align-items-center rounded mb-2">
				<div class="flexcol mr-2"><i class="zmdi zmdi-label"></i></div>
				<div class="flexcol"> 		     		    
				<template v-if="promo_id.length<=0">
				<span class="bold">{{ data.length }}</span> <?php echo t("Promotion available")?>
				</template> 		       
				<template v-if="promo_id.length>0">
					<?php echo t("Promotion applied")?>
				</template>
							
				<p v-if="promo_id.length>0" class="m-0 text-success">{{promo_id[2]}}</p>
				
				</div> 		    		   
			</a>  		  
			<?php $this->renderPartial("//account/checkout-promo")?>
			</template>

			</template>
			</el-skeleton>
			 		 
 		   
 		    <!--COMPONENT PROMO CODE--> 		   
 		   <component-promocode 
 		   ref="childref"
 		   title="<?php echo t("Have a promo code?")?>"
 		   add_promo_code="<?php echo t("Add promo code")?>"
 		   apply_text="<?php echo t("Apply")?>"
 		   @back="show"
 		   @set-loadpromo="loadPromo"
			is_mobile="<?php echo Yii::app()->params['isMobile'];?>"
 		   >
 		   </component-promocode>
 		   <!--END COMPONENT PROMO CODE-->
 		   
 		   </DIV>	   		   
 		   <!--promo-section-->
 		   
 		   
 		   <!--add promo code manually--> 	
 		   <DIV v-cloak id="vue-add-promocode">   		   
 		   <template v-if="enabled"> 		   
 		   <a @click="show" href="javascript:;" class="d-block chevron-section promo-section d-flex align-items-center rounded mb-2">
 		    <div class="flexcol mr-2"><i class="zmdi zmdi-label"></i></div>
 		    <div class="flexcol"> 	 		       
 		       <span v-if="has_promocode===false"><?php echo t("Add promo code")?></span>
 		       <span v-else><?php echo t("Remove promo code")?></span>
 		       
 		       <p v-if="has_promocode" class="m-0 text-success">{{saving}}</p>
 		       
 		    </div> 		    		   
 		   </a>  		   		   
 		   </template>
 		   
 		    <!--COMPONENT PROMO CODE--> 		   
 		   <component-apply-promocode 
 		   ref="childref"
 		   title="<?php echo t("Have a promo code?")?>"
 		   add_promo_code="<?php echo t("Add promo code")?>"
 		   apply_text="<?php echo t("Apply")?>" 		   
 		   @back="show"
 		   @set-loadpromo="loadPromo"
 		   >
 		   </component-apply-promocode>
 		   <!--END COMPONENT PROMO CODE-->
 		   
 		   </DIV>	   		   
 		   <!--end add promo code-->
 		   
 		   
 		   <!--ADD UTENSILS-->
 		   <DIV v-cloak id="vue-utensils">  		   
 		   <div v-if="visible" class="d-block chevron-section d-flex align-items-center justify-content-between rounded mb-2"> 		     
 		      <div class="flexcol">
 		       <?php echo t("Include utensils and condoments")?>
 		     </div>
 		      		    
 		     <div>  		     
 		     <div class="custom-control custom-switch custom-switch-md">  			  
			  <input v-model="include_utensils" 
 		     id="include_utensil" type="checkbox" class="custom-control-input checkbox_child">
			  <label class="custom-control-label" for="include_utensil">
			   &nbsp;
			  </label>
			</div>        
 		     
 		     </div>
 		   </div>
 		   </DIV> <!--vue-utensils-->
 		   <!--END ADD UTENSILS-->
 		   
 		   <!--tips-->
 		   <DIV v-cloak id="vue-tips">   
			
			<el-skeleton :loading="is_loading" animated>
			<template #template>
			<div class="border rounded p-1 mb-2">
				<el-skeleton :rows="1" />
			</div>
			</template>		   
			<template #default>

			 <!-- TIPS -->

			 <template v-if="ifDelivery">
 		   <div class="d-block chevron-section d-flex align-items-center justify-content-between rounded mb-2"> 		    		    
 		    <div class="flexcol">
 		      <?php echo t("Tip the courier")?>
 		      <p class="m-0 mb-2"><?php echo t("Optional tip for the courier")?></p>
 		      
 		       <!--tips-->
		        <div class="btn-group btn-group-toggle input-group-small mb-3" >
		        
		           <label  class="btn tip-group-btn" v-for="tip in data" :class="{ active: tips == tip.value }"  >
		             <input type="radio" :value="tip.value" v-model="tips" @click="checkoutAddTips(tip.value)"> 
		             {{ tip.value == '0.00' ? 'No Tip' : tip.name  }}
		           </label>		        
		           
		        </div>
		        <!--tips-->
		        
		       <!--tips-other-->		       
		       <div v-if="ifOthers">
		       <div class="d-flex align-items-center">
		         <div class="flexcol mr-2">
		         
		           <input type="text" class="form-control form-control-text text-center" 
 		            type="text" v-model="manual_tip"  maxlength="2" style="width:80px;">   
		         
		         </div> <!--flexcol-->
		         <div class="flexcol">
		         <button @click="checkoutAddTips(manual_tip)" class="btn btn-green" :class="{ loading: is_loading }" >
		           <span class="label" ><?php echo t("Add tip")?></span>
		           <div class="m-auto circle-loader" data-loader="circle-side"></div>
		         </button>
		         </div>
		       </div>   
		       </div> 
		       <!--tips-other-->
		        
 		      
 		    </div> <!--flexcol-->	    
 		   </div>
 		   </template>

			 <!-- TIPS -->

			</template>
			</el-skeleton>
			 		   
 		   </DIV>
 		   <!--tips-->
 		   
 		   <!--ITEM SUGGESTION-->			
 		   <DIV id="vue-item-suggestion"> 		     
 		   <components-item-suggestion
 		   title="<?php echo t("People also ordered")?>" 
 		   merchant_id="<?php echo $merchant_id;?>"		 
 		   image_use="thumbnail"
 		   :settings="{		      
		      items: '<?php echo CJavaScript::quote(3)?>',      
		      lazyLoad: '<?php echo CJavaScript::quote(true)?>', 
		      loop: '<?php echo CJavaScript::quote(false)?>', 
		      margin: '<?php echo CJavaScript::quote(5)?>', 
		      nav: '<?php echo CJavaScript::quote(false)?>', 
		      dots: '<?php echo CJavaScript::quote(false)?>', 
		      stagePadding: '<?php echo CJavaScript::quote(0)?>',				  
		  }"  		  
 		   >
 		   </components-item-suggestion>
 		   
 		    		   
 		   </DIV> 		   
 		   <?php $this->renderPartial("//components/item-suggestion")?>
 		   <!--END ITEM SUGGESTION-->
 		   
 		   
          </div>
          <!--Delivery method and time-->
          
          <div class="divider p-0"></div>
         
          <!--vue-manage-address-->
          <DIV id="vue-manage-address" v-cloak >
          <input type="hidden" id="autoload" value="1">	
          <template v-if="ifDelivery">
           <div class="card-body">
           
             <div class="row mb-3" >    
			     <div class="col-lg-6 col-md-6 col d-flex justify-content-start align-items-center" >
			         <span class="badge badge-dark rounded-pill">2</span>
			         <h5 class="m-0 ml-2 section-title"><?php echo t("Choose a delivery address")?></h5>			         
			     </div>		     
			     
			     <div class="col-lg-6 col-md-6 col-1 d-flex justify-content-end"> 
			       <a href="javascript:;"  @click="showNewAddress" class="d-flex align-items-center" >
			        <span class="bold d-none d-md-block"><?php echo t("Add new address")?></span>  
			        <span class="badge btn-black rounded-pill ml-1 font20">+</span>
			       </a>
			     </div>			     
	 		 </div> <!--row-->    
	 		 
	 		 <!--COMPONENTS CHANGE ADDRESS-->	 		    
			<component-address 
			ref="childref"
			:label="{
			    title:'<?php echo CJavaScript::quote(t("Change address"))?>', 
			    enter_address: '<?php echo CJavaScript::quote(t("Enter delivery address"))?>',	    	    
			}"
			:addresses="addresses"
			:location_data="location_data"
			@set-location="setLocationDetails"
			@set-edit="setLocationDetails"
			@set-placeid="setPlaceData"
			@load-data="loadData"
			>
			</component-address>
			<!--END COMPONENTS CHANGE ADDRESS-->
	 		  
	 		<template v-if="hasLocationData">
	 		   <a @click="show" href="javascript:;" class="d-block chevron-section promo-section d-flex align-items-center rounded mb-2">
	 		    <div class="flexcol mr-2"><i class="zmdi zmdi-pin"></i></div>
	 		    <div class="flexcol" > 		     		     		      
	                <span  class="bold mr-1">{{ location_data.address.formatted_address }}</span>
	                <p   class="m-0 text-muted" v-if="delivery_options!== ''">
	                 <?php echo t("Delivery options")?>: {{delivery_options}}
	                </p>
	                
	                <div  v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
			          <p  v-for="err in error" class="m-0">{{err}}</p>	    
			       </div>   			   
			   
	 		    </div> 		    		    		    
	 		   </a>   		    		    	
	 		 </template>	    
	 		 
             <?php $this->renderPartial("//account/checkout-address")?>
            
           </div> <!--card body-->
           <div class="divider p-0"></div>
          </template>
          </DIV> 
          <!--vue-manage-address-->
          
          <!--PAYMENT METHOD-->
          <div class="card-body">
          <DIV id="vue-payment-list" v-cloak>
             <div class="row mb-3" >              
              <div class="col d-flex justify-content-start align-items-center" >              
		         <span class="badge badge-dark rounded-pill">
		         <template v-if="transaction_type==='delivery'">
		         3
		         </template>
		         <template v-else>
		         2
		         </template>
		         </span>
		         <h5 class="m-0 ml-2 section-title"><?php echo t("Choose Payment Methods")?></h5>			         
			  </div>	             
             </div> <!--row-->
             
             <!--SAVE PAYMENT METHOD-->        			 
			<!-- <el-skeleton :count="3" :loading="saved_payment_loading" animated>
			<template #template>
			<div class="border rounded p-1 mb-2">
				<el-skeleton :rows="1" />
			</div>
			</template>		   
			<template #default> -->

			<!-- <template v-if="hasSavedPayment">    
	             <div v-for="saved_payment in data_saved_payment" class="row no-gutters align-items-center chevron-section medium rounded mb-2"  :class="{ selected: saved_payment.as_default==1 }" >
	             
	              <div class="col-lg-8 col-md-8 col-10 d-flex align-items-center">
	                <div class="flexcol mr-0 mr-lg-2 payment-logo-wrap">
		 		      <i v-if="saved_payment.logo_type=='icon'" :class="saved_payment.logo_class"></i>
		 		      <img v-else class="img-35 contain" :src="saved_payment.logo_image" /> 		      
		 		    </div> 
		 		    <div class="flexcol" > 		     		     		      
		 		       <span class=" mr-1">{{saved_payment.attr1}}</span>       
		 		       <p class="m-0 text-muted">{{saved_payment.attr2}}</p>   
		 		    </div> 		    		    		    
	              </div> 
	              <div class="col-lg-4 col-md-4 col-2  d-flex align-items-center justify-content-end">
	                         
	                 <template v-if="saved_payment.as_default==1">
		                 <div class="mr-1 d-none d-md-block"><i class="zmdi zmdi-check text-success"></i></div>
		                 <div class="mr-3 d-none d-md-block"><p class="m-0"><?php echo t("Default")?></p></div>
	                 </template>
	                 
		             <div class="dropdown" v-if="saved_payment.as_default!=1">
			             <a href="javascript:;" class="rounded-pill rounded-button-icon d-inline-block" 
			             id="dropdownMenuLink" data-toggle="dropdown" >
			               <i class="zmdi zmdi-more" style="font-size: inherit;"></i>
			             </a>
		                 <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
						    <a  v-if="saved_payment.as_default!=1" @click="setDefaultPayment(saved_payment.payment_uuid)" class="dropdown-item a-12" href="javascript:;"><?php echo t("Set Default")?></a> -->
						    
						    <!-- <a @click="deleteSavedPaymentMethod(saved_payment.payment_uuid,saved_payment.payment_code)" class="dropdown-item a-12" href="javascript:;"><?php //echo t("Delete")?></a>	 -->			    
						 <!--  </div>
		             </div>
		              
	              </div> 
	             </div>
             </template> -->
             <!--END SAVE PAYMENT METHOD-->

			<!-- </template>
			</el-skeleton> -->

             
             <!-- <h5 class="mb-3 mt-4"><?php //echo t("Add New Payment Method")?></h5> -->

			<el-skeleton :count="3" :loading="payment_list_loading" animated>
			<template #template>
			<div class="border rounded p-1 mb-2">
				<el-skeleton :rows="1" />
			</div>
			</template>		   
			<template #default>

			<template v-if="hasData">
             <a v-for="payment in data" @click="showPayment(payment.payment_code)" class="d-block chevron-section medium d-flex align-items-center rounded mb-2" :class="{ 'cbg-success': payment.as_default==1 }">
	 		    <div class="flexcol mr-0 mr-lg-2  payment-logo-wrap">
	 		      <i v-if="payment.logo_type=='icon'" :class="payment.logo_class"></i>
	 		      <img v-else class="img-35 contain" :src="payment.logo_image" />
	 		    </div>
	 		    
	 		    <div class="flexcol" > 		     		     		      
	 		       <span class="mr-1">{{payment.payment_name}}</span>          
	 		    </div> 
	 		    <div class="flexcol" v-if="payment.as_default==1"> 		     		     		      
	 		       <span class="ml-1 text-success">✅</span>          
	 		    </div> 		    		    		    
	 		 </a> 
             </template>            

			</template>
			</el-skeleton>
			 
                   
             <!--RENDER PAYMENT COMPONENTS-->       
             <?php CComponentsManager::renderComponents($payments,$payments_credentials,$this)?>                          
          	 
          </DIV> <!-- vue-payment-list-->
          </div> <!--card-body-->                               
          <!--END PAYMENT METHOD-->
           		 
          
        </div> <!--card-->
     </div> <!--col-->
     
     
     <!--RIGHT SIDE PANEL-->
     <div class="col-lg-4 col-md-12 mb-4 mb-lg-3  p-0 p-lg-2">
     
      <!--vue-cart-->       
      <div id="vue-cart" class="sticky-cart" v-cloak >
		 <div class="card">     		  
		   <div class="card-body pb-3"   v-if="cart_items.length>0" >      
		     <div class="items d-flex justify-content-between">
		        <div>		       		            
					 <el-image
						style="width: 50px; height: 50px"
						class="rounded-pill"
						:src="cart_merchant.logo"
						fit="cover"
						lazy
					></el-image>
		        </div> <!--col-->
		        <div class=" flex-fill pl-2">
		          		          
		          <a :href="cart_merchant.restaurant_slug" class="m-0 p-0">
                  <h5 class="m-0 chevron d-inline position-relative">{{ cart_merchant.restaurant_name }}</h5>
                  </a>  
                  
                  <template v-for="(cuisine, index) in cart_merchant.cuisine"  >
                  <div>
		          <!-- <span v-if="index <= 0" class="badge mr-1" 
	             :style="'background:'+cuisine.bgcolor+';font-color:'+cuisine.fncolor" >
		            {{ cuisine.cuisine_name }}
		          </span> -->
		          </div>
		          </template>
                  
		          <p class="m-0">{{ cart_merchant.merchant_address }}</p>
		        </div> <!--col-->
		     </div> <!--items-->                
		   </div> <!--card body-->
		   		   
		   <div class="divider p-0"></div>
		   
		   <div class="card-body">		     
		     <?php $this->renderPartial("//store/cart",array(
		      'checkout'=>true
		     ))?>	      
		   </div> <!--card body-->
		   
		 </div> <!--card-->       
		 </div> <!--sticky-sidebar-->
     <!--end vue-cart-->
     
     </div> <!--col-->
     <!--END RIGHT SIDE PANEL-->
     
   </div> <!--row-->
</div><!-- container-->

</div> <!--container-fluid--> 

<?php $this->renderPartial("//components/loading-box")?>
<?php $this->renderPartial("//components/vue-bootbox")?>