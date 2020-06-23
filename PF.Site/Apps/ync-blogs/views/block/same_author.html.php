<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 16/01/2017
 * Time: 13:49
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynadvblog_most_read">
    <ul>
        {foreach from=$aItems item=aItem}
        <li>
            {template file='ynblog.block.entry_most_block'}
        </li>
        {/foreach}
    </ul>
</div>