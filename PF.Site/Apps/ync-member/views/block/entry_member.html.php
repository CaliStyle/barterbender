<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynmember_member_entry_item_inner grid_view">
    <div class="ynmember_cover">
        {if $aUser.cover_photo}
        <div class="ynmember_cover_inner" style="background-image: url('{img server_id=$aUser.cover_photo.server_id path='photo.url_photo' file=$aUser.cover_photo.destination suffix='_1024' return_url=true}');"></div>
        {else}
        <div class="ynmember_cover_inner no_cover"></div>
        {/if}
    </div>

    <div class="ynmember_avatar">
        {if $aUser.user_image}
            <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
        {else}
            {img user=$aUser suffix='_200_square' return_url=true}
        {/if}
        {if $aUser.is_featured}
        <i class="fa fa-diamond ynmember_feature_icon" aria-hidden="true"></i>
        {/if}
        <sup class="ynicon {if $aUser.is_online}active{/if}"></sup>
    </div>

    <div class="ynmember_info text-center">
        <div class="ynmember_owner_name">
            <sup class="ynicon {if $aUser.is_online}active{/if}"></sup>
            {$aUser|user}
        </div>
        {if $aUser.total_review == 1}
            <div class="ynmember_rating_block text-center{if $aUser.is_review_written} write{/if}">
                <span class="rating_star">
                    {$aUser.rating|ynmember_rating}
                </span>
            </div>
            <a href="{url link='ynmember.review' user_id=$aUser.user_id}" class="ynmember_write_review review">({_p var='1_review'})</a>
        {elseif $aUser.total_review > 1}
            <div class="ynmember_rating_block text-center">
                <span class="rating_star">
                    {$aUser.rating|ynmember_rating}
                </span>
                <a href="{url link='ynmember.review' user_id=$aUser.user_id}" class="ynmember_write_review review">({_p var='more_reviews' number=$aUser.total_review})</a>
            </div>
        {elseif Phpfox::getService('ynmember.review')->canWriteReview('' . $aUser.user_id . '') && !$aUser.is_review_written}
            <a href="{url link='ynmember.writereview' user_id=$aUser.user_id}" class="ynmember_write_review uppercase write popup"><i class="fa fa-star" aria-hidden="true"></i>&nbsp;{_p var='Review & Rate'}</a>
        {else}
            <a href="{url link='ynmember.review' user_id=$aUser.user_id}" class="ynmember_write_review write">{_p var='more_reviews' number=$aUser.total_review}</a>
        {/if}
        <div class="ynmember_basic_info">{template file='ynmember.block.entry_mutual_friends'}{template file='ynmember.block.entry_info'}</div>

        {template file='ynmember.block.entry_link_friendship_new'}
        {if $aUser.about_me}
        <div class="ynmember_status">
            <div class="ynmember_status_inner">
                {$aUser.about_me}
            </div>
        </div>
        {/if}
    </div>
    {if $aUser.about_me}
        <i class="fa fa-angle-up ynmember_toggle_status" aria-hidden="true"></i>
    {/if}
    {template file='ynmember.block.entry_link_action'}
</div>

<div class="ynmember_member_entry_item_inner clearfix list_view">
    <div class="ynmember_avatar">
        {if $aUser.user_image}
        <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
        {else}
        {img user=$aUser suffix='_200_square' return_url=true}
        {/if}
        {if $aUser.is_featured}
        <i class="fa fa-diamond ynmember_feature_icon" aria-hidden="true"></i>
        {/if}
    </div>
    <div class="ynmember_info">
        <div class="ynmember_name">
            <span class="ynicon {if $aUser.is_online}active{/if}"></span>
            {$aUser|user}
        </div>
        {if $aUser.about_me}
        <div class="ynmember_about_me">
            {$aUser.about_me}
        </div>
        {/if}
        <div class="ynmember_basic_info">
            {template file='ynmember.block.entry_mutual_friends_icon'}
            {template file='ynmember.block.entry_info_icon'}
        </div>
        <div class="ynmember_rating_block">
            {if $aUser.total_review}
                <span class="rating_star">
                    {$aUser.rating|ynmember_rating}
                </span>
                {if $aUser.total_review == 1}
                <a href="{url link='ynmember.review' user_id=$aUser.user_id}" class="ynmember_review_member">({_p var='1_review'})</a>
                {else}
                <a href="{url link='ynmember.review' user_id=$aUser.user_id}" class="ynmember_review_member">({_p var='more_reviews' number=$aUser.total_review})</a>
                {/if}
            {/if}
        </div>
        {if Phpfox::getService('ynmember.review')->canWriteReview('' . $aUser.user_id . '') && !$aUser.is_review_written}
            <a href="{url link='ynmember.writereview' user_id=$aUser.user_id}" class="ynmember_write_review fw-bold popup"><i class="fa fa-star" aria-hidden="true"></i>{_p var='Review & Rate'}</a>
        {/if}
    </div>
    {if Phpfox::isUser()}
    <div class="dropdown ynmember_add_friend_parent {if (Phpfox::getUserId() == $aUser.user_id)} no_relationship{/if}">
        {if Phpfox::isModule('friend') && Phpfox::getUserId() != $aUser.user_id}
        {template file='ynmember.block.entry_link_friendship'}
        {/if}
        {template file='ynmember.block.entry_link_action'}
    </div>
    {/if}
</div>