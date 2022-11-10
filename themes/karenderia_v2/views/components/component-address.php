
<!--COMPONENTS ADDRESS-->	 		    
<?php $maps = CMaps::config();?>    
<component-address
ref="addressform"
@after-close="afterCloseAddForm"
@after-save="afterSaveAddForm"
@after-delete="afterDeleteAddForm"
set_place_id="true"
:cmaps_config="{
  provider: '<?php echo CJavaScript::quote($maps['provider'])?>',  
  key: '<?php echo CJavaScript::quote($maps['key'])?>',  
  zoom: '<?php echo CJavaScript::quote($maps['zoom'])?>',
  icon: '<?php echo CJavaScript::quote($maps['icon'])?>',
  icon_merchant: '<?php echo CJavaScript::quote($maps['icon_merchant'])?>',
  icon_destination: '<?php echo CJavaScript::quote($maps['icon_destination'])?>',
}"
:label="{
  title: '<?php echo CJavaScript::quote(t("Address details"))?>',  
  adjust_pin: '<?php echo CJavaScript::quote(t("Adjust pin"))?>',  
  delivery_options: '<?php echo CJavaScript::quote(t("Delivery options"))?>',
  delivery_instructions: '<?php echo CJavaScript::quote(t("Add delivery instructions"))?>',
  notes: '<?php echo CJavaScript::quote(t("eg. ring the bell after dropoff, leave next to the porch, call upon arrival, etc"))?>',
  address_label: '<?php echo CJavaScript::quote(t("Address label"))?>',
  save: '<?php echo CJavaScript::quote(t("Save"))?>',
  cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>',
  location_name: '<?php echo CJavaScript::quote(t("Aparment, suite or floor"))?>',  
  address_label: '<?php echo CJavaScript::quote(t("Address label"))?>',  
  confirm: '<?php echo CJavaScript::quote(t("Confirm"))?>',
  yes: '<?php echo CJavaScript::quote(t("Yes"))?>',
  are_you_sure: '<?php echo CJavaScript::quote(t("Are you sure you want to continue?"))?>',
  complete_address: '<?php echo CJavaScript::quote(t("Complete Address"))?>',
  edit: '<?php echo CJavaScript::quote(t("Edit"))?>',
}"
>
</component-address>
<!--END COMPONENTS  ADDRESS-->