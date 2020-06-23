<li class="videos-addto-more js_ync_videovp_edit_more" title="{_p var='more_actions'}">
    <div class="dropup dropdown-container">
        <span role="button" data-toggle="dropdown"
           class="item_bar_action"
           data-id="{$aItem.video_id}" data-imgpath="{$corePath}/assets/image/loading.gif"
           onclick="getPlaylistToQuickAddVideo(this);">
            <i class="ico ico-plus mr-1"></i>{_p('add_to')}
        </span>
        <ul class="dropdown-menu dropdown-menu-right ync-videovp-actions">
            {if isset($aItem.watchlater)}
                <li role="presentation">
                    {if !$aItem.watchlater }
                        <a href="" data-toggle="ultimatevideo" data-cmd="watchlater_video" data-id="{$aItem.video_id}"
                           class="ynuv_watchlater_video_{$aItem.video_id}">
                            <i class="fa fa-clock-o"></i>
                            {_p('watch_later')}
                        </a>
                    {else}
                        <a href="" data-toggle="ultimatevideo" data-cmd="unwatchlater_video" data-id="{$aItem.video_id}"
                           class="ynuv_watchlater_video_{$aItem.video_id}">
                            <i class="fa fa-clock-o"></i>
                            {_p('Un-Watch Later')}
                        </a>
                    {/if}
                </li>
            {/if}
            <li role="presentation">
                {if ultimatevideo_favourite($aItem.video_id) }
                    <a href="" data-toggle="ultimatevideo" data-cmd="unfavorite_video" data-id="{$aItem.video_id}"
                       class="ynuv_favorite_video_{$aItem.video_id}">
                        <i class="fa fa-star"></i>
                        {_p('Un-Favorite')}</a>
                {else}
                    <a href="" data-toggle="ultimatevideo" data-cmd="favorite_video" data-id="{$aItem.video_id}"
                       class="ynuv_favorite_video_{$aItem.video_id}">
                        <i class="fa fa-star-o"></i>
                        {_p('Favorite')}</a>
                {/if}
            </li>
            <li class="dropdown-header">
                {_p('add_to_playlist')}
            </li>
            <li class="ynuv_error_add_to_playlist_{$aItem.video_id}" hidden></li>
            <li class="ynuv_noti_add_to_playlist_{$aItem.video_id}" hidden></li>
            <li class="ynuv_quick_list_playlist">
                <div class="text-center"><img src="{$corePath}/assets/image/loading.gif"/></div>
            </li>
            <li class="ynuv_quick_list_playlist-title">
                <a data-id="{$aItem.video_id}" data-toggle="ultimatevideo" data-cmd="showFormAddPlaylist">
                    {_p('add_to_new_playlist')}
                    <i class="pull-right ico ico-angle-down" aria-hidden="true"></i>
                </a>
            </li>
            <li class="ynuv_quick_list_playlist-title-form">
                <div class="ultimatevideo-quick-add-form" style="display:none">
                    <span class="ultimatevideo-error"
                          style="display:none">{_p('please_input_the_playlist_title')}</span>
                    <input type="text" name="title" class="ynuv_quick_add_playlist_input"/>
                    {module name='privacy.form' privacy_name='privacy' privacy_type='mini'}
                    <div class="ultimatevideo-quick-add-form-btn">
                        <button class="btn btn-primary btn-sm" data-toggle="ultimatevideo" data-cmd="add_new_playlist"
                                data-id="{$aItem.video_id}"><span>{_p('Create')}</span></button>
                        <button class="btn btn-default btn-sm" data-toggle="ultimatevideo" data-cmd="close_add_form">
                            <span>{_p('Cancel')}</span></button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</li>