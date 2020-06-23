<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynfr_gallery_slides-block">
	<div id="ynfr_gallery_slides" class="owl-carousel owl-theme">
		
		{if $aGalleryVideo}
		<div class="item" >
			<div class="wrapper">
				<iframe width="560" height="325" src="{$aGalleryVideo.video_url}" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>	
		{/if}

		{foreach from=$aGalleryImages item=aImage} 
		<div class="item" >
			<div class="wrapper ynfr_gallery_item js_fundraising_click_image" href="{img return_url=true server_id=$aImage.server_id path='core.url_pic' file=$aImage.image_path suffix=''}" style="background-image:url({img return_url=true server_id=$aImage.server_id path='core.url_pic' file=$aImage.image_path suffix=''})"> </div>
		</div>
		{/foreach}
        {if !($aGalleryVideo || $aGalleryImages)}
        <div class="item" >
            <div class="wrapper ynfr_gallery_item" href="" style="background-image:url({$corepath}module/fundraising/static/image/noimage_big.png)"> </div>
        </div>
        {/if}

	</div>
	<div class="custom_owl-buttons" id="custom_owl-buttons">
		<div class="custom_owl-prev">prev</div>
		<div class="custom_owl-next">next</div>
	</div>
</div>

<div class="clear"></div>
<script type="text/javascript">
{literal}
	function initynfrGallerySlider(){
		$("#ynfr_gallery_slides").owlCarousel({
			navigation : false, // Show next and prev buttons
			slideSpeed : 300,
			paginationSpeed : 400,
			singleItem: true,
			mouseDrag: false,
			autoPlay: false,
	  	});
	  	if($('.owl-item').length < 2)
	  	{
	  		$('#custom_owl-buttons').hide();
	  	}
	  	else
	  	{
		  	var owl = $('#ynfr_gallery_slides').data('owlCarousel');
		  	if($('.custom_owl-next').length)
		  	{
		  		$('.custom_owl-next').click(function(event){
			    	owl.next();
			  	});
		  	}
		  	if($('.custom_owl-prev').length)
		  	{
		  		$('.custom_owl-prev').click(function(event){
			    	owl.prev();
			  	});
		  	}
	   }

	}
	$Behavior.ynfrDetailSlider = function(){
		var ynfrGallerySlideInterval,
			isynfrInitSlide = false;
		if(typeof $.fn.owlCarousel == 'undefined'){

			ynfrGallerySlideInterval = window.setInterval(function(){
					if(isynfrInitSlide == true) return;
					initynfrGallerySlider();
					isynfrInitSlide = true;
					window.clearInterval(ynfrGallerySlideInterval);
			},250);
		}
		else{

			setTimeout(function(){
				if(isynfrInitSlide == true) return;
				initynfrGallerySlider();
				isynfrInitSlide = true;
			},250)
		}	
		
	}	
{/literal}
</script>
{literal}
<script type="text/javascript">  
   $Behavior.FundraisingShowProfileImage = function(){
         $('.js_fundraising_click_image').click(function(){
               var oNewImage = new Image();
               oNewImage.onload = function(){
                     $('#js_marketplace_click_image_viewer').show();
                     $('#js_marketplace_click_image_viewer_inner').html('<img src="' + this.src + '"  alt="" />');
                     $('#js_marketplace_click_image_viewer_close').show();
               };
               oNewImage.src = $(this).attr('href');
               
               return false;
         });
         
         $('#js_marketplace_click_image_viewer_close a').click(function(){
               $('#js_marketplace_click_image_viewer').hide();
               return false;
         });
   }
</script>
{/literal}
<div id="js_marketplace_click_image_viewer">
	<div id="js_marketplace_click_image_viewer_inner">
		{phrase var='loading'}
	</div>
	<div id="js_marketplace_click_image_viewer_close">
		<a href="#">{phrase var='close'}</a>
	</div>
</div>