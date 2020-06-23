<?php
?>
<div class="item-user-row">
    <div class="item-outer">
        <div class="item-media">
            {img user=$aUser suffix='_50_square' max_width=50 max_height=50}
        </div>
        <div class="item-inner">
            <div class="item-inner-wrapper">
                <div class="item-wrapper-left">
                    <div class="item-name">
                        {$aUser|user}
                    </div>
                    <div class="p-outer-rating p-outer-rating-row">
                        <div class="p-rating-star">
                            {$iRating|ultimatevideo_rating}
                        </div>
                    </div>
                </div>
                <div class="item-wrapper-right">
                    <div class="item-rating">
                        {$iRating}
                    </div>
                    <div class="item-friend-action"> <!-- friend actions -->
                        {if \Phpfox::getUserId() != $aUser.user_id && !Phpfox::getUserBy('profile_page_id') && !$aUser.profile_page_id}
                            <div class="item-actions">
                                {if $bIsFriend}
                                    <div class="dropdown">
                                        <a role="button" data-toggle="dropdown"
                                           class="btn btn-default btn-sm has-caret item-action-btn">
                                            <span class="ico ico-check"></span><span
                                                    class="item-text ml-1">{_p var='friend'}</span><span
                                                    class="ml-1 ico ico-caret-down"></span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a role="button"
                                                   onclick="$Core.composeMessage({l}user_id: {$aUser.user_id}{r}); return false;"
                                                   title="{_p var='message'}">
                                                    <span class="ico ico-pencilline-o"></span><span
                                                            class="item-text ml-1">{_p var='message'}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#?call=report.add&amp;height=220&amp;width=400&amp;type=user&amp;id={$aUser.user_id}"
                                                   class="inlinePopup" title="{_p var='report_this_user'}">
                                                    <span class="ico ico-warning-o "></span><span
                                                            class="item-text ml-1">{_p var='report_this_user'}</span>
                                                </a>
                                            </li>
                                            <li class="item-delete">
                                                <a role="button"
                                                   onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('friend.delete', 'friend_user_id={$aUser.user_id}&amp;reload=1');{r}, function(){l}{r}); return false;"
                                                   title="{_p var='remove_friend'}">
                                                    <span class="ico ico-user2-del-o"></span><span
                                                            class="item-text ml-1">{_p var='remove_friend'}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                {else}
                                    <button onclick="return $Core.addAsFriend({$aUser.user_id});"
                                            class="btn btn-default btn-sm item-action-btn">
                                        <span class="ico ico-user1-plus-o"></span><span
                                                class="item-text ml-1">{_p var='add_friend'}</span>
                                    </button>
                                {/if}
                            </div>
                        {/if}
                    </div>  <!-- end of friend actions -->
                </div>
            </div>
        </div>
    </div>
</div>
