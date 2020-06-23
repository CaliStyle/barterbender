<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class=ynmember_block_item_most>
	<div class="ynmember_block_item_inner clearfix">
		<div class="ynmember_most_avatar">
			{if $aUser.user_image}
				<a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
			{else}
				{img user=$aUser suffix='_200_square' return_url=true}
			{/if}
		</div>
	    <div class="ynmember_most_info">
            {$aUser|user}
            {if count($aUser.mutual_friends) > 1}
                <a href="javascript:void(0)" class="ynmember_mutual_friend font-small" title="mutual friends" onclick="$Core.box('friend.getMutualFriends', 300, 'user_id={$aUser.user_id}'); return false;">
                    {_p var='total_mutual_friends' total=$aUser.total_mutual_friends}
                </a>
            {/if}

            {if $aUser.places.living_name}
                <a href="{$aUser.places.living_place|ynmember_place}" class="ynmember_locale font-small max-text" title="{$aUser.places.living_name}"><span>{_p var='Live in'} </span>{$aUser.places.living_name}</a>
            {/if}
            {if $aUser.places.work_name}
                <a href="{$aUser.places.work_place|ynmember_place}" class="ynmember_work font-small max-text" title="{$aUser.places.work_name}"><span>{_p var='Work at'} </span>{$aUser.places.work_name}</a>
            {/if}
            {if $aUser.places.study_name}
                <a href="{$aUser.places.study_place|ynmember_place}" class="ynmember_study font-small max-text" title="{$aUser.places.study_name}"><span>{_p var='Studied at'} </span>{$aUser.places.study_name}</a>
            {/if}
            {if (isset($popularMode) && $popularMode == 'most_reviewed')}
                {if $aUser.total_review == 1}
                    <a href="{url link='ynmember.review' user_id=$aUser.user_id}" class="ynmember_reviews font-small" title="reviews">{_p var='1_review'}</a>
                {/if}
                {if $aUser.total_review > 1}
                    <a href="{url link='ynmember.review' user_id=$aUser.user_id}" class="ynmember_reviews font-small" title="reviews">{_p var='more_reviews' number=$aUser.total_review}</a>
                {/if}
            {/if}
            {if (isset($popularMode) && $popularMode == 'top_rated')}
                <div class="ynmember_rating_block">
                    <span class="rating_star">
                        {$aUser.rating|ynmember_rating}
                    </span>
                </div>
            {/if}

            {if Phpfox::getService('ynmember.review')->canWriteReview('' . $aUser.user_id . '') && !$aUser.is_review_written}
                <a href="{url link='ynmember.writereview' user_id=$aUser.user_id}" class="ynmember_write popup"><i class="fa fa-star" aria-hidden="true"></i>{_p var='Review & Rate'}</a>
            {else}
                {template file='ynmember.block.entry_link_friendship_new'}
            {/if}

	    </div>
    </div>
</div>

