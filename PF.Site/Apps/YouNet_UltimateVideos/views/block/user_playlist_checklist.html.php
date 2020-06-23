{if count($aItems)}
    <div class="ynuv_quick_list_playlist_wrapper">
        {foreach from=$aItems name=playlist item=aItem}
            <div class="item-playlist-checkbox">
                <div class="checkbox p-checkbox-custom">
                    <label data-toggle="ultimatevideo" data-cmd="add_to_playlist" data-playlist="{$aItem.playlist_id}"
                           data-id="{$iVideoId}">
                        <input type="checkbox" {if $aItem.added_video}checked{/if} /><i
                                class="ico ico-square-o mr-1"></i> {$aItem.title} {if isset($aItem.privacy) && $aItem.privacy == 3}&nbsp;
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        {/if}
                    </label>
                </div>
            </div>
        {/foreach}
    </div>
{else}
    <span style="padding: 10px 15px; display:block">{_p('no_playlists_found')}</span>
{/if}