<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_NewsFeed
 * @version        3.02p5
 * 
 */
?>


{if count($aFeeds) >0 }
	{if !$bNewsNotFound}
		<!-- Add News Form Layout -->
		{$sCreateJs}
		<form method="post" enctype="multipart/form-data" action="{url link='foxfeedspro.addnews'}{if $bIsEdit }item_{$aForm.item_id} {/if}" id="js_add_feed_form" onSubmit="{$sGetJsForm}">
			<!-- News Title Element -->
			<div class="form-group">
                <label for="">{required}{phrase var='foxfeedspro.title'}:</label>
			    <input class="form-control" type="text" size ="60" name="val[item_title]" id ="title" value="{if isset($aForm.item_title)}{$aForm.item_title}{/if}"/>
			</div>
			<!-- News Author Element -->
			<div class="form-group">
                <label for="">{phrase var='foxfeedspro.author'}:</label>
			    <input class="form-control" type="text" size ="60" name="val[item_author]" id ="author" value="{if isset($aForm.item_author)}{$aForm.item_author}{/if}"/>
			</div>
			<!-- News URL Element -->
		    <div class="form-group">
                <label for="">{required}{phrase var='foxfeedspro.news_url_source'}:</label>
			    <input class="form-control" type="text" id ="url" name="val[item_url_detail]" size="60" value="{if isset($aForm.item_url_detail)}{$aForm.item_url_detail}{/if}"/>
			</div>
			<!-- News RSS Provider -->
			<div class="form-group">
                <label for="">{phrase var='foxfeedspro.from_rss_provider'}:</label>
                <select class="form-control" name="val[feed_id]" id="val[feed_id]">
                    {foreach from=$aFeeds item=aFeed}
                        <option value="{$aFeed.feed_id}" {if isset($aForm.feed_id) and $aForm.feed_id == $aFeed.feed_id}selected{/if}>{$aFeed.feed_name}</option>
                    {/foreach}
                </select>
		    </div>
			<!-- Upload news thumbnail image -->
			<div class="form-group">
                {if !empty($aForm.item_image)}
                    {module name='core.upload-form' type='foxfeedspro' current_photo=$aForm.current_image id=$aForm.item_id}
                    <input type="hidden" name="val[item_image]" value="{value type='input' id='item_image'}">
                    <input type="hidden" name="val[item_server_id]" value="{value type='input' id='item_server_id'}">
                {else}
                    {module name='core.upload-form' type='foxfeedspro' current_photo=''}
                {/if}
			</div>
			<!-- News Description Element -->
			<div class="form-group">
                <label for="">{phrase var='foxfeedspro.headline_description'}:</label>
		        <textarea class="form-control" cols="60" rows="15" id ="description" name ="val[item_description]">{if isset($aForm.item_description)}{$aForm.item_description}{/if}</textarea>
		    </div>
		    <!-- News Content Element -->
			<div class="form-group">
                <label for="">{phrase var='foxfeedspro.headline_content'}:</label>
		        <textarea class="form-control" cols="60" rows="15" id ="item_content" name ="val[item_content]">{if isset($aForm.item_content)}{$aForm.item_content}{/if}</textarea>
		    </div>

			<div class="form-group">
                <label for="">{phrase var='foxfeedspro.tags'}:</label>
                <input class="form-control" type="text" name="val[tag_list]" value="{if isset($aForm.tag_list)}{$aForm.tag_list}{/if}" size="30">
                <div class="extra_info">
			        {phrase var='foxfeedspro.separate_multiple_topics_with_commas'}
                </div>
			</div>

		    <!-- Submit Button -->
			<div class="">
			    <input type="submit" name="val[submit]" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm" />
			</div>
		</form>
	{else}
		<div class="public_message" id="public_message" style="display: block;">
			{phrase var="foxfeedspro.cannot_find_the_related_news"}
		</div>
	{/if}
{else}
	<div class="extra_info">{phrase var="foxfeedspro.you_must_have_a_least_one_rss_provider_before_you_can_add_a_news"}</div>
{/if}