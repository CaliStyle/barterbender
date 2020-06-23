<div class="pul-right was_full ynmember_review_useful_{$aReview.review_id}">
    <p>{_p var='was_this_useful'}</p>
    <div>
        <div>
            {if $aReview.is_vote_yes || !Phpfox::isUser()}
                <span class="fw-bold" href="javascript:void(0)">{_p var='Yes'}</span> ({$aReview.total_yes})
            {else}
                <a href="javascript:void(0)" onclick="ynmember.voteReview({$aReview.review_id}, 1)">{_p var='Yes'}</a> ({$aReview.total_yes})
            {/if}
        </div>
        <i class="yn_dots">.</i>
        <div>
            {if $aReview.is_vote_no || !Phpfox::isUser()}
                <span class="fw-bold" href="javascript:void(0)">{_p var='No'}</span> ({$aReview.total_no})
            {else}
                <a href="javascript:void(0)" onclick="ynmember.voteReview({$aReview.review_id}, 0)">{_p var='No'}</a> ({$aReview.total_no})
            {/if}
        </div>
    </div>
</div>