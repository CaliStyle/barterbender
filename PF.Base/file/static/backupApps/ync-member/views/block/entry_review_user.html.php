<div class="ynmember_user_block clearfix">
    <div class="ynmember_avatar">
        {if $aReview.aUser.user_image}
        <a href="{url link=$aReview.aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aReview.aUser suffix='_200_square' return_url=true}');"></a>
        {else}
        {img user=$aReview.aUser suffix='_200_square' return_url=true}
        {/if}
    </div>
    <div class="ynmember_info clearfix">
        <div>
            <div class="ynmember_name ">
                <span class="ynicon{if $aReview.aUser.is_online} active{/if}"></span>
                {$aReview.aUser|user}
            </div>
            <div class="ynmember_info_show_one_line">
                {if count($aReview.aUser.mutual_friends)}
                <a href="javascript:void(0)" class="small hover one-line line-h ynmember_info_list_item" onclick="$Core.box('friend.getMutualFriends', 300, 'user_id={$aReview.aUser.user_id}'); return false;">
                    {_p var='total_mutual_friends' total=$aReview.aUser.total_mutual_friends}
                </a>
                {/if}
                <!-- <a href="javascript:void(0)" class="small hover one-line line-h">34 friends</a> -->
                {if $aReview.aUser.places.living_name}
                <div class="line-h margin-t max-wid ynmember_info_list_item">
                    <span class="desc small">{_p var='Live in'}</span>
                    <a href="{$aReview.aUser.places.living_place|ynmember_place}" class="small hover" title="{$aReview.aUser.places.living_name}">{$aReview.aUser.places.living_name}</a>
                </div>
                {/if}
                {if $aReview.aUser.places.work_name}
                <div class="line-h margin-t max-wid ynmember_info_list_item">
                    <span class="desc small">{_p var='Work at'}</span>
                    <a href="{$aReview.aUser.places.work_place|ynmember_place}" class="small hover" title="{$aReview.aUser.places.work_name}">{$aReview.aUser.places.work_name}</a>
                </div>
                {/if}
                {if $aReview.aUser.places.study_name}
                <div class="line-h margin-t max-wid ynmember_info_list_item">
                    <span class="desc small">{_p var='Studied at'} </span>
                    <a href="{$aReview.aUser.places.study_place|ynmember_place}" class="small hover" title="{$aReview.aUser.places.study_name}">{$aReview.aUser.places.study_name}</a>
                </div>
                {/if}
                {if $aReview.aUser.about_me}
                <div class="small desc max-wid ynmember_info_list_item">
                    <i class="fa fa-quote-left" aria-hidden="true"></i>
                    {$aReview.aUser.about_me}
                </div>
                {/if}
            </div>
        </div>
        <div>
            <div class="ynmember_user_rating clearfix">
                <div class="rating_number fw-bold">{$aReview.aUser.rating|ynmember_round}</div>
                <div>
                    <div class="ynmember_rating_block">
				        	<span class="rating_star">
				            	{$aReview.aUser.rating|ynmember_rating}
				        	</span>
                    </div>
                    {if $aReview.aUser.total_review == 1}
                    <a href="{url link='ynmember.review' user_id=$aReview.item_id}">({_p var='1_review'})</a>
                    {/if}
                    {if $aReview.aUser.total_review > 1}
                    <a href="{url link='ynmember.review' user_id=$aReview.item_id}">({_p var='more_reviews' number=$aReview.aUser.total_review})</a>
                    {/if}
                </div>
            </div>
            <div class="dropdown ynmember_add_friend_parent clearfix">
                {module name="ynmember.entry_link_friendship" aUser=$aReview.aUser}
                {module name="ynmember.entry_link_action" aUser=$aReview.aUser}
            </div>
            {if Phpfox::getService('ynmember.review')->canWriteReview('' . $aReview.aUser.user_id . '') && !$aReview.aUser.is_review_written && !$bSingleUser}
            <a href="{url link='ynmember.writereview' user_id=$aReview.aUser.user_id}" class="btn btn-primary capitalize popup">{_p var='Write Review'}</a>
            {/if}
        </div>
    </div>
    <div class="ynmember_info ynmember_hidden clearfix">
        <div>
            <div class="ynmember_user_rating clearfix">
                <div class="rating_number fw-bold">{$aReview.aUser.rating|ynmember_round}</div>
                <div>
                    <div class="ynmember_rating_block">
				        	<span class="rating_star">
				            	{$aReview.aUser.rating|ynmember_rating}
				        	</span>
                        {if $aReview.aUser.total_review == 1}
                        <span class="ynmember_review_member">{_p var='1_review'}</span>
                        {/if}
                        {if $aReview.aUser.total_review > 1}
                        <span class="ynmember_review_member">{_p var='more_reviews' number=$aReview.aUser.total_review}</span>
                        {/if}
                    </div>
                    {if $aReview.aUser.total_review == 1}
                    <a href="javascript:void(0)">({_p var='1_review'})</a>
                    {/if}
                    {if $aReview.aUser.total_review > 1}
                    <a href="javascript:void(0)">({_p var='more_reviews' number=$aReview.aUser.total_review})</a>
                    {/if}
                </div>
            </div>
            <div class="dropdown ynmember_add_friend_parent clearfix">
                <div class="dropdown ynmember_add_friend_option">
                    {module name="ynmember.entry_link_friendship" aUser=$aReview.aUser}
                </div>
                {module name="ynmember.entry_link_action" aUser=$aReview.aUser}
            </div>
        </div>
    </div>
</div>