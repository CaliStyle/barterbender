<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 03/01/2017
 * Time: 11:21
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="ynadvblog_blog_detail" id="ynadvblog_add">
    <div class="ynadvblog_user_info">
        <div class="{if !empty($aItem.permission_enable)}pr-5{/if} clearfix">
            {img user=$aItem suffix='_50_square'}
            <div>
                <span>
                    <?php $this->_aVars['aItem']['time_stamp_display'] = Phpfox::getTime('D, M j, Y',$this->_aVars['aItem']['time_stamp']); ?>
                    {$aItem.time_stamp_display}
                </span>
                <span>{_p var='by'} {$aItem|user:'':'':50:'':'author'}</span>
            </div>
        </div>
        {if !empty($aItem.permission_enable)}
            <div class="ynadvblog_option dropdown">
                <span class="dropdown-toggle btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o hover" aria-hidden="true"></i>
                </span>
                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='ynblog.block.link'}
                </ul>
            </div>
        {/if}
    </div>

    <div class="ynadvblog_statistic_detail mb-2 mt-1">
        <div>
            {if $aItem.total_view == 1}
            <div class="fw-bold"><i class="fa fa-eye" aria-hidden="true"></i>{$aItem.total_view}</div><div class="font-small uppercase">{_p var='view'}</div>
            {else}
            <div class="fw-bold"><i class="fa fa-eye" aria-hidden="true"></i>{$aItem.total_view}</div><div class="font-small uppercase">{_p var='views'}</div>
            {/if}
        </div>
        <div>
            {if $aItem.total_like == 1}
            <div class="fw-bold"><i class="fa fa-thumbs-up" aria-hidden="true"></i>{$aItem.total_like}</div><div class="font-small uppercase">{_p var='like'}</div>
            {else}
            <div class="fw-bold"><i class="fa fa-thumbs-up" aria-hidden="true"></i>{$aItem.total_like}</div><div class="font-small uppercase">{_p var='likes'}</div>
            {/if}
        </div>
        <div>
            {if $aItem.total_comment == 1}
            <div class="fw-bold"><i class="fa fa-comments" aria-hidden="true"></i>{$aItem.total_comment}</div><div class="font-small uppercase">{_p var='comment'}</div>
            {else}
            <div class="fw-bold"><i class="fa fa-comments" aria-hidden="true"></i>{$aItem.total_comment}</div><div class="font-small uppercase">{_p var='comments'}</div>
            {/if}
        </div>
        <div>
            {if $aItem.total_share == 1}
            <div class="fw-bold"><i class="fa fa-share-alt" aria-hidden="true"></i>{$aItem.total_share}</div><div class="font-small uppercase">{_p var='share'}</div>
            {else}
            <div class="fw-bold"><i class="fa fa-share-alt" aria-hidden="true"></i>{$aItem.total_share}</div><div class="font-small uppercase">{_p var='shares'}</div>
            {/if}
        </div>
        <div>
            {if $aItem.total_favorite == 1}
            <div class="fw-bold"><i class="fa fa-star" aria-hidden="true"></i>{$aItem.total_favorite}</div><div class="font-small uppercase">{_p var='favorite'}</div>
            {else}
            <div class="fw-bold"><i class="fa fa-star" aria-hidden="true"></i>{$aItem.total_favorite}</div><div class="font-small uppercase">{_p var='favorites'}</div>
            {/if}
        </div>
    </div>

    {if $aItem.is_approved == 0 && $aItem.post_status == 'public'}
        <div class="message js_moderation_off" id="js_approve_message">
            {_p var='this_blog_is_pending_an_admins_approval'}
        </div>
    {/if}

    <div class="ynadvblog_avatar">
        <span title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" class="ynadvblog_cover_inner item_image{if empty($aItem.text)} full{/if}" style="background-image: url(<?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aItem']['image_path'], $this->_aVars['aItem']['server_id'], '_big'); ?>)">
        </span>
    </div>
    {if !empty($aItem.text)}
        <div class="item-desc item_content ynadvblog-content item_view_content">
            {$aItem.text}
        </div>
    {/if}

    {module name='attachment.list' sType=ynblog iItemId=$aItem.blog_id}

    {if isset($aItem.tag_list)}
        {module name='tag.item' sType=$sTagType sTags=$aItem.tag_list iItemId=$aItem.blog_id iUserId=$aItem.user_id sMicroKeywords='keywords'}
    {/if}

    <div class="ynadvblog_blog_share">
        {addthis url=$aItem.bookmark_url title=$aItem.title description=$aItem.text}
        <div class="ynadvblog_embed_favorite">
            <div class="ynadvblog-more-action ynadvblog-embedcode">
                <a data-caption="HTML Code" title="HTML Code" class="btn btn-success btn-sm" onclick="$(this).parent('div').toggleClass('open'); if($('.advanced_blog_html_code_block textarea').length){l} $('.advanced_blog_html_code_block textarea').get(0).select();{r}">{_p('Embed')}</a>

                <div class="dropdown-menu advanced_blog_html_code_block dropdown-menu-right">
                    <textarea id="ynadvblog_html_code_value" readony class="form-control disabled"><iframe width="500" height="550" src="{$sUrl}"></iframe></textarea>

                    <div class="text-right">
                        <button type="button" onclick="$(this).parents('.ynadvblog-embedcode').toggleClass('open');" class="btn btn-sm btn-default">
                            {_p('Close')}
                        </button>
                        <button type="button" data-cmd="copy_embed_code" class="yns-copy-btn btn btn-sm btn-primary" data-clipboard-target="#ynadvblog_html_code_value">
                            {_p('Copy code')}
                        </button>
                    </div>
                </div>
            </div>

            {if isset($aItem.is_favorite) && $aItem.is_favorite}
                <div id="ynadvblog-detail-favorite-blog-{$aItem.blog_id}">
                    <a title="{_p('favorited')}" class="btn btn-primary btn-sm" onclick="$Core.ajaxMessage();ynadvancedblog.updateFavorite({$aItem.blog_id},0);return false;">
                        <i class="fa fa-star-o" aria-hidden="true"></i>&nbsp;{_p var='favorited'}
                    </a>
                </div>
            {elseif !empty($aItem.bCanFavorite)}
                <div id="ynadvblog-detail-favorite-blog-{$aItem.blog_id}">
                    <a title="{_p('favorite')}" class="btn btn-default btn-sm" onclick="$Core.ajaxMessage();ynadvancedblog.updateFavorite({$aItem.blog_id},1);return false;">
                        <i class="fa fa-star" aria-hidden="true"></i>&nbsp;{_p var='favorite'}
                    </a>
                </div>
            {/if}
        </div>
    </div>


    {plugin call='ynblog.template_controller_view_end'}
    {if user('yn_advblog_comment')}
    <div {if $aItem.is_approved != 1 || $aItem.post_status != 'public'}style="display:none;" class="js_moderation_on"{/if}>
        {module name='feed.comment'}
    </div>
    {/if}
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