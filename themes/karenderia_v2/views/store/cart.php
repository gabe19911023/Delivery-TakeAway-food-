
<div id="vue-cart" class="sticky-cart" >
 
  <el-skeleton :count="10" :loading="cart_loading" animated>
    <template #template>
      <div class="row m-0">  
        <div class="col-lg-3 col-md-3 p-0 mb-2">
              <el-skeleton-item variant="image" style="width: 50px; height: 50px" />
        </div> <!-- col -->
         <div class=" col-lg-9 col-md-9 p-0">					  
             <div class="row m-0 p-0">
                  <div class="col-lg-12">							
                        <el-skeleton :rows="2" />
                    </div>							
              </div>
              <!-- row -->
          </div> <!-- col --> 					  
      </div> <!--  row -->
      </template>

  <template #default>
  <?php $this->renderPartial("//store/cart-data",array(
    'checkout'=>$checkout,
    'checkout_link'=>isset($checkout_link)?$checkout_link:''
  ))?>
  </template>

  </el-skeleton>
     
</div> <!--sticky-->