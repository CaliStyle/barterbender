<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
{if !isset($aFeed.feed_display) || $aFeed.feed_display != 'view' || ($aFeed.feed_display == 'view' && !empty($aFeed.is_detail_item))}
    {if !empty($aFeed.is_detail_item)}
    <div class="js_comment_like_holder js_ync_reaction_display_in_detail" style="display: none;" id="js_feed_like_holder_{$aFeed.feed_id}">
        <div id="js_like_body_{$aFeed.feed_id}">
    {/if}

    {if isset($ajaxLoadLike) && $ajaxLoadLike}
    <div id="js_like_body_{$aFeed.feed_id}" class="ync-reaction-like-body">
    {/if}
    {if !empty($aFeed.feed_like_phrase)}
    <div class="activity_like_holder ync-reaction-activity-like" id="activity_like_holder_{$aFeed.feed_id}">
        <div class="ync-reaction-list-mini dont-unbind-children">
            {if !empty($aFeed.most_reactions)}
                {for $i = 0; $i <= 2; $i++}
                    {if isset($aFeed.most_reactions[$i])}
                        <div class="ync-reaction-item js_reaction_item">
                            <a href="javascript:void(0)" class="item-outer"
                               data-toggle="ync_reaction_toggle_user_reacted_cmd"
                               data-action="ync_reaction_show_list_user_react_cmd"
                               data-type_id="{$aFeed.like_type_id}"
                               data-item_id="{$aFeed.item_id}"
                               data-feed_id="{if isset($aFeed.feed_id)}{$aFeed.feed_id}{else}0{/if}"
                               data-total_reacted="{$aFeed.most_reactions[$i].total_reacted}"
                               data-react_id="{$aFeed.most_reactions[$i].id}"
                               data-table_prefix="{if isset($aFeed.feed_table_prefix)}{$aFeed.feed_table_prefix}{elseif defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')}pages_{/if}"
                            >
                                <img src="{$aFeed.most_reactions[$i].full_path}" alt="">
                            </a>
                            <div class="ync-reaction-tooltip-user js_ync_reaction_tooltip">
                                <div class="item-title">{_p var=$aFeed.most_reactions[$i].title|clean} ({$aFeed.most_reactions[$i].total_reacted|short_number})</div>
                                <div class="item-tooltip-content js_ync_reaction_preview_reacted">
                                    <div class="item-user">{_p var='loading_three_dot'}</div>
                                </div>
                            </div>
                        </div>
                    {/if}
                {/for}
            {/if}
         </div>
        {$aFeed.feed_like_phrase}
        {if (isset($aFeed.feed_total_like) && $aFeed.feed_total_like)}
        <a href="javascript:void(0)" class="ync-reaction-total" style="display: none;"
           data-action="ync_reaction_show_list_user_react_cmd"
           data-type_id="{$aFeed.like_type_id}"
           data-item_id="{$aFeed.item_id}"
           data-feed_id="{if isset($aFeed.feed_id)}{$aFeed.feed_id}{else}0{/if}"
           data-react_id="0"
           data-table_prefix="{if isset($aFeed.feed_table_prefix)}{$aFeed.feed_table_prefix}{elseif defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')}pages_{/if}">
            {$aFeed.feed_total_like|short_number}
        </a>
        {/if}
    </div>
    {else}
    <div class="activity_like_holder activity_not_like">
        {_p var='when_not_like'}
    </div>
    {/if}
    {if isset($ajaxLoadLike) && $ajaxLoadLike}
    </div>
    {/if}
    {if !empty($aFeed.is_detail_item)}
        </div>
    </div>
    {/if}
{/if}
