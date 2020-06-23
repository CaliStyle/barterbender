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

{if !$bIsAddNews}
	{literal}
		<script type="text/javascript">
			$(document).ready(function(){
				$('.breadcrumbs_menu')[0].find('li:first').hide();
			});
		</script>
	{/literal}
{/if}
{if !$bIsAddFeed}
	{literal}
		<script type="text/javascript">
			$(document).ready(function(){
				$('.breadcrumbs_menu')[0].find('li:first').next().hide();
			});
		</script>
	{/literal}
{/if}

<form method="post" ENCTYPE="multipart/form-data" action="{url link='foxfeedspro.edit.feed_'.$feed_id}" >
<div class="news_list">
</div>
<div class="form-group">
    <label for="">{required}{phrase var='foxfeedspro.rss_provider_name'}:</label>
    <input type="text" name="feed_item[feed_name]" value="{$feed_item.feed_name}" size="65" class="input form-control" />
</div>

<div class="form-group">
    <label for="">{phrase var='foxfeedspro.rss_provider_logo_url'}:</label>
    <input  type="text" name="feed_item[feed_logo]" value="{$feed_item.feed_logo}" size="65" class="input form-control" />
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.or_upload_logo_from_your_computer'}</label>
    <input type="file" class="input" name="logo_feed"/>
</div>
<div class="form-group">
    <label for="">{required}{phrase var='foxfeedspro.rss_provider_url'}:</label>
    <textarea class="form-control" type="text" name="feed_item[feed_url]" cols="50" rows="5" >{$feed_item.feed_url}</textarea>
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.rss_provider_category'}:</label>
    <select class="form-control" name="feed_item[category_id]" id="feed_item[category_id]">
        {foreach from=$categories item=cat}
            <option value="{$cat.category_id}" {if $cat.category_id eq $feed_item.category_id}selected{/if}>{$cat.category_name}{if $cat.is_active eq 0}*{/if}</option>
        {/foreach}
    </select>
</div>
<div class="form-group">
    <label for="">{phrase var='foxfeedspro.language_package'}</label>
    <select class="form-control" id="feed_item[feed_language]" name="feed_item[feed_language]">
    <option value="any">Any</option>
     {foreach from=$languages item=langs}
        <option {if $langs.language_id eq $feed_item.feed_language}selected {/if}value="{$langs.language_id}">{$langs.title}</option>

     {/foreach}
   </select>
</div>
<div class="clear">
	<input type="hidden" name="feed_item[feed_id]" value="{$feed_item.feed_id}" id="feed_edit_id"/>
	<input type="hidden" name="iPage" value="{$iPage}" />   
	<input type="submit" name="edit" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm" />
</div>
</form>
{template file='foxfeedspro.block.message'}