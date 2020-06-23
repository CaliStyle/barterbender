<div id="menu_seller_my_requests" class="page_section_menu page_section_menu_header">
    <ul>
        <li class="ynecommerce-insight {if $sFullControllerName == 'ecommerce.statistic'}active{/if}">
        	<a href="{url link='ecommerce.statistic'}">{phrase var='statistic'}</a>
        </li>
        <li class="ynecommerce-insight">
        	<a href="javascript:;">{phrase var='manage_auctions'}</a>
        </li>
        <li class="ynecommerce-insight {if $sFullControllerName == 'ecommerce.settings'}active{/if}">
        	<a href="{url link='ecommerce.settings'}">{phrase var='settings'}</a>
        </li>
        <li class="ynecommerce-insight">
        	<a href="javascript:;">{phrase var='manage_orders'}</a>
        </li>
        <li class="ynecommerce-insight">
        	<a href="javascript:;">{phrase var='gateway_configuration'}</a>
        </li>
        <li class="ynecommerce-insight {if $sFullControllerName == 'ecommerce.my-requests'}active{/if}">
        	<a href="{url link='ecommerce.my-requests'}">{phrase var='my_requests'}</a>
        </li>
    </ul> 
</div>
