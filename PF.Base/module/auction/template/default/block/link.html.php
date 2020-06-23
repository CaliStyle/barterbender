<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if $sView == "myauctions"  || $bIsProfile }
    <li class="item">
        <a href="{url link='auction.edit.id_'.$aProduct.product_id.'.'.$aProduct.name}"> {phrase var='dashboard'} </a>
    </li>
    {if isset($aProduct.can_feature) && $aProduct.can_feature}
    <li class="item">
        <a href="javascript:void(0)" onclick="ynauction.featureInBox(this, {$aProduct.product_id}); return false;"> {phrase var='featured_auction'} </a>
    </li>
    {/if}
    {if Phpfox::getUserParam('auction.can_delete_own_auction')}
    <li class="item">
        <a href="javascript:void(0)" onclick="ynauction.confirmDeleteAuction({$aProduct.product_id});"> {phrase var='delete_auction'} </a>
    </li>
    {/if}
    {if isset($aProduct.can_payment) && $aProduct.can_payment}
    <li class="item">
        <a href="{url link='auction.manage-packages.id_'.$aProduct.product_id}"> {phrase var='make_payment'} </a>
    </li>
    {/if}
{/if}