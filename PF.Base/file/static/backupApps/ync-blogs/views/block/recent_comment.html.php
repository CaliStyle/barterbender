<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 06/01/2017
 * Time: 18:40
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynadvblog_recent_comments">
	<ul>
	    {foreach from=$aItems item=aItem}
		    <li>
		    	<div class="clearfix">
			        <div class="ynadvblog_avatar">
                        {if $aItem.user_image}
			                <a href="{url link=$aItem.user_name}" style="background-image: url('{img user=$aItem suffix='_100' return_url=true}');"></a>
                        {else}
                            {img user=$aItem suffix='_100'}
                        {/if}
			        </div>
			        <div class="ynadvblog_info">
				        {$aItem|user:'':'':50:'':'author'}
				        <div class="ynadvblog_post_title">
				        	<span class="lowercase">{_p var='on'}</span>
				        	<a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link fw-bold" itemprop="url"> {$aItem.title|clean}</a>
				        </div>
			        </div>
		        </div>
		        <div class="ynadvancedblog-recent-comment">
	                    <?php $this->_aVars['aLastComment'] = Phpfox::getService('ynblog.blog')->getLastCommentByBlogId($this->_aVars['aItem']['blog_id'], $this->_aVars['aItem']['latest_comment']); ?>
                        {if !empty($aLastComment)}
                           <sup class="fa fa-quote-left" aria-hidden="true"></sup>{$aLastComment.text|feed_strip|split:500|shorten:200:'...'}
                        {/if}
                </div>
		        <div class="time">{$aItem.latest_comment|convert_time}</div>
	        </li>
	    {/foreach}
	</ul>
</div>
