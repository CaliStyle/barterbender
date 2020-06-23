<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="footer-holder">
    <div class="select-language">
        <div class="language-list-inline">
            {foreach from=$aLanguages item=aLanguage name=languages}
                {if Phpfox_Locale::instance()->getLangId() == $aLanguage.language_id}
                    <li class="active"><span title="{$aLanguage.title}">{$aLanguage.title}</span></li>
                {/if}
            {/foreach}
            {if count($aLanguages) > 1}
                {for $i=0; $i < 2; $i++}
                    {if (Phpfox_Locale::instance()->getLangId() == $aLanguages[0].language_id) || (Phpfox_Locale::instance()->getLangId() == $aLanguages[1].language_id)}
                        {if Phpfox_Locale::instance()->getLangId() != $aLanguages[$i].language_id}
                            <li>
                                <a href="#" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguages[$i].language_id}'); return false;">{$aLanguages[$i].title}</a>
                            </li>
                            {if count($aLanguages) > 2}
                                <li>
                                    <a href="#" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguages[2].language_id}'); return false;">{$aLanguages[2].title}</a>
                                </li>
                            {/if}
                        {/if}
                    {else}
                        {if Phpfox_Locale::instance()->getLangId() != $aLanguages[$i].language_id}
                            <li>
                                {if Phpfox_Locale::instance()->getLangId() != $aLanguages[$i].language_id}<a href="#" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguages[$i].language_id}'); return false;">{/if}{$aLanguages[$i].title}{if Phpfox_Locale::instance()->getLangId() != $aLanguages[$i].language_id}</a>{/if}
                            </li>
                        {/if}
                    {/if}
                {/for}
            {/if}
        </div>
        {if count($aLanguages) > 3}
        <div class="language-list-dropdown dropup">
             <a class="dropdown-toggle select-language-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="ico ico-plus"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right" >
                {foreach from=$aLanguages item=aLanguage name=languages}
                    <li>
                        {if Phpfox_Locale::instance()->getLangId() != $aLanguage.language_id}
                            <a href="#" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguage.language_id}'); return false;">
                            <span class="ico ico-pencilline-o mr-1"></span>{$aLanguage.title}</a>
                        {else}
                            <a class="{if Phpfox_Locale::instance()->getLangId() == $aLanguage.language_id} active{/if}" title="{$aLanguage.title}" href="#" ><span class="ico ico-pencilline-o mr-1"></span>{$aLanguage.title}</a>
                        {/if}
                    </li>
                {/foreach}
            </ul>
        </div>
        {/if}
    </div>
	<ul class="list-inline footer-menu">
		{foreach from=$aFooterMenu key=iKey item=aMenu name=footer}
		<li{if $phpfox.iteration.footer == 1} class="first"{/if}><a href="{url link=''$aMenu.url''}" class="ajax_link{if $aMenu.url == 'mobile'} no_ajax_link{/if}">{_p var=$aMenu.var_name}</a></li>
		{/foreach}
       
	</ul>
     <div class="footer-sitename">{param var='core.site_copyright'}
        {if (defined('PHPFOX_TRIAL_MODE'))}
        &middot; <a href="https://www.phpfox.com/">Powered by phpFox</a>
        {/if}
    </div>
</div>
