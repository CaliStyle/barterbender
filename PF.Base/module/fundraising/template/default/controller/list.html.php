<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{if $sViewFr == 'detail'}
    {template file='fundraising.block.statisticdetail' aTransaction=$aTransaction}
{else}
{module name='fundraising.statistic'}
{/if}