<div class="modal order_history_modal fade"   role="dialog" >
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
     <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"><?php echo t("Order history")?></h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
        </div>
        <div class="modal-body">
          <table class="table table-borderless table-hover">
          <thead>
          <tr>
           <th><?php echo t("Date/Time")?></th>
           <th><?php echo t("Status")?></th>
           <th><?php echo t("Remarks")?></th>
          </tr>
          </thead>
          <tbody>           
          </tbody>
          </table>
        </div>

       <div class="modal-footer">
         <button type="button" class="btn btn-green" data-dismiss="modal">
          <?php echo t("Close")?>
         </button>        
       </div>
               
     </div>
  </div>
</div>