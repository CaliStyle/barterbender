<input type="hidden" name="yndirectory_detail_business_id" id="yndirectory_detail_business_id" value="{$aBusiness.business_id}" >
{if Phpfox::isMobile()}
	<div class="yndirectory-detailcover yndirectory-detailcover-style1">
		{if count($aCoverPhotos)}
			<div id="yndirectory_detail_cover" class="flexslider">			
				<ul class="slides">
			    	{foreach from=$aCoverPhotos item=aPhoto name=aPhoto}
			    		<li class="">
							<div class="yndirectory-cover-image"  style="background-image: url({img return_url=true server_id=$aPhoto.server_id path='core.url_pic' file='yndirectory/'.$aPhoto.image_path suffix='' width=520 height=320})"></div>
						</li>
		        	{/foreach}
		        </ul>		    
			</div>
			{if isset($sCurrentTheme) && $sCurrentTheme == 'nebula'}
				{literal}
					<style type="text/css">
						div#left{
							top: 255px;
						}
					</style>
				{/literal}
			{/if}
		{else}
			{if isset($sCurrentTheme) && $sCurrentTheme == 'nebula'}
				{literal}
					<style type="text/css">
						div#left{
							top: 45px;
						}
					</style>
				{/literal}
			{/if}
		{/if}

		<div class="yndirectory-detailcover-footer">
			<div class="business-detailcover-rating-review">
				<div>
			    	{$aBusiness.total_score_text}  
			    </div>
			</div>	

			<div class="yndirectory-review">
				<i class="fa fa-pencil-square-o"></i>
				<span>{$iReview}</span><span>{phrase var='review_a_s'}</span>
			</div>

			<div class="yndirectory-follower">
				<i class="fa fa-arrow-right"></i>
				<span>{$iFollower}</span><span>{phrase var='follower_a_s'}</span>
			</div>

			<div class="yndirectory-member">
				<i class="fa fa-group"></i>
				<span>{$iMember}</span><span>{phrase var='member_b_s'}</span>
			</div>
		</div>
	</div>

	{literal}
	<script type="text/javascript" >
		(function(){
				var _stage = '#js_block_border_directory_detailcover #yndirectory_detail_cover',
				_options = {
					controlNav: false,
        			prevText: "",
					nextText: "", 
				},
				_required = function(){
					return !/undefined/i.test(typeof jQuery.flexslider);
				},
				_initFeaturedSlideshow_flag = false,
				initFeaturedSlideshow = function ()
				{
					var stage =  $(_stage);
					if(!stage.length) return;
					if(_initFeaturedSlideshow_flag) return;
					if(!_required()) return;
					_initFeaturedSlideshow_flag = true;
					$(_stage).flexslider(_options);	
				},
				initNextPrev = function()
				{
					$('.flex-prev').on('click', function(){
					    $('.flexslider').flexslider('prev')
					    return false;
					})
					$('.flex-next').on('click', function(){
					    $('.flexslider').flexslider('next')
					    return false;
					})
				}
		
				$Behavior.yndirectoryCoverSlideshow = function() {
					if(!$(_stage).length) return;
					function checkCondition(){
						var stage =  $(_stage);
						if(!stage.length) return;
						if(_initFeaturedSlideshow_flag) return;
		
						if(!_required()){
							window.setTimeout(checkCondition, 400);
						}else{
							initFeaturedSlideshow();
						}
					}
					window.setTimeout(checkCondition, 400);
					
					function checkButtonNextPrev()
					{
						if (!$('.flex-prev').length || !$('.flex-next').length)
						{
							window.setTimeout(checkButtonNextPrev, 400);
						}
						else
						{
							initNextPrev();
						}
					}
					window.setTimeout(checkButtonNextPrev, 400);
				}	
			})();
	</script>
	{/literal}
{else}
	{if $aBusiness.theme_id == 1}
		<div class="yndirectory-detailcover yndirectory-detailcover-style1">
			{if count($aCoverPhotos)}
				<div id="yndirectory_detail_cover" class="flexslider">			
					<ul class="slides">
				    	{foreach from=$aCoverPhotos item=aPhoto name=aPhoto}
				    		<li class="">
								<div class="yndirectory-cover-image"  style="background-image: url({img return_url=true server_id=$aPhoto.server_id path='core.url_pic' file='yndirectory/'.$aPhoto.image_path suffix='' width=520 height=320})"></div>
							</li>
			        	{/foreach}
			        </ul>		    
				</div>
				{if isset($sCurrentTheme) && $sCurrentTheme == 'nebula'}
					{literal}
						<style type="text/css">
							div#left{
								top: 255px;
							}
						</style>
					{/literal}
				{/if}
			{else}
				{if isset($sCurrentTheme) && $sCurrentTheme == 'nebula'}
					{literal}
						<style type="text/css">
							div#left{
								top: 45px;
							}
						</style>
					{/literal}
				{/if}
			{/if}

			<div class="yndirectory-detailcover-footer">
				<div class="business-detailcover-rating-review">
					<div>
				    	{$aBusiness.total_score_text}  
				    </div>
				</div>	

				<div class="yndirectory-review">
					<i class="fa fa-pencil-square-o"></i>
					<span>{$iReview}</span><span>{phrase var='review_a_s'}</span>
				</div>

				<div class="yndirectory-follower">
					<i class="fa fa-arrow-right"></i>
					<span>{$iFollower}</span><span>{phrase var='follower_a_s'}</span>
				</div>

				<div class="yndirectory-member">
					<i class="fa fa-group"></i>
					<span>{$iMember}</span><span>{phrase var='member_b_s'}</span>
				</div>
			</div>
		</div>

		{literal}
		<script type="text/javascript" >
			(function(){
				var _stage = '#js_block_border_directory_detailcover #yndirectory_detail_cover',
				_options = {
					controlNav: false,
        			prevText: "",
					nextText: "", 
				},
				_required = function(){
					return !/undefined/i.test(typeof jQuery.flexslider);
				},
				_initFeaturedSlideshow_flag = false,
				initFeaturedSlideshow = function ()
				{
					var stage =  $(_stage);
					if(!stage.length) return;
					if(_initFeaturedSlideshow_flag) return;
					if(!_required()) return;
					_initFeaturedSlideshow_flag = true;
					$(_stage).flexslider(_options);
				},
				initNextPrev = function()
				{
					$('.flex-prev').on('click', function(){
					    $('.flexslider').flexslider('prev')
					    return false;
					})
					$('.flex-next').on('click', function(){
					    $('.flexslider').flexslider('next')
					    return false;
					})
				}
		
				$Behavior.yndirectoryCoverSlideshow = function() {
					if(!$(_stage).length) return;
					function checkCondition(){
						var stage =  $(_stage);
						if(!stage.length) return;
						if(_initFeaturedSlideshow_flag) return;
		
						if(!_required()){
							window.setTimeout(checkCondition, 400);
						}else{
							initFeaturedSlideshow();
						}
					}
					window.setTimeout(checkCondition, 400);
					
					function checkButtonNextPrev()
					{
						if (!$('.flex-prev').length || !$('.flex-next').length)
						{
							window.setTimeout(checkButtonNextPrev, 400);
						}
						else
						{
							initNextPrev();
						}
					}
					window.setTimeout(checkButtonNextPrev, 400);
				}	
			})();
		</script>
		{/literal}

	{elseif $aBusiness.theme_id == 2}

		{if isset($sCurrentTheme) && $sCurrentTheme == 'nebula'}
			{literal}
				<style type="text/css">
					div#left{
						top: 305px;
					}
				</style>
			{/literal}
		{/if}

		<div class="yndirectory-detailcover yndirectory-detailcover-style2">
			<input type="hidden" id="yndirectory-api-key" value="{$apiKey}" />
			<div class="yndirectory-detailcover-maps">
				<div id="yndirectory_cover_maps"></div>
			</div>

			<div class="yndirectory-detailcover-avatar-group">
				<a class="yndirectory-detailcover-avatar" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" title="{$aBusiness.name|clean}">
				    {img yndirectory_overridenoimage=true server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_400_square'}
				</a>
					
				<div class="yndirectory-detailcover-footer">
					<div class="business-detailcover-rating-review">
						<div>
					    	{$aBusiness.total_score_text}  
					    </div>
					</div>	

					<div class="yndirectory-review">
						<i class="fa fa-pencil-square-o"></i>
						<span>{phrase var='review_a_s'}</span>
						<span>{$iReview}</span>
					</div>

					<div class="yndirectory-follower">
						<i class="fa fa-arrow-right"></i>
						<span>{phrase var='follower_a_s'}</span>
						<span>{$iFollower}</span>
					</div>

					<div class="yndirectory-member">
						<i class="fa fa-group"></i>
						<span>{phrase var='member_b_s'}</span>
						<span>{$iMember}</span>
					</div>
				</div>
			</div>

			{if count($aCoverPhotos)}
			<div class="yndirectory-detailcover-slider yndirectory-slider-mini">
				<div class="yndirectory-detailcover-slider-mini">
					<div class="yndirectory-photo-span" style="background-image: url({img return_url=true server_id=$aCoverPhotos.0.server_id path='core.url_pic' file='yndirectory/'.$aCoverPhotos.0.image_path suffix='' width=520 height=320})"></div>
				</div>

				<div id="yndirectory_detail_cover" class="flexslider">
					<div class="yndirectory-detailcover-slider-close-btn">
						<i class="fa fa-times"></i>
					</div>				
					<ul class="slides">
				    	{foreach from=$aCoverPhotos item=aPhoto name=aPhoto}
				    		<li class="">
								<div class="yndirectory-cover-image"  style="background-image: url({img return_url=true server_id=$aPhoto.server_id path='core.url_pic' file='yndirectory/'.$aPhoto.image_path suffix='' width=520 height=320})"></div>
							</li>
			        	{/foreach}
			        </ul>			    
				</div>
			</div>
			{/if
	}		
		</div>


		{literal}
		<script type="text/javascript">
			(function(){
				var _stage = '#js_block_border_directory_detailcover #yndirectory_detail_cover',
				_debug = true,
				_options = {
					controlNav: false,
        			prevText: "",
					nextText: "", 
				},
				_required = function(){
					return !/undefined/i.test(typeof jQuery.flexslider);
				},
				_initFeaturedSlideshow_flag = false,
				initFeaturedSlideshow = function ()
				{
					var stage =  $(_stage);
					if(!stage.length) return;
					if(_initFeaturedSlideshow_flag) return;
					if(!_required()) return;
					_initFeaturedSlideshow_flag = true;
					$(_stage).flexslider(_options);	
				},
				initSildeOpenClose = function ()
				{
					if ( $('.yndirectory-detailcover-slider').length ) {
						$('.yndirectory-detailcover-slider-close-btn').on('click', function(){
							$('.yndirectory-detailcover-slider').addClass('yndirectory-slider-mini');
						});
	
						$('.yndirectory-detailcover-slider-mini').on('click', function(){
							$('.yndirectory-detailcover-slider').removeClass('yndirectory-slider-mini');
						});	
					}
				},
				initNextPrev = function()
				{
					$('.flex-prev').on('click', function(){
					    $('.flexslider').flexslider('prev')
					    return false;
					})
					$('.flex-next').on('click', function(){
					    $('.flexslider').flexslider('next')
					    return false;
					})
				}
		
				$Behavior.yndirectoryCoverSlideshow = function() {
					if(!$(_stage).length) return;
					function checkCondition(){
						var stage =  $(_stage);
						if(!stage.length) return;
						if(_initFeaturedSlideshow_flag) return;
		
						if(!_required()){
							window.setTimeout(checkCondition, 400);
						}else{
							initFeaturedSlideshow();
						}
					}
					window.setTimeout(checkCondition, 400);
					
					function checkSlideOpenClose()
					{
						if (!$('.yndirectory-detailcover-slider').length || !$('.yndirectory-detailcover-slider-close-btn').length || !$('.yndirectory-detailcover-slider-mini').length)
						{
							window.setTimeout(checkSlideOpenClose, 400);
						}
						else
						{
							initSildeOpenClose();
						}
					}
					window.setTimeout(checkSlideOpenClose, 400);
					
					function checkButtonNextPrev()
					{
						if (!$('.flex-prev').length || !$('.flex-next').length)
						{
							window.setTimeout(checkButtonNextPrev, 400);
						}
						else
						{
							initNextPrev();
						}
					}
					window.setTimeout(checkButtonNextPrev, 400);
				}	
			})();
			$Behavior.yndirectoryMap = function() {
				yndirectory.loadAjaxMapStaticImage($('#yndirectory_detail_business_id').val());
			}
		</script>
		{/literal}

	{/if}
{/if}

{if isset($sCurrentTheme) && $sCurrentTheme == 'nebula'}
	{literal}
		<style type="text/css">
			#content_load_data{
				min-height: 0px;
			}
		</style>
	{/literal}
{/if}
