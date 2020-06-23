<?php 
    defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="footer-holder">
    <div class="copyright">
        {logo}{param var='core.site_copyright'} <span>|</span>
        <div class="select-language">
            <div class="language-list-inline clearfix">
                {foreach from=$aLanguages item=aLanguage name=languages}
                    {if Phpfox_Locale::instance()->getLangId() == $aLanguage.language_id}
                        <a href="javascript:void(0)" class="active" title="{$aLanguage.title}">{$aLanguage.title}</a>
                    {/if}
                {/foreach}
                {if count($aLanguages) > 1}
                    {for $i=0; $i < 1; $i++}
                        {if (Phpfox_Locale::instance()->getLangId() == $aLanguages[0].language_id)}
                            <a href="#" title="{$aLanguages[1].title}" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguages[1].language_id}'); return false;">{$aLanguages[1].title}</a>
                        {elseif (Phpfox_Locale::instance()->getLangId() == $aLanguages[1].language_id)}
                            <a href="#" title="{$aLanguages[0].title}" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguages[0].language_id}'); return false;">{$aLanguages[0].title}</a>
                        {else}
                            {if Phpfox_Locale::instance()->getLangId() != $aLanguages[$i].language_id}
                                <span>
                                    {if Phpfox_Locale::instance()->getLangId() != $aLanguages[$i].language_id}<a href="#" title="{$aLanguages[$i].title}" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguages[$i].language_id}'); return false;">{/if}{$aLanguages[0].title}{if Phpfox_Locale::instance()->getLangId() != $aLanguages[$i].language_id}</a>{/if}
                                </span>
                            {/if}
                        {/if}
                    {/for}
                {/if}
            </div>
            {if count($aLanguages) > 2}
                <div class="language-list-dropdown dropup">
                     <span class="dropdown-toggle select-language-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        {_p var = 'Others'}
                    </span>
                    <ul class="dropdown-menu dropdown-menu-right" >
                        {foreach from=$aLanguages item=aLanguage name=languages}
                            <li>
                                {if Phpfox_Locale::instance()->getLangId() != $aLanguage.language_id}
                                    <a href="#" title="{$aLanguages.title}" onclick="$('#js_language_package_holder').hide(); $('#js_loading_language').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}')); $.ajaxCall('language.process', 'id={$aLanguage.language_id}'); return false;">{$aLanguage.title}</a>
                                {else}
                                    <a class="{if Phpfox_Locale::instance()->getLangId() == $aLanguage.language_id} active{/if}" title="{$aLanguage.title}" href="#" >{$aLanguage.title} {if Phpfox_Locale::instance()->getLangId() == $aLanguage.language_id}<i class="ico ico-check"></i>{/if}</a>
                                {/if}
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </div>
    </div>
	<ul class="list-inline footer-menu">
		{foreach from=$aFooterMenu key=iKey item=aMenu name=footer}
		    <li{if $phpfox.iteration.footer == 1} class="first"{/if}><a href="{url link=''$aMenu.url''}" class="ajax_link{if $aMenu.url == 'mobile'} no_ajax_link{/if}">{_p var=$aMenu.var_name}</a></li>
		{/foreach}
	</ul>
</div>
