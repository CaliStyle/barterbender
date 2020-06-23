<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 03/01/2017
 * Time: 11:21
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<script>
    oTranslations['save'] = "{_p var='save'}";
    oTranslations['saved'] = "{_p var='saved'}";
</script>
<div class="p-detail-container p-advblog-detail-container">
    <div class="p-detail-main-content">
        <div class="p-detail-statistic-wrapper">
            <div class="p-detail-statistic-list">
                {if $aItem.total_view}
                <span class="item-statistic">
                    {$aItem.total_view} <span class="p-text-lowercase">{if $aItem.total_view == 1}{_p var='view'}{else}{_p var='views'}{/if}</span>
                </span>
                {/if}
                {if $aItem.total_like}
                <span class="item-statistic">
                    {$aItem.total_like} <span class="p-text-lowercase">{if $aItem.total_like == 1}{_p var='like'}{else}{_p var='likes'}{/if}</span>
                </span>
                {/if}
                {if $aItem.total_comment}
                <span class="item-statistic">
                    {$aItem.total_comment} <span class="p-text-lowercase">{if $aItem.total_comment == 1}{_p var='comment'}{else}{_p var='comments'}{/if}</span>
                </span>
                {/if}
            </div>
        </div>
        <div class="p-detail-action-wrapper">
            <div class="p-detail-action-list">
                {if isset($aItem.is_favorite) && $aItem.is_favorite}
                    <div id="ynadvblog-detail-favorite-blog-{$aItem.blog_id}" class="item-action">
                        <a title="{_p('favorited')}" class="btn btn-default btn-sm btn-icon" onclick="$Core.ajaxMessage();ynadvancedblog.updateFavorite({$aItem.blog_id},0);return false;">
                            <i class="ico ico-star"></i><span class="item-text p-text-capitalize">{_p var='favorited'}</span>
                        </a>
                    </div>
                {elseif !empty($aItem.bCanFavorite)}
                    <div id="ynadvblog-detail-favorite-blog-{$aItem.blog_id}" class="item-action">
                        <a title="{_p('favorite')}" class="btn btn-default btn-sm btn-icon" onclick="$Core.ajaxMessage();ynadvancedblog.updateFavorite({$aItem.blog_id},1);return false;">
                            <i class="ico ico-star-o"></i><span class="item-text p-text-capitalize">{_p var='favorite'}</span>
                        </a>
                    </div>
                {/if}

                {if Phpfox::isUser() && $aItem.post_status == 'public' && $aItem.is_approved}
                    {if $aItem.is_saved}
                        <div class="item-action">
                            <a href="javascript:void(0);" class="btn btn-default btn-sm btn-icon js_p_blog_save_btn" onclick="$Core.ajaxMessage();ynadvancedblog.updateSavedBlog({$aItem.blog_id},0);return false;">
                                <i class="ico ico-bookmark"></i><span class="item-text p-text-capitalize">{_p var='saved'}</span>
                            </a>
                        </div>
                    {else}
                        <div class="item-action">
                            <a href="javascript:void(0);" class="btn btn-default btn-sm btn-icon js_p_blog_save_btn" onclick="$Core.ajaxMessage();ynadvancedblog.updateSavedBlog({$aItem.blog_id},1);return false;">
                                <i class="ico ico-bookmark-o"></i><span class="item-text p-text-capitalize">{_p var='save'}</span>
                            </a>
                        </div>
                    {/if}
                {/if}

            </div>
        </div>
        {if $aItem.is_approved != 1}
        {template file='core.block.pending-item-action'}
        {/if}
        <div class="p-detail-author-wrapper">
            <div class="p-detail-author-image">{img user=$aItem suffix='_50_square'}</div>
            <div class="p-detail-author-info">
                <span class="item-author"><span class="item-text-label">{_p var='by'}</span> {$aItem|user:'':'':50:'':'author'}</span>
                <span class="item-time">{_p var='published'} <span class="p-text-lowercase">{_p var='on'}</span> {$aItem.time_stamp|convert_time:'core.global_update_time'}</span>
            </div>
            {if !empty($aItem.permission_enable)}
            <div class="p-detail-option-manage">
                <div class="dropdown">
                    <a data-toggle="dropdown" class="p-option-button"><i class="ico ico-gear-o"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right" >
                        {template file='ynblog.block.link'}
                    </ul>
                </div>
            </div>
            {/if}
        </div>
        <div class="p-detail-content-wrapper ">
            <div class="">
                {if $aItem.is_hidden == 0 && $aItem.image_path}
                <div class="p-advblog-detail-main-image">
                    <span title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" class="item-main-media-src" style="background-image: url(<?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aItem']['image_path'], $this->_aVars['aItem']['server_id'], '_1024', $this->_aVars['aItem']['is_old_suffix']); ?>)">
                    </span>
                </div>
                {/if}
                {if !empty($aItem.text)}
                    <div class="item-desc p-advblog-detail-desc-content item_view_content">
                        {$aItem.text}
                    </div>
                {/if}
                {module name='attachment.list' sType=ynblog iItemId=$aItem.blog_id}
                <div class="p-detail-type-info">
                    {if isset($aItem.tag_list)}
                    <div class="p-type-info-item">
                        <div class="p-tag">
                            <span class="p-item-label">{_p var='tags'}:</span>
                            <div class="p-item-content">{$sTags}</div>
                        </div>
                    </div>
                    {/if}
                    {if (isset($sCategories) && $sCategories)}
                        <div class="p-type-info-item">
                            <div class="p-category">
                                <span class="p-item-label">{_p var='categories'}:</span>
                                <div class="p-item-content">{$sCategories}</div>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>

        </div>

    </div>
    <div class="p-detail-bottom-content">
        <div class="p-detail-addthis-wrapper">
            <div class="p-detail-addthis">
            {addthis url=$aItem.bookmark_url title=$aItem.title}
            </div>
            <div class="p-detail-minor-action">
                <a data-caption="HTML Code" title="HTML Code" class="p-btn-minor-action" onclick="$(this).closest('.p-detail-bottom-content').find('.advanced_blog_html_code_block').toggleClass('hide'); if($('.advanced_blog_html_code_block textarea').length){l} $('.advanced_blog_html_code_block textarea').get(0).select();{r}">
                    <i class="ico ico-code"></i>{_p('embed_code')}
                </a>

            </div>
        </div>
        <div class="advanced_blog_html_code_block hide mb-2">
            <textarea id="ynadvblog_html_code_value" readony class="form-control disabled"><iframe width="500" height="550" src="{$sUrl}"></iframe></textarea>

            <div class="p-form-group-btn-container form-group-btn-align-end mt-1">
                <button type="button" onclick="$(this).parents('.advanced_blog_html_code_block').toggleClass('hide');" class="btn btn-sm btn-default">
                    {_p var='close'}
                </button>
                <button type="button" data-cmd="copy_embed_code" class="yns-copy-btn btn btn-sm btn-primary" data-clipboard-target="#ynadvblog_html_code_value">
                    {_p var='ynblog_copy_code'}
                </button>
            </div>
        </div>
        {if $aItem.is_approved == 1}
            <div class="item-detail-feedcomment p-detail-feedcomment">
                {module name='feed.comment'}
            </div>
        {/if}
    </div>
</div>





<script type="text/javascript" src="{$appPath}/assets/jscript/add.js"></script>
{literal}
    <script>
        $Behavior.loadClipboardJs = function(){
            var eles =  $('.yns-copy-btn');
            if(eles.length){
                if(typeof Clipboard == 'undefined'){
                    $Core.loadStaticFile('{/literal}{$appPath}{literal}/assets/jscript/clipboard.min.js');
                    window.setTimeout(function(){
                        new Clipboard('.yns-copy-btn');
                    },2000);
                }else{
                    new Clipboard('.yns-copy-btn');
                }
            }
        };
    </script>
{/literal}