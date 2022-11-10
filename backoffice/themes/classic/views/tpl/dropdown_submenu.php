<?php if(isset($menu) && is_object($menu)):?>
<ul id="mobile-nav-tabs" class="nav nav-tabs mb-3">	  
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:;">
      <?php echo CHtml::encode($this->pageTitle)?>
    </a>
    <div class="dropdown-menu">
      <?php foreach ($menu->items as $val):?>
        <?php
        $params = array();
        foreach ($val['url'] as $key=>$url) {        	
        	if($key!="0"){
        		$params[$key]=$url;
        	}        	
        }        
        ?>
        <a class="dropdown-item" href="<?php echo Yii::app()->CreateUrl($val['url'][0],(array)$params)?>">
          <?php echo $val['label']?>
        </a>
      <?php endforeach;?>
    </div>
  </li>  
</ul>
<?php endif;?>
