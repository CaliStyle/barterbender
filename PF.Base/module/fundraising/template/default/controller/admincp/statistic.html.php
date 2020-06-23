<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style>
    ul.pagination li a.active {
        color: #FFFFFF;
        background-color: #1ab394;
    }
</style>
{/literal}
{if $sViewFr == 'detail'}
    {template file='fundraising.block.statisticdetail' aTransaction=$aTransaction}
{else}
    {module name='fundraising.statistic'}
{/if}