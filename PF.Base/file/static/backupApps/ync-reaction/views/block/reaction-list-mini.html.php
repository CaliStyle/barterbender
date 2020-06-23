<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($aMostReacted)}
<div class="ync-reaction-list-mini dont-unbind-children">
    {for $i = 0; $i <= 2; $i++}
        {if isset($aMostReacted[$i])}
        <div class="ync-reaction-item js_reaction_item {if count($aMostReacted) == 1}only-1{/if}">
            <a href="javascript:void(0)" class="item-outer"
               data-action="ync_reaction_show_list_user_react_cmd"
               data-type_id="{$sType}"
               data-item_id="{$iItemId}"
               data-total_reacted="{$aMostReacted[$i].total_reacted}"
               data-react_id="0"
               data-table_prefix="{$sPrefix}"
            >
                <img src="{$aMostReacted[$i].full_path}" alt="">
            </a>
        </div>
        {/if}
    {/for}
    <div class="ync-reaction-liked-total">
      <span class="ync-reaction-liked-number">{$iTotalReact|short_number}</span>
      <div class="ync-reaction-tooltip-total js_ync_reaction_tooltip">
          <div class="item-tooltip-content js_ync_reaction_preview_reacted">
              {for $i = 0; $i <= 4; $i++}
                {if isset($aMostReacted[$i])}
                    <div class="item-user"><img src="{$aMostReacted[$i].full_path}" alt="" width="16px"><span class="item-number">{$aMostReacted[$i].total_reacted|short_number}</span></div>
                {/if}
              {/for}
              {if $iTotalReactType > 5}
                  <div class="item-user t_center item-more">...</div>
              {/if}
          </div>
      </div>
    </div>
</div>
{/if}
 
