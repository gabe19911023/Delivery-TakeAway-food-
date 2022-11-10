<?php 
$menu = new WidgetSiteSetup;
$menu->init();
?>
<ul id="mobile-nav-tabs" class="nav nav-tabs">	  
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:;">
      <?php echo CHtml::encode($this->pageTitle)?>
    </a>
    <div class="dropdown-menu">
      <?php foreach ($menu->items as $val):?>
        <a class="dropdown-item" href="<?php echo Yii::app()->CreateUrl($val['url'][0])?>">
          <?php echo $val['label']?>
        </a>
      <?php endforeach;?>
    </div>
  </li>  
</ul>
