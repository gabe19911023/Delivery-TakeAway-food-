<script type="text/x-template" id="xtemplate_menu">

<div ref="menu_modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static" >
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
      
        
        <h5 class="modal-title" id="exampleModalLabel"><template v-if="!replace_item">
           <?php echo t("Menu")?>
        </template>
        <template v-else>
           <?php echo t("Replace Item")?> {{replace_item.item_name}}
        </template>
        </h5>
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
	    <div>
	      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
	    </div>
	  </div>
	  
      <div class="modal-body grey-bg">

      
      <h5><?php echo t("Category")?></h5>
            
      <div ref="carousel" class="owl-carousel owl-theme d-flex menu-categories mb-3">
      
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
                             
       <p class="m-0 mt-1 text-truncate">{{category.category_name}}</p>
       </a>
      
      </div>
                   
      
      <h5><?php echo t("Menu")?></h5>
      <div class="row align-items-center ">
        <div class="col-8 mb-2">
           <div class="form-group has-search m-0" :class="{loading : awaitingSearch}">
	        <span class="fa fa-search form-control-feedback"></span>	  
	        <div class="circle-loader" data-loader="circle-side"></div>      
	        <input v-model="q" type="text" class="form-control" placeholder="Search">
	       </div>
        </div>
        
        <div class="col-4 text-right font12 ">{{total_results}}</div>
      </div>
      
      <div class="row menu-item-list pl-2 pr-2">
      
        <div v-for="(items,item_id) in item_list" 
        @click="itemShow(items)"
        class="col-md-3 mb-3 pl-1 pr-1">
           
           <div class="row items no-gutters align-items-center rounded w-100">
             <div class="col-5 position-relative">
                <img class="rounded lozad" 
                :data-background-image="image_placeholder"
                :data-src="items.url_image" 
                :src="items.url_image" 
                >
             </div>
             <div class="col-7 text-center p-1">
               <p class="m-0 text-truncate"><b>{{items.item_name}}</b></p>
                                             
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
            
      </div> <!-- body -->    
      
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
      <!-- footer -->
        
    </div> <!-- content -->      
  </div> <!-- dialog -->      
</div>  <!-- modal -->                
       
</script>