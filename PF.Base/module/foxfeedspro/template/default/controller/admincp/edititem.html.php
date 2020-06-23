<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */
?>
{if !$bNewsNotFound}
	<!-- Add News Form Layout -->
	{$sCreateJs}
	<form method="post" enctype="multipart/form-data" action="{url link='admincp.foxfeedspro.edititem'}{if $bIsEdit }item_{$aForm.item_id} {/if}" id="js_add_feed_form" onSubmit="{$sGetJsForm}">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {phrase var="foxfeedspro.edit_news"}
                </div>
            </div>
            <div class="panel-body">
                <!-- News Title Element -->
                <div class="form-group">
                    <label for="">
                        {required}{phrase var='foxfeedspro.title'}:
                    </label>
                    <input type="text" class="form-control" size ="60" name="val[item_title]" id ="title" value="{if isset($aForm.item_title)}{$aForm.item_title}{/if}"/>
                </div>
                <!-- News Author Element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.author'}:</label>
                    <input type="text" class="form-control" size ="60" name="val[item_author]" id ="author" value="{if isset($aForm.item_author)}{$aForm.item_author}{/if}"/>
                </div>
                <div class="form-group">
                    <label for="">{required}{phrase var='foxfeedspro.news_url_source'}:</label>
                    <input class="form-control" type="text" id ="url" name="val[item_url_detail]" size="60" value="{if isset($aForm.item_url_detail)}{$aForm.item_url_detail}{/if}"/>
                </div>
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.from_rss_provider'}:</label>
                    <select class="form-control" name="val[feed_id]" id="val[feed_id]">
                        {foreach from=$aFeeds item=aFeed}
                        <option value="{$aFeed.feed_id}" {if isset($aForm.feed_id) and $aForm.feed_id == $aFeed.feed_id}selected{/if}>{$aFeed.feed_name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.upload_news_thumbnail_image'}:</label>
                    <input class="form-control" type="file" class="input" name="thumbnail"/>
                    {if isset($aForm.item_image)}
                    <div class="clear"></div>
                    <img src="{$sCorePath}{$aForm.item_image}" style="margin: 5px; width: 100px;"/>
                    {/if}
                </div>
                <div class="form-group">
                    <label for="">{required}{phrase var='foxfeedspro.headline_description'}:</label>
                    <textarea class="form-control" cols="60" rows="15" id="description" name ="val[item_description]">{if isset($aForm.item_description_parse)}{$aForm.item_description_parse}{/if}</textarea>
                </div>
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.headline_content'}:</label>
                    <textarea class="form-control" cols="60" rows="15" id ="content" name ="val[item_content]">{if isset($aForm.item_content_parse)}{$aForm.item_content_parse}{/if}</textarea>
                </div>
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.tags'}:</label>
                    <input class="form-control" type="text" name="val[tag_list]" value="{if isset($aForm.tag_list)}{$aForm.tag_list}{/if}" size="30">
                    <div class="extra_info">
                        {phrase var='foxfeedspro.separate_multiple_topics_with_commas'}
                    </div>
                </div>
                <div class="form-group">
                    <label for=""> {required}{phrase var='foxfeedspro.is_active'}:</label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active"><input class="form-control" type="radio" name="val[is_active]" value="1" {if isset($aForm.is_active) and $aForm.is_active ==1 }checked{/if}/>
                            {phrase var='admincp.yes'}
                        </span>
                        <span class="js_item_active item_is_not_active"><input class="form-control" type="radio" name="val[is_active]" value="0" {if isset($aForm.is_active) and $aForm.is_active ==0 }checked{/if}/>
                            {phrase var='admincp.no'}
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{required}{phrase var='foxfeedspro.is_featured_news'}:</label>
                    <div class="item_is_active_holder">
	                <span class="js_item_active item_is_active"><input type="radio" name="val[is_featured]" value="1" {if isset($aForm.is_featured) and $aForm.is_featured ==1 }checked{/if}/>
	                	 {phrase var='admincp.yes'}
	                </span>
                        <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_featured]" value="0" {if isset($aForm.is_featured) and $aForm.is_featured ==0 }checked{/if}/>
	                	 {phrase var='admincp.no'}
	                </span>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" name="val[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            </div>
        </div>
	</form>
{else}
	<!-- Table header -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var="foxfeedspro.edit_news"}
            </div>
        </div>
    </div>
		
	<div class="public_message" id="public_message" style="display: block;">
		{phrase var="foxfeedspro.cannot_find_the_related_news"}
	</div>
{/if}