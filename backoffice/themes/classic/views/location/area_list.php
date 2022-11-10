<?php $this->renderPartial("/tpl/search-form",array(
 'link'=>isset($link)?$link:''
))?>

<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_datatables",
  'class'=>"frm_datatables",
  'onsubmit'=>"return false;"
)); 
echo CHtml::hiddenField('country_id',$model->country_id);
?> 

<div class="table-responsive-md">
<table class="ktables_list table_datatables">
<thead>
<tr>
<th width="8%"><?php echo t("#")?></th>
<th width="25%"><?php echo t("Area")?></th>
<th width="25%"><?php echo t("City")?></th>
<th width="15%"><?php echo t("Sequence")?></th>
<th width="15%"><?php echo t("Actions")?></th>
</tr>
</thead>
<tbody></tbody>
</table>
</div>

<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>