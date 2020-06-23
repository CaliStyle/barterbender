<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="container design-2">
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
    <div class="copyright">
        <div class="copyright-title">
            {param var='core.site_copyright'}
            {if (defined('PHPFOX_TRIAL_MODE'))}
            &middot; <a href="https://www.phpfox.com/">Powered by phpFox</a>
            {/if}
            <ul class="list-inline footer-menu">
                {foreach from=$aFooterMenu key=iKey item=aMenu name=footer}
                    <li{if $phpfox.iteration.footer == 1} class="first"{/if}><a href="{url link=''$aMenu.url''}" class="ajax_link{if $aMenu.url == 'mobile'} no_ajax_link{/if}">{_p var=$aMenu.var_name}</a></li>
                {/foreach}
            </ul>
            <a href="#" class="select-lang" onclick="$('#select_lang_pack').trigger('click');return false;">{if !empty($sLocaleFlagId)}<img src="{$sLocaleFlagId}" alt="{$sLocaleName}" class="v_middle" />{/if} {$sLocaleName}</a>
        </div>
        <div class="social-icon-block">
            {if !empty($aSocialIcons)}
                {foreach from=$aSocialIcons item=aIcon}
                    <a href="{$aIcon.link}" title="{$aIcon.info.name}" class="footer-social-icon" onmouseover="this.style.backgroundColor='{$aIcon.info.color}';" onmouseout="this.style.backgroundColor='inherit';">
                        <i class="fa fa-{$aIcon.info.icon}"></i>
                    </a>
                {/foreach}
            {/if}
        </div>
    </div>
</div>