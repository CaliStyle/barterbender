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

<form method="post" ENCTYPE="multipart/form-data" action="{url link='admincp.foxfeedspro.edit'}" >
<div class="table_header">
	{phrase var='foxfeedspro.edit_rss_provider'}
</div>

<div class="table">
    <div class="table_left">
        {phrase var='foxfeedspro.rss_provider_name'}:
    </div>
    <div class="table_right">
        <input type="text" name="feed[name]" value="{$feed.feed_name}" class="input" />
    </div>
    <div class="clear"></div>
</div>
    
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.rss_provider_logo_url'}:
    </div>
    <div class="table_right">
        <input type="text" name="feed[url_logo]" value="{$feed.feed_logo}" class="input" />
    </div>
    <div class="clear"></div>
</div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.or_upload_logo_from_your_computer'}
    </div>
    <div class="table_right">
       <input type="file" class="input" name="logo_feed"/>
    </div>
    <div class="clear"></div>
</div>
<div class="table">
    <div class="table_left">
       {required}{phrase var='foxfeedspro.rss_provider_url'}:
    </div>
    <div class="table_right">
        <textarea type="text" name="feed[url]" cols="50" rows="5" >{$feed.feed_url}</textarea>
    </div>
    <div class="clear"></div>
</div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.rss_provider_category'}:
    </div>
    <div class="table_right">
        <select name="feed[category]" id="feed[category]">                    
            {foreach from=$cats item=cat}            
                <option value="{$cat.category_id}" {if $cat.category_id eq $feed.category_id}selected{/if}>{$cat.category_name}</option>            
            {/foreach}
        </select>
    </div>
    <div class="clear"></div>
</div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.display_order'}: 
    </div>
    <div class="table_right">
        <input type="text" name="feed[order_display]" value="{$feed.order_display}" class="input" />
    </div>
    <div class="clear"></div>
</div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.item_per_page'}:
    </div>
    <div class="table_right">
        <input type="text" name="feed[feed_item_display]" value="{$feed.feed_item_display}" class="input" />
    </div>
    <div class="clear"></div>
</div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.number_of_item_full_description'}:
    </div>
    <div class="table_right">
        <input type="text" name="feed[feed_item_display_full]" value="{$feed.feed_item_display_full}" class="input" />
    </div>
    <div class="clear"></div>
</div>
 <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.is_active'}:
        </div>
        <div class="table_right">    
            <div class="item_is_active_holder">        
                <span class="js_item_active item_is_active"><input type="radio" name="feed[is_active]" value="1" {if $feed.is_active ==1 } {value type='radio' id='is_active' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="feed[is_active]" value="0" {if $feed.is_active ==0 } {value type='radio' id='is_active' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>        
    </div>
<div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.display_mini_logo'}:
        </div>
        <div class="table_right">    
            <div class="item_is_active_holder">        
                <span class="js_item_active item_is_active"><input type="radio" name="feed[is_active_logo_mini]" value="1" {if $feed.is_active_mini_logo ==1 }{value type='radio' id='is_active_logo_mini' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="feed[is_active_logo_mini]" value="0" {if $feed.is_active_mini_logo ==0 }{ {value type='radio' id='is_active_logo_mini' default='0' selected='true' }{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>        
    </div>
<div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.display_logo'}:
        </div>
        <div class="table_right">    
            <div class="item_is_active_holder">        
                <span class="js_item_active item_is_active"><input type="radio" name="feed[is_active_logo]" value="1" {if $feed.is_active_logo ==1 }{value type='radio' id='is_active_logo' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="feed[is_active_logo]" value="0" {if $feed.is_active_logo ==0 }{value type='radio' id='is_active_logo' default='0' selected='true' }{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>        
    </div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.language_package'}
    </div>
    <div class="table_right">
       <select id="feed[feed_language]" name="feed[feed_language]">
        <option value="any">Any</option> 
         {foreach from=$languages item=langs}
            <option {if $langs.language_id eq $feed.feed_language}selected {/if}value="{$langs.language_id}">{$langs.title}</option>        
         {/foreach}
       </select>
       
    </div>
    <div class="clear"></div>
</div> 
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.items_per_rss_provider_to_import'}:
    </div>
    <div class="table_right">
        <input type="text" name="feed[feed_item_import]" value="{$feed.feed_item_import}" class="input" />
    </div>
    <div class="clear"></div>
</div>               
<div class="table_clear">
	<input type="hidden" name="feed[id]" value="{$feed.feed_id}" id="feed_edit"/>
	<input type="hidden" name="iPage" value="{$iPage}" />
	<input type="submit" name="edit" value="{phrase var='core.submit'}" class="button" />
		
</div>
</form>
{template file='foxfeedspro.block.message'}