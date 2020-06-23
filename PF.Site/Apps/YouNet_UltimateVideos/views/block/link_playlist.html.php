{if user('ynuv_can_feature_playlist') || ($aPitem.is_approved == 0 && user('ynuv_can_approve_playlist')) || (Phpfox::getUserId () == $aPitem.user_id && ( user('ynuv_can_edit_own_playlists') || user('ynuv_can_delete_own_playlists'))) || ( user('ynuv_can_edit_playlist_of_other_user') || user('ynuv_can_delete_playlist_of_other_user')) || (isset($sView) && ($sView == 'historyplaylist'))}
    <div class="dropdown clearfix">
        <a role="button" data-toggle="dropdown" class="p-option-button">
            <i class="ico ico-gear-o"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            {if $aPitem.is_approved == 0 && user('ynuv_can_approve_playlist')}
                <li role="presentation">
                    <a href="" data-toggle="ultimatevideo" data-cmd="approve_playlist" data-id="{$aPitem.playlist_id}">
                        <i class="fa fa-check-square"></i>
                        {_p('Approve')}
                    </a>
                </li>
            {/if}
            {if user('ynuv_can_feature_playlist')}
                {if $aPitem.is_featured == 0}
                    <li role="presentation">
                        <a href="" data-toggle="ultimatevideo" data-cmd="featured_playlist"
                           data-id="{$aPitem.playlist_id}" class="ynuv_feature_playlist_{$aPitem.playlist_id}">
                            <i class="fa fa-diamond"></i>
                            {_p('Feature')}
                        </a>
                    </li>
                {else}
                    <li role="presentation">
                        <a href="" data-toggle="ultimatevideo" data-cmd="unfeatured_playlist"
                           data-id="{$aPitem.playlist_id}" class="ynuv_feature_playlist_{$aPitem.playlist_id}">
                            <i class="fa fa-diamond"></i>
                            {_p('Un-Feature')}
                        </a>
                    </li>
                {/if}
            {/if}
            {if user('ynuv_can_edit_playlist_of_other_user') || (Phpfox::getUserId() == $aPitem.user_id && user('ynuv_can_edit_own_playlists')) }
                <li role="presentation">
                    <a href="{url link='ultimatevideo.addplaylist' id=$aPitem.playlist_id}">
                        <i class="fa fa-pencil"></i>
                        {_p('edit_playlist_info')}
                    </a>
                </li>
                <li role="presentation">
                    <a href="{url link='ultimatevideo.addplaylist' id=$aPitem.playlist_id tab='video'}">
                        <i class="ico ico-video"></i>
                        {_p('manage_videos')}
                    </a>
                </li>
            {/if}
            {if user('ynuv_can_delete_playlist_of_other_user') || (Phpfox::getUserId() == $aPitem.user_id && user('ynuv_can_delete_own_playlists'))}
                <li role="presentation" class="item_delete">
                    <a href="" data-toggle="ultimatevideo" data-cmd="delete_playlist" data-id="{$aPitem.playlist_id}"
                       data-confirm="{_p('are_you_sure_want_to_delete_this_playlist')}"
                       {if isset($bIsDetailViewPlaylist) && $bIsDetailViewPlaylist}data-detail="true"
                       {else}data-detail="false"{/if}>
                        <i class="fa fa-trash"></i>
                        {_p('delete_playlist')}
                    </a>
                </li>
            {/if}
            {if isset($sView) && ($sView == 'historyplaylist')}
                <li role="presentation" class="">
                    <a href="" class="no_ajax_link" data-toggle="ultimatevideo" data-cmd="delete_playlist_history"
                       data-id="{$aPitem.playlist_id}">
                        <i class="fa fa-times"></i>
                        {_p('remove_from_history')}
                    </a>
                </li>
            {/if}
        </ul>
    </div>
{/if}