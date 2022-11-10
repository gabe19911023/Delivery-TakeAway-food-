<nav class="navbar navbar-light justify-content-between">
<a class="navbar-brand">
<h5><?php echo CHtml::encode($this->pageTitle)?></h5>
</a>
</nav>

<div class="container ">

    <div class="d-flex justify-content-end mb-3">
        <a href="<?php echo Yii::app()->createUrl("/addon/install")?>" class="btn btn-green"><?php echo t("Install/Update Addon")?></a>
    </div>
    
    
   <div id="vue-addons" v-cloak >
   
   <el-card class="box-card m-auto" shadow="never">
    <el-tabs 
    v-model="active_tab" 
    @tab-click="handleClick"   
    >
        <el-tab-pane label="Installed addon" name="addontab1">

        <el-skeleton  :loading="is_loading" animated>
        <template #template>
           <div  class="row align-items-center mt-2 mb-2">
             <div class="col-md-12 col-lg-3 text-center text-lg-left mb-1 mb-lg-0">
                  <el-skeleton-item variant="image" style="width: 120px; height: 60px" />
              </div>
              <div class="col-md-12 col-lg-9 text-right text-lg-left"> 
                  <div><el-skeleton-item variant="text" style="width: 50%" /></div>
                  <div><el-skeleton-item variant="text" /></div>
                  <div><el-skeleton-item variant="text" /></div>
              </div>
           </div>           
        </template>

        <template #default>
             
            <template v-if="hasData">     
            <div v-if="data" class="w-75 m-auto">
                <div v-for="items in data" class="row align-items-center mt-2 mb-2 border-bottom">
                <div class="col-md-12 col-lg-3 text-center text-lg-left mb-1 mb-lg-0">

                <el-image
                    style="width: 120px; height: 70px"
                    :src="items.image"
                    :fit="fit"
                    lazy
                ></el-image>

                </div>
                <div class="col-md-12 col-lg-7 mb-3 mb-lg-0">
                    <div class="d-flex justify-content-center justify-content-lg-start">
                        <h5 class="m-0 mr-4">{{items.addon_name}}</h5>
                        <p class="m-0 text-muted">{{items.version}}</p>
                    </div>
                </div>
                <div class="col-md-12 col-lg-2  text-right text-lg-left">                
                        <el-switch
                            v-model="items.activated"
                            class="ml-2"
                            active-color="#13ce66"
                            inactive-color="#ebeef5"                    
                            @click="enabledDisabledAddon(items)"
                        />
                </div>
                </div>
                <!-- row -->
           </div>
           </template>
           <template v-else>
              <p><?php echo t("No addons installed.")?></p>
            </template>

        </template>

        </el-skeleton>
        

        </el-tab-pane>
        <el-tab-pane label="Available addon" name="addontab2">
            
            <template v-if="hasAddons">            
            <div v-for="addons in data_addons" class="row mt-2">
                <div class="col-lg-4 col-md-6">                
                <el-card :body-style="{ padding: '0px' }">                    
                    <el-image
                        style="width: 100%; height: 200px"
                        class="addons-image"
                        :src="addons.image"
                        :fit="contain"                        
                    ></el-image>                    

                    <div class="p-2">
                      <h5>{{addons.item}}</h5>
                      <p class="text-truncate-3">{{addons.short_description}}</p>
                    </div>
                    
                    <hr class="m-0"/>
                    <div class="p-2">
                        <div class="row align-items-center">
                            <div class="col-lg-3 col-md-12 text-center text-lg-left mb-2 mb-lg-0">
                                <h3 class="m-0">{{addons.price}}</h3>
                            </div>
                            <div class="col-lg-8 col-md-12 text-center text-lg-right p-0">                                 
                                 <span class="mr-2"><el-link :href="addons.preview" target="_blank" type="info"><?php echo t("Preview")?></el-link></span>
                                 <el-link target="_blank" :href="addons.link" type="success"><?php echo t("Purchase")?></el-link>
                            </div>
                        </div>
                    </div>

                </el-card>

                </div>
            </div>
            </template>
            <template v-else>
              <p><?php echo t("No addons available.")?></p>
            </template>

        </el-tab-pane>    
    </el-tabs>
  </el-card>

   </div>
   <!-- vue addons -->

</div>
<!-- container -->