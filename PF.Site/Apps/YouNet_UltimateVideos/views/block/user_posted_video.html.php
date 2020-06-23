{if !empty($aItems)}
    <div class="ultimatevideo-grid show_grid_view clearfix">
        {foreach from=$aItems name=video item=aItem}
            {template file='ultimatevideo.block.entry'}
        {/foreach}
    </div>
{else}
    <div class="extra_info">
        {_p('no_videos_found')}
    </div>
{/if}


