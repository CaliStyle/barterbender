<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="p-item p-advblog-comment-item">
    <div class="item-outer">
        <div class="item-inner">
            <div class="item-comment-content">
                <div class="item-icon">
                    <i class="ico ico-quote-alt-left"></i>
                </div>
                <div class="item-text">
                    <?php $this->_aVars['aLastComment'] = Phpfox::getService('ynblog.blog')->getLastCommentByBlogId($this->_aVars['aItem']['blog_id'], $this->_aVars['aItem']['latest_comment']); ?>
                    {if !empty($aLastComment)}
                        {$aLastComment.text|feed_strip|split:500|shorten:200:'...'}
                    {/if}
                </div>
            </div>
            <div class="p-item-minor-info p-seperate-dot-wrapper">
                <span class="p-seperate-dot-item item-author"><span class="p-text-capitalize">{_p var='by'}</span> {$aItem|user:'':'':50:'':'author'}</span>
                <span class="p-seperate-dot-item item-time">{$aItem.latest_comment|convert_time}</span>
            </div>
            <div class="p-item-minor-info item-blog-post-on">
                <span class="p-text-capitalize">{_p var='on'}</span>
                <a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link " itemprop="url">
                    {$aItem.title|clean}
                </a>
            </div>
        </div>
    </div>
</div>