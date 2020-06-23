{if $aItem.canDoAction || (isset($sView) && ($sView == 'history'))}
<div class="dropdown clearfix {if isset($bIsDetailView) && $bIsDetailView}item_bar_action_holder{/if}">
    <a role="button" data-toggle="dropdown" class="p-option-button">
        <i class="ico ico-gear-o"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        {if $aItem.canApprove}
            <li role="presentation">
                <a href="" data-toggle="ultimatevideo" data-cmd="approve_video" data-id="{$aItem.video_id}">
                    <i class="fa fa-check-square"></i>
                    {_p('Approve')}
                </a>
            </li>
        {/if}
        {if $aItem.canSponsor || $aItem.canPurchaseSponsor}
            <li role="presentation" id="js_unsponsor_{$aItem.video_id}" class="{if !$aItem.is_sponsor}hide{/if}">
                <a href="javascript:void(0);" data-toggle="ultimatevideo" data-cmd="sponsor_video" data-id="{$aItem.video_id}" data-value="0">
                    <span class="ico ico-sponsor mr-1"></span>
                    {_p var='unsponsor'}
                </a>
            </li>
            {if $aItem.canSponsor}
            <li role="presentation" id="js_sponsor_{$aItem.video_id}" class="{if $aItem.is_sponsor}hide{/if}">
                <a href="javascript:void(0);" data-toggle="ultimatevideo" data-cmd="sponsor_video" data-id="{$aItem.video_id}" data-value="1">
                    <span class="ico ico-sponsor mr-1"></span>
                    {_p var='sponsor'}
                </a>
            </li>
            {else if $aItem.canPurchaseSponsor}
            <li role="presentation" id="js_sponsor_{$aItem.video_id}" class="{if $aItem.is_sponsor}hide{/if}">
                <a href="{permalink module='ad.sponsor' id=$aItem.video_id}section_ultimatevideo_video/">
                    <span class="ico ico-sponsor mr-1"></span>
                    {_p var='sponsor'}
                </a>
            </li>
            {/if}
        {/if}
        {if $aItem.canSponsorInFeed}
            {if $aItem.sponsorInFeedId}
            <li role="presentation">
                <a title="{_p var='sponsor_in_feed'}" href="{url link='ad.sponsor' where='feed' section='ultimatevideo_video' item=$aItem.video_id}">
                    <span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_in_feed'}
                </a>
            </li>
            {else}
            <li role="presentation">
                <a title="{_p var='unsponsor_in_feed'}" href="javascript:void(0)" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=ultimatevideo_video&item_id={$aItem.video_id}', 'GET'); return false;">
                    <span class="ico ico-sponsor mr-1"></span>{_p var="unsponsor_in_feed"}
                </a>
            </li>
            {/if}
        {/if}
        {if $aItem.canFeature}
            {if $aItem.is_featured == 0 && isset($bIsPagesView) && !$bIsPagesView }
                <li role="presentation">
                    <a href="" data-toggle="ultimatevideo" data-cmd="featured_video" data-id="{$aItem.video_id}"
                       class="ynuv_feature_video_{$aItem.video_id}">
                        <i class="fa fa-diamond"></i>
                        {_p('Feature')}</a>
                </li>
            {elseif $aItem.is_featured == 1 && isset($bIsPagesView) && !$bIsPagesView}
                <li role="presentation">
                    <a href="" data-toggle="ultimatevideo" data-cmd="unfeatured_video" data-id="{$aItem.video_id}"
                       class="ynuv_feature_video_{$aItem.video_id}">
                        <i class="fa fa-diamond"></i>
                        {_p('Un-Feature')}</a>
                </li>
            {/if}
        {/if}
        {if $aItem.canEdit}
            <li role="presentation">
                <a href="{url link='ultimatevideo.add' id=$aItem.video_id}">
                    <i class="fa fa-pencil"></i>
                    {_p('edit_video')}</a>
            </li>
        {/if}
        {if $aItem.canDelete}
            <li role="presentation" class="item_delete">
                <a href="" href="" class="no_ajax_link" data-toggle="ultimatevideo" data-cmd="delete_video"
                   data-id="{$aItem.video_id}" data-confirm="{_p('are_you_sure_want_to_delete_this_video')}"
                   {if isset($bIsDetailView) && $bIsDetailView}data-detail="true" {else}data-detail="false"{/if}>
                    <i class="fa fa-trash"></i>
                    {_p('delete_video')}</a>
            </li>
        {/if}
        {if isset($sView) && ($sView == 'history')}
            <li role="presentation" class="item_delete">
                <a href="" class="no_ajax_link" data-toggle="ultimatevideo" data-cmd="delete_video_history"
                   data-id="{$aItem.video_id}">
                    <i class="fa fa-times"></i>
                    {_p('remove_from_history')}</a>
            </li>
        {/if}
    </ul>
</div>
{/if}