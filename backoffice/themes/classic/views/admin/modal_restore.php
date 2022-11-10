<div class="modal modal_restore fade"   role="dialog" >
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
     <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title;?></h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
        </div>
        <div class="modal-body">
        <p><?php echo $content?></p>        
        </div>

       <div class="modal-footer">
         <button type="button" class="btn" data-dismiss="modal">
          <?php echo t("Cancel")?>
         </button>
         <a href="<?php echo $link?>" class="btn btn-green item_delete">
         <?php echo t("Yes and continue")?>
         </a>
       </div>
               
     </div>
  </div>
</div>