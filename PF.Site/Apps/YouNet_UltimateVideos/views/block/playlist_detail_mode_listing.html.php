<div class="block" id="ultimatevideo-modeviews-playlist-detail">
    <div class="ultimatevideo_playlist-count-modeview">
        <div class="ultimatevideo_video_detail-countvideo">
            <i class="fa fa-film" aria-hidden="true"></i> <span>{$aPitem.total_video}</span> {if $aPitem.total_video == 1} {_p('Video')} {else} {_p('Videos')} {/if}
        </div>

        <div class="pull-right ultimatevideo-modeviews {if $iCurrentMode == 1}show_grid_view{else}show_casual_view{/if}">
            <span title="{_p('Grid View')}" data-toggle="ultimatevideo" data-cmd="show_grid_view"><i class="ynicon yn-grid-view"></i></span>
            <span title="{_p('Casual View')}" data-toggle="ultimatevideo" data-cmd="show_casual_view"><i class="ynicon yn-casual-view"></i></span>
        </div>
    </div>
    <div class="ultimatevideo-grid {if $iCurrentMode == 1}show_grid_view{else}show_casual_view{/if} clearfix">
    	{if count($aItems)}
    	    {foreach from=$aItems name=video item=aItem}
            	{template file='ultimatevideo.block.entry'}
            {/foreach}
            {template file="ultimatevideo.block.pager_playlist"}
        {else}
        	<div style="padding-left:20px;">{_p('no_videos_found')}</div>
       	{/if}

    </div>
</div>