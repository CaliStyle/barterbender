<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="container design-4">
    {if Phpfox::getParam('advancedfooter.enablejoinshareimage') and !Phpfox::getUserId()}
        <div class="footer-register">
            <a href="{url link='user.register'}" class="join-share-a">
                <img src="{$footerPath}{if Phpfox::getParam('advancedfooter.advancedfootertheme') == 'light'}join_light.png{else}join.png{/if}" />
            </a>
            <a href="{url link='user.register'}" class="button btn btn-primary footer-register-button btn-gradient">
                {_p var='Join Now'}
            </a>
        </div>
    {/if}
    <div class="row">
        {foreach from=$aFooterMenus name=footerMenus item=aItem}
            {if $phpfox.iteration.footerMenus < 5}
                <div class="col-sm-3">
                    <h4 class="footer-heading">
                        {if !empty($aItem.link) or !empty($aItem.direct_link)}
                            <a href="{if !empty($aItem.link)}{url link=$aItem.link}{elseif !empty($aItem.direct_link)}{url link=$aItem.direct_link}{/if}">
                        {/if}
                        {$aItem.name}
                        {if !empty($aItem.link) or !empty($aItem.direct_link)}
                            </a>
                        {/if}
                    </h4>
                    {if !empty($aItem.sub)}
                        <ul>
                            {foreach from=$aItem.sub item=sub}
                                <li>
                                    <a href="{if !empty($sub.link)}{url link=$sub.link}{elseif !empty($sub.direct_link)}{url link=$sub.direct_link}{/if}">
                                        {$sub.name}
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </div>
            {/if}
        {/foreach}
    </div>
</div>
<div class="design-2 design-4 copyright-wrap">
    <div class="container">
        <div class="copyright">
            <div class="copyright-title">
                {param var='core.site_copyright'}  {if (defined('PHPFOX_TRIAL_MODE'))}
                &middot; <a href="https://www.phpfox.com/">Powered by phpFox</a>
                {/if}
                <ul class="list-inline footer-menu">
                    {foreach from=$aFooterMenu key=iKey item=aMenu name=footer}
                    <li{if $phpfox.iteration.footer == 1} class="first"{/if}><a href="{url link=''$aMenu.url''}" class="ajax_link{if $aMenu.url == 'mobile'} no_ajax_link{/if}">{_p var=$aMenu.var_name}</a></li>
                    {/foreach}
                </ul>
                <a href="#" class="select-lang" onclick="$('#select_lang_pack').trigger('click');return false;">{if !empty($sLocaleFlagId)}<img src="{$sLocaleFlagId}" alt="{$sLocaleName}" class="v_middle" />{/if} {$sLocaleName}</a>
            </div>

        </div>
    </div>
</div>