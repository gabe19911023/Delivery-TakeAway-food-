
<DIV id="vue-notifications-list" class="mt-3">

<el-skeleton animated :loading="is_loading" >
<template #template>    
    <div class="mt-4 mb-4">
       <div><el-skeleton-item style="width: 100%;" variant="button" /></div>
       <div><el-skeleton-item style="width: 100%;" variant="text" /></div>
    </div>
    <el-skeleton variant="p" :rows="12" />
</template>
<template #default>

<h5 ><?php echo t("All notifications")?></h5>
<div class="card p-3 mb-3"  >
 <div class="card-body position-relative"> 

 	<div v-if="isFirstLoad" class="loading cover-loader d-flex align-items-center justify-content-center">
	    <div>
	      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
	    </div>
	</div>

 <div class="notification-dropdown">

   <ul v-if="hasData"  class="list-unstyled m-0 normal">
     <template v-for="(data_item,key) in data" > 
     <li v-for="(item,index) in data_item">     
      <a :class="{ active: index<=0 }" >
        <div class="d-flex">
           <div v-if="item.image!=''" class="flex-col mr-3">  
              <template v-if="item.image_type=='icon'">
                 <div class="notify-icon rounded-circle bg-soft-primary">
                    <i :class="item.image"></i>
                  </div>
              </template>
              <template v-else>
               <div class="notify-icon">
                  <img class="img-40 rounded-circle" :src="item.image" />
               </div>
              </template>
           </div>
           <div class="flex-col">
              <div class="text-heading" v-html="item.message"></div>
              <div class="dropdown-text-light">{{item.date}}</div>
           </div>
        </div>
      </a>
     </li>		
     </template>            		             
    </ul>
 
 </div>
 <!--notification-dropdown-->
  
<div class="d-flex justify-content-center mt-5 mb-1" >

 <template  v-if="show_next_page" >
 <a href="javascript:;" @click="getData(page)" class="btn btn-black m-auto w25"
 :class="{ loading: is_loading }" :disabled="is_loading" 
 >
   <span class="label"><?php echo t("Load more")?></span>	   
   <div class="m-auto circle-loader" data-loader="circle-side"></div>
 </a>
 </template>
 <template v-else>
    <p v-if="!is_loading" class="m-0 text-muted"><?php echo t("end of results");?></p>
 </template>
 
</div>
 
 </div> 
 <!--card-body-->
</div>
<!--card-->

</template>
</template>

</DIV>