{if Phpfox::isAdmin() || ($bCheckPage)}
    <div class="dropdown clearfix {if isset($bIsDetailView) && $bIsDetailView}item_bar_action_holder{/if}">
        <!--    --><?php //var_dump($this->_aVars['bCheckPage']);?>
        {if Phpfox::isAdmin() || isset($bCheckPage)}
            <a role="button" data-toggle="dropdown"
               class="{if isset($bIsDetailView) && $bIsDetailView}item_bar_action{else}btn btn-default btn-sm{/if}">
                {if !isset($bIsDetailView)}
                    <i class="fa fa-edit"></i>
                {else}
                    <i id="icon_edit" class="fa fa-edit" style="font-size:16px; margin:12px; color:#626262"
                       onclick="$('#icon_edit').css('color','white');"
                       onmouseout="$('#icon_edit').css('color','#626262');"></i>
                {/if}
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                {if $aItem.is_approved == 0 && user('ynuv_can_approve_video')}
                    <li role="presentation">
                        <a href="" data-toggle="ultimatevideo" data-cmd="approve_video" data-id="{$aItem.video_id}">
                            <i class="fa fa-check-square"></i>
                            {_p('Approve')}
                        </a>
                    </li>
                {/if}
                {if user('ynuv_can_feature_video')}
                    {if $aItem.is_featured == 0 && isset($bIsPagesView) && !$bIsPagesView }
                        <li role="presentation">
                            <a href="" data-toggle="ultimatevideo" data-cmd="featured_video" data-id="{$aItem.video_id}"
                               class="ynuv_feature_video_{$aItem.video_id}">
                                <i class="fa fa-diamond"></i>
                                {_p('Featured')}</a>
                        </li>
                    {elseif $aItem.is_featured == 1 && isset($bIsPagesView) && !$bIsPagesView}
                        <li role="presentation">
                            <a href="" data-toggle="ultimatevideo" data-cmd="unfeatured_video"
                               data-id="{$aItem.video_id}" class="ynuv_feature_video_{$aItem.video_id}">
                                <i class="fa fa-diamond"></i>
                                {_p('Un-Feature')}</a>
                        </li>
                    {/if}
                {/if}
                <li role="presentation" class="item_delete">
                    <a href="" href="" class="no_ajax_link" data-toggle="ultimatevideo" data-cmd="delete_video"
                       data-id="{$aItem.video_id}" data-confirm="{_p('are_you_sure_want_to_delete_this_video')}"
                       {if isset($bIsDetailView) && $bIsDetailView}data-detail="true" {else}data-detail="false"{/if}>
                        <i class="fa fa-trash"></i>
                        {_p('delete_video')}</a>
                </li>
            </ul>
        {/if}
    </div>
{/if}