<div id="vue-profile-photo" class="position-relative update-photo"> 
            
    <div ref="refavatar">
    <el-image		    
    style="width: 100px; height: 100px"
    src="<?php echo $avatar;?>"
    fit="contain"
    lazy
    class="img-fluid mb-2 rounded-circle"
    ></el-image>
    </div>
    
    <div class="layer-black rounded-circle img-120"></div>
    <div class="d-flex align-items-center rounded-circle img-120">	         
        <a @click="showUpload" class="handle w-100 text-center bold">
        <i class="zmdi zmdi-cloud-upload"></i> <?php echo t("Update")?>
        </a>	         
    </div>	    
    
    <components-profile-photo
    ref="profile_photo"
    @on-save="SavePhoto"
    :label="{
        crop_photo: '<?php echo CJavaScript::quote(t("Adjust photo"))?>',
        save_photo: '<?php echo CJavaScript::quote(t("Save Photo"))?>',
        add_photo: '<?php echo CJavaScript::quote(t("Add Photos"))?>',
        drop_files_here: '<?php echo CJavaScript::quote(t("Drop files here to upload"))?>', 
        max_file_exceeded : '<?php echo CJavaScript::quote(t("Maximum files exceeded"))?>',  
        dictDefaultMessage : '<?php echo CJavaScript::quote(t("Drop files here to upload"))?>',  
        dictFallbackMessage : '<?php echo CJavaScript::quote(t("Your browser does not support drag'n'drop file uploads."))?>',  dictFallbackText : '<?php echo CJavaScript::quote(t("Please use the fallback form below to upload your files like in the olden days."))?>',  
        dictFileTooBig: '<?php echo CJavaScript::quote(t("File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.w"))?>',  
        dictInvalidFileType: '<?php echo CJavaScript::quote(t("You can't upload files of this type."))?>',  
        dictResponseError: '<?php echo CJavaScript::quote(t("Server responded with {{statusCode}} code."))?>',  
        dictCancelUpload: '<?php echo CJavaScript::quote(t("Cancel upload"))?>',  
        dictCancelUploadConfirmation: '<?php echo CJavaScript::quote(t("Are you sure you want to cancel this upload?"))?>',  
        dictRemoveFile: '<?php echo CJavaScript::quote(t("Remove file"))?>',  
        dictMaxFilesExceeded: '<?php echo CJavaScript::quote(t("You can not upload any more files."))?>',   
    }"
    >
    </components-profile-photo>
        
</div> <!--update-photo-->