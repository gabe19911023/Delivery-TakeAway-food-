<script type="text/x-template" id="xtemplate_menu">
<div class="card">

<div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
<div>
  <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
</div>
</div>
	
 <div class="card-body">
 
   <h5><?php echo t("Category")?></h5>
      
    <div id="menu-categories" ref="carousel" class="owl-carousel owl-theme d-flexx menu-categories mb-3 v_menu_categories">
      
       <a class="text-center"
       @click="categoryItem('all')"
       :class="{active:active_category=='all'}"
       class="rounded align-self-center text-center"
       >
       <i class="zmdi zmdi-mall"></i>
       <p class="m-0 mt-1 text-truncate"><?php echo t("All")?></p>
       </a>   
       
        <a v-for="category in category_list" 
       @click="categoryItem(category.cat_id)"
       :class="{active:active_category==category.cat_id}"
       class="rounded align-self-center text-center">
       
       <img class="rounded lozad" 
        :data-background-image="image_placeholder"
        :data-src="category.url_image" 
        class="rounded-pill lozad"
       >       
                             
       <p class="m-0 mt-1 text-truncate2">{{category.category_name}}</p>
       </a>
      
      </div>
      <!-- owl-carousel -->
      
      <h5><?php echo t("Menu")?></h5>
      <div class="row align-items-center ">
        <div class="col-6">
           <div class="form-group has-search" :class="{loading : awaitingSearch}">
	        <span class="fa fa-search form-control-feedback"></span>	  
	        <div class="circle-loader" data-loader="circle-side"></div>      
	        <input v-model="q" type="text" class="form-control" placeholder="<?php echo t("Search")?>">
	       </div>
        </div>
        <div class="col-2"></div>
        <div class="col-4 text-right font12">{{total_results}}</div>
      </div>
      
     
      <div class="row menu-item-list pl-2 pr-2">
      
        <div v-for="(items,item_id) in item_list" 
        @click="itemShow(items)"
        class="col-md-3 mb-3 pl-1 pr-1">
           
           <div class="row items no-gutters align-items-center rounded w-100 v_menu_items">
             <!--<div class="col-5 position-relative">
                <img class="rounded lozad" 
                :data-background-image="image_placeholder"
                :data-src="items.url_image" 
                :src="items.url_image" 
                >
             </div>-->
             <div class="col-12 text-center p-1">
               <p class="m-0 v_text-truncate_2"><b>{{items.item_name}}</b></p>
                                             
               <p class="m-0 text-green text-truncate" v-for="(price, index) in items.price" >
                  <template v-if="price.discount <=0">
                    {{price.size_name}} {{price.pretty_price}}
                  </template><!-- v-if-->
                  <template v-else>
                    {{price.size_name}} <del>{{price.pretty_price}}</del> {{price.pretty_price_after_discount}}
                  </template> <!--v-else-->               
               </p>
               
             </div>
           </div> <!-- items -->
           
        </div> 
        <!-- col -->
                
      </div> 
      <!-- menu-item-list --> 
  
 
 </div> <!-- card body -->
 
  <div class="modal-footer justify-content-end">            
         <nav aria-label="Page navigation" >
           <ul class="pagination">
             <li class="page-item"  :class="{disabled: current_page=='1'}" >
		      <a @click="pagePrev()" class="page-link" href="javascript:;">{{label.previous}}</a>
		     </li>		
		     <li class="page-item" :class="{disabled: page_count==current_page}" >
		       <a @click="pageNext()" class="page-link" href="javascript:;">{{label.next}}</a>
		     </li>		    
           </ul>
         </nav>
      </div>
	  
</div> <!-- card -->
</script>