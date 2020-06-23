<ul class="yc_list_right">
    {foreach from=$aEntries item=aEntry}
    <li>
        {if $aEntry.type == 1 || $aEntry.type == 4}
        <div class="ycimg_right_small">
            {*img server_id=$aEntry.user_server_id path='core.url_pic' file='user/'.$aEntry.user_image suffix='_50_square' max_width=50 max_height=50*}
            {img server_id=$aEntry.user_server_id user=$aEntry suffix='_50_square' }
        </div>
        {else}
        {if $aEntry.image_path}
            <div class="ycimg_right" style="background-image:url('{if $aEntry.type == 2}
                    {img return_url=true server_id=$aEntry.server_id path='core.url_pic' file=$aEntry.image_path suffix='_200'}
                {elseif $aEntry.type == 3}
                    {img return_url=true server_id=$aEntry.server_id path='core.url_pic' file=$aEntry.image_path suffix='_120'}
                {/if}')">
            </div>
        {else}
        <div class="ycimg_right" style="background-image:url('{$sUrlNoImagePhoto}')"></div>
        {/if}
        {/if}
        <div class="ycinfo_right">
            <a href="{$sContestUrl}entry_{$aEntry.entry_id}/" title="{$aEntry.title}">{$aEntry.title|clean|shorten:20:'...'|split:20}</a>
            <p>{phrase var='contest.by'} {$aEntry|user}</p>
            <span class="ycvotes">{$aEntry.total_vote}</span>
        </div>
    </li>
    {/foreach}
</ul>
<div class="clear"></div>
<div class="text-center">
    <a href="{$sViewMoreUrl}" class="yc_view_more button btn btn-success btn-sm"> {phrase var='contest.view_more'}</a>
</div>