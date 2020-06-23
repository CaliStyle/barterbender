<div class="ultimatevideo-video-entry">
    <div class="ultimatevideo-video-image-wrap">
        <a class="ultimatevideo-video-image"
            alt="{$aItem.title}"
            href="{permalink module='ultimatevideo.playlist' id=$aItem.playlist_id title=$aItem.title}"
            {if $aItem.image_path}
                style="background-image: url({img server_id=$aItem.image_server_id path='core.url_pic' file=$aItem.image_path suffix='_500' return_url=true})">
            {else}
                style="background-image: url({param var='core.path_actual'}PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_playlist.jpg)">
            {/if}
        </a>
        <div class="ultimatevideo-playlist-opacity"></div>
        <a class="ultimatevideo-playlist-btn-play" href="{permalink module='ultimatevideo.playlist' id=$aItem.playlist_id title=$aItem.title}"><i class="ynicon yn-play"></i></a>
    </div>

    <div class="ultimatevideo-playlist-info">
        <a class="ultimatevideo-video-title" href="{permalink module='ultimatevideo.playlist' id=$aItem.playlist_id title=$aItem.title}">{$aItem.title|clean}</a>
        <div class="ultimatevideo-video-owner ultimatevideo-separators">
            <span>{_p('by')} {$aItem|user}</span>
            <span> {_p('Category')}: <a {if $aItem.category_id !=0}href="{permalink module='ultimatevideo.playlist.category' id=$aItem.category_id title=$aItem.category_name}"{/if}>{softPhrase var=$aItem.category_name}</a></span>
        </div>
        <span class="ultimatevideo-total">
            <b>{$aItem.total_video}</b> {if $aItem.total_video == 1}{_p('video')}{else}{_p('videos')}{/if}
        </span>
        <div class="ultimatevideo-video-stats ultimatevideo-separators">
            {if isset($bShowTotalView) && $bShowTotalView}
            <span>
                <b>{$aItem.total_view}</b> {if $aItem.total_view == 1}{_p('view')}{else}{_p('views')}{/if}
            </span>
            {/if}
            {if isset($bShowTotalLike) && $bShowTotalLike}
            <span>
                <b>{$aItem.total_like}</b> {if $aItem.total_like == 1}{_p('like')}{else}{_p('likes')}{/if}
            </span>
            {/if}
            {if isset($bShowTotalComment) && $bShowTotalComment}
            <span>
                <b>{$aItem.total_comment}</b> {if $aItem.total_comment == 1}{_p('comment')}{else}{_p('comments')}{/if}
            </span>
            {/if}
        </div>
    </div>
</div>