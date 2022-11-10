<!--TOP SEARCH -->
<div class="container-fluid mt-0 change-address-wrap">

<div class="container">
   <div class="d-flex align-self-center row">
      <div class="mr-auto">
              
        <h5 class="m-0"><?php echo $local_info->address1?></h5>
        <p class="m-0 text-grey"><?php echo $local_info->address2?></p>
        <a href="javascript:;" data-toggle="modal" data-target="#changeAddress"  >
        <?php echo t("Change Address")?>
        </a>
       
      </div> <!-- div-->
      
      <div class="w-30 ">
      
      <div class="typeahead__container position-relative clear-custom button-left auto-suggestion" >
      <div class="typeahead__field">   
      <div class="typeahead__query"> 
      
       <div class="form-label-group w-100 m-0" id="search-form">    
	     <input class="form-control form-control-text form-control-text-white auto_typehead " 
          placeholder="" id="search" type="text"  maxlength="40" autocomplete="off"
          
          data-settings="minLength=2&limit=Infinity&dynamic=true&maxLength=Infinity&maxItem=Infinity&filter=false&hint=true&accent=true"
	      data-api="getSearchSuggestion"
	      data-template="<div class='d-flex'> 
<div class='  mr-2'>
<img class='img-40 rounded-pill' src='{{url_logo}}' />
</div> 
<div class=' align-self-center w-100'>
<h6 class='m-0 light'>{{restaurant_name}}</h6>
<p class='m-0 text-grey text-break'>{{cuisine2}}</p>
</div> 
</div>"
	      data-emptytemplate="Hmm... can't find what your looking for. Try again?"
	      data-display="restaurant_name,url_logo,cuisine2"
          >   
	     <label for="search" class="required"><?php echo t("Search")?></label>   
	     
	     <div class="d-flex align-items-center icon-left">
	       <button class="btn shadow-none" type="submit">
	        <img class="img-20" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/magnifiying-glass.svg"?>" />
	       </button>
	     </div>	     
       </div> <!--form group-->
       
       </div></div></div>  
      <!--typhead div-->
      
      </div> <!--div-->
   </div> <!--flex-->
</div><!-- container-->

</div> <!--container-fluid-->
<!--TOP SEARCH -->

<!--CHANGE ADDRESS-->
<div class="modal change-address-modal" id="changeAddress" tabindex="-1" role="dialog" aria-labelledby="changeAddress" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centeredx" role="document" >
    <div class="modal-content">      
      <div class="modal-body">
      
        <h4 class="m-0 mb-3"><?php echo t("Search for new address")?></h4>
      

<div class="position-relative">
<div class="typeahead__container position-relative clear-custom">
<div class="typeahead__field">
<div class="typeahead__query"> 
  
<!--search-form-->
<?php   
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'frm-search',
		'enableAjaxValidation' => false,		
	)
);
?>
<div id="search-form" class="form-label-group m-0">    

<input name="search" class="form-control form-control-text auto_typehead" 
placeholder="" id="search" type="text"  maxlength="40" autocomplete="off"
data-settings="minLength=2&limit=Infinity&dynamic=true&maxLength=Infinity&maxItem=Infinity&filter=false&hint=true&accent=true"
data-api="getlocation_autocomplete"
data-template="<h6 class='m-0'>{{addressLine1}}</h6><p class='m-0 text-grey'>{{addressLine2}}</p>"
data-emptytemplate="Hmm... can't find this address. Try again?"
data-display="addressLine1,addressLine2"
 >   
<!--<label for="search" class="required"><?php echo t("Enter your delivery location")?></label>  --> 

<a href="javascript:;" class="icon-left d-flex align-items-center">       
   <div class="img-20 m-auto pin_placeholder get_current_location"></div>
   <div class="small_preloader display-none m-auto" data-loader="circle"></div>
</a>   
       
</div>
<?php $this->endWidget(); ?>
<!--search-form-->
  
</div></div></div>  
<!--typhead div-->
</div>

      

       </div> <!--modal body-->            
    </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->      		   
<!--CHANGE ADDRESS-->