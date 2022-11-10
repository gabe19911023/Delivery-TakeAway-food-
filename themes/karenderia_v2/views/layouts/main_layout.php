<?php $this->beginContent('/layouts/main-layout'); ?>

<!--TOP NAV-->
<?php $this->renderPartial("//layouts/top-nav",array(
  'widget_col1'=>CNavs::widgetColOne(),
  'widget_col2'=>CNavs::widgetColTwo(),
))?>
<!--END TOP NAV-->

<!--MAIN CONTENT-->
<div class="page-content">
<?php echo $content; ?>
</div>
<!--END MAIN CONTENT-->

<!--SUB FOOTER-->
<?php $this->renderPartial("//layouts/sub-footer")?>
<!--END SUB FOOTER-->

<!--FOOTER-->
<?php $this->renderPartial("//layouts/footer")?>
<!--END FOOTER-->

<?php $this->endContent(); ?>