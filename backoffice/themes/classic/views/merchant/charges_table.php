<?php $this->renderPartial("/tpl/search-form",array(
 'link'=>isset($link)?$link:''
))?>

<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_datatables",
  'class'=>"frm_datatables",
  'onsubmit'=>"return false;"
)); 
?> 

<table class="ktables_list table_datatables">
<thead>
<tr>
<th width="30%"><?php echo t("Shipping")?></th>
<th width="20%"><?php echo t("Fee")?></th>
<th width="20%"><?php echo t("Min. Order")?></th>
<th width="20%"><?php echo t("Max. Order")?></th>
<th width="15%"><?php echo t("Actions")?></th>
</tr>
</thead>

<tbody></tbody>

</table>

<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>