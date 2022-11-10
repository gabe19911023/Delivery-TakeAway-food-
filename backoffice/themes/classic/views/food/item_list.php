<?php $this->renderPartial("/tpl/search-form",array(
 'link'=>isset($link)?$link:''
))?>

<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_datatables",
  'class'=>"frm_datatables",
  'onsubmit'=>"return false;"
)); 
?> 

<div class="table-responsive-md">
<table class="ktables_list table_datatables">
<thead>
<tr>
<th width="10%"><?php echo t("#")?></th>
<th width="15%"><?php echo t("Available")?></th>
<th width="30%"><?php echo t("Name")?></th>
<th width="15%"><?php echo t("Category")?></th>
<th width="15%"><?php echo t("Price")?></th>
<th width="15%"><?php echo t("Actions")?></th>
</tr>
</thead>
</div>

<tbody></tbody>

</table>

<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>