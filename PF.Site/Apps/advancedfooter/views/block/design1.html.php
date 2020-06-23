<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="container design-1">
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
        <div class="col-sm-3">
            <div class="site-logo">
                <a href="{url link=''}" class="site-logo-link">
                   <img src="{$logo}"/>
                </a>
            </div>
            <a href="#" class="select-lang" onclick="$('#select_lang_pack').trigger('click');return false;">{if !empty($sLocaleFlagId)}<img src="{$sLocaleFlagId}" alt="{$sLocaleName}" class="v_middle" />{/if} {$sLocaleName}</a>
        </div>
        {foreach from=$aFooterMenus name=footerMenus item=aItem}
            {if $phpfox.iteration.footerMenus < 4}
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
    <div class="copyright">
        <h4>
            {_p var='Join Us On'}
        </h4>
        {if !empty($aSocialIcons)}
            {foreach from=$aSocialIcons item=aIcon}
                <a href="{$aIcon.link}" title="{$aIcon.info.name}" class="footer-social-icon" onmouseover="this.style.backgroundColor='{$aIcon.info.color}';" onmouseout="this.style.backgroundColor='inherit';">
                    <i class="fa fa-{$aIcon.info.icon}"></i>
                </a>
            {/foreach}
        {/if}
        <div class="copyright-title">
            {param var='core.site_copyright'}
            {if (defined('PHPFOX_TRIAL_MODE'))}
                &middot; <a href="https://www.phpfox.com/">Powered by phpFox</a>
            {/if}
        </div>
    </div>
</div>