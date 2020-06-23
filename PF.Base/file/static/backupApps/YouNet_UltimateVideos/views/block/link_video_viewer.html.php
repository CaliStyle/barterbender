<div class="ultimatevideo-actions dropdown clearfix js-ultimatevideo-addto">
    <a role="button" data-toggle="dropdown" class="p-option-button" data-id="{$aItem.video_id}"
       data-imgpath="{$corePath}/assets/image/loading.gif" onclick="getPlaylistToQuickAddVideo(this);" title="{_p var='add_to'}">
        <i class="ico ico-list-plus"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        <li role="presentation">
            {if !$aItem.watchlater }
                <a href="" data-toggle="ultimatevideo" data-cmd="watchlater_video" data-id="{$aItem.video_id}"
                   class="ynuv_watchlater_video_{$aItem.video_id}">
                    <i class="ico ico-clock-o"></i>
                    {_p('watch_later')}
                </a>
            {else}
                <a href="" data-toggle="ultimatevideo" data-cmd="unwatchlater_video" data-id="{$aItem.video_id}"
                   class="ynuv_watchlater_video_{$aItem.video_id}">
                    <i class="ico ico-clock"></i>
                    {_p('Un-Watch Later')}
                </a>
            {/if}
        </li>
        <li role="presentation">
            {if ultimatevideo_favourite($aItem.video_id) }
                <a href="" data-toggle="ultimatevideo" data-cmd="unfavorite_video" data-id="{$aItem.video_id}"
                   class="ynuv_favorite_video_{$aItem.video_id}">
                    <i class="ico ico-star"></i>
                    {_p('un_favorite')}</a>
            {else}
                <a href="" data-toggle="ultimatevideo" data-cmd="favorite_video" data-id="{$aItem.video_id}"
                   class="ynuv_favorite_video_{$aItem.video_id}">
                    <i class="ico ico-star-o"></i>
                    {_p('favorite')}</a>
            {/if}
        </li>
        {if !in_array($aItem.type, array(6,7))}
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
                    <span class="ultimatevideo-error" style="display:none">{_p('please_input_the_playlist_title')}</span>
                    <input type="text" name="title" class="ynuv_quick_add_playlist_input input-sm"/>
                    {module name='privacy.form' privacy_name='privacy' privacy_type='mini'}
                    <div class="ultimatevideo-quick-add-form-btn">
                        <button class="btn btn-primary btn-sm" data-toggle="ultimatevideo" data-cmd="add_new_playlist"
                                data-id="{$aItem.video_id}"><span>{_p('Create')}</span></button>
                        <button class="btn btn-default btn-sm" data-toggle="ultimatevideo" data-cmd="close_add_form">
                            <span>{_p('Cancel')}</span></button>
                    </div>
                </div>
            </li>
        {/if}
    </ul>
</div>
