<div class="d-flex justify-content-between">

<nav class="navbar navbar-light justify-content-between">
<a class="navbar-brand">
<h5><?php echo CHtml::encode($this->pageTitle)?></h5>
</a>
</nav>

<?php if(isset($link)):?>
<?php if(!empty($link)):?>
<div class="d-flex flex-row justify-content-end">  
  <div class="p-2">    
  <a type="button" class="btn btn-black btn-circle" 
  href="<?php echo $link?>">
    <i class="zmdi zmdi-plus"></i>
  </a>  
  </div>  
  <div class="p-2"><h5><?php echo t("Add new")?></h5></div>  
</div> <!--flex-->      
<?php endif;?>
<?php endif;?>

</div>
<!--d-flex justify-content-between-->
  

<!--SEARCH -->
<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_search_app",
  'class'=>"form-inline justify-content-end frm_search mb-3",
  'onsubmit'=>"return false;"
)); 
?> 

<div class="input-group rounded mb-2">
<?php echo CHtml::dropDownList('filter_merchant','',
  array(),          
  array(
  'class'=>'form-control custom-select form-control-select select_two_ajax mr-sm-2',
  'action'=>'search_merchant',
  'data-placeholder'=>t("Filter by merchant")
))?>
</div> <!--input-group-->

<div class="input-group">

<input name="date_filter" class="form-control mr-sm-2 date_range_picker" 
type="text" placeholder="<?php echo t("Filter by date")?>"
data-separator="<?php echo t("to")?>"
readonly  >    

<button type="submit" class="submit input-group-text border-0 ml-2">
<i class="zmdi zmdi-search"></i>
</button>

<button type="button" class="input-group-text border-0 ml-2 btn-black search_close_app" >
<i class="zmdi zmdi-close"></i>
</button>
    
</div> <!--input-group-->

<?php echo CHtml::endForm(); ?>
<!--END SEARCH -->

<div class="page-no-results text-center mt-5">
 <img src="<?php echo CommonUtility::getPhoto('','no-results.png')?>">
 <p class="dim mt-2"><?php echo t(HELPER_NO_RESULTS)?></p>
</div>

<DIV id="lazy-start" ></DIV>

<div class="lazy-load-status">
 <div>Loading...</div>
</div>

<?php 
$this->renderPartial("/admin/modal_delete");
?>