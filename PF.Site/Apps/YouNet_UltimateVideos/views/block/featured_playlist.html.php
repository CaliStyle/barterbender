{if !empty($aItems)}
<div class="ultimatevideo-grid clearfix">
    {foreach from=$aItems name=video item=aPitem}
    {template file='ultimatevideo.block.entry_playlist'}
    {/foreach}
</div>
{else}
<div class="extra_info">
    {_p('no_playlists_found')}
</div>
{/if}