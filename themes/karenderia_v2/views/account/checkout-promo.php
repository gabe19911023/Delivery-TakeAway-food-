<!--promoModal-->
<div class="modal" ref="promo_modal" id="promoModal" tabindex="-1" role="dialog" aria-labelledby="promoModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content 
    <?php echo Yii::app()->params['isMobile']==TRUE?"modal-mobile":"" ?>
    ">      
      <div class="modal-body">
      
      <a href="javascript:;" @click="close" 
	   class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
      
      <h4 class="m-0 mb-3 mt-3"><?php echo t("Promotions")?></h4>
      
      <a href="javascript:;" class="btn btn-light mb-3" @click="showPromoCode">
       <?php echo t("Add promo")?>
      </a>

      <div  v-cloak v-if="error" class="alert alert-warning m-0" role="alert">
	    <p v-cloak class="m-0">{{error}}</p>	    
	   </div>       
      
      <ul class="list-unstyled list-selection m-0 p-0">
      
        <li class="d-flex align-items-center" v-cloak v-for="promo in data" >
         <div class="flexcol  mr-3">
         
            <div class="custom-control custom-radio">
		      <input type="radio" v-model="promo_id" class="custom-control-input"
              :id="promo.promo_id" :value="[promo.promo_type, promo.promo_id]"
		      >
		      <label class="custom-control-label font14 bold" :for="promo.promo_id">
		         {{ promo.title }}
		      </label>
		      <p class="m-0 text-grey" v-if="promo.sub_title" >{{ promo.sub_title }}</p>
		      <p class="m-0 text-grey" v-if="promo.valid_to">{{ promo.valid_to }}</p>
		      
		      <a v-if="promo.promo_id==promo_id[1]" @click="removePromo(promo.promo_type,promo.promo_id)"
		       href="javascript:;" class="btn btn-black mt-1" :class="{ loading: remove_loading }"  >
		        <span class="label"><?php echo t("Remove")?></span>
		        <div class="m-auto circle-loader" data-loader="circle-side"></div>
		      </a>		      
		    </div>   	           
         
         </div> <!--flexcol-->         
       </li>       
       
      </ul>  

      </div> <!--modal body-->
      
      <div class="modal-footer justify-content-start">
       <button class="btn btn-green w-100" @click="save" :class="{ loading: loading }" :disabled="saved_disabled" >
          <span class="label"><?php echo t("Save")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
      </button>
      
      </div> <!--footer-->
    </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->              

