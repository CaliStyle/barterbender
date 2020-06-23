{if $aSaCampaigns}
{if $isAdminPanel}
<div class="panel panel-default">
{/if}
	<table class="{if $isAdminPanel}table table-bordered{/if} ynsaTable" cellpadding="1" cellspacing="0">
		<thead>
            <tr>
                <th class="first w20"></th>
                <th class="second w200">{_p var='name'}</th>
                <th class="w140">{_p var='ad'}</th>
                <th>{_p var='status'}</th>
                <th class="w200">{_p var='impressions'}</th>
                <th>{_p var='clicks'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aSaCampaigns name=acampaign item=aCampaign}
                <tr{if is_int($phpfox.iteration.acampaign/2)} class="on"{/if}>
                    <td class="t_center ynsaFirstColumn w20">
                        {template file='socialad.block.campaign.action'}
                    </td>
                    <td class="w200" title="{_p var='name'}">
                        {if $isAdminPanel}
                             <a href="{url link='admincp.socialad.campaign.detail' id=$aCampaign.campaign_id}"> {$aCampaign.campaign_name|clean|shorten:40:'...'} </a>
                        {else}
                            <a href="{url link='socialad.campaign.detail' id=$aCampaign.campaign_id}"> {$aCampaign.campaign_name|clean|shorten:40:'...'} </a>
                        {/if}
                    </td>
                    <td title="{_p var='ad'}" class="t_center w140">{$aCampaign.total_ad}</td>
                    <td title="{_p var='status'}" class="t_center">{$aCampaign.status_phrase}</td>
                    <td title="{_p var='impressions'}" class="t_center w200">{$aCampaign.campaign_total_impression}</td>
                    <td title="{_p var='clicks'}" class="t_center ynsaLastColumn">{$aCampaign.campaign_total_click}</td>
                </tr>
            {/foreach}
        </tbody>
	</table>
{if $isAdminPanel}
</div>
{/if}
{else}
<div class="extra_info">
	{_p var='no_campaigns_found'}
</div>
{/if}
<div class="clear"></div>
{if isset($bSaIsNoPaging) && $bSaIsNoPaging}

{else}
	<div class="pager_ads">
	{module name='socialad.paging'}
	</div>
{/if}
