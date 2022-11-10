

<div class="card">

  <div class="text-center p-2 pt-4">
    <h5>Import SQL</h5>
    <p><b>Database is connected</b>. Proceed Import Database. The auto installer will run a sql file.</p>
  </div>
  
    <div class="card-body">
      <?php if(isset($error)):?>
      <p class="alert alert-danger"><?php echo $error?></p>
      <?php endif;?>
    </div>
  
	<div class="tex-center m-3">
	  <a href="<?php echo Yii::app()->createUrl('/install/import_sql',array('version'=> date("c")))?>" class="btn btn-success w-100 rounded-0 disabled ">
	  Import SQL
	  </a>
	</div>
	
</div> 
 
</div> 
<!--card-->