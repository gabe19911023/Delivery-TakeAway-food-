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
<th width="10%"></th>
<th width="12%"><?php echo t("Order ID")?></th>
<th width="18%"><?php echo t("Merchant Name")?></th>
<th width="18%"><?php echo t("Customer")?></th>
<th width="30%"><?php echo t("Order Information")?></th>
<th width="15%"><?php echo t("Actions")?></th>
</tr>
</thead>

<tbody></tbody>

</table>

<?php echo CHtml::endForm(); ?>

<?php $this->renderPartial("/admin/modal_delete");?>
<?php $this->renderPartial("/admin/modal_order_history");?>