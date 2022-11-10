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

    <?php echo CHtml::dropDownList('filter_merchant','',
	  array(),          
	  array(
	  'class'=>'form-control custom-select form-control-select select_two_ajax mr-sm-2',
	  'action'=>'search_merchant',
	  'data-placeholder'=>t("Filter by merchant")
	))?>
	
    <input name="date_filter" class="form-control mr-sm-2 date_range_picker" 
    type="text" placeholder="<?php echo t("Filter by date")?>" data-separator="<?php echo t("to")?>"
    readonly
     >    
    
    <?php 
    echo CHtml::dropDownList("status_filter",'',(array)$status,array(
      'class'=>"selectpicker mr-sm-2",
      'multiple'=>true,
      'title'=>t("Filter by status"),
      'data-selected-text-format'=>"count > 2"
    ));
    ?>

    <input name="search[value]" class="form-control mr-sm-2 search" type="search" placeholder="<?php echo t("Search")?>"  >
    <button class="btn my-2 my-sm-0 " type="submit">
    <i class="zmdi zmdi-search"></i>
    </button>
        
    <button type="button" class="btn my-2 my-sm-0 ml-2 btn-black search_close_filter" >
    <i class="zmdi zmdi-close"></i>
    </button>
</div>    

<table class="ktables_list table_datatables mt-3">
<thead>
<tr>
<th width="7%"><?php echo t("#")?></th>
<th width="10%"></th>
<th width="15%"><?php echo t("Name")?></th>
<th width="15%"><?php echo t("Total Sales")?></th>
<th width="15%"><?php echo t("Total Commission")?></th>
<th width="15%"><?php echo t("Merchant Earnings")?></th>
</tr>
</thead>

<tbody></tbody>

</table>

<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>