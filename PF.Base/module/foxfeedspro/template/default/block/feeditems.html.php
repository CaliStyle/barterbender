<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_NewsFeed
 * @version          2.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{if count($aItem.items)>0}
<h3>
     {if isset($aItem.feed.is_active_mini_logo) && $aItem.feed.is_active_mini_logo eq 1}
     	{if $aItem.feed.logo_mini_logo}
     		<img class ='mini_logo' src="{$aItem.feed.logo_mini_logo}" alt=""/>
     	{else}
     		<img class ='mini_logo' src="{$core_url}theme/frontend/default/style/default/image/rss/small.gif" alt=""/>
     	{/if}
     {/if}
     {if $is_friendly_url eq 1}     
        <a href = "{url link ='foxfeedspro.details.feed_'.$aItem.feed.feed_id.'.'.$aItem.feed.feed_alias}">{$aItem.feed.feed_name}</a>
     {else}
     	<a href = "{url link ='foxfeedspro.details.feed_'.$aItem.feed.feed_id}">{$aItem.feed.feed_name}</a>
     {/if}
    {if isset($aItem.feed.is_active_logo) and $aItem.feed.is_active_logo eq 1}
    	{if $aItem.feed.feed_logo}<img class ='logo' style="float:right;max-height:23px;" src="{$aItem.feed.feed_logo}" alt="" onerror="this.src = '{$core_url}theme/frontend/default/style/default/image/rss/small.gif'" />{/if}
    {/if}
</h3>
<div class="clear"></div>
<div id="js_new_entry">
    <div class="feed_right">
    {foreach  from=$aItem.items name=feeditem item=entry} 
    {if $phpfox.iteration.feeditem > $aItem.feed.feed_item_display_full}
    <div style="background: url({$core_url}module/foxfeedspro/static/image/sprite-2.gif) no-repeat scroll;padding-left: 10px;margin-bottom:2px; clear:both;">
        {*{if $phpfox.iteration.feeditem == $aItem.feed.feed_item_display_full+1}
	        <div class = "image_small" style="float:left; margin-top:3px;{if !empty($entry.item_image)}width:75px;{/if}">
	            {if $entry.item_image}
	            	{if $is_friendly_url eq 1}
	            		<a href="{url link='foxfeedspro.details.item_'.$entry.item_id.'.'.$entry.item_alias}"><img src="{$entry.item_image}" alt=""/></a>
	            	{else}
	            		<a href="{url link='foxfeedspro.details.item_'.$entry.item_id}"><img src="{$entry.item_image}" alt=""/></a>
	            	{/if}
	            {else}
	            	<img class="small_icon_news" src="{$core_url}module/foxfeedspro/static/image/sprite-2.gif" />
	            {/if}
	        </div>
        {/if}*}
        {if $is_friendly_url eq 1}
            <a {if !$entry.item_image}style="float:left; width:168px;"{/if} class="tip_trigger" title="" href = "{url link='foxfeedspro.details.item_'.$entry.item_id.'.'.$entry.item_alias}" >{$entry.item_title}</a><br/>
            <span class="datetime">{$entry.item_pubDate}</span>
        {else}       	
            <a {if !$entry.item_image}style="float:left; width:168px;"{/if}  class="tip_trigger" title="" href = "{url link='foxfeedspro.details.item_'.$entry.item_id}" >{$entry.item_title}</a>
            {if $phpfox.iteration.feeditem!=$aItem.feed.feed_item_display_full+1}<br/>{else}<br/>{/if}
            <span class="datetime">{$entry.item_pubDate}</span>
        {/if}
         <div class="tip">
               <div class="blog_content">
                    <div class = "image_content">
                        {if $entry.item_image} 
                        	<a href="{url link='foxfeedspro.details.item_'.$entry.item_id}"><img src="{$entry.item_image}" alt=""/></a>
                        {else}
                       		<a href="{$entry.item_url_detail}"><img src="{$core_url}module/foxfeedspro/static/image/default_news.png" alt=""/></a>
                       	{/if}
                    </div>
                    <div class ="description_content">                   
                      	{if !empty($entry.item_description)}
                      		<span class ="description">{$entry.item_description|strip_tags:'<p><b><i><u><br><br />'|shorten:250:'...'}</span>
                      	{else}
                      		<span class ="description">{$entry.item_title}</span>
                      	{/if}
                    </div>        
            </div>          
        </div>
    </div>
    {/if}
    {/foreach}
    </div>
  <div class="feed_left"> 
  {foreach  from=$aItem.items name=feeditem item=entry} 
    {if $phpfox.iteration.feeditem <= $aItem.feed.feed_item_display_full} 
  	<div id="news_item" {if ($phpfox.iteration.feeditem == $aItem.feed.feed_item_display_full) || ($phpfox.iteration.feeditem == count($aItem.items))}style="border-bottom:none;"{/if}>
    <div class="blog_content">
        <div class = "image_content">
        	{if $is_friendly_url eq 1}
        		{if $entry.item_image} 
        			<a href="{url link='foxfeedspro.details.item_'.$entry.item_id.'.'.$entry.item_alias}"><img src="{$entry.item_image}" alt=""/></a>
        		{else}
        			<a href="{url link='foxfeedspro.details.item_'.$entry.item_id.'.'.$entry.item_alias}"><img src="{$core_url}module/foxfeedspro/static/image/default_news.png" alt=""/></a>
        		{/if}
        	{else}
            	{if $entry.item_image} 
            		<a href="{url link='foxfeedspro.details.item_'.$entry.item_id}"><img src="{$entry.item_image}" alt=""/></a>
            	{else}
            		<a href="{url link='foxfeedspro.details.item_'.$entry.item_id}"><img src="{$core_url}module/foxfeedspro/static/image/default_news.png" alt=""/></a>
            	{/if}
            {/if}
        </div>
        <div class="description_content description_content_mid">
            <span class="row_title">
	            {if isset($aItem.is_active_mini_logo) eq 1}{if $aItem.logo_mini_logo}<img src="{$aItem.logo_mini_logo}" alt="" />{/if} {/if}
	            {if $is_friendly_url eq 1 and $is_display_popup_item eq 0}        
	                 <a href = "{url link='foxfeedspro.details.item_'.$entry.item_id.'.'.$entry.item_alias}">{$entry.item_title}</a>
	               {elseif $is_friendly_url eq 1 and $is_display_popup_item eq 1}
	                  <a href="#?call=foxfeedspro.viewpopup&amp;height=200&amp;width=600&amp;id={$entry.item_id}&amp;view=1" class="inlinePopup" title="{$entry.item_title}">{$entry.item_title}</a>
	               {elseif $is_friendly_url eq 0 and $is_display_popup_item eq 0}
	                  <a href = "{url link='foxfeedspro.details.item_'.$entry.item_id}">{$entry.item_title}</a>
	               {else}
	                <a href="#?call=foxfeedspro.viewpopup&amp;height=200&amp;width=600&amp;id={$entry.item_id}&amp;view=1" class="inlinePopup" title="{$entry.item_title}">{$entry.item_title}</a>
	            {/if}   
	          </span>   
	       	<br/>
           
            <span class="datetime" >Posted {$entry.item_pubDate}</span><br/>
            <span class = "description"> {$entry.item_description|strip_tags:'<p><b><i><u><br><br />'|shorten:250:'...'}</span>
        </div>
    </div>
    <div style="clear:both"></div>
  </div>
  {/if}
  {/foreach}
  </div>
  <div style="clear:both"></div> 
  {if count($aItem.items)>0 and $aItem.view_all}
  <div class="view_all text-center">
    {if $is_friendly_url eq 1}
        <a href="{url link ='foxfeedspro.details.feed_'.$aItem.feed.feed_id.'.'.$aItem.feed.feed_alias}">{phrase var='foxfeedspro.view_all'}</a>
    {else}
        <a href="{url link='foxfeedspro.details.feed_'.$aItem.feed.feed_id}">{phrase var='foxfeedspro.view_all'}</a>
    {/if}
   </div>
  {/if}
  <div style="clear:both"   ></div>
</div>
{/if}
