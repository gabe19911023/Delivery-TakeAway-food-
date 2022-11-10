<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'frm-filter',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array(
	  'onsubmit'=>"return false;"
	)		
));
		  
echo CHtml::hiddenField("local_id", isset($local_id)?$local_id:'' );
echo CHtml::hiddenField("target", isset($target)?$target:'' );
echo CHtml::hiddenField("total", isset($total)?$total:'' );
echo CHtml::hiddenField("page", isset($page)?$page:'' );
?>
<div class="accordion section-filter" id="sectionFilter">
   
   
   <div class="filter-row border-bottom pb-2">
     <h5>
     <a href="#filterServices" class="d-block" data-toggle="collapse" aria-expanded="true" aria-controls="filterServices"  >
     Services
     </a>
     </h5>   
   
     <div id="filterServices" class="collapse show" aria-labelledby="headingOne" >
     
       <div class="row m-0">
                      
     <?php if(isset($services)):?>
     <?php if(is_array($services) && count($services)>=1):?>
        <?php foreach ($services as $service_id=>$service_name):?>
        
        <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
        <div class="custom-control custom-checkbox">
          <?php echo $form->checkBox($model,"services[$service_id]",array(
          'class'=>"custom-control-input services",
          'id'=>"services[$service_id]",
          'value'=>$service_id,
          'checked'=>in_array($service_id,(array)$services_selected)?true:false
          ))?> 
          <label class="custom-control-label" for="services[<?php echo $service_id?>]">
           <?php echo $service_name;?>
          </label>
        </div>   		      
      </div> 
        
        <?php endforeach;?>
     <?php endif;?>
     <?php endif;?>
     
    
     </div> <!--row-->
     
     </div> <!-- filterServices-->
   
   </div> <!--filter-row-->
   
   
    <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterSort" class="d-block" data-toggle="collapse" aria-expanded="true" aria-controls="filterSort"  >
     <?php echo t("Sort")?>
     </a>
     </h5>   
   
     <div id="filterSort" class="collapse show" aria-labelledby="headingOne" >
     
      <?php foreach ($sort as $sort_id=>$sort_value):?>
      <div class="row m-0 ml-2 mb-2">
		<div class="custom-control custom-radio">
	     <?php echo $form->radioButton($model,"sort",array(
          'class'=>"custom-control-input sort",
          'id'=>"sort[$sort_id]",
          'value'=>$sort_id
          ))?>   
          <label class="custom-control-label" for="sort[<?php echo $sort_id?>]">
           <?php echo $sort_value;?>
          </label>
		</div>   		      
	  </div><!--row-->  
      <?php endforeach;?>
     
     </div> <!--filterSort-->   
   </div> <!--filter-row-->
   
   
   <!--PRICE-->
   <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterPrice" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterPrice"  >
     <?php echo t("Price range")?>
     </a>
   </h5>   
   
    <div id="filterPrice" class="collapse" aria-labelledby="headingOne" >
   
    
    <div class="btn-group btn-group-toggle input-group-small mt-2 mb-2" data-toggle="buttons">
       <label class="btn active ">
         <input name="price_range" class="price_range" value="$" type="radio"> 
         $
       </label>
       <label class="btn">
         <input name="price_range" class="price_range" value="$$" type="radio"> 
         $$
       </label>
       <label class="btn">
         <input name="price_range" class="price_range" value="$$$" type="radio"> 
         $$$
       </label>
       <label class="btn">
         <input name="price_range" class="price_range" value="$$$$" type="radio"> 
         $$$$
       </label>
     </div>   
   
     </div> <!-- filterCuisine-->  
   </div> <!--filter-row-->
   
   <!--END PRICE-->
   
   
    <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterCuisine" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterCuisine"  >
     <?php echo t("Cuisines")?>
     </a>
     </h5>   
   
     <div id="filterCuisine" class="collapse" aria-labelledby="headingOne" >
       
     
        <div class="row m-0">
     
         <?php $x=0;?>
	     <?php if(isset($cuisine)):?>
	     <?php if(is_array($cuisine) && count($cuisine)>=1):?>
	        <?php foreach ($cuisine as $cuisine_id=>$cuisine_name):?>
	        
	        <?php if($x<=5):?>
	        <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
	         <div class="custom-control custom-checkbox">
	          <?php echo $form->checkBox($model,"cuisine[$cuisine_id]",array(
	          'class'=>"custom-control-input cuisine",
	          'id'=>"cuisine[$cuisine_id]",
	          'value'=>$cuisine_id
	          ))?> 
	          <label class="custom-control-label" for="cuisine[<?php echo $cuisine_id?>]">
	           <?php echo $cuisine_name;?>
	          </label>
	         </div>   		      
	        </div> <!--col-->	         	       
	        <?php endif;?>
	      
	        <?php $x++;?>
	        <?php endforeach;?>
	     <?php endif;?>
	     <?php endif;?>	     	      		 	
	    </div><!-- row-->
	    
	    <?php $x=0;?>
	    <?php if(isset($cuisine)):?>
	    <?php if(is_array($cuisine) && count($cuisine)>=1):?>
	    <div class="collapse" id="moreCuisine">
	      <div class="row m-0">
	      
	         <?php foreach ($cuisine as $cuisine_id=>$cuisine_name):?>
	         <?php if($x>5):?>
	         <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 
	         
	         <div class="custom-control custom-checkbox">
	          <?php echo $form->checkBox($model,"cuisine[$cuisine_id]",array(
	          'class'=>"custom-control-input cuisine",
	          'id'=>"cuisine[$cuisine_id]",
	          'value'=>$cuisine_id
	          ))?> 
	          <label class="custom-control-label" for="cuisine[<?php echo $cuisine_id?>]">
	           <?php echo $cuisine_name;?>
	          </label>
	         </div>   		   
	         
	         </div> <!--col-->
	         <?php endif;?>
	         <?php $x++;?>
	         <?php endforeach;?>
	         
	      </div> <!--row-->
	    </div> <!--collapse-->
	    
	    
	    <div class="row ml-3 mt-1 mt-0 mb-2">
		 <a class="btn link more-cuisine" data-toggle="collapse" href="#moreCuisine" role="button" aria-expanded="false" aria-controls="collapseExample">
		  <u><?php echo t("Show more +")?></u>
		 </a>
		</div>
		<?php endif;?>
		<?php endif;?>
		  	    
     
     </div> <!-- filterCuisine-->  
   </div> <!--filter-row-->
   
   
   <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterMinimum" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterMinimum"  >
     <?php echo t("Max Delivery Fee")?>
     </a>
     </h5>   
   
     <div id="filterMinimum" class="collapse" aria-labelledby="headingOne" >       
     
     <div class="form-group">
	    <label for="formControlRange">Delivery Fee <b><span class="min-selected-range"></span></b></label>
	    <input id="min_range_slider" value="10" type="range" class="custom-range" id="formControlRange"  min="1" max="20" >
	  </div>
     
     </div> <!-- filterMinimum-->  
   </div> <!--filter-row-->
   
   <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterRating" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterRating"  >
     <?php echo t("Rating")?>
     </a>
     </h5>   
   
     <DIV class="position-relative">
     <div id="filterRating" class="collapse" aria-labelledby="headingOne" >            
          
     <div class="star-rating"
      data-totalstars="5"
      data-minrating="1"
      data-initialrating="0"
      data-strokecolor="#fedc79"
      data-ratedcolor="#fedc79"
      data-hovercolor="#fedc79"
      data-strokewidth="0"
      data-starsize="25"
      data-readonly="false"
      data-disableafterrate="false"
      data-usefullstars="true"
      >
      <input type="hidden" name="ratings_selected" id="ratings_selected" class="ratings_selected">
      </div>      
      </DIV>
     
     </div> <!-- filterRating-->  
   </div> <!--filter-row-->
   
   <div class="mt-3 mb-3">
   <button type="submit" class="btn btn-green w-100 restaurants-filter">
     <span class="label"><?php echo t("Show restaurants")?></span>
     <div class="m-auto circle-loader" data-loader="circle-side"></div>
   </button></div>
   
</div> <!--section-filter-->
<?php $this->endWidget(); ?>