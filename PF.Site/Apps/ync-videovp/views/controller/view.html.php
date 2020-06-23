<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ync-videovp-container">
    <div class="ync-videovp-block-content "> <!-- photo block (left)-->
        <div class="videovp_view dont-unbind" data-photo-id="{$aItem.photo_id}">
            {if isset($aTitleLabel) && isset($aTitleLabel.type_id) && isset($aTitleLabel.label) &&
            count($aTitleLabel.label)}
                <div class="{$aTitleLabel.type_id}-icon">
                    {foreach from=$aTitleLabel.label key=sKey item=aLabel}
                        <div class="sticky-label-icon title-label sticky-{$sKey}-icon" title="{_p var=$sKey}">
                            <span class="ico ico-{$aLabel.icon_class}"></span>
                            <span class="{if isset($aLabel.title_class)}{$aLabel.title_class}{/if}">{$aLabel.title}</span>
                        </div>
                    {/foreach}
                </div>
            {/if}
            <div class="ync-videovp-image-holder">
                {$aItem.embed_code}
            </div>
            {if PHPFOX_IS_AJAX_PAGE}
                <span class="_a_back"><i class="ico ico-arrow-left"></i>{_p var='back'}</span>
            {/if}

            {literal}
                <script>
                    var preLoadImages = false;
                    var preSetActivePhoto = false;
                </script>
            {/literal}
        </div>
        <div class="ync-videovp-content-info">
            <div class="ync-videovp-title-container">
                <div class="item-title">
                    <span>{$aItem.title}</span>
                </div>
                <div class="item-size-videovp">
                    <div class="item-size-wrapper">
                        {if $aItem.duration}
                            <div class="item-stat">
                                {$aItem.duration}
                            </div>
                        {/if}
                        {if $aItem.sHtmlCategories}
                            <div class="item-stat item-category">
                                {_p var="Categories"}: {$aItem.sHtmlCategories}
                            </div>
                        {/if}
                        {if !empty($aItem.breadcrumb)}
                            <div class="item-stat item-category">
                                {_p var="Categories"}:
                                {foreach from=$aItem.breadcrumb name=breadcrumbs item=aBredcrumb}
                                    {if $phpfox.iteration.breadcrumbs != 1} &raquo; {/if}
                                    <a href="{$aBredcrumb.1}">{$aBredcrumb.0}</a>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="videovp_actions">
                <ul>
                    {if $aItem.video_type == 'ultimatevideo'}
                        {template file='yncvideovp.block.addto'}
                    {/if}
                    <li class="videos-edit-more js_ync_videovp_edit_more" title="{_p var='more_actions'}">
                        <div class="dropup dropdown-container">
                            <span role="button" data-toggle="dropdown" class="item_bar_action">
                                <i class="ico ico-navbar mr-1"></i>{_p var="options"}
                            </span>
                            <ul class="dropdown-menu dropdown-menu-right">
                                {template file='yncvideovp.block.menu'}
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div> <!-- end photo block -->
    <div class="ync-videovp-block-info">
        <div class="ync-videovp-block-info-inner">
            <div class="ync_videovp_content_form">
                <div class="item_view">
                    <div class="header-info">
                        {img user=$aItem suffix='_50_square'}
                        <div class="item_info_author">
                            <div class="item-user">{$aItem|user:'':'':50}</div>
                            <div class="item-time">{$aItem.time_stamp|convert_time}</div>
                        </div>
                    </div>
                    {if $aItem.view_id == 2}
                        {template file='core.block.pending-item-action'}
                    {/if}
                    <div class="videovp-title-text">
                        {$aItem.title}
                    </div>
                    {if $aItem.total_view > 0}
                        <div class="videovp-total-stat">
                            <div class="videovp-total-view videovp-total-stat-item">
                                <span>{$aItem.total_view|short_number}</span>{if $aItem.total_view == 1} {_p var='view_lowercase'}{else} {_p var='views_lowercase'}{/if}
                            </div>
                            {if $aItem.total_favorite}
                                <div class="videovp-total-favorite videovp-total-stat-item">
                                    <span>{$aItem.total_favorite|short_number}</span>{if $aItem.total_favorite == 1} {_p
                                    var='favorite_lowercase'}{else} {_p var='favorites_lowercase'}{/if}
                                </div>
                            {/if}
                        </div>
                    {/if}
                    {if $aItem.text}
                        <div class="videovp-description-text item_view_content">
                            <span>
                                {$aItem.text|parse|shorten:100:'feed.view_more':true|split:55|max_line}
                            </span>
                        </div>
                    {/if}
                    {if $aItem.embed || (Phpfox::isUser() && ($aItem.video_type == 'ultimatevideo'))}
                        <div class="videovp-embed-container"> <!-- Embed code and Invite friends buttons-->
                            {if Phpfox::isUser() && ($aItem.video_type == 'videochannel')}
                                <a href="javascript:void(0)" data-video_id="{$aItem.video_id}"
                                   class="videovp-star-vote{if $aItem.bIsFavourite} voted{/if}">
                                    <i class="ico ico-star-o"></i>
                                </a>
                            {/if}

                            {if $aItem.embed}
                                <span class="item-embed">
                                <a href="javascript:void(0)" class="js_yncvideovp_get_embed_code collapsed"
                                   data-toggle="collapse" data-target="#yncvideovp_embed_code">
                                    {_p var='embed_code'}
                                </a>
                            </span>
                            {/if}
                            {if (Phpfox::isUser() && ($aItem.video_type == 'ultimatevideo'))}
                                <span class="item-invite">
                                <a title="Invite Friends" class="popup"
                                   href="{permalink module='ultimatevideo.invite' id=$aItem.video_id type=1}">
                                    {_p('invite_friends')}
                                </a>
                            </span>
                            {/if}
                        </div>
                    {/if}
                    <div id="yncvideovp_embed_code" class="collapse videovp-embed-collapse-content">
                        <textarea id="yncvideovp_embed_code_value" readony class="disabled" rows="3"><iframe
                                    src="{$aItem.embed}" width="525" height="525"
                                    style="overflow:hidden;"></iframe></textarea>
                        <div class="videovp-embed-btn-group">
                            <button type="button" class="btn btn-sm btn-default" data-toggle="collapse"
                                    data-target="#yncvideovp_embed_code">
                                {_p('Close')}
                            </button>
                            <button type="button" id="yncvideovp_copy_embed_code"
                                    class="btn btn-sm btn-primary yncvideovp_copy_embed_code"
                                    data-cmd="copy_embed_code" data-clipboard-target="#yncvideovp_embed_code_value">
                                {_p('copy_code')}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="item-addthis">
                    {addthis url=$aItem.link title=$aItem.title description=$sShareDescription}
                </div>
                <div class="ync-videovp-comment-container">
                    <div class="item-detail-feedcomment">
                        {module name='feed.comment'}
                    </div>
                </div>
            </div>
            <div class="ync_videovp_edit_form"> <!-- edit form container-->
            </div>
        </div>
        <div class="ync-videovp-block-info-bottom">
        </div>
    </div> <!-- end edit form -->
</div>
<input type="hidden" id="js_ync_videovp_slink" value="{$sLink}">

{if $aItem.video_type == 'ultimatevideo'}
{literal}
    <script>
        $Core.loadStaticFile(oParams['sBaseURL'].replace('index.php/', '') + 'PF.Site/Apps/YouNet_UltimateVideos/assets/jscript/clipboard.min.js');
        if (typeof Clipboard !== 'undefined') {
            window.setTimeout(function () {
                new Clipboard('.yncvideovp_copy_embed_code');
            }, 1000);
        }
    </script>
{/literal}
{/if}