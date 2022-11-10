

<div class="card">

<div class="text-center p-2 pt-4">
  <h5>An error has occured</h5>
  <p><b>Error updating see error for details.</p>
</div>

  <div class="card-body">
    <?php if(isset($error)):?>
    <p class="alert alert-danger"><?php echo $error?></p>
    <?php endif;?>
  </div>

  <div class="tex-center m-3">
    <a href="<?php echo Yii::app()->createUrl('/install/import_sql')?>" class="btn btn-success w-100 rounded-0 disabled ">
    Import SQL
    </a>
  </div>
  
</div> 

</div> 
<!--card-->