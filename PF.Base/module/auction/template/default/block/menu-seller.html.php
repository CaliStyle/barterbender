<div id="menu_seller_my_requests" class="ynauction-menu-options sub_section_menu header_display">
    <ul class="action">
        <li class="ynecommerce-insight {if $sFullControllerName == 'auction.statistic'}active{/if}">
        	<a href="{url link='auction.statistic'}">{phrase var='statistic'}</a>
        </li>
        <li class="ynecommerce-insight {if $sFullControllerName == 'auction.manageauction'}active{/if}">
        	<a href="{url link='auction.manageauction'}">{phrase var='ecommerce.manage_auctions'}</a>
        </li>
        <li class="ynecommerce-insight {if $sFullControllerName == 'auction.settings'}active{/if}">
        	<a href="{url link='auction.settings'}">{phrase var='settings'}</a>
        </li>
        <li class="ynecommerce-insight {if $sFullControllerName == 'ecommerce.manage-orders'}active{/if}">
        	<a href="{url link='auction.manage-orders'}">{phrase var='ecommerce.manage_orders'}</a>
        </li>
        <li class="ynecommerce-insight {if $sFullControllerName == 'ecommerce.my-requests'}active{/if}">
        	<a href="{url link='auction.my-requests'}">{phrase var='ecommerce.my_requests'}</a>
        </li>
    </ul> 
</div>

{literal}
<script type="text/javascript">
    $Behavior.onLoadMenuSeller = function(){
        if($('#page_auction_manageauction').length >0 || $('#page_ynsocialstore_manage-photos').length >0 || $('#page_auction_settings').length >0 || $('#page_ecommerce_my-requests').length >0 || $('#page_ecommerce_order-detail').length >0 || $('#page_ecommerce_manage-orders').length >0){
            $('#js_block_border_core_menusub .sub_section_menu ul > li.active').removeClass("active");
            $('#js_block_border_core_menusub .header_display ul > li.active').removeClass("active");
            $("#js_block_border_core_menusub .sub_section_menu ul > li > a:contains('{/literal}{phrase var='seller_section'}{literal}')").parent('li').addClass("active");
            $("#js_block_border_core_menusub .header_display ul > li > a:contains('{/literal}{phrase var='seller_section'}{literal}')").parent('li').addClass("active");
        }
    }
</script>
{/literal}