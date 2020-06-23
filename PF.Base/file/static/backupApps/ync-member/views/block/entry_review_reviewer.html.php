<div class="ynmember_review_block">
    <div class="ynmember_user_review_block_inner">
        <div class="ynmember_avatar">
            {if $aReview.aReviewer.user_image}
            <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" class="ynmember_avatar_thumb" style="background-image: url('{img user=$aReview.aReviewer suffix='_200_square' return_url=true}');"></a>
            {else}
            {img user=$aReview.aReviewer suffix='_200_square' return_url=true}
            {/if}
        </div>
        <div class="ynmember_info clearfix">
            <div class="pull-left">
                <div class="ynmember_time">
                    {$aReview.aReviewer|user}
                    <i class="yn_dots">-</i>
                    <p>{$aReview.time_stamp|date:'core.global_update_time'}</p>
                </div>
                <p class="ynmember_review_title fw-bold">{$aReview.title|highlight:'search'}</p>
                <div class="js_view_more_parent">
                    <div class="ynmember_desc">
                        <span class="fw-bold">{$aReview.rating|ynmember_round:0} <i class="fa fa-star" aria-hidden="true"></i></span>
                        <div>
                            {$aReview.text|parse|highlight:'search'}
                        </div>
                        {module name='ynmember.custom.view' review_id=$aReview.review_id}
                    </div>
                    <span class="item_view_more">
                        <a href="javascript:void(0);" onclick="ynmember.toggleViewmore(this)">{_p var='view more'}</a>
                    </span>
                    <span class="item_view_less">
                        <a href="javascript:void(0);" onclick="ynmember.toggleViewmore(this)">{_p var='view less'}</a>
                    </span>
                </div>
                {template file='ynmember.block.entry_review_useful'}
                <div class="ynmember_static clearfix">
                    <span class="like" onclick="ynmember.toggleReviewComment({$aReview.review_id})">{if $aReview.total_like==1}{_p var='1 like'}{else}{_p var='number_likes' number=$aReview.total_like}{/if}</span>
                    <span onclick="ynmember.toggleReviewComment({$aReview.review_id})">{if $aReview.total_comment==1}{_p var='1_comment'}{else}{_p var='number_comments' number=$aReview.total_comment}{/if}</span>
                </div>
                {if Phpfox::isUser()}
                {if (Phpfox::getUserParam('ynmember_edit_review_self') && Phpfox::getUserId() == $aReview.user_id)
                || (Phpfox::getUserParam('ynmember_edit_review_others') && Phpfox::getUserId() != $aReview.user_id)
                || (Phpfox::getUserParam('ynmember_delete_review_self') && Phpfox::getUserId() == $aReview.user_id)
                || (Phpfox::getUserParam('ynmember_delete_review_others') && Phpfox::getUserId() != $aReview.user_id)
                }
                <div class="dropdown">
                    <span class="fa fa-pencil-square-o dropdown-toggle" aria-hidden="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span>
                    <ul class="dropdown-menu dropdown-menu-right">
                        {if (Phpfox::getUserParam('ynmember_edit_review_self') && Phpfox::getUserId() == $aReview.user_id)
                        || Phpfox::getUserParam('ynmember_edit_review_others')
                        }
                        <li><a href="{url link='ynmember.writereview' user_id=$aReview.item_id review_id=$aReview.review_id}" class="popup"><i class="fa fa-pencil" aria-hidden="true"></i>{_p var='Edit review'}</a></li>
                        {/if}
                        {if (Phpfox::getUserParam('ynmember_delete_review_self') && Phpfox::getUserId() == $aReview.user_id)
                        || (Phpfox::getUserParam('ynmember_delete_review_others') && Phpfox::getUserId() != $aReview.user_id)
                        }
                        <li>
                            <a href="javascript:void(0)" class="delete" onclick="ynmember.deleteReview({$aReview.review_id})">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>{_p var='Delete review'}
                            </a>
                        </li>
                        {/if}
                    </ul>
                </div>
                {/if}
                {/if}
            </div>
            {template file='ynmember.block.entry_review_useful'}
        </div>
    </div>
    {if !$bSingleUser}
        <a href="{url link='ynmember.review' user_id=$aReview.item_id}" class="uppercase view_more_detail"><i class="fa fa-caret-right" aria-hidden="true"></i>{_p var='view all'}</a>
    {/if}
</div>