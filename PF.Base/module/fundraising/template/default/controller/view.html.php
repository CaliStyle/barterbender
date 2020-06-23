<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{literal}
<style type="text/css">
	.item_tag{
		display: none !important;
	}
	.item_tag_holder{
		border-top: none !important;
		margin-top: 0 !important;
		padding-top: 0 !important;
	}
</style>
{/literal}

<div class="ynfr_owner_category">
	<span class="ynfr_detail_owner">
			{phrase var='created_by'}
		<a href="{url link=''$aCampaign.user_name''}">
			{$aCampaign.full_name|shorten:20:'...'|split:20}
		</a>
	</span>

	{if $aLastCategory}
		<span>.</span>
		<span class="ynfr_detail_category">
			{phrase var='category'} <a href="{$aLastCategory[1]}"> {$aLastCategory[0]} </a> </p>
		</span>
	{/if}
</div>

<div class="item_view">	

	{if $aCampaign.is_approved != 1 && !$aCampaign.is_draft}
        {template file='core.block.pending-item-action'}
	{/if}
	<div class="item_bar">
		<div class="item_bar_action_holder">
			{if $aCampaign.having_action_button}
				<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{phrase var='actions'}</span><i class="ico ico-gear-o"></i></a>
				<ul class="dropdown-menu dropdown-menu-right">
					{template file='fundraising.block.link'}
				</ul>
			{/if}
		</div>		
	</div>
    {module name='fundraising.campaign.gallery' iCampaignId=$aCampaign.campaign_id}
    {module name='fundraising.campaign.side-campaign-goal' id=$aCampaign.campaign_id}
    {module name='fundraising.campaign.side-add-this' id=$aCampaign.campaign_id}
    {module name='fundraising.detail' sType=description id=$aCampaign.campaign_id}
	{plugin call='fundraising.template_controller_view_end'}
	<div id="fundraising_comment_block">
		<div {if $aCampaign.is_approved != 1}style="display:none;" class="js_moderation_on"{/if}>		
			{module name='feed.comment'}
		</div>
	</div>
</div>
<script type="text/javascript">
    $Behavior.setupInviteLayout = function() {l}
    $("#js_friend_search_content").append('<div class="clear" style="padding:5px 0px 10px 0px;"><input type="button" onclick="ClickAll($(this));" value="{phrase var="fundraising.select_all"}" /> </div>');
    $("#js_friend_search_content").parent().parent().css('height','');
    {r}

</script>
<script type="text/javascript">
var loadMap = false;
{literal}
var googleApiKey={/literal}"{$googleApiKey}"{literal};
 $Behavior.ynfrInitializeGoogleMapLocation = function() {
    if (loadMap === false) {
        loadMap = true; 
        $('#js_country_child_id_value').change(function(){
            debug("Cleaning  city, postal_code and address");
            $('#city').val('');
            $('#postal_code').val('');
            $('#address').val('');
        });
		if(typeof(inputToMap) !== 'undefined') {
			$('#country_iso, #js_country_child_id_value').change(inputToMap);
			$('#location_venue, #address, #postal_code, #city').blur(inputToMap);
		}
        loadScript();
    }
  };

{/literal}
</script> 