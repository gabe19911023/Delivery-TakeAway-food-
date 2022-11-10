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
echo CHtml::hiddenField('item_id',$model->item_id); 
?> 

<div class="input-group rounded">
  <input name="search" type="search" class="form-control rounded search w-25" placeholder="<?php echo t("Search")?>" required  />
  <button type="submit" class="submit input-group-text border-0 ml-2">
    <i class="zmdi zmdi-search"></i>
  </button>
   <button type="button" class="input-group-text border-0 ml-2 btn-black search_close_app" >
    <i class="zmdi zmdi-close"></i>
    </button>
</div>

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