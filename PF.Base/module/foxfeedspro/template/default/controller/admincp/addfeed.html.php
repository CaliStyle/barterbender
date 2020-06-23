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

<!-- Add Feed Form Layout -->
{if !$bFeedNotFound}
	{$sCreateJs}
	<form method="post" enctype="multipart/form-data" action="{url link='admincp.foxfeedspro.addfeed'}{if $bIsEdit }feed_{$aForm.feed_id} {/if}" id="js_add_feed_form" onSubmit="{$sGetJsForm}">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {phrase var='foxfeedspro.rss_provider_details'}
                </div>
            </div>
            <div class="panel-body">
                <!-- News Title Element -->
                <div class="form-group">
                    <!-- Feed Provider Name Element -->
                    <label for="">{required}{phrase var='foxfeedspro.rss_provider_name'}:</label>
                    <input type="text" class="form-control" name="val[feed_name]" id ="name" value="{if isset($aForm.feed_name)}{$aForm.feed_name}{/if}"/>
                </div>
                <!-- Provider Category Element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.rss_provider_category'}:</label>
                    {$sCategories}
                </div>
                <!-- Provider Logo Element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.rss_provider_logo_url'}:</label>
                    <input type="text" class="form-control" name="val[feed_logo]" value=""/>
                    {if $bIsEdit && !empty($aForm.feed_logo)}
                        <div class="clear"></div>
                        <img src="{$aForm.feed_logo}" style="max-height: 100px;"/>
                    {/if}
                </div>
                <!-- Upload logo from computer space -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.or_upload_logo_from_your_computer'}:</label>
                    <input class="form-control" type="file" class="input" name="logo_file"/>
                </div>

                <!-- Provider Favicon Element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.favicon'}:</label>
                    <input class="form-control" type="file" class="input" name="logo_mini_logo"/>
                </div>

                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.current_favicon'}:</label>
                    {if $bIsEdit && !empty($aForm.logo_mini_logo)}
                        <img src="{$aForm.logo_mini_logo}" style="max-height: 16px;"/>
                    {/if}
                </div>

                <!-- Provider URL Element -->
                <div class="form-group">
                    <label for="">{required}{phrase var='foxfeedspro.rss_provider_url'}:</label>
                    <textarea class="form-control" type="text" id ="url" name="val[feed_url]" cols="50" rows="5" >{if isset($aForm.feed_url)}{$aForm.feed_url}{/if}</textarea>
                </div>
                <!-- Feed Language Element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.language_package'}:</label>
                   <select class="form-control" name="val[feed_language]">
                    <option value="any">{phrase var='foxfeedspro.any'}</option>
                     {foreach from = $aLanguages item = aLang}
                         <option value="{$aLang.language_id}" {if isset($aForm.feed_language) and $aForm.feed_language == $aLang.language_id} selected {/if}>{$aLang.title}</option>
                     {/foreach}
                   </select>
                </div>

                <div class="form-group">
                    <label for="">
                        {phrase var='foxfeedspro.rss_feed_parse'}:<br/>
                        ({phrase var='foxfeedspro.parse_rss_feed_to_get_full_content'})
                    </label>
                    <div>
                        <input type="radio" name="val[rssparse]" {if isset($aForm.rssparse) && $aForm.rssparse==1}checked{/if} value="1"/> {phrase var='foxfeedspro.rss_feed_only'}
                    </div>
                    <div>
                        <input type="radio" name="val[rssparse]" {if !isset($aForm.rssparse) || (isset($aForm.rssparse) && $aForm.rssparse==0)}checked{/if}  value="0"/> {phrase var='foxfeedspro.get_news_content_0_to_display_whole_news_other_numbers_to_display_limited_characters'}
                    </div>
                    <div style="padding-left: 5px;padding-top: 3px;">
                        <input class="form-control" type="text" size ="20" name="val[lengthcontent]" id ="name" value="{if isset($aForm.lengthcontent)}{$aForm.lengthcontent}{else}0{/if}"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.tags'}:</label>
                    <input class="form-control" type="text" name="val[tag_list]" value="{if isset($aForm.tag_list)}{$aForm.tag_list}{/if}" size="30">
                    <div class="extra_info">
                        {phrase var='foxfeedspro.separate_multiple_topics_with_commas'}
                    </div>
                </div>

                <!-- Feed Items Import Number Element -->
                <input type="hidden" id ="item_import" name="val[feed_item_import]" value="10" />
                <!-- Fields used in Edit Mode Only -->
                {if $bIsEdit}
                    <!-- Display Order element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.display_order'}:</label>
                    <input class="form-control" type="text" name="val[order_display]" value="{if isset($aForm.order_display)}{$aForm.order_display}{/if}" class="input" />
                </div>
                    <!-- News Item Per Page Element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.item_per_page'}:</label>
                    <input class="form-control" type="text" name="val[feed_item_display]" value="{if isset($aForm.feed_item_display)}{$aForm.feed_item_display}{/if}" class="input" />
                </div>
                    <!-- Number of News with full-description display Element -->
                <div class="form-group">
                    <label for="">{phrase var='foxfeedspro.number_of_item_full_description'}:</label>
                    <input class="form-control" type="text" name="val[feed_item_display_full]" value="{if isset($aForm.feed_item_display)}{$aForm.feed_item_display_full}{/if}" class="input" />
                </div>
                <!-- Is Active Element -->
                <div class="form-group">
                    <label for="">{required}{phrase var='foxfeedspro.is_active'}:</label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {if isset($aForm.is_active) and $aForm.is_active ==1 }checked{/if}/>
                            {phrase var='admincp.yes'}
                        </span>
                        <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {if isset($aForm.is_active) and $aForm.is_active ==0 }checked{/if}/>
                            {phrase var='admincp.no'}
                        </span>
                    </div>
                </div>
                <!-- Display mini logo Element -->
                <div class="form-group">
                    <label for="">{required}{phrase var='foxfeedspro.display_mini_logo'}:</label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active"><input type="radio" name="val[is_active_mini_logo]" value="1" {if isset($aForm.is_active_mini_logo) and $aForm.is_active_mini_logo ==1 }checked{/if}/>
                            {phrase var='admincp.yes'}
                        </span>
                        <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active_mini_logo]" value="0" {if isset($aForm.is_active_mini_logo) and $aForm.is_active_mini_logo ==0 }checked{/if}/>
                            {phrase var='admincp.no'}
                        </span>
                    </div>
                </div>
                <!-- Display Logo Element -->
                <div class="form-group">
                    <label for="">{required}{phrase var='foxfeedspro.display_logo'}:</label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active"><input type="radio" name="val[is_active_logo]" value="1" {if isset($aForm.is_active_logo) and $aForm.is_active_logo ==1 }checked{/if}/>
                             {phrase var='admincp.yes'}
                        </span>
                        <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active_logo]" value="0" {if isset($aForm.is_active_logo) and $aForm.is_active_logo ==0 }checked{/if}/>
                             {phrase var='admincp.no'}
                        </span>
                    </div>
                </div>
                    {/if}
            </div>
            <!-- Submit Button -->
            <div class="panel-footer">
                <input type="submit" name="val[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            </div>
        </div>
	</form>
{else}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
		    {phrase var='foxfeedspro.rss_provider_details'}
	    </div>
    </div>
    <div class="panel-body">
        <div class="public_message" id="public_message" style="display: block;">
            {phrase var="foxfeedspro.cannot_find_the_related_rss_provider"}
        </div>
    </div>
</div>
{/if}