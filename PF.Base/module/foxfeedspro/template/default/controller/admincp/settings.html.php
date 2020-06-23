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

{literal}
<style type="text/css">
   ul#breadcrumb_menu li a
    {
      font-size: 12px;
    }
</style>
{/literal}
<form method="post" action="{url link='admincp.foxfeedspro.settings'}" id="admincp_news_form_message">
<input type="hidden" name="action" value="add"/>
    <div class="table_header">
       {phrase var='foxfeedspro.global_settings'}
    </div>
    <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.number_of_rss_providers_display_on_homepage'}
        </div>
        
        <div class="table_right">
            <input type="text" name="number_feed_display" value="{$number_feed_display}" />
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.number_top_display_block'}
        </div>
        <div class="table_right">
            <input type="text" name="number_top_news" value="{$number_top_news}" />
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.number_recent_display_block'}
        </div>
        <div class="table_right">
            <input type="text" name="number_recent_news" value="{$number_recent_news}" />
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.number_featured_display_block'}
        </div>
        <div class="table_right">
            <input type="text" name="number_featured_news" value="{$number_featured_news}" />
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.number_commented_display_block'}
        </div>
        <div class="table_right">
            <input type="text" name="number_commented_news" value="{$number_commented_news}" />
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.number_favorite_display_block'}
        </div>
        <div class="table_right">
            <input type="text" name="number_favorite_news" value="{$number_favorite_news}" />
        </div>
        <div style="color:red; font-weight:bold;">{phrase var='foxfeedspro.note_set_all_above_items_to_0_if_there_should_be_no_limit'}</div>
        <div class="clear"></div>
    </div>
    
    <div class="table">
        <div class="table_left">
            <input type="checkbox" {if $is_auto_delete eq 1 }checked {/if} value="{$is_auto_delete}" id="is_auto_delete_check" name="is_auto_delete_check" onclick="document.getElementById('is_auto_delete').value= document.getElementById('is_auto_delete_check').checked" />
            <input type="hidden" value="{$is_auto_delete}" name="is_auto_delete" id="is_auto_delete" />
            {phrase var='foxfeedspro.auto_delete_news_older_than'}
        </div>
        <div class="table_right">
            <input type="text" name="number_day_delete" value="{$number_day_delete}" size="2" maxlength="4" />
            {phrase var='foxfeedspro.days'}
            <span id ="delete_id">
                <a href="javascript:deletedata();">{phrase var='foxfeedspro.or_manually_deleted'}</a>
            </span>           
        </div>
        <div class="clear"></div>
    </div>
    <div class="table">
    	<div class="table_left">
    		{phrase var='foxfeedspro.number_of_related_news_displayed_on_detail'}:
    	</div>
    	<div class="table_right">
    		<input type="text" name="number_related_news" value="{if isset($number_related_news) && ($number_related_news != '')}{$number_related_news}{else}10{/if}" />
    	</div>
    	<div class="clear"></div>
    </div>
     <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.view_news_detail_on_popup'}
        </div>
        <div class="table_right">    
            <div class="item_is_active_holder">             
                <span class="js_item_active item_is_active"><input type="radio" name="is_display_popup" value="1" {if $is_display_popup eq 1 } {value type='radio' id='is_active' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="is_display_popup" value="0" {if $is_display_popup eq 0 } {value type='radio' id='is_active' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>        
    </div>
     <div class="table">
        <div class="table_left">
            {required}{phrase var='foxfeedspro.enable_friendly_url'}
        </div>
        <div class="table_right">    
            <div class="item_is_active_holder">             
                <span class="js_item_active item_is_active"><input type="radio" name="is_friendly_url" value="1" {if $is_friendly_url eq 1 } {value type='radio' id='is_active' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="is_friendly_url" value="0" {if $is_friendly_url eq 0 } {value type='radio' id='is_active' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>        
    </div>
   <div class="table">
        <div class="table_left">
          {phrase var='foxfeedspro.add_random_featured_news'}
        </div>
        <div class="table_right">
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="is_random_featured" value="1" {if $is_random_featured eq 1 } {value type='radio' id='is_random_featured' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="is_random_featured" value="0" {if $is_random_featured eq 0 } {value type='radio' id='is_random_featured' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>

     <div class="table">
        <div class="table_left">
          {phrase var='foxfeedspro.download_the_images_in_the_news_to_your_server'}
        </div>
        <div class="table_right">
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="is_downloaded" value="1" {if $is_downloaded eq 1 } {value type='radio' id='is_downloaded' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="is_downloaded" value="0" {if $is_downloaded eq 0 } {value type='radio' id='is_downloaded' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    {*<div class="table">
        <div class="table_left">
          {phrase var='foxfeedspro.auto_approval_user_posted'}
        </div>
        <div class="table_right">
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="is_approved" value="1" {if $is_approved eq 1 } {value type='radio' id='is_approved' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="is_approved" value="0" {if $is_approved eq 0 } {value type='radio' id='is_approved' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>*}
    <div class="table_clear">
        <input type="submit" name="save_settings" value="{phrase var='foxfeedspro.save_change'}" class="button" />
    </div>
</form>
{literal}
<script>
    function deletedata()
    {
    	 $('#delete_id').attr('rel', $('#delete_id').html());        
         $('#delete_id').html('Deleting....');
         $Core.ajax('foxfeedspro.deleteNews',{params:'&days=1', success:onDeleteSuccess});
    }
    function onDeleteSuccess(data)
    {
    	$('#delete_id').html('Complete...').fadeOut('slow', function()
       		 {
       	 		$('#delete_id').html($('#delete_id').attr('rel'));
            	$('#delete_id').show();
       		 }
         );
    }
</script>
{/literal}
{template file='foxfeedspro.block.message'}