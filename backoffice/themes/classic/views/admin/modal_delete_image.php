<div class="modal delete_image_confirm_modal fade"   role="dialog" >
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
     <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"><?php echo t("Delete Confirmation")?></h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
        </div>
        <div class="modal-body">
        <p><?php echo t("Are you sure you want to permanently delete the selected item?")?></p>        
        </div>

       <div class="modal-footer">
         <button type="button" class="btn" data-dismiss="modal">
          <?php echo t("Cancel")?>
         </button>
         <a href="javascript:;" class="btn btn-green item_delete">
         <?php echo t("Delete")?>
         </a>
       </div>
               
     </div>
  </div>
</div>