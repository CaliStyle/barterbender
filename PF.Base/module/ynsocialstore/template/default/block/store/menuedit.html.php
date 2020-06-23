<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="ynstore_menu_edit_store" class="page_section_menu page_section_menu_header">
    <ul class="action">
        <li class="ynstore-insight {if isset($sTabView) && ($sTabView == 'insight')}active{/if}"  >
            <a href="{url link='ynsocialstore.store.insight.id_'.$iStoreId}">{_p var='ynsocialstore.insight'}</a>
        </li>

        <li class="ynstore-edit-info {if isset($sTabView) && ($sTabView == 'add')}active{/if}">
            <a href="{url link='ynsocialstore.store.add.id_'.$iStoreId}">{_p var='ynsocialstore.edit_info'}</a>
        </li>

        <li class="ynstore-manage-packages {if isset($sTabView) && ($sTabView == 'manage-packages')}active{/if}">
            <a href="{url link='ynsocialstore.store.manage-packages.id_'.{$iStoreId}">{_p var='ynsocialstore.manage_packages'}</a>
        </li>

        <li class="ynstore-manage-products {if isset($sTabView) && ($sTabView == 'manage-products')}active{/if}">
            <a href="{url link='ynsocialstore.store.manage-products.id_'.{$iStoreId}">{_p var='ynsocialstore.manage_products'}</a>
        </li>

        <li class="ynstore-sale-of-store {if isset($sTabView) && ($sTabView == 'sale-of-store')}active{/if}">
            <a href="{url link='ynsocialstore.store.sale-of-store.id_'.{$iStoreId}">{_p var='ynsocialstore.sale_of_store'}</a>
        </li>

        <li class="ynstore-manage-faqs {if isset($sTabView) && ($sTabView == 'manage-faqs')}active{/if}">
            <a href="{url link='ynsocialstore.store.manage-faqs.id_'.{$iStoreId}">{_p var='ynsocialstore.manage_faqs'}</a>
        </li>
        
        <li>
            <a href="{url link='ynsocialstore.store'}{$iStoreId}">{_p var='ynsocialstore.view_this_store'}</a>
        </li>
    </ul> 
</div>
