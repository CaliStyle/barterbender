<?php
?>
{if count($aNews) > 0}   
<table id="list_news">
    <tr class="checkRow">
        <th style="width:10px;"><input class="form-control" type="checkbox" value="" id = "checkAll" name="checkAll" onclick="javascript:selectAll()"/></th>
        <th>Title</th>
        <th>Feed Type</th>
        <th>Posted Date</th>
        <th>Options</th>
    </tr>
    {foreach from=$aNews key=iKey item=provider}
    
    <tr id="{$provider.item_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}" align="center">
        <td style="width:10px;">
            <input class="form-control" type="checkbox" value="{$provider.item_id}" name="is_selected"/>
            <input type="hidden" value="{$provider.is_active}" id="is_selected_active_{$provider.item_id}" />
        </td>
        <td >{$provider.item_title|clean}</td>
        <td >{$provider.feed_name}</td>
        <td >{$provider.posted_date}</td>
        <td >
            <a href="{url link='foxfeedspro.details.item_'.$provider.item_id.'.'.$provider.item_title}" target="_blank" >View<a/>
        </td>
    </tr>

    {/foreach}
</table>
{/if}