<script type="text/x-template" id="item_suggestion">

  <p class="bold m-0 mb-1 mt-3">{{title}}</p>	  	  
  <div id="item-suggestion-list" class="owl-carousel" >
  
  <template v-for="val in menu_data">
  
  <a v-for="items in val.items" 
  @click="viewItem({cat_id:val.cat_id,item_uuid:items.item_uuid})"
   class="rounded d-flex justify-content-between align-items-center list-item-rows">
   <div class="flex-col">
     <p class="m-0 bold text-truncate">{{items.item_name}}</p>
     
     <template v-if="items.price!=''"  >     
     <p class="m-0"  >
          <template v-if="items.price[0].discount <=0">
            {{items.price[0].pretty_price}}
          </template><!-- v-if-->
          <template v-else>
            {{items.price[0].pretty_price_after_discount}}
          </template> <!--v-else-->   
     </p>
     </template>
     
   </div>
   <div class="flex-col">
    <img class="img-50" 
        :src="items.url_image">
   </div>
   </a>               
  
  </template> <!-- menu_data -->
          
  </div> 
  <!--item-suggestion-list-->
  
  <!-- NEXT -->
  <div class="d-flex justify-content-end">
    <div class="flex-col mr-2">
      <a @click="SlidePrev" class="d-flex align-items-center">
      <span class="badge btn-grey rounded-pill ml-1 font20">
       <i class="zmdi zmdi-arrow-left"></i>
      </span>
      </a>
    </div>
    
    <div class="flex-col">
      <a @click="SlideNext" class="d-flex align-items-center">
      <span class="badge btn-black rounded-pill font20">
       <i class="zmdi zmdi-arrow-right"></i>
      </span>
      </a>
    </div>       
  </div      
    
  <?php $this->renderPartial("//store/item-details",array(
    'is_mobile'=>Yii::app()->params['isMobile']
  ))?>

</script>