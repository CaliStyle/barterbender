
{if isset($aModuleView.photos) && $aModuleView.photos.is_show && count($aItemphoto)>0}
    <div id="yndirectory_detailnewestitem">
        <div class="yndirectory-detailnewestitem-photos block">
            <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_photo'}</div>
            <div class="content">
                <div class="photo-newest photo-newest-count-{$aNumberOfItem.photos}">
                {foreach from=$aItemphoto key=ikey item=item}
                    <div class="photo-item">

                        <!-- image -->
                        <a class="item-media-src" href="{$item.link}{if isset($iForceAlbumId)}albumid_{$iForceAlbumId}/{/if}{if isset($sPhotoCategory)}category_{$sPhotoCategory}/{/if}" title="{$item.title}">
                            <span {if $item.destination}style="background-image: url({if Phpfox::isModule('photo') || ( Phpfox::isModule('advancedphoto') && Phpfox::getParam('advancedphoto.delete_original_after_resize')) }
                                    {img server_id=$item.server_id path='photo.url_photo' file=$item.destination suffix='_240' max_width=150 max_height=150 return_url="true"}
                                {else}
                                    {img server_id=$item.server_id path='photo.url_photo' file=$item.destination suffix='_240' max_width=150 max_height=150 return_url="true"}
                                {/if})"{/if})>
                            </span>
                        </a>

                    </div>
                {/foreach}
                </div>
            </div>
            {if $aNumberOfItem.photos > 4}
            <div class="bottom">
                <a class="btn btn-block btn-default" href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}photos"> {phrase var='view_more'}</a>
            </div>
            {/if}
        </div>
    </div>
{/if}

{if isset($aModuleView.videos) && $aModuleView.videos.is_show && count($aItemVideoChannel)>0}
    <div class="yndirectory-detailnewestitem-videos block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_video_channel'}</div>
        <div class="content">
            <div class="item-container with-video1">
                <div class="videos-list">
                    {foreach from=$aItemVideoChannel item=aItem}
                    <div class="video-item">
                        <div class="item-outer">
                            <!-- image -->
                            <a class="item-media-src" href="{permalink module='videochannel' id=$aItem.video_id title=$aItem.title}">
                                <span class="image_load" data-src="{$aItem.image_path}"></span>
                                <div class="item-video-icon"><span class="ico ico-play"></span></div>
                            </a>
                            <div class="item-inner">
                                <!-- title -->
                                <div class="item-title">
                                    <a href="{permalink module='videochannel' id=$aItem.video_id title=$aItem.title}" id="js_video_edit_inner_title{$aItem.video_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean}</a>
                                </div>
                                <!-- author -->
                                <div class="item-info">
                                    {if !empty($aItem.duration)}
                                    <div class="item-video-length">{$aItem.duration}</div>
                                    {/if}
                                    <div class="item-author dot-separate">
                                        <span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                                    </div>
                                </div>
                                <div class="total-view">
                                    <span>
                                        {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
        {if $aNumberOfItem.videos > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}videos"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.v) && $aModuleView.v.is_show && count($aItemVideo)>0}
    <div class="yndirectory-detailnewestitem-videos block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_video'}</div>
        <div class="content">
            <div class="item-container with-video">
                <div class="videos-list">
                    {foreach from=$aItemVideo item=aItem}
                    <div class="video-item">
                        <div class="item-outer">
                            <!-- image -->
                            <a class="item-media-src" href="{permalink module='video/play' id=$aItem.video_id title=$aItem.title}">
                                <span class="image_load" data-src="{$aItem.image_path}"></span>
                                <div class="item-video-icon"><span class="ico ico-play"></span></div>
                            </a>
                            <div class="item-inner">
                                <!-- title -->
                                <div class="item-title">
                                    <a href="{permalink module='video/play' id=$aItem.video_id title=$aItem.title}" id="js_video_edit_inner_title{$aItem.video_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean}</a>
                                </div>
                                <!-- author -->
                                <div class="item-info">
                                    {if !empty($aItem.duration)}
                                    <div class="item-video-length">{$aItem.duration}</div>
                                    {/if}
                                    <div class="item-author dot-separate">
                                        <span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                                    </div>
                                </div>
                                <div class="total-view">
                                    <span>
                                        {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
        {if $aNumberOfItem.v > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}v"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.ultimatevideo) && $aModuleView.ultimatevideo.is_show && count($aItemUltimateVideo)>0}
    <div class="yndirectory-detailnewestitem-videos block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_ultimate_video'}</div>
        <div class="content">

            <div class="item-container with-video1">
                <div class="videos-list">
                    {foreach from=$aItemUltimateVideo item=aItem}
                    <div class="video-item">
                        <div class="item-outer">
                            <!-- image -->
                            <a class="item-media-src" href="{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title}">
                                <span class="image_load" data-src="{$aItem.image_path}"></span>
                                <div class="item-video-icon"><span class="ico ico-play"></span></div>
                            </a>
                            <div class="item-inner">
                                <!-- title -->
                                <div class="item-title">
                                    <a href="{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title}" id="js_video_edit_inner_title{$aItem.video_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean}</a>
                                </div>
                                <!-- author -->
                                <div class="item-info">
                                    {if !empty($aItem.duration)}
                                    <div class="item-video-length">{$aItem.duration}</div>
                                    {/if}
                                    <div class="item-author dot-separate">
                                        <span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                                    </div>
                                </div>
                                <div class="total-view">
                                    <span>
                                        {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
        {if $aNumberOfItem.ultimatevideo > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}ultimatevideo"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}


{if isset($aModuleView.musics) && $aModuleView.musics.is_show && count($aItemMusic)>0}
    <div class="yndirectory-detailnewestitem-musics block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_song'}</div>
        <div class="content">
            <div class="music-widget-block item-container list-view music">
                {foreach from=$aItemMusic item=aSong}

                    <div class="music_row album-item" data-songid="{$aSong.song_id}" xmlns="http://www.w3.org/1999/html">
                        <div class="item-outer song">
                            <div class="item-media">
                                <div class="item-media-inner">
                                    <span class="music-bg-thumb thumb-border" onclick="$Core.music.playSongRow(this)" style="background-image: url(
                                            {if $aSong.image_path}
                                                {img return_url="true" server_id=$aSong.image_server_id path='music.url_image' file=$aSong.image_path suffix=''}
                                            {else}
                                               {$sMusicPath}
                                            {/if}
                                        )"><span class="music-overlay"><i class="ico ico-play-circle-o"></i></span></span>
                                </div>
                            </div>

                            <div class="item-inner song">
                                <div class="item-title">
                                    <a href="{permalink title=$aSong.title id=$aSong.song_id module='music'}">{$aSong.title|clean}</a>
                                </div>

                                <div class="item-statistic">
                                    <span>{_p var='by'} {$aSong|user}</span>

                                    <span>
                                        {if $aSong.total_play != 1}
                                            {_p var='music_total_plays' total=$aSong.total_play|short_number}
                                        {else}
                                            {_p var='music_total_play' total=$aSong.total_play|short_number}
                                        {/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="item-player music_player hide">
                            <div class="audio-player dont-unbind-children js_player_holder">
                                <audio class="js_song_player" src="{$aSong.song_path}" type="audio/mp3" controls="controls"></audio>
                            </div>
                        </div>
                    </div>

                {/foreach}
            </div>
        </div>
        {if $aNumberOfItem.musics > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}musics"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.blogs) && $aModuleView.blogs.is_show && count($aItemBlog)>0}
    <div class="yndirectory-detailnewestitem-blogs block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_blog'}</div>
        <div class="content">
            <div class="item-container with-blog">
            {foreach from=$aItemBlog item=aItem}
                <div class="blog-item">
                    <div class="item-outer">
                        {if !empty($aItem.image_path)}
                            <!-- image -->
                            <a class="item-media-src" href="{permalink module='blog' id=$aItem.blog_id title=$aItem.title}">
                                <span style="background-image: url(
                                {if $aItem.image_path}
                                    {img server_id=$aItem.server_id path='core.url_pic' file='blog/'.$aItem.image_path suffix='_1024' return_url=true}
                                {/if}
                                )"></span>
                            </a>
                        {/if}
                        <div class="item-inner">
                            <!-- title -->
                            <div class="item-title">
                                <a href="{if empty($aItem.sponsor_id)}{permalink module='blog' id=$aItem.blog_id title=$aItem.title}{else}{url link='ad.sponsor' view=$aItem.sponsor_id}{/if}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
                            </div>
                            <!-- author -->
                            <div class="item-author dot-separate">
                                <span>{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                            </div>
                            <!-- description -->
                            <div class="item-desc item_content">
                                {$aItem.text|striptag|stripbb|highlight:'search'|split:500|shorten:100:'...'}
                            </div>
                            <div class="total-view">
                                <span>
                                    {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
            </div>
        </div>
        {if $aNumberOfItem.blogs > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}blogs"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if $bShow && count($aItemAdvBlog)>0}
    <div class="yndirectory-detailnewestitem-blogs block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_advanced_blog'}</div>
        <div class="content">
            <div class="item-container with-blog">
                {foreach from=$aItemAdvBlog item=aItem}
                <div class="blog-item">
                    <div class="item-outer">
                        {if !empty($aItem.image_path)}
                            <!-- image -->
                            <a class="item-media-src" href="{permalink module='advanced-blog' id=$aItem.blog_id title=$aItem.title}">
                                <span style="background-image: url(
                                {if $aItem.image_path}
                                    {img server_id=$aItem.server_id path='core.url_pic' file='ynadvancedblog/'.$aItem.image_path suffix='_grid' return_url=true}
                                {/if}
                                )"></span>
                            </a>
                        {/if}
                        <div class="item-inner">
                            <!-- title -->
                            <div class="item-title">
                                <a href="{if empty($aItem.sponsor_id)}{permalink module='advanced-blog' id=$aItem.blog_id title=$aItem.title}{else}{url link='ad.sponsor' view=$aItem.sponsor_id}{/if}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
                            </div>
                            <!-- author -->
                            <div class="item-author dot-separate">
                                <span>{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                            </div>
                            <!-- description -->
                            <div class="item-desc item_content">
                                {$aItem.text|striptag|stripbb|highlight:'search'|split:500|shorten:100:'...'}
                            </div>
                            <div class="total-view">
                                <span>
                                    {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
        {if $aNumberOfItem.ynblog > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}advanced-blog"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.polls) && $aModuleView.polls.is_show && count($aItemPoll)>0}
    <div class="yndirectory-detailnewestitem-polls block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_poll'}</div>
        <div class="content">
            <div class="item-container with-blog">
                {foreach from=$aItemPoll item=aItem}
                <div class="blog-item">
                    <div class="item-outer">
                        {if !empty($aItem.image_path)}
                        <!-- image -->
                        <a class="item-media-src" href="{permalink module='poll' id=$aItem.poll_id title=$aItem.question}">
                            <span style="background-image: url(
                            {if $aItem.image_path}
                                {img server_id=$aItem.server_id path='poll.url_image' file=$aItem.image_path suffix='' return_url=true}
                            {/if}
                            )"></span>
                        </a>
                        {/if}
                        <div class="item-inner">
                            <!-- title -->
                            <div class="item-title">
                                <a href="{permalink module='poll' id=$aItem.poll_id title=$aItem.question}" title="{$aItem.question|clean}">{$aItem.question|clean}</a>
                            </div>
                            <!-- author -->
                            <div class="item-author dot-separate">
                                <span>{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                            </div>
                            <div class="total-view">
                            <span>
                                {$aItem.voted|short_number} {if $aItem.voted == 1}{_p var='vote_lowercase'}{else}{_p var='votes_lowercase'}{/if}
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
        {if $aNumberOfItem.polls > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}polls"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.coupons) && $aModuleView.coupons.is_show && count($aItemCoupon)>0}
    <div class="yndirectory-detailnewestitem-coupons block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_coupon'}</div>

        <div class="content">
            <div class="item-container with-blog">
                {foreach from=$aItemCoupon item=aItem}
                <div class="blog-item">
                    <div class="item-outer">
                        {if !empty($aItem.image_path)}
                        <!-- image -->
                        <a class="item-media-src" href="{permalink module='coupon' id=$aItem.coupon_id title=$aItem.title}" title="{$aItem.title|clean}">
                            <span style="background-image: url(
                            {if $aItem.image_path}
                                {img server_id=$aItem.server_id path='core.url_pic' file=$aItem.image_path suffix='_200' return_url=true}
                            {/if}
                            )"></span>
                        </a>
                        {/if}
                        <div class="item-inner">
                            <!-- title -->
                            <div class="item-title">
                                <a href="{permalink module='coupon' id=$aItem.coupon_id title=$aItem.title}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
                            </div>
                            <!-- author -->
                            <div class="item-author dot-separate">
                                <span>{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                            </div>
                            {if isset($aItem.discount) }<p>{phrase var='coupon.discount'} {$item.discount}</p> {/if}
                            {if isset($aItem.special_price) }<p>{phrase var='coupon.special_price'} : {$aItem.special_price}</p> {/if}
                            <div class="total-view">
                            <span>
                                {$aItem.total_claim|short_number} {if $aItem.total_claim == 1}{_p var='claim_lowercase'}{else}{_p var='claims_lowercase'}{/if}
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
        {if $aNumberOfItem.coupons > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}coupons"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.events) && $aModuleView.events.is_show && count($aItemEvent)>0}
    <div class="yndirectory-detailnewestitem-events block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_event'}</div>
        <div class="content">
            {if !$iAdvEvent}
            <div class="event-featured-container event-mini-block-container">
                <div class="event-mini-block-content">
            {/if}
            {foreach from=$aItemEvent item=aEvent name=aItem}
                {if $iAdvEvent}
                <div class="ynfevent-block-item">
                    <div class="ynfevent-block-item-top">
                        <a href="{permalink module='fevent' id=$aEvent.event_id title=$aEvent.title}" title="{$aEvent.title|clean}" class="ynfevent-block-item-photo" style="background-image: url('{$aEvent.url_photo}');"></a>
                    </div>
                    <div class="ynfevent-block-item-top-info">
                        <a href="{permalink module='fevent' id=$aEvent.event_id title=$aEvent.title}" class="ynfevent-block-item-title" title="{$aEvent.title|clean}">{$aEvent.title|clean|shorten:50:'...'|split:20}</a>
                        <div class="space-left text-gray-dark ynfevent-block-item-owner mt-1 fz-12"><i class="ico ico-user1-o"></i>{_p var='fevent.by'} {$aEvent|user}</div>
                        <time class="mt-h1 d-block space-left">
                            <i class="ico ico-calendar-star-o text-gray-dark"></i><span class="fw-bold fz-12">{$aEvent.d_end_time}&nbsp;-&nbsp;{$aEvent.short_end_time}</span>
                        </time>
                    </div>
                </div>
                {else}
                    {template file='event.block.mini-entry'}
                {/if}
            {/foreach}
            {if !$iAdvEvent}
                </div>
            </div>
            {/if}
        </div>
        {if $aNumberOfItem.events > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}events"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.jobs) && $aModuleView.jobs.is_show && count($aItemJobs)>0}
    <div class="yndirectory-detailnewestitem-jobs block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_job'}</div>

        <div class="content">
            <div class="item-container with-blog">
                {foreach from=$aItemJobs item=aItem}
                <div class="blog-item">
                    <div class="item-outer">
                        {if !empty($aItem.image_path)}
                        <!-- image -->
                        <a class="item-media-src" href="{permalink module='jobposting' id=$aItem.job_id title=$aItem.title}" title="{$aItem.title|clean}">
                            <span style="background-image: url(
                            {if $aItem.image_path}
                                {img server_id=$aItem.server_id path='core.url_pic' file='jobposting/'.$aItem.image_path suffix='_240' return_url=true}
                            {/if}
                            )"></span>
                        </a>
                        {/if}
                        <div class="item-inner">
                            <!-- title -->
                            <div class="item-title">
                                <a href="{permalink module='jobposting' id=$aItem.job_id title=$aItem.title}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
                            </div>
                            <!-- author -->
                            <div class="item-author dot-separate">
                                <span>{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                            </div>
                            <div class="total-view">
                            <span>
                                {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>

        {if $aNumberOfItem.jobs > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}jobs"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}

{if isset($aModuleView.marketplace) && $aModuleView.marketplace.is_show && count($aItemMarketplace)>0}
    <div class="yndirectory-detailnewestitem-marketplace block">
        <div class="yndirectory-detailnewestitem-header title">{phrase var='newest_listing'}</div>
        <div class="content">
            <ul class="ynmarketplace-most-view-block ync-listing-container-mini ync-list-layout">
                {foreach from=$aItemMarketplace key=iKey item=aListing}
                    <li class="ynmarketplace-most__item ync-item">
                        <div class="item-outer">
                            {if $aListing.image_path != NULL}
                                <a href="{$aListing.url}" title="{$aListing.title|parse|clean}" class="item-media-src">
                                    {if $iAdvancedMarketplace}
                                        <span style="background-image: url(
                                        {img server_id=$aListing.server_id title=$aListing.title path='advancedmarketplace.url_pic' file=$aListing.image_path  suffix='_400_square' return_url=true}
                                    );"></span>
                                    {else}
                                        <span style="background-image: url(
                                        {img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path  suffix='_400_square' return_url=true}
                                    );"></span>
                                    {/if}

                                </a>
                            {/if}

                            <div class="item-inner">
                                <div class="item-title"><a href="{$aListing.url}" title="{$aListing.title|parse|clean}">{$aListing.title}</a></div>
                                <p class="item-price mt-h1 text-warning mb-0">
                                    {if $aListing.price == '0.00'}
                                    {phrase var='free'}
                                    {else}
                                    {$aListing.currency_id|currency_symbol}{$aListing.price}
                                    {/if}
                                </p>
                                {if $aListing.total_view > 0}
                                <div class="total-view">
                                    <span>{$aListing.total_view} {if (int)$aListing.total_view > 1}{phrase var='views'}{else}{phrase var='one_view'}{/if}</span>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </li>
                {/foreach}
            </ul>
        </div>
        {if $aNumberOfItem.marketplace > 4}
        <div class="bottom">
            <a href="{permalink module='directory.detail' id=$iBusinessId title=$sBusinessTitle}marketplace"> {phrase var='view_more'}</a>
        </div>
        {/if}
    </div>
{/if}
