<div class="yndirectory-video-list">
	{if count($aVideos) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}

    <div class="item-container with-video1 video-listing1">
        <div class="clear"></div>
        {foreach from=$aVideos item=aItem name=videos}
        <div id="js_video_item_{$aItem.video_id}" class="video-item moderation_row js_video_parent"  data-uid="{$aItem.video_id}" >
            <div class="item-outer">
                <!-- image -->
                <a class="item-media-src" href="{$aItem.link}">
                    <span class="image_load" data-src="{$aItem.image_path}"></span>
                    <div class="item-icon">
                        {if isset($sView) && $sView == 'my' && $aItem.view_id != 0}
                        <div class="sticky-label-icon sticky-pending-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-clock-o"></i>
                        </div>
                        {/if}
                        {if $aItem.is_sponsor}
                        <!-- Sponsor -->
                        <div class="sticky-label-icon sticky-sponsored-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-sponsor"></i>
                        </div>
                        {/if}
                        {if $aItem.is_featured}
                        <!-- Featured -->
                        <div class="sticky-label-icon sticky-featured-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-diamond"></i>
                        </div>
                        {/if}
                    </div>
                </a>

                <div class="item-inner">
                    <!-- please show length video time -->
                    {if !empty($aItem.duration)}
                    <div class="item-video-length"><span>{$aItem.duration}</span></div>
                    {/if}
                    <!--  avatar user show when all video and hide when my vieo -->
                    {if isset($sView) && $sView != 'my'}
                    <div class="item-video-avatar">{img user=$aItem suffix='_50_square'}</div>
                    {/if}

                    <!-- title -->
                    <div class="item-title">
                        <a href="{$aItem.link}" id="js_video_edit_inner_title{$aItem.video_id}" class="link ajax_link" itemprop="url">
                            {$aItem.title|clean}
                        </a>
                    </div>
                    <!-- author -->
                    <div class="item-author dot-separate">
                        {if isset($sView) && $sView != 'my'}<span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>{/if}
                        <span>{_p var='on'} {$aItem.time_stamp|convert_time:'core.global_update_time'}</span>
                    </div>

                    {if !isset($bVideoView)}
                    <div class="total-view">
                    <span>
                        {$aItem.total_view} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                    </span>
                        <span>.</span>
                        <span>
                        {$aItem.total_like} {if $aItem.total_like == 1}{_p var='like_lowercase'}{else}{_p var='likes_lowercase'}{/if}
                    </span>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        {/foreach}
    </div>
	<div class="clear"></div>
	{module name='directory.paging'}	
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}