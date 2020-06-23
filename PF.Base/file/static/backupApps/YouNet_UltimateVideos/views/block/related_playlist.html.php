{if !empty($aItems)}
<div class="ultimatevideo_playlists_grid show_grid_view no-multiview clearfix">
    {foreach from=$aItems name=video item=aPitem}
    {template file='ultimatevideo.block.entry_playlist'}
    {/foreach}
</div>
{else}
<div class="extra_info">
    {_p('no_playlists_found')}
</div>
{/if}