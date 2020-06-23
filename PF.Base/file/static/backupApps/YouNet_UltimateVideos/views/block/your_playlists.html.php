<div class="ultimatevideo-yourplaylist-container">
    <div class="ultimatevideo-yourplaylist-wrapper playlist-listing-type">
        <div class="ultimatevideo-yourplaylist-listing">
            <div class="item-playlist"><a href="{url link='ultimatevideo' view='later'}"><i class="ico ico-clock-o"></i>
                    <div class="item-name">{_p var='watch_later'}</div>
                    <div class="total-count">{$iTotalWatchLater}</div>
                </a></div>
            <div class="item-playlist"><a href="{url link='ultimatevideo' view='favorite'}"><i
                            class="ico ico-star-o"></i>
                    <div class="item-name">{_p var='favorite'}</div>
                    <div class="total-count">{$iTotalFavorite}</div>
                </a></div>
        </div>
    </div>
    {if count($aItems)}
        <div class="ultimatevideo-yourplaylist-wrapper playlist-listing-default">
            <div class="ultimatevideo-yourplaylist-listing">
                {foreach from=$aItems item=aItem}
                    <div class="item-playlist"><a
                                href="{permalink module='ultimatevideo.playlist' id=$aItem.playlist_id title=$aItem.title}"><i
                                    class="ico ico-list"></i>
                            <div class="item-name">{$aItem.title|clean}</div>
                            <div class="total-count">{$aItem.total_video|short_number}</div>
                        </a></div>
                {/foreach}
            </div>
        </div>
    {/if}
</div>