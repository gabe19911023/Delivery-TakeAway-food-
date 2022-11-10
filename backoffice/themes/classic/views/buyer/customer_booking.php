<?php $this->renderPartial("/tpl/search-form",array(
 'link'=>isset($link)?$link:''
))?>

<?php echo CHtml::beginForm('','post',array(
  'id'=>"frm_datatables",
  'class'=>"frm_datatables",
  'onsubmit'=>"return false;"
)); 
echo CHtml::hiddenField('id',$model->client_id);
?>

<table class="ktables_list table_datatables">
<thead>
<tr>
<th width="10%"><?php echo t("#")?></th>
<th width="30%"><?php echo t("Details")?></th>
<th width="15%"><?php echo t("Actions")?></th>
</tr>
</thead>

<tbody></tbody>

</table>


<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>
<?php $this->renderPartial("/admin/modal_order_history");?>