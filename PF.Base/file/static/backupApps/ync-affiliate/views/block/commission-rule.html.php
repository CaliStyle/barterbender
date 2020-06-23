<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/20/17
 * Time: 20:22
 */
?>

<table class="table table-bordered yncaffiliate_table">
    <thead>
    <tr>
        <th>{_p('Payment Types')}</th>
        <th>{_p('Level 1')}</th>
        <th>{_p('Level 2')}</th>
        <th>{_p('Level 3')}</th>
        <th>{_p('Level 4')}</th>
        <th>{_p('Level 5')}</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$aItems key=iKey item=aItem}
    <tr>
        <td align="left">
            {_p var=$aItem.rule_title}
        </td>
        <td align="center">
            {if isset($aItem.level_1)}{_p var=$aItem.level_1}{/if}
        </td>
        <td align="center">
            {if isset($aItem.level_2)}{_p var=$aItem.level_2}{/if}
        </td>
        <td align="center">
            {if isset($aItem.level_3)}{_p var=$aItem.level_3}{/if}
        </td>
        <td align="center">
            {if isset($aItem.level_4)}{_p var=$aItem.level_4}{/if}
        </td>
        <td align="center">
            {if isset($aItem.level_5)}{_p var=$aItem.level_5}{/if}
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>