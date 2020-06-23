{assign var='bShowCommand' value='true' }
{assign var='bMultiViewMode' value='true' }
{if (isset($bIsUserProfile) && $bIsUserProfile) && $iPage <= 1}
<div class="mb-6">
    <div class="page_section_menu page_section_menu_header">
        <ul id="ultimatevideo_tab" class="nav nav-tabs nav-justified">
            <li>
                <a href="{url link=$aUser.user_name}ultimatevideo"><span>{_p('Videos')}</span></a>
            </li>
            <li class="active">
                <a href="{url link=$aUser.user_name}ultimatevideo/playlist"><span>{_p('Playlists')}</span></a>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
</div>
{/if}
{if $bIsSearch}
    {if isset($bSpecialMenu) && $bSpecialMenu == true}
        {template file='ultimatevideo.block.specialmenu'}
    {/if}
    {if empty($aItems)}
        {if $iPage <= 1}
            <div class="{if (isset($bIsUserProfile) && $bIsUserProfile)} p-mt--3{/if}">
                <div class="extra_info">
                    {_p('no_playlists_found')}
                </div>
            </div>
        {/if}
    {else}
        {if !PHPFOX_IS_AJAX}
            <div class="block p-block">
            <div class="content">
            {if isset($sView) && ($sView == 'historyplaylist')}
                <div class="p-mode-view-container">
                    <a class="p-ultimatevideo-clear-all" data-toggle="ultimatevideo" data-cmd="playlist_clear_all"
                       data-view="{$sView}" >
                        <i class="ico ico-trash-o"></i>&nbsp;{_p('clear_all_playlists_history')}
                    </a>
                </div>
            {/if}
            {module name='ynccore.mode_view'}
            <div class="p-listing-container p-mode-view col-4" data-mode-view="{$sModeViewDefault}">
        {/if}
        {foreach from=$aItems name=video item=aPitem}
            {template file='ultimatevideo.block.entry_playlist'}
        {/foreach}
        {pager}
        {if !PHPFOX_IS_AJAX}
            </div>
            </div>
            </div>
        {/if}
    {/if}
{/if}
{unset var=$bShowCommand}
{unset var=$bMultiViewMode}

{if !PHPFOX_IS_AJAX && ($bShowModeration) && $bIsSearch}
    {moderation}
{/if}

<script>
    oTranslations['are_you_sure_you_want_to_clear_all_playlists_from_this_section'] = "{_p var='are_you_sure_you_want_to_clear_all_playlists_from_this_section'}";
</script>