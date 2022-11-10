<?php if($data) :?>
<div class="dropdown language-bar">	      
    <a class="btn btn-sm dropdown-toggle text-truncate" href="javascript:;" 
    role="button" id="languageSelectionList" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <img class="img-30" src="<?php echo Yii::app()->baseUrl?>/assets/flag/<?php echo strtolower($flag)?>.svg" />
    </a>				    
    <div class="dropdown-menu" aria-labelledby="languageSelectionList">				    
        <?php foreach ($data as $key => $item):?>                    
            <?php 
            $active = '';
            if($item->code==$current_lang){
                $active = 'active';
            }
            echo CHtml::link(
                '<div class="d-flex align-items-center">
                  <div class="mr-3"><img class="img-30" src="'.Yii::app()->baseUrl ."/assets/flag/". strtolower($item->flag) .'.svg" /></div>
                  <div class="text-truncate">'.$item->title.'</div>
                </div>'
                ,$this->getOwner()->createMultilanguageReturnUrl($item->code),array('class'=>"dropdown-item $active"));
            ?>
        <?php endforeach?>
    </div>
</div>	
<?php endif;?>