<DIV id="vue-bootbox">
<component-bootbox
ref="bootbox"
@callback="Callback"
size='small'
:label="{
  confirm: '<?php echo CJavaScript::quote(t("Confirm"))?>',
  are_you_sure: '<?php echo CJavaScript::quote(t("Are you sure you want to continue?"))?>',
  yes: '<?php echo CJavaScript::quote(t("Yes"))?>',
  cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>',  
  ok: '<?php echo CJavaScript::quote(t("Okay"))?>',  
}"
>
</component-bootbox>
</DIV>