<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>
 

<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_datatables",
  'class'=>"frm_datatables frm_search_filter",
  'onsubmit'=>"return false;"
)); 
?> 

<div class="form-inline justify-content-end">

    
   <div class="input-group rounded">
		    <?php echo CHtml::dropDownList('filter_merchant','',
			  array(),          
			  array(
			  'class'=>'form-control custom-select form-control-select select_two_ajax mr-sm-2',
			  'action'=>'search_merchant',
			  'data-placeholder'=>t("Filter by merchant")
			))?>		
	
			
		 <input name="date_filter" class="form-control date_range_picker ml-2" 
		    type="text" placeholder="<?php echo t("Filter by date")?>" data-separator="<?php echo t("to")?>"
		    readonly
		  >     
		 
		  <button type="submit" class="submit input-group-text border-0 ml-2">
		    <i class="zmdi zmdi-search"></i>
		  </button>
		   <button type="button" class="input-group-text border-0 ml-2 btn-black search_close_filter" >
		    <i class="zmdi zmdi-close"></i>
		    </button>
	</div> <!--input-group-->
    
</div>    

<table class="ktables_list table_datatables mt-3">
<thead>
<tr>
<th width="10%"></th>
<th width="15%"><?php echo t("Name")?></th>
<th width="15%"><?php echo t("Total Approved")?></th>
<th width="15%"><?php echo t("Total Denied")?></th>
<th width="15%"><?php echo t("Total Pending")?></th>
</tr>
</thead>
<tbody></tbody>
</table>

<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>