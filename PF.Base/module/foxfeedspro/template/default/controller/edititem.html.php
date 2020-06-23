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

<form method="post" ENCTYPE="multipart/form-data" id="core_js_news_form" action="{url link='foxfeedspro.edititem.item_'$item_id}" >
<div class="news_list">
</div>

<div class="form-group">
    <label for="">{required}{phrase var='foxfeedspro.headline_title'}:</label>
    <input type="text" name="val[item_title]" value="{$item_edit.item_title|clean}" class="input form-control" size="70" />
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.author'}:</label>
    <input type="text" name="val[item_author]" value="{$item_edit.item_author}" class="input form-control" size="70" />
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.url_source'}:</label>
    <input type="text" name="val[item_url_detail]" value="{$item_edit.item_url_detail}" class="input form-control" size="70" />
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.upload_new_thumbnail_image'}:</label>
    <input type="file" class="input" name="item_photo_thumb"/>
   <div style="margin-top:5px;">
        {if $item_edit.item_image !=""}
            <img src="{$item_edit.item_image}" alt="{$item_edit.item_title|clean}" width="80" height="80">
        {else}
            {phrase var='foxfeedspro.there_is_no_thumbnail_image'}
        {/if}
   </div>
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.from_rss_provider'}:</label>
    <select class="form-control" name="val[feed_id]" id="item_edit[feed_id]">
        {foreach from=$feeds item=feed}
            <option value="{$feed.feed_id}" {if $item_edit.feed_id eq $feed.feed_id}selected{/if}>{$feed.feed_name}</option>
        {/foreach}
    </select>
</div>
<div class="form-group">
    <label for="">{required}{phrase var='foxfeedspro.headline_description'}:</label>
    <textarea class="form-control" cols="60" rows="10" name ="val[item_description]">{$item_edit.item_description}</textarea>
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.content'}:</label>
    <input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" />
    {editor id='item_content'}
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.topic'}:</label>
    <input class="form-control" size="50" name="val[item_tags]" {if isset($item_edit.item_tags)}value="{$item_edit.item_tags}"{/if} />
    <div class="extra_info">{phrase var='tag.separate_multiple_topics_with_commas'}</div>
</div>
<div class="clear">
    <input type="hidden" name="val[item_id]" value="{$item_edit.item_id}" id="item_id"/>
    <input type="hidden" name="val[owner_id]" value="{$item_edit.owner_id}" id="owner_id"/>
    <input type="submit" name="edit" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm" />
</div>
</form>
{template file='foxfeedspro.block.message'}