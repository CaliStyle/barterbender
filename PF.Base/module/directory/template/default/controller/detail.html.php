{literal}
<style>
	.feed_sort_order{
		display: none !important;
	}
	#js_main_feed_holder{
		display: none;
	}
	#js_feed_content{
		display: none;
	}
	#js_block_border_feed_display .title{
		display: none;
	}
	.ym-feed-header{
		display: none;
	}
</style>
{/literal}

<div id="yndirectory_detail" class="ync-detail main_break yndirectory_detail_theme_{$aYnDirectoryDetail.aBusiness.theme_id}">
    <div class="business-icon-sticky">
        {if isset($aBusiness.featured) && $aBusiness.featured }
        <div class="sticky-label-icon sticky-featured-icon">
            <span class="flag-style-arrow"></span>
            <i class="ico ico-diamond"></i>
        </div>
        {/if}
        {if $sView == "mybusinesses" }
            {if $aBusiness.business_status == 3}
            <div class="sticky-label-icon sticky-pending-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-clock-o"></i>
            </div>
            {/if}
        {/if}
    </div>
    <div class="item-view ">

        {if $aBusiness.theme_id == 1}
        {if count($aCoverPhotos)}
        <div id="yndirectory-featured" class="yndirectory-featured dont-unbind-children owl-carousel owl-theme">
            {foreach from=$aCoverPhotos item=aPhoto name=aPhoto}
            <div class="item">
                <div class="yndirectory-featured__item">
                <div class="yndirectory-featured__photo"  style="background-image: url(
                {if $aPhoto.image_path}
                    {img return_url=true server_id=$aPhoto.server_id path='core.url_pic' file='yndirectory/'.$aPhoto.image_path suffix=''}
                {/if}
                )"></div>
                </div>
            </div>
            {/foreach}
        </div>
        {else}
             <div id="yndirectory-featured" class="yndirectory-featured dont-unbind-children owl-carousel owl-theme">
                <div class="item">
                    <div class="yndirectory-featured__item">
                    <div class="yndirectory-featured__photo"  style="background-image: url(
                    {if $aBusiness.default_cover}
                        {$aBusiness.cover_photo}
                    {/if}
                    )"></div>
                    </div>
                </div>
            </div>
        {/if}

        <div class="ync-detail-info theme-1">
            <div class="yndirectory-featured__info mt-2 ">
                <div class="yndirectory-featured__inner pr-1">
                    <div class="yndirectory-featured__media mr-2">
                        {if isset($aBusiness.logo_path)}
                            {img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix=''}
                        {else}
                            <img title="{$aBusiness.name}" src="{$aBusiness.default_logo_path}"/>
                        {/if}
                    </div>
                    <div class="yndirectory-featured__body">
                        <h2 class="fw-bold mt-0	mb-0 yndirectory-featured__title"><a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}" id="js_business_edit_inner_title{$aYnDirectoryDetail.aBusiness.business_id}">{$aYnDirectoryDetail.aBusiness.name|clean}</a></h2>

                        <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews" class="ync-outer-rating ync-rating-md ync-outer-rating-row full">
                            {if $aYnDirectoryDetail.aBusiness.total_rating != 0}
                            <div class="ync-outer-rating-row">
                                <div class="ync-rating-star">
                                    {for $i = 0; $i < 5; $i++}
                                    {if $i < (int)$aBusiness.total_score}
                                    <i class="ico ico-star" aria-hidden="true"></i>
                                    {elseif ((round($aBusiness.total_score) - $aBusiness.total_score) > 0) && ($aBusiness.total_score - $i) > 0}
                                    <i class="ico ico-star half-star" aria-hidden="true"></i>
                                    {else}
                                    <i class="ico ico-star disable" aria-hidden="true"></i>
                                    {/if}
                                    {/for}
                                </div>
                            </div>
                            <span class="ync-rating-count-review">
                                <span class="item-number">{$aBusiness.total_review}</span>
                                {if $aBusiness.total_review > 1 }
                                    <span class="item-text">{_p var = 'directory.reviews'}</span>
                                {else}
                                    <span class="item-text">{_p var = 'directory.review'}</span>
                                {/if}
                            </span>
                            {/if}
                            <span class="yndirectory-detail-rating-action">
                                {if $aBusiness.bCanRateBusiness && $aBusiness.is_pending_claiming != 1}
                                <div class="business-rating-action">
                                {if $aBusiness.total_rating == 0}
                                    <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews"><span class="only-text">{phrase var='no_reviews_be_the_first'}</span></a>
                                {else}
                                     &nbsp;<a class="item-write-review" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews"><i class="ico ico-textedit"></i><span>{phrase var='write_a_review'}</span></a>
                                {/if}
                                </div>
                                {/if}
                            </span>
                        </a>
                        {if count($aBusinesLocations)>0}
                        <div class="yndirectory-featured__address">
                            {if count($aBusinesLocations)<2}
                                {foreach from=$aBusinesLocations item=aItem}
                                    <p>{if !empty($aItem.location_address)}{$aItem.location_address}{/if}</p>
                                {/foreach}
                            {else}
                                {foreach from=$aBusinesLocations item=aItem}
                                    <p class="mb-h1">{if !empty($aItem.location_title)}{$aItem.location_title} - {/if}{if !empty($aItem.location_address)}{$aItem.location_address}{/if}</p>
                                {/foreach}
                            {/if}
                        </div>
                        {/if}
                        <div class="yndirectory-detail-action-btn-group">
                            {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                            {if $isFollowedBusiness}
                            <button type="button" class="btn btn-default btn-icon btn-sm" onclick="$.ajaxCall('directory.deleteFollow', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-check"></i><span>{phrase var='following'}</span></button>
                            {else}
                            <button type="button" class="btn btn-primary btn-icon btn-sm" onclick="$.ajaxCall('directory.addFollow', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-check"></i><span>{phrase var='follow'}</span></button>
                            {/if}
                            {/if}

                            {if Phpfox::isUser() && $aBusiness.type != 'claiming' && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                            {if $isLiked}
                            <button type="button" class="btn btn-default btn-icon btn-sm" onclick="$(this).parent().hide();$.ajaxCall('directory.deleteLike', 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-thumbup"></i><span>{_p var = 'liked'}</span></button>
                            {else}
                            <button type="button" class="btn btn-primary btn-icon btn-sm" onclick="$(this).parent().hide();$.ajaxCall('directory.addLike', 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-thumbup-o"></i><span>{_p var = 'like'}</span></button>
                            {/if}
                            {/if}

                            {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && PhpFox::getUserId() != $aBusiness.user_id}
                                <a href="javascript:void()" class="btn btn-default btn-sm" onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id}{r}); return false;">{_p var='message'}</a>
                            {/if}

                            <div class="dropdown dropdown-overflow">
                                <a class="btn btn-sm btn-default" data-toggle="dropdown" role="button"><i class="ico ico-dottedmore-o"></i></a>
                                <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aBusiness.business_id}">
                                    <li>
                                        <a id="yndirectory_detail_download" style="cursor: pointer;" onclick="yndirectory.downloadBusinessDetail(this, {$aBusiness.business_id});"><i title="{_p var ='directory.download_pdf'}" class="fa fa-download"></i>{_p var='download'}</a>
                                    </li>
                                    <li>
                                        <a id="yndirectory_detail_print" class=" no_ajax_link" target="_blank" href="{url link='directory.printbusiness.'.$aYnDirectoryDetail.aBusiness.business_id}"><i title="{_p var = 'directory.print'}" class="fa fa-print"></i>{_p var='print'}</a>
                                    </li>
                                    <li class="responsive-compose-msg" style="display: none;">
                                        <a href="javascript:void()"  onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id}{r}); return false;"><span class="ico ico-comment-o"></span>{_p var='message'}</a>
                                    </li>
                                    <li>
                                        <span id="yndirectory_detail_hidden" style="display: none;"><a href="{$aYnDirectoryDetail.sDownloadBusinessUrl}" class="no_ajax_link"></a></span>
                                    </li>
                                    {if $aBusiness.type != 'claiming'}
                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                                    <li>
                                        <a id="yndirectory_detailcheckinlist_comparebutton" href="javascript:void(0)" onclick="yndirectory.click_yndirectory_detailcheckinlist_comparebutton(this, {$aBusiness.business_id}); return false;"><i class="fa fa-files-o"></i> {phrase var='add_to_compare'}</a>
                                        <div style="display: none;">
                                            <input type="checkbox"
                                                data-compareitembusinessid="{$aBusiness.business_id}"
                                                data-compareitemname="{$aBusiness.name}"
                                                data-compareitemlink="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}"
                                                data-compareitemlogopath="{if isset($aBusiness.logo_path)}{img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_100' return_url=true}{else}
                                                {img server_id=$aBusiness.server_id path='' file=$aBusiness.default_logo_path suffix='' return_url=true}{/if}"
                                                onclick="yndirectory.clickCompareCheckbox(this);"
                                                class="yndirectory-compare-checkbox"> {phrase var='add_to_compare'}
                                        </div>
                                    </li>
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && $aBusiness.setting_support.allow_users_to_share_business}
                                    <li>
                                        <a href="javascript:void(0)" onclick="tb_show('{phrase var='share'}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=feed&amp;url={$aBusiness.linkBusiness}&amp;title={$aBusiness.name}&amp;feed_id={$aBusiness.business_id}&amp;is_feed_view=1&amp;sharemodule=directory')); return false;"><i class="fa fa-share"></i> {phrase var='share'}</a>
                                    </li>
                                    {/if}

                                    {if Phpfox::isUser() &&  ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                                        {if $isFavoriteBusiness}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.deleteFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="fa fa-bookmark"></i> {phrase var='unfavorite'}</a></li>
                                        {else}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.addFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="fa fa-bookmark"></i> {phrase var='favorite'}</a></li>
                                        {/if}
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                                        <li><a href="javascript:void(0)" onclick="yndirectory.click_detailcheckinlist_promotebusiness(this, {$aBusiness.business_id}); return false;"><i class="fa fa-bullhorn"></i> {phrase var='promote_business'}</a></li>
                                    <li><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type=directory&amp;id={$aBusiness.business_id}" class="inlinePopup activity_feed_report" title="{phrase var='Report this business'}"><i class="fa fa-bolt"></i> {phrase var='report_business'}</a></li>
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && PhpFox::getUserId() != $aBusiness.user_id && $aBusiness.setting_support.allow_users_to_confirm_working_at_the_business}
                                        {if !$aBusiness.isMember}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.addUserMemberRole', 'item_id={$aBusiness.business_id}'); return false;" class="yndirectory-detailcheckinlist-active"><i class="fa fa-briefcase"></i> {phrase var='working_here'}</a></li>
                                        {else}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.leaveBusiness', 'item_id={$aBusiness.business_id}'); return false;" class="yndirectory-detailcheckinlist-active"><i class="fa fa-briefcase"></i> {phrase var='leave_this_business'}</a></li>
                                        {/if}
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && PhpFox::getUserId() != $aBusiness.user_id}
                                        <li><a href="javascript:void(0)" onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id} {r}); return false;"><i class="fa fa-envelope"></i> {phrase var='message_owner'}</a></li>
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && $canInviteMember && $aBusiness.setting_support.allow_users_to_invite_friends_to_business}
                                        <li><a href="javascript:void(0)" onclick="$Core.box('directory.inviteBlock',800,'id={$aBusiness.business_id}&url={$aBusiness.linkBusiness}');"><i class="fa fa-chevron-circle-right"></i> {phrase var='invite_member'}</a></li>
                                    {/if}
                                    {/if}
                                </ul>
                            </div>
                        </div>
                        <div class="yndirectory-statistic-checkin-like">
                            {if $aBusiness.total_checkin > 0}
                            <a style="cursor:pointer" onclick="return $Core.box('directory.browsecheckinhere', 400, 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;" href="javascript:void(0)"><span class="item-number">{$aBusiness.total_checkin}</span> {_p var='checked_in'}</a>
                            {/if}
                            {if $aBusiness.total_like > 0}
                            <a style="cursor:pointer" onclick="return $Core.box('directory.browselike', 400, 'type_id=directory&amp;item_id={$aBusiness.business_id}&amp;force_like=1');" ><span class="item-number">{$aBusiness.total_like}</span> {if $aBusiness.total_like > 1}{_p var='likes_low'}{else}{_p var='like_low'}{/if}</a>
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="ync-detail-bar">
                    {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                        {if $isFavoriteBusiness}
                            <a class="ync-star-vote voted" title="{_p var = 'unfavorite'}" href="javascript:void(0)" onclick="$.ajaxCall('directory.deleteFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-star-o"></i></a>
                        {else}
                            <a class="ync-star-vote" title="{_p var = 'favorite'}" href="javascript:void(0)" onclick="$.ajaxCall('directory.addFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-star-o"></i></a>
                        {/if}
                    {/if}

                    {if $aYnDirectoryDetail.aBusiness.canManageDashBoard && $bIsNotClaim}
                    <div class="item_bar_action_holder">
                        <a data-toggle="dropdown" class="ync-option-button item_bar_action"><span>{_p var='actions'}</span><i class="ico ico-gear-o"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aBusiness.business_id}">
                            <li id="js_directory_action"></li>
                            <li id="js_directory_manage" >
                                <a href="{$aYnDirectoryDetail.aBusiness.linkBusinessDashBoard}" title="{_p var = 'directory.manage_business'}" onclick=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i>{_p var = 'directory.manage_business'}</a>
                            </li>
                            {if ($aYnDirectoryDetail.aBusiness.canTransferOwner && !$aYnDirectoryDetail.aBusiness.isClaimingDraft && !$aYnDirectoryDetail.aBusiness.isDraft)}
                            <li id="js_directory_transfer">
                                <a href="javascript:void(0)" title="{_p var = 'directory.transfer_owner'}" onclick="yndirectory.click_detailcheckinlist_transferowner(this,{$aBusiness.business_id})"><i class="fa fa-user-o" aria-hidden="true"></i>{_p var ='directory.transfer_owner'}</a>
                            </li>
                            {/if}
                            {if ($aYnDirectoryDetail.aBusiness.canCloseBusiness)}
                            <li id="js_directory_transfer">
                                <a href="javascript:void(0)" title="{_p var = 'directory.close_business'}" onclick="yndirectory.closeBusiness({$aBusiness.business_id})"><i class="fa fa-times-circle-o" aria-hidden="true"></i>{_p var ='directory.close_business'}</a>
                            </li>
                            {/if}
                            {if ($aYnDirectoryDetail.aBusiness.canOpenBusiness)}
                            <li id="js_directory_transfer">
                                <a href="javascript:void(0)" title="{_p var = 'directory.open_business'}" onclick="yndirectory.openBusiness({$aBusiness.business_id})"><i class="fa fa-play-circle-o" aria-hidden="true"></i>{_p var ='directory.open_business'}</a>
                            </li>
                            {/if}
                            {if ($aYnDirectoryDetail.aBusiness.isDraft)}
                            <li id="js_directory_transfer">
                                <a href="{url link='directory.manage-packages.id_'.$aYnDirectoryDetail.aBusiness.business_id}" title="{_p var = 'directory.make_payment'}"><i class="fa fa-money" aria-hidden="true"></i>{_p var = 'directory.make_payment'}</a>
                            </li>
                            {/if}
                            {if ($aYnDirectoryDetail.aBusiness.bCanDelete)}
                            <li id="js_directory_transfer" class="item_delete">
                                <a href="javascript:void(0)" title="{_p var = 'directory.delete_business'}" onclick="yndirectory.confirmDeleteBusiness({$aBusiness.business_id}, 1); return false;"><i class="fa fa-trash-o" aria-hidden="true"></i>{_p var = 'directory.delete_business'}</a>
                            </li>
                            {/if}

                        </ul>
                    </div>
                    {/if}

                </div>
            </div>
            <!-- responsive block theme 1-->
            <div class="yndirectory-theme-1-responsive-container" style="display: none;">
                <div class="yndirectory-detail-action-btn-group">
                    {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                    {if $isFollowedBusiness}
                    <button type="button" class="btn btn-default btn-icon btn-sm" onclick="$.ajaxCall('directory.deleteFollow', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-check"></i><span>{phrase var='following'}</span></button>
                    {else}
                    <button type="button" class="btn btn-primary btn-icon btn-sm" onclick="$.ajaxCall('directory.addFollow', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-check"></i><span>{phrase var='follow'}</span></button>
                    {/if}
                    {/if}

                    {if Phpfox::isUser() && $aBusiness.type != 'claiming' && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                    {if $isLiked}
                    <button type="button" class="btn btn-default btn-icon btn-sm" onclick="$(this).parent().hide();$.ajaxCall('directory.deleteLike', 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-thumbup"></i><span>{_p var = 'liked'}</span></button>
                    {else}
                    <button type="button" class="btn btn-primary btn-icon btn-sm" onclick="$(this).parent().hide();$.ajaxCall('directory.addLike', 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-thumbup-o"></i><span>{_p var = 'like'}</span></button>
                    {/if}
                    {/if}

                    {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && PhpFox::getUserId() != $aBusiness.user_id}
                        <a href="javascript:void()" class="btn-compose-msg btn btn-default btn-sm" onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id}{r}); return false;">{_p var='message'}</a>
                    {/if}

                    <div class="dropdown dropdown-overflow">
                        <a class="btn btn-sm btn-default" data-toggle="dropdown" role="button"><i class="ico ico-dottedmore-o"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aBusiness.business_id}">
                            <li>
                                <a id="yndirectory_detail_download" style="cursor: pointer;" onclick="yndirectory.downloadBusinessDetail(this, {$aBusiness.business_id});"><i title="{_p var ='directory.download_pdf'}" class="fa fa-download"></i>{_p var='download'}</a>
                            </li>
                            <li>
                                <a id="yndirectory_detail_print" class=" no_ajax_link" target="_blank" href="{url link='directory.printbusiness.'.$aYnDirectoryDetail.aBusiness.business_id}"><i title="{_p var = 'directory.print'}" class="fa fa-print"></i>{_p var='print'}</a>
                            </li>
                            <li class="responsive-compose-msg" style="display: none;">
                                <a href="javascript:void()"  onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id}{r}); return false;"><span class="ico ico-comment-o"></span>{_p var='message'}</a>
                            </li>
                            <li>
                                <span id="yndirectory_detail_hidden" style="display: none;"><a href="{$aYnDirectoryDetail.sDownloadBusinessUrl}" class="no_ajax_link"></a></span>
                            </li>
                        </ul>
                    </div>
                    <div class="yndirectory-theme-1-vote-responsive">
                        {if Phpfox::isUser() &&  ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                            {if $isFavoriteBusiness}
                                <a class="ync-star-vote voted" title="{_p var = 'unfavorite'}" href="javascript:void(0)" onclick="$.ajaxCall('directory.deleteFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-star-o"></i></a>
                            {else}
                                <a class="ync-star-vote" title="{_p var = 'favorite'}" href="javascript:void(0)" onclick="$.ajaxCall('directory.addFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-star-o"></i></a>
                            {/if}
                        {/if}
                    </div>

                </div>
                <div class="yndirectory-statistic-checkin-like">
                    {if $aBusiness.total_checkin > 0}
                    <a style="cursor:pointer" onclick="return $Core.box('directory.browsecheckinhere', 400, 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;" href="javascript:void(0)"><span class="item-number">{$aBusiness.total_checkin}</span> {_p var='checked_in'}</a>
                    {/if}
                    {if $aBusiness.total_like > 0}
                    <a style="cursor:pointer" onclick="return $Core.box('directory.browselike', 400, 'type_id=directory&amp;item_id={$aBusiness.business_id}&amp;force_like=1');" ><span class="item-number">{$aBusiness.total_like}</span> {if $aBusiness.total_like > 1}{_p var='likes_low'}{else}{_p var='like_low'}{/if}</a>
                    {/if}
                </div>
            </div>

        </div>

        {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && $aBusiness.bCanCheckinhere}
        <div class="yndirectory-checkin-now t_center">
        <a href="javascript:void(0)" onclick="yndirectory.click_detailcheckinlist_checkinhere(this, {$aBusiness.business_id}); return false;"><strong>{_p var='check_in_now'}</strong> {_p var = 'to_your_friend_know_you_have_been_here'}</a>
        </div>
        {/if}

        {elseif $aBusiness.theme_id == 2}
<!--            TODO theme2-->
        {if count($aCoverPhotos)}
        <div id="yndirectory-featured" class="yndirectory-featured dont-unbind-children owl-carousel owl-theme">
            {foreach from=$aCoverPhotos item=aPhoto name=aPhoto}
            <div class="item">
                <div class="yndirectory-featured__item">
                <div class="yndirectory-featured__photo"  style="background-image: url(
                {if $aPhoto.image_path}
                    {img return_url=true server_id=$aPhoto.server_id path='core.url_pic' file='yndirectory/'.$aPhoto.image_path suffix=''}
                {/if}
                )"></div>
                </div>
            </div>
            {/foreach}
        </div>
        {else}
            <div id="yndirectory-featured" class="yndirectory-featured dont-unbind-children owl-carousel owl-theme">
                <div class="item">
                    <div class="yndirectory-featured__item">
                    <div class="yndirectory-featured__photo"  style="background-image: url(
                    {if $aBusiness.default_cover}
                        {$aBusiness.cover_photo}
                    {/if}
                    )"></div>
                    </div>
                </div>
            </div>
        {/if}

        <div class="ync-detail-info theme-2">
            <div class="yndirectory-featured__info">
                <div class="yndirectory-featured__inner pr-1">
                    <div class="yndirectory-featured__media mr-2">
                        {if isset($aBusiness.logo_path)}
                            {img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix=''}
                        {else}
                            <img title="{$aBusiness.name}" src="{$aBusiness.default_logo_path}"/>
                        {/if}
                    </div>
                    <div class="ync-detail-bar">
                        {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                            {if $isFavoriteBusiness}
                                <a class="ync-star-vote voted" title="{_p var = 'unfavorite'}" href="javascript:void(0)" onclick="$.ajaxCall('directory.deleteFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-star-o"></i></a>
                            {else}
                                <a class="ync-star-vote" title="{_p var = 'favorite'}" href="javascript:void(0)" onclick="$.ajaxCall('directory.addFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-star-o"></i></a>
                            {/if}
                        {/if}

                        {if $aYnDirectoryDetail.aBusiness.canManageDashBoard && $bIsNotClaim}
                        <div class="item_bar_action_holder">
                            <a data-toggle="dropdown" class="ync-option-button item_bar_action"><span>{_p var='actions'}</span><i class="ico ico-gear-o"></i></a>
                            <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aBusiness.business_id}">
                                <li id="js_directory_action"></li>
                                <li id="js_directory_manage" >
                                    <a href="{$aYnDirectoryDetail.aBusiness.linkBusinessDashBoard}" title="{_p var = 'directory.manage_business'}" onclick=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i>{_p var = 'directory.manage_business'}</a>
                                </li>
                                {if ($aYnDirectoryDetail.aBusiness.canTransferOwner && !$aYnDirectoryDetail.aBusiness.isClaimingDraft && !$aYnDirectoryDetail.aBusiness.isDraft)}
                                <li id="js_directory_transfer">
                                    <a href="javascript:void(0)" title="{_p var = 'directory.transfer_owner'}" onclick="yndirectory.click_detailcheckinlist_transferowner(this,{$aBusiness.business_id})"><i class="fa fa-user-o" aria-hidden="true"></i>{_p var ='directory.transfer_owner'}</a>
                                </li>
                                {/if}
                                {if ($aYnDirectoryDetail.aBusiness.canCloseBusiness)}
                                <li id="js_directory_transfer">
                                    <a href="javascript:void(0)" title="{_p var = 'directory.close_business'}" onclick="yndirectory.closeBusiness({$aBusiness.business_id})"><i class="fa fa-times-circle-o" aria-hidden="true"></i>{_p var ='directory.close_business'}</a>
                                </li>
                                {/if}
                                {if ($aYnDirectoryDetail.aBusiness.canOpenBusiness)}
                                <li id="js_directory_transfer">
                                    <a href="javascript:void(0)" title="{_p var = 'directory.open_business'}" onclick="yndirectory.openBusiness({$aBusiness.business_id})"><i class="fa fa-play-circle-o" aria-hidden="true"></i>{_p var ='directory.open_business'}</a>
                                </li>
                                {/if}
                                {if ($aYnDirectoryDetail.aBusiness.isDraft)}
                                <li id="js_directory_transfer">
                                    <a href="{url link='directory.manage-packages.id_'.$aYnDirectoryDetail.aBusiness.business_id}" title="{_p var = 'directory.make_payment'}"><i class="fa fa-money" aria-hidden="true"></i>{_p var = 'directory.make_payment'}</a>
                                </li>
                                {/if}
                                {if ($aYnDirectoryDetail.aBusiness.bCanDelete)}
                                <li id="js_directory_transfer" class="item_delete">
                                    <a href="javascript:void(0)" title="{_p var = 'directory.delete_business'}" onclick="yndirectory.confirmDeleteBusiness({$aBusiness.business_id}, 1); return false;"><i class="fa fa-trash-o" aria-hidden="true"></i>{_p var = 'directory.delete_business'}</a>
                                </li>
                                {/if}

                            </ul>
                        </div>
                        {/if}

                    </div>
                    <div class="yndirectory-featured__body">
                        <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews" class="ync-outer-rating ync-rating-md ync-outer-rating-row full">
                            {if $aYnDirectoryDetail.aBusiness.total_rating != 0}
                            <div class="ync-outer-rating-row">
                                <div class="ync-rating-star">
                                    {for $i = 0; $i < 5; $i++}
                                    {if $i < (int)$aBusiness.total_score}
                                    <i class="ico ico-star" aria-hidden="true"></i>
                                    {elseif ((round($aBusiness.total_score) - $aBusiness.total_score) > 0) && ($aBusiness.total_score - $i) > 0}
                                    <i class="ico ico-star half-star" aria-hidden="true"></i>
                                    {else}
                                    <i class="ico ico-star disable" aria-hidden="true"></i>
                                    {/if}
                                    {/for}
                                </div>
                            </div>
                            <span class="ync-rating-count-review">
                                <span class="item-number">{$aBusiness.total_review}</span>
                                {if $aBusiness.total_review > 1 }
                                    <span class="item-text">{_p var = 'directory.reviews'}</span>
                                {else}
                                    <span class="item-text">{_p var = 'directory.review'}</span>
                                {/if}
                            </span>
                        {/if}
                            <span class="yndirectory-detail-rating-action">
                                {if $aBusiness.bCanRateBusiness && $aBusiness.is_pending_claiming != 1}
                                <div class="business-rating-action">
                                {if $aBusiness.total_rating == 0}
                                    <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews"><span>{phrase var='no_reviews_be_the_first'}</span></a>
                                {else}
                                     &nbsp;<a class="item-write-review" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews"><i class="ico ico-textedit"></i><span>{phrase var='write_a_review'}</span></a>
                                {/if}
                                </div>
                                {/if}
                            </span>
                        </a>

                        <h2 class="fw-bold yndirectory-featured__title"><a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}" id="js_business_edit_inner_title{$aYnDirectoryDetail.aBusiness.business_id}">{$aYnDirectoryDetail.aBusiness.name|clean}</a></h2>

                        {if count($aBusinesLocations)>0}
                        <div class="yndirectory-featured__address">
                            {if count($aBusinesLocations)<2}
                                {foreach from=$aBusinesLocations item=aItem}
                                    <p>{if !empty($aItem.location_address)}{$aItem.location_address}{/if}</p>
                                {/foreach}
                            {else}
                                {foreach from=$aBusinesLocations item=aItem}
                                    <p class="mb-h1">{if !empty($aItem.location_title)}{$aItem.location_title}
                                    -
                                    {/if}{if !empty($aItem.location_address)}{$aItem.location_address}{/if}</p>
                                {/foreach}
                            {/if}
                        </div>
                        {/if}
                        <div class="yndirectory-detail-action-btn-group">
                        {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                        {if $isFollowedBusiness}
                        <button type="button" class="btn btn-default btn-icon btn-sm" onclick="$.ajaxCall('directory.deleteFollow', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-check"></i><span>{phrase var='following'}</span></button>
                        {else}
                        <button type="button" class="btn btn-primary btn-icon btn-sm" onclick="$.ajaxCall('directory.addFollow', 'item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-check"></i><span>{phrase var='follow'}</span></button>
                        {/if}
                        {/if}

                        {if Phpfox::isUser() && $aBusiness.type != 'claiming' && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                        {if $isLiked}
                        <button type="button" class="btn btn-default btn-icon btn-sm" onclick="$(this).parent().hide();$.ajaxCall('directory.deleteLike', 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-thumbup"></i><span>{_p var = 'liked'}</span></button>
                        {else}
                        <button type="button" class="btn btn-primary btn-icon btn-sm" onclick="$(this).parent().hide();$.ajaxCall('directory.addLike', 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;"><i class="ico ico-thumbup-o"></i><span>{_p var = 'like'}</span></button>
                        {/if}
                        {/if}

                        {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && PhpFox::getUserId() != $aBusiness.user_id}
                            <a href="javascript:void()" class="btn-compose-msg btn btn-default btn-sm" onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id}{r}); return false;">{_p var='message'}</a>
                        {/if}

                        <div class="dropdown dropdown-overflow">
                            <a class="btn btn-sm btn-default" data-toggle="dropdown" role="button"><i class="ico ico-dottedmore-o"></i></a>
                            <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aBusiness.business_id}">
                                <li>
                                    <a id="yndirectory_detail_download" style="cursor: pointer;" onclick="yndirectory.downloadBusinessDetail(this, {$aBusiness.business_id});"><i title="{_p var ='directory.download_pdf'}" class="fa fa-download"></i>{_p var='download'}</a>
                                </li>
                                <li>
                                    <a id="yndirectory_detail_print" class=" no_ajax_link" target="_blank" href="{url link='directory.printbusiness.'.$aYnDirectoryDetail.aBusiness.business_id}"><i title="{_p var = 'directory.print'}" class="fa fa-print"></i>{_p var='print'}</a>
                                </li>
                                <li class="responsive-compose-msg" style="display: none;">
                                    <a href="javascript:void()"  onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id}{r}); return false;"><span class="ico ico-comment-o"></span>{_p var='message'}</a>
                                </li>
                                <li>
                                    <span id="yndirectory_detail_hidden" style="display: none;"><a href="{$aYnDirectoryDetail.sDownloadBusinessUrl}" class="no_ajax_link"></a></span>
                                </li>

                                {if $aBusiness.type != 'claiming'}
                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                                    <li>
                                        <a id="yndirectory_detailcheckinlist_comparebutton" href="javascript:void(0)" onclick="yndirectory.click_yndirectory_detailcheckinlist_comparebutton(this, {$aBusiness.business_id}); return false;"><i class="fa fa-files-o"></i> {phrase var='add_to_compare'}</a>
                                        <div style="display: none;">
                                            <input type="checkbox"
                                                data-compareitembusinessid="{$aBusiness.business_id}"
                                                data-compareitemname="{$aBusiness.name}"
                                                data-compareitemlink="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}"
                                                data-compareitemlogopath="{img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_100' return_url=true}"
                                                onclick="yndirectory.clickCompareCheckbox(this);"
                                                class="yndirectory-compare-checkbox"> {phrase var='add_to_compare'}
                                        </div>
                                    </li>
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && $aBusiness.setting_support.allow_users_to_share_business}
                                    <li>
                                        <a href="javascript:void(0)" onclick="tb_show('{phrase var='share'}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type=feed&amp;url={$aBusiness.linkBusiness}&amp;title={$aBusiness.name}&amp;feed_id={$aBusiness.business_id}&amp;is_feed_view=1&amp;sharemodule=directory')); return false;"><i class="fa fa-share"></i> {phrase var='share'}</a>
                                    </li>
                                    {/if}

                                    {if Phpfox::isUser() && ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                                        {if $isFavoriteBusiness}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.deleteFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="fa fa-bookmark"></i> {phrase var='unfavorite'}</a></li>
                                        {else}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.addFavorite', 'item_id={$aBusiness.business_id}'); return false;"><i class="fa fa-bookmark"></i> {phrase var='favorite'}</a></li>
                                        {/if}
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}
                                        <li><a href="javascript:void(0)" onclick="yndirectory.click_detailcheckinlist_promotebusiness(this, {$aBusiness.business_id}); return false;"><i class="fa fa-bullhorn"></i> {phrase var='promote_business'}</a></li>
                                    <li><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type=directory&amp;id={$aBusiness.business_id}" class="inlinePopup activity_feed_report" title="{phrase var='Report this business'}"><i class="fa fa-bolt"></i> {phrase var='report_business'}</a></li>
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && PhpFox::getUserId() != $aBusiness.user_id && $aBusiness.setting_support.allow_users_to_confirm_working_at_the_business}
                                        {if !$aBusiness.isMember}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.addUserMemberRole', 'item_id={$aBusiness.business_id}'); return false;" class="yndirectory-detailcheckinlist-active"><i class="fa fa-briefcase"></i> {phrase var='working_here'}</a></li>
                                        {else}
                                            <li><a href="javascript:void(0)" onclick="$.ajaxCall('directory.leaveBusiness', 'item_id={$aBusiness.business_id}'); return false;" class="yndirectory-detailcheckinlist-active"><i class="fa fa-briefcase"></i> {phrase var='leave_this_business'}</a></li>
                                        {/if}
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && PhpFox::getUserId() != $aBusiness.user_id}
                                        <li><a href="javascript:void(0)" onclick="$Core.composeMessage({l}user_id: {$aBusiness.user_id} {r}); return false;"><i class="fa fa-envelope"></i> {phrase var='message_owner'}</a></li>
                                    {/if}

                                    {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && $canInviteMember && $aBusiness.setting_support.allow_users_to_invite_friends_to_business}
                                        <li><a href="javascript:void(0)" onclick="$Core.box('directory.inviteBlock',800,'id={$aBusiness.business_id}&url={$aBusiness.linkBusiness}');"><i class="fa fa-chevron-circle-right"></i> {phrase var='invite_member'}</a></li>
                                    {/if}
                                    {/if}

                            </ul>
                        </div>

                        </div>
                        <div class="yndirectory-statistic-checkin-like">
                            {if $aBusiness.total_checkin > 0}
                            <a style="cursor:pointer" onclick="return $Core.box('directory.browsecheckinhere', 400, 'type_id=directory&amp;item_id={$aBusiness.business_id}'); return false;" href="javascript:void(0)"><span class="item-number">{$aBusiness.total_checkin}</span> {_p var='checked_in'}</a>
                            {/if}
                            {if $aBusiness.total_like > 0}
                            <a style="cursor:pointer" onclick="return $Core.box('directory.browselike', 400, 'type_id=directory&amp;item_id={$aBusiness.business_id}&amp;force_like=1');" ><span class="item-number">{$aBusiness.total_like}</span> {if $aBusiness.total_like > 1}{_p var='likes_low'}{else}{_p var='like_low'}{/if}</a>
                            {/if}
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft')) && $aBusiness.bCanCheckinhere}
        <div class="yndirectory-checkin-now t_center">
        <a href="javascript:void(0)" onclick="yndirectory.click_detailcheckinlist_checkinhere(this, {$aBusiness.business_id}); return false;"><strong>{_p var='check_in_now'}</strong> {_p var = 'to_your_friend_know_you_have_been_here'}</a>
        </div>
        {/if}

        {/if}

    </div>

	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="{$aYnDirectoryDetail.aBusiness.business_id}" id="yndirectory_detail_business_id" name="yndirectory_detail_business_id" />
		<input type="hidden" value="detail" id="yndirectory_pagename" name="yndirectory_pagename" />
		<input type="hidden" value="data" id="yndirectory_detail_data" name="yndirectory_detail_data"
			data-sdetailurl="{$aYnDirectoryDetail.sDetailUrl}"
			data-businessname="{$aYnDirectoryDetail.aBusiness.name}"
			data-businesslocationaddress="{$aYnDirectoryDetail.aBusiness.location_address}"
			data-businessid="{$aYnDirectoryDetail.aBusiness.business_id}"
			data-sdownloadbusinessurl="{$aYnDirectoryDetail.sDownloadBusinessUrl}"
			/>
		<input type="hidden" name="yndirectory_business_item_id" id="yndirectory_business_item_id" value="{$aYnDirectoryDetail.aBusiness.business_id}">
		<input type="hidden" name="yndirectory_business_isliked" id="yndirectory_business_isliked" value="{$aYnDirectoryDetail.aBusiness.isLiked}">
		<input type="hidden" name="yndirectory_displayLikeButtonTheme2" id="yndirectory_displayLikeButtonTheme2" value="{if ($aYnDirectoryDetail.aBusiness.business_status == (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))}0{else}{$aYnDirectoryDetail.aBusiness.displayLikeButtonTheme2}{/if}">
		<input type="hidden" name="yndirectory_can_manage_businesss" id="yndirectory_can_manage_businesss" value="{$aYnDirectoryDetail.aBusiness.canManageDashBoard}">
		<input type="hidden" name="yndirectory_can_close_business" id="yndirectory_can_close_business" value="{$aYnDirectoryDetail.aBusiness.canCloseBusiness}">
		<input type="hidden" name="yndirectory_can_open_business" id="yndirectory_can_open_business" value="{$aYnDirectoryDetail.aBusiness.canOpenBusiness}">
		<input type="hidden" name="yndirectory_can_delete_business" id="yndirectory_can_delete_business" value="{$aYnDirectoryDetail.aBusiness.bCanDelete}">
		<input type="hidden" name="yndirectory_can_publish_business" id="yndirectory_can_publish_business" value="{$aYnDirectoryDetail.aBusiness.isDraft}">
		<input type="hidden" name="yndirectory_can_transfer_businesss" id="yndirectory_can_transfer_businesss" value="{$aYnDirectoryDetail.aBusiness.canTransferOwner}">
		<input type="hidden" name="yndirectory_manage_businesss_link" id="yndirectory_manage_businesss_link" value="{$aYnDirectoryDetail.aBusiness.linkBusinessDashBoard}">
		<input type="hidden" name="yndirectory_makepayment_businesss_link" id="yndirectory_makepayment_businesss_link" value="{url link='directory.manage-packages.id_'.$aYnDirectoryDetail.aBusiness.business_id}">
		<input type="hidden" name="yndirectory_is_claiming_draft" id="yndirectory_is_claiming_draft" value="{$aYnDirectoryDetail.aBusiness.isClaimingDraft}">
		<input type="hidden" name="yndirectory_print_businesss_link" id="yndirectory_print_businesss_link" value="{url link='directory.printbusiness.'.$aYnDirectoryDetail.aBusiness.business_id}">
		<input type="hidden" name="yndirectory_is_print_page" id="yndirectory_is_print_page" value="0">
	</div>
    {if (!empty($aModuleView))}
    <div class="page_section_menu page_section_menu_header ">
        <ul id="directory_tab" class="nav nav-tabs nav-justified">
            {if isset($aModuleView.overview) && $aModuleView.overview.is_show}
            <li {if $aModuleView.overview.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}overview" class="fw-bold text-primary-light mb-0 whs-nw">{$aModuleView.overview.module_phrase|convert}</a>
            </li>
            {/if}

            {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
            && isset($aModuleView.aboutus) && $aModuleView.aboutus.is_show
            }
            <li {if $aModuleView.aboutus.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}aboutus" class="fw-bold text-primary-light mb-0 whs-nw">{$aModuleView.aboutus.module_phrase|convert}</a>
            </li>
            {/if}

            {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
            && isset($aModuleView.contactus) && $aModuleView.contactus.is_show}
            <li {if $aModuleView.contactus.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}contactus" class="fw-bold text-primary-light mb-0 whs-nw">{_p var='contact_us'}</a>
            </li>
            {/if}

            {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
            && isset($aModuleView.activities) && $aModuleView.activities.is_show}
            <li {if $aModuleView.activities.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}activities" class="fw-bold text-primary-light mb-0 whs-nw">{$aModuleView.activities.module_phrase|convert} </a>
            </li>
            {/if}

            {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
            && isset($aModuleView.members) && $aModuleView.members.is_show}
            <li {if $aModuleView.members.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}members" class="fw-bold text-primary-light mb-0 whs-nw">{$aModuleView.members.module_phrase|convert}<span class="yndirectory-numonmenu">({$aNumberOfItem.members})</span></a>
            </li>
            {/if}

            {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
            && isset($aModuleView.followers) && $aModuleView.followers.is_show}
            <li {if $aModuleView.followers.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}followers" class="fw-bold text-primary-light mb-0 whs-nw">{$aModuleView.followers.module_phrase|convert}<span class="yndirectory-numonmenu">({$aNumberOfItem.followers})</span></a>
            </li>
            {/if}

            {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
            && isset($aModuleView.reviews) && $aModuleView.reviews.is_show}
            <li {if $aModuleView.reviews.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}reviews" class="fw-bold text-primary-light mb-0 whs-nw">{$aModuleView.reviews.module_phrase|convert}<span class="yndirectory-numonmenu">({$aNumberOfItem.reviews})</span></a>
            </li>
            {/if}

            {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
            && isset($aModuleView.faq) && $aModuleView.faq.is_show}
            <li {if $aModuleView.faq.active}class='active'{/if}>
                <a href="{permalink module='directory.detail' id=$aYnDirectoryDetail.aBusiness.business_id title=$aYnDirectoryDetail.aBusiness.name}faq" class="fw-bold text-primary-light mb-0 whs-nw">{$aModuleView.faq.module_phrase|convert}</a>
            </li>
            {/if}
        </ul>
        <div class="clear"></div>
    </div>
    {/if}
	{if isset($iCustomPage) && $iCustomPage}
		{module name='directory.detailcustompage' aYnDirectoryDetail=$aYnDirectoryDetail}
	{else}
		{if 'overview' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailoverview' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'aboutus' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailaboutus' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'activities' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailactivities' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'members' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailmembers' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'followers' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailfollowers' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'reviews' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailreviews' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'photos' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailphotos' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'videos' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailvideos' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'musics' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailmusics' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif in_array($aYnDirectoryDetail.firstpage, array('blogs', 'advanced-blog'))}
			{module name='directory.detailblogs' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'discussion' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detaildiscussion' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'coupons' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailcoupons' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'polls' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailpolls' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'events' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailevents' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'jobs' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailjobs' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'marketplace' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailmarketplace' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'contactus' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailcontactus' aYnDirectoryDetail=$aYnDirectoryDetail}

		{elseif 'faq' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailfaq' aYnDirectoryDetail=$aYnDirectoryDetail}
		{elseif 'ultimatevideo' == $aYnDirectoryDetail.firstpage}
			{module name='directory.detailultimatevideos' aYnDirectoryDetail=$aYnDirectoryDetail}
        {elseif 'v' == $aYnDirectoryDetail.firstpage}
            {module name='directory.detailcorevideos' aYnDirectoryDetail=$aYnDirectoryDetail}
		{/if}

	{/if}
</div>
{literal}
<script>
	window.initialize = function initialize() {}
</script>
{/literal}
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places&callback=initialize"></script>