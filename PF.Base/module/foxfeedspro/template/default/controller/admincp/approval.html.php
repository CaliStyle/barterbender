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
    .table_right input{
        width:200px;
    }
</style>
<style type="text/css">
   ul#breadcrumb_menu li a
    {
      font-size: 12px;
    }

    td.options
    {
      text-align: center;
      padding:6px 0;
    }
    td.options a
    {
      font-size: 12px;
    }
    th.options
    {
     padding: 4px 10px;
   	}
   	.foxfeedspro_approval_status
   	{
   		text-align: center;
   	}
</style>
{/literal}
<form method="post" action="{url link='admincp.foxfeedspro.approval'}">
<div class="table_header">
    {phrase var='foxfeedspro.search_filter'}
</div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.keywords'}:
    </div>
    <div class="table_right">
        {$aFilters.title}
    </div>
    <div class="clear"></div>
</div>
<div class="table">
    <div class="table_left">
        {phrase var='foxfeedspro.rss_provider_name'}:
    </div>
    <div class="table_right">
        {$aFilters.type}
    </div>
    <div class="clear"></div>
</div>
<div class="table_clear">
    <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="button" />
    <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="button" />
</div>
</form>
<div id="sss">
{pager}
    
{if count($items) >0}    
    <table id="list_news" style="position:relative;">
    <tr>          
        <th><input type="checkbox" value="" id = "checkAll" name="checkAll" onclick="javascript:selectAll()"/></th>
        <th>{phrase var='foxfeedspro.headline_title'}</th>
        <th>{phrase var='foxfeedspro.rss_provider_name'}</th>
        <th width="120">{phrase var='foxfeedspro.headline_posted_date'}</th>
        <th width="120">{phrase var='foxfeedspro.headline_published_date'}</th>
        <th align="center" width="60">{phrase var='foxfeedspro.approve'}</th> 
    </tr>
   <form action="{url link='admincp.foxfeedspro.approval'}" method="post"  onsubmit="return getsubmit();">
    {foreach from=$items key=iKey item=provider}
    
    <tr id="js_news_item_{$provider.item_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
        <td style="width:10px">
            <input type="checkbox" value="{$provider.item_id}" name="is_selected" style="margin-top:0px;"/>
            <input type="hidden" value="{$provider.is_active}" id="is_selected_active_{$provider.item_id}" />
        </td>
        <td><a href="{url link='foxfeedspro.details.item_'.$provider.item_id}" target="_blank">{$provider.item_title|shorten:50:"..."}</as></td>
        <td>{$provider.feed_name|shorten:30:"..."}</td>
        <td>{$provider.item_posted_date}</td>
        <td>{$provider.item_pubDate}</td>
		<td align="center">
            <div id="item_update_approval_{$provider.item_id}">
				<a href="javascript:updateapproval({$provider.item_id},0);" >{phrase var='foxfeedspro.approve'}</a>
            </div> 
        </td>
    </tr>
     
    {/foreach}
    </table>
    <div class="table_bottom">
        <input type="hidden" value="" name="arr_selected" id="arr_selected"/>
        <input type="hidden" value="" name="feed_selected" id="feed_selected"/>
        {if isset($value_search) and isset($current_feed)}
        <input type="hidden" value="{$value_search}" name="value_search" id="value_search"/>
        <input type="hidden" value="{$current_feed}" name="current_feed" id="current_feed"/>
        {/if}   
        <input type="submit" name="approval" value="{phrase var='foxfeedspro.approve'}" class="button" onclick="return approvalBySelect();" />                 
        {*<input type="submit" name="deleteselect" value="{phrase var='foxfeedspro.delete_selected'}" class="button" onclick="javascript:setValue();"/>*}
    </div>
 </form>
    
    
   {pager}
   

</div>
{else}
   <div class="extra_info">{phrase var='foxfeedspro.no_news_found'}</div>
{/if}
  {if isset($is_search) != 'is_search'}
 
 {literal}
 <script type="text/javascript">
 {
  {/literal}  
     var feed = document.getElementsByName('search[type]')[0]; 
     feed.value = "{$current_feed}";
    // document.getElementById('current_feed').value = '{$current_feed}';
     $('#current_feed').val('{$current_feed}');
  {literal}
 }
 </script>
 {/literal}
 {/if}
{template file='foxfeedspro.block.message'}