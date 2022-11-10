<template v-if="cart_loading==false">
<template v-if="cart_items.length>0">    
    <h5><?php echo t("Cart")?></h5>  
        <div class="d-flex justify-content-between align-items-center mb-1">
        <div><h6 class="m-0"><?php echo t("Summary")?></h6></div>
        <div>
            <a href="javascript:;" @click="clear(cart_uuid)" class="javascript:;">
            <p class="m-0"><u><?php echo t("Clear")?></u></p>
            </a>
        </div>
    </div>
</template>

<template v-else>
<h5><?php echo t("Cart")?></h5> 
<div class="cart-empty text-center" >
    <div class="mt-5">
    <div class="no-results m-auto"></div>
    <h6 class="m-0 mt-3"><?php echo t("You don't have any orders here!")?></h6>
    <p><?php echo t("let's change that!")?></p>
    </div>
</div>
</template>

</template>


<!--section-cart-->  
<DIV class="section-cart">

<div v-cloak class="items" v-for="(items, index) in cart_items" >

<div class="line-items row mb-1">
    <div class="col-3 ">	        
       <div class="cart-image-preview">        
       <el-image
        style="width: 50px; height: 50px"
        :src="items.url_image"
        fit="cover"
        lazy
       >
       </el-image>
       </div>
    </div> <!--col-->
    
    <div class="col-6 p-0 d-flex justify-content-start flex-column">
    
    <p class="mb-1">
        {{ items.item_name }}
        <template v-if=" items.price.size_name!='' "> 
        ({{items.price.size_name}})
        </template>
    </p>	     
            
        <template v-if="items.price.discount>0">         
        <p class="m-0 font11"><del>{{items.price.pretty_price}}</del> {{items.price.pretty_price_after_discount}}</p>
        </template>
        <template v-else>
        <p class="m-0 font11">{{items.price.pretty_price}}</p>
        </template>
            
        <!--quantity-->
        <div class="quantity d-flex justify-content-between">
        
        <div>
            <a href="javascript:;" @click="updateCartQty(0,items.qty,items.cart_row,cart_uuid)"  class="rounded-pill qty-btn" data-id="less">
            <i class="zmdi zmdi-minus"></i>
            </a>
        </div>
        
        <div class="qty">{{  items.qty }}</div>
        
        <div>
        <a href="javascript:;" @click="updateCartQty(1,items.qty,items.cart_row,cart_uuid)" class="rounded-pill qty-btn" data-id="plus">
            <i class="zmdi zmdi-plus"></i>
            </a>
        </div>
        
        </div>
        <!--quantity-->		 
    
    <p class="mb-0" v-if=" items.special_instructions!='' ">{{ items.special_instructions }}</p>	               
        
    <template v-if=" items.attributes!='' "> 
        <template v-for="(attributes, attributes_key) in items.attributes">                    
        <p class="mb-0">            
        <template v-for="(attributes_data, attributes_index) in attributes">            
            {{attributes_data}}<template v-if=" attributes_index<(attributes.length-1) ">, </template>
        </template>
        </p>
        </template>
    </template>
    
            
    </div> <!--col-->
    
    <div class="col-3  quantity d-flex justify-content-start flex-column  text-right ">
    <a href="javascript:;" @click="remove(items.cart_row,cart_uuid)" class="rounded-pill item-remove ml-auto mb-1"><i class="zmdi zmdi-close"></i></a>
    <template v-if="items.price.discount<=0 ">
        <p class="mb-0">{{ items.price.pretty_total }}</p>
    </template>
    <template v-else>
        <p class="mb-0">{{ items.price.pretty_total_after_discount }}</p>
    </template>
    </div> <!--col-->
    
</div><!-- line-items-->

<!--addon-items-->
<div class="addon-items row mb-1"  v-for="(addons, index_addon) in items.addons" >
    <div class="col-3 "><!--empty--></div> <!--col-->		     
    <div class="col-9  pl-0 d-flex justify-content-start flex-column ">
        <p class="m-0 bold">{{ addons.subcategory_name }}</p>		  
            
        <template v-cloak v-for="addon_items in addons.addon_items">
        <div class="d-flex justify-content-between mb-1">
        <div class="flexrow"><p class="m-0">{{addon_items.qty}} x {{addon_items.pretty_price}} {{addon_items.sub_item_name}}</p></div>
        <div class="flexrow"><p class="m-0">{{addon_items.pretty_addons_total}}</p></div>
        </div>	<!--flex-->                  
    </template>
        
    </div> <!--col-->		      		      
</div>
<!-- addon-items-->

</div> <!--items-->

<div class="cart-summary mt-2 mb-3 ">

    <template v-for="summary in cart_summary">      
    <div class="d-flex justify-content-between align-items-center mb-1">
        <template v-if=" summary.type=='total' ">
            <div><h6 class="m-0">{{ summary.name }}</h6></div>
            <div><h6 class="m-0">{{ summary.value }}</h6></div>
        </template>
        <template v-else>
            <div>{{ summary.name }}</div>
            <div>{{ summary.value }}</div>
        </template>
    </div> <!--flex-->
    </template>
        
</div> <!--cart-summary -->

<?php if($checkout):?>
    <template v-if="cart_items.length>0">         
    <button @click="placeOrder" class="btn btn-green w-100 pointer" :disabled="hasError" 
    :class="{ disabled: hasPayment, 'loading': is_submit }"  >     	  
    <span class="label"><?php echo t("Place Order")?></span>
    <div class="m-auto circle-loader" data-loader="circle-side"></div>	  
    </button>
    </template>
<?php else :?>    
<template v-if="cart_items.length>0">          
    <a class="btn btn-green w-100 pointer d-flex justify-content-between" :disabled="hasError"
    :href="hasError?'javascript:;':'<?php echo isset($checkout_link)?$checkout_link:'';?>'"
    >
    <div class="flex-col"><?php echo t("Checkout")?></div>
    <div class="flex-col">{{cart_subtotal.value}}</div>
    <div class="fixed-loader">
        <div class="m-auto circle-loader" data-loader="circle-side"></div> 
    </div>
    </a>
</template>   
<?php endif;?>

<div v-cloak class="alert alert-warning m-0 mt-2" v-if="error.length>0">
    <p class="m-0" v-for="error_msg in error">
    {{ error_msg }}
    </p>
</div>        

<div v-cloak class="alert alert-warning m-0 mt-2" v-if="hasPayment">
<p class="m-0">{{error_payment}}</p>
</div>  

<div v-cloak class="alert alert-warning m-0 mt-2" v-if="error_placeorder.length>0">
    <p class="m-0" v-for="error_msg in error_placeorder">
    {{ error_msg }}
    </p>
</div>   

</DIV> <!--section-cart-->