<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script>
	$Behavior.ynloadFeaturedPetition = function(){
		$("#yns_petition_carousel").owlCarousel({
		navigation : true,
		slideSpeed : 300,
		paginationSpeed : 500,
		singleItem : true,
		pagination : false,
		autoPlay : true
		});
		$('.owl-buttons').addClass('dont-unbind');
		$('.owl-buttons .owl-prev').addClass('dont-unbind');
		$('.owl-buttons .owl-prev').addClass('dont-unbind');

	}
</script>
{/literal}
<div class="yns_petition_carousel_title">{phrase var='petition.featured_petitions'}</div>
<div class="owl-carousel yns_petition_carousel" id="yns_petition_carousel">
	{foreach from=$aFeatured item=aPetition name=Featured}
	<div class="item">
			<div class="pet_img_tit">
				<a target="_self" href="{permalink module='petition' id=$aPetition.petition_id title=$aPetition.title}" class="carousel_link" title="{$aPetition.title|clean}">
					{if (!empty($aPetition.image_path)) }
						{img server_id=$aPetition.server_id path='core.url_pic' file=$aPetition.image_path suffix='_500' max_width=600 class='photo_holder'}
					{else}
						<img src="{$corepath}module/petition/static/image/no_photo.png" />
					{/if}
				</a>
			</div>
			<div class="row_title_info">
					<a class="featuredpetition_title" target="_self" href="{permalink module='petition' id=$aPetition.petition_id title=$aPetition.title}" title="{$aPetition.title|clean}">{$aPetition.title|clean|shorten:50:'...'|split:20}</a>
					<div class="extra_info">{phrase var='petition.created_by'} {$aPetition|user} {if isset($aDirect.category)}{phrase var='petition.in'} <a href="{$aPetition.category.link}">{$aPetition.category.name}</a>{/if}
							</br> {if $aPetition.is_directsign == 1}
							<span class="total_sign">{$aPetition.total_sign}</span>
							<i class="fa fa-pencil"></i> 
							{else}
								<i class="fa fa-pencil"></i> {$aPetition.total_sign}
							{/if} 
							&nbsp; 
							<i class="fa fa-thumbs-up"></i> {$aPetition.total_like}
							&nbsp; 
							<i class="fa fa-eye"></i> {$aPetition.total_view}
					</div>
					<div class="item_content item_view_content">
						{$aPetition.short_description|clean|shorten:140:'...'}
					</div>
					<div class="clear"></div>
			</div>
	</div>
	{/foreach}
</div>
