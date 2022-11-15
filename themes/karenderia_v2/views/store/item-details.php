<!--itemModal-->
<div class="modal" ref="modal_item_details"
id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered
  <?php echo $is_mobile?"modal-full":"modal-dialog-scrollable" ?>
  " role="document">
    <div class="modal-content">      
        
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    
     <div class="modal-body" id="item-details">       
     
     <el-skeleton animated :loading="item_loading">
     <template #template>
          <div><el-skeleton-item variant="image" style="width: 100%; height: 170px" /></div>              
          <div class="mt-2 mb-2">
          <el-skeleton :rows="5" variant="text"  />
          </div>

          <div class="border-top row pt-3">
              <div class="col-4">
                <div><el-skeleton-item variant="text" /></div>
                <div><el-skeleton-item variant="text" /></div>
              </div>
              <div class="col">                      
                  <el-skeleton-item variant="button" style="width: 100%" />
              </div>
          </div>
     </template>
     <template #default>
     </template>
     </el-skeleton>     
 
     
     <div v-if="!item_loading">
          
      <template v-if="items!=''">
      
       <div class="item_s" :data-item_token="items.item_token" >
        <el-image
          style="width: 100%; height: 170px"
          :src="items.url_image"
          :fit="cover"          
        ></el-image>
       </div> <!--item_s-->
      
       <h4 class="m-0 mt-2 mb-2">{{items.item_name}}</h4>
       <template v-if="items.item_description!=''">
       <p v-html="items.item_description"></p>
       </template>
                                   
        <template v-if="items.price!=''"  >     
                     
          <div class="mb-4 btn-group btn-group-toggle input-group-small choose-sizex flex-wrap" data-toggle="buttons" >          
           <label v-for="(price, index) in items.price"
	       class="btn" @click="setItemSize" :class="{ active: price.item_size_id==size_id }"  >       
                               
             <input name="size_uuid" id="size_uuid" class="size_uuid" type="radio" 
	          :value="price.item_size_id" v-model="size_id"      
	          >
              <template v-if="price.discount <=0">
                {{price.size_name}} {{price.pretty_price}}
              </template><!-- v-if-->
              <template v-else>
                {{price.size_name}} <del>{{price.pretty_price}}</del> {{price.pretty_price_after_discount}}
              </template> <!--v-else-->   
                                                     
           </label>
          </div> <!--btn-group-->
          
        </template> <!--v-if-->
        
      </template> <!--v-if-->
      
           
      <!--META-->
     <template v-if="meta!=''">    
      <DIV class="addon-rows" v-for="(item_meta, meta_key) in meta" >       
     <template v-if="meta_key!='dish' && item_meta.length>0">   
	   <div class="d-flex align-items-center heads">
	      <div class="flexcol flex-grow-1">
	         <template v-if="meta_key=='cooking_ref'" ><?php echo t("Cooking Reference")?></template>
	         <template v-else-if="meta_key=='ingredients'" ><?php echo t("Ingredients")?></template>
	      </div>	
	      <div class="flexcol"><?php echo t("Optional")?></div>	
	   </div>	  
	 		   
	 <ul class="list-unstyled list-selection list-addon no-hover m-0 p-0">
	 
      <li  v-for="(meta_details, index2) in item_meta" class="d-flex align-items-center" >            
       
      <div v-if="meta_key=='cooking_ref'" class="custom-control custom-radio flex-grow-1">            
		  <input type="radio" class="custom-control-input"
	      :name="'meta_' + meta_key" :value="meta_details.meta_id" 
	      :id="'meta_' + meta_key + meta_details.meta_id"
	      v-model="meta_details.checked"              
		  >	  
		  <label class="custom-control-label font14 bold" :for="'meta_' + meta_key + meta_details.meta_id" >
		   <h6 class="m-0">{{ meta_details.meta_name }}</h6>
		  </label>
	  </div>    
	  
	  <div v-if="meta_key=='ingredients'" class="custom-control custom-checkbox flex-grow-1">            
		  <input type="checkbox" class="custom-control-input"
	      :name="'meta_' + meta_details.meta_id" :value="meta_details.meta_id" 
	      :id="'meta_' + meta_key + meta_details.meta_id"
	      v-model="meta_details.checked"              
		  >	  
		  <label class="custom-control-label font14 bold" :for="'meta_' + meta_key + meta_details.meta_id" >
		   <h6 class="m-0">{{ meta_details.meta_name }}</h6>
		  </label>
	  </div>    
	  
      </li>
     </ul>
	 	 
     </DIV>  <!--addon-rows--> 
     </template> <!--v-if-->
     </template> <!--v-if-->
     <!--END META-->
      
      <!--ADDONS-->
      <template v-if="item_addons!=''">       
        <DIV class="addon-rows" v-for="(addons, index) in item_addons" >
          <div class="d-flex align-items-center heads">
              <div class="flexcol flex-grow-1">
                <h5 class="m-0">{{ addons.subcategory_name }}</h5>
                <p class="text-grey m-0 mb-1">
                
                  <template v-if=" addons.multi_option=='one' " >
                  <?php echo t("Select 1")?>
                  </template>
                  
                  <template v-else-if="addons.multi_option=='multiple'">
                  <?php echo t("Choose up to")?> {{addons.sub_items.length}}
                  </template>
                  
                  <template v-else-if="addons.multi_option=='two_flavor'">
                  <?php echo t("Select flavor")?> {{addons.multi_option_value}}
                  </template>
                  
                  <template v-else-if="addons.multi_option=='custom'">
                  <?php echo t("Choose up to")?> {{addons.multi_option_value}}
                  </template>
                
                </p>
              </div>
              <div class="flexcol">
                <h6 class="m-0">
                   <template v-if=" addons.require_addon==1 " >
                     <span class="addon-required rounded"><?php echo t("Required")?></span>
                   </template>
                   <template v-else>
                   <?php echo t("Optional")?>
                   </template>
                </h6>
              </div>
          </div>
          
          
          <ul class="list-unstyled list-selection list-addon no-hover m-0 p-0"
	        :data-subcat_id="addons.subcat_id" 
	        :data-multi_option="addons.multi_option"
	        :data-multi_option_value="addons.multi_option_value"
	        :data-require_addon="addons.require_addon"
	        :data-pre_selected="addons.pre_selected"
          >                                 
          <li v-for="(addon_items, index) in addons.sub_items" class="d-flex align-items-center" >
                       
             <template v-if=" addons.multi_option=='one' " >                                           
                <div class="custom-control custom-radio flex-grow-1">                  
                  <input type="radio" :id="'sub_item_id' + addon_items.sub_item_id" :name="'sub_item_id' + addons.subcat_id" 
                   :value="addon_items.sub_item_id" 
                   v-model="addons.sub_items_checked"                      
                   class="custom-control-input addon-items">
                  
                  <label class="custom-control-label font14 bold" :for="'sub_item_id' + addon_items.sub_item_id">
                    <h6 class="m-0">{{ addon_items.sub_item_name }}</h6>
                  </label>
                </div>                
                <p class="m-0">{{ addon_items.pretty_price }}</p>                
             </template>
              
             <template v-else-if="addons.multi_option=='multiple'">           
             <div class="position-relative quantity-wrapper ">
             
                <template v-if=" addon_items.checked==true " >  
			    <div class="quantity-parent" >
					 <div class="quantity d-flex justify-content-between m-auto">
						<div><a href="javascript:;" @click="addon_items.qty>1?addon_items.qty--:addon_items.checked=false" class="rounded-pill qty-btn multiple_qty" data-id="less"><i class="zmdi zmdi-minus"></i></a></div>
						<div class="qty" :data-id="addon_items.sub_item_id" >{{ addon_items.qty }}</div>
						<div><a href="javascript:;" @click="addon_items.qty++" class="rounded-pill qty-btn multiple_qty" data-id="plus"><i class="zmdi zmdi-plus"></i></a></div>
					 </div>
				 </div> 
				 </template>
					 
				 <template v-else=" addon_items.checked==false " > 
				 <a href="javascript:;" class="btn quantity-add-cart multiple_qty" 
                   @click=" addon_items.checked = true "  >
				   <i class="zmdi zmdi-plus"></i>
				 </a>
				 </template>
			 </div> 
			 
			 <div class="flexcol ml-3">
			 <h6 class="m-0">{{ addon_items.sub_item_name }}</h6>
			 <p class="m-0 text-grey">+{{ addon_items.pretty_price }}</p>
			 </div>			              
             </template>
             
            <template v-else-if="addons.multi_option=='custom'">
            
            <div class="custom-control custom-checkbox flex-grow-1">
            
			  <input type="checkbox" :id="'sub_item_id' + addon_items.sub_item_id" 
              :name="'sub_item_id' + addons.subcat_id" :value="addon_items.sub_item_id" 
              v-model="addon_items.checked"              
              :class="'addon-items' + addons.subcat_id"
              :data-id="addons.subcat_id"
              class="custom-control-input addon-items"
              :disabled="addon_items.disabled"
              >
			  
			  <label class="custom-control-label font14 bold" :for="'sub_item_id' + addon_items.sub_item_id">
			   <h6 class="m-0">{{ addon_items.sub_item_name }}</h6>
			  </label>
			</div>         
			<p class="m-0">+{{ addon_items.pretty_price }}</p>
            </template>
             
           </li>    
          </ul>
          
        </DIV> <!--addon-rows-->
      </template> <!--v-if-->
      <!--END ADDONS-->
      
      <h5 class="m-0 mt-2 mb-2"><?php echo t("Special Instructions")?></h5>      
      <div class="form-label-group">    
        <textarea 
        :value="special_instructions"
        @input="special_instructions=$event.target.value?$event.target.value:''"
        class="form-control form-control-text font13" 
              placeholder="<?php echo t("Add a note for this item (no onions, meat well cooked, etc.)")?>">
        </textarea>       
      </div>   
  <!--    
      <h5 class="m-0 mt-2 mb-2"><?php echo t("If sold out")?></h5>            
      <div class="form-label-group m-0 p-0 mb-3">  
	   <select v-model="if_sold_out" class="form-control custom-select">		 
	    <option v-for="(sold_label, sold_key) in sold_out_options" :value="sold_key" >{{sold_label}}</option>
	   </select>
	  </div>
    -->      
    
     </div>  
     <!-- v-if -->

      </div> <!--modal body-->
      
      <div class="item-modal-footer modal-footer justify-content-start">
      
       <div class="w-25">
       
        <!--quantity-->
         <div class="quantity d-flex justify-content-between">
            <div><a @click="item_qty>1?item_qty--:1" href="javascript:;" class="rounded-pill qty-btn" data-id="less"><i class="zmdi zmdi-minus"></i></a></div>
            <div class="qty">{{ item_qty }}</div>
            <div><a @click="item_qty++" href="javascript:;" class="rounded-pill qty-btn" data-id="plus"><i class="zmdi zmdi-plus"></i></a></div>
         </div>
         <!--quantity-->
       
       </div> <!--w-45-->
       
       <div class="w-75">
        <button class="btn btn-green w-100 add_to_cart" :class="{ loading: add_to_cart }" :disabled="disabled_cart" @click="CheckaddCartItems" > <!--addCartItems testConfirm-->
          <span class="label"><?php echo t("Add to cart")?> - <span class="item-summary"><money-format :amount="item_total" ></money-format></span></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
        </button>
       </div>  <!--w-75-->
      
      </div> <!--footer-->
    </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->