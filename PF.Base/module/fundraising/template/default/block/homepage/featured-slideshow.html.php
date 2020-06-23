<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<script type="text/javascript">
{literal}
$Behavior.ynfHomepageSlide = function(){
	$("#ynfr_sliders").owlCarousel({
 
      navigation : true, // Show next and prev buttons
      slideSpeed : 300,
      paginationSpeed : 400,
      singleItem:true,
      autoPlay: true,
  	});
}
{/literal}
</script>

<div id="ynfr_sliders" class="owl-carousel owl-theme dont-unbind-children">
	{foreach from=$aFeaturedCampaigns item=aCampaign name=fundraising}
	<div class="item ynfr-feature-item">
		<div class="ynfr-bg-img">
			<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" title="{$aCampaign.title|clean}">
				{if $aCampaign.image_path}
				<span style="background-image:url({img return_url=true server_id=$aCampaign.server_id path='core.url_pic' file=$aCampaign.image_path suffix='_600' max-width=600})">			
					{*img server_id=$aCampaign.server_id path='core.url_pic' file=$aCampaign.image_path suffix='_120' max_width='120' max_height='120' class='js_mp_fix_width'}
				</span>
				{else}
				<span class="no_image_campaign">
					{img server_id=$aCampaign.server_id path='core.url_pic' file=$aCampaign.image_path suffix='_120' max_width='120' max_height='120' class='js_mp_fix_width'}
				</span>
				{/if}
			</a>
		</div>

		<div class="ynfr-feature-info">
			<div id="js_fundraising_edit_title{$aCampaign.campaign_id}" class="ynfr-title">
				<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" id="js_fundraising_edit_inner_title{$aCampaign.campaign_id}" class="link ajax_link">{$aCampaign.title|clean|shorten:40:'...'|split:20}</a>
			</div>
			<p>{phrase var='created_by'} <a href="javascript:void(0)">{$aCampaign|user}</a></p>
			<p><span class="total_sign">{$aCampaign.total_donor}</span>{phrase var='total_donor' total_donor=''} - {phrase var='total_like_liked' total_like=$aCampaign.total_like} - {phrase var='total_view_viewed' total_view=$aCampaign.total_view}</p>
			<div class="ync-discount">{$aCampaign.financial_goal_text} {phrase var='goal_upper'}</div>
			<p class="ynfr-short-des item_view_content">{$aCampaign.short_description|clean|shorten:160:'...'|split:30}</p>
		</div>

		<div class="ynfr-feature-donated">
			<div class="extra_info">
				<p class="ynfr-m"><span>{$aCampaign.total_amount_text} {phrase var='raised_upper'}</span><span>{$aCampaign.financial_goal_text} {phrase var='goal_upper'}</span></p>
                {if $aCampaign.financial_goal}
                    <div class="meter-wrap-l">
                        <div class="meter-wrap-r">
                            <div class="meter-wrap">
                                <div class="meter-value" style="width: {$aCampaign.financial_percent}"></div>
                                <div class="meter-percent">{$aCampaign.financial_percent}</div>
                            </div>
                        </div>
                    </div>
                {/if}
				<p class="ynfr-remain">
					{if isset($aCampaign.remain_time)}
						{$aCampaign.remain_time}
					{/if}
				</p>
			</div>
			<div class="ynfr-donor">		
				<p class="ynfr-thankyou-donor"><span>{phrase var='thankyou_donors'}</span></p>
				{if $aCampaign.donor_list}
					{foreach from=$aCampaign.donor_list item=aUser}
						{module name='fundraising.campaign.user-image-entry'}
					{/foreach}
				{else}
				<div class="ynfr-be-the-first-phrase">
					<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" > {phrase var='be_the_first_donor_of_this_campaign'} </a>
				</div>
				{/if}

			</div>  
		</div>

	</div>
	{/foreach}
</div>
<div class="clear"></div>