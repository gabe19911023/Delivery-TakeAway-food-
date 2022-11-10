<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>    
    
    <div class="d-flex flex-row justify-content-end">
	  <div class="p-2">  
	  <a type="button" class="btn btn-black btn-circle"
	  href="<?php echo $link?>">
	    <i class="zmdi zmdi-plus"></i>
	  </a>  
	  </div>
	  <div class="p-2 mr-4"><h5><?php echo t("Add new")?></h5></div>
	</div> <!--flex-->      
	
</nav>


<!--SEARCH -->
<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_search",
  'class'=>"form-inline justify-content-end frm_search mb-2",
  'onsubmit'=>"return false;"
)); 
?> 
    <input name="search" class="form-control mr-sm-2 search" type="search" placeholder="<?php echo t("Search")?>" required >
    <button class="btn my-2 my-sm-0 " type="submit">
    <i class="zmdi zmdi-search"></i>
    </button>
        
    <button type="button" class="btn my-2 my-sm-0 ml-2 btn-black search_close" >
    <i class="zmdi zmdi-close"></i>
    </button>
    
<?php echo CHtml::endForm(); ?>
<!--END SEARCH -->

<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_datatables",
  'class'=>"frm_datatables",
  'onsubmit'=>"return false;"
)); 
?> 

<table class="ktables_list table_datatables">
<thead>
<tr>
<th width="10%"><?php echo t("#")?></th>
<th width="30%"><?php echo t("Name")?></th>
<th width="15%"><?php echo t("Actions")?></th>
</tr>
</thead>

<tbody></tbody>

</table>

<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>