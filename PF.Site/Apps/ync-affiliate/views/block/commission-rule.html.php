<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/20/17
 * Time: 20:22
 */
?>
{if $iMaxLevel}
<table class="table table-bordered yncaffiliate_table">
    <thead>
    <tr>
        <th>{_p('Payment Types')}</th>
        {foreach from=$labels item=label}
            <th>{$label}</th>
        {/foreach}
    </tr>
    </thead>
    <tbody>
    {foreach from=$aItems key=iKey item=aItem}
    <tr>
        <td align="left">
            {_p var=$aItem.rule_title}
        </td>
        {if isset($aItem.level_1)}
            <td align="center">
                {_p var=$aItem.level_1}
            </td>
        {/if}
        {if isset($aItem.level_2)}
            <td align="center">
                {_p var=$aItem.level_2}
            </td>
        {/if}
        {if isset($aItem.level_3)}
            <td align="center">
                {_p var=$aItem.level_3}
            </td>
        {/if}
        {if isset($aItem.level_4)}
            <td align="center">
                {_p var=$aItem.level_4}
            </td>
        {/if}
        {if isset($aItem.level_5)}
            <td align="center">
                {_p var=$aItem.level_5}
            </td>
        {/if}
    </tr>
    {/foreach}
    </tbody>
</table>
{else}
    <div class="extra_info">{_p var='no_rules_found'}</div>
{/if}