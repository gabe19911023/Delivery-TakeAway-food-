<!--COMPONENTS NEW ORDER-->
<div id="components-modal-neworder">
	<components-neworder @new-order="onConfirm" @close-order="onClose" :title="title" :content="content" :is_loading="is_loading"></components-neworder>
</div>

<?php $this->renderPartial("//components/vue-bootbox") ?>

<DIV id="vue-merchant-details">
	<div class="top-merchant-details d-none d-lg-block">
		<div class="container pt-3">
			<div class="row">
				<div class="col-lg-3 col-md-3 mb-4 mb-lg-3 list-items">

					<div class="merchant-image-preview">
						<el-image style="width: 100%; height: 170px" src="<?php echo $data['url_logo']; ?>" fit="cover" lazy>
						</el-image>
					</div>

				</div>
				<!--col-->

				<div class="col-lg-9 col-md-9 mb-4 mb-lg-3">

					<div class="d-flex merchant-details" v-cloak>
						<div class="align-self-center w-100">
							<div class="d-flex justify-content-start mb-2">
								<div class="w-50 align-self-center">
									<h5><?php echo $data['restaurant_name'] ?></h5>
								</div>

								<?php if (0 & isset($data['cuisine'][0])) : ?>
									<div class="w-25 align-self-center"><span class="badge badge-white"><?php echo $data['cuisine'][0] ?></span></div>
								<?php endif; ?>

								<div class="w-25 align-self-center" style="display: none;">
									<span class="badge badge-white rounded-pill">
										<?php echo Price_Formatter::convertToRaw($data['ratings'], 1) ?>
									</span>
								</div>
								<div class="w-25 align-self-center" style="display: none;">

									<template v-if="!is_loading">
										<!--COMPONENTS-->
										<component-save-store :active="found" :merchant_id="<?php echo intval($data['merchant_id']) ?>" @after-save="getSaveStore" />
										</component-save-store>
										<!--COMPONENTS-->
									</template>

								</div>
							</div>
							<!--d-flex-->

							<div class="d-flex justify-content-start mb-3">

								<!-- <div class="w-50 align-self-center"><p class="m-0">$ - <?php //echo t("low cost restaurant")
																							?></p></div> -->

								<div class="w-25 align-self-center">&nbsp;</div>
								<!-- <div class="w-25 align-self-center">
	           <p class="m-0">
	           <?php //echo t("Based on")
				?> <a href="#section-review"><u><?php //echo t("{{review_count}} reviews",array('{{review_count}}'=>$data['review_count']))
												?></u>
	           </a>
	           </p>
	        </div> -->
								<!-- <div class="w-25 align-self-center">		        
	           <template v-if="!is_loading">	        
	           <p v-if="!found" class="m-0"><?php //echo t("Save store")
											?></p>
	           <p v-else class="m-0"><?php //echo t("Saved")
										?></p>
	           </template>
	        </div> -->
							</div>
							<!--d-flex-->


							<?php if (is_string($data['description']) && strlen($data['description']) > 0) : ?>
								<div class="readmoreeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee">
									<div class="collapse" id="collapse-content" aria-expanded="false">
										<?php echo Yii::app()->input->xssClean($data['description']) ?>
									</div>
									<a role="button" class="collapsed" data-toggle="collapse" href="#collapse-content" aria-expanded="false" aria-controls="collapse-content"></a>
								</div>
							<?php endif; ?>

						</div>
						<!--cente-->
					</div>
					<!--flex-->

					<component-merchant-services ref="ref_services" @after-update="afterUpdateServices" :label="{
			min:'<?php echo CJavaScript::quote(t("min")) ?>', 			
		}">
					</component-merchant-services>
					<div class="row widget-search-bar mt-2">
					</div>

				</div>
				<!--col-->


			</div>
			<!--row-->
		</div>
		<!--container-->
	</div>
	<!--top-merchant-details-->

	<!-- mobile view -->
	<div class="d-block d-lg-none">
		<div class="top-merchant-details mobile-merchant-details position-relative" style="">

			<!-- <el-image
	style="width: 100%; height: 100%"
	:src=""
	fit="cover"
 ></el-image> -->

			<!--  <div class="sub">
	 <div class="container p-4">
	     <div class="d-flex justify-content-end">		
			<template v-if="!is_loading"> 	          
			<component-save-store
				:active="found"
				:merchant_id="<?php echo intval($data['merchant_id']) ?>"
				@after-save="getSaveStore"
			/>
			</component-save-store>	        
			</template>
		</div>  
	</div> 
 </div>  -->
		</div>
		<!-- top-merchant-details -->


		<div class="container pt-2 pb-2">
			<h5 class="m-0"><?php echo $data['restaurant_name'] ?></h5>

			<!--   <a href="#section-address" class="d-block chevron center position-relative no-hover" style="display:none !important;">
	    <p class="font-weight-bolder m-0">
			<span class="mr-1"><i class="zmdi zmdi-star"></i></span>
			<span class="mr-1">(<?php //echo t("{{rating}} ratings",array('{{rating}}'=>$data['review_count']))
								?>)</span>
			<span>&bull; <?php //echo $data['cuisine'][0]
							?> &bull; $<span>
		</p>		
		<p class="font-weight-light m-0"><?php echo t("Tap for hours,address, and more") ?></p>
	</a> -->

			<div class="text-center mobile-service">
				<component-merchant-services ref="ref_services" @after-update="afterUpdateServices" :label="{
			min:'<?php echo CJavaScript::quote(t("min")) ?>', 			
		}">
				</component-merchant-services>
				<div class="row">
					<div class="col-4"></div>
					<div class="col-5 widget-search-bar-mobileeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee mt-2"></div>
				</div>
			</div>

		</div>
		<!-- container-fluid -->

	</div>
	<!-- mobile view -->

</DIV>
<!-- vue-merchant-details -->


<!--SHOW CHANGE ADDRESS IF OUT OF COVERAGE-->
<?php $this->renderPartial("//components/change-address") ?>
<?php $this->renderPartial("//components/address-needed") ?>
<?php
// $this->renderPartial("//components/schedule-order",array(
//   'show'=>true
// ))
?>


<div class="section-menu mt-4">
	<div class="container">
		<div class="row">

			<div id="vue-merchant-category" class="col-lg-2 col-md-12 mb-3 mb-lg-3 pr-lg-0 menu-left">

				<div id="sticky-sideba1r" class="sti1cky-sidebar d-none d-lg-block" v-cloak>
					<el-skeleton :count="10" :loading="category_loading" animated>
						<template #template>
							<div class="mb-2">
								<el-skeleton-item variant="caption" style="width: 50%" />
							</div>
							<el-skeleton-item variant="text" style="width: 90%" />
						</template>
						<template #default>
							<h5><?php echo t("Menu") ?></h5>
							<ul id="menu-category" class="list-unstyled menu-category">
								<li v-for="val in category_data">
									<a :href="'#'+val.category_uiid" class="nav-link">{{ val.category_name }}</a>
								</li>
							</ul>
						</template>
					</el-skeleton>
				</div>
				<!-- sticky	 -->

				<!-- mobile view category -->
				<div class="d-block d-lg-none">

					<components-category-carousel :data="category_data" restaurant_name="<?php echo CHtml::encode($data['restaurant_name']) ?>">
					</components-category-carousel>

				</div>
				<!-- mobile view category -->

			</div>
			<!--col menu-left-->

			<div id="vue-merchant-menu" class="col-lg-7 col-md-12 mb-3 mb-lg-3 menu-center position-relative">
				<div class="row search-geocomplete-container">
					<div class="col-md-12 mb-3 search-geocomplete-content">
						<div class="position-relative search-geocomplete">
							<div class="img-20 m-auto pin_placeholder icon"></div>
							<input class="form-control form-control-text xsearch-input" placeholder="Enter delivery address">
						</div>
						<!-- <div class="w-100 text-right">
	    				<a href="javascript:void(0);" class="add-address">Add Address Manually</a>
	    			</div> -->
					</div>
					<div class="col-md-6">
						<select id="custom_delivery_days" class="form-control custom-select mb-3 custom_delivery_days">
						</select>
					</div>
					<div class="col-md-6">
						<select id="custom_delivery_time" class="form-control custom-select custom_delivery_time">
						</select>
					</div>
				</div>
				<!--CHANGE ADDRRESS-->
				<component-change-address ref="address" @set-location="afterChangeAddress" @after-close="afterCloseAddress" @set-placeid="afterSetAddress" @set-edit="editAddress" @after-delete="afterDeleteAddress" :label="{
			title:'<?php echo CJavaScript::quote(t("Delivery Address")) ?>', 
			enter_address: '<?php echo CJavaScript::quote(t("Enter your address")) ?>',	    	    
		}" :addresses="addresses" :location_data="">
				</component-change-address>

				<el-skeleton :count="12" :loading="menu_loading" animated>
					<template #template>
						<div class="row m-0">
							<div class="col-lg-3 col-md-3 p-0 mb-2">
								<el-skeleton-item variant="image" style="width: 95%; height: 140px" />
							</div> <!-- col -->
							<div class=" col-lg-9 col-md-9 p-0">
								<div class="row m-0 p-0">
									<div class="col-lg-12">
										<el-skeleton :rows="2" />
									</div>
								</div>
								<!-- row -->
							</div> <!-- col -->
						</div> <!--  row -->
					</template>

					<template #default>
						<?php $this->renderPartial("//store/menu-data") ?>
					</template>

				</el-skeleton>

				<?php $this->renderPartial("//store/item-details", array(
					'is_mobile' => Yii::app()->params['isMobile']
				)) ?>


				<el-affix position="bottom" :offset="20" v-if="item_in_cart>0" z-index="9" v-cloak>
					<div class="floating-cart d-block d-md-none">
						<button @click="showDrawerCart" class="btn btn-black small rounded w-100 position-relative">
							<p class="m-0"><?php echo t("View order") ?></p>
							<h5 class="m-0">{{merchant_data.restaurant_name}}</h5>
							<count>{{item_in_cart}}</count>
						</button>
					</div>
				</el-affix>

			</div>
			<!--col menu center-->

			<div class="col-lg-3 col-md-12 mb-3 mb-lg-3 menu-right p-0 d-none d-lg-block">

				<?php $this->renderPartial("//store/cart", array(
					'checkout' => false,
					'checkout_link' => $checkout_link
				)) ?>

			</div>
			<!--col menu right-->

		</div>
		<!--row-->
	</div>
	<!--container-->
</div>
<!--section-menu-->
<!--SECTION MENU-->


<!--SECTION RESTAURANT DETAILS-->

<div class="container mt-0 mt-lg-5">

	<section id="section-about" class="mb-3 p-2 p-lg-0">
		<div class="row">
			<div class="col-lg-3 col-md-3 p-0 mb-2 mb-lg-0">
				<div class="d-flex">
					<div class="mr-3"><img class="img-20" src="<?php echo Yii::app()->theme->baseUrl . "/assets/images/comment-more.png" ?>" /></div>
					<div>
						<h5><?php echo t("Few words about {{restaurant_name}}", array('{{restaurant_name}}' => $data['restaurant_name'])) ?></h5>
					</div>
				</div>
				<!--d-flex-->
			</div>
			<!--col-->
			<div class="col-lg-6 col-md-6">
				<p><?php echo Yii::app()->input->xssClean(nl2br($data['short_description'])) ?></p>
			</div>
			<!--col-->
		</div>
		<!--row-->
	</section>


	<section id="section-gallery" class="mb-5 p-2 p-lg-0">
		<div class="row">
			<div class="col-lg-3 col-md-3 p-0 mb-2 mb-lg-0">
				<div class="d-flex">
					<div class="mr-3"><img class="img-20" src="<?php echo Yii::app()->theme->baseUrl . "/assets/images/image-gallery.png" ?>" /></div>
					<div>
						<h5><?php echo t("Gallery") ?></h5>
					</div>
				</div>
				<!--d-flex-->
			</div>
			<!--col-->
			<div class="col-lg-9 col-md-9">

				<?php if ($gallery) : ?>
					<div class="gallery gallery_magnific row w-50 hover13">
						<?php $x = 1; ?>
						<?php foreach ($gallery as $gallery_item) : ?>
							<?php //if($x<=5):
							?>
							<div class="col-lg-4 col-md-5 col-sm-6 col-6 mb-0 mb-lg-0  p-1">
								<div class="position-relative">
									<figure>
										<div class="skeleton-placeholder"></div>
										<a href="<?php echo $gallery_item['image_url'] ?>">
											<img class="rounded lazy" data-src="<?php echo $gallery_item['thumbnail'] ?>" />
										</a>
									</figure>
								</div>
							</div>
							<?php //endif;
							?>

							<?php if (0) : ?>
								<div class="col-lg-4 col-md-5 col-sm-6 col-6 mb-0 mb-lg-0  p-1">
									<div class="position-relative">
										<div class="skeleton-placeholder"></div>
										<a href="<?php echo $gallery_item['image_url'] ?>">
											<div class="gallery-more d-flex align-items-center justify-content-center">+<?php echo count($gallery) - 5; ?></div>
											<img class="rounded lazy" data-src="<?php echo $gallery_item['image_url'] ?>" />
										</a>
									</div>
								</div>
								<?php break; ?>
							<?php endif; ?>

							<?php $x++; ?>
						<?php endforeach; ?>
					</div>
					<!--gallery-->
				<?php endif; ?>

			</div>
			<!--col-->
		</div>
		<!--row-->
	</section>

	<section id="section-address" class="mb-4 p-2 p-lg-0">
		<div class="row">
			<div class="col-lg-3 col-md-12 p-0 mb-3 mb-lg-0">
				<div class="d-flex">
					<div class="mr-3"><img class="img-20 contain" style="height:28px;" src="<?php echo Yii::app()->theme->baseUrl . "/assets/images/location.png" ?>" /></div>
					<div>
						<h5><?php echo t("Address") ?>:</h5>
						<div class="mb-3">
							<p class="m-0"><?php echo $data['merchant_address'] ?></p>
							<?php if ($map_direction) : ?>
								<a href="<?php echo $map_direction; ?>" target="_blank" class="a-12"><u><?php echo t("Get direction") ?></u></a>
							<?php endif; ?>
						</div>

					</div>
				</div>
				<!--
       <div class="d-flex">
          <div class="mr-3"><img class="img-20" src="<?php echo Yii::app()->theme->baseUrl . "/assets/images/clock.png" ?>"/></div>
          <div class="flex-fill">
             <h5><?php echo t("Opening hours") ?>:</h5>
             <?php if (is_array($opening_hours) && count($opening_hours) >= 1) : ?>
             <table class="w-100">              
             <?php foreach ($opening_hours as $opening_hours_val) : ?>
                <tr >
                <td class="align-top pb-1"><?php echo ucwords(t($opening_hours_val['day'])) ?></td>
                 <td class="bold align-top pb-1">
                  <p class="m-0">
                  <?php echo t(
							"[start] - [end]",
							array(
								'[start]' => Date_Formatter::Time($opening_hours_val['start_time']),
								'[end]' => Date_Formatter::Time($opening_hours_val['end_time'])
							)
						)
					?>
                  </p>
                  <?php if (!empty($opening_hours_val['start_time_pm'])) : ?>
                  
	                  <p class="m-0">
	                  <?php echo t(
								"[start] - [end]",
								array(
									'[start]' => Date_Formatter::Time($opening_hours_val['start_time_pm']),
									'[end]' => Date_Formatter::Time($opening_hours_val['end_time_pm'])
								)
							)
						?>
	                  </p>  
                  
                  <?php endif; ?>
                  
                  <?php if (!empty($opening_hours_val['custom_text'])) : ?>
                  <p class="m-0"><?php echo $opening_hours_val['custom_text']; ?></p>
                  <?php endif; ?>
                  
                 </td>
                </tr>
             <?php endforeach; ?>
             </table>
             <?php endif; ?>
          </div>
       </div>
	-->

				<div class="d-flex">
					<div class="mr-3"><img class="img-20" src="<?php echo Yii::app()->theme->baseUrl . "/assets/images/clock.png" ?>" /></div>
					<div class="flex-fill">
						<h5><?php echo t("Opening hours") ?>:</h5>
						<table class="menu-table">
							<thead>
								<tr>
									<th class="align-top pb-1">Day</th>
									<th class="bold align-top pb-1">
										<p class="m-0">Take Away</p>
									</th>
									<th class="bold align-top pb-1">
										<p class="m-0">Delivery</p>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="align-top pb-1">Monday</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 12:15 PM - 9:45 PM </p>
									</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 03:45 PM - 9:00 PM </p>
									</td>
								</tr>
								<tr>
									<td class="align-top pb-1">Tuesday</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 12:15 PM - 9:45 PM </p>
									</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 03:45 PM - 9:00 PM </p>
									</td>
								</tr>
								<tr>
									<td class="align-top pb-1">Wednesday</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 12:15 PM - 9:40 PM </p>
									</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 02:00 PM - 9:00 PM </p>
									</td>
								</tr>
								<tr>
									<td class="align-top pb-1">Thursday</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 12:15 PM - 9:40 PM </p>
									</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 02:00 PM - 9:00 PM </p>
									</td>
								</tr>
								<tr>
									<td class="align-top pb-1">Friday</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 12:15 PM - 9:40 PM </p>
									</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 02:00 PM - 9:00 PM </p>
									</td>
								</tr>
								<tr>
									<td class="align-top pb-1">Saturday</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 12:15 PM - 9:55 PM </p>
									</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 02:00 PM - 9:00 PM </p>
									</td>
								</tr>
								<tr>
									<td class="align-top pb-1">Sunday</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 12:15 PM - 9:55 PM </p>
									</td>
									<td class="bold align-top pb-1">
										<p class="m-0"> 02:00 PM - 9:00 PM </p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

			</div>
			<!--col-->

			<!-- <div class="col-lg-9 col-md-12">
      <?php if (0/*!empty($static_maps)*/) : ?>
      <img class="rounded w-100"  src="<?php //echo $static_maps
										?>" alt="<?php //echo $data['restaurant_name']
													?>">
      <?php endif; ?>     
    </div> -->

		</div>
		<!--row-->
	</section>

</div>
<!--container-->

<!--END SECTION RESTAURANT DETAILS-->



<!--SECTION REVIEW-->
<section id="section-review" class="container mb-4">


	<!--  <div class="row mb-4">
	 <div class="col-3 p-lg-0">
	    <div class="d-flex align-items-center" style="height:28px;">
          <div class="m-0 mr-3"><img class="img-20" src="<?php echo Yii::app()->theme->baseUrl . "/assets/images/star.png" ?>"/></div>
          <div><h5 class="m-0"><?php echo t("Reviews") ?></h5></div>
        </div> 
	 </div> 
	 
	 <div class="col-9">
	     <div class="d-flex justify-content-between align-items-center" style="height:28px;">
	       <div class="flex-fill">
	         <a href="javascript:;" @click="openFormReview" class="a-12"><u><?php echo t("Add your opinion") ?></u></a>
	       </div>
	       <div class=""><p class="m-0 mr-5"><?php echo t("Based on") ?> <u><?php echo t("{{review_count}} reviews", array('{{review_count}}' => $data['review_count'])) ?></u></p></div>
	       <div><span class="badge badge-yellow rounded-pill"><?php echo Price_Formatter::convertToRaw($data['ratings'], 1) ?></span></div>
	     </div> 
	 </div> 
 </div>  -->


	<el-skeleton :count="4" :loading="review_loading" animated>
		<template #template>
			<div class="row items-review mb-4">
				<div class="col-lg-3 col-md-3 p-lg-0 mb-2 mb-lg-0">
					<div class="d-flex align-items-center">
						<div class="mr-3">
							<el-skeleton-item variant="circle" style="width: 60px; height: 60px" />
						</div>
						<div class="flex-grow-1">
							<el-skeleton-item variant="h3" style="width: 50%" />
						</div>
					</div>

				</div>
				<div class="col-lg-9 col-md-9">
					<el-skeleton :rows="2" />
				</div>
			</div>
		</template>
		<template #default>

			<!--items-review-->
			<template v-for="data in review_data">
				<div class="row items-review mb-4" v-for="reviews in data">
					<div class="col-lg-3 col-md-3 p-lg-0 mb-2 mb-lg-0">
						<div class="d-flex align-items-center">
							<div class="mr-3"><img class="img-60 rounded rounded-pill" :src="reviews.url_image" /></div>
							<div>

								<h6 class="m-0" v-if="reviews.as_anonymous==0">{{ reviews.fullname }}</h6>
								<h6 class="m-0" v-if="reviews.as_anonymous==1">{{ reviews.hidden_fullname }}</h6>

								<div class="star-rating" data-totalstars="5" :data-initialrating="reviews.rating" data-strokecolor="#fedc79" data-ratedcolor="#fedc79" data-strokewidth="10" data-starsize="15" data-readonly="true"></div>

							</div>
						</div>
						<!--d-flex-->
					</div>
					<!--col-->

					<div class="col-lg-9 col-md-9">
						<div class="d-flex justify-content-between ">
							<div class="flex-fill mr-4">

								<p class="d-none d-lg-block" v-html="reviews.review"></p>
								<div class="d-block d-lg-none">
									<div class="row no-gutters">
										<div class="col pr-2">
											<p v-html="reviews.review"></p>
										</div>
										<div class="col-1"><span class="badge  rounded-pill">{{ reviews.rating }}</span></div>
									</div>
								</div>

								<div v-if="reviews.meta.tags_like" class="d-flex flex-row mb-3">
									<div v-for="tags_like in reviews.meta.tags_like" class="mr-2">
										<span v-if="tags_like" class="rounded-pill bg-lighter p-1 a-12 pl-2 pr-2">{{ tags_like }}</span>
									</div>
								</div>

								<div v-if="reviews.meta.upload_images" class="gallery review_magnific row m-0">
									<div v-for="upload_images in reviews.meta.upload_images" class="col-lg-2 col-md-3 col-sm-6 col-6 mb-0 mb-lg-0 p-1">
										<figure class="m-0">
											<a :href="upload_images">
												<img class="rounded" :src="upload_images">
											</a>
										</figure>
									</div>
								</div>
								<!--gallery-->

							</div>
							<div class="d-none d-lg-block"><span class="badge badge-yellow rounded-pill">{{ reviews.rating }}</span></div>
						</div>
						<!--flex-->
					</div>
					<!--col-->
				</div>
			</template>
			<!--items-review-->

		</template>
	</el-skeleton>



	<div class="row mb-3" v-if="review_loadmore">
		<div class="col-lg-3 col-md-3 p-0"></div>
		<div class="col-lg-9 col-md-9 ">
			<a href="javascript:;" @click="loadMore" class="btn btn-black m-auto w25"><?php echo t("Load more") ?></a>
		</div>
	</div><!-- row-->


</section>
<!--END SECTION REVIEW-->


<!--COMPONENTS REVIEW -->
<div id="components-modal-review">
	<components-review @add-review="onConfirm" @close-order="onClose" @remove-upload="onRemove" :title="title" :is_loading="is_loading" :required_message="required_message" :upload_images="upload_images" :review-value="review_content" @update:review-value="review_content = $event" :rating-value="rating_value" @update:rating-value="rating_value = $event"></components-review>
</div>


<div class="container-fluid m-0 p-0 full-width">
	<?php $this->renderPartial("//store/join-us", ['data' => $data]) ?>
</div>
<input type="hidden" id="footer_image" value="<?php echo $data['footer_image'] ?>">
<input type="hidden" id="footer_instagram" value="<?php echo $data['instagram'] ?>">
<input type="hidden" id="footer_ticktok" value="<?php echo $data['ticktok'] ?>">
<input type="hidden" id="footer_facebook" value="<?php echo $data['facebook'] ?>">
<input type="hidden" id="url_logo" value="<?php echo $data['url_logo']; ?>">
<div class="modal" id="changeCAddressModal" tabindex="-1" role="dialog" aria-labelledby="changeCAddressModal" aria-hidden="true" style="display: none;">
	<div class="modal-dialog" role="document">
		<form class="forms mt-2 mb-2 address-form" action="/api/getLocationDetails" method="post">
			<div class="modal-content">
				<div class="modal-body" style="overflow-y: inherit !important;">
					<!-- <a href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a> -->
					<h4 class="m-0 mb-3 mt-3">Add Address Details</h4>
					<div class="row search-geocomplete-container">
						<div class="col-md-12 mb-3 search-geocomplete-content">
							<div class="position-relative search-geocomplete">
								<div class="img-20 m-auto pin_placeholder icon"></div>
								<input class="form-control form-control-text search-input" placeholder="Enter delivery address">
								<div class="icon loading-icon" data-loader="circle" style="display:none;"></div>
							</div>
							<div class="search-geocomplete-results" style="display:none;"></div>
						</div>
					</div>
					<div class="form-label-group">
						<input class="form-control form-control-text" placeholder="" id="name" name="name" type="text" value="" required>
						<label for="name" class="required">Number *</label>
					</div>
					<div class="form-label-group">
						<input class="form-control form-control-text" placeholder="" id="address" name="address" type="text" value="" required>
						<label for="address" class="required">Address *</label>
					</div>
					<div class="form-label-group">
						<input class="form-control form-control-text" placeholder="" id="postcode" name="postcode" type="text" value="" required>
						<label for="postcode" class="required">Postcode *</label>
					</div>
					<div class="form-label-group">
						<input class="form-control form-control-text" placeholder="" id="city" name="city" type="text" value="" required>
						<label for="city" class="required">City *</label>
					</div>
				</div>
				<div class="modal-footer justify-content-start">
					<!-- <div class="border flex-fill"><button class="btn btn-black w-100">Cancel</button></div> -->
					<div class="border flex-fill">
						<button class="btn btn-green w-100 basic-info_submt" type="button">
							<span class="label">Save</span>
							<div class="m-auto circle-loader" data-loader="circle-side"></div>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>