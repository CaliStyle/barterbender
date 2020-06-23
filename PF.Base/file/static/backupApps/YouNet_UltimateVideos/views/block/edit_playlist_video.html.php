<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 8/24/16
 * Time: 09:11
 */
defined('PHPFOX') or exit('NO DICE!');

?>
<script type="text/javascript">
    oTranslations['video'] = "{_p var='video_lc'}";
    oTranslations['videos'] = "{_p var='videos_lc'}";
</script>

{if count($aVideos)}
    <div class="ultimatevideo-manage-video-header">
    <span id="ultimatevideo_select_all" class="ultimatevideo-select-none">
        <i class="icon-custom"></i>{_p var='select_all'}
    </span>
        <a class="p-text-danger item-remove-all" href="javascript:void(0);" id="ultimatevideo_remove_selected"
           style="display: none;">
            <i class="ico ico-close-circle-o"></i>
            {_p var='remove'} <span class="ultimatevideo_count"></span> <span class="ultimatevideo_count_label"></span>
        </a>
    </div>
    <form method="post" action="{url link='ultimatevideo.addplaylist' id=$aForms.playlist_id video=true}" onsubmit="">
        <div class="table">
            <div class="sortable">
                <ul class="ultimatevideo-dragdrop ui-sortable ultimatevideo-manage-video-container">
                    {foreach from=$aVideos name=video item=aItem}
                        <li class="ui-sortable-handle ultimatevideo-manage-video-item">
                            <div class="item-outer">
                                <input type="hidden" name="order[{$aItem.video_id}]" value="{$aItem.ordering}"
                                       class="js_mp_order"/>
                                <div class="item-checkbox">
                                    <div class="checkbox p-checkbox-custom">
                                        <label>
                                            <input type="checkbox" class="ultimatevideo_remove"><i
                                                    class="ico ico-square-o"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="item-media">
                                    <span {if $aItem.image_path}style="background-image: url({if $aItem.image_server_id == -1}{$aItem.image_path}{else}{img server_id=$aItem.image_server_id path='core.url_pic' file=$aItem.image_path suffix='_500' return_url=true}{/if})"
                                          {else}style="background-image: url({param var='core.path_actual'}PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_video.jpg)"{/if}></span>
                                </div>
                                <div class="item-inner">
                                    <div class="item-title">
                                        {$aItem.title}
                                    </div>
                                    <div class="item-info p-seperate-dot-wrapper">
                                        <span class="p-seperate-dot-item"><span
                                                    class="p-text-capitalize">{_p var='by'}</span> <span
                                                    class="item-name">{$aItem|user}</span></span>
                                        {if $aItem.total_view}
                                            <span class="p-seperate-dot-item">{$aItem.total_view|short_number} <span
                                                        class="p-text-lowercase">{if $aItem.total_view == 1}{_p var='view'}{else} {_p var='views'}{/if}</span></span>
                                        {/if}
                                        {if $aItem.duration}
                                            <span class="p-seperate-dot-item">{$aItem.duration|ultimatevideo_duration}</span>
                                        {/if}
                                    </div>
                                </div>
                                <div class="item-action">
                                    <a class="ultimatevideo-dragdrop-remove" data-toggle="ultimatevideo"
                                       data-cmd="remove_video_from_playlist" data-video="{$aItem.video_id}"><i
                                                class="ico ico-close-circle-o"></i></a>
                                    <a href="#" class="js_drop_down item-drag"><i class="ico ico-arrows-move"
                                                                                  aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
        <input type="hidden" name="removed" id="ynuv_removed_video" value="">
        <div class="p-form-group-btn-container ultimatevideo-manage-video-form-group-btn">
            <input type="submit" value="{_p('Update')}" class="btn btn-primary"/>
        </div>
    </form>
{else}
    <div class="ultimatevideo-playlist-manage-empty">
        <div class="item-icon"><i class="ico ico-videocam-o"></i></div>
        <div class="p-mb-line">{_p var='have_no_any_videos_in_this_playlist'}</div>
        <div><a href="{url link='ultimatevideo'}">{_p var='discover_more_videos_and_add_to_playlist'}</a></div>
    </div>
{/if}
