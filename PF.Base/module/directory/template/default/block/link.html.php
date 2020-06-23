<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if $sView == "mybusinesses"  || $bIsProfile }
    {if Phpfox::getUserParam('directory.can_edit_business')}
        {if $aBusiness.is_pending_claiming == 0}
        <li class="item">
            <a href="{url link='directory.dashboard.id_'.$aBusiness.business_id.'.'.$aBusiness.name}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> {phrase var='dashboard'} </a>
        </li>
        {/if}
    {/if}
    {if isset($aBusiness.can_feature) && $aBusiness.can_feature && $aBusiness.is_pending_claiming == 0 }
    <li class="item">
        <a href="javascript:void(0)" onclick="yndirectory.featureInBox(this, {$aBusiness.business_id}); return false;"><i class="fa fa-diamond" aria-hidden="true"></i> {phrase var='featured_business'} </a>
    </li>
    {/if}
    {if $aBusiness.is_pending_claiming == 0 && $aBusiness.is_draft == 0 && ($aBusiness.business_status == Phpfox::getService('directory.helper')->getConst('business.status.running') || $aBusiness.business_status == Phpfox::getService('directory.helper')->getConst('business.status.approved'))}
    <li class="item">
        <a href="javascript:void(0)" onclick="yndirectory.closeBusiness({$aBusiness.business_id}); return false;"><i class="fa fa-times-circle-o" aria-hidden="true"></i> {phrase var='close_business'} </a>
    </li>
    {/if}
    {if $aBusiness.is_pending_claiming == 0 && $aBusiness.is_draft == 0 && $aBusiness.is_closed == 1}
    <li class="item">
        <a href="javascript:void(0)" onclick="yndirectory.openBusiness({$aBusiness.business_id}); return false;"><i class="fa fa-play-circle-o" aria-hidden="true"></i> {phrase var='open_business'} </a>
    </li>
    {/if}
    {if Phpfox::getUserParam('directory.can_delete_own_business')}
    <li class="item item_delete">
        <a href="javascript:void(0)" onclick="yndirectory.confirmDeleteBusiness({$aBusiness.business_id},{if isset($bIsDetail)}1{else}0{/if});"><i class="fa fa-trash-o" aria-hidden="true"></i> {phrase var='delete_business'} </a>
    </li>
    {/if}
    {if isset($aBusiness.can_payment) && $aBusiness.can_payment && $aBusiness.is_pending_claiming == 0 }
    <li class="item">
        <a href="{url link='directory.manage-packages.id_'.$aBusiness.business_id}"><i class="fa fa-money" aria-hidden="true"></i> {phrase var='make_payment'} </a>
    </li>
    {/if}
{/if}