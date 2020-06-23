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
    table
{
    width:100%;
    background:#f1f1f1;
    border-bottom:1px #dfdfdf solid;
    margin-bottom:10px;
}
td
{
    vertical-align: middle;
}
th
{
    vertical-align: middle;
}
 .tr
   {
       background: #fff;
}
td.dddd
    {
        text-align: center;
    }
    th.dddd
    {
        text-align: left;
    }
    
   ul#breadcrumb_menu li a
    {
      font-size: 12px;
    }

   .table_right input{
      width:200px;
    }
    th.options
    {
     padding: 4px 25px;
   }
   .foxfeedspro_approval_status
   {
   		text-align: center;
   }
</style>
{/literal}
<form method="post" action="{url link='admincp.foxfeedspro.approvalfeed'}">
<div class="news_list">
    <div class="table_header">{phrase var='foxfeedspro.search_filter'}
    </div>
</div>
<div class="table">
    <div class="table_left">
       {phrase var='foxfeedspro.rss_provider_name'}:
    </div>
    <div class="table_right">
        {$aFilters.feed_name}
    </div>
    <div class="clear"></div>
</div>
<div class="table_clear">
    <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="button" />
    <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="button" />

</div>
</form>
{if count($feeds)>0}
    <table align="center" style="text-align:left;">
    <tr>
        <th><input type="checkbox" value="" id="checkAll" name="checkAll" onclick="javascript:selectAll()"/></th>
        <th>{phrase var='foxfeedspro.headline_logo'}</th>
        <th>{phrase var='foxfeedspro.rss_provider_name'}</th>
        <th>{phrase var='foxfeedspro.headline_feed_url'}</th>
        <th>{phrase var='foxfeedspro.headline_category'}</th>
        <th width="120">{phrase var='foxfeedspro.headline_posted_date'}</th> 
        <th width="80" align="center">{phrase var='foxfeedspro.approve'}</th>
    </tr>
	
    <form action="{url link='admincp.foxfeedspro.approvalfeed'}" method="post" id="order_display_sb" >
	{if $iPage>1}<input type="hidden" name="page" value="{$iPage}" />{/if}
    {foreach from=$feeds key=iKey item=feed}
    <tr id="js_feed_item_{$feed.feed_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
        <td style="width:10px">
            <input type="checkbox" value="{$feed.feed_id}" name="is_selected"/>
            <div id="div_is_selected_active_{$feed.feed_id}">
                <input type="hidden" value="{$feed.is_active}" id="is_selected_active_{$feed.feed_id}" />
            </div>
        </td>
        <td>{if $feed.feed_logo}<img src ="{$feed.feed_logo}" style="max-width:60px;max-height:30px" alt=""/>{/if}</td>
        <td>{$feed.feed_name|shorten:50:'...')}</td>
        <td><a href="{$feed.feed_url}" target="_blank">{$feed.feed_url|shorten:50:'...'}</a></td>
        <td>{$feed.category_name}</td> 
        <td>{$feed.posted_date}</td>
        <td align="center">
            <div id="feed_update_approval_{$feed.feed_id}">
                <a href="javascript:updateapprovalfeed({$feed.feed_id},0);" >{phrase var='foxfeedspro.approve'}</a>
            </div> 
        </td>
    </tr>
    {/foreach}
    </table>
	<div class="table_bottom">
        <input type="submit" name="approval" value="{phrase var='foxfeedspro.approve'}" class="button" onclick="return approvalBySelect();" />
        {*<input  type="submit" name="deleteselect" value="{phrase var='foxfeedspro.delete_selected'}" class="button" onclick="setValue(); return is_submit;"/>*}
        <input type="hidden" value="" name="arr_selected" id="arr_selected"/>
    </form>
        <div style="clear:both"></div>
    </div>
    {pager}
{else}
    <div class="extra_info">{phrase var='foxfeedspro.no_feeds_found'}</div>
	</form>
{/if}

{literal}
<script type="text/javascript">
       /*
	   var select = document.getElementsByName('search[is_approved]')[0];
       select.selectedIndex = {/literal}{$position_status}{literal};
	   */
       is_feed_s = true;
</script>
{/literal}
{template file='foxfeedspro.block.message'}
