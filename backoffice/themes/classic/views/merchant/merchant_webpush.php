<div id="vue-webpush-settings">
 
  <?php if($webpush_provider=="pusher"):?>
  <components-web-pusher
  ref="pushsettings"
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
  :settings="{    
    instance_id : '<?php echo $pusher_instance_id;?>',    
  }"  
  :iterest_list='<?php echo json_encode($iterest_list)?>'
  
  :message="{    
    could_not_get_device : '<?php echo t('Could not get device interests');?>',    
    notification_enabled : '<?php echo t('notifications enabled');?>',    
    notification_disabled : '<?php echo t('notifications disabled');?>',    
    notification_stop : '<?php echo t('Could not stop Beams SDK');?>',    
    notification_start : '<?php echo t('Could not start Beams SDK');?>', 
    notification_save : '<?php echo t('Notification type save');?>',    
    notification_could_not_set_device : '<?php echo t('Could not set device interests');?>',    
  }"  
  
  >
  </components-web-pusher>
  <?php endif;?>
  
</div>
<!--vue-->

<script type="text/x-template" id="xtemplate_webpushsettings">
<DIV class="position-relative">

<div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
    <div>
      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
    </div>
</div>


<h5 class="mt-3 mb-3"><?php echo t("Notifications Settings")?></h5>

<div class="custom-control custom-switch custom-switch-md">
  <input @change="enabledWebPush" v-model="webpush_enabled" value="1" type="checkbox" class="custom-control-input" id="webpush_enabled">
  <label class="custom-control-label" for="webpush_enabled"><?php echo t("Enabled")?></label>
</div>


<button @click="saveWebNotifications" class="mt-4 btn btn-green w-100"
:class="{ loading: is_submitted }"
:disabled="is_submitted" 
>
      <span><?php echo t("Save")?></span>
	  <div class="m-auto circle-loader" data-loader="circle-side"></div> 
</button>


</DIV>
</script>