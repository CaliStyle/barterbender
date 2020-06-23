{if !$bSingleUser && !PHPFOX_IS_AJAX}
    {template file='ynmember.block.advanced_search_review'}
{/if}
{if !count($aReviews)}
    {if !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {_p var='No reviews found'}
    </div>
    {/if}
{else}
    {if !PHPFOX_IS_AJAX}<div class="ynmember_review_block"> 
        <ul>{/if}
            {foreach from=$aReviews name=ynmember_review item=aReview}
                <li class="ynmember_review_block_item{if $bSingleUser && $phpfox.iteration.ynmember_review > 1} ynmember_single_review{/if}">
                    <div class="ynmember_review_block_inner{if $bSingleUser} detail_page{/if}">
                        <!-- user -->
                        {if !empty($aReview.aUser) && (!$bSingleUser || ($phpfox.iteration.ynmember_review==1 && !PHPFOX_IS_AJAX))}
                            {template file='ynmember.block.entry_review_user'}
                        {/if}
                        <!-- reviewer -->
                        {if !empty($aReview.aReviewer)}
                            {template file='ynmember.block.entry_review_reviewer'}
                        <div id="ynmember_review_comment_{$aReview.review_id}" style="display: none">
                            {if user('ynmember_like_comment_review')}
                                {module name='feed.comment' aFeed=$aReview.aFeed}
                            {/if}
                        </div>
                        {/if}
                    </div>
                </li>
            {/foreach}
            {pager}
       {if !PHPFOX_IS_AJAX}</ul>
      </div>{/if}
{/if}

{literal}
<script>
    $Behavior.initReviewShowMore = function(){
        $('.js_view_more_parent').each(function(index, el){
            var content = $(el).find('.ynmember_desc');
            if (content.height() > 100) {
                $(el).addClass('ynmember_has_viewmore');
            }
        });
    };
</script>
{/literal}

{literal}
<style>
    .ynmember_desc {
        max-height: 110px;
        overflow: hidden;
    }

    .ynmember_viewmore > .ynmember_desc {
        max-height: inherit;
    }
</style>
{/literal}