<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 09/01/2017
 * Time: 17:43
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
