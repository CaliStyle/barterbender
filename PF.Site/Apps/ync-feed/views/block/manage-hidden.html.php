<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<!--Search-->
{if $iPage == 1 && !$bSearch}
<div id="ynfeed_manage_hidden" class="ynfeed-manage-hidden-popup">
    <form id="ynfeed_search_hidden" method="POST" onsubmit="$Core.ynfeed.searchHidden(this);return false;">
      <div class="ynfeed-form-search">
         <input type="text" placeholder="{_p('search_name')}" name="name" class="form-control">
         <select name="type" class="form-control">
            <option value="user">{_p('All')}</option>
            <option value="page">{_p('Pages')}</option>
            <option value="friend">{_p('Friends')}</option>
         </select>
         <input type="submit" value="{_p('Search')}" class="btn btn-primary">
      </div>
      <p class="ynfeed-tips">{_p('hide_posts_from_below_users_pages_or_groups')}</p>

    <div id="ynfeed_list_hidden" class="ynfeed-list-hidden-popup">
      <div class="ynfeed-list-headline">
         <span>{_p var='number_items_selected' number=0}</span>

         <div class="ynfeed-select-all checkbox">
            <label>
               <input type="checkbox" onchange="$Core.ynfeed.selectAllHiddens(this);"> {_p('select_all')}
            </label>
         </div>
      </div>

      <input type="hidden" id="ynfeed_list_unhide">
      <div class="ynfeed-hidden-items clearfix">
{/if}
      <!--Ajax loading part-->
      {if $iCnt > 0}
         {foreach from=$aHiddens item=aHidden}
         <div id="ynfeed_item_hidden_{$aHidden.hide_id}" class="ynfeed-hidden-item">
            <div class="ynfeed-hidden-item-content">
               <label for="ynfeed_item_hidden_checkbox_{$aHidden.hide_id}">{$aHidden.user_image_actual}{$aHidden.full_name}</label>
               <span class="ynfeed-delete">
                  <i class="fa fa-close" onclick="$Core.ynfeed.unhide({$aHidden.hide_id}, {$aHidden.hide_resource_id}, '{$aHidden.hide_resource_type}'); return false;"></i>
               </span>
               <input type="checkbox" id="ynfeed_item_hidden_checkbox_{$aHidden.hide_id}" class="ynfeed_item_hidden_checkbox"
               onchange="$Core.ynfeed.selectUnhide(this)"
               data-hid="{$aHidden.hide_id}">
            </div>
         </div>
         {/foreach}
         {pager}
      {else}
         <div id="ynfeed_no_hidden">{_p('no_hidden_items_found')}</div>
      {/if}
      <!--End ajax loading part-->

{if $iPage == 1 && !$bSearch}
      </div>
    </div>

    <div id="ynfeed_action_hidden">
        <a class="btn btn-default" onclick="return js_box_remove(this);">{_p('Cancel')}</a>
        <a class="btn btn-primary disabled" id="ynfeed_unhide_button" onclick="$Core.ynfeed.multiUnhide();return false;">{_p var='unhide_selected'}</a>
    </div>
    </form>
</div>
{/if}
